<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Old\OldProfile;

use Illuminate\Support\Facades\Log;

final class ProfileServices
{
    public function migrate_old_to_new(BaseModel $user): void
    {
        $profiles = $user->profiles;
        // OldProfile::ID

        foreach ($profiles as $profile) {
            log::info("profile id : #{$profile->id}");
        }
    }
}
