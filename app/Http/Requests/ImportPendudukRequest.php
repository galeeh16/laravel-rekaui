<?php

namespace App\Http\Requests;

use App\Enums\Citizenship;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Religion;
use App\Models\Penduduk;
use avadim\FastExcelReader\Excel;
use BackedEnum;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Throwable;

/**
 * Import residents from an Excel (.xlsx, .xls) or CSV file whose first row holds the column names.
 *
 * Every row is checked with the same rules as the single-resident form. When any row is invalid,
 * nothing is imported and the errors are reported per row (`baris.{row number}`).
 */
class ImportPendudukRequest extends FormRequest
{
    public const MAX_FILE_SIZE_KB = 5_120;

    public const MAX_ROWS = 1_000;

    /**
     * Row errors listed individually; the rest are summarised in one line.
     */
    private const MAX_LISTED_ROW_ERRORS = 50;

    /**
     * Spreadsheet column names and the field each fills. Every column must be in the file, in any order
     * (cells of optional fields may be empty). Names are matched ignoring case, spaces and `_`, so
     * "Tanggal Lahir", "TANGGAL_LAHIR" and "tanggal lahir" all work.
     *
     * @var array<string, string>
     */
    public const COLUMNS = [
        'NIK' => 'nik',
        'Nama' => 'nama',
        'Tempat Lahir' => 'tempat_lahir',
        'Tanggal Lahir' => 'tanggal_lahir',
        'Jenis Kelamin' => 'jenis_kelamin',
        'Alamat Lengkap' => 'alamat_lengkap',
        'Agama' => 'agama',
        'Status Perkawinan' => 'status_perkawinan',
        'Pekerjaan' => 'pekerjaan',
        'Kewarganegaraan' => 'kewarganegaraan',
    ];

    /**
     * Other ways people write option values in a spreadsheet, mapped to the stored value.
     *
     * @var array<string, array<string, string>>
     */
    private const OPTION_ALIASES = [
        'jenis_kelamin' => ['l' => 'male', 'lk' => 'male', 'pria' => 'male', 'laki laki' => 'male', 'p' => 'female', 'pr' => 'female', 'wanita' => 'female'],
        'agama' => ['protestan' => 'kristen', 'kristen protestan' => 'kristen', 'budha' => 'buddha', 'khonghucu' => 'konghucu', 'kong hu cu' => 'konghucu'],
        'status_perkawinan' => ['belum menikah' => 'belum_kawin', 'menikah' => 'kawin', 'cerai' => 'cerai_hidup', 'janda' => 'cerai_mati', 'duda' => 'cerai_mati'],
    ];

