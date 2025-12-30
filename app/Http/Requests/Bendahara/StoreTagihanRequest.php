<?php
namespace App\Http\Requests\Bendahara;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagihanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('bendahara') ?? false; }
    public function rules(): array
    {
        return [
            'user_id' => ['required','exists:users,id'],
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

