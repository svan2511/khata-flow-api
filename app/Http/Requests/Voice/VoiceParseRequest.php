<?php

namespace App\Http\Requests\Voice;

use Illuminate\Foundation\Http\FormRequest;

class VoiceParseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'transcript' => ['required', 'string', 'min:2', 'max:1000'],
            'language' => ['sometimes', 'string', 'in:hi,en'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'transcript.required' => 'Please speak or type the bill first.',
            'transcript.min' => 'The voice instruction is too short.',
            'transcript.max' => 'The voice instruction is too long.',
            'language.in' => 'Language must be either hi or en.',
        ];
    }
}
