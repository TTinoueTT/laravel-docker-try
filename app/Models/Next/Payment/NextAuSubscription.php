<?php

namespace App\Models\Next\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NextAuSubscription extends BaseModel
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
    protected $table = 'au_subscriptions';

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
        self::RSA_ITEM_ID => '',
        self::PRICE => 0,
        self::TRANSACTION_ID => '',
        self::CONTINUE_ACCOUNT_ID => '',
        self::RESULT_CODE => '',
        self::PARAMS => '[]',
    ];

    const OPEN_ID = "open_id";
    const RSA_STATUS = "rsa_status";
    const RSA_ITEM_ID = "rsa_item_id";
    const PRICE = "price";
    const TRANSACTION_ID = "transaction_id";
    const CONTINUE_ACCOUNT_ID = "continue_account_id";
    const RESULT_CODE = "result_code";
    const PARAMS = "params";
}
