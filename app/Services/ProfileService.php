<?php

namespace App\Services;

use App\Dto\MigrateProfileIdMapDto;
use App\Dto\Partial\MigrateIdMapDto;

use App\Models\BaseModel;
use App\Models\Next\NextProfile;
use App\Models\Next\NextUser;
use App\Models\Old\OldTargetProfile;
use App\Models\Old\OldUser;
use App\Models\Old\OldHistory;
use App\Models\Old\OldProfile;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class ProfileService implements IMigrateService
{
    private $historyService;

    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }
    public function migrateOldToNew(BaseModel $user): void
    {
    }

    /**
     * プロフィール情報を移行する。
     * @param BaseModel $oldUser
     * @param NextUser $NextUser
     * @return MigrateProfileIdMapDto
     */
    public function migrateOldToNewWithNew(BaseModel $oldUser, NextUser $NextUser): MigrateProfileIdMapDto
    {
        $profiles = $oldUser->profiles()->get();
        $this->ensureProfileConsistency($profiles, $oldUser);

        $targetProfiles = $oldUser->targetProfiles()->get();
        $this->ensureTargetProfileConsistency($targetProfiles, $oldUser);

        $singleMap = [];
        foreach ($profiles as $old) {
            $new = new NextProfile();
            $new->user_id = $NextUser->id;
            $new = $this->oldToNew($old, $new, 0, 1);

            Log::info("full_name: {$new->full_name}, birthday: {$new->birthday}");
            Log::info("Start save to {$new->getTable()} from {$old->getTable()}");
            if ($new->save()) {
                Log::info("Saved successfully.", ['profile_id' => $new->id]);
            } else {
                Log::error("Failed to save the profile.");
            }
            log::info("profile id : #{$old->id}");

            array_push($singleMap, new MigrateIdMapDto($new->id, $old->id));
        }
        Log::info($profiles->isEmpty() ? "Not exist profile \≠(   ._.)\≠" : "Finish profile migrate process !!!");

        $targetMap = [];
        foreach ($targetProfiles as $oldTarget) {
            $new = new NextProfile();
            $new->user_id = $NextUser->id;
            $new = $this->oldToNew($oldTarget, $new, 0, 2);

            Log::info("full_name: {$new->full_name}, birthday: {$new->birthday}");
            Log::info("Start save to {$new->getTable()} from {$oldTarget->getTable()}");
            if ($new->save()) {
                Log::info("saved successfully.", ['profile_id' => $new->id]);
            } else {
                Log::error("Failed to save the profile.");
            }
            log::info("target_profile id : #{$oldTarget->id}");

            array_push($targetMap, new MigrateIdMapDto($new->id, $old->id));
        }
        Log::info($targetProfiles->isEmpty() ? "Not exist target_profile \≠(   ._.)\≠" : "Finish target_profile migrate process !!!");

        Log::info("profile migrate process is finish !!!");

        return new MigrateProfileIdMapDto($singleMap, $targetMap);
    }

    /**
     * 旧プロフィール情報(自分、お相手)を新プロフィール情報に変更
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
        $dateTime = new \DateTime($old->birthday);
        $new->birthday = $dateTime->format('Y-m-d');
        $new->birthtime = "";
        $new->created_at = $old->created_at;
        $new->updated_at = $old->updated_at;
        // $new->gender = ;

        return $new;
    }

    /**
     * 重複データの不在確認処理、あれば最初の profile id を返却
     * @param Collection $profiles
     * @return array
     */
    private function getDuplicateIdsIfDuplicateBirthdaysAndNames(Collection $profiles): ?array
    {
        // birthday と full_name の組み合わせでグルーピング
        $grouped = $profiles->groupBy(function ($item) {
            return $item->birthday . '_' . $item->full_name;
        });

        // 重複したデータを含むグループをフィルタリング
        $duplicates = $grouped->filter(function ($items) {
            return $items->count() > 1;
        });

        // 重複があるかどうかをチェック
        if ($duplicates->isNotEmpty()) {
            $duplicateIds = [];
            foreach ($duplicates->first() as $profile) {
                array_push($duplicateIds, $profile->id);
            }
            Log::info("重複したデータが存在します。");
            return $duplicateIds;
        } else {
            Log::info("重複したデータはありません。");
            return null;
        }
    }


    /**
     * profile 情報を整合性とるために、history の更新 と profile の削除
     * @param Collection $profiles
     * @param OldUser $oldUser
     * @return void
     */
    private function ensureProfileConsistency(Collection &$profiles, OldUser $oldUser)
    {
        $dupProfileIds = $this->getDuplicateIdsIfDuplicateBirthdaysAndNames($profiles);
        if ($dupProfileIds) {
            $firstId = array_shift($dupProfileIds);

            foreach ($dupProfileIds as $id) {
                // 条件にあった histories にあるレコードのデータを更新させる
                OldHistory::where("user_id", $oldUser->id)
                    ->where("profile_id", $id)
                    ->update(["profile_id" => $firstId]);

                OldProfile::destroy($id);
                Log::info("delete profile id: {$id}");

                foreach ($profiles as $i => $profile) {
                    if ($profile->id == $id) {
                        unset($profiles[$i]);
                    }
                }
            }
        }
    }

    /**
     * targetProfile 情報を整合性とるために、history の更新 と targetProfile の削除
     * @param Collection $targetProfiles
     * @param OldUser $oldUser
     * @return void
     */
    private function ensureTargetProfileConsistency(Collection &$targetProfiles, OldUser $oldUser)
    {
        $dupProfileIds = $this->getDuplicateIdsIfDuplicateBirthdaysAndNames($targetProfiles);
        if ($dupProfileIds) {
            $firstId = array_shift($dupProfileIds);

            foreach ($dupProfileIds as $id) {
                // 条件にあった histories にあるレコードのデータを更新させる
                OldHistory::where("user_id", $oldUser->id)
                    ->where("target_profile_id", $id)
                    ->update(["target_profile_id" => $firstId]);

                OldTargetProfile::destroy($id);
                Log::info("delete profile id: {$id}");

                foreach ($targetProfiles as $i => $profile) {
                    if ($profile->id == $id) {
                        unset($targetProfiles[$i]);
                    }
                }
            }
        }
    }
}
