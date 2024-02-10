<?php

namespace App\Models\Next;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NextUser extends BaseModel
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
    protected $table = 'users';

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

    const EXTERNAL_ID = "external_id";
    const INTEREST_TYPE = "interest_type";
    const PAYMENT_TYPE = "payment_type";
    const PREFER_PROFILE_ID = "prefer_profile_id";
    const PREFER_TARGET_PROFILE_ID = "prefer_target_profile_id";
    const MIGRATION_CODE = "migration_code";
    const MAIL_ADDRESS = "mail_address";
    const NOTIFICATION = "notification";
    const NOTIFICATION_OPTOUT_AT = "notification_optout_at";
    const NOTIFICATION_OPTIN_AT = "notification_optin_at";

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes = [
            self::EXTERNAL_ID => '',
            self::INTEREST_TYPE => 1,
            self::PAYMENT_TYPE => -1,
            self::PREFER_PROFILE_ID => -1,
            self::PREFER_TARGET_PROFILE_ID => -1,
            self::MIGRATION_CODE => '',
            self::MAIL_ADDRESS => '',
            self::NOTIFICATION => 1,
            self::NOTIFICATION_OPTOUT_AT => date("Y-m-d H:i:s"),
            self::NOTIFICATION_OPTIN_AT => date("Y-m-d H:i:s"),
        ];
    }

    /**
     * hasMany
     */

    public function profiles()
    {
        return $this->hasMany(NextProfile::class, NextProfile::USER_ID)->where(NextProfile::TYPE, 1);
    }

    public function targetProfiles()
    {
        return $this->hasMany(NextProfile::class, NextProfile::USER_ID)->where(NextProfile::TYPE, 2);
    }

    public function bookmarks()
    {
        return $this->hasMany(NextBookmark::class, NextBookmark::USER_ID);
    }

    public function histories()
    {
        return $this->hasMany(NextHistory::class, NextHistory::USER_ID);
    }

    public function unsubscribe_reasons()
    {
        return $this->hasMany(NextUnsubscribeReason::class, NextUnsubscribeReason::USER_ID);
    }


    /**
     * hasOne
     */

    public function users_analyses()
    {
        // user と users_analyses のレコードは 1対１ の関係
        return $this->hasOne(NextUserAnalysis::class, NextUserAnalysis::USER_ID);
    }

    public function users_datas()
    {
        return $this->hasOne(NextUserData::class, NextUserData::USER_ID);
    }
}
