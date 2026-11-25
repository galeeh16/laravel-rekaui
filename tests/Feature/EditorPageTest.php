<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('signed in users can open the document editor', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('editor'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Editor'));
});

test('guests are redirected to login from the document editor', function () {
    $this->get(route('editor'))->assertRedirect(route('login'));
});
