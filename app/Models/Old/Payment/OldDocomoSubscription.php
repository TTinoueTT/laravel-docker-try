<?php

namespace App\Models\Old\Payment;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldDocomoSubscription extends BaseModel
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

    const USER_ID = "user_id";
    const REQUEST_TYPE = "request_type";
    const STATUS = "status";
    const DOCOMO_STATUS = "docomo_status";
    const CP_TOKEN = "cp_token";
    const CP_ORDER_NO = "cp_order_no";

    const CP_PROCESSING_TIME = "cp_processing_time";
    const CP_PARAM = "cp_param";
    const TRANSACTION_TYPE = "transaction_type";
    const DOCOMO_TOKEN = "docomo_token";
    const DOCOMO_ORDER_NO = "docomo_order_no";
    const DOCOMO_AUTH_TIME = "docomo_auth_time";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::USER_ID => null,
            self::REQUEST_TYPE => 0,
            self::STATUS => 0,
            self::DOCOMO_STATUS => 199,
            self::CP_TOKEN => '',
            self::CP_ORDER_NO => '',
            self::CP_PROCESSING_TIME => date("Y-m-d H:i:s"),
            self::CP_PARAM => '',
            self::TRANSACTION_TYPE => '',
            self::DOCOMO_TOKEN => '',
            self::DOCOMO_ORDER_NO => '',
            self::DOCOMO_AUTH_TIME => date("Y-m-d H:i:s"),
        ];
    }
}
