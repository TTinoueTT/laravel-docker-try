<?php

declare(strict_types=1);

namespace App\Enums\Old;

use BenSampo\Enum\Enum;

final class AmazonPayStatus extends Enum
{
    const DRAFT = 0; // OrderReferenceが生成された初期状態
    const OPEN = 1; // オーソリができる唯一の状態
    const SUSPENDED = 2; // オーソリ処理にて支払い方法に問題があった状態
    const CANCELED = 3; // キャンセルが完了した状態
    const CLOSED = 4; // 明示的にclose処理したか180日を超えた状態、auto_payの場合は初期状態がclosedらしい、auto_payの場合はまだ曖昧
}
