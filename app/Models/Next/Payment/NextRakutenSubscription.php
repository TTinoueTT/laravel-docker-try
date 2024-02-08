<?php

namespace App\Models\Next\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NextRakutenSubscription extends BaseModel
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
    protected $table = 'rakuten_subscriptions';

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
        self::RSA_STATUS => 0,
        self::FROM_RAKUTEN_SERVICE => 0,
        self::ORDER_CONTROL_ID => '',
        self::AUTH_REQUEST_ID => '',
        self::SUBSCRIPTION_ID => '',
        self::PARAMS => '[]',
    ];

    const OPEN_ID = "open_id";
    const RSA_STATUS = "rsa_status";
    const FROM_RAKUTEN_SERVICE = "from_rakuten_service";
    const ORDER_CONTROL_ID = "order_control_id";
    const AUTH_REQUEST_ID = "auth_request_id";
    const SUBSCRIPTION_ID = "subscription_id";
    const PARAMS = "params";
}
