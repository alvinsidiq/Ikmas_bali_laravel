<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnggotaRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['nullable','string','min:8'],
            'nik' => ['nullable','string','max:50'],
            'nama_lengkap' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'alamat' => ['nullable','string'],
            'tanggal_lahir' => ['nullable','date'],
            'jenis_kelamin' => ['nullable', Rule::in(['L','P'])],
            'pekerjaan' => ['nullable','string','max:100'],
            'organisasi' => ['nullable','string','max:100'],
            'avatar' => ['nullable','image','max:2048'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}
