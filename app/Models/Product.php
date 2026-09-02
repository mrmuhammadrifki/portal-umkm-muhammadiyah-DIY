<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'umkm_id',
        'category_id',
        'name',
        'description',
        'image_path',
        'status',
    ];

    public function umkmProfile(): BelongsTo
    {
        return $this->belongsTo(UmkmProfile::class, 'umkm_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Path gambar sampul untuk thumbnail listing — foto pertama dari galeri
     * kalau ada, fallback ke image_path lama (produk yang dibuat sebelum
     * fitur multi-foto ada).
     */
    public function getCoverImagePathAttribute(): ?string
    {
        return $this->images->first()?->image_path ?? $this->image_path;
    }
}
