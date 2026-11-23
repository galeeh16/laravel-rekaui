<?php

use App\Enums\UiComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('guests cannot see the component documentation', function () {
    $this->get(route('components.index'))->assertRedirect(route('login'));
    $this->get(route('components.accordion'))->assertRedirect(route('login'));
});

test('the overview lists every component', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('components.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('components/Index')
            ->has('components', count(UiComponent::cases()))
            ->where('components.0', UiComponent::Accordion->toArray())
        );
});

test('every component has a documentation page', function (UiComponent $component) {
    $this->actingAs(User::factory()->create())
        ->get(route($component->routeName()))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component($component->page())
            ->where('component.slug', $component->value)
            ->where('component.label', $component->label())
            ->where('component.docsUrl', "https://reka-ui.com/docs/components/{$component->value}")
        );
})->with(UiComponent::cases());

test('the sidebar receives a link for every component', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('componentNavigation', count(UiComponent::cases()))
            ->where('componentNavigation.1', ['label' => 'Alert Dialog', 'route' => 'components.alert-dialog'])
        );
});

test('guests do not receive the component navigation', function () {
    $this->get(route('login'))
        ->assertInertia(fn (Assert $page) => $page->where('componentNavigation', null));
});
