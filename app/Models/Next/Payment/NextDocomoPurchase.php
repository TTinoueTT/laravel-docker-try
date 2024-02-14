<?php

namespace App\Models\Next\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Carbon\Carbon;

class NextDocomoPurchase extends BaseModel
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
    protected $table = 'docomo_purchases';

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
    const PRICE = "price";
    const CP_TOKEN = "cp_token";
    const CP_ORDER_NO = "cp_order_no";
    const RSA_ITEM_ID = "rsa_item_id";
    const DOCOMO_PURCHASE_STATUS = "docomo_purchase_status";
    const DOCOMO_TOKEN = "docomo_token";
    const DOCOMO_AUTH_TIME = "docomo_auth_time";
    const PARAMS = "params";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::OPEN_ID => '',
            self::RSA_STATUS => 0,
            self::PRICE => 0,
            self::CP_TOKEN => '',
            self::CP_ORDER_NO => '',
            self::RSA_ITEM_ID => '',
            self::DOCOMO_PURCHASE_STATUS => '',
            self::DOCOMO_TOKEN => '',
            self::DOCOMO_AUTH_TIME => Carbon::create(2000, 1, 1, 0, 0, 0),
            self::PARAMS => '[]',
        ];
    }
}
