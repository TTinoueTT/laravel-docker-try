<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Models\Next\NextUser;
use App\Models\Next\NextUserAnalysis;
use App\Services\IMigrateService;

use Illuminate\Support\Facades\Log;

final class UserAnalysisService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $oldUser)
    {
    }

    public function migrateOldToNewWithNew(BaseModel $oldUser, NextUser $nextUser)
    {
        $nextUserAnalysis = $this->oldToNew($oldUser, $nextUser);

        Log::info("Start save to {$nextUserAnalysis->getTable()}");
        if ($nextUserAnalysis->save()) {
            Log::info("saved successfully.", ['users_analyses id' => $nextUserAnalysis->id]);
        } else {
            Log::error("Failed to save the users_analyses.");
        }
        Log::info("user analysis migrate process is finish !!!");
    }

    private function oldToNew(OldUser $oldUser, NextUser $nextUser): NextUserAnalysis
    {
        $new = new NextUserAnalysis();
        $new->user_id = $nextUser->id;
        $new->ref_from = $oldUser->ref_from ? $oldUser->ref_from : $new->ref_from;
        $new->ref_cate = $oldUser->ref_cate ? $oldUser->ref_cate : $new->ref_cate;
        $new->ref_u_id = $oldUser->ref_u_id ? $oldUser->ref_u_id : $new->ref_u_id;
        $new->ref_ad_id = $oldUser->ref_ad_id ? $oldUser->ref_ad_id : $new->ref_ad_id;
        $new->created_at = $oldUser->created_at;
        $new->updated_at = $oldUser->updated_at;

        return $new;
    }
}
