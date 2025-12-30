<?php
namespace App\Http\Requests\Bendahara;

use Illuminate\Foundation\Http\FormRequest;

class BulkGenerateTagihanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->hasRole('bendahara') ?? false; }
    public function rules(): array
    {
        return [
            'periode' => ['required','regex:/^\d{4}-\d{2}$/'],
            'judul' => ['required','string','max:255'],
            'nominal' => ['required','integer','min:0'],
            'denda' => ['nullable','integer','min:0'],
            'diskon' => ['nullable','integer','min:0'],
            'jatuh_tempo' => ['nullable','date'],
            'target' => ['required','in:all,selected'],
            'user_ids' => ['nullable','array','required_if:target,selected'],
            'user_ids.*' => ['integer','exists:users,id'],
            'skip_if_exists' => ['nullable','boolean'],
        ];
    }
}
