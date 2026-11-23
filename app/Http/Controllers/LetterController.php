<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLetterRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class LetterController extends Controller
{
    /**
     * Validate a new letter submitted from the "Surat baru" modal.
     *
     * Letters are not persisted yet; this endpoint only validates the input
     * so the modal can show errors and confirm a successful submission.
     */
    public function store(StoreLetterRequest $request): RedirectResponse
    {
        $fullName = trim($request->string('first_name').' '.$request->string('last_name'));

        Inertia::flash('success', "Form surat baru untuk {$fullName} berhasil dikirim.");

        return back();
    }
}
