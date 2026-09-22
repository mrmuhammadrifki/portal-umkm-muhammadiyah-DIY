<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_event_with_seminar_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/event', [
            'title'          => 'Seminar Kewirausahaan',
            'type'           => 'seminar',
            'description'    => 'Deskripsi seminar lengkap',
            'date_start'     => Carbon::now()->addDays(5)->toDateTimeString(),
            'location'       => 'Yogyakarta',
            'contact_person' => 'Ibu Amalya | 082280126691',
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_admin_can_create_event_with_simplified_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (['pelatihan', 'pendampingan', 'workshop'] as $validType) {
            $response = $this->actingAs($admin)->post('/admin/event', [
                'title'          => 'Kegiatan ' . ucfirst($validType),
                'type'           => $validType,
                'description'    => 'Deskripsi lengkap kegiatan ' . $validType,
                'date_start'     => Carbon::now()->addDays(5)->toDateTimeString(),
                'location'       => 'Aula PWM DIY, Gedongkuning',
                'contact_person' => 'Ibu Amalya | 082280126691',
            ]);

            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('admin.event.index'));
        }

        $this->assertDatabaseHas('events', [
            'title'          => 'Kegiatan Pelatihan',
            'contact_person' => 'Ibu Amalya | 082280126691',
        ]);
    }
}
