<?php
namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLaporanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('anggota') ?? false; }
    public function rules(): array
    {
        return [
            'judul' => ['required','string','max:255'],
            'kategori' => ['nullable','string','max:100'],
            'deskripsi' => ['nullable','string'],
        ];
    }
}

