<?php

use App\Enums\Citizenship;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Religion;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

/**
 * @return array<string, string|null>
 */
function validPendudukInput(array $overrides = []): array
{
    return [
        'nik' => '3201234567890001',
        'nama' => 'Budi Santoso',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-05-01',
        'jenis_kelamin' => 'male',
        'alamat_lengkap' => 'Jl. Merdeka No. 1, Bandung',
        'agama' => 'islam',
        'status_perkawinan' => 'kawin',
        'pekerjaan' => 'Wiraswasta',
        'kewarganegaraan' => 'WNI',
        ...$overrides,
    ];
}

test('guests are redirected to login from penduduk pages', function () {
    $this->get(route('penduduk.index'))->assertRedirect(route('login'));
    $this->post(route('penduduk.store'), validPendudukInput())->assertRedirect(route('login'));
});

test('the list shows every field of the residents, sorted by name', function () {
    Penduduk::factory()->create([
        'nama' => 'Siti Aminah',
        'tempat_lahir' => 'Bogor',
        'tanggal_lahir' => '1985-02-03',
        'jenis_kelamin' => Gender::Female,
        'agama' => Religion::Katolik,
        'status_perkawinan' => MaritalStatus::CeraiMati,
        'pekerjaan' => 'Guru',
        'kewarganegaraan' => Citizenship::Wna,
        'created_at' => '2026-11-20 08:30:00',
        'updated_at' => '2026-11-24 10:15:00',
    ]);
    Penduduk::factory()->create(['nama' => 'Agus Salim']);

    $this->actingAs(User::factory()->create())
        ->get(route('penduduk.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Penduduk/Index')
            ->has('residents.data', 2)
            ->where('residents.data.0.nama', 'Agus Salim')
            ->where('residents.data.1.nama', 'Siti Aminah')
            ->where('residents.data.1.tanggal_lahir', '1985-02-03')
            ->where('residents.data.1.jenis_kelamin', 'Perempuan')
            ->where('residents.data.1.tempat_lahir', 'Bogor')
            ->where('residents.data.1.agama', 'Katolik')
            ->where('residents.data.1.status_perkawinan', 'Cerai mati')
            ->where('residents.data.1.pekerjaan', 'Guru')
            ->where('residents.data.1.kewarganegaraan', 'WNA')
            ->where('residents.data.1.created_at', '2026-11-20T08:30:00+00:00')
            ->where('residents.data.1.updated_at', '2026-11-24T10:15:00+00:00')
        );
});

test('the list searches by name or NIK', function (string $search, array $expectedNames) {
    Penduduk::factory()->create(['nama' => 'Budi Santoso', 'nik' => '3201000000000001']);
    Penduduk::factory()->create(['nama' => 'Siti Aminah', 'nik' => '3273000000000002']);

    $this->actingAs(User::factory()->create())
        ->get(route('penduduk.index', ['q' => $search]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('search', $search)
            ->where('residents.data', fn ($residents) => collect($residents)->pluck('nama')->all() === $expectedNames)
        );
})->with([
    'name, case-insensitive' => ['SITI', ['Siti Aminah']],
    'part of a NIK' => ['3201', ['Budi Santoso']],
    'no match' => ['Joko', []],
]);

test('the list filters by gender', function (array $query, ?string $expectedGender, array $expectedNames) {
    Penduduk::factory()->create(['nama' => 'Budi Santoso', 'jenis_kelamin' => Gender::Male]);
    Penduduk::factory()->create(['nama' => 'Siti Aminah', 'jenis_kelamin' => Gender::Female]);

    $this->actingAs(User::factory()->create())
        ->get(route('penduduk.index', $query))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('gender', $expectedGender)
            ->where('genderOptions', [
                ['value' => 'male', 'label' => 'Laki-laki'],
                ['value' => 'female', 'label' => 'Perempuan'],
            ])
            ->where('residents.data', fn ($residents) => collect($residents)->pluck('nama')->all() === $expectedNames)
        );
})->with([
    'male' => [['jenis_kelamin' => 'male'], 'male', ['Budi Santoso']],
    'female' => [['jenis_kelamin' => 'female'], 'female', ['Siti Aminah']],
    'together with a search' => [['jenis_kelamin' => 'female', 'q' => 'a'], 'female', ['Siti Aminah']],
    'unknown gender shows everyone' => [['jenis_kelamin' => 'unknown'], null, ['Budi Santoso', 'Siti Aminah']],
]);

