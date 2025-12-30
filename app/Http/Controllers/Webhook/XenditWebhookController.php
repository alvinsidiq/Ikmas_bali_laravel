<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\IuranPembayaran;
use App\Services\IuranStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function __invoke(Request $request, IuranStatusService $svc)
    {
        $token = config('services.xendit.webhook_token');
        if ($token && $request->header('x-callback-token') !== $token) {
            Log::warning('Xendit webhook token mismatch');
            return response()->json(['message' => 'unauthorized'], 403);
        }

        $payload = $request->all();
        $invoiceId = data_get($payload, 'id') ?? data_get($payload, 'data.id');
        $status = strtoupper(data_get($payload, 'status') ?? data_get($payload, 'data.status', ''));
        $invoiceUrl = data_get($payload, 'invoice_url') ?? data_get($payload, 'data.invoice_url');

        if (!$invoiceId || !$status) {
            return response()->json(['message' => 'invalid payload'], 400);
        }

        $pay = IuranPembayaran::where('xendit_transaction_id', $invoiceId)
            ->orWhere('gateway_reference', $invoiceId)
            ->first();

        if (!$pay) {
            Log::info('Xendit webhook ignored: payment not found', ['invoice_id' => $invoiceId]);
            return response()->json(['message' => 'ignored']);
        }

        // Idempotent: jika status sudah final dan sama, abaikan
        $finalStatuses = ['verified','rejected'];
        if (in_array($pay->status, $finalStatuses) && strtoupper($pay->status_pembayaran ?? '') === $status) {
            return response()->json(['message' => 'ok']);
        }

        Log::info('Xendit webhook received', [
            'invoice_id' => $invoiceId,
            'status' => $status,
            'payment_id' => $pay->id,
            'tagihan_id' => $pay->tagihan_id,
        ]);

        $pay->gateway_reference = $pay->gateway_reference ?: $invoiceId;
        $pay->xendit_transaction_id = $pay->xendit_transaction_id ?: $invoiceId;
        $pay->invoice_url = $pay->invoice_url ?: $invoiceUrl;
        $pay->gateway_payload = $payload;
        $pay->status_pembayaran = $status;

        if (in_array($status, ['PAID','SETTLED'])) {
            $pay->status = 'verified';
            $pay->paid_at = $pay->paid_at ?: now();
            $pay->verified_at = $pay->verified_at ?: now();
        } elseif ($status === 'PENDING') {
            $pay->status = 'pending_gateway';
        } else {
            $pay->status = 'rejected';
        }

        $pay->save();
        if ($pay->tagihan) {
            $svc->refreshTagihan($pay->tagihan);
        }

        return response()->json(['message' => 'ok']);
    }
}
