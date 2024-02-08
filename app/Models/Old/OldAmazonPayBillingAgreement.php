<?php

namespace App\Models\Old;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldAmazonPayBillingAgreement extends BaseModel
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
    protected $table = 'amazon_pay_billing_agreements';

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
        self::AMAZON_BILLING_AGREEMENT_ID => '',
        self::SELLER_BILLING_AGREEMENT_ID => '',
        self::STATUS => null,
        self::STATE_UPDATE_TIME => date("Y-m-d H:i:s"),
        self::STATE_REASON => '',
        self::CANCELLED_AT => date("Y-m-d H:i:s"),
    ];

    const USER_ID = "user_id";
    const AMAZON_BILLING_AGREEMENT_ID = "amazon_billing_agreement_id";
    const SELLER_BILLING_AGREEMENT_ID = "seller_billing_agreement_id";
    const STATUS = "status";
    const STATE_UPDATE_TIME = "state_update_time";
    const STATE_REASON = "state_reason";
    const CANCELLED_AT = "cancelled_at";
}
