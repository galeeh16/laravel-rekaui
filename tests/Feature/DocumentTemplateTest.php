<?php

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

/**
 * A real .docx upload whose body is the given paragraphs.
 *
 * @param  list<string>  $paragraphs
 */
function wordUpload(array $paragraphs, string $fileName = 'surat-keterangan.docx'): UploadedFile
{
    $phpWord = new PhpWord;
    $section = $phpWord->addSection();

    foreach ($paragraphs as $paragraph) {
        $section->addText($paragraph);
    }

    $path = tempnam(sys_get_temp_dir(), 'template-test-');
    IOFactory::createWriter($phpWord, 'Word2007')->save($path);

    return new UploadedFile($path, $fileName, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true);
}

/**
 * A stored template for `$user` with its .docx file on the faked local disk.
 *
 * @param  list<string>  $paragraphs
 */
function storedTemplate(User $user, array $paragraphs): DocumentTemplate
{
    $file = wordUpload($paragraphs);

    return DocumentTemplate::factory()->for($user)->create([
        'path' => $file->store(DocumentTemplate::STORAGE_DIRECTORY, DocumentTemplate::STORAGE_DISK),
        'variables' => DocumentTemplate::extractVariables($file->getRealPath()),
    ]);
}

/**
 * The main document XML of a downloaded .docx response. The temporary file is removed afterwards.
 */
function downloadedDocumentXml(TestResponse $response): string
{
    $path = $response->baseResponse->getFile()->getPathname();
    $zip = new ZipArchive;
    $zip->open($path);
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    @unlink($path);

    return $xml;
}

test('guests are redirected to login from document templates', function () {
    $this->get(route('document-templates.index'))->assertRedirect(route('login'));
});

test('the index lists uploaded templates with their variables', function () {
    $user = User::factory()->create();
    DocumentTemplate::factory()->for($user)->create(['name' => 'Surat domisili', 'variables' => ['nama', 'alamat']]);

    $this->actingAs($user)
        ->get(route('document-templates.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DocumentTemplates/Index')
            ->has('templates.data', 1)
            ->where('templates.data.0.name', 'Surat domisili')
            ->where('templates.data.0.variables', ['nama', 'alamat'])
            ->where('templates.data.0.uploaded_by', $user->name)
            ->where('templates.data.0.can_update', true)
            ->where('templates.data.0.can_delete', true)
        );
});

test('other users cannot rename or delete a template from the list', function () {
    DocumentTemplate::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('document-templates.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('templates.data.0.can_update', false)
            ->where('templates.data.0.can_delete', false)
        );
});

test('the list searches templates by name, file name and variable', function (string $search, array $expectedNames) {
    $user = User::factory()->create();
    DocumentTemplate::factory()->for($user)->create(['name' => 'Surat domisili', 'original_filename' => 'domisili.docx', 'variables' => ['nama']]);
    DocumentTemplate::factory()->for($user)->create(['name' => 'Surat kuasa', 'original_filename' => 'kuasa-v2.docx', 'variables' => ['nama', 'npwp']]);

    $this->actingAs($user)
        ->get(route('document-templates.index', ['q' => $search]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('search', $search)
            ->where('templates.data', fn ($templates) => collect($templates)->pluck('name')->sort()->values()->all() === $expectedNames)
        );
})->with([
    'name, case-insensitive' => ['DOMISILI', ['Surat domisili']],
    'file name' => ['kuasa-v2', ['Surat kuasa']],
    'variable' => ['npwp', ['Surat kuasa']],
    'no match' => ['tidak-ada', []],
]);

test('the list respects the selected rows per page and keeps the search in page links', function () {
    $user = User::factory()->create();
    DocumentTemplate::factory()->for($user)->count(7)->create(['name' => 'Surat keterangan']);

    $this->actingAs($user)
        ->get(route('document-templates.index', ['per_page' => 5, 'q' => 'keterangan']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('templates.data', 5)
            ->where('templates.total', 7)
            ->where('templates.last_page', 2)
            ->where('perPage', 5)
            ->where('templates.next_page_url', fn (string $url) => str_contains($url, 'per_page=5') && str_contains($url, 'q=keterangan'))
        );
});

test('the list falls back to the default rows per page for unsupported values', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('document-templates.index', ['per_page' => 7]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('perPage', 10)
            ->where('templates.per_page', 10)
        );
});

test('the create page shows the upload form with the size limit', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('document-templates.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DocumentTemplates/Create')
            ->where('maxFileSizeMb', 10)
        );
});