test('the list respects the selected rows per page and falls back to the default', function () {
    Penduduk::factory()->count(7)->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('penduduk.index', ['per_page' => 5]))
        ->assertInertia(fn (Assert $page) => $page
            ->has('residents.data', 5)
            ->where('residents.last_page', 2)
            ->where('perPage', 5)
        );

    $this->actingAs($user)
        ->get(route('penduduk.index', ['per_page' => 7]))
        ->assertInertia(fn (Assert $page) => $page->where('perPage', 10));
});

test('the create page offers the form choices', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('penduduk.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Penduduk/Create')
            ->where('options.genders', Gender::options())
            ->where('options.religions', Religion::options())
            ->where('options.maritalStatuses', MaritalStatus::options())
            ->where('options.citizenships', Citizenship::options())
        );
});

test('a resident can be created', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.store'), validPendudukInput())
        ->assertRedirect(route('penduduk.index'));

    $resident = Penduduk::sole();

    expect($resident->nik)->toBe('3201234567890001')
        ->and($resident->nama)->toBe('Budi Santoso')
        ->and($resident->tanggal_lahir->toDateString())->toBe('1990-05-01')
        ->and($resident->jenis_kelamin)->toBe(Gender::Male)
        ->and($resident->agama)->toBe(Religion::Islam)
        ->and($resident->status_perkawinan)->toBe(MaritalStatus::Kawin)
        ->and($resident->kewarganegaraan)->toBe(Citizenship::Wni);
});

test('optional fields can be left empty', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.store'), validPendudukInput([
            'alamat_lengkap' => '',
            'agama' => null,
            'status_perkawinan' => null,
            'pekerjaan' => '',
        ]))
        ->assertSessionHasNoErrors();

    $resident = Penduduk::sole();

    expect($resident->alamat_lengkap)->toBeNull()
        ->and($resident->agama)->toBeNull()
        ->and($resident->status_perkawinan)->toBeNull()
        ->and($resident->pekerjaan)->toBeNull();
});

test('creating requires the identity fields', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.store'), [])
        ->assertSessionHasErrors([
            'nik' => 'NIK wajib diisi.',
            'nama' => 'Nama wajib diisi.',
            'tempat_lahir' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin' => 'Jenis kelamin wajib diisi.',
            'kewarganegaraan' => 'Kewarganegaraan wajib diisi.',
        ]);

    expect(Penduduk::count())->toBe(0);
});

test('creating rejects invalid values', function (array $input, string $field, string $message) {
    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.store'), validPendudukInput($input))
        ->assertSessionHasErrors([$field => $message]);
})->with([
    'NIK too short' => [['nik' => '320123'], 'nik', 'NIK harus terdiri dari 16 angka.'],
    'NIK with letters' => [['nik' => '320123456789000A'], 'nik', 'NIK harus terdiri dari 16 angka.'],
    'birth date in the future' => [['tanggal_lahir' => now()->addDay()->toDateString()], 'tanggal_lahir', 'Tanggal lahir tidak boleh melewati hari ini.'],
    'unknown gender' => [['jenis_kelamin' => 'other'], 'jenis_kelamin', 'Jenis kelamin yang dipilih tidak valid.'],
    'unknown religion' => [['agama' => 'lainnya'], 'agama', 'Agama yang dipilih tidak valid.'],
    'unknown citizenship' => [['kewarganegaraan' => 'XYZ'], 'kewarganegaraan', 'Kewarganegaraan yang dipilih tidak valid.'],
]);

test('creating rejects a NIK that is already registered', function () {
    Penduduk::factory()->create(['nik' => '3201234567890001']);

    $this->actingAs(User::factory()->create())
        ->post(route('penduduk.store'), validPendudukInput(['nik' => '3201234567890001']))
        ->assertSessionHasErrors(['nik' => 'NIK ini sudah terdaftar.']);

    expect(Penduduk::count())->toBe(1);
});

