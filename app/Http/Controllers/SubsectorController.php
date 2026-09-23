<?php

namespace App\Http\Controllers;

use App\Models\Subsector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubsectorController extends Controller
{
    public function create(): View
    {
        return view('admin.subsektor.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:subsectors,name'],
            'icon'        => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama subsektor wajib diisi.',
            'name.unique'   => 'Nama subsektor sudah ada di database.',
        ]);

        Subsector::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'icon'        => $validated['icon'] ?? '🏷️',
            'description' => $validated['description'],
        ]);

        return redirect()->route('admin.kategori.index')->with('success', "Subsektor '{$validated['name']}' berhasil ditambahkan.");
    }

    public function edit(Subsector $subsektor): View
    {
        return view('admin.subsektor.edit', ['subsector' => $subsektor]);
    }

    public function update(Request $request, Subsector $subsektor): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:subsectors,name,' . $subsektor->id],
            'icon'        => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama subsektor wajib diisi.',
            'name.unique'   => 'Nama subsektor sudah ada di database.',
        ]);

        $subsektor->update([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'icon'        => $validated['icon'] ?? $subsektor->icon,
            'description' => $validated['description'],
        ]);

        return redirect()->route('admin.kategori.index')->with('success', "Subsektor '{$subsektor->name}' berhasil diperbarui.");
    }

    public function destroy(Subsector $subsektor): RedirectResponse
    {
        if ($subsektor->products()->exists() || $subsektor->umkmProfiles()->exists()) {
            return redirect()->route('admin.kategori.index')->with('error', "Subsektor '{$subsektor->name}' tidak dapat dihapus karena masih terhubung dengan produk atau profil UMKM.");
        }

        $name = $subsektor->name;
        $subsektor->delete();

        return redirect()->route('admin.kategori.index')->with('success', "Subsektor '{$name}' berhasil dihapus.");
    }
}
