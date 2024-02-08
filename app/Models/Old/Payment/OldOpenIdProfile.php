<?php

namespace App\Models\Old\Payment;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldOpenIdProfile extends BaseModel
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
    protected $table = 'open_id_profiles';

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
        self::CLAIMED_ID => '',
        self::CARRIER_TYPE => 0,
    ];

    const USER_ID = "user_id";
    const CLAIMED_ID = "claimed_id";
    const CARRIER_TYPE = "carrier_type";
}
