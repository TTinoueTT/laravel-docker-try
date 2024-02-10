<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Old\OldHistory;
use App\Models\Old\OldUser;

final class HistoryService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $user)
    {
        if (!$user instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        // TODO: History に関するレコードを取得して新規レコードに追加
    }

    public function createParams(int $historyId)
    {
        $history = OldHistory::find($historyId);
        // TODO:
        $params = [
            "type" => "2",
            "price" => $history->price,
            // "bg_url" => "https://web-img.rensa.jp.net/images/capo/ogushi-noriko.net/member/bg.jpg",
            "itemcd" => $history->itemcd,
            "carrier" => "amazon",
            "is_tmode" => "1",
            "next_url" => "/history/check",
            "item_name" => "「もう変わりませんよ」彼が既に決めた◆あなたへの想い・恋本音",
            "profile_id" => $history->profile_id,
            "external_id" => "sho.nagao@rensa.co.jp",
            "payment_url" => "/history/check",
            "payment_type" => "prc",
            "target_gender" => "1",
            "appraisal_type" => "payment",
            "redirect_method" => "post",
            // "return_error_url" => "https://dev-ogushi.rensa.jp.net/pre/love046",
            "target_full_name" => "てすろう",
            // "return_cancel_url" => "https://dev-ogushi.rensa.jp.net/pre/love046",
            "target_profile_id" => "43",
            // "return_success_url" => "https://dev-ogushi.rensa.jp.net/history/save",
            "target_birthday_day" => "20",
            "target_birthday_year" => "1984",
            "target_birthday_month" => "10",
            "selector_target_profile_id" => $history->target_profile_id
        ];
    }
}
