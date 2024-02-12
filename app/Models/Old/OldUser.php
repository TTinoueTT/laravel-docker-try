<?php

namespace App\Models\Old;

use App\Models\BaseModel;

use App\Models\Old\Payment\OldAmazonPayBillingAgreement;
use App\Models\Old\Payment\OldAmazonPayOrderReference;
use App\Models\Old\Payment\OldAuPurchase;
use App\Models\Old\Payment\OldAuSubscription;
use App\Models\Old\Payment\OldDocomoPurchase;
use App\Models\Old\Payment\OldDocomoSubscription;
use App\Models\Old\Payment\OldDocomoSuid;
use App\Models\Old\Payment\OldOpenIdProfile;
use App\Models\Old\Payment\OldRakutenPurchase;
use App\Models\Old\Payment\OldRakutenSubscription;
use App\Models\Old\Payment\OldSoftbankPurchase;
use App\Models\Old\Payment\OldSoftbankSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldUser extends BaseModel
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

    public function profiles()
    {
        return $this->hasMany(OldProfile::class, OldProfile::USER_ID);
    }

    public function targetProfiles()
    {
        return $this->hasMany(OldTargetProfile::class, OldTargetProfile::USER_ID);
    }

    public function histories()
    {
        return $this->hasMany(OldHistory::class, OldHistory::USER_ID);
    }

    public function users_data()
    {
        return $this->hasOne(OldUserData::class, OldUserData::USER_ID);
    }

    /*
    * ここから決済系のリレーション
    */
    public function softbankSubscriptions()
    {
        return $this->hasMany(OldSoftbankSubscription::class, OldSoftbankSubscription::USER_ID);
    }

    public function softbankPurchases()
    {
        return $this->hasMany(OldSoftbankPurchase::class, OldSoftbankPurchase::USER_ID);
    }

    public function auSubscriptions()
    {
        return $this->hasMany(OldAuSubscription::class, OldAuSubscription::USER_ID);
    }

    public function auPurchases()
    {
        return $this->hasMany(OldAuPurchase::class, OldAuPurchase::USER_ID);
    }

    public function docomoSubscriptions()
    {
        return $this->hasMany(OldDocomoSubscription::class, OldDocomoSubscription::USER_ID);
    }

    public function docomoPurchases()
    {
        return $this->hasMany(OldDocomoPurchase::class, OldDocomoPurchase::USER_ID);
    }

    public function docomoSuids()
    {
        return $this->hasOne(OldDocomoSuid::class, OldDocomoSuid::USER_ID);
    }

    public function openIdProfiles()
    {
        return $this->hasOne(OldOpenIdProfile::class, OldOpenIdProfile::USER_ID);
    }

    public function rakutenSubscriptions()
    {
        return $this->hasMany(OldRakutenSubscription::class, OldRakutenSubscription::USER_ID);
    }

    public function rakutenPurchases()
    {
        return $this->hasMany(OldRakutenPurchase::class, OldRakutenSubscription::USER_ID);
    }

    public function amazonPayBillingAgreements()
    {
        return $this->hasMany(OldAmazonPayBillingAgreement::class, OldAmazonPayBillingAgreement::USER_ID);
    }

    public function amazonPayOrderReferences()
    {
        return $this->hasMany(OldAmazonPayOrderReference::class, OldAmazonPayOrderReference::USER_ID);
    }
}
