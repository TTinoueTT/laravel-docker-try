<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Old\OldProfile;

use Illuminate\Support\Facades\Log;

final class ProfileService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $user): void
    {
        $profiles = $user->profiles;
        // OldProfile::ID

        foreach ($profiles as $profile) {
            log::info("profile id : #{$profile->id}");
        }
    }
}
