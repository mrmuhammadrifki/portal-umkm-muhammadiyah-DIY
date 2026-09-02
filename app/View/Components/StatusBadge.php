<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class StatusBadge extends Component
{
    private const MAPS = [
        // UmkmProfile.status
        'pending' => ['bg-yellow-100 text-yellow-800', 'Pending'],
        'approved' => ['bg-green-100 text-green-800', 'Approved'],
        'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
        // Product.status
        'active' => ['bg-green-100 text-green-800', 'Aktif'],
        'inactive' => ['bg-gray-100 text-gray-600', 'Nonaktif'],
        // User.is_active (dipakai lewat variant="account")
        'account_active' => ['bg-green-100 text-green-800', 'Aktif'],
        'account_suspended' => ['bg-red-100 text-red-800', 'Disuspend'],
    ];

    public string $classes;
    public string $text;

    public function __construct(string $status, ?string $label = null)
    {
        [$this->classes, $default] = self::MAPS[$status] ?? ['bg-gray-100 text-gray-600', ucfirst($status)];
        $this->text = $label ?? $default;
    }

    public function render(): View
    {
        return view('components.status-badge');
    }
}
