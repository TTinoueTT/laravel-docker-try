<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use \Illuminate\Database\Eloquent\Model;

final class ProfileServices
{
    public function migrate_old_to_new(Model $user): void
    {
        $profiles = $user->profiles;

        foreach ($profiles as $profile) {
            log::info("profile id : #{$profile->id}");
        }
    }
}
