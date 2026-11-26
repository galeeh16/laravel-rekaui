<?php

use App\Models\DocumentApplication;
use App\Models\DocumentTemplate;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('guests are redirected to login from document applications', function () {
    $this->get(route('document-applications.index'))->assertRedirect(route('login'));
});

test('the index lists applications newest first with the applicant, document and officer', function () {
    $officer = User::factory()->create();
    $resident = Penduduk::factory()->create(['nama' => 'Siti Aminah', 'nik' => '3201010101010001']);
    $template = DocumentTemplate::factory()->create(['name' => 'Surat domisili']);

    DocumentApplication::factory()->create(['created_at' => now()->subDay()]);
    DocumentApplication::factory()->for($resident)->for($template)->for($officer)->create(['purpose' => 'Melamar pekerjaan']);

    $this->actingAs($officer)
        ->get(route('document-applications.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DocumentApplications/Index')
            ->has('applications.data', 2)
            ->where('applications.data.0.applicant_name', 'Siti Aminah')
            ->where('applications.data.0.applicant_nik', '3201010101010001')
            ->where('applications.data.0.document_template_id', $template->id)
            ->where('applications.data.0.template_name', 'Surat domisili')
            ->where('applications.data.0.purpose', 'Melamar pekerjaan')
            ->where('applications.data.0.recorded_by', $officer->name)
        );
});

test('the list searches applications by name, NIK, document and purpose', function (string $search) {
    $resident = Penduduk::factory()->create(['nama' => 'Siti Aminah', 'nik' => '3201010101010001']);
    $template = DocumentTemplate::factory()->create(['name' => 'Surat domisili']);
    DocumentApplication::factory()->for($resident)->for($template)->create(['purpose' => 'Melamar pekerjaan']);
    DocumentApplication::factory()->create(['purpose' => null]);

    $this->actingAs(User::factory()->create())
        ->get(route('document-applications.index', ['q' => $search]))
        ->assertInertia(fn (Assert $page) => $page
            ->has('applications.data', 1)
            ->where('applications.data.0.applicant_name', 'Siti Aminah')
            ->where('search', $search)
        );
})->with(['name' => 'aminah', 'nik' => '32010101', 'document' => 'domisili', 'purpose' => 'lamar']);

test('the create page offers every template, sorted by name', function () {
    DocumentTemplate::factory()->create(['name' => 'Surat usaha']);
    DocumentTemplate::factory()->create(['name' => 'Surat domisili']);

    $this->actingAs(User::factory()->create())
        ->get(route('document-applications.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DocumentApplications/Create')
            ->where('templates.0.label', 'Surat domisili')
            ->where('templates.1.label', 'Surat usaha')
            ->where('maxPurposeLength', 500)
        );
});

test('storing an application records the applicant, document and officer', function () {
    $officer = User::factory()->create();
    $resident = Penduduk::factory()->create(['nama' => 'Siti Aminah', 'nik' => '3201010101010001']);
    $template = DocumentTemplate::factory()->create(['name' => 'Surat domisili']);

    $this->actingAs($officer)
        ->post(route('document-applications.store'), [
            'penduduk_id' => $resident->id,
            'document_template_id' => $template->id,
            'purpose' => '  Melamar pekerjaan  ',
        ])
        ->assertRedirect(route('document-applications.index'));

    $application = DocumentApplication::sole();

    expect($application)
        ->penduduk_id->toBe($resident->id)
        ->applicant_nik->toBe('3201010101010001')
        ->applicant_name->toBe('Siti Aminah')
        ->document_template_id->toBe($template->id)
        ->template_name->toBe('Surat domisili')
        ->purpose->toBe('Melamar pekerjaan')
        ->user_id->toBe($officer->id);
});

test('the purpose is optional', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('document-applications.store'), [
            'penduduk_id' => Penduduk::factory()->create()->id,
            'document_template_id' => DocumentTemplate::factory()->create()->id,
            'purpose' => '',
        ])
        ->assertSessionHasNoErrors();

    expect(DocumentApplication::sole()->purpose)->toBeNull();
});

test('storing an application requires an existing resident and template', function (array $data, string $field) {
    $valid = [
        'penduduk_id' => Penduduk::factory()->create()->id,
        'document_template_id' => DocumentTemplate::factory()->create()->id,
    ];

    $this->actingAs(User::factory()->create())
        ->post(route('document-applications.store'), [...$valid, ...$data])
        ->assertSessionHasErrors($field);

    expect(DocumentApplication::count())->toBe(0);
})->with([
    'missing resident' => [['penduduk_id' => null], 'penduduk_id'],
    'unknown resident' => [['penduduk_id' => 999_999], 'penduduk_id'],
    'missing template' => [['document_template_id' => null], 'document_template_id'],
    'unknown template' => [['document_template_id' => 999_999], 'document_template_id'],
    'purpose too long' => [['purpose' => str_repeat('a', 501)], 'purpose'],
]);

test('the history keeps the applicant and document names after they are deleted', function () {
    $resident = Penduduk::factory()->create(['nama' => 'Siti Aminah']);
    $template = DocumentTemplate::factory()->create(['name' => 'Surat domisili']);
    $application = DocumentApplication::factory()->for($resident)->for($template)->create();

    $resident->delete();
    $template->delete();

    $this->actingAs(User::factory()->create())
        ->get(route('document-applications.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('applications.data.0.id', $application->id)
            ->where('applications.data.0.applicant_name', 'Siti Aminah')
            ->where('applications.data.0.template_name', 'Surat domisili')
            ->where('applications.data.0.document_template_id', null)
        );
});
