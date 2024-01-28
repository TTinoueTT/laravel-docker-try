<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldDocomoPurchase extends Model
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
    protected $attributes = [
        self::USER_ID => null,
        self::history_id => null,
        self::status => 0,
        self::price => 0,
        self::cp_token => '',
        self::cp_order_no => '',
        self::cp_processing_time => '',
        self::cp_param => '',
        self::transaction_type => '',
        self::docomo_token => '',
        self::docomo_order_no => '',
        self::docomo_auth_time => date("Y-m-d H:i:s"),
    ];

    const USER_ID = "user_id";
    const history_id = "history_id";
    const status = "status";
    const price = "price";
    const cp_token = "cp_token";
    const cp_order_no = "cp_order_no";
    const cp_processing_time = "cp_processing_time";
    const cp_param = "cp_param";
    const transaction_type = "transaction_type";
    const docomo_token = "docomo_token";
    const docomo_order_no = "docomo_order_no";
    const docomo_auth_time = "docomo_auth_time";
}
