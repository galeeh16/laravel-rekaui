<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('signed in users can view the blank page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('blank'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Blank'));
});

test('guests are redirected to login from the blank page', function () {
    $this->get(route('blank'))->assertRedirect(route('login'));
});