test('uploading a template stores the file and reads its variables in order', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('document-templates.store'), [
        'file' => wordUpload(['Nama: ${nama}', 'NIK: ${nik}', 'Alamat: ${alamat}', 'Ttd: ${nama}']),
    ]);

    $template = DocumentTemplate::sole();

    $response->assertRedirect(route('document-templates.index'))
        ->assertInertiaFlash('toast.title', 'Template berhasil diunggah')
        ->assertInertiaFlash('toast.description', '"surat-keterangan" tersimpan dengan 3 variabel.')
        ->assertInertiaFlash('toast.variant', 'success');
    expect($template->name)->toBe('surat-keterangan')
        ->and($template->original_filename)->toBe('surat-keterangan.docx')
        ->and($template->variables)->toBe(['nama', 'nik', 'alamat'])
        ->and($template->user_id)->toBe($user->id);
    Storage::disk(DocumentTemplate::STORAGE_DISK)->assertExists($template->path);
});

test('an uploaded template uses the given name', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);

    $this->actingAs(User::factory()->create())->post(route('document-templates.store'), [
        'name' => '  Surat keterangan domisili  ',
        'file' => wordUpload(['${nama}']),
    ]);

    expect(DocumentTemplate::sole()->name)->toBe('Surat keterangan domisili');
});

test('block markers and unsupported variable names are not form fields', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);

    $this->actingAs(User::factory()->create())->post(route('document-templates.store'), [
        'file' => wordUpload(['${nama}', '${daftar}', '${/daftar}', '${tempat lahir}', '${no_hp}']),
    ]);

    expect(DocumentTemplate::sole()->variables)->toBe(['nama', 'no_hp']);
});

test('uploading rejects files that are not word documents', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);

    $this->actingAs(User::factory()->create())
        ->post(route('document-templates.store'), ['file' => UploadedFile::fake()->create('surat.pdf', 20, 'application/pdf')])
        ->assertSessionHasErrors(['file' => 'File harus berupa dokumen Word (.docx).']);

    expect(DocumentTemplate::count())->toBe(0);
});

test('uploading rejects templates without variables', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);

    $this->actingAs(User::factory()->create())
        ->post(route('document-templates.store'), ['file' => wordUpload(['Surat tanpa variabel.'])])
        ->assertSessionHasErrors(['file' => 'Template belum berisi variabel. Tulis variabel seperti ${nama} di dokumen Word.']);

    expect(DocumentTemplate::count())->toBe(0);
});

test('uploading requires a file', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('document-templates.store'), [])
        ->assertSessionHasErrors(['file' => 'File template wajib diisi.']);
});

test('the fill-in page shows a labelled field for every variable', function () {
    $user = User::factory()->create();
    $template = DocumentTemplate::factory()->for($user)->create(['variables' => ['nama', 'nik', 'tempat_lahir']]);

    $this->actingAs($user)
        ->get(route('document-templates.show', $template))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DocumentTemplates/Show')
            ->where('template.fields', [
                ['name' => 'nama', 'label' => 'Nama'],
                ['name' => 'nik', 'label' => 'NIK'],
                ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir'],
            ])
        );
});

test('generating fills every variable and escapes special characters', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);
    $user = User::factory()->create();
    $template = storedTemplate($user, ['Nama: ${nama}', 'NIK: ${nik}', 'Alamat: ${alamat}']);

    $response = $this->actingAs($user)->post(route('document-templates.generate', $template), [
        'values' => [
            'nama' => 'Budi & Sons <PT>',
            'nik' => '3201234567890001',
            'alamat' => "Jl. Merdeka No. 1\nBandung",
        ],
    ]);

    $response->assertOk()->assertDownload();
    $xml = downloadedDocumentXml($response);

    expect($xml)->toContain('Budi &amp; Sons &lt;PT&gt;')
        ->toContain('3201234567890001')
        ->toContain('Jl. Merdeka No. 1</w:t><w:br/><w:t>Bandung')
        ->not->toContain('${');
});

