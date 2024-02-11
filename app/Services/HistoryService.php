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
    private  $softPaymentService;
    private  $docomoPaymentService;
    private  $rakutenPayService;

    public function __construct(
        AmazonPayService $amazonPayService,
        AuPaymentService $auPaymentService,
        SoftBankPaymentService $softPaymentService,
        DocomoPaymentService $docomoPaymentService,
        RakutenPayService $rakutenPayService
    ) {
        $this->amazonPayService = $amazonPayService;
        $this->auPaymentService = $auPaymentService;
        $this->softPaymentService = $softPaymentService;
        $this->docomoPaymentService = $docomoPaymentService;
        $this->rakutenPayService = $rakutenPayService;
    }
    public function migrateOldToNew(BaseModel $user)
    {
        // if (!$user instanceof OldUser) {
        //     throw new \InvalidArgumentException('Expected an instance of OldUser');
        // }

        // $histories = $user->histories()->orderBy('created_at', 'desc')->get();
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

        foreach ($histories as $history) {
            if ($history->is_free == 1) {
                continue;
            }

            $new = $this->oldToNew($history, new NextHistory(), $nextUser, $migrateProfileIdMap);

            if ($new->save()) {
                Log::info("saved successfully.", ['history_id' => $new->id]);
            } else {
                Log::error("Failed to save the history.");
            }

            // 決済注文レコードの移行をこのあたりで行う
            $this->savePaymentOrder($nextUser->payment_type);
        }
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

        $migrateProfileIdMap->getSingle();
        foreach ($migrateProfileIdMap->getSingle() as $migrateIdMapDto) {
            if ($old->profile_id == $migrateIdMapDto->getOld()) {
                $new->profile_id = $migrateIdMapDto->getNew();
            }
        }

        foreach ($migrateProfileIdMap->getTarget() as $migrateIdMapDto) {
            if ($old->target_profile_id == $migrateIdMapDto->getOld()) {
                $new->target_profile_id = $migrateIdMapDto->getNew();
            }
        }

        return $new;
    }

    private function savePaymentOrder(int $paymentType)
    {
        switch ($paymentType) {
            case PaymentType::SOFTBANK:
                # code...
                break;

            default:
                # code...
                break;
        }
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
