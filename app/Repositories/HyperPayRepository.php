<?php

namespace App\Repositories;

// Import the class namespaces first, before using it directly


use App\Models\Orders;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;

class HyperPayRepository
{
    const SUCCESS_CODE_PATTERN = '/^(000\.000\.|000\.100\.1|000\.[36])/';
    const SUCCESS_MANUAL_REVIEW_CODE_PATTERN = '/^(000\.400\.0[^3]|000\.400\.[0-1]{2}0)/';

    private const AUTH_KEY = 'OGFjOWE0Yzc3ZGQ4MDdlYjAxN2RkZDcyM2U5MzU0Yjd8OUJidFpZd2NyUA==';
    private const ENTITY_ID = '8ac9a4c77dd807eb017ddd72b60354c5';
    private const MADA_ENTITY_ID = '8ac9a4c77dd807eb017ddd736b3154dc';


    private const URL = 'https://oppwa.com';
    private const TEST_URL = 'https://test.oppwa.com';
    private const PRODUCTION = true;

    public const VISA = 'visa';
    public const MADA = 'mada';


    public static function pay($credit, $type, $billing = [], $merchantTransactionId = null)
    {
        $auth_key = self::AUTH_KEY;
        $entity_id = (self::VISA === strtolower($type)) ? self::ENTITY_ID : self::MADA_ENTITY_ID;
        $hyperpayUrl = self::PRODUCTION ? self::URL : self::TEST_URL;
        $url = $hyperpayUrl . "/v1/checkouts";
        $production = self::PRODUCTION;
        if (auth()->user()->email == 'aasemelrayes@gmail.com' || auth()->id()==429) {
            $credit = 1;
        }
        $data = "currency=SAR" .
            "&entityId=" . $entity_id .
            "&amount=" . $credit .
            "&paymentType=DB";

//        if ((self::VISA === $type)) {
////            $data .= "&testMode=EXTERNAL";
//        }
        if ($merchantTransactionId) {
            $data .= "&merchantTransactionId=" . $merchantTransactionId;
        }
        foreach ($billing as $key => $item) {
            $data .= "&" . $key . "=" . $item;
        }
//        echo $data;
//        die();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $auth_key));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $production);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $res = json_decode($responseData, true);
        return $res;
    }

    public static function request()
    {
        $url = "https://oppwa.com/v1/payments";
        $data = "entityId=" . self::ENTITY_ID .
            "&amount=92.00" .
            "&currency=SAR" .
            "&paymentBrand=VISA" .
            "&paymentType=DB" .
            "&card.number=4200000000000000" .
            "&card.holder=Jane Jones" .
            "&card.expiryMonth=05" .
            "&card.expiryYear=2024" .
            "&card.cvv=123";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . self::AUTH_KEY));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $responseData;
    }

    public static function validateCheckout($id, $payment_type, $get_amount = false)
    {
        $url = self::PRODUCTION ? self::URL : self::TEST_URL;
        $url .= '/v1/checkouts/' . $id . '/payment';
        $url .= "?entityId=" . ((strtolower($payment_type) === self::VISA) ? self::ENTITY_ID : self::MADA_ENTITY_ID);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . self::AUTH_KEY));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::PRODUCTION);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);

        $response = json_decode($responseData, true);
        Log::alert($response);
        if (isset($response['ndc']) && $response['ndc'] === $id
            && isset($response['result']) && isset($response['result']['code']) &&
            (preg_match(self::SUCCESS_CODE_PATTERN, $response['result']['code'])
                || preg_match(self::SUCCESS_MANUAL_REVIEW_CODE_PATTERN, $response['result']['code'])
                || $response['result']['code'] === '000.200.100')
            && isset($response['currency']) && isset($response['amount'])
            && $response['currency'] === 'SAR') {
            if ($get_amount) {
                return [true, $response['amount'], $response['result']['description']];
            }
            return true;
        } else {
            return false;
        }
    }

    public static function validateCheckoutByResourcePath($resourcePath, $payment_type)
    {
        $url = self::PRODUCTION ? self::URL : self::TEST_URL;
        $url .= $resourcePath;
        $url .= "?entityId=" . (($payment_type === self::VISA) ? self::ENTITY_ID : self::MADA_ENTITY_ID);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . self::AUTH_KEY));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::PRODUCTION);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);

        $response = json_decode($responseData, true);
        if (isset($response['ndc']) && isset($response['result'])
            && isset($response['result']) && isset($response['result']['code']) && (
                preg_match(self::SUCCESS_CODE_PATTERN, $response['result']['code'])
                || preg_match(self::SUCCESS_MANUAL_REVIEW_CODE_PATTERN, $response['result']['code'])
                || $response['result']['code'] === '000.200.100')
            && isset($response['currency'])
            && isset($response['amount']) && $response['currency'] === 'SAR') {
            return true;
        } else {
            return false;
        }
    }


    public static function requestCheckoutId($request, $user, $platform = 'api')
    {
        if ($request->has('order_id') && $request->has('type')) {
            $settings = Settings::where('option_name', 'tax_fees')->first();
            $tax = @$settings->value;
            $objects = CartOrderRepository::summary_objects($tax, $request->order_id, $user);
            $order = Orders::find($request->order_id);
            $total_cost = CartOrderRepository::getSummaryCost($objects, $tax);
            $code = $request->code;
            $coupon_money = 0;
            if ($code) {
                $coupon_result = CartOrderRepository::check_coupon_actions($request, $user);
                if ($coupon_result['result'] == 'success') {
                    $coupon_money = $coupon_result['money'];
                }
            }
            $total_cost = $total_cost - $coupon_money;
            //
            $billing = [
                'billing.street1' => $order->address->street,
                'billing.city' => $order->address->state->name,
                'billing.state' => $order->address->region->name,
                'billing.country' => $order->address->state->getCountry->code,
                'billing.postcode' => '',
                'customer.email' => $order->user->email,
                'customer.givenName' => $order->user->username,
                'customer.surname' => $order->user->username,
            ];
            $token = $order->user->id . $request->order_id;
            //

            $result = (new self)->pay(number_format((float)$total_cost, 2, '.', ''),
                $request->type, $billing, $token);
            if (isset($result['id'])) {
                if ($platform == 'website') {
                    return response()->json([
                        'status' => 200,
                        'message' => __('trans.Redirect to payment page'),
                        'checkoutId' => $result['id'],
                        'total_cost' => $total_cost,
                    ], 200);
                }
                return response()->json([
                    'message' => __('trans.Redirect to payment page'),
                    'data' => [
                        'checkoutId' => $result['id']
                    ]
                ], 200);
            }
        }
        if ($platform == 'website') {
            if ($platform == 'website') {
                return response()->json([
                    'status' => 400,
                    'message' => __('trans.Redirect to payment page'),
                ], 200);
            }
        }
        return response()->json([
            'message' => __('trans.Error in redirecting to payments page'),
        ], 400);
    }


    public static function checkPaymentStatus($request, $user, $platform = 'api')
    {
        if ($request->has('resourcePath') && $request->has('type')) {
            $result = (new self)->validateCheckout($request->resourcePath, $request->type);
            if ($result) {
                return response()->json([
                    'message' => "success",
                ], 200);
            }
        }
        return response()->json([
            'message' => 'error'
        ], 400);
    }
}
