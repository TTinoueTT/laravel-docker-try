<?php

namespace App\Models\Next\Payment;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AmazonPayCharge extends BaseModel
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
    protected $table = 'amazon_pay_charges';

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

    const BUYER_ID = "buyer_id";
    const CHARGE_PERMISSION_ID = "charge_permission_id";
    const CHARGE_ID = "charge_id";
    const EMAIL = "email";
    const PRICE = "price";
    const CHARGE_PERMISSION_STATE = "charge_permission_state";
    const CHARGE_PERMISSION_STATE_REASONS = "charge_permission_state_reasons";
    const CHARGE_STATE = "charge_state";
    const CHARGE_STATE_REASON_CODE = "charge_state_reason_code";
    const MERCHANT_REFERENCE_ID = "merchant_reference_id";
    const REFUND_ID = "refund_id";
    const PARAMS = "params";

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes = [
            self::BUYER_ID => '',
            self::CHARGE_PERMISSION_ID => '',
            self::CHARGE_ID => '',
            self::EMAIL => '',
            self::PRICE => 0,
            self::CHARGE_PERMISSION_STATE => '',
            self::CHARGE_PERMISSION_STATE_REASONS => '',
            self::CHARGE_STATE => '',
            self::CHARGE_STATE_REASON_CODE => '',
            self::MERCHANT_REFERENCE_ID => '',
            self::REFUND_ID => '',
            self::PARAMS => '[]',
        ];
    }
}
