<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'email',
        'phone',
        'institution_or_business',
        'subsector_or_category',
        'city',
        'notes',
        'status',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCleanWhatsappAttribute(): ?string
    {
        $num = preg_replace('/[^0-9]/', '', $this->phone ?? '');
        if (str_starts_with($num, '0')) {
            $num = '62' . substr($num, 1);
        }

        return strlen($num) >= 9 ? $num : null;
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'bg-green-100 text-green-800 border-green-200',
            'attended'  => 'bg-blue-100 text-blue-800 border-blue-200',
            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
            default     => 'bg-amber-100 text-amber-800 border-amber-200',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'Terkonfirmasi',
            'attended'  => 'Hadir',
            'cancelled' => 'Dibatalkan',
            default     => 'Terdaftar',
        };
    }
}
