<?php

namespace App\Services;

use App\Contexts\RandomComponent;
use App\Enums\OpenIdCarrierType;
use App\Enums\PaymentType;
use App\Models\BaseModel;
use App\Models\Old\OldUser;
use App\Models\Next\NextUser;
use App\Services\IMigrateService;
use App\Services\Payment\AmazonPayService;
use App\Services\Payment\AuPaymentService;
use App\Services\Payment\SoftBankPaymentService;
use App\Services\Payment\DocomoPaymentService;
use App\Services\Payment\RakutenPayService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

final class UserService implements IMigrateService
{
    private  $amazonPayService;
    private  $auPaymentService;
    private  $softbankPaymentService;
    private  $docomoPaymentService;
    private  $rakutenPayService;

    public function __construct(
        AmazonPayService $amazonPayService,
        AuPaymentService $auPaymentService,
        SoftBankPaymentService $softbankPaymentService,
        DocomoPaymentService $docomoPaymentService,
        RakutenPayService $rakutenPayService
    ) {
        $this->amazonPayService = $amazonPayService;
        $this->auPaymentService = $auPaymentService;
        $this->softbankPaymentService = $softbankPaymentService;
        $this->docomoPaymentService = $docomoPaymentService;
        $this->rakutenPayService = $rakutenPayService;
    }

    public function migrateOldToNew(BaseModel $oldUser): ?NextUser
    {
        if (!$oldUser instanceof OldUser) {
            throw new \InvalidArgumentException('Expected an instance of OldUser');
        }
        $nextUser = new NextUser();

        $nextUser->interest_type = $this->exchangeIntent($oldUser->intent);
        $nextUser->payment_type = $this->findAndSavePaymentType($oldUser);

        $externalId = $this->setExternalId($nextUser, $oldUser);
        if (empty($externalId)) {
            return null;
        }

        $nextUser->migration_code = $this->generateUniqueMigrationCode();
        $nextUser->mail_address = $oldUser->mail_address;

        $nextUser->notification = $oldUser->notification;
        $nextUser->notification_optout_at = isset($oldUser->notification_optout_at) ? $oldUser->notification_optout_at : Carbon::create(1000, 1, 1, 0, 0, 0);
        $nextUser->notification_optin_at = isset($oldUser->notification_optin_at) ? $oldUser->notification_optin_at : Carbon::create(1000, 1, 1, 0, 0, 0);
        $nextUser->created_at = $oldUser->created_at;
        $nextUser->updated_at = $oldUser->updated_at;

        // $nextUser->external_id を payment_type の決済タイプに応じて検索して、open_id を検索 同じものがあれば、return null;
        // $isExists = $this->existsDuplicatePaymentSubscription($nextUser);
        $isExists = $this->existsDuplicateUserByExternalId($nextUser->external_id);

        if ($isExists) {
            return null;
        }

        if ($nextUser->save()) {
            Log::info("User saved successfully.", ['user_id' => $nextUser->id]);
        } else {
            Log::error("Failed to save the user.");
        }

        Log::info("User migrate process is finish !!!");

        return $nextUser;
    }

    /**
     * 各決済キャリアの継続課金データをもとにセット
     * @param NextUser $nextUser
     * @param OldUser $oldUser
     * @return string
     */
    private function setExternalId(NextUser $nextUser, OldUser $oldUser): string
    {
        if (in_array($nextUser->payment_type, OpenIdCarrierType::getValues())) {
            $openIdProfile = $oldUser->openIdProfiles()->get()->first();
            if (is_null($openIdProfile)) {
                Log::error("openIdProfile is null");
            }
            $nextUser->external_id = $openIdProfile->claimed_id;
        } elseif ($nextUser->payment_type == PaymentType::RAKUTEN) {
            $rakutenSubscription = $oldUser->rakutenSubscriptions()->get()->last();
            $nextUser->external_id = $rakutenSubscription->open_id;
        } else {
            $nextUser->external_id = $oldUser->email;
        }

        return $nextUser->external_id;
    }

