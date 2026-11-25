<?php

namespace App\Http\Requests;

use App\Models\DocumentTemplate;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * The values for every `${variable}` of the template in the route, sent as `values[variable]`.
 */
class GenerateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'values' => ['required', 'array'],
            ...collect($this->template()->variables)
                ->mapWithKeys(fn (string $variable): array => ["values.{$variable}" => ['required', 'string', 'max:5000']])
                ->all(),
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'string' => ':Attribute harus berupa teks.',
            'max' => ':Attribute maksimal :max karakter.',
        ];
    }

    /**
     * Get the custom attribute names for validation messages: each variable's label, e.g. "NIK".
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return collect($this->template()->variables)
            ->mapWithKeys(fn (string $variable): array => ["values.{$variable}" => DocumentTemplate::labelFor($variable)])
            ->all();
    }

    private function template(): DocumentTemplate
    {
        return $this->route('documentTemplate');
    }
}
