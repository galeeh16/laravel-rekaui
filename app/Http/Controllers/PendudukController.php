<?php

namespace App\Http\Controllers;

use App\Enums\Citizenship;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Religion;
use App\Http\Requests\ImportPendudukRequest;
use App\Http\Requests\PendudukRequest;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/**
 * List, add, edit and delete residents (penduduk).
 */
class PendudukController extends Controller
{
    /**
     * Allowed "rows per page" choices on the list.
     *
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [5, 10, 25];

    private const DEFAULT_PER_PAGE = 10;

    private const MIN_SEARCH_DIGITS = 3;

    private const MAX_SEARCH_RESULTS = 8;

    /**
     * List residents, searchable by name or NIK.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('q')->trim()->toString();

        $perPage = $request->integer('per_page', self::DEFAULT_PER_PAGE);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : self::DEFAULT_PER_PAGE;

        $residents = Penduduk::query()
            ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->whereLike('nama', "%{$search}%")
                ->orWhereLike('nik', "%{$search}%")
            ))
            ->orderBy('nama')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Penduduk $resident): array => [
                'id' => $resident->id,
                'nik' => $resident->nik,
                'nama' => $resident->nama,
                'tempat_lahir' => $resident->tempat_lahir,
                'tanggal_lahir' => $resident->tanggal_lahir->toDateString(),
                'jenis_kelamin' => $resident->jenis_kelamin->label(),
                'alamat_lengkap' => $resident->alamat_lengkap,
                'agama' => $resident->agama?->label(),
                'status_perkawinan' => $resident->status_perkawinan?->label(),
                'pekerjaan' => $resident->pekerjaan,
                'kewarganegaraan' => $resident->kewarganegaraan->value,
                'created_at' => $resident->created_at?->toIso8601String(),
                'updated_at' => $resident->updated_at?->toIso8601String(),
            ]);

        return Inertia::render('Penduduk/Index', [
            'residents' => $residents,
            'search' => $search,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    /**
     * Find residents whose NIK starts with the typed digits (at least three), for filling in documents.
     * Each result carries its values formatted for a letter, keyed by database field.
     */
    public function search(Request $request): JsonResponse
    {
        $nik = preg_replace('/\D/', '', $request->string('nik')->toString());

        if (strlen($nik) < self::MIN_SEARCH_DIGITS) {
            return response()->json(['data' => []]);
        }

        $residents = Penduduk::query()
            ->where('nik', 'like', "{$nik}%")
            ->orderBy('nik')
            ->limit(self::MAX_SEARCH_RESULTS)
            ->get()
            ->map(fn (Penduduk $resident): array => [
                'id' => $resident->id,
                'nik' => $resident->nik,
                'nama' => $resident->nama,
                'fields' => $this->documentFields($resident),
            ]);

        return response()->json(['data' => $residents]);
    }

    /**
     * Show the form for adding a resident.
     */
    public function create(): Response
    {
        return Inertia::render('Penduduk/Create', [
            'options' => $this->formOptions(),
            'importSettings' => [
                'maxFileSizeMb' => ImportPendudukRequest::MAX_FILE_SIZE_KB / 1024,
                'maxRows' => ImportPendudukRequest::MAX_ROWS,
                'columns' => array_keys(ImportPendudukRequest::COLUMNS),
            ],
        ]);
    }

    /**
     * Store a new resident.
     */
    public function store(PendudukRequest $request): RedirectResponse
    {
        $resident = Penduduk::create($request->validated());

        Inertia::flash('success', "Data penduduk {$resident->nama} berhasil ditambahkan.");

        return to_route('penduduk.index');
    }

    /**
     * Store every resident of an uploaded Excel or CSV file. The request has validated all rows,
     * so either every row is stored or none.
     */
    public function import(ImportPendudukRequest $request): RedirectResponse
    {
        $now = now();
        $residents = collect($request->residents())
            ->map(fn (array $resident): array => [...$resident, 'created_at' => $now, 'updated_at' => $now]);

        DB::transaction(function () use ($residents): void {
            $residents->chunk(500)->each(fn (Collection $chunk) => Penduduk::insert($chunk->values()->all()));
        });

        Inertia::flash('success', "{$residents->count()} data penduduk berhasil diimpor.");

        return to_route('penduduk.index');
    }

