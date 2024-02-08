<?php

namespace App\Models\Old;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldUserData extends BaseModel
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
    protected $table = 'users_data';

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
        self::CAMPAIGN_VALUES => '[]',
        self::RESERVATION => '[]',
    ];

    const USER_ID = "user_id";
    const CAMPAIGN_VALUES = "campaign_values";
    const RESERVATION = "reservation";
}
