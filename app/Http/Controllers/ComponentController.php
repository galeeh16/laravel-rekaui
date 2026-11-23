<?php

namespace App\Http\Controllers;

use App\Enums\UiComponent;
use Inertia\Inertia;
use Inertia\Response;

class ComponentController extends Controller
{
    /**
     * Show the overview of every documented reka-ui component.
     */
    public function index(): Response
    {
        return Inertia::render('components/Index', [
            'components' => UiComponent::all(),
        ]);
    }

    /**
     * Show the documentation page of a single component.
     *
     * The component comes from the route's default parameter (see routes/web.php).
     */
    public function show(UiComponent $component): Response
    {
        return Inertia::render($component->page(), [
            'component' => $component->toArray(),
        ]);
    }
}
