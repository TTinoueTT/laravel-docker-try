<?php

namespace App\Services\Payment;

use App\Enums\OpenIdCarrierType;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\NextUser;
use App\Models\Next\Payment\NextAuPurchase;
use App\Models\Next\Payment\NextAuSubscription;
use App\Models\Old\OldUser;
use App\Models\Old\OldHistory;
use App\Models\Old\Payment\OldAuPurchase;
use App\Services\IMigrateService;

use Illuminate\Support\Facades\Log;

final class AuPaymentService implements IMigrateService
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

        $subscriptions = $oldUser->auSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            Log::info("Not found au subscriptions => process is continue .... ");
            return PaymentType::UNKNOWN;
        } else {
            $lastSubscription = $subscriptions->last();
            $openIdProfile = $this->openIdService->migrateOldToNewWithCarrier($oldUser, OpenIdCarrierType::AU);

            $new = new NextAuSubscription();
            $new->open_id = $openIdProfile->open_id;
            $new->rsa_status = $lastSubscription->our_status + 1;
            $new->rsa_item_id = $lastSubscription->manage_no;
            $new->price = $lastSubscription->amount;
            $new->transaction_id = $lastSubscription->transaction_id;
            $new->continue_account_id = $lastSubscription->continue_account_id;
            $new->result_code = $lastSubscription->result_code;
            $new->created_at = $lastSubscription->created_at;
            $new->updated_at = $lastSubscription->updated_at;
            // $new->params = $lastSubscription->params; 必要であれば
            Log::info("Start save to {$new->getTable()}");
            if ($new->save()) {
                Log::info("au subscription saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the au subscription.");
            }

            return PaymentType::AU;
        }
    }

    public function migrateOrder(NextUser $nextUser, OldUser $oldUser, OldHistory $oldHistory, string $jsonParams)
    {
        $oldPurchase = OldAuPurchase::where('user_id', $oldUser->id)
            ->where('history_id', $oldHistory->id)
            ->first();

        if (is_null($oldPurchase)) {
            Log::info("No found order => process is continue .... ");
        } else {
            $new = new NextAuPurchase();
            $new->open_id = $nextUser->external_id;
            $new->rsa_status = $oldPurchase->our_status + 1;
            $new->rsa_item_id = $oldPurchase->manage_no;
            $new->price = $oldPurchase->amount;
            $new->transaction_id = $oldPurchase->transaction_id;
            $new->pay_info_no = $oldPurchase->pay_info_no;
            $new->result_code = $oldPurchase->result_code;
            $new->created_at = $oldPurchase->created_at;
            $new->updated_at = $oldPurchase->updated_at;
            $new->params = $jsonParams;

            Log::info("Start save to {$new->getTable()}");
            if ($new->save()) {
                Log::info("au purchase saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the au purchase.");
            }
        }
    }

    /**
     * NextAuSubscription モデルから、open_id プロパティが
     * 引数 $openId に一致するものがあるかどうかをチェック
     *
     * @param string $openId
     * @return boolean
     */
    public function checkDuplicateOpenId(string $openId): bool
    {
        $exists = NextAuSubscription::where('open_id', $openId)->exists();
        return $exists;
    }
}
