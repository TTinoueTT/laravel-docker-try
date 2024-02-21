<?php

declare(strict_types=1);

namespace App\Enums\Old;

use BenSampo\Enum\Enum;

final class RakutenPayStatus extends Enum
{
    const ACTIVE = 0;
    const CANCELED = 1;
    const SUSPEND = 2;
}
