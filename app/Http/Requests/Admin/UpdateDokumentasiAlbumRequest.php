<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDokumentasiAlbumRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }
    public function rules(): array
    {
        return [
            'judul' => ['required','string','max:255'],
            'tanggal_kegiatan' => ['nullable','date'],
            'lokasi' => ['nullable','string','max:255'],
            'deskripsi' => ['nullable','string'],
            'tags' => ['nullable','string','max:255'],
            'is_published' => ['nullable','boolean'],
        ];
    }
}