test('generating requires a value for every variable', function () {
    $user = User::factory()->create();
    $template = DocumentTemplate::factory()->for($user)->create(['variables' => ['nama', 'nik']]);

    $this->actingAs($user)
        ->postJson(route('document-templates.generate', $template), ['values' => ['nama' => 'Budi']])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['values.nik' => 'NIK wajib diisi.'])
        ->assertJsonMissingValidationErrors(['values.nama']);
});

test('the uploader can rename a template', function () {
    $user = User::factory()->create();
    $template = DocumentTemplate::factory()->for($user)->create(['name' => 'Surat lama']);

    $this->actingAs($user)
        ->from(route('document-templates.index', ['page' => 2]))
        ->patch(route('document-templates.update', $template), ['name' => '  Surat domisili  '])
        ->assertRedirect(route('document-templates.index', ['page' => 2]));

    $template->refresh();

    expect($template->name)->toBe('Surat domisili')
        ->and($template->updated_by_id)->toBe($user->id)
        ->and($template->user_id)->toBe($user->id);
});

test('the list shows who last edited a template', function () {
    $uploader = User::factory()->create();
    $editor = User::factory()->create();
    DocumentTemplate::factory()->for($uploader)->create(['name' => 'Belum diubah']);
    DocumentTemplate::factory()->for($uploader)->create(['name' => 'Sudah diubah', 'updated_by_id' => $editor->id]);

    $this->actingAs($uploader)
        ->get(route('document-templates.index', ['q' => 'diubah']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('templates.data.0.name', 'Sudah diubah')
            ->where('templates.data.0.uploaded_by', $uploader->name)
            ->where('templates.data.0.was_edited', true)
            ->where('templates.data.0.updated_by', $editor->name)
            ->where('templates.data.1.name', 'Belum diubah')
            ->where('templates.data.1.was_edited', false)
            ->where('templates.data.1.updated_by', null)
        );
});

test('renaming a template requires a name of at most 255 characters', function (string $name) {
    $user = User::factory()->create();
    $template = DocumentTemplate::factory()->for($user)->create(['name' => 'Surat lama']);

    $this->actingAs($user)
        ->patch(route('document-templates.update', $template), ['name' => $name])
        ->assertSessionHasErrors('name');

    expect($template->refresh()->name)->toBe('Surat lama');
})->with([
    'empty' => '',
    'only spaces' => '   ',
    'too long' => str_repeat('a', 256),
]);

test('other users cannot rename a template', function () {
    $template = DocumentTemplate::factory()->create(['name' => 'Surat lama']);

    $this->actingAs(User::factory()->create())
        ->patch(route('document-templates.update', $template), ['name' => 'Surat baru'])
        ->assertForbidden();

    expect($template->refresh()->name)->toBe('Surat lama');
});

test('the uploader can delete a template and its file', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);
    $user = User::factory()->create();
    $template = storedTemplate($user, ['${nama}']);

    $this->actingAs($user)
        ->delete(route('document-templates.destroy', $template))
        ->assertRedirect(route('document-templates.index'));

    $this->assertModelMissing($template);
    Storage::disk(DocumentTemplate::STORAGE_DISK)->assertMissing($template->path);
});

test('other users cannot delete a template', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);
    $template = storedTemplate(User::factory()->create(), ['${nama}']);

    $this->actingAs(User::factory()->create())
        ->delete(route('document-templates.destroy', $template))
        ->assertForbidden();

    $this->assertModelExists($template);
    Storage::disk(DocumentTemplate::STORAGE_DISK)->assertExists($template->path);
});

test('the original template can be downloaded', function () {
    Storage::fake(DocumentTemplate::STORAGE_DISK);
    $user = User::factory()->create();
    $template = storedTemplate($user, ['${nama}']);

    $this->actingAs($user)
        ->get(route('document-templates.download', $template))
        ->assertOk()
        ->assertDownload($template->original_filename);
});

test('the sample template contains the nama, nik and alamat_lengkap variables', function () {
    $response = $this->actingAs(User::factory()->create())
        ->get(route('document-templates.sample'))
        ->assertOk()
        ->assertDownload('contoh-template-surat-keterangan.docx');

    $path = $response->baseResponse->getFile()->getPathname();
    $variables = DocumentTemplate::extractVariables($path);
    @unlink($path);

    expect($variables)->toBe(['nomor_surat', 'nama', 'nik', 'alamat_lengkap', 'tanggal_surat', 'nama_penandatangan']);
});
