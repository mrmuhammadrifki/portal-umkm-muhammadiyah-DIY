<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    /**
     * Riwayat tombol "Pesan via WhatsApp" yang pernah ditekan pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        $orders = $request->user()->whatsappOrders()
            ->with(['umkmProfile.user', 'product.umkmProfile.user'])
            ->latest()
            ->paginate(10);

        return view('riwayat.index', ['orders' => $orders]);
    }
}
