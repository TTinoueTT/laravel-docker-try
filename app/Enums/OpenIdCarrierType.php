<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OpenIdCarrierType extends Enum
{
    const UNKNOWN = 0;
    const SOFTBANK = 1;
    const AU = 2;
    const DOCOMO = 3;
}
