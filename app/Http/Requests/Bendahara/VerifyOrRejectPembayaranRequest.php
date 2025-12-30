<?php
namespace App\Http\Requests\Bendahara;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOrRejectPembayaranRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('bendahara') ?? false; }
    public function rules(): array
    {
        return [ 'rejection_reason' => ['nullable','string','max:255'] ];
    }
}

