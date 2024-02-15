<?php

namespace App\Services;

use App\Dto\MigrateProfileIdMapDto;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\NextHistory;
use App\Models\Old\OldHistory;
use App\Models\Old\OldUser;
use App\Models\Next\NextUser;

use App\Services\Payment\AmazonPayService;
use App\Services\Payment\AuPaymentService;
use App\Services\Payment\SoftBankPaymentService;
use App\Services\Payment\DocomoPaymentService;
use App\Services\Payment\RakutenPayService;

use Illuminate\Support\Facades\Log;

final class HistoryService implements IMigrateService
{
    private  $amazonPayService;
    private  $auPaymentService;
    private  $softbankPaymentService;
    private  $docomoPaymentService;
    private  $rakutenPayService;

    public function __construct(
        AmazonPayService $amazonPayService,
        AuPaymentService $auPaymentService,
        SoftBankPaymentService $softbankPaymentService,
        DocomoPaymentService $docomoPaymentService,
        RakutenPayService $rakutenPayService
    ) {
        $this->amazonPayService = $amazonPayService;
        $this->auPaymentService = $auPaymentService;
        $this->softbankPaymentService = $softbankPaymentService;
        $this->docomoPaymentService = $docomoPaymentService;
        $this->rakutenPayService = $rakutenPayService;
    }
    public function migrateOldToNew(BaseModel $user)
    {
    }

