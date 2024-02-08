<?php

namespace App\Models\Old;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldAuSubscription extends BaseModel
{
    use HasFactory;

    /**
     * このモデルが使用するデータベース接続
     *
     * @var string
     */
    protected $connection = 'mysql_old';

    /**
     * モデルに関連付けるテーブル、tableプロパティを定義してモデルのテーブル名を自分で指定できる
     * 別の名前を明示的に指定しない限り、クラスの複数形の「スネークケース」をテーブル名として使用
     * @var string
     */
    protected $table = 'au_subscriptions';

    /**
     * モデルにタイムスタンプを付けるか
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * モデルの属性のデフォルト値
     *
     * @var array
     */
    protected $attributes = [
        self::USER_ID => null,
        self::OUR_STATUS => 0,
        self::MANAGE_NO => '',
        self::AMOUNT => 0,
        self::TRANSACTION_ID => '',
        self::CONTINUE_ACCOUNT_ID => '',
        self::RESULT_CODE => '',
        self::PROCESS_DAY => date("Y-m-d H:i:s"),
        self::CANCELLED_AT => date("Y-m-d H:i:s"),
    ];

    const USER_ID = "user_id";
    const OUR_STATUS = "our_status";
    const MANAGE_NO = "manage_no";
    const AMOUNT = "amount";
    const TRANSACTION_ID = "transaction_id";
    const CONTINUE_ACCOUNT_ID = "continue_account_id";
    const RESULT_CODE = "result_code";
    const PROCESS_DAY = "process_day";
    const CANCELLED_AT = "cancelled_at";
}
