<?php

namespace App\Services\Payment;

use App\Enums\Old\RakutenPayStatus;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\NextUser;
use App\Models\Next\Payment\NextRakutenPurchase;
use App\Models\Next\Payment\NextRakutenSubscription;
use App\Models\Old\OldUser;
use App\Models\Old\OldHistory;
use App\Models\Old\Payment\OldRakutenPurchase;

use Illuminate\Support\Facades\Log;

final class RakutenPayService
{
    public function migrateOldToNew(BaseModel $oldUser, int $execMode)
    {
        if (!$oldUser instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        $subscriptions = $oldUser->rakutenSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            Log::info("Not found rakuten subscriptions => process is continue .... ");
            return PaymentType::UNKNOWN;
        } else {
            $lastSubscription = $subscriptions->last();

            # MIGRATE_EXEC_PATTERN によって処理の中断を行う
            if ($execMode == 1) {
                # 退会ステータス以外のものは skip
                if ($lastSubscription->status != RakutenPayStatus::CANCELED) {
                    return PaymentType::UNKNOWN;
                }
            } else {
                /*
                # 2回目実行の処理
                # updated_at が config("diff_before_migrate_time")よりも前のもので退会ステータスは skip
                */
                if ($lastSubscription->updated_at < config("app.diff_before_migrate_time") && $lastSubscription->status == RakutenPayStatus::CANCELED) {
                    return PaymentType::UNKNOWN;
                }
            }

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
            Log::info("Start save to {$new->table}");

            if ($new->save()) {
                Log::info("rakuten subscription saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the rakuten subscription.");
            }

            return PaymentType::RAKUTEN;
        }
    }

    public function migrateOrder(NextUser $nextUser, OldUser $oldUser, OldHistory $oldHistory, string $jsonParams)
    {
        $oldPurchase = OldRakutenPurchase::where('user_id', $oldUser->id)
            ->where('history_id', $oldHistory->id)
            ->first();

        if (is_null($oldPurchase)) {
            Log::info("No found order => process is continue .... ");
        } else {
            $new = new NextRakutenPurchase();
            $new->open_id = $nextUser->external_id;
            $new->order_cart_id = $oldPurchase->order_cart_id;
            $new->order_control_id = $oldPurchase->order_control_id;
            $new->price = $oldPurchase->price;
            $new->rsa_item_id = $oldPurchase->itemcd;
            // $new->state = $oldPurchase->pay_info_no;

            $new->created_at = $oldPurchase->created_at;
            $new->updated_at = $oldPurchase->updated_at;
            $new->params = $jsonParams;

            Log::info("Start save to {$new->getTable()}");

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
