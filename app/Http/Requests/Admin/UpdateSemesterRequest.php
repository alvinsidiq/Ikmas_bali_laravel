<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSemesterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public function rules(): array
    {
        $id = $this->route('semester')?->id ?? $this->semester;
        return [
            'tahun_ajaran_id' => ['required','exists:tahun_ajarans,id'],
            'nama' => ['required','string','max:100','unique:semesters,nama,'.$id.',id,tahun_ajaran_id,'.$this->input('tahun_ajaran_id')],
            'tanggal_mulai' => ['required','date'],
            'tanggal_selesai' => ['required','date','after:tanggal_mulai'],
            'aktif' => ['nullable','boolean'],
        ];
    }
}
