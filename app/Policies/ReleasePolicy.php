<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Release;
use App\Models\User;

class ReleasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isCommunicationAdmin();
    }
    public function create(User $user): bool
    {
        return $user->isCommunicationAdmin();
    }
    public function update(User $user, Release $release): bool
    {
        return $user->isCommunicationAdmin();
    }
    public function publish(User $user, Release $release): bool
    {
        return $user->isCommunicationAdmin();
    }
    public function view(User $user, Release $release): bool
    {
        return $user->isCommunicationAdmin();
    }
}
