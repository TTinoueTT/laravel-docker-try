<?php

namespace App\Models\Next;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NextBookmark extends BaseModel
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
    protected $table = 'bookmarks';

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
    const ITEMCD = "itemcd";
    const CONTENT_KEY = "content_key";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::USER_ID => null,
            self::ITEMCD => '',
            self::CONTENT_KEY => '',
        ];
    }

    public function profiles()
    {
        // return $this->hasMany(OldProfile::class, OldProfile::USER_ID);
    }
}
