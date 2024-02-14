<?php

namespace App\Models\Next\Payment;

use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Models\Old\Payment\OldAmazonPayOrderReference;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NextAmazonPayOrderReference extends BaseModel
{
    use HasFactory;
    /**
     * このモデルが使用するデータベース接続
     *
     * @var string
     */
    protected $connection = 'mysql_new_payment';

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
    protected $attributes = [];

    const OPEN_ID = "open_id";
    const BILLING_AGREEMENT_ID = "billing_agreement_id";
    const AMAZON_ORDER_REFERENCE_ID = "amazon_order_reference_id";
    const PRICE = "price";
    const ORDER_REFERENCE_STATE = "order_reference_state";
    const ORDER_REFERENCE_REASON_CODE = "order_reference_reason_code";
    const PARAMS = "params";

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes = [
            self::OPEN_ID => '',
            self::BILLING_AGREEMENT_ID => 0,
            self::AMAZON_ORDER_REFERENCE_ID => '',
            self::PRICE => 0,
            self::ORDER_REFERENCE_STATE => 0,
            self::ORDER_REFERENCE_REASON_CODE => '',
            self::PARAMS => '[]',
        ];
    }
}
