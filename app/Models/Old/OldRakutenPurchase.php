<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldRakutenPurchase extends Model
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
        self::USER_ID => null,
        self::history_id => null,
        self::order_cart_id => null,
        self::order_control_id => null,
        self::price => 0,
        self::itemcd => '',
    ];

    const USER_ID = "user_id";
    const history_id = "history_id";
    const order_cart_id = "order_cart_id";
    const order_control_id = "order_control_id";
    const price = "price";
    const itemcd = "itemcd";
}
