<?php

namespace App\Models\Old\Payment;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldSoftbankSubscription extends BaseModel
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
    protected $table = 'softbank_subscriptions';

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
    protected $attributes = [];

    const USER_ID = "user_id";
    const OUR_STATUS = "our_status";
    const REGIST_STATUS = "regist_status";
    const MANAGE_NO = "manage_no";
    const AMOUNT = "amount";
    const CP_NOTE = "cp_note";
    const TRANSACTION_ID = "transaction_id";
    const ORDER_NO = "order_no";
    const PURCHASE_DAY = "purchase_day";
    const RESULT_STATUS = "result_status";
    const STATUS_CODE = "status_code";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::USER_ID => null,
            self::OUR_STATUS => 0,
            self::REGIST_STATUS => 0,
            self::MANAGE_NO => '',
            self::AMOUNT => 0,
            self::CP_NOTE => '',
            self::TRANSACTION_ID => '',
            self::ORDER_NO => '',
            self::PURCHASE_DAY => date("Y-m-d H:i:s"),
            self::RESULT_STATUS => '',
            self::STATUS_CODE => 0,
        ];
    }
}
