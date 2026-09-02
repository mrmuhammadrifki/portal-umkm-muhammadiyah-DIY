<?php

namespace App\Http\Controllers;

use App\Models\UmkmProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UmkmProfileController extends Controller
{
    /**
     * Show the logged-in UMKM's own profile edit form.
     */
    public function edit(Request $request): View
    {
        $profile = $request->user()->umkmProfile;

        return view('umkm-profile.edit', ['profile' => $profile]);
    }

    /**
     * Update the logged-in UMKM's own profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $profile = $request->user()->umkmProfile;

        $this->authorize('update', $profile);

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'nib' => ['nullable', 'string', 'max:255'],
            'affiliation_status' => ['required', 'in:afiliasi,non_afiliasi'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('umkm-logos', 'public');
        }
        unset($validated['logo']);

        $profile->update($validated);

        return redirect()->route('umkm-profile.edit')->with('success', 'Profil usaha berhasil diperbarui.');
    }

    /**
     * F14: Admin listing + search semua UMKM untuk moderasi (semua status,
     * bukan cuma approved seperti katalog publik).
     */
    public function index(Request $request): View
    {
        $this->authorize('moderate', UmkmProfile::class);

        $query = UmkmProfile::query()->with('user');

        if ($request->filled('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $profiles = $query->latest()->paginate(15)->withQueryString();

        return view('admin.umkm-profiles.index', ['profiles' => $profiles]);
    }

    /**
     * Admin: list profiles waiting for approval.
     */
    public function pending(Request $request): View
    {
        $this->authorize('moderate', UmkmProfile::class);

        $profiles = UmkmProfile::where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('admin.umkm-profiles.pending', ['profiles' => $profiles]);
    }

    /**
     * Admin: approve a pending profile.
     */
    public function approve(UmkmProfile $umkmProfile): RedirectResponse
    {
        $this->authorize('moderate', UmkmProfile::class);

        $umkmProfile->update(['status' => 'approved']);

        return redirect()->back()->with('success', "Profil {$umkmProfile->business_name} disetujui.");
    }

    /**
     * Admin: reject a pending profile.
     */
    public function reject(UmkmProfile $umkmProfile): RedirectResponse
    {
        $this->authorize('moderate', UmkmProfile::class);

        $umkmProfile->update(['status' => 'rejected']);

        return redirect()->back()->with('success', "Profil {$umkmProfile->business_name} ditolak.");
    }

    /**
     * F15: Admin suspend UMKM bermasalah — memblokir login akun tsb dan
     * menyembunyikannya dari katalog publik (lihat UmkmController).
     */
    public function suspend(UmkmProfile $umkmProfile): RedirectResponse
    {
        $this->authorize('moderate', UmkmProfile::class);

        $umkmProfile->user->update(['is_active' => false]);

        return redirect()->back()->with('success', "Akun {$umkmProfile->business_name} disuspend.");
    }

    /**
     * F15: Admin mengaktifkan kembali UMKM yang sebelumnya disuspend.
     */
    public function reactivate(UmkmProfile $umkmProfile): RedirectResponse
    {
        $this->authorize('moderate', UmkmProfile::class);

        $umkmProfile->user->update(['is_active' => true]);

        return redirect()->back()->with('success', "Akun {$umkmProfile->business_name} diaktifkan kembali.");
    }
}
