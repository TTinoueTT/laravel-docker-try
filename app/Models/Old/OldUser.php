<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldUser extends Model
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

    const EMAIL = "email";
    const ENCRYPTED_PASSWORD = "encrypted_password";
    const REF_FROM = "ref_from";
    const REF_CATE = "ref_cate";
    const REF_U_ID = "ref_u_id";
    const SIGN_IN_COUNT = "sign_in_count";
    const REF_AD_ID = "ref_ad_id";
    const MIGRATION_CODE = "migration_code";
    const INTENT = "intent";
    const MAIL_ADDRESS = "mail_address";
    const NOTIFICATION = "notification";
    const NOTIFICATION_OPTOUT_AT = "notification_optout_at";
    const NOTIFICATION_OPTIN_AT = "notification_optin_at";

    public function __construct($attributes = [])
    {
        $this->attributes = [
            self::EMAIL => '',
            self::ENCRYPTED_PASSWORD => '',
            self::REF_FROM => '',
            self::REF_CATE => '',
            self::REF_U_ID => '',
            self::REF_AD_ID => '',
            self::MIGRATION_CODE => '',
            self::INTENT => 1,
            self::MAIL_ADDRESS => '',
            self::NOTIFICATION => 1,
            self::NOTIFICATION_OPTOUT_AT => date("Y-m-d H:i:s"),
            self::NOTIFICATION_OPTIN_AT => date("Y-m-d H:i:s"),
        ];
    }
}
