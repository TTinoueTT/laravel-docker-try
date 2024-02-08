<?php

namespace App\Models\Old;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OldHistory extends BaseModel
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
    protected $table = 'histories';

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
        self::USER_ID => 0,
        self::ITEMCD => '',
        self::PRICE => 0,
        self::PROFILE_ID => null,
        self::TARGET_PROFILE_ID => null,
        self::IS_FREE => 0,
        self::PURCHASED => 0,
        self::SUBMENU_PURCHASED => 0,
        self::CAMPAIGN_CODE => '',
        self::IS_FIRST => 0,
        self::COUPON_ID => null,
    ];

    const USER_ID = "user_id";
    const ITEMCD = "itemcd";
    const PRICE = "price";
    const PROFILE_ID = "profile_id";
    const TARGET_PROFILE_ID = "target_profile_id";
    const IS_FREE = "is_free";
    const PURCHASED = "purchased";
    const SUBMENU_PURCHASED = "submenu_purchased";
    const CAMPAIGN_CODE = "campaign_code";
    const IS_FIRST = "is_first";
    const COUPON_ID = "coupon_id";
}
