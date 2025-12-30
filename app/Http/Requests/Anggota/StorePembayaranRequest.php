<?php
namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;

class StorePembayaranRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('anggota') ?? false; }
    public function rules(): array
    {
        return [
            'amount' => ['required','integer','min:1000'],
            'paid_at' => ['nullable','date'],
            'channel' => ['required','in:gateway,manual'],
            'gateway_method' => ['required_if:channel,gateway','nullable','in:transfer,ewallet'],
            'manual_method' => ['required_if:channel,manual','nullable','in:transfer,cash'],
            'bukti' => ['required_if:channel,manual','nullable','file','max:8192','mimes:jpg,jpeg,png,webp,pdf'],
        ];
    }
}
