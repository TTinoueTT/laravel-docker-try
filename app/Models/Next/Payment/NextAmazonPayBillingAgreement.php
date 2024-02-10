<?php

namespace App\Models\Next\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Models\Old\Payment\OldAmazonPayBillingAgreement;

class NextAmazonPayBillingAgreement extends BaseModel
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
    protected $table = 'amazon_pay_billing_agreements';

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
        self::OPEN_ID => '',
        self::AMAZON_BILLING_AGREEMENT_ID => '',
        self::SELLER_BILLING_AGREEMENT_ID => '',
        self::BILLING_AGREEMENT_STATE => 0,
        self::BILLING_AGREEMENT_REASON_CODE => '',
        self::CANCELLED_AT => date("Y-m-d H:i:s"),
        self::PARAMS => '[]',
    ];

    const OPEN_ID = "open_id";
    const AMAZON_BILLING_AGREEMENT_ID = "amazon_billing_agreement_id";
    const SELLER_BILLING_AGREEMENT_ID = "seller_billing_agreement_id";
    const BILLING_AGREEMENT_STATE = "billing_agreement_state";
    const BILLING_AGREEMENT_REASON_CODE = "billing_agreement_reason_code";
    const CANCELLED_AT = "cancelled_at";
    const PARAMS = "params";

    /**
     * Undocumented function
     *
     * @param OldAmazonPayBillingAgreement $old
     * @param NextAmazonPayBillingAgreement $new
     * @param OldUser $oldUser
     * @return NextAmazonPayBillingAgreement
     */
    public function oldToNew(OldAmazonPayBillingAgreement $old, NextAmazonPayBillingAgreement $new, OldUser $oldUser): NextAmazonPayBillingAgreement
    {
        $new->open_id = $oldUser->email;
        $new->amazon_billing_agreement_id = $old->amazon_billing_agreement_id;
        $new->seller_billing_agreement_id = $old->seller_billing_agreement_id;
        $new->billing_agreement_state = $oldUser->status;
        $new->billing_agreement_reason_code = $oldUser->state_reason;
        $new->cancelled_at = $oldUser->cancelled_at;
        $new->created_at = $oldUser->created_at;
        $new->updated_at = $oldUser->updated_at;

        return $new;
    }
}
