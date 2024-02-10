<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Next\NextProfile;
use App\Models\Next\NextUser;
use App\Models\Old\OldProfile;

use Illuminate\Support\Facades\Log;

final class ProfileService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $user): void
    {
        // $profiles = $user->profiles()->get();
        // // dd($profiles);
        // // log::info("profiles {$profiles}");

        // $targetProfiles = $user->target_profiles()->get();
        // // OldProfile::ID

        // foreach ($profiles as $profile) {
        //     // dump($profile);
        //     log::info("profile id : #{$profile->id}");
        // }

        // foreach ($targetProfiles as $target) {
        //     // dump($target);
        //     log::info("target_profile id : #{$target->id}");
        // }
    }

    /**
     * プロフィール情報を移行する。
     * @param BaseModel $user
     * @param NextUser $NextUser
     * @return void
     */
    public function migrateOldToNewWithNew(BaseModel $user, NextUser $NextUser): void
    {
        $profiles = $user->profiles()->get();
        $targetProfiles = $user->target_profiles()->get();

        foreach ($profiles as $old) {
            $new = new NextProfile();
            $new->user_id = $NextUser->id;
            $new = $this->oldToNew($old, $new, 0, 1);
            Log::info($new->full_name);

            // TODO: 削除するのが面倒なため、コメントアウト
            // if ($new->save()) {
            //     Log::info("saved successfully.", ['profile_id' => $new->id]);
            // } else {
            //     Log::error("Failed to save the profile.");
            // }

            log::info("profile id : #{$old->id}");
        }

        foreach ($targetProfiles as $oldTarget) {
            $new = new NextProfile();
            $new->user_id = $NextUser->id;
            $new = $this->oldToNew($oldTarget, $new, 0, 2);
            Log::info($new->full_name);

            // TODO: 削除するのが面倒なため、コメントアウト
            // if ($new->save()) {
            //     Log::info("saved successfully.", ['profile_id' => $new->id]);
            // } else {
            //     Log::error("Failed to save the profile.");
            // }

            log::info("target_profile id : #{$oldTarget->id}");
        }
    }

    /**
     * 旧プロフィール情報(自分、お相手)を新プロフィール情報に変更
     *
     * @param BaseModel $old
     * @param NextProfile $new
     * @param integer $state
     * @param integer $type
     * @return NextProfile
     */
    private function oldToNew(BaseModel $old, NextProfile $new, int $state, int $type): NextProfile
    {
        if (isset($old->is_hidden)) {
            $new->state = $old->is_hidden;
        }
        $new->type = $type;
        $new->full_name = $old->full_name;
        $new->last_name = "";
        $new->first_name = "";
        $new->last_name_kana = "";
        $new->first_name_kana = "";
        $new->birth_place = "";
        // dd($old->birthday);

        $dateTime = new \DateTime($old->birthday);
        $new->birthday = $dateTime->format('Y-m-d');
        $new->birthtime = "";
        $new->created_at = $old->created_at;
        $new->updated_at = $old->updated_at;
        // $new->gender = ;

        return $new;
    }
}
