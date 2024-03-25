<?php

namespace App\Services\Payment;

use App\Enums\Old\SoftbankStatus;
use App\Enums\OpenIdCarrierType;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\NextUser;
use App\Models\Next\Payment\NextSoftBankPurchase;
use App\Models\Next\Payment\NextSoftBankSubscription;
use App\Models\Old\OldUser;
use App\Models\Old\OldHistory;
use App\Models\Old\Payment\OldSoftbankPurchase;

use Illuminate\Support\Facades\Log;

final class SoftBankPaymentService
{
    private $openIdService;

    public function __construct(OpenIdService $openIdService)
    {
        $this->openIdService = $openIdService;
    }

    public function migrateOldToNew(BaseModel $oldUser, int $execMode)
    {
        if (!$oldUser instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        $subscriptions = $oldUser->softbankSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            Log::info("Not found softbank subscriptions => process is continue .... ");
            return PaymentType::UNKNOWN;
        } else {
            $lastSubscription = $subscriptions->last();
            # MIGRATE_EXEC_PATTERN によって処理の中断を行う
            if ($execMode == 1) {
                # 退会ステータス以外のものは skip
                if ($lastSubscription->our_status != SoftbankStatus::CANCELED) {
                    return PaymentType::UNKNOWN;
                }
            } else {
                /*
                # 2回目実行の処理
                # updated_at が config("diff_before_migrate_time")よりも前のもので退会ステータスは skip
                */
                if ($lastSubscription->updated_at < config("app.diff_before_migrate_time") && $lastSubscription->our_status == SoftbankStatus::CANCELED) {
                    return PaymentType::UNKNOWN;
                }
            }

            $openIdProfile = $this->openIdService->migrateOldToNewWithCarrier($oldUser, OpenIdCarrierType::SOFTBANK);

            $new = new NextSoftBankSubscription();
            $new->open_id = $openIdProfile->open_id;
            $new->rsa_status = $lastSubscription->our_status + 1;
            $new->rsa_item_id = $lastSubscription->manage_no;
            $new->price = $lastSubscription->amount;
            $new->transaction_id = $lastSubscription->transaction_id;
            $new->order_no = $lastSubscription->order_no;
            $new->regist_status = $lastSubscription->regist_status;
            $new->result_status = $lastSubscription->result_status;
            $new->status_code = $lastSubscription->status_code;
            $new->created_at = $lastSubscription->created_at;
            $new->updated_at = $lastSubscription->updated_at;
            // $new->params = $lastSubscription->params; 必要であれば
            Log::info("Start save to {$new->table}");

            if ($new->save()) {
                Log::info("softbank subscription saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the softbank subscription.");
            }

            return PaymentType::SOFTBANK;
        }
    }

    public function migrateOrder(NextUser $nextUser, OldUser $oldUser, OldHistory $oldHistory, string $jsonParams)
    {
        // 重複するhistoryの存在チェック
        $oldPurchase = OldSoftbankPurchase::where(OldSoftbankPurchase::USER_ID, $oldUser->id)
            ->where(OldSoftbankPurchase::HISTORY_ID, $oldHistory->id)
            ->first();

        if (is_null($oldPurchase)) {
            Log::info("No found order => process is continue .... ");
        } else {
            $new = new NextSoftBankPurchase();
            $new->open_id = $nextUser->external_id;
            $new->rsa_status = $oldPurchase->our_status + 1;
            $new->rsa_item_id = $oldPurchase->manage_no;
            $new->price = $oldPurchase->amount;
            $new->transaction_id = $oldPurchase->transaction_id;
            $new->order_no = $oldPurchase->order_no;
            $new->result_status = $oldPurchase->result_status;
            $new->status_code = $oldPurchase->status_code;
            $new->created_at = $oldPurchase->created_at;
            $new->updated_at = $oldPurchase->updated_at;
            $new->params = $jsonParams;

            Log::info("Start save to {$new->getTable()}");

            if ($new->save()) {
                Log::info("softbank purchase saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the softbank purchase.");
            }
        }
    }

    /**
     * NextSoftBankSubscription モデルから、open_id プロパティが
     * 引数 $openId に一致するものがあるかどうかをチェック
     *
     * @param string $openId
     * @return boolean
     */
    public function checkDuplicateOpenId(string $openId): bool
    {
        $exists = NextSoftBankSubscription::where('open_id', $openId)->exists();
        return $exists;
    }
}
