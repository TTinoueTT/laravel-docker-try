<?php

namespace App\Contexts;

use Illuminate\Support\Carbon;
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

    public function cv1_to_cv2(): void
    {
        $logHeader = "amazon pay update cv1 to cv2 : ";

        $repeatTime = 50;
        DB::connection('mysql_new_payment')->beginTransaction();

        $now = Carbon::now();
        // TODO 検証ように先月の時期に変更している。
        // $startOfMonthStr = $now->firstOfMonth()->startOfDay()->toDateTimeString(); // 今月初めの日時を取得
        // $endOfMonthStr = $now->lastOfMonth()->endOfDay()->toDateTimeString(); // 今月末の日時を取得
        $startOfMonthStr = $now->copy()->subMonth()->firstOfMonth()->startOfDay()->toDateTimeString(); // 先月初めの日時を取得
        $endOfMonthStr = $now->copy()->subMonth()->lastOfMonth()->endOfDay()->toDateTimeString(); // 先月末の日時を取得

        $client = new Client($this->amazonPayCv2Service->setConfig());
        try {
            NextAmazonPayBillingAgreement::where(NextAmazonPayBillingAgreement::BILLING_AGREEMENT_STATE, '!=', AmazonPayStatus::CLOSED)
                ->orderBy('id', 'asc')
                ->chunk($repeatTime, function (Collection $cv1s) use ($client, $startOfMonthStr, $endOfMonthStr) {
                    foreach ($cv1s as $cv1) {

                        // TODO: 練習用に修正
                        if ($cv1->id != config('app.amazon_pay_billing_agreement_id')) {
                            continue;
                        }

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

    public function sizeInfo(): array
    {
        $now = Carbon::now();
        // $startOfMonthStr = $now->firstOfMonth()->startOfDay()->toDateTimeString(); // 今月初めの日時を取得
        // $endOfMonthStr = $now->lastOfMonth()->endOfDay()->toDateTimeString(); // 今月末の日時を取得
        $startOfMonthStr = $now->copy()->subMonth()->firstOfMonth()->startOfDay()->toDateTimeString(); // 先月初めの日時を取得
        $endOfMonthStr = $now->copy()->subMonth()->lastOfMonth()->endOfDay()->toDateTimeString(); // 先月末の日時を取得

        $size = NextAmazonPayBillingAgreement::where(NextAmazonPayBillingAgreement::CREATED_AT, '>=', $startOfMonthStr)
            ->where(NextAmazonPayBillingAgreement::CREATED_AT, '<=', $endOfMonthStr)
            ->where(NextAmazonPayBillingAgreement::BILLING_AGREEMENT_STATE, '!=', AmazonPayStatus::CLOSED)
            ->count();

        // $this->info($size);
        return [
            "size"            => $size,
            "startOfMonthStr" => $startOfMonthStr,
            "endOfMonthStr"   => $endOfMonthStr,
        ];
    }
}
