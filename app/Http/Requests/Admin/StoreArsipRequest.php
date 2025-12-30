<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreArsipRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        return [
            'judul' => ['required','string','max:255'],
            'kategori' => ['nullable','string','max:100'],
            'tahun' => ['nullable','integer','between:1900,2100'],
            'nomor_dokumen' => ['nullable','string','max:100'],
            'tags' => ['nullable','string','max:255'],
            'ringkasan' => ['nullable','string'],
            'thumbnail_url' => ['nullable','url','max:500'],
            'file' => ['required','file','mimes:pdf,doc,docx,xls,xlsx,csv,zip','max:10240'],
            'is_published' => ['nullable','boolean'],
        ];
    }
}
