<?php

namespace App\Http\Requests;

use App\Enums\Citizenship;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Religion;
use App\Models\Penduduk;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Create or update a resident (penduduk). On update the NIK may stay the resident's own.
 */
class PendudukRequest extends FormRequest
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
        return self::residentRules($this->route('penduduk'));
    }

    /**
     * Rules for one resident, also used for every row of an Excel import.
     *
     * @param  Penduduk|null  $resident  The resident being edited, whose own NIK stays allowed.
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public static function residentRules(?Penduduk $resident = null): array
    {
        return [
            'nik' => ['required', 'digits:16', Rule::unique('penduduk', 'nik')->ignore($resident)],
            'nama' => ['required', 'string', 'max:100'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['bail', 'required', 'date_format:Y-m-d', 'after:1900-01-01', 'before_or_equal:today'],
            'jenis_kelamin' => ['required', Rule::enum(Gender::class)],
            'alamat_lengkap' => ['nullable', 'string', 'max:1000'],
            'agama' => ['nullable', Rule::enum(Religion::class)],
            'status_perkawinan' => ['nullable', Rule::enum(MaritalStatus::class)],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'kewarganegaraan' => ['required', Rule::enum(Citizenship::class)],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return self::residentMessages();
    }

    /**
     * @return array<string, string>
     */
    public static function residentMessages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'max' => ':Attribute maksimal :max karakter.',
            'nik.digits' => 'NIK harus terdiri dari 16 angka.',
            'nik.unique' => 'NIK ini sudah terdaftar.',
            'date_format' => ':Attribute harus berupa tanggal yang valid.',
            'tanggal_lahir.after' => ':Attribute tidak boleh sebelum tahun 1900.',
            'tanggal_lahir.before_or_equal' => ':Attribute tidak boleh melewati hari ini.',
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
        return self::residentAttributes();
    }

    /**
     * @return array<string, string>
     */
    public static function residentAttributes(): array
    {
        return [
            'nik' => 'NIK',
            'nama' => 'nama',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'jenis_kelamin' => 'jenis kelamin',
            'alamat_lengkap' => 'alamat lengkap',
            'agama' => 'agama',
            'status_perkawinan' => 'status perkawinan',
            'pekerjaan' => 'pekerjaan',
            'kewarganegaraan' => 'kewarganegaraan',
        ];
    }
}
