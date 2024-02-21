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
     * @param NextUser $nextUser
     * @return MigrateProfileIdMapDto
     */
    public function migrateOldToNewWithNew(BaseModel $oldUser, NextUser $nextUser): MigrateProfileIdMapDto
    {
        Log::info("============================================================");
        Log::info("********************* profile process *********************");
        Log::info("============================================================");

        $profiles = $oldUser->profiles()->get();
        $this->ensureProfileConsistency($profiles, $oldUser);

        $singleMap = [];
        $preferProfileId = -1;
        foreach ($profiles as $oldProfile) {
            $new = new NextProfile();
            $new->user_id = $nextUser->id;
            $new = $this->oldToNew($oldProfile, $new, 1);

            Log::info("┌=========================================================┐");
            Log::info(" full_name: {$new->full_name}, birthday: {$new->birthday}");
            Log::info(" Start save to {$new->getTable()} from {$oldProfile->getTable()}");
            Log::info("- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ");
            if ($new->save()) {
                Log::info(" Saved successfully.", ['new_profile_id' => $new->id]);
            } else {
                Log::error(" Failed to save the profile.");
            }
            log::info(" old_profile id : #{$oldProfile->id}");

            array_push($singleMap, new MigrateIdMapDto($new->id, $oldProfile->id));
            $preferProfileId = $new->id;
        }
        Log::info($profiles->isEmpty() ? "Not exist profile \/(´；ω；`;)\/" : "Finish profile migrate process !!!");

        Log::info("============================================================");
        Log::info("****************** target profile process ******************");
        Log::info("============================================================");

        $targetProfiles = $oldUser->targetProfiles()->get();
        $this->ensureTargetProfileConsistency($targetProfiles, $oldUser);

        $targetMap = [];
        $preferTargetId = -1;
        foreach ($targetProfiles as $oldTarget) {
            $new = new NextProfile();
            $new->user_id = $nextUser->id;
            $new = $this->oldToNew($oldTarget, $new, 2);

            Log::info("┌=========================================================┐");
            Log::info(" full_name: {$new->full_name}, birthday: {$new->birthday}");
            Log::info(" Start save to {$new->getTable()} from {$oldTarget->getTable()}");
            Log::info("- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ");
            if ($new->save()) {
                Log::info(" Saved successfully.", ['new_profile_id' => $new->id]);
            } else {
                Log::error(" Failed to save the profile.");
            }
            log::info(" old_target_profile id : #{$oldTarget->id}");

            array_push($targetMap, new MigrateIdMapDto($new->id, $oldTarget->id));

            // prefer target を取得する処理を作る
            if ($new->state != 1) {
                $preferTargetId = $new->id;
            }
        }

        $nextUser->prefer_profile_id = $preferProfileId;
        $nextUser->prefer_target_profile_id = $preferTargetId;

        if ($nextUser->save()) {
            Log::info("User update successfully,", ['user_id' => $nextUser->id]);
        } else {
            Log::error("Failed to save the user.");
        }

        Log::info($targetProfiles->isEmpty() ? "Not exist target_profile \/(´；ω；`;)\/" : "Finish target_profile migrate process !!!");

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
    private function oldToNew(BaseModel $old, NextProfile $new, int $type): NextProfile
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
     * @param Collection $profiles 古い user_id に紐づく profiles または target Profiles
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
            Log::info("重複したデータが存在します。");
            $duplicateIds = [];
            foreach ($duplicates->first() as $profile) {
                array_push($duplicateIds, $profile->id);
            }
            /*
                $duplicateIds は重複したデータをもつ profile の id を配列にしたもの
                array:3 [
                    0 => 6242
                    1 => 6243
                    2 => 6339
                ] // app/Services/ProfileService.php:172
                この時、6242, 6243, 6339 は 同一の名前と誕生日を持つ
            */
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
     * @param Collection $targetProfiles 古い user_id に紐づく target Profiles
     * @param OldUser $oldUser
     * @return void
     */
    private function ensureTargetProfileConsistency(Collection &$targetProfiles, OldUser $oldUser)
    {
        $dupProfileIds = $this->getDuplicateIdsIfDuplicateBirthdaysAndNames($targetProfiles);
        if ($dupProfileIds) {
            $firstId = array_shift($dupProfileIds);
            /*
                dupProfileIds が [6242,6243,6339] ← このような配列の時、
                $firstId に 先頭の 6242 が代入され、参照渡しにより、
                dupProfileIds は、[6243,6339] となる
             */

            foreach ($dupProfileIds as $id) {
                // 条件にあった histories にあるレコードのデータを更新させる
                OldHistory::where("user_id", $oldUser->id)
                    ->where("target_profile_id", $id)
                    ->update(["target_profile_id" => $firstId]);

                // dump(OldHistory::where("user_id", $oldUser->id)
                //     ->where("target_profile_id", $firstId));

                // dd($oldUser->histories()->get());
                // Log::info($id);
                // if ($id == 6339) {
                //     dd($oldUser->histories()->get()->map(function ($history) {
                //         return $history->target_profile_id;
                //     }));
                //     # code...
                //     dd($oldUser->histories()->get());
                // }
                OldTargetProfile::destroy($id);
                Log::info("delete profile id: {$id}");

                foreach ($targetProfiles as $i => $profile) {
                    if ($profile->id == $id) {
                        /* 参照渡ししている、$targetProfiles から一致する profile を削除 */

                        unset($targetProfiles[$i]);
                    }
                }

                // dd($targetProfiles);
            }
        }
    }
}
