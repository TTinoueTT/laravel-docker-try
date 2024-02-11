<?php

namespace App\Services\Payment;

use App\Enums\OpenIdCarrierType;
use App\Enums\PaymentType;
use App\Models\BaseModel;
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
            Log::info("No docomo subscriptions found => process is continue .... ");
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

            if ($new->save()) {
                Log::info("docomo subscription saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the docomo subscription.");
            }

            return PaymentType::DOCOMO;
        }
    }

    public function migratePurchase(BaseModel $user)
    {
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
