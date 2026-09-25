<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function createDummyEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title'          => 'Pelatihan Packaging & Branding UMKM',
            'slug'           => 'pelatihan-packaging-branding-umkm',
            'type'           => 'pelatihan',
            'summary'        => 'Tingkatkan nilai jual produk dengan kemasan premium.',
            'description'    => 'Deskripsi detail pelatihan packaging dan branding.',
            'date_start'     => Carbon::now()->addDays(7),
            'location'       => 'Aula PWM DIY',
            'organizer'      => 'LP UMKM PWM DIY',
            'contact_person' => '081234567890',
            'quota'          => 30,
            'cost'           => 'Gratis',
            'is_published'   => true,
        ], $overrides));
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $event = $this->createDummyEvent();

        $response = $this->get(route('event.register', $event->slug));

        $response->assertStatus(200);
        $response->assertSee('Formulir Pendaftaran');
        $response->assertSee($event->title);
    }

    public function test_guest_can_register_for_event_with_general_fields(): void
    {
        $event = $this->createDummyEvent();

        $payload = [
            'name'                    => 'Budi Santoso',
            'email'                   => 'budi@example.com',
            'phone'                   => '081234567891',
            'institution_or_business' => 'Kripik Tempe Berkah',
            'subsector_or_category'   => 'Kuliner (Makanan & Minuman)',
            'city'                    => 'Kabupaten Bantul',
            'notes'                   => 'Ingin belajar teknik desain kemasan kedap udara.',
            'agree'                   => '1',
        ];

        $response = $this->post(route('event.register.store', $event->slug), $payload);

        $this->assertDatabaseHas('event_registrations', [
            'event_id'                => $event->id,
            'name'                    => 'Budi Santoso',
            'email'                   => 'budi@example.com',
            'phone'                   => '081234567891',
            'institution_or_business' => 'Kripik Tempe Berkah',
            'city'                    => 'Kabupaten Bantul',
            'status'                  => 'registered',
        ]);

        $registration = EventRegistration::first();
        $response->assertRedirect(route('event.register.success', [
            'event'        => $event->slug,
            'registration' => $registration->id,
        ]));
    }

    public function test_authenticated_user_associates_user_id_on_registration(): void
    {
        $user = User::factory()->create(['role' => 'umkm']);
        $event = $this->createDummyEvent();

        $payload = [
            'name'                    => $user->name,
            'email'                   => $user->email,
            'phone'                   => '081987654321',
            'institution_or_business' => 'Batik Sekar Wangi',
            'subsector_or_category'   => 'Fashion & Pakaian',
            'city'                    => 'Kota Yogyakarta',
            'agree'                   => '1',
        ];

        $response = $this->actingAs($user)->post(route('event.register.store', $event->slug), $payload);

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id'  => $user->id,
            'email'    => $user->email,
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_prevents_duplicate_registration_for_same_event(): void
    {
        $event = $this->createDummyEvent();

        EventRegistration::create([
            'event_id' => $event->id,
            'name'     => 'Ahmad',
            'email'    => 'ahmad@example.com',
            'phone'    => '081234567890',
            'city'     => 'Kabupaten Sleman',
            'status'   => 'registered',
        ]);

        // Attempt to register again with same email
        $response = $this->post(route('event.register.store', $event->slug), [
            'name'  => 'Ahmad Dahlan',
            'email' => 'ahmad@example.com',
            'phone' => '089999999999',
            'city'  => 'Kabupaten Sleman',
            'agree' => '1',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, EventRegistration::where('event_id', $event->id)->count());
    }

    public function test_registration_is_blocked_when_quota_is_full(): void
    {
        $event = $this->createDummyEvent(['quota' => 1]);

        EventRegistration::create([
            'event_id' => $event->id,
            'name'     => 'Peserta Pertama',
            'email'    => 'pertama@example.com',
            'phone'    => '081111111111',
            'city'     => 'Kota Yogyakarta',
            'status'   => 'registered',
        ]);

        $this->assertTrue($event->fresh()->is_full);

        // Attempt to register when full
        $response = $this->post(route('event.register.store', $event->slug), [
            'name'  => 'Peserta Kedua',
            'email' => 'kedua@example.com',
            'phone' => '082222222222',
            'city'  => 'Kabupaten Sleman',
            'agree' => '1',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, EventRegistration::where('event_id', $event->id)->count());
    }

    public function test_admin_can_view_registrations_and_update_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = $this->createDummyEvent();

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'name'     => 'Peserta Uji',
            'email'    => 'uji@example.com',
            'phone'    => '081234567899',
            'city'     => 'Kabupaten Bantul',
            'status'   => 'registered',
        ]);

        // Admin views list
        $viewResponse = $this->actingAs($admin)->get(route('admin.event.registrations', $event));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Peserta Uji');

        // Admin updates status to confirmed
        $updateResponse = $this->actingAs($admin)->patch(
            route('admin.event.registrations.update', [$event, $registration]),
            ['status' => 'confirmed']
        );

        $updateResponse->assertRedirect();
        $this->assertEquals('confirmed', $registration->fresh()->status);
    }

    public function test_admin_can_export_registrations_to_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = $this->createDummyEvent();

        EventRegistration::create([
            'event_id' => $event->id,
            'name'     => 'Peserta CSV',
            'email'    => 'csv@example.com',
            'phone'    => '081234567899',
            'city'     => 'Kabupaten Bantul',
            'status'   => 'registered',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.event.registrations.export', $event));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
