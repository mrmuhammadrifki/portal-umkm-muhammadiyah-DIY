<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UmkmProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return view('dashboard', [
                'pendingCount' => UmkmProfile::where('status', 'pending')->count(),
                'approvedCount' => UmkmProfile::where('status', 'approved')->count(),
                'totalProducts' => Product::where('status', 'active')->count(),
            ]);
        }

        return view('dashboard', ['profile' => $user->umkmProfile]);
    }
}
