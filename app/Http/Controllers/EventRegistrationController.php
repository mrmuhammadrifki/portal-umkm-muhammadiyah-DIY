<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventRegistrationController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran umum untuk suatu event
     */
    public function create(Event $event): View
    {
        abort_unless(
            $event->is_published || (auth()->check() && auth()->user()->role === 'admin'),
            404
        );

        $user = auth()->user();
        $umkm = $user?->umkmProfile;

        // Nilai awal / auto-fill jika pengguna sudah login
        $defaults = [
            'name'                    => old('name', $user?->name ?? ''),
            'email'                   => old('email', $user?->email ?? ''),
            'phone'                   => old('phone', $umkm?->whatsapp ?? ($user?->phone ?? '')),
            'institution_or_business' => old('institution_or_business', $umkm?->business_name ?? ''),
            'subsector_or_category'   => old('subsector_or_category', $umkm?->subsector?->name ?? ''),
            'city'                    => old('city', $umkm?->kabupaten_kota ?? ''),
        ];

        return view('event.register', [
            'event'    => $event,
            'defaults' => $defaults,
        ]);
    }

    /**
     * Proses penyimpanan pendaftaran peserta event
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        abort_unless(
            $event->is_published || (auth()->check() && auth()->user()->role === 'admin'),
            404
        );

        // Cek kuota
        if ($event->is_full) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Mohon maaf, kuota pendaftaran untuk kegiatan ini sudah penuh.');
        }

        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'email'                   => ['required', 'string', 'email', 'max:255'],
            'phone'                   => ['required', 'string', 'min:9', 'max:20'],
            'institution_or_business' => ['nullable', 'string', 'max:255'],
            'subsector_or_category'   => ['nullable', 'string', 'max:100'],
            'city'                    => ['required', 'string', 'max:100'],
            'notes'                   => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'phone.min'      => 'Nomor telepon minimal 9 digit.',
            'city.required'  => 'Kabupaten / Kota domisili wajib dipilih.',
        ]);

        // Sanitasi nomor telepon untuk perbandingan duplikat
        $cleanInputPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanInputPhone, '0')) {
            $cleanInputPhone = '62' . substr($cleanInputPhone, 1);
        }

        // Cek duplikasi pendaftaran di event yang sama
        $alreadyRegistered = $event->registrations()
            ->where(function ($query) use ($validated, $cleanInputPhone) {
                $query->where('email', $validated['email'])
                    ->orWhere('phone', $validated['phone'])
                    ->orWhere('phone', 'like', '%' . substr($cleanInputPhone, -8));
            })
            ->exists();

        if ($alreadyRegistered) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau nomor WhatsApp ini sudah terdaftar pada kegiatan ini.');
        }

        $registration = $event->registrations()->create([
            'user_id'                 => auth()->id(),
            'name'                    => $validated['name'],
            'email'                   => $validated['email'],
            'phone'                   => $validated['phone'],
            'institution_or_business' => $validated['institution_or_business'] ?? null,
            'subsector_or_category'   => $validated['subsector_or_category'] ?? null,
            'city'                    => $validated['city'],
            'notes'                   => $validated['notes'] ?? null,
            'status'                  => 'registered',
        ]);

        return redirect()->route('event.register.success', [
            'event'        => $event->slug,
            'registration' => $registration->id,
        ])->with('success', 'Pendaftaran Anda berhasil dikirim!');
    }

    /**
     * Halaman konfirmasi sukses pendaftaran
     */
    public function success(Event $event, Request $request): View
    {
        $registrationId = $request->query('registration');
        $registration = null;

        if ($registrationId) {
            $registration = $event->registrations()->find($registrationId);
        }

        return view('event.register-success', [
            'event'        => $event,
            'registration' => $registration,
        ]);
    }

    /**
     * Admin: Daftar peserta terdaftar pada suatu event
     */
    public function adminIndex(Event $event, Request $request): View
    {
        $query = $event->registrations()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('institution_or_business', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => $event->registrations()->count(),
            'confirmed' => $event->registrations()->where('status', 'confirmed')->count(),
            'attended'  => $event->registrations()->where('status', 'attended')->count(),
            'cancelled' => $event->registrations()->where('status', 'cancelled')->count(),
        ];

        return view('admin.event.registrations', [
            'event'         => $event,
            'registrations' => $registrations,
            'stats'         => $stats,
        ]);
    }

    /**
     * Admin: Perbarui status kehadiran/pendaftaran peserta
     */
    public function updateStatus(Request $request, Event $event, EventRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:registered,confirmed,attended,cancelled'],
        ]);

        $registration->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', "Status peserta {$registration->name} berhasil diperbarui menjadi {$registration->status_label}.");
    }

    /**
     * Admin: Hapus data peserta
     */
    public function destroy(Event $event, EventRegistration $registration): RedirectResponse
    {
        $name = $registration->name;
        $registration->delete();

        return redirect()->back()->with('success', "Data pendaftaran {$name} berhasil dihapus.");
    }

    /**
     * Admin: Export daftar peserta ke format CSV
     */
    public function exportCsv(Event $event): StreamedResponse
    {
        $filename = 'peserta-' . $event->slug . '-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($event) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM untuk Microsoft Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom
            fputcsv($file, [
                'No',
                'Nama Lengkap',
                'Email',
                'No WhatsApp / HP',
                'Usaha / Instansi',
                'Bidang Usaha / Minat',
                'Domisili',
                'Catatan / Motivasi',
                'Status',
                'Waktu Pendaftaran',
            ]);

            $no = 1;
            $event->registrations()->oldest()->chunk(100, function ($registrations) use ($file, &$no) {
                foreach ($registrations as $reg) {
                    fputcsv($file, [
                        $no++,
                        $reg->name,
                        $reg->email,
                        "'" . $reg->phone,
                        $reg->institution_or_business ?: '-',
                        $reg->subsector_or_category ?: '-',
                        $reg->city,
                        $reg->notes ?: '-',
                        $reg->status_label,
                        $reg->created_at->translatedFormat('d M Y H:i:s'),
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
