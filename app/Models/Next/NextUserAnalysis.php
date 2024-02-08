<?php

namespace App\Models\Next;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NextUserAnalysis extends BaseModel
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
    protected $table = 'users_analyses';

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
    const REF_FROM = "ref_from";
    const REF_CATE = "ref_cate";
    const REF_U_ID = "ref_u_id";
    const REF_AD_ID = "ref_ad_id";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::USER_ID => null,
            self::REF_FROM => '',
            self::REF_CATE => '',
            self::REF_U_ID => '',
            self::REF_AD_ID => '',
        ];
    }

    public function profiles()
    {
        // return $this->hasMany(OldProfile::class, OldProfile::USER_ID);
    }
}
