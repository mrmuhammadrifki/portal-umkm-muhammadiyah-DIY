<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'summary',
        'description',
        'image_path',
        'date_start',
        'date_end',
        'location',
        'organizer',
        'speaker',
        'quota',
        'cost',
        'registration_url',
        'whatsapp_contact',
        'contact_person',
        'is_published',
    ];

    protected $casts = [
        'date_start'   => 'datetime',
        'date_end'     => 'datetime',
        'is_published' => 'boolean',
        'quota'        => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'pelatihan'    => 'Pelatihan',
            'pendampingan' => 'Pendampingan',
            'workshop'     => 'Workshop',
            default        => ucfirst($this->type),
        };
    }

    public function getTypeBadgeClassesAttribute(): string
    {
        return match ($this->type) {
            'pelatihan'    => 'bg-blue-100 text-blue-800 border-blue-200',
            'pendampingan' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'workshop'     => 'bg-purple-100 text-purple-800 border-purple-200',
            default        => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getDisplayContactPersonAttribute(): string
    {
        return $this->contact_person ?: ($this->whatsapp_contact ?: '-');
    }

    public function getCleanWhatsappAttribute(): ?string
    {
        $source = $this->whatsapp_contact ?: $this->contact_person;
        if (! $source) {
            return null;
        }

        if (preg_match('/08[0-9]{8,12}/', $source, $matches)) {
            return '62' . substr($matches[0], 1);
        }

        $num = preg_replace('/[^0-9]/', '', $source);
        if (str_starts_with($num, '0')) {
            $num = '62' . substr($num, 1);
        }

        return strlen($num) >= 9 ? $num : null;
    }
}
