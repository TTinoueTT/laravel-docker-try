<?php

namespace App\Models\Next\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Models\Old\Payment\OldAmazonPayBillingAgreement;
use Carbon\Carbon;

class AmazonPaySubscription extends BaseModel
{
    use HasFactory;
    /**
     * このモデルが使用するデータベース接続
     *
     * @var string
     */
    protected $connection = 'mysql_new_payment';

    /**
     * モデルに関連付けるテーブル、tableプロパティを定義してモデルのテーブル名を自分で指定できる
     * 別の名前を明示的に指定しない限り、クラスの複数形の「スネークケース」をテーブル名として使用
     * @var string
     */
    protected $table = 'amazon_pay_subscriptions';

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

    const BUYER_ID = "buyer_id";
    const CHARGE_PERMISSION_ID = "charge_permission_id";
    const CHARGE_ID = "charge_id";
    const CHARGE_PERMISSION_STATE = "charge_permission_state";
    const CANCELLED_AT = "cancelled_at";
    const PARAMS = "params";

    /**
     * Undocumented function
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes); // BaseModelのコンストラクタを呼び出す場合

        $this->attributes = [
            self::BUYER_ID => '',
            self::CHARGE_PERMISSION_ID => '',
            self::CHARGE_ID => '',
            self::CHARGE_PERMISSION_STATE => 0,
            self::CANCELLED_AT => Carbon::create(2000, 1, 1, 0, 0, 0),
            self::PARAMS => '[]',
        ];
    }

    // /**
    //  * Undocumented function
    //  *
    //  * @param OldAmazonPayBillingAgreement $old
    //  * @param NextAmazonPayBillingAgreement $new
    //  * @param OldUser $oldUser
    //  * @return NextAmazonPayBillingAgreement
    //  */
    // public function oldToNew(OldAmazonPayBillingAgreement $old, NextAmazonPayBillingAgreement $new, OldUser $oldUser): NextAmazonPayBillingAgreement
    // {
    //     $new->open_id = $oldUser->email;
    //     $new->amazon_billing_agreement_id = $old->amazon_billing_agreement_id;
    //     $new->seller_billing_agreement_id = $old->seller_billing_agreement_id;
    //     $new->billing_agreement_state = $oldUser->status;
    //     $new->billing_agreement_reason_code = $oldUser->state_reason;
    //     $new->cancelled_at = $oldUser->cancelled_at;
    //     $new->created_at = $oldUser->created_at;
    //     $new->updated_at = $oldUser->updated_at;

    //     return $new;
    // }
}
