<?php
namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('anggota') ?? false; }
    public function rules(): array
    {
        return [
            'judul' => ['required','string','max:255'],
            'kategori' => ['nullable','string','max:100'],
            'deskripsi' => ['nullable','string'],
            'files' => ['nullable','array','max:10'],
            'files.*' => ['file','max:8192','mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,webp,zip'],
        ];
    }
}

