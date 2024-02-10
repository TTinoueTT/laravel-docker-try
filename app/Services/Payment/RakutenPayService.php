<?php

namespace App\Services\Payment;

use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Services\IMigrateService;

final class RakutenPayService implements IMigrateService
{
    public function migrateOldToNew(BaseModel $user)
    {
        if (!$user instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }

        // TODO: RakutenPay に関するレコードを取得して新規レコードに追加
    }
}
