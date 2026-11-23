<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\Hobby;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreLetterRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['required', 'date_format:Y-m-d', 'after:1900-01-01', 'before:today'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'hobby' => ['required', Rule::enum(Hobby::class)],
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
            'max' => ':Attribute maksimal :max karakter.',
            'date_format' => ':Attribute harus berupa tanggal yang valid.',
            'birth_date.after' => ':Attribute tidak boleh sebelum tahun 1900.',
            'birth_date.before' => ':Attribute harus sebelum hari ini.',
            // Rule objects are matched by class name, not by a short rule name.
            Enum::class => ':Attribute yang dipilih tidak valid.',
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
            'first_name' => 'nama depan',
            'last_name' => 'nama belakang',
            'birth_date' => 'tanggal lahir',
            'gender' => 'jenis kelamin',
            'hobby' => 'hobi',
        ];
    }
}
