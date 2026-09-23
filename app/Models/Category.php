<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'min_revenue',
        'max_revenue',
        'description',
    ];

    protected $casts = [
        'min_revenue' => 'integer',
        'max_revenue' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function umkmProfiles(): HasMany
    {
        return $this->hasMany(UmkmProfile::class);
    }

    /**
     * Format rentang pendapatan bulanan untuk tampilan UI
     */
    public function getFormattedRevenueRangeAttribute(): string
    {
        if ($this->min_revenue === 0 || $this->min_revenue === null) {
            return '< Rp ' . number_format($this->max_revenue, 0, ',', '.') . ' / bulan';
        }

        if ($this->max_revenue === null) {
            return '> Rp ' . number_format($this->min_revenue, 0, ',', '.') . ' / bulan';
        }

        return 'Rp ' . number_format($this->min_revenue, 0, ',', '.') . ' – Rp ' . number_format($this->max_revenue, 0, ',', '.') . ' / bulan';
    }
}
