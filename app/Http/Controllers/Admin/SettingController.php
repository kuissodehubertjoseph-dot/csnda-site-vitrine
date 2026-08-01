<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private const KEYS = [
        'contact_telephone',
        'contact_email',
        'contact_adresse',
        'contact_map_embed',
        'accueil_accroche',
    ];

    public function edit()
    {
        $settings = collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => Setting::get($key)]);

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'contact_telephone' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_adresse' => ['nullable', 'string', 'max:500'],
            'contact_map_embed' => ['nullable', 'string'],
            'accueil_accroche' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Les informations de contact ont été mises à jour.');
    }
}
