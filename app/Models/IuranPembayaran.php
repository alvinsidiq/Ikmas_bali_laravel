<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranPembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode','tagihan_id','user_id','amount','paid_at','method','channel','bukti_path','bukti_mime','bukti_size','status','status_pembayaran','verified_by','verified_at','rejection_reason',
        'gateway','gateway_reference','gateway_receipt_url','gateway_payload',
        'xendit_transaction_id','invoice_url'
    ];

    protected $casts = [ 'paid_at' => 'datetime', 'verified_at' => 'datetime', 'gateway_payload' => 'array' ];

    public function tagihan(){ return $this->belongsTo(IuranTagihan::class, 'tagihan_id'); }
    public function user(){ return $this->belongsTo(User::class); }
    public function verifier(){ return $this->belongsTo(User::class, 'verified_by'); }
}
