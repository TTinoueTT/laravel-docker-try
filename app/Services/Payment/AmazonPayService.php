<?php

namespace App\Services\Payment;

use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\NextUser;
use App\Models\Next\Payment\NextAmazonPayBillingAgreement;
use App\Models\Next\Payment\NextAmazonPayOrderReference;
use App\Models\Old\OldUser;
use App\Models\Old\OldHistory;
use App\Models\Old\Payment\OldAmazonPayBillingAgreement;
use App\Models\Old\Payment\OldAmazonPayOrderReference;
use App\Services\IMigrateService;
use Illuminate\Support\Facades\Log;

final class AmazonPayService implements IMigrateService
{
    private $nextAmazonPayBillingAgreement;

    function __construct(NextAmazonPayBillingAgreement $nextAmazonPayBillingAgreement)
    {
        $this->nextAmazonPayBillingAgreement = $nextAmazonPayBillingAgreement;
    }
    public function migrateOldToNew(BaseModel $oldUser): int
    {
        if (!$oldUser instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        // AmazonPay に関するレコードを取得して新規レコードに追加
        $billingAgreements = $oldUser->amazonPayBillingAgreements()->get();
        if ($billingAgreements->isEmpty()) {
            Log::info("Not found billing agreements => process is continue .... ");
            return PaymentType::UNKNOWN;
        } else {
            $lastBillingAgreement = $billingAgreements->last();
            // $lastBillingAgreement を使用した処理...
            $new = new NextAmazonPayBillingAgreement();
            $new->open_id = $oldUser->email;
            $new->amazon_billing_agreement_id = $lastBillingAgreement->amazon_billing_agreement_id;
            $new->seller_billing_agreement_id = $lastBillingAgreement->seller_billing_agreement_id;
            $new->billing_agreement_state = $lastBillingAgreement->status;
            $new->billing_agreement_reason_code = $lastBillingAgreement->state_reason;
            if ($lastBillingAgreement->cancelled_at != '0000-00-00 00:00:00') {
                $new->cancelled_at = $lastBillingAgreement->cancelled_at;
            }
            $new->created_at = $lastBillingAgreement->created_at;
            $new->updated_at = $lastBillingAgreement->updated_at;

            Log::info("Start save to {$new->getTable()}");
            if ($new->save()) {
                Log::info("billing agreement saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the billing agreement.");
            }

            return PaymentType::AMAZON;
        }
    }


    public function migrateOrder(NextUser $nextUser, OldUser $oldUser, OldHistory $oldHistory, string $jsonParams)
    {
        $oldPurchase = OldAmazonPayOrderReference::where('user_id', $oldUser->id)
            ->where('history_id', $oldHistory->id)
            ->first();

        if (is_null($oldPurchase)) {
            Log::info("No found order => process is continue .... ");
        } else {
            $new = new NextAmazonPayOrderReference();
            $new->open_id = $nextUser->external_id;
            $new->billing_agreement_id = $oldPurchase->billing_agreement_id;
            $new->amazon_order_reference_id = $oldPurchase->amazon_order_reference_id;
            $new->price = $oldPurchase->order_amount;
            $new->order_reference_state = $oldPurchase->status;
            $new->order_reference_reason_code = $oldPurchase->state_reason;
            $new->created_at = $oldPurchase->created_at;
            $new->updated_at = $oldPurchase->updated_at;
            $new->params = $jsonParams;

            Log::info("Start save to {$new->getTable()}");
            if ($new->save()) {
                Log::info("amazon purchase saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the amazon purchase.");
            }
        }
    }

    /**
     * NextAmazonPayBillingAgreement モデルから、open_id プロパティが
     * 引数 $openId に一致するものがあるかどうかをチェック
     *
     * @param string $openId
     * @return boolean
     */
    public function checkDuplicateOpenId(string $openId): bool
    {
        $exists = NextAmazonPayBillingAgreement::where('open_id', $openId)->exists();
        return $exists;
    }
}
