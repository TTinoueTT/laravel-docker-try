<?php

namespace App\Services\Payment;

use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Next\Payment\NextDocomoSubscription;
use App\Models\Next\Payment\NextDocomoSuid;
use App\Models\Next\Payment\NextOpenId;
use App\Models\Old\OldUser;
use App\Services\IMigrateService;

use Illuminate\Support\Facades\Log;

final class OpenIdService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $oldUser)
    {
    }

    public function migrateOldToNewWithCarrier(BaseModel $oldUser, int $carrierType)
    {
        if (!$oldUser instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        $openIdProfile = $oldUser->openIdProfiles()->get()->first();

        if (is_null($openIdProfile)) {
            Log::info("No open id found => process is continue .... ");
        } else {

            $new = new NextOpenId();
            $new->open_id = $openIdProfile->claimed_id;
            $new->carrier_type = $carrierType;

            $new->created_at = $openIdProfile->created_at;
            $new->updated_at = $openIdProfile->updated_at;
            // $new->params = $lastSubscription->params; 必要であれば

            // 同じ open_id があれば、保存しない
            $isExists = $this->checkDuplicateOpenId($new->open_id);

            if ($isExists) {
                return $new;
            }

            Log::info("Start save to {$new->getTable()}");

            if ($new->save()) {
                Log::info("open id saved successfully.", ['open_id' => $new->open_id]);
            } else {
                Log::error("Failed to save the open id.");
            }

            return $new;
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
        $exists = NextOpenId::where('open_id', $openId)->exists();
        return $exists;
    }
}
