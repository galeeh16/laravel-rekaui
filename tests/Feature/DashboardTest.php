<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('dashboard shows the first page of letters by default', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('letters.data', 10)
            ->where('letters.total', 15)
            ->where('letters.from', 1)
            ->where('letters.to', 10)
            ->where('letters.last_page', 2)
            ->where('letters.data.0.number', '015/SK/XI/2026')
            ->where('letters.data.9.number', '006/SK/X/2026')
            ->where('totalLetters', 15)
            ->where('statusCounts', ['draft' => 3, 'awaiting' => 4, 'sent' => 5, 'overdue' => 3])
            ->where('activeStatus', null)
            ->where('perPage', 10)
        );
});

test('dashboard shows the next page with continued row numbers', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('letters.data', 5)
            ->where('letters.from', 11)
            ->where('letters.current_page', 2)
            ->where('letters.data.0.number', '005/SK/X/2026')
            ->where('letters.data.4.number', '001/SK/IX/2026')
        );
});

test('dashboard respects the selected rows per page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['per_page' => 5]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('letters.data', 5)
            ->where('letters.per_page', 5)
            ->where('letters.last_page', 3)
            ->where('perPage', 5)
            ->where('queryWithoutStatus', ['per_page' => 5])
            ->where('letters.next_page_url', fn (string $url) => str_contains($url, 'per_page=5&page=2'))
        );
});

test('dashboard falls back to the default rows per page for unsupported values', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['per_page' => 7]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('perPage', 10)
            ->where('letters.per_page', 10)
        );
});

test('dashboard filters letters by status', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['status' => 'overdue']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('activeStatus', 'overdue')
            ->has('letters.data', 3)
            ->where('letters.data.0.number', '006/SK/X/2026')
            ->where('letters.data.1.number', '004/SK/X/2026')
            ->where('letters.data.2.number', '002/SK/X/2026')
        );
});

test('dashboard ignores unknown status filters', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['status' => 'unknown']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('activeStatus', null)
            ->where('letters.total', 15)
        );
});

test('dashboard searches letters case-insensitively', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['q' => 'arunika']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('search', 'arunika')
            ->has('letters.data', 2)
            ->where('letters.data.0.number', '013/SK/XI/2026')
            ->where('letters.data.1.number', '009/SK/X/2026')
        );
});

test('dashboard combines status filter and search', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['status' => 'sent', 'q' => 'arunika']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('letters.data', 1)
            ->where('letters.data.0.number', '009/SK/X/2026')
        );
});

test('status filters keep the search term and rows per page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['q' => 'surat', 'per_page' => 25]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('queryWithoutStatus', ['q' => 'surat', 'per_page' => 25])
        );
});

test('dashboard returns an empty page when nothing matches', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard', ['q' => 'tidak-ada-surat-ini']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('letters.data', 0)
            ->where('letters.total', 0)
            ->where('letters.last_page', 1)
        );
});

test('dashboard shares the signed in user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.user.email', $user->email)
            ->missing('auth.user.password')
        );
});
