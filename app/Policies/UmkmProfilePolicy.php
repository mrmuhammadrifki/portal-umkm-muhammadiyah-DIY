<?php

namespace App\Policies;

use App\Models\UmkmProfile;
use App\Models\User;

class UmkmProfilePolicy
{
    public function update(User $user, UmkmProfile $umkmProfile): bool
    {
        return $user->id === $umkmProfile->user_id;
    }

    public function moderate(User $user): bool
    {
        return $user->role === 'admin';
    }
}
