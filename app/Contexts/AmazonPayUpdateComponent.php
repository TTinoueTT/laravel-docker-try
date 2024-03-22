<?php

namespace App\Contexts;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Amazon\Pay\API\Client;
use App\Enums\Old\AmazonPayStatus;
use App\Services\Payment\AmazonPayCv2Service;
use App\Models\Next\Payment\NextAmazonPayBillingAgreement;
use App\Models\Next\Payment\NextAmazonPayOrderReference;
use App\Models\Next\Payment\AmazonPaySubscription;
use App\Models\Next\Payment\AmazonPayCharge;
use Illuminate\Database\Eloquent\Collection;

use RuntimeException;

class AmazonPayUpdateComponent
{
    private $amazonPayCv2Service;

    public function __construct(AmazonPayCv2Service $amazonPayCv2Service)
    {
        $this->amazonPayCv2Service = $amazonPayCv2Service;
    }

    /**
     * Cv1 から Cv2 のデータに移行
     *
     * @param string $startOfMonthStr
     * @param string $endOfMonthStr
     * @param mixed $idList - 個別指定して移行をする際の id 値
     * @return void
     */
    public function cv1_to_cv2($startOfMonthStr, $endOfMonthStr, $idList): void
    {
        $repeatTime = 50;
        DB::connection('mysql_new_payment')->beginTransaction();

        $client = new Client($this->amazonPayCv2Service->setConfig());
        try {
            if ($idList) {
                foreach ($idList as $id) {
                    $cv1 = NextAmazonPayBillingAgreement::find($id);
                    # users レコードの移行(決済継続データの移行も)
                    Log::info("*************************************************************");
                    Log::info("Start update {$cv1->open_id} ((( id: {$cv1->id} )))");
                    Log::info("↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓");
                    $nextUser = $this->amazonPayCv2Service->updateCv1toCv2($client, $cv1, $startOfMonthStr, $endOfMonthStr);
                    if ($nextUser == null) {
                        Log::info("↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑");
                        Log::info("New user is null, old user ((( id :{$cv1->id} ))) has invalid parameter");
                        Log::info("*************************************************************");
                        continue;
                    }
                }
            } else {
                NextAmazonPayBillingAgreement::where(NextAmazonPayBillingAgreement::BILLING_AGREEMENT_STATE, '!=', AmazonPayStatus::CLOSED)
                    ->orderBy('id', 'asc')
                    ->chunk($repeatTime, function (Collection $cv1s) use ($client, $startOfMonthStr, $endOfMonthStr) {
                        foreach ($cv1s as $cv1) {

                            # users レコードの移行(決済継続データの移行も)
                            Log::info("*************************************************************");
                            Log::info("Start update {$cv1->open_id} ((( id: {$cv1->id} )))");
                            Log::info("↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓");

                            $nextUser = $this->amazonPayCv2Service->updateCv1toCv2($client, $cv1, $startOfMonthStr, $endOfMonthStr);
                            if ($nextUser == null) {
                                Log::info("↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑");
                                Log::info("New user is null, old user ((( id :{$cv1->id} ))) has invalid parameter");
                                Log::info("*************************************************************");
                                continue;
                            }
                        }
                    });
            }

            DB::connection('mysql_new_payment')->commit();
        } catch (\Exception $e) {
            DB::connection('mysql_new_payment')->rollBack();
            //throw $th;
            // エラーメッセージと例外の詳細をログに記録
            Log::error('トランザクション失敗:', ['message' => $e->getMessage(), 'exception' => $e]);

            // 必要に応じてカスタム例外を投げる
            throw new RuntimeException("トランザクション中にエラーが発生しました。", 0, $e);
        }
    }

    /**
     * 更新対象アカウント数、更新対象範囲開始日、更新対象範囲終了日 の情報を持つオブジェクトを返却
     *
     * @param string $startOfMonthStr
     * @param string $endOfMonthStr
     * @return array
     */
    public function sizeInfo($startOfMonthStr, $endOfMonthStr): array
    {
        $size = NextAmazonPayBillingAgreement::where(NextAmazonPayBillingAgreement::UPDATED_AT, '>=', $startOfMonthStr)
            ->where(NextAmazonPayBillingAgreement::UPDATED_AT, '<=', $endOfMonthStr)
            ->where(NextAmazonPayBillingAgreement::BILLING_AGREEMENT_STATE, '!=', AmazonPayStatus::CLOSED)
            ->count();

        return [
            "size"            => $size,
            "startOfMonthStr" => $startOfMonthStr,
            "endOfMonthStr"   => $endOfMonthStr,
        ];
    }
}
