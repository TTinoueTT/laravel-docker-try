<?php

namespace App\Services\Payment;

use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\NextUser;
use App\Models\Next\Payment\NextRakutenPurchase;
use App\Models\Next\Payment\NextRakutenSubscription;
use App\Models\Old\OldUser;
use App\Services\IMigrateService;

use Illuminate\Support\Facades\Log;

final class RakutenPayService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $oldUser)
    {
        if (!$oldUser instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        $subscriptions = $oldUser->rakutenSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            Log::info("No rakuten subscriptions found  => process is continue .... ");
            return PaymentType::UNKNOWN;
        } else {
            $lastSubscription = $subscriptions->last();

            $new = new NextRakutenSubscription();
            $new->open_id = $lastSubscription->open_id;
            $new->rsa_status = $lastSubscription->status + 1;
            $new->from_rakuten_service = $lastSubscription->service_id;
            $new->order_control_id = $lastSubscription->order_control_id;
            $new->auth_request_id = $lastSubscription->auth_request_id;
            $new->subscription_id = $lastSubscription->subscription_id;
            $new->created_at = $lastSubscription->created_at;
            $new->updated_at = $lastSubscription->updated_at;
            // $new->params = $lastSubscription->params; 必要であれば

            if ($new->save()) {
                Log::info("rakuten subscription saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the rakuten subscription.");
            }

            return PaymentType::RAKUTEN;
        }
    }

    public function migrateOrder(NextUser $nextUser, OldUser $oldUser, string $jsonParams)
    {
        $new = new NextRakutenPurchase();
        $purchases = $oldUser->rakutenPurchases()->get();

        foreach ($purchases as $oldPurchase) {
            $new->open_id = $nextUser->external_id;
            $new->order_cart_id = $oldPurchase->order_cart_id;
            $new->order_control_id = $oldPurchase->order_control_id;
            $new->price = $oldPurchase->price;
            $new->rsa_item_id = $oldPurchase->itemcd;
            // $new->state = $oldPurchase->pay_info_no;

            $new->created_at = $oldPurchase->created_at;
            $new->updated_at = $oldPurchase->updated_at;
            $new->params = $jsonParams;

            if ($new->save()) {
                Log::info("rakuten purchase saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the rakuten purchase.");
            }
        }
    }

    /**
     * NextRakutenSubscription モデルから、open_id プロパティが
     * 引数 $openId に一致するものがあるかどうかをチェック
     *
     * @param string $openId
     * @return boolean
     */
    public function checkDuplicateOpenId(string $openId): bool
    {
        $exists = NextRakutenSubscription::where('open_id', $openId)->exists();
        return $exists;
    }
}
