<?php

namespace App\Models\Old;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldRakutenSubscription extends BaseModel
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
        self::USER_ID => null,
        self::OPEN_ID => '',
        self::STATUS => 0,
        self::SERVICE_ID => 0,
        self::ORDER_CONTROL_ID => '',
        self::AUTH_REQUEST_ID => '',
        self::SUBSCRIPTION_ID => '',
        self::SUBSCRIPTION_DATE => date("Y-m-d H:i:s"),

    ];

    const USER_ID = "user_id";
    const OPEN_ID = "open_id";
    const STATUS = "status";
    const SERVICE_ID = "service_id";
    const ORDER_CONTROL_ID = "order_control_id";
    const AUTH_REQUEST_ID = "auth_request_id";
    const SUBSCRIPTION_ID = "subscription_id";
    const SUBSCRIPTION_DATE = "subscription_date";
}
