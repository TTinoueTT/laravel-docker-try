<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldTargetProfile extends Model
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
    protected $table = 'target_profiles';

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
        self::FULL_NAME => "",
        self::BIRTHDAY => "",
        self::IS_HIDDEN => 0,
    ];

    const USER_ID = "user_id";
    const FULL_NAME = "full_name";
    const BIRTHDAY = "birthday";
    const IS_HIDDEN = "is_hidden";
}
