<?php
namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanCommentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('anggota') ?? false; }
    public function rules(): array
    {
        return [ 'body' => ['required','string'] ];
    }
}

