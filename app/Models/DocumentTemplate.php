<?php

namespace App\Models;

use Database\Factories\DocumentTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * An uploaded Word (.docx) template with `${variable}` placeholders, filled in with PHPWord's TemplateProcessor.
 */
#[Fillable(['user_id', 'updated_by_id', 'name', 'original_filename', 'path', 'size', 'variables'])]
class DocumentTemplate extends Model
{
    /** @use HasFactory<DocumentTemplateFactory> */
    use HasFactory;

    /**
     * Private disk and folder where template files are stored. PHPWord needs a local file path.
     */
    public const STORAGE_DISK = 'local';

    public const STORAGE_DIRECTORY = 'document-templates';

    /**
     * Variable names that are shown in capitals instead of title case.
     *
     * @var list<string>
     */
    private const ACRONYMS = ['nik', 'nip', 'npwp', 'nisn', 'kk', 'ktp', 'rt', 'rw', 'sk'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'size' => 'integer',
        ];
    }

    /**
     * The user who uploaded the template.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The user who last edited the template, null while it has never been edited.
     *
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    /**
     * Whether the template was changed after it was uploaded.
     */
    public function wasEdited(): bool
    {
        return $this->updated_by_id !== null || $this->updated_at->gt($this->created_at);
    }

    /**
     * Read the `${variable}` names from a .docx file, in order of first appearance and without duplicates.
     *
     * Block markers (`${name}` … `${/name}`) and names other than letters, digits, `_` and `-` are left out,
     * because they cannot be filled as a single value.
     *
     * @return list<string>
     */
    public static function extractVariables(string $absolutePath): array
    {
        $names = (new TemplateProcessor($absolutePath))->getVariables();

        $closedBlocks = collect($names)
            ->filter(fn (string $name): bool => str_starts_with($name, '/'))
            ->map(fn (string $name): string => substr($name, 1));

        return collect($names)
            ->reject(fn (string $name): bool => str_starts_with($name, '/') || $closedBlocks->contains($name))
            ->filter(fn (string $name): bool => (bool) preg_match('/^[A-Za-z0-9_-]+$/', $name))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Human-readable label for a variable name, e.g. `tempat_lahir` → "Tempat Lahir", `nik` → "NIK".
     */
    public static function labelFor(string $variable): string
    {
        return Str::of($variable)
            ->replace(['_', '-'], ' ')
            ->explode(' ')
            ->filter()
            ->map(fn (string $word): string => in_array(Str::lower($word), self::ACRONYMS, true) ? Str::upper($word) : Str::ucfirst(Str::lower($word)))
            ->join(' ');
    }

    /**
     * Fill the template's variables and write the result to a temporary .docx file.
     *
     * Values are XML-escaped here (PHPWord does not escape by default), and line breaks become
     * line breaks in Word.
     *
     * @param  array<string, string|null>  $values  Keyed by variable name.
     * @return string Absolute path of the generated file; the caller deletes it.
     */
    public function generate(array $values): string
    {
        $processor = new TemplateProcessor(Storage::disk(self::STORAGE_DISK)->path($this->path));

        foreach ($this->variables as $variable) {
            $value = str_replace("\r\n", "\n", (string) ($values[$variable] ?? ''));

            $processor->setValue($variable, htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
        }

        $outputPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'document-'.Str::uuid().'.docx';
        $processor->saveAs($outputPath);

        return $outputPath;
    }
}
