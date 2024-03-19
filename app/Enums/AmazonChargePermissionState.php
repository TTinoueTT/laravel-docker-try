<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AmazonChargePermissionState extends Enum
{
    const CHARGEABLE = 0;
    const NON_CHARGEABLE = 1;
    const CLOSED = 2;

    public static function generateIntState($state): Int
    {
        switch ($state) {
            case "Chargeable":
                return self::CHARGEABLE;
            case "Non-Chargeable":
                return self::NON_CHARGEABLE;
            case "Closed":
                return self::CLOSED;
            default:
                return 9; // 未知のstate
        }
    }
}
