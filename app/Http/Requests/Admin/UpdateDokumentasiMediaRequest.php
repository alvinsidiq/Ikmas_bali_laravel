<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDokumentasiMediaRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }
    public function rules(): array
    {
        return [
            'caption' => ['nullable','string','max:255'],
            'sort_order' => ['nullable','integer','min:0','max:100000'],
        ];
    }
}
