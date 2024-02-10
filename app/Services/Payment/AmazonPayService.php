<?php

namespace App\Services\Payment;

use App\Models\BaseModel;
use App\Models\Next\Payment\NextAmazonPayBillingAgreement;
use App\Models\Old\OldUser;
use App\Models\Old\Payment\OldAmazonPayBillingAgreement;
use App\Models\Old\Payment\OldAmazonPayOrderReference;
use App\Services\IMigrateService;

final class AmazonPayService implements IMigrateService
{
    private $nextAmazonPayBillingAgreement;

    function __construct(NextAmazonPayBillingAgreement $nextAmazonPayBillingAgreement)
    {
        $this->nextAmazonPayBillingAgreement = $nextAmazonPayBillingAgreement;
    }
    public function migrateOldToNew(BaseModel $user)
    {
        if (!$user instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        // TODO: AmazonPay に関するレコードを取得して新規レコードに追加
        $billingAgreement = OldAmazonPayBillingAgreement::where(OldAmazonPayBillingAgreement::USER_ID, $user->id)->first();
        if (!$billingAgreement) {
            $this->nextAmazonPayBillingAgreement->oldToNew($billingAgreement, new NextAmazonPayBillingAgreement(), $user);
        }

        $orderReferences = OldAmazonPayOrderReference::where(OldAmazonPayOrderReference::USER_ID, $user->id);
        foreach ($orderReferences as $order) {
            # code...
        }
    }
}
