<?php

use App\Enums\Citizenship;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Religion;
use App\Http\Requests\ImportPendudukRequest;
use App\Models\Penduduk;
use App\Models\User;
use avadim\FastExcelReader\Excel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

/**
 * An .xlsx upload with one sheet. Cells are strings, numbers (int/float), or `['date' => 'Y-m-d']`
 * for a real Excel date cell (a serial number with a date format).
 *
 * @param  list<list<string|int|float|array{date: string}|null>>  $rows
 */
function xlsxUpload(array $rows, string $fileName = 'penduduk.xlsx'): UploadedFile
{
    $rowsXml = collect($rows)->map(function (array $cells, int $rowIndex): string {
        $cellsXml = collect($cells)->map(function ($value, int $columnIndex) use ($rowIndex): string {
            $reference = chr(65 + $columnIndex).($rowIndex + 1);

            return match (true) {
                $value === null => '',
                is_array($value) => sprintf('<c r="%s" s="1"><v>%d</v></c>', $reference, (new DateTime('1899-12-30'))->diff(new DateTime($value['date']))->days),
                is_int($value) || is_float($value) => sprintf('<c r="%s"><v>%s</v></c>', $reference, $value),
                default => sprintf('<c r="%s" t="inlineStr"><is><t>%s</t></is></c>', $reference, htmlspecialchars($value, ENT_XML1)),
            };
        })->join('');

        return sprintf('<row r="%d">%s</row>', $rowIndex + 1, $cellsXml);
    })->join('');

    $main = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    $relationships = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    $path = tempnam(sys_get_temp_dir(), 'penduduk-xlsx-');

    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
    $zip->addFromString('_rels/.rels', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><Relationships xmlns=\"http://schemas.openxmlformats.org/package/2006/relationships\"><Relationship Id=\"rId1\" Type=\"{$relationships}/officeDocument\" Target=\"xl/workbook.xml\"/></Relationships>");
    $zip->addFromString('xl/workbook.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><workbook xmlns=\"{$main}\" xmlns:r=\"{$relationships}\"><sheets><sheet name=\"Penduduk\" sheetId=\"1\" r:id=\"rId1\"/></sheets></workbook>");
    $zip->addFromString('xl/_rels/workbook.xml.rels', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><Relationships xmlns=\"http://schemas.openxmlformats.org/package/2006/relationships\"><Relationship Id=\"rId1\" Type=\"{$relationships}/worksheet\" Target=\"worksheets/sheet1.xml\"/><Relationship Id=\"rId2\" Type=\"{$relationships}/styles\" Target=\"styles.xml\"/></Relationships>");
    $zip->addFromString('xl/styles.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><styleSheet xmlns=\"{$main}\"><fonts count=\"1\"><font><sz val=\"11\"/></font></fonts><fills count=\"1\"><fill><patternFill patternType=\"none\"/></fill></fills><borders count=\"1\"><border/></borders><cellStyleXfs count=\"1\"><xf numFmtId=\"0\"/></cellStyleXfs><cellXfs count=\"2\"><xf numFmtId=\"0\" xfId=\"0\"/><xf numFmtId=\"14\" xfId=\"0\" applyNumberFormat=\"1\"/></cellXfs></styleSheet>");
    $zip->addFromString('xl/worksheets/sheet1.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><worksheet xmlns=\"{$main}\"><sheetData>{$rowsXml}</sheetData></worksheet>");
    $zip->close();

    return new UploadedFile($path, $fileName, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
}

/**
 * A .csv upload from rows of text.
 *
 * @param  list<list<string>>  $rows
 */
function csvUpload(array $rows): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'penduduk-csv-');
    $handle = fopen($path, 'w');

    foreach ($rows as $row) {
        fputcsv($handle, $row, escape: '');
    }

    fclose($handle);

    return new UploadedFile($path, 'penduduk.csv', 'text/csv', null, true);
}

/**
 * @return list<string>
 */
function importHeader(): array
{
    return array_keys(ImportPendudukRequest::COLUMNS);
}

test('guests cannot import residents', function () {
    $this->post(route('penduduk.import'), ['file' => csvUpload([importHeader()])])
        ->assertRedirect(route('login'));
});

test('the create page offers the excel import settings', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('penduduk.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('importSettings.maxFileSizeMb', 5)
            ->where('importSettings.maxRows', 1000)
            ->where('importSettings.columns', importHeader())
        );
});

test('the import template has the column names and a guide sheet', function () {
    $response = $this->actingAs(User::factory()->create())
        ->get(route('penduduk.import-template'))
        ->assertOk()
        ->assertDownload('template-import-penduduk.xlsx');

    $path = $response->baseResponse->getFile()->getPathname();
    $excel = Excel::open($path);
    $header = array_values($excel->sheet()->readRows()[1]);
    $sheetNames = array_values($excel->getSheetNames());
    @unlink($path);

    expect($header)->toBe(importHeader())
        ->and($sheetNames)->toBe(['Penduduk', 'Petunjuk']);
});

test('an excel file is imported with date cells, numeric NIKs and option labels', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.import'), ['file' => xlsxUpload([
            importHeader(),
            [3201234567890001, 'Budi Santoso', 'Bandung', ['date' => '1990-05-01'], 'Laki-laki', 'Jl. Merdeka No. 1', 'Islam', 'Kawin', 'Wiraswasta', 'WNI'],
            ['3273000000000002', 'Siti Aminah', 'Bogor', ['date' => '1985-02-03'], 'Perempuan', null, null, null, null, null],
        ])])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('penduduk.index'));

    $budi = Penduduk::query()->where('nik', '3201234567890001')->sole();
    $siti = Penduduk::query()->where('nik', '3273000000000002')->sole();

    expect(Penduduk::count())->toBe(2)
        ->and($budi->tanggal_lahir->toDateString())->toBe('1990-05-01')
        ->and($budi->jenis_kelamin)->toBe(Gender::Male)
        ->and($budi->agama)->toBe(Religion::Islam)
        ->and($budi->status_perkawinan)->toBe(MaritalStatus::Kawin)
        ->and($siti->jenis_kelamin)->toBe(Gender::Female)
        ->and($siti->alamat_lengkap)->toBeNull()
        ->and($siti->agama)->toBeNull()
        ->and($siti->kewarganegaraan)->toBe(Citizenship::Wni);
});

