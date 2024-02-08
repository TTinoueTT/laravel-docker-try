<?php

namespace App\Models\Old\Payment;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldAmazonPayOrderReference extends BaseModel
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
    protected $table = 'amazon_pay_order_references';

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
        self::BILLING_AGREEMENT_ID => null,
        self::HISTORY_ID => null,
        self::AMAZON_ORDER_REFERENCE_ID => '',
        self::ORDER_AMOUNT => 0,
        self::STATUS => 0,
        self::STATE_UPDATE_TIME => date("Y-m-d H:i:s"),
        self::STATE_REASON => '',
    ];

    const USER_ID = "user_id";
    const BILLING_AGREEMENT_ID = "billing_agreement_id";
    const HISTORY_ID = "history_id";
    const AMAZON_ORDER_REFERENCE_ID = "amazon_order_reference_id";
    const ORDER_AMOUNT = "order_amount";
    const STATUS = "status";
    const STATE_UPDATE_TIME = "state_update_time";
    const STATE_REASON = "state_reason";
}
