<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Halaman Publik: Daftar Agenda, Pelatihan, & Workshop LP UMKM PWM DIY
     */
    public function index(Request $request): View
    {
        $query = Event::published();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('summary', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('speaker', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Urutkan berdasarkan tanggal kegiatan terbaru
        $events = $query->orderBy('date_start', 'desc')
            ->paginate(9)
            ->withQueryString();

        $stats = [
            'total'        => Event::published()->count(),
            'pelatihan'    => Event::published()->where('type', 'pelatihan')->count(),
            'pendampingan' => Event::published()->where('type', 'pendampingan')->count(),
            'workshop'     => Event::published()->where('type', 'workshop')->count(),
        ];

        return view('event.index', [
            'events' => $events,
            'stats'  => $stats,
        ]);
    }

    /**
     * Halaman Publik: Detail Agenda / Event
     */
    public function show(Event $event): View
    {
        abort_unless(
            $event->is_published || (auth()->check() && auth()->user()->role === 'admin'),
            404
        );

        $relatedEvents = Event::published()
            ->where('id', '!=', $event->id)
            ->where('type', $event->type)
            ->orderBy('date_start', 'desc')
            ->take(3)
            ->get();

        if ($relatedEvents->isEmpty()) {
            $relatedEvents = Event::published()
                ->where('id', '!=', $event->id)
                ->orderBy('date_start', 'desc')
                ->take(3)
                ->get();
        }

        return view('event.show', [
            'event'         => $event,
            'relatedEvents' => $relatedEvents,
        ]);
    }

    /**
     * Admin: Listing semua event untuk moderasi
     */
    public function adminIndex(Request $request): View
    {
        $query = Event::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $events = $query->latest('date_start')->paginate(15)->withQueryString();

        return view('admin.event.index', ['events' => $events]);
    }

    /**
     * Admin: Form tambah event baru
     */
    public function create(): View
    {
        return view('admin.event.create');
    }

    /**
     * Admin: Simpan event baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'type'             => ['required', 'in:pelatihan,pendampingan,workshop'],
            'date_start'       => ['required', 'date'],
            'date_end'         => ['nullable', 'date', 'after_or_equal:date_start'],
            'location'         => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string'],
            'contact_person'   => ['nullable', 'string', 'max:255'],
            'summary'          => ['nullable', 'string', 'max:300'],
            'organizer'        => ['nullable', 'string', 'max:255'],
            'speaker'          => ['nullable', 'string', 'max:255'],
            'quota'            => ['nullable', 'integer', 'min:1'],
            'cost'             => ['nullable', 'string', 'max:100'],
            'registration_url' => ['nullable', 'url', 'max:255'],
            'image'            => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_published'     => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published', true);

        if (empty($validated['summary'])) {
            $validated['summary'] = Str::limit(strip_tags($validated['description']), 200);
        }
        if (empty($validated['organizer'])) {
            $validated['organizer'] = 'LP UMKM PWM DIY';
        }
        if (empty($validated['cost'])) {
            $validated['cost'] = 'Gratis';
        }

        // Buat slug unik
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $validated['slug'] = $slug;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('event-banners', 'public');
        }
        unset($validated['image']);

        Event::create($validated);

        return redirect()->route('admin.event.index')->with('success', 'Agenda berhasil ditambahkan.');
    }

    /**
     * Admin: Form edit event
     */
    public function edit(Event $event): View
    {
        return view('admin.event.edit', ['event' => $event]);
    }

    /**
     * Admin: Update event
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'type'             => ['required', 'in:pelatihan,pendampingan,workshop'],
            'date_start'       => ['required', 'date'],
            'date_end'         => ['nullable', 'date', 'after_or_equal:date_start'],
            'location'         => ['required', 'string', 'max:255'],
            'description'      => ['required', 'string'],
            'contact_person'   => ['nullable', 'string', 'max:255'],
            'summary'          => ['nullable', 'string', 'max:300'],
            'organizer'        => ['nullable', 'string', 'max:255'],
            'speaker'          => ['nullable', 'string', 'max:255'],
            'quota'            => ['nullable', 'integer', 'min:1'],
            'cost'             => ['nullable', 'string', 'max:100'],
            'registration_url' => ['nullable', 'url', 'max:255'],
            'image'            => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_published'     => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if (empty($validated['summary'])) {
            $validated['summary'] = Str::limit(strip_tags($validated['description']), 200);
        }
        if (empty($validated['organizer'])) {
            $validated['organizer'] = 'LP UMKM PWM DIY';
        }
        if (empty($validated['cost'])) {
            $validated['cost'] = 'Gratis';
        }

        if ($validated['title'] !== $event->title) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('event-banners', 'public');
        }
        unset($validated['image']);

        $event->update($validated);

        return redirect()->route('admin.event.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    /**
     * Admin: Hapus event
     */
    public function destroy(Event $event): RedirectResponse
    {
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return redirect()->route('admin.event.index')->with('success', 'Agenda berhasil dihapus.');
    }
}