    /**
     * Undocumented function
     * @param BaseModel $oldUser
     * @param NextUser $nextUser
     * @return void
     */
    public function migrateOldToNewWithNew(BaseModel $oldUser, NextUser $nextUser, MigrateProfileIdMapDto $migrateProfileIdMap): void
    {
        if (!$oldUser instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        $histories = $oldUser->histories()->orderBy('created_at', 'desc')->get();

        foreach ($histories as $oldHistory) {
            if ($oldHistory->is_free == 1) {
                continue;
            }

            $newHistory = $this->oldToNew($oldHistory, new NextHistory(), $nextUser, $migrateProfileIdMap);

            Log::info("*******************************************************************************************");
            Log::info("Save to {$newHistory->getTable()}, from old ", [
                OldHistory::ID                => $oldHistory->id,
                OldHistory::ITEMCD            => $oldHistory->itemcd,
                OldHistory::PROFILE_ID        => $oldHistory->profile_id,
                OldHistory::TARGET_PROFILE_ID => $oldHistory->target_profile_id,
            ]);

            // 重複するhistoryの存在チェック
            $exists = NextHistory::where('user_id', $newHistory->user_id)
                ->where('itemcd', $newHistory->itemcd)
                ->where('profile_id', $newHistory->profile_id)
                ->where('target_profile_id', $newHistory->target_profile_id)
                ->exists();

            if ($exists) {
                Log::info("A record with the same user_id, itemcd, profile_id, and target_profile_id combination already exists, skipping save.");
                continue;
            }

            if ($newHistory->save()) {
                Log::info("Saved successfully,new data ", [
                    NextHistory::ID                => $newHistory->id,
                    NextHistory::ITEMCD            => $newHistory->itemcd,
                    NextHistory::PROFILE_ID        => $newHistory->profile_id,
                    NextHistory::TARGET_PROFILE_ID => $newHistory->target_profile_id,
                ]);
            } else {
                Log::error("Failed to save the history.");
            }

            // 決済注文レコードの移行をこのあたりで行う
            if ($oldHistory->price > 0) {
                $this->savePaymentOrder($newHistory, $oldHistory, $nextUser, $oldUser);
            }
        }

        Log::info($histories->isEmpty() ? "Not exist history \/(´；ω；`;)\/" : "history migrate process is finish !!!");
    }

    /**
     * 旧情報を新情報に移行
     * @param BaseModel $old
     * @param NextHistory $new
     * @return NextHistory
     */
    private function oldToNew(
        BaseModel $old,
        NextHistory $new,
        NextUser $nextUser,
        MigrateProfileIdMapDto $migrateProfileIdMap
    ): NextHistory {
        $new->user_id = $nextUser->id;
        $new->itemcd = $old->itemcd;
        $new->payment_type = $nextUser->payment_type;
        $new->content_key = "NMA";

        foreach ($migrateProfileIdMap->getSingle() as $migrateIdMapDto) {
            if ($old->profile_id == $migrateIdMapDto->getOld()) {
                $new->profile_id = $migrateIdMapDto->getNew();
            }
        }

        foreach ($migrateProfileIdMap->getTarget() as $migrateIdMapDto) {
            if ($old->target_profile_id > 0 && $old->target_profile_id == $migrateIdMapDto->getOld()) {
                $new->target_profile_id = $migrateIdMapDto->getNew();
            }
        }

        return $new;
    }

    private function savePaymentOrder(NextHistory $nextHistory, OldHistory $oldHistory, NextUser $nextUser, OldUser $oldUser)
    {
        switch ($nextHistory->payment_type) {
            case PaymentType::SOFTBANK:
                $this->softbankPaymentService->migrateOrder($nextUser, $oldUser, $oldHistory, $this->createParams($nextHistory, $nextUser));
                break;
            case PaymentType::AU:
                $this->auPaymentService->migrateOrder($nextUser, $oldUser, $oldHistory, $this->createParams($nextHistory, $nextUser));
                break;
            case PaymentType::DOCOMO:
                $this->docomoPaymentService->migrateOrder($nextUser, $oldUser, $oldHistory, $this->createParams($nextHistory, $nextUser));
                break;
            case PaymentType::RAKUTEN:
                $this->rakutenPayService->migrateOrder($nextUser, $oldUser, $oldHistory, $this->createParams($nextHistory, $nextUser));
                break;
            case PaymentType::AMAZON:
                $this->amazonPayService->migrateOrder($nextUser, $oldUser, $oldHistory, $this->createParams($nextHistory, $nextUser));
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * 注文レコードの params の値生成
     * @param NextHistory $history
     * @param NextUser $nextUser
     * @return string
     */
    public function createParams(NextHistory $history, NextUser $nextUser): string
    {
        $carrier = "unknown";

        switch ($history->payment_type) {
            case PaymentType::SOFTBANK:
                $carrier = "softbank";
                break;

            case PaymentType::AU:
                $carrier = "au";
                break;

            case PaymentType::DOCOMO:
                $carrier = "docomo";
                break;

            case PaymentType::RAKUTEN:
                $carrier = "rakuten";
                break;

            case PaymentType::AMAZON:
                $carrier = "amazon";
                break;

            default:
                # code...
                break;
        }

        $params = [
            "type" => "2",
            "price" => $history->price,
            // "bg_url" => "https://web-img.rensa.jp.net/images/capo/ogushi-noriko.net/member/bg.jpg",
            "itemcd" => $history->itemcd,
            "carrier" => $carrier,
            "is_tmode" => env("APP_ENV") == "production" ? 0 : 1,
            "next_url" => "/history/check",
            "item_name" => "",
            "profile_id" => $history->profile_id,
            "external_id" => $nextUser->external_id,
            "payment_url" => "/history/check",
            "payment_type" => $history->payment_type,
            "target_gender" => "",
            "appraisal_type" => "payment",
            "redirect_method" => "post",
            // "return_error_url" => "https://dev-ogushi.rensa.jp.net/pre/love046",
            // "target_full_name" => "てすろう",
            // "return_cancel_url" => "https://dev-ogushi.rensa.jp.net/pre/love046",
            "target_profile_id" => $history->target_profile_id,
            // "return_success_url" => "https://dev-ogushi.rensa.jp.net/history/save",
            // "target_birthday_day" => "20",
            // "target_birthday_year" => "1984",
            // "target_birthday_month" => "10",
            "selector_target_profile_id" => $history->target_profile_id
        ];

        return json_encode($params);
    }
}
