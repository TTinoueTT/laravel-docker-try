<?php

namespace App\Services;

use App\Contexts\RandomComponent;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Models\Next\NextUser;
use App\Services\IMigrateService;
use App\Services\Payment\AmazonPayService;
use App\Services\Payment\AuPaymentService;
use App\Services\Payment\SoftBankPaymentService;
use App\Services\Payment\DocomoPaymentService;
use App\Services\Payment\RakutenPayService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

final class UserService implements IMigrateService
{
    private  $amazonPayService;
    private  $auPaymentService;
    private  $softPaymentService;
    private  $docomoPaymentService;
    private  $rakutenPayService;

    public function __construct(
        AmazonPayService $amazonPayService,
        AuPaymentService $auPaymentService,
        SoftBankPaymentService $softPaymentService,
        DocomoPaymentService $docomoPaymentService,
        RakutenPayService $rakutenPayService
    ) {
        $this->amazonPayService = $amazonPayService;
        $this->auPaymentService = $auPaymentService;
        $this->softPaymentService = $softPaymentService;
        $this->docomoPaymentService = $docomoPaymentService;
        $this->rakutenPayService = $rakutenPayService;
    }

    public function migrateOldToNew(BaseModel $user): NextUser
    {
        if (!$user instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }
        $nextUser = new NextUser();

        $nextUser->external_id = $user->email;
        $nextUser->interest_type = $this->exchangeIntent($user->intent);
        $nextUser->payment_type = $this->findAndSavePaymentType($user);
        $nextUser->migration_code = isset($user->migration_code) ? $user->migration_code : RandomComponent::Generate(12);
        $nextUser->mail_address = $user->mail_address;
        $nextUser->notification = $user->notification;
        $nextUser->notification_optout_at = isset($user->notification_optout_at) ? $user->notification_optout_at : Carbon::create(1000, 1, 1, 0, 0, 0);
        $nextUser->notification_optin_at = isset($user->notification_optin_at) ? $user->notification_optin_at : Carbon::create(1000, 1, 1, 0, 0, 0);
        $nextUser->created_at = $user->created_at;
        $nextUser->updated_at = $user->updated_at;

        if ($nextUser->save()) {
            Log::info("User saved successfully.", ['user_id' => $nextUser->id]);
        } else {
            Log::error("Failed to save the user.");
        }

        return $nextUser;
    }

    /**
     * Ruby に登録している intent を interest_type に変更
     * @param integer $intent 二進数の値
     * @return integer 10進数に変換して
     */
    private function exchangeIntent(?int $intent): int
    {
        if ($intent == null) {
            return 1;
        }

        $binaryString = "{$intent}";
        return bindec($binaryString);
    }

    /**
     * OldUser に payment_type が存在しないため、old の 決済情報を全部照合して、
     * データ移行を行い、payment_type を取得し、NextUser の更新を行う
     * @param OldUser $oldUser
     * @param NextUser $NextUser
     * @return int
     */
    private function findAndSavePaymentType(OldUser $oldUser): int
    {
        $paymentType = PaymentType::UNKNOWN;
        // SOFTBANK
        if ($paymentType == PaymentType::UNKNOWN) {
            $paymentType = $this->softPaymentService->migrateOldToNew($oldUser);
        }
        // AU
        if ($paymentType == PaymentType::UNKNOWN) {
            // $paymentType = $this->amazonPayService->migrateOldToNew($oldUser);
        }
        // DOCOMO
        if ($paymentType == PaymentType::UNKNOWN) {
            // $paymentType = $this->amazonPayService->migrateOldToNew($oldUser);
        }
        // RAKUTEN
        if ($paymentType == PaymentType::UNKNOWN) {
            // $paymentType = $this->amazonPayService->migrateOldToNew($oldUser);
        }
        // AMAZON
        if ($paymentType == PaymentType::UNKNOWN) {
            $paymentType = $this->amazonPayService->migrateOldToNew($oldUser);
        }

        return $paymentType;
    }
}
