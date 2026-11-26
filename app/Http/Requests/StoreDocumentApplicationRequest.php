<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentApplicationRequest extends FormRequest
{
    public const MAX_PURPOSE_LENGTH = 500;

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
            'penduduk_id' => ['required', 'integer', 'exists:penduduk,id'],
            'document_template_id' => ['required', 'integer', 'exists:document_templates,id'],
            'purpose' => ['nullable', 'string', 'max:'.self::MAX_PURPOSE_LENGTH],
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
            'penduduk_id.required' => 'Pilih pemohon dari data penduduk dengan mengetik NIK-nya.',
            'penduduk_id.integer' => 'Pemohon tidak ditemukan di data penduduk.',
            'penduduk_id.exists' => 'Pemohon tidak ditemukan di data penduduk.',
            'document_template_id.required' => 'Pilih dokumen yang diajukan.',
            'document_template_id.integer' => 'Dokumen tidak ditemukan. Muat ulang halaman, lalu pilih lagi.',
            'document_template_id.exists' => 'Dokumen tidak ditemukan. Muat ulang halaman, lalu pilih lagi.',
            'purpose.max' => 'Keperluan maksimal :max karakter.',
        ];
    }
}
