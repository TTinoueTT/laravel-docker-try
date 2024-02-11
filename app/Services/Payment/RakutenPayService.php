<?php

namespace App\Services\Payment;

use App\Enums\PaymentType;
use App\Models\BaseModel;
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
            $new->rsa_status = $lastSubscription->status;
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
}