    /**
     * Download an empty Excel template for the import, with a sheet explaining every column.
     */
    public function importTemplate(): BinaryFileResponse
    {
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'template-penduduk-'.Str::uuid().'.xlsx';

        $this->writeImportTemplate($path);

        return response()->download($path, 'template-import-penduduk.xlsx')->deleteFileAfterSend();
    }

    /**
     * Show the form for editing a resident.
     */
    public function edit(Penduduk $penduduk): Response
    {
        return Inertia::render('Penduduk/Edit', [
            'resident' => [
                'id' => $penduduk->id,
                'nik' => $penduduk->nik,
                'nama' => $penduduk->nama,
                'tempat_lahir' => $penduduk->tempat_lahir,
                'tanggal_lahir' => $penduduk->tanggal_lahir->toDateString(),
                'jenis_kelamin' => $penduduk->jenis_kelamin->value,
                'alamat_lengkap' => $penduduk->alamat_lengkap,
                'agama' => $penduduk->agama?->value,
                'status_perkawinan' => $penduduk->status_perkawinan?->value,
                'pekerjaan' => $penduduk->pekerjaan,
                'kewarganegaraan' => $penduduk->kewarganegaraan->value,
            ],
            'options' => $this->formOptions(),
        ]);
    }

    /**
     * Update a resident.
     */
    public function update(PendudukRequest $request, Penduduk $penduduk): RedirectResponse
    {
        $penduduk->update($request->validated());

        Inertia::flash('success', "Data penduduk {$penduduk->nama} berhasil diperbarui.");

        return to_route('penduduk.index');
    }

    /**
     * Delete a resident and go back to the list page the user was on.
     */
    public function destroy(Penduduk $penduduk): RedirectResponse
    {
        $penduduk->delete();

        Inertia::flash('success', "Data penduduk {$penduduk->nama} dihapus.");

        return back();
    }

    /**
     * Write the import template: a "Penduduk" sheet with the column names (NIK formatted as text so Excel keeps
     * all 16 digits, birth date as a date) and a "Petunjuk" sheet describing each column.
     */
    private function writeImportTemplate(string $path): void
    {
        $optionLabels = fn (array $options): string => collect($options)->pluck('label')->join(', ');

        $guide = [
            ['Kolom', 'Wajib', 'Keterangan'],
            ['NIK', 'Ya', '16 angka, belum terdaftar. Contoh: 3201234567890001'],
            ['Nama', 'Ya', 'Nama lengkap. Contoh: Budi Santoso'],
            ['Tempat Lahir', 'Ya', 'Contoh: Bandung'],
            ['Tanggal Lahir', 'Ya', 'Format tanggal Excel, atau teks 01-05-1990 / 01/05/1990 / 1990-05-01'],
            ['Jenis Kelamin', 'Ya', $optionLabels(Gender::options()).' (boleh L / P)'],
            ['Alamat Lengkap', 'Tidak', 'Contoh: Jl. Merdeka No. 1, Bandung'],
            ['Agama', 'Tidak', $optionLabels(Religion::options())],
            ['Status Perkawinan', 'Tidak', $optionLabels(MaritalStatus::options())],
            ['Pekerjaan', 'Tidak', 'Contoh: Wiraswasta'],
            ['Kewarganegaraan', 'Tidak', 'WNI atau WNA. Kosong = WNI'],
        ];

        $columnWidths = [20, 28, 18, 16, 16, 40, 14, 20, 20, 18];
        $dataColumns = collect($columnWidths)->map(fn (int $width, int $index): string => sprintf(
            '<col min="%1$d" max="%1$d" width="%2$d" customWidth="1"%3$s/>',
            $index + 1,
            $width,
            match ($index) {
                0 => ' style="2"',
                3 => ' style="3"',
                default => '',
            },
        ))->join('');

        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $mainNamespace = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        $relationshipType = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml';

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            ."<Override PartName=\"/xl/workbook.xml\" ContentType=\"{$contentType}.sheet.main+xml\"/>"
            ."<Override PartName=\"/xl/worksheets/sheet1.xml\" ContentType=\"{$contentType}.worksheet+xml\"/>"
            ."<Override PartName=\"/xl/worksheets/sheet2.xml\" ContentType=\"{$contentType}.worksheet+xml\"/>"
            ."<Override PartName=\"/xl/styles.xml\" ContentType=\"{$contentType}.styles+xml\"/>"
            .'</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            ."<Relationship Id=\"rId1\" Type=\"{$relationshipType}/officeDocument\" Target=\"xl/workbook.xml\"/>"
            .'</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            ."<workbook xmlns=\"{$mainNamespace}\" xmlns:r=\"{$relationshipType}\"><sheets>"
            .'<sheet name="Penduduk" sheetId="1" r:id="rId1"/><sheet name="Petunjuk" sheetId="2" r:id="rId2"/>'
            .'</sheets></workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            ."<Relationship Id=\"rId1\" Type=\"{$relationshipType}/worksheet\" Target=\"worksheets/sheet1.xml\"/>"
            ."<Relationship Id=\"rId2\" Type=\"{$relationshipType}/worksheet\" Target=\"worksheets/sheet2.xml\"/>"
            ."<Relationship Id=\"rId3\" Type=\"{$relationshipType}/styles\" Target=\"styles.xml\"/>"
            .'</Relationships>');