test('the edit page is filled with the resident data', function () {
    $resident = Penduduk::factory()->create([
        'tanggal_lahir' => '1990-05-01',
        'jenis_kelamin' => Gender::Female,
        'agama' => null,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('penduduk.edit', $resident))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Penduduk/Edit')
            ->where('resident.id', $resident->id)
            ->where('resident.nik', $resident->nik)
            ->where('resident.tanggal_lahir', '1990-05-01')
            ->where('resident.jenis_kelamin', 'female')
            ->where('resident.agama', null)
            ->has('options.genders')
        );
});

test('a resident can be updated while keeping the same NIK', function () {
    $resident = Penduduk::factory()->create(['nik' => '3201234567890001', 'nama' => 'Budi']);

    $this->actingAs(User::factory()->create())
        ->put(route('penduduk.update', $resident), validPendudukInput(['nama' => 'Budi Santoso', 'pekerjaan' => 'Guru']))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('penduduk.index'));

    expect($resident->fresh())
        ->nama->toBe('Budi Santoso')
        ->pekerjaan->toBe('Guru');
});

test('a resident cannot take the NIK of another resident', function () {
    Penduduk::factory()->create(['nik' => '3201234567890001']);
    $resident = Penduduk::factory()->create(['nik' => '3201234567890002']);

    $this->actingAs(User::factory()->create())
        ->put(route('penduduk.update', $resident), validPendudukInput(['nik' => '3201234567890001']))
        ->assertSessionHasErrors(['nik' => 'NIK ini sudah terdaftar.']);

    expect($resident->fresh()->nik)->toBe('3201234567890002');
});

test('a resident can be deleted', function () {
    $resident = Penduduk::factory()->create();

    $this->actingAs(User::factory()->create())
        ->from(route('penduduk.index', ['page' => 2]))
        ->delete(route('penduduk.destroy', $resident))
        ->assertRedirect(route('penduduk.index', ['page' => 2]));

    $this->assertModelMissing($resident);
});

test('guests cannot search residents', function () {
    $this->getJson(route('penduduk.search', ['nik' => '3201']))->assertUnauthorized();
});

test('the search finds residents by the start of their NIK with document-ready values', function () {
    Penduduk::factory()->create([
        'nik' => '3201234567890001',
        'nama' => 'Budi Santoso',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-05-01',
        'jenis_kelamin' => Gender::Male,
        'alamat_lengkap' => 'Jl. Merdeka No. 1, Bandung',
        'agama' => Religion::Islam,
        'status_perkawinan' => MaritalStatus::BelumKawin,
        'pekerjaan' => null,
        'kewarganegaraan' => Citizenship::Wni,
    ]);
    Penduduk::factory()->create(['nik' => '3273000000000002']);

    $this->actingAs(User::factory()->create())
        ->getJson(route('penduduk.search', ['nik' => '3201 2345']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.nik', '3201234567890001')
        ->assertJsonPath('data.0.nama', 'Budi Santoso')
        ->assertExactJson(['data' => [[
            'id' => Penduduk::query()->where('nik', '3201234567890001')->value('id'),
            'nik' => '3201234567890001',
            'nama' => 'Budi Santoso',
            'fields' => [
                'nik' => '3201234567890001',
                'nama' => 'Budi Santoso',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1 Mei 1990',
                'jenis_kelamin' => 'Laki-laki',
                'alamat_lengkap' => 'Jl. Merdeka No. 1, Bandung',
                'agama' => 'Islam',
                'status_perkawinan' => 'Belum kawin',
                'kewarganegaraan' => 'WNI',
            ],
        ]]]);
});

test('the search needs at least three digits and returns at most eight residents', function () {
    Penduduk::factory()->count(10)->sequence(fn ($sequence) => ['nik' => sprintf('32010000000000%02d', $sequence->index)])->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson(route('penduduk.search', ['nik' => '32']))
        ->assertOk()
        ->assertJsonCount(0, 'data');

    $this->actingAs($user)
        ->getJson(route('penduduk.search', ['nik' => '3201']))
        ->assertOk()
        ->assertJsonCount(8, 'data')
        ->assertJsonPath('data.0.nik', '3201000000000000');
});
