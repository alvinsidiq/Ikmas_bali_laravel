<?php
namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;

class UpdateForumPostRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('anggota') ?? false; }
    public function rules(): array
    {
        return [ 'content' => ['required','string'] ];
    }
}

