<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferencesController extends Controller
{
    /**
     * Display the user's preferences form.
     */
    public function edit(Request $request): View
    {
        return view('preferences.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's preferences.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'currency' => ['required', 'string', 'max:10'],
            'buget_goal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $request->user()->fill($validated);
        $request->user()->save();

        return redirect()->route('preferences.edit')->with('status', 'preferences-updated');
    }
}
