<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

/**
 * @return array<string, string>
 */
function validLetterInput(array $overrides = []): array
{
    return [
        'first_name' => 'Budi',
        'last_name' => 'Santoso',
        'birth_date' => '1995-08-17',
        'gender' => 'male',
        'hobby' => 'reading',
        ...$overrides,
    ];
}

test('guests cannot submit a new letter', function () {
    $this->post(route('letters.store'), validLetterInput())
        ->assertRedirect(route('login'));
});

test('a valid new letter redirects back with a success message', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('dashboard'))
        ->post(route('letters.store'), validLetterInput())
        ->assertRedirect(route('dashboard'))
        ->assertSessionHasNoErrors()
        ->assertInertiaFlash('success', 'Form surat baru untuk Budi Santoso berhasil dikirim.');
});

test('the last name is optional', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('dashboard'))
        ->post(route('letters.store'), validLetterInput(['last_name' => '']))
        ->assertSessionHasNoErrors()
        ->assertInertiaFlash('success', 'Form surat baru untuk Budi berhasil dikirim.');
});

test('required fields are validated with Indonesian messages', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('dashboard'))
        ->post(route('letters.store'), [])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHasErrors([
            'first_name' => 'Nama depan wajib diisi.',
            'birth_date' => 'Tanggal lahir wajib diisi.',
            'gender' => 'Jenis kelamin wajib diisi.',
            'hobby' => 'Hobi wajib diisi.',
        ])
        ->assertSessionDoesntHaveErrors('last_name');
});

test('invalid values are rejected', function (string $field, mixed $value, string $message) {
    $this->actingAs(User::factory()->create())
        ->post(route('letters.store'), validLetterInput([$field => $value]))
        ->assertSessionHasErrors([$field => $message]);
})->with([
    'unknown gender' => ['gender', 'other', 'Jenis kelamin yang dipilih tidak valid.'],
    'unknown hobby' => ['hobby', 'skydiving', 'Hobi yang dipilih tidak valid.'],
    'birth date today' => ['birth_date', now()->toDateString(), 'Tanggal lahir harus sebelum hari ini.'],
    'birth date before 1900' => ['birth_date', '1899-12-31', 'Tanggal lahir tidak boleh sebelum tahun 1900.'],
    'birth date wrong format' => ['birth_date', '17-08-1995', 'Tanggal lahir harus berupa tanggal yang valid.'],
    'first name too long' => ['first_name', str_repeat('a', 101), 'Nama depan maksimal 100 karakter.'],
]);

test('signed in users receive the form options', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('letterFormOptions.genders', [
                ['value' => 'male', 'label' => 'Laki-laki'],
                ['value' => 'female', 'label' => 'Perempuan'],
            ])
            ->has('letterFormOptions.hobbies', 7)
            ->where('letterFormOptions.hobbies.0', ['value' => 'reading', 'label' => 'Membaca'])
        );
});

test('guests do not receive the form options', function () {
    $this->get(route('login'))
        ->assertInertia(fn (Assert $page) => $page->where('letterFormOptions', null));
});
