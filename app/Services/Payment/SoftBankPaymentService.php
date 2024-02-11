<?php

namespace App\Services\Payment;

use App\Enums\OpenIdCarrierType;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\Payment\NextSoftBankSubscription;
use App\Models\Old\OldUser;
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
            Log::info("No softbank subscriptions found  => process is continue .... ");
            return PaymentType::UNKNOWN;
        } else {
            $lastSubscription = $subscriptions->last();
            $openIdProfile = $this->openIdService->migrateOldToNewWithCarrier($oldUser, OpenIdCarrierType::SOFTBANK);

            $new = new NextSoftBankSubscription();
            $new->open_id = $openIdProfile->open_id;
            $new->rsa_status = $lastSubscription->our_status;
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

            if ($new->save()) {
                Log::info("softbank subscription saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the softbank subscription.");
            }

            return PaymentType::SOFTBANK;
        }
    }
}
