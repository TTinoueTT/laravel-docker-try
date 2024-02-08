<?php

namespace App\Models\Next;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NextProfile extends BaseModel
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
    protected $table = 'profiles';

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
    const STATE = "state";
    const TYPE = "type";
    const FULL_NAME = "full_name";
    const LAST_NAME = "last_name";
    const FIRST_NAME = "first_name";
    const LAST_NAME_KANA = "last_name_kana";
    const FIRST_NAME_KANA = "first_name_kana";
    const BIRTH_PLACE = "birth_place";
    const BIRTHDAY = "birthday";
    const BIRTHTIME = "birthtime";
    const GENDER = "gender";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::USER_ID => null,
            self::STATE => 1,
            self::TYPE => 1,
            self::FULL_NAME => '',
            self::LAST_NAME => '',
            self::FIRST_NAME => '',
            self::LAST_NAME_KANA => '',
            self::FIRST_NAME_KANA => '',
            self::BIRTH_PLACE => '',
            self::BIRTHDAY => '',
            self::BIRTHTIME => '',
            self::GENDER => 1,
        ];
    }

    public function profiles()
    {
        // return $this->hasMany(OldProfile::class, OldProfile::USER_ID);
    }
}
