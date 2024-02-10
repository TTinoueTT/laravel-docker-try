<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Models\Next\NextUser;
use App\Services\IMigrateService;
use App\Services\Payment\AmazonPayService;
use App\Services\Payment\AuPaymentService;
use App\Services\Payment\SoftBankPaymentService;
use App\Services\Payment\DocomoPaymentService;
use App\Services\Payment\RakutenPayService;

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

    public function migrateOldToNew(BaseModel $user)
    {
        if (!$user instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }
        $nextUser = new NextUser();
        $nextUser->external_id = $user->email;
        $nextUser->interest_type = self::exchange_intent($user->intent);
        // TODO: 決済のアカウントをそれぞれ探して
        // $nextUser->payment_type = getPaymentType($user)
    }

    /**
     * Ruby に登録している intent を interest_type に変更
     * @param integer $intent 二進数の値
     * @return integer 10進数に変換して
     */
    private function exchange_intent(int $intent): int
    {
        if ($intent == null) {
            return 1;
        }

        $binaryString = "{$intent}";
        return bindec($binaryString);
    }

    private function getPaymentType(OldUser $user)
    {
        // AmazonPaySer

    }
}
