<?php
namespace App\Http\Requests\Bendahara;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagihanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('bendahara') ?? false; }
    public function rules(): array
    {
        return [
            'judul' => ['required','string','max:255'],
            'periode' => ['nullable','string','max:20'],
            'nominal' => ['required','integer','min:0'],
            'denda' => ['nullable','integer','min:0'],
            'diskon' => ['nullable','integer','min:0'],
            'jatuh_tempo' => ['nullable','date'],
            'catatan' => ['nullable','string'],
        ];
    }
}

