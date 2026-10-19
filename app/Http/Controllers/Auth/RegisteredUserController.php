<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UmkmProfile;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View|RedirectResponse
    {
        return $this->redirectIfNotEligible() ?? view('auth.register');
    }

    /**
     * Admin / UMKM yang sudah login tidak perlu daftar lagi; pengunjung boleh
     * mendaftarkan usaha (akun UMKM baru menggantikan sesi pengunjung).
     */
    private function redirectIfNotEligible(): ?RedirectResponse
    {
        if (Auth::check() && Auth::user()->role !== 'pengunjung') {
            return redirect(RouteServiceProvider::HOME);
        }

        return null;
    }

    /**
     * Form pendaftaran pengunjung (pembeli) — tanpa profil usaha.
     */
    public function createPengunjung(): View
    {
        return view('auth.register-pengunjung');
    }

    /**
     * Daftarkan pengunjung. Setelah daftar, kembali ke halaman yang tadi
     * dituju (mis. tombol pesan WhatsApp), atau ke katalog.
     */
    public function storePengunjung(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'numeric', 'digits_between:9,15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.numeric' => 'Nomor WhatsApp hanya boleh diisi dengan angka.',
            'phone.digits_between' => 'Nomor WhatsApp harus 9–15 digit.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'pengunjung',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('katalog.publik'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->redirectIfNotEligible()) {
            return $redirect;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'business_name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20'],
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'umkm',
            ]);

            UmkmProfile::create([
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'owner_name' => $request->name,
                'whatsapp' => $request->whatsapp,
                'status' => 'pending',
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
