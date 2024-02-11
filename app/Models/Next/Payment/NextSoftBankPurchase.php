<?php

namespace App\Models\Next\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NextSoftBankPurchase extends BaseModel
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
    protected $table = 'softbank_purchases';

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
    const RSA_STATUS = "rsa_status";
    const RSA_ITEM_ID = "rsa_item_id";
    const PRICE = "price";
    const TRANSACTION_ID = "transaction_id";
    const ORDER_NO = "order_no";
    const RESULT_STATUS = "result_status";
    const STATUS_CODE = "status_code";
    const PARAMS = "params";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::OPEN_ID => '',
            self::RSA_STATUS => 0,
            self::RSA_ITEM_ID => '',
            self::PRICE => 0,
            self::TRANSACTION_ID => '',
            self::ORDER_NO => '',
            self::RESULT_STATUS => '',
            self::STATUS_CODE => '',
            self::PARAMS => '[]',
        ];
    }
}