test('a csv file is imported with any column order, header spelling, text dates and short options', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.import'), ['file' => csvUpload([
            ['nama', 'NIK', 'TANGGAL_LAHIR', 'tempat lahir', 'Jenis Kelamin', 'Status Perkawinan', 'Kolom Lain', 'kewarganegaraan', 'Agama', 'pekerjaan', 'Alamat_Lengkap'],
            ['Budi Santoso', '3201234567890001', '01-05-1990', 'Bandung', 'L', 'Belum Kawin', 'diabaikan', '', '', '', ''],
            ['Siti Aminah', '3273000000000002', '03/02/1985', 'Bogor', 'P', 'menikah', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', '', ''],
        ])])
        ->assertSessionHasNoErrors();

    $budi = Penduduk::query()->where('nik', '3201234567890001')->sole();
    $siti = Penduduk::query()->where('nik', '3273000000000002')->sole();

    expect(Penduduk::count())->toBe(2)
        ->and($budi->tanggal_lahir->toDateString())->toBe('1990-05-01')
        ->and($budi->jenis_kelamin)->toBe(Gender::Male)
        ->and($budi->status_perkawinan)->toBe(MaritalStatus::BelumKawin)
        ->and($siti->tanggal_lahir->toDateString())->toBe('1985-02-03')
        ->and($siti->jenis_kelamin)->toBe(Gender::Female)
        ->and($siti->status_perkawinan)->toBe(MaritalStatus::Kawin);
});

test('nothing is imported when a row is invalid, and each invalid row is reported', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.import'), ['file' => csvUpload([
            importHeader(),
            ['3201234567890001', 'Budi Santoso', 'Bandung', '1990-05-01', 'Laki-laki', '', '', '', '', ''],
            ['320123', '', 'Bogor', '1985-02-03', 'Lainnya', '', '', '', '', ''],
            ['3273000000000003', 'Rina', 'Depok', 'besok', 'Perempuan', '', '', '', '', ''],
        ])])
        ->assertSessionHasErrors([
            'file' => '2 baris perlu diperbaiki. Tidak ada data yang disimpan.',
            'baris.3' => 'Baris 3: NIK harus terdiri dari 16 angka. Nama wajib diisi. Jenis kelamin yang dipilih tidak valid.',
            'baris.4' => 'Baris 4: Tanggal lahir harus berupa tanggal yang valid.',
        ]);

    expect(Penduduk::count())->toBe(0);
});

test('rows with a registered NIK or a NIK repeated in the file are reported', function () {
    Penduduk::factory()->create(['nik' => '3201234567890001']);

    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.import'), ['file' => csvUpload([
            importHeader(),
            ['3201234567890001', 'Budi Santoso', 'Bandung', '1990-05-01', 'L', '', '', '', '', ''],
            ['3273000000000002', 'Siti Aminah', 'Bogor', '1985-02-03', 'P', '', '', '', '', ''],
            ['3273000000000002', 'Siti Lain', 'Bogor', '1986-02-03', 'P', '', '', '', '', ''],
        ])])
        ->assertSessionHasErrors([
            'baris.2' => 'Baris 2: NIK ini sudah terdaftar.',
            'baris.4' => 'Baris 4: NIK sama dengan baris 3.',
        ]);

    expect(Penduduk::count())->toBe(1);
});

test('the import rejects files without the required columns or without data', function (array $rows, string $message) {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.import'), ['file' => csvUpload($rows)])
        ->assertSessionHasErrors(['file' => $message]);

    expect(Penduduk::count())->toBe(0);
})->with([
    'only some columns' => [[['Nama', 'Alamat'], ['Budi', 'Bandung']], 'Kolom berikut tidak ditemukan: NIK, Tempat Lahir, Tanggal Lahir, Jenis Kelamin, Alamat Lengkap, Agama, Status Perkawinan, Pekerjaan, Kewarganegaraan. Gunakan template yang disediakan.'],
    'one optional column missing' => [[array_values(array_diff(array_keys(ImportPendudukRequest::COLUMNS), ['Pekerjaan'])), ['3201234567890001', 'Budi', 'Bandung', '1990-05-01', 'L', '', '', '', '']], 'Kolom berikut tidak ditemukan: Pekerjaan. Gunakan template yang disediakan.'],
    'header only' => [[importHeader()], 'File tidak berisi data penduduk.'],
]);

test('the import rejects files that are not spreadsheets', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.import'), ['file' => UploadedFile::fake()->create('penduduk.pdf', 20, 'application/pdf')])
        ->assertSessionHasErrors(['file' => 'File harus berupa Excel (.xlsx, .xls) atau CSV.']);
});
