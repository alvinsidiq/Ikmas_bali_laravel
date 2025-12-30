<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokumentasiMediaRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }
    public function rules(): array
    {
        return [
            // multiple upload: name="media[]"
            'media' => ['required','array','max:30'],
            'media.*' => ['required','file','mimes:jpg,jpeg,png,webp','max:8192'],
        ];
    }
}
