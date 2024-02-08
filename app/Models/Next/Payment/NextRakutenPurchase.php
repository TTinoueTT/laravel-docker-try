<?php

namespace App\Models\Next\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NextRakutenPurchase extends BaseModel
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
    protected $table = 'rakuten_purchases';

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
        self::OPEN_ID => '',
        self::ORDER_CART_ID => '',
        self::ORDER_CONTROL_ID => '',
        self::PRICE => 0,
        self::RSA_ITEM_ID => '',
        self::STATE => 0,
        self::PARAMS => '[]',
    ];

    const OPEN_ID = "open_id";
    const ORDER_CART_ID = "order_cart_id";
    const ORDER_CONTROL_ID = "order_control_id";
    const PRICE = "price";
    const RSA_ITEM_ID = "rsa_item_id";
    const STATE = "state";
    const PARAMS = "params";
}