    /**
     * Date formats accepted for text dates (Excel date cells are read as Y-m-d already).
     *
     * @var list<string>
     */
    private const DATE_FORMATS = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'd.m.Y', 'j-n-Y', 'j/n/Y'];

    /**
     * Valid rows ready to store, filled during validation.
     *
     * @var list<array<string, string|null>>
     */
    private array $residents = [];

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
     * CSV files are often detected as plain text, hence `txt` next to the spreadsheet types.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'extensions:xlsx,xls,csv', 'max:'.self::MAX_FILE_SIZE_KB],
        ];
    }

    /**
     * Read and check the rows once the file itself is valid.
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
                    $rows = $this->readRows($this->file('file')->getRealPath());
                } catch (Throwable) {
                    $validator->errors()->add('file', 'File tidak dapat dibaca. Pastikan formatnya .xlsx, .xls, atau .csv.');

                    return;
                }

                $this->validateRows($validator, $rows);
            },
        ];
    }

    /**
     * The validated rows, ready to be stored.
     *
     * @return list<array<string, string|null>>
     */
    public function residents(): array
    {
        return $this->residents;
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.file' => 'File gagal diunggah. Coba lagi.',
            'file.mimes' => 'File harus berupa Excel (.xlsx, .xls) atau CSV.',
            'file.extensions' => 'File harus berupa Excel (.xlsx, .xls) atau CSV.',
            'file.max' => 'Ukuran file maksimal '.(self::MAX_FILE_SIZE_KB / 1024).' MB.',
        ];
    }

    /**
     * Rows of the first sheet keyed by row number, each mapped from column names to fields.
     * Columns that are not recognised are ignored, and completely empty rows are skipped.
     *
     * @return array{columns: list<string>, rows: array<int, array<string, mixed>>}
     */
    private function readRows(string $path): array
    {
        $excel = Excel::open($path);
        $excel->dateFormatter('Y-m-d');

        $sheetRows = $excel->sheet()->readRows();
        $headerRowNumber = collect($sheetRows)->search(fn (array $cells): bool => collect($cells)->filter(fn ($value): bool => $this->isFilled($value))->isNotEmpty());

        if ($headerRowNumber === false) {
            return ['columns' => [], 'rows' => []];
        }

        $knownColumns = collect(self::COLUMNS)
            ->flatMap(fn (string $field, string $label): array => [$this->normalizeHeader($label) => $field, $this->normalizeHeader($field) => $field]);

        $fieldByColumn = collect($sheetRows[$headerRowNumber])
            ->map(fn ($header): ?string => $knownColumns->get($this->normalizeHeader((string) $header)))
            ->filter();

        $rows = collect($sheetRows)
            ->filter(fn (array $cells, int $rowNumber): bool => $rowNumber > $headerRowNumber)
            ->map(fn (array $cells): array => $fieldByColumn->mapWithKeys(fn (string $field, string $column): array => [$field => $cells[$column] ?? null])->all())
            ->filter(fn (array $row): bool => collect($row)->contains(fn ($value): bool => $this->isFilled($value)));

        return ['columns' => $fieldByColumn->values()->all(), 'rows' => $rows->all()];
    }

    /**
     * @param  array{columns: list<string>, rows: array<int, array<string, mixed>>}  $sheet
     */
    private function validateRows(Validator $validator, array $sheet): void
    {
        $missingColumns = array_diff(self::COLUMNS, $sheet['columns']);

        if ($missingColumns !== []) {
            $labels = collect($missingColumns)->map(fn (string $field): string => array_search($field, self::COLUMNS, true))->join(', ');
            $validator->errors()->add('file', "Kolom berikut tidak ditemukan: {$labels}. Gunakan template yang disediakan.");

            return;
        }

        if ($sheet['rows'] === []) {
            $validator->errors()->add('file', 'File tidak berisi data penduduk.');

            return;
        }

        if (count($sheet['rows']) > self::MAX_ROWS) {
            $validator->errors()->add('file', 'Maksimal '.number_format(self::MAX_ROWS, 0, ',', '.').' baris per file. Bagi data ke beberapa file.');

            return;
        }

        $rowErrors = $this->rowErrors($sheet['rows']);

        if ($rowErrors === []) {
            return;
        }

        $validator->errors()->add('file', count($rowErrors).' baris perlu diperbaiki. Tidak ada data yang disimpan.');

        foreach (array_slice($rowErrors, 0, self::MAX_LISTED_ROW_ERRORS, true) as $rowNumber => $message) {
            $validator->errors()->add("baris.{$rowNumber}", $message);
        }

        if (count($rowErrors) > self::MAX_LISTED_ROW_ERRORS) {
            $validator->errors()->add('baris.lainnya', 'Dan '.(count($rowErrors) - self::MAX_LISTED_ROW_ERRORS).' baris lainnya.');
        }
    }

    /**
     * Validate every row; fill `$this->residents` when all are valid.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, string> Error message per row number.
     */
    private function rowErrors(array $rows): array
    {
        $rules = PendudukRequest::residentRules();
        $rules['nik'] = ['required', 'digits:16'];

        $normalizedRows = collect($rows)->map(fn (array $row): array => $this->normalizeRow($row));
        $registeredNiks = Penduduk::query()->whereIn('nik', $normalizedRows->pluck('nik')->filter()->unique())->pluck('nik')->flip();

        $firstRowByNik = [];
        $errors = [];
        $residents = [];

        foreach ($normalizedRows as $rowNumber => $row) {
            $rowValidator = ValidatorFactory::make($row, $rules, PendudukRequest::residentMessages(), PendudukRequest::residentAttributes());
            $messages = $rowValidator->errors()->all();

            if ($row['nik'] !== null && $registeredNiks->has($row['nik'])) {
                $messages[] = 'NIK ini sudah terdaftar.';
            } elseif ($row['nik'] !== null && isset($firstRowByNik[$row['nik']])) {
                $messages[] = "NIK sama dengan baris {$firstRowByNik[$row['nik']]}.";
            }

            if ($row['nik'] !== null) {
                $firstRowByNik[$row['nik']] ??= $rowNumber;
            }

            if ($messages !== []) {
                $errors[$rowNumber] = "Baris {$rowNumber}: ".implode(' ', $messages);

                continue;
            }

            $residents[] = $rowValidator->validated();
        }

        if ($errors === []) {
            $this->residents = $residents;
        }

        return $errors;
    }

    /**
     * Turn spreadsheet values into what the form would send: trimmed text, `Y-m-d` dates,
     * option values instead of labels, and null for empty cells.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, string|null>
     */
    private function normalizeRow(array $row): array
    {
        $text = fn (string $field): ?string => $this->isFilled($row[$field] ?? null) ? trim((string) $row[$field]) : null;

        $nik = $row['nik'] ?? null;
        $nik = is_int($nik) || is_float($nik) ? sprintf('%.0f', $nik) : $text('nik');

        return [
            'nik' => $nik === null ? null : preg_replace('/[\s\'.-]/', '', $nik),
            'nama' => $text('nama'),
            'tempat_lahir' => $text('tempat_lahir'),
            'tanggal_lahir' => $this->normalizeDate($row['tanggal_lahir'] ?? null),
            'jenis_kelamin' => $this->normalizeOption('jenis_kelamin', Gender::class, $text('jenis_kelamin')),
            'alamat_lengkap' => $text('alamat_lengkap'),
            'agama' => $this->normalizeOption('agama', Religion::class, $text('agama')),
            'status_perkawinan' => $this->normalizeOption('status_perkawinan', MaritalStatus::class, $text('status_perkawinan')),
            'pekerjaan' => $text('pekerjaan'),
            'kewarganegaraan' => $this->normalizeOption('kewarganegaraan', Citizenship::class, $text('kewarganegaraan')) ?? Citizenship::Wni->value,
        ];
    }

    /**
     * A date cell (already `Y-m-d`), an Excel serial number, or text in a common Indonesian format.
     * Anything else is returned as it is, so validation reports it.
     */
    private function normalizeDate(mixed $value): ?string
    {
        if (! $this->isFilled($value)) {
            return null;
        }

        if (is_numeric($value) && $value > 0 && $value < 100_000) {
            return CarbonImmutable::create(1899, 12, 30)->addDays((int) $value)->toDateString();
        }

        $value = trim((string) $value);

        foreach (self::DATE_FORMATS as $format) {
            $date = DateTimeImmutable::createFromFormat("!{$format}", $value);

            if ($date !== false && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        return $value;
    }

    /**
     * Map a label ("Laki-laki"), a stored value ("male") or a common alias ("L") to the stored value.
     * Unknown text is returned unchanged, so validation reports it.
     *
     * @param  class-string<BackedEnum>  $enum
     */
    private function normalizeOption(string $field, string $enum, ?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $key = $this->normalizeHeader($value);

        foreach ($enum::cases() as $case) {
            $matches = [$this->normalizeHeader((string) $case->value), $this->normalizeHeader($case->label())];

            if (in_array($key, $matches, true)) {
                return (string) $case->value;
            }
        }

        return self::OPTION_ALIASES[$field][$key] ?? $value;
    }

    /**
     * Lower case with `_`, `-` and repeated spaces turned into single spaces: "Tanggal_Lahir" → "tanggal lahir".
     */
    private function normalizeHeader(string $value): string
    {
        return Str::of($value)->lower()->replaceMatches('/[\s_\-]+/', ' ')->trim()->toString();
    }

    private function isFilled(mixed $value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }
}
