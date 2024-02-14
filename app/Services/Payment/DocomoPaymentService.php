<?php

namespace App\Services\Payment;

use App\Enums\OpenIdCarrierType;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\NextUser;
use App\Models\Next\Payment\NextDocomoPurchase;
use App\Models\Next\Payment\NextDocomoSubscription;
use App\Models\Next\Payment\NextDocomoSuid;
use App\Models\Old\OldUser;
use App\Services\IMigrateService;

use Illuminate\Support\Facades\Log;

final class DocomoPaymentService implements IMigrateService
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

        $subscriptions = $oldUser->docomoSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            Log::info("Not found docomo payment subscription => process is continue .... ");
            return PaymentType::UNKNOWN;
        } else {
            $lastSubscription = $subscriptions->last();

            $nextDocomoSuid = $this->migrateSuid($oldUser);

            $new = new NextDocomoSubscription();
            $new->open_id = $nextDocomoSuid->open_id;
            $new->request_type = $lastSubscription->request_type;
            $new->rsa_status = $lastSubscription->status;
            $new->docomo_status = $lastSubscription->docomo_status;
            $new->cp_token = $lastSubscription->cp_token;
            $new->cp_order_no = $lastSubscription->cp_order_no;
            $new->rsa_item_id = $lastSubscription->cp_order_no;
            $new->docomo_subscription_status = $lastSubscription->transaction_type;
            $new->docomo_token = $lastSubscription->docomo_token;
            $new->docomo_order_no = $lastSubscription->docomo_order_no;
            $new->docomo_auth_time = $lastSubscription->docomo_auth_time;
            $new->created_at = $lastSubscription->created_at;
            $new->updated_at = $lastSubscription->updated_at;
            // $new->params = $lastSubscription->params; 必要であれば

            Log::info("Start save to {$new->getTable()}");
            if ($new->save()) {
                Log::info("docomo subscription saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the docomo subscription.");
            }

            return PaymentType::DOCOMO;
        }
    }

    public function migrateOrder(NextUser $nextUser, OldUser $oldUser, string $jsonParams)
    {
        $new = new NextDocomoPurchase();
        $purchases = $oldUser->docomoPurchases()->get();

        foreach ($purchases as $oldPurchase) {
            $new->open_id = $nextUser->external_id;
            $new->rsa_status = $oldPurchase->status;
            $new->price = $oldPurchase->price;
            $new->cp_token = $oldPurchase->cp_token;
            $new->cp_order_no = $oldPurchase->cp_order_no;
            $new->rsa_item_id = $oldPurchase->cp_param;
            $new->docomo_purchase_status = $oldPurchase->transaction_type;
            $new->docomo_token = $oldPurchase->docomo_token;

            if ($oldPurchase->docomo_auth_time != '0000-00-00 00:00:00') {
                $new->docomo_auth_time = $oldPurchase->docomo_auth_time;
            }

            $new->created_at = $oldPurchase->created_at;
            $new->updated_at = $oldPurchase->updated_at;
            $new->params = $jsonParams;

            Log::info("Start save to {$new->getTable()}");
            if ($new->save()) {
                Log::info("docomo purchase saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the docomo purchase.");
            }
        }
    }

    /**
     * NextDocomoSubscription モデルから、open_id プロパティが
     * 引数 $openId に一致するものがあるかどうかをチェック
     *
     * @param string $openId
     * @return boolean
     */
    public function checkDuplicateOpenId(string $openId): bool
    {
        $exists = NextDocomoSubscription::where('open_id', $openId)->exists();
        return $exists;
    }

    /**
     * user_id で一意性あり
     *
     * @param BaseModel $oldUser
     * @return NextDocomoSuid
     */
    private function migrateSuid(BaseModel $oldUser): NextDocomoSuid
    {
        $suidData = $oldUser->docomoSuids()->get()->first();
        if (is_null($suidData)) {
            Log::error("Failed not found docomo suid", ["old_user_id" => $oldUser->id]);
        }
        $openIdProfile = $this->openIdService->migrateOldToNewWithCarrier($oldUser, OpenIdCarrierType::DOCOMO);

        $new = new NextDocomoSuid();

        $new->open_id = $openIdProfile->open_id;
        $new->suid = $suidData->suid;
        $new->guid = $suidData->guid;
        $new->created_at = $suidData->created_at;
        $new->updated_at = $suidData->updated_at;

        return $new;
    }
}
