<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SuiteController extends Controller
{
    public function index(): Response
    {
        $suites = Suite::orderBy('tier')->orderBy('name')->get();

        return Inertia::render('admin/suites/index', [
            'suites' => $suites->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'tier' => $s->tier->value,
                'price' => $s->base_price,
                'is_active' => $s->is_active,
                'image_count' => count($s->images ?? []),
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'theme_slug' => 'required|string|max:255|unique:suites,theme_slug',
            'suite_slug' => 'required|string|max:255|unique:suites,suite_slug',
            'tier' => 'required|in:ruby,emerald,diamond',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'is_active' => 'boolean',
            // Handle image uploads separately in a real app
        ]);

        Suite::create($validated);

        return redirect()->route('admin.suites.index')->with('success', 'Suite created.');
    }

    public function update(Request $request, Suite $suite): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'theme_slug' => 'required|string|max:255',
            'tier' => 'required|in:ruby,emerald,diamond',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $suite->update($validated);

        return redirect()->route('admin.suites.index')->with('success', 'Suite updated.');
    }

    public function destroy(Suite $suite): RedirectResponse
    {
        // Prevent deletion if has bookings
        if ($suite->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete suite with existing bookings.');
        }
        $suite->delete();

        return back()->with('success', 'Suite deleted.');
    }
}
