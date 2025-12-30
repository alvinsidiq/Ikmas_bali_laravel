<?php
namespace App\Services;

use App\Models\IuranPembayaran;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xendit\Exceptions\ApiException;
use Xendit\Invoice;
use Xendit\Xendit;

class XenditService
{
    protected string $secretKey;
    protected string $baseUrl;
    protected string $checkoutBase;
    protected string $checkoutPattern;
    protected bool $mock;

    public function __construct()
    {
        $this->secretKey = (string) (config('services.xendit.secret_key') ?? config('services.xendit.api_key') ?? '');
        $this->baseUrl = rtrim(config('services.xendit.base_url', 'https://api.xendit.co'), '/');
        // Gunakan pola checkout yang sesuai sandbox: https://checkout-staging.xendit.co/v4/invoices/{id}/pay
        $this->checkoutBase = rtrim(config('services.xendit.checkout_base', 'https://checkout-staging.xendit.co'), '/');
        $pattern = config('services.xendit.checkout_pattern', $this->checkoutBase.'/v4/invoices/%s/pay');
        // Jika pola lama /web/v1/%s digunakan, paksa ke pola baru pay page
        if (str_contains($pattern, '/web/v1/')) {
            $pattern = $this->checkoutBase.'/v4/invoices/%s/pay';
        }
        $this->checkoutPattern = $pattern;
        $this->mock = (bool)config('services.xendit.mock', false);
        if (!$this->mock && $this->secretKey) {
            Xendit::setApiKey($this->secretKey);
        }
    }

    public function createInvoice(IuranPembayaran $pay, string $method, string $successUrl, string $failureUrl): array
    {
        if ($this->mock) {
            return $this->fakeInvoicePayload($pay, $successUrl, $failureUrl);
        }
        if (empty($this->secretKey)) {
            throw new \RuntimeException('Xendit secret key belum dikonfigurasi.');
        }

        $payload = [
            'external_id' => $pay->kode,
            'payer_email' => $pay->user?->email ?? 'member@example.com',
            'description' => "Pembayaran iuran {$pay->tagihan?->judul}",
            'amount' => $pay->amount,
            'success_redirect_url' => $successUrl,
            'failure_redirect_url' => $failureUrl,
        ];

        try {
            return Invoice::create($payload);
        } catch (ApiException $e) {
            Log::error('Xendit invoice creation failed', ['message' => $e->getMessage(), 'payload'=>$payload]);
            throw $e;
        }
    }

    public function checkoutUrl(array $invoice): ?string
    {
        if (!empty($invoice['invoice_url'])) {
            return $invoice['invoice_url'];
        }
        if (!empty($invoice['id'])) {
            return sprintf($this->checkoutPattern, $invoice['id']);
        }
        return null;
    }

    public function fakeInvoicePayload(IuranPembayaran $pay, ?string $successUrl = null, ?string $failureUrl = null): array
    {
        $id = 'sandbox-'.$pay->kode.'-'.Str::random(6);
        return [
            'id' => $id,
            'status' => 'PENDING',
            'amount' => $pay->amount,
            'invoice_url' => sprintf($this->checkoutPattern, $id),
            'description' => "Sandbox invoice untuk {$pay->tagihan?->judul}",
            'success_redirect_url' => $successUrl,
            'failure_redirect_url' => $failureUrl,
        ];
    }

    public function getInvoice(string $id): ?array
    {
        if ($this->mock || empty($this->secretKey)) {
            return null;
        }
        try {
            return Invoice::retrieve($id);
        } catch (ApiException $e) {
            Log::warning('Gagal mengambil invoice Xendit', [
                'invoice_id' => $id,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
