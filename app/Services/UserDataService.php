<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Models\Next\NextUser;
use App\Models\Next\NextUserData;
use App\Services\IMigrateService;

use Illuminate\Support\Facades\Log;

final class UserDataService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $oldUser)
    {
    }

    public function migrateOldToNewWithNew(BaseModel $oldUser, NextUser $nextUser)
    {
        Log::info("============================================================");
        Log::info("******************** user data process ********************");
        Log::info("============================================================");
        $nextUserData = $this->oldToNew($oldUser, $nextUser);

        Log::info("Start save to {$nextUserData->getTable()}");
        if ($nextUserData->save()) {
            Log::info("saved successfully.", ['users_data id' => $nextUserData->id]);
        } else {
            Log::error("Failed to save the users_data.");
        }
        Log::info("user data migrate process is finish !!!");
    }

    private function oldToNew(OldUser $oldUser, NextUser $nextUser): NextUserData
    {
        $new = new NextUserData();
        $new->user_id = $nextUser->id;
        $oldUserData = $oldUser->users_data()->get()->first();
        $new->campaign_values = isset($oldUserData->campaign_values) ? $this->convertCampaignValues($oldUserData->campaign_values) : $new->campaign_values;
        $new->reservation_items = isset($oldUserData->reservation) ? $this->convertReservationData($oldUserData->reservation) : $new->reservation_items;

        if (isset($oldUserData->created_at)) {
            $new->created_at = $oldUserData->created_at;
        }

        if (isset($oldUserData->updated_at)) {
            $new->updated_at = $oldUserData->updated_at;
        }

        return $new;
    }

    private function convertCampaignValues(string $campaignValues)
    {
        $newData = [];

        if (is_null($campaignValues) || empty($campaignValues)) {
            return json_encode($newData);
        }
        $campaignObj = json_decode($campaignValues, true);

        return json_encode($campaignObj);
    }


    /**
     * 旧予約鑑定情報を新規テーブルように変換
     *
     * @param string|null $oldReservation
     * @return string
     */
    private function convertReservationData(?string $oldReservation): string
    {
        $newData = [];

        if (is_null($oldReservation) || empty($oldReservation)) {
            return json_encode($newData);
        }
        $reservationData = json_decode($oldReservation, true);

        if (isset($reservationData["this_month"]["created_at"]) && !empty($reservationData["this_month"]["created_at"])) {
            $dateParts = explode("-", $reservationData["this_month"]["created_at"]);
            $year = $dateParts[0];
            $month = intval($dateParts[1]);

            $newData["{$year}"]["{$month}"] = $reservationData["this_month"]["itemcd"];
        }

        if (isset($reservationData["last_month"]["created_at"]) && !empty($reservationData["last_month"]["created_at"])) {
            $dateParts = explode("-", $reservationData["last_month"]["created_at"]);
            $year = $dateParts[0];
            $month = intval($dateParts[1]);

            $newData["{$year}"]["{$month}"] = $reservationData["last_month"]["itemcd"];
        }

        return json_encode($newData);
    }
}
