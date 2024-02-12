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
        $nextUserData = $this->oldToNew($oldUser, $nextUser);

        if ($nextUserData->save()) {
            Log::info("saved successfully.", ['users_data id' => $nextUserData->id]);
        } else {
            Log::error("Failed to save the users_data.");
        }
    }

    private function oldToNew(OldUser $oldUser, NextUser $nextUser): NextUserData
    {
        $new = new NextUserData();
        $new->user_id = $nextUser->id;
        $oldUserData = $oldUser->users_data()->get()->first();
        $new->campaign_values = isset($oldUserData->campaign_values) ? $oldUserData->campaign_values : $new->campaign_values;
        $new->reservation_items = $this->convertReservationData($oldUserData->reservation);
        $new->created_at = $oldUserData->created_at;
        $new->updated_at = $oldUserData->updated_at;

        return $new;
    }


    private function convertReservationData(String $oldReservation): string
    {
        $reservationData = json_decode($oldReservation, true);
        $newData = [];

        if (empty($reservationData)) {
            return json_encode($newData);
        }

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
