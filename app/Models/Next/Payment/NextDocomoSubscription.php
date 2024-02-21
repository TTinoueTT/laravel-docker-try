<?php

namespace App\Models\Next\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Carbon\Carbon;

class NextDocomoSubscription extends BaseModel
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
    protected $table = 'docomo_subscriptions';

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
    const REQUEST_TYPE = "request_type";
    const RSA_STATUS = "rsa_status";
    const DOCOMO_STATUS = "docomo_status";
    const CP_TOKEN = "cp_token";
    const CP_ORDER_NO = "cp_order_no";
    const RSA_ITEM_ID = "rsa_item_id";
    const DOCOMO_SUBSCRIPTION_STATUS = "docomo_subscription_status";
    const DOCOMO_TOKEN = "docomo_token";
    const DOCOMO_ORDER_NO = "docomo_order_no";
    const DOCOMO_AUTH_TIME = "docomo_auth_time";
    const PARAMS = "params";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::OPEN_ID => '',
            self::REQUEST_TYPE => 0,
            self::RSA_STATUS => 0,
            self::DOCOMO_STATUS => 0,
            self::CP_TOKEN => '',
            self::CP_ORDER_NO => '',
            self::RSA_ITEM_ID => '',
            self::DOCOMO_SUBSCRIPTION_STATUS => '',
            self::DOCOMO_TOKEN => '',
            self::DOCOMO_ORDER_NO => '',
            self::DOCOMO_AUTH_TIME => Carbon::create(2000, 1, 1, 0, 0, 0),
            self::PARAMS => '[]',
        ];
    }
}
