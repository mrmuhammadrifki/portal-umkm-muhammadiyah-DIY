<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan setiap kali pengguna menekan tombol "Pesan via WhatsApp".
 * Ini jejak klik, bukan pesanan terkonfirmasi — transaksi terjadi di luar sistem.
 */
class WhatsappOrder extends Model
{
    protected $fillable = [
        'user_id',
        'umkm_id',
        'product_id',
        'business_name',
        'product_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function umkmProfile(): BelongsTo
    {
        return $this->belongsTo(UmkmProfile::class, 'umkm_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
