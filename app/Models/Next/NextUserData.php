<?php

namespace App\Models\Next;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NextUserData extends BaseModel
{
    use HasFactory;
    /**
     * このモデルが使用するデータベース接続
     *
     * @var string
     */
    protected $connection = 'mysql_new';

    /**
     * モデルに関連付けるテーブル、tableプロパティを定義してモデルのテーブル名を自分で指定できる
     * 別の名前を明示的に指定しない限り、クラスの複数形の「スネークケース」をテーブル名として使用
     * @var string
     */
    protected $table = 'users_datas';

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
    protected $attributes = []; // デフォルト値を空の配列で初期化

    const USER_ID = "user_id";
    const CAMPAIGN_VALUES = "campaign_values";
    const RESERVATION_ITEMS = "reservation_items";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::USER_ID => null,
            self::CAMPAIGN_VALUES => '[]',
            self::RESERVATION_ITEMS => '[]',
        ];
    }

    public function profiles()
    {
        // return $this->hasMany(OldProfile::class, OldProfile::USER_ID);
    }
}
