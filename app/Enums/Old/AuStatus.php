<?php

declare(strict_types=1);

namespace App\Enums\Old;

use BenSampo\Enum\Enum;

final class AuStatus extends Enum
{
    const INIT = 0; // 初期値
    const REQUESTED = 1; // 要求失敗
    const EXECUTED = 2; // 処理中
    const FINISHED = 3; // 正常終了
    const CANCELED = 4; // 退会済み
    const ERROR = 5;
}
