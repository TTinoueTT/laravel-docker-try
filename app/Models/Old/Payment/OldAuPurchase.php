<?php

namespace App\Models\Old\Payment;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldAuPurchase extends BaseModel
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
    protected $table = 'au_purchases';

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
    const HISTORY_ID = "history_id";
    const OUR_STATUS = "our_status";
    const MANAGE_NO = "manage_no";
    const AMOUNT = "amount";
    const TRANSACTION_ID = "transaction_id";
    const PAYMTD = "paymtd";
    const PAY_INFO_NO = "pay_info_no";
    const PROCESS_DAY = "process_day";
    const RESULT_CODE = "result_code";

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes = [
            self::USER_ID => null,
            self::HISTORY_ID => null,
            self::OUR_STATUS => 0,
            self::MANAGE_NO => '',
            self::AMOUNT => 0,
            self::TRANSACTION_ID => '',
            self::PAYMTD => '',
            self::PAY_INFO_NO => '',
            self::PROCESS_DAY => date("Y-m-d H:i:s"),
            self::RESULT_CODE => '',
        ];
    }
}
