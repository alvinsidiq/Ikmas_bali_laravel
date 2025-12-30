<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengumumanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        return [
            'judul' => ['required','string','max:255'],
            'kategori' => ['nullable','string','max:100'],
            'isi' => ['nullable','string'],
            'cover' => ['nullable','image','max:3072'],
            'is_published' => ['nullable','boolean'],
            'is_pinned' => ['nullable','boolean'],
        ];
    }
}