    /**
     * 新規 users テーブル内を検証して、一意な文字列(12文字)を生成
     * @return string
     */
    private function generateUniqueMigrationCode(): string
    {
        do {
            // 一意の migration_code を生成
            $uniqueMigrationCode = RandomComponent::Generate(12);
            // 生成した migration_code が NextUser モデルのテーブルに存在するか確認
            $exists = NextUser::where('migration_code', $uniqueMigrationCode)->exists();
        } while ($exists); // 生成した migration_code が既に存在する場合は再度生成

        // 生成した一意の migration_code を nextUser オブジェクトに設定
        return $uniqueMigrationCode;
    }

    /**
     * Ruby に登録している intent を interest_type に変更
     * @param integer $intent 二進数の値
     * @return integer 10進数に変換して
     */
    private function exchangeIntent(?int $intent): int
    {
        if ($intent == null) {
            return 1;
        }

        switch ($intent) {
            case 101000:
                # ["仕事"]
                return 1;

            case 110000:
                # "結婚"
                return 2;

            case 100100:
                # "片想い"
                return 3;

            case 100010:
                # "不倫"
                return 4;

            case 100001:
                # "復縁"
                return 5;

            default:
                return 1;
        }
    }

    /**
     * OldUser に payment_type が存在しないため、old の 決済情報を全部照合して、
     * データ移行を行い、payment_type を取得し、NextUser の更新を行う
     * @param OldUser $oldUser
     * @param NextUser $NextUser
     * @return int
     */
    private function findAndSavePaymentType(OldUser $oldUser): int
    {
        Log::info("============================================================");
        Log::info("************ payment subscription data process ************");
        Log::info("============================================================");
        $paymentType = PaymentType::UNKNOWN;
        // SOFTBANK
        if ($paymentType == PaymentType::UNKNOWN) {
            $paymentType = $this->softbankPaymentService->migrateOldToNew($oldUser);
        }
        // AU
        if ($paymentType == PaymentType::UNKNOWN) {
            $paymentType = $this->auPaymentService->migrateOldToNew($oldUser);
        }
        // DOCOMO
        if ($paymentType == PaymentType::UNKNOWN) {
            $paymentType = $this->docomoPaymentService->migrateOldToNew($oldUser);
        }
        // RAKUTEN
        if ($paymentType == PaymentType::UNKNOWN) {
            $paymentType = $this->rakutenPayService->migrateOldToNew($oldUser);
        }
        // AMAZON
        if ($paymentType == PaymentType::UNKNOWN) {
            $paymentType = $this->amazonPayService->migrateOldToNew($oldUser);
        }

        return $paymentType;
    }

    private function existsDuplicatePaymentSubscription(NextUser $nextUser): bool
    {
        $isExists = false;
        switch ($nextUser->payment_type) {
            case PaymentType::SOFTBANK:
                $isExists = $this->softbankPaymentService->checkDuplicateOpenId($nextUser->external_id);
                break;
            case PaymentType::AU:
                $isExists = $this->auPaymentService->checkDuplicateOpenId($nextUser->external_id);
                break;
            case PaymentType::DOCOMO:
                $isExists = $this->docomoPaymentService->checkDuplicateOpenId($nextUser->external_id);
                break;
            case PaymentType::RAKUTEN:
                $isExists = $this->rakutenPayService->checkDuplicateOpenId($nextUser->external_id);
                break;
            case PaymentType::AMAZON:
                $isExists = $this->amazonPayService->checkDuplicateOpenId($nextUser->external_id);
                break;

            default:
                # code...
                break;
        }

        return $isExists;
    }

    /**
     * external_id の重複をチェック
     * @param string $externalId
     * @return bool
     */
    private function existsDuplicateUserByExternalId(string $externalId): bool
    {
        $exists = NextUser::where('external_id', $externalId)->exists();
        return $exists;
    }
}
