<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Local()
 * @method static static Development()
 * @method static static Production()
 */
final class LocationType extends Enum
{
    const LOCAL = "LOCAL";
    const DEVELOPMENT = "DEVELOPMENT";
    const PRODUCTION = "PRODUCTION";
}
