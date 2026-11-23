<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('login page can be rendered for guests', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('auth/Login'));
});

test('authenticated users are redirected away from the login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard', absolute: false));
});

test('users can authenticate with valid credentials', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('users are redirected to the intended page after login', function () {
    $user = User::factory()->create();

    $this->get(route('dashboard'))->assertRedirect(route('login'));

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));
});

test('users cannot authenticate with an invalid password', function () {
    $user = User::factory()->create();

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('email and password are required', function () {
    $this->post(route('login.store'), [])
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

test('login attempts are rate limited', function () {
    $this->freezeTime();

    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email' => __('auth.throttle', ['seconds' => 60, 'minutes' => 1])]);

    $this->assertGuest();
});

test('dashboard requires authentication', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can view the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('auth.user.name', $user->name)
            ->where('auth.user.email', $user->email)
        );
});

test('logout status message is shown on the login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('logout'));

    $this->get(route('login'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login')
            ->hasFlash('status', 'Anda telah berhasil logout.')
        );
});

test('users can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'))
        ->assertInertiaFlash('status', 'Anda telah berhasil logout.');

    $this->assertGuest();
});

test('guests cannot access the logout route', function () {
    $this->post(route('logout'))->assertRedirect(route('login'));
});
