<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Old\OldProfile;

use Illuminate\Support\Facades\Log;

final class ProfileService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $user): void
    {
        $profiles = $user->profiles()->get();
        // dd($profiles);
        // log::info("profiles {$profiles}");

        $targetProfiles = $user->target_profiles()->get();
        // OldProfile::ID

        foreach ($profiles as $profile) {
            // dump($profile);
            log::info("profile id : #{$profile->id}");
        }

        foreach ($targetProfiles as $target) {
            // dump($target);
            log::info("target_profile id : #{$target->id}");
        }
    }
}
