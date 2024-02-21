<?php

declare(strict_types=1);

namespace App\Enums\Old;

use BenSampo\Enum\Enum;

final class SoftbankStatus extends Enum
{
    const INIT = 0; // 初期値
    const START = 1; // 要求開始
    const EXECUTED = 2; // 処理中
    const FINISHED = 3; // 正常終了
    const CANCELED = 4; // 退会済み
    const ERROR = 5;
}
