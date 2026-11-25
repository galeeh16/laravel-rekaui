<?php

namespace App\Http\Requests;

use App\Models\DocumentTemplate;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Throwable;

class StoreDocumentTemplateRequest extends FormRequest
{
    /**
     * Maximum template size in kilobytes.
     */
    public const MAX_FILE_SIZE_KB = 10_240;

    /**
     * Variable names read from the uploaded file during validation.
     *
     * @var list<string>
     */
    private array $templateVariables = [];

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
            'name' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:docx', 'extensions:docx', 'max:'.self::MAX_FILE_SIZE_KB],
        ];
    }

    /**
     * Read the template's variables once the file itself is valid, and reject files without any.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('file')) {
                    return;
                }

                try {
                    $this->templateVariables = DocumentTemplate::extractVariables($this->file('file')->getRealPath());
                } catch (Throwable) {
                    $validator->errors()->add('file', 'File tidak dapat dibaca sebagai dokumen Word (.docx).');

                    return;
                }

                if ($this->templateVariables === []) {
                    $validator->errors()->add('file', 'Template belum berisi variabel. Tulis variabel seperti ${nama} di dokumen Word.');
                }
            },
        ];
    }

    /**
     * Variable names found in the uploaded template.
     *
     * @return list<string>
     */
    public function templateVariables(): array
    {
        return $this->templateVariables;
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
            'max' => ':Attribute maksimal :max karakter.',
            'file.max' => 'Ukuran file maksimal '.(self::MAX_FILE_SIZE_KB / 1024).' MB.',
            'file.file' => 'File gagal diunggah. Coba lagi.',
            'file.mimes' => 'File harus berupa dokumen Word (.docx).',
            'file.extensions' => 'File harus berupa dokumen Word (.docx).',
        ];
    }

    /**
     * Get the custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama template',
            'file' => 'file template',
        ];
    }
}
