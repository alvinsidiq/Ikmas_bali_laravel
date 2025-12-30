<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKegiatanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        return [
            'judul' => ['required','string','max:255'],
            'lokasi' => ['nullable','string','max:255'],
            'deskripsi' => ['nullable','string'],
            'waktu_mulai' => ['required','date'],
            'waktu_selesai' => ['nullable','date','after_or_equal:waktu_mulai'],
            'poster' => ['nullable','image','max:3072'],
            'is_published' => ['nullable','boolean'],
        ];
    }
}