        /* Styles: 0 default, 1 bold header, 2 text (NIK), 3 date. */
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            ."<styleSheet xmlns=\"{$mainNamespace}\">"
            .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            .'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="4">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            .'<xf numFmtId="14" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>');

        $zip->addFromString('xl/worksheets/sheet1.xml', $this->worksheetXml([array_keys(ImportPendudukRequest::COLUMNS)], "<cols>{$dataColumns}</cols>"));
        $zip->addFromString('xl/worksheets/sheet2.xml', $this->worksheetXml($guide, '<cols><col min="1" max="1" width="20" customWidth="1"/><col min="2" max="2" width="8" customWidth="1"/><col min="3" max="3" width="90" customWidth="1"/></cols>'));

        $zip->close();
    }

    /**
     * A worksheet with text cells; the first row is bold.
     *
     * @param  list<list<string>>  $rows
     */
    private function worksheetXml(array $rows, string $columns): string
    {
        $rowsXml = collect($rows)->map(function (array $cells, int $rowIndex): string {
            $cellsXml = collect($cells)->map(fn (string $value, int $columnIndex): string => sprintf(
                '<c r="%s%d" t="inlineStr"%s><is><t>%s</t></is></c>',
                chr(65 + $columnIndex),
                $rowIndex + 1,
                $rowIndex === 0 ? ' s="1"' : '',
                htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8'),
            ))->join('');

            return sprintf('<row r="%d">%s</row>', $rowIndex + 1, $cellsXml);
        })->join('');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .$columns
            ."<sheetData>{$rowsXml}</sheetData>"
            .'</worksheet>';
    }

    /**
     * A resident's values as they should read in a document: labels instead of stored codes and an
     * Indonesian birth date ("1 Mei 1990"). Empty fields are left out so they never clear a typed value.
     *
     * @return array<string, string>
     */
    private function documentFields(Penduduk $resident): array
    {
        return array_filter([
            'nik' => $resident->nik,
            'nama' => $resident->nama,
            'tempat_lahir' => $resident->tempat_lahir,
            'tanggal_lahir' => $resident->tanggal_lahir->locale('id')->translatedFormat('j F Y'),
            'jenis_kelamin' => $resident->jenis_kelamin->label(),
            'alamat_lengkap' => $resident->alamat_lengkap,
            'agama' => $resident->agama?->label(),
            'status_perkawinan' => $resident->status_perkawinan?->label(),
            'pekerjaan' => $resident->pekerjaan,
            'kewarganegaraan' => $resident->kewarganegaraan->value,
        ], fn (?string $value): bool => $value !== null && $value !== '');
    }

    /**
     * Choices for the select and radio fields of the form.
     *
     * @return array<string, list<array{value: string, label: string}>>
     */
    private function formOptions(): array
    {
        return [
            'genders' => Gender::options(),
            'religions' => Religion::options(),
            'maritalStatuses' => MaritalStatus::options(),
            'citizenships' => Citizenship::options(),
        ];
    }
}
