<?php

declare(strict_types=1);

namespace App\Enums\Old;

use BenSampo\Enum\Enum;

final class DocomoStatus extends Enum
{
    const INIT = 0; // レコード追加
    const DETAILS_SENT = 1; // 処理中
    const AUTHENTICATED = 2; // 認証完了
    const FINISHED = 3; // 正常終了
    const CANCELED = 4; // 処理のキャンセル
    const UNDONE = 5; //不明
    const USER_CANCELLED = 6; //退会ステータス
}
