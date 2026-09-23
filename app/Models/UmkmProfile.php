<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UmkmProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'owner_name',
        'established_year',
        'employee_count',
        'monthly_revenue',
        'description',
        'address',
        'kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'whatsapp',
        'instagram',
        'nib',
        'has_halal_certificate',
        'halal_certificate_year',
        'has_attended_training',
        'logo_path',
        'subsector_id',
        'category_id',
        'status',
    ];

    protected $casts = [
        'established_year'       => 'integer',
        'employee_count'         => 'integer',
        'monthly_revenue'        => 'integer',
        'has_halal_certificate'  => 'boolean',
        'halal_certificate_year' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subsector(): BelongsTo
    {
        return $this->belongsTo(Subsector::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'umkm_id');
    }

    /**
     * Hitung kategori skala usaha secara dinamis berdasarkan pendapatan bulanan
     */
    public function getComputedCategoryScaleAttribute(): string
    {
        if ($this->monthly_revenue === null) {
            return 'Belum Mengisi';
        }

        if ($this->monthly_revenue < 25_000_000) {
            return 'Mikro';
        } elseif ($this->monthly_revenue <= 208_000_000) {
            return 'Kecil';
        } else {
            return 'Menengah';
        }
    }
}
