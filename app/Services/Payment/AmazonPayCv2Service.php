<?php

namespace App\Services\Payment;

use App\Contexts\RandomComponent;
use Amazon\Pay\API\Client;
use App\Enums\AmazonChargePermissionState;
use App\Models\Next\Payment\AmazonPayCharge;
use App\Models\Next\Payment\AmazonPaySubscription;
use App\Models\Next\Payment\NextAmazonPayOrderReference;
use App\Models\Next\Payment\NextAmazonPayBillingAgreement;
use App\Models\Next\NextUser;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AmazonPayCv2Service
{
    /**
     * 事業者側がもつ、Client(インスタンス)の設定値
     *
     * @return array
     */
    public function setConfig(): array
    {
        $privateKeyPath = Storage::disk('keys')->path('AmazonPay/mkamaki/AmazonPay_' . config('amazon_pay.public_key_id') . '.pem');

        return array(
            'public_key_id' => config('amazon_pay.public_key_id'),
            'private_key'   => $privateKeyPath,
            'region'        => config('amazon_pay.region'),
            "sandbox"       => config('amazon_pay.sandbox')
        );
    }


    /**
     * Cv1 レコードを Cv2 レコードに移行
     *
     * @param Client $client
     * @param NextAmazonPayBillingAgreement $cv1data
     * @param string $startOfMonthStr
     * @param string $endOfMonthStr
     * @return NextUser | null
     */
    public function updateCv1toCv2(Client $client, NextAmazonPayBillingAgreement $cv1data, string $startOfMonthStr, string $endOfMonthStr): ?NextUser
    {
        $chargePermissionResponse = $this->getChargePermissionResponse($client, $cv1data->amazon_billing_agreement_id); // Buyer Id を取得

        # 注文ID(amazon_order_reference_id) を取得して、その値を charge_id 欄の保存時に使用する
        $nextOrder = NextAmazonPayOrderReference::where(NextAmazonPayOrderReference::BILLING_AGREEMENT_ID, '=', $cv1data->id)
            ->where(NextAmazonPayOrderReference::CREATED_AT, '>=', $startOfMonthStr)
            ->where(NextAmazonPayOrderReference::CREATED_AT, '<=', $endOfMonthStr)
            ->first();

        if (!$nextOrder) {
            Log::info("There are no orders this month, or there is no amazon_pay_order_reference matching: {$cv1data->id}");
            return null;
        }

        Log::info("state = " . $chargePermissionResponse["statusDetails"]["state"]);

        $amazonPaySubscription = $this->insertOfUpdateSubscription($cv1data, $chargePermissionResponse, $nextOrder);
        $this->updateOrders($cv1data->open_id);

        // users レコードの更新を行う
        $user = NextUser::where(NextUser::EXTERNAL_ID, $cv1data->open_id)->first();

        if (!$user) {
            Log::info("There are no user matching: {$cv1data->id}");
            return null;
        }

        $user->external_id = $amazonPaySubscription->buyer_id;
        if ($amazonPaySubscription->charge_permission_state == AmazonChargePermissionState::CLOSED) {
            do {
                $random12Str = RandomComponent::Generate(12);
                $user->external_id = "{$user->external_id}__closed__{$random12Str}";
                $exists = NextUser::where(NextUser::EXTERNAL_ID, $user->external_id)->exists();
            } while ($exists);
        }

        if ($user->save()) {
            Log::info("Update user for AmazonPayCv2 saved successfully.", [NextUser::ID => $user->id]);
        } else {
            Log::error("Failed to save user for AmazonPayCv2.");
        }

        dd($user);

        return $user;
    }

    /**
     * AmazonPayCv2 のレコード、amazon_pay_subscriptions のアップサート処理を行う
     *
     * @param NextAmazonPayBillingAgreement $cv1data
     * @param array $chargePermissionResponse
     * @param NextAmazonPayOrderReference $nextOrder
     * @return AmazonPaySubscription
     */
    private function insertOfUpdateSubscription(NextAmazonPayBillingAgreement $cv1data, array $chargePermissionResponse, NextAmazonPayOrderReference $nextOrder): AmazonPaySubscription
    {
        $amazonPaySubscription = AmazonPaySubscription::where(AmazonPaySubscription::BUYER_ID, $chargePermissionResponse["buyer"]["buyerId"])->first();

        if (!$amazonPaySubscription) {
            $amazonPaySubscription = new AmazonPaySubscription();
            $amazonPaySubscription->buyer_id = $chargePermissionResponse["buyer"]["buyerId"];
        }
        $amazonPaySubscription->charge_permission_id = $chargePermissionResponse["chargePermissionId"];
        $amazonPaySubscription->charge_id = $nextOrder->amazon_order_reference_id;
        $amazonPaySubscription->charge_permission_state = AmazonChargePermissionState::generateIntState($chargePermissionResponse["statusDetails"]["state"]);

        $amazonPaySubscription->created_at = $cv1data->created_at;
        $amazonPaySubscription->updated_at = $cv1data->updated_at;
        $amazonPaySubscription->cancelled_at = $cv1data->cancelled_at;

        Log::info("Start save to {$amazonPaySubscription->getTable()}");
        if ($amazonPaySubscription->save()) {
            Log::info("Update AmazonPayCv2 subscription saved successfully.", ['buyer_id' => $amazonPaySubscription->buyer_id]);
        } else {
            Log::error("Failed to save AmazonPayCv2 subscription.");
        }

        return $amazonPaySubscription;
    }

    /**
     * Charge Permission を取得して、その'response'の値を返す
     *
     * @param Client $client SDKのメソッドを使うためのインスタンス
     * @param string    $chargePermissionId Checkout Session が完了状態のときに発行される Charge Permission のID
     * @param array     $headers ヘッダー署名。API呼び出しに追加して、さまざまな動作をシミュレートさせる。たとえば、“x-amz-pay-simulation-code = ”BuyerCanceled”、指定しない時は null
     * @return array    連想配列、取得した Charge Permission のキー"response"内の配列を返す
     */
    private  function getChargePermissionResponse(Client $client, string $chargePermissionId, $headers = null)
    {
        try {
            $chargePermission = $client->getChargePermission($chargePermissionId, $headers = null);
        } catch (\Throwable $th) {
            $message = "Amazon Pay API access failed: " . $th->getMessage();
            throw new Exception($message);
        }
        $chargePermissionResponse = json_decode($chargePermission["response"], true);

        return $chargePermissionResponse;
    }

    /**
     * Undocumented function
     *
     * @param string $openId
     * @return void
     */
    private function updateOrders(string $openId)
    {
        // $orders = NextAmazonPayOrderReference::where(NextAmazonPayOrderReference::OPEN_ID, $openId);
        //TODO:
        // foreach ($orders as $order) {
        //     $charge = new AmazonPayCharge();
        //     $charge->
        // }
    }
}
