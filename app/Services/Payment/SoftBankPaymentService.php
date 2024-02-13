<?php

namespace App\Services\Payment;

use App\Enums\OpenIdCarrierType;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\NextUser;
use App\Models\Next\Payment\NextSoftBankPurchase;
use App\Models\Next\Payment\NextSoftBankSubscription;
use App\Models\Old\OldUser;
use App\Models\Old\Payment\OldSoftbankPurchase;
use App\Services\IMigrateService;

use Illuminate\Support\Facades\Log;

final class SoftBankPaymentService implements IMigrateService
{
    private $openIdService;

    public function __construct(OpenIdService $openIdService)
    {
        $this->openIdService = $openIdService;
    }

    public function migrateOldToNew(BaseModel $oldUser)
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

    public function migrateOrder(NextUser $nextUser, OldUser $oldUser, string $jsonParams)
    {
        $new = new NextSoftBankPurchase();
        $purchases = $oldUser->softbankPurchases()->get();

        foreach ($purchases as $oldPurchase) {
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
