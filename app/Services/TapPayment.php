<?php

namespace App\Services;

use App\Models\PaymentSettings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;


class TapPayment
{
    private $tap_secret_key;
    private $tap_public_key;
    private $tap_lang_code;
    private $verify_route_name;

    public function __construct()
    {
        $this->currency = 'SAR';
        $this->tap_secret_key = PaymentSettings::find(5);
        $this->tap_public_key = PaymentSettings::find(6);
        $this->tap_lang_code = App::getLocale();
        $this->verify_route_name = url('/verify-checkout');
    }

    public function pay($amount = null, $user_id = null, $username = null, $email = null, $phone = null, $order_id = null, $platform = 'api', $complete_order = false)
    {
        $unique_id = uniqid();
        $response = Http::withHeaders([
            "authorization" => "Bearer " . $this->tap_secret_key,
            "content-type" => "application/json",
            'lang_code' => $this->tap_lang_code
        ])->post('https://api.tap.company/v2/charges', [
            "amount" => $amount,
            "currency" => $this->currency,
            "threeDSecure" => true,
            "save_card" => false,
            "description" => "Cerdit",
            "statement_descriptor" => "Cerdit",
            "reference" => [
                "transaction" => $unique_id,
                "order" => $unique_id
            ],
            "receipt" => [
                "email" => true,
                "sms" => true
            ],
            "customer" => [
                "first_name" => $username,
                "middle_name" => "",
                "last_name" => '',
                "email" => $email ?: '',
                "phone" => [
                    "country_code" => "966",
                    "number" => $phone
                ]
            ],
            'metadata' => [
                "udf1" => $order_id,
                "udf2" => $platform,
                "udf3" => $amount,
                "udf4" => $user_id,
                "udf5" => $complete_order ? "1" : "0",
            ],
            "source" => ["id" => "src_all"],
            "post" => ["url" => $this->verify_route_name],
            "redirect" => ["url" => $this->verify_route_name]
        ])->json();

        return [
            'status' => 200,
            'payment_id' => $response['id'],
            'redirect_url' => $response['transaction']['url'],
            'html' => ""
        ];
    }

    public function pay2($amount = null, $user_id = null, $username = null, $email = null, $phone = null, $order_id = null, $platform = 'api', $complete_order = false, $with_balance = 0, $balance = 0)
    {
        $unique_id = uniqid();
        $response = Http::withHeaders([
            "authorization" => "Bearer " . $this->tap_secret_key,
            "content-type" => "application/json",
            'lang_code' => $this->tap_lang_code
        ])->post('https://api.tap.company/v2/charges', [
            "amount" => $amount,
            "currency" => $this->currency,
            "threeDSecure" => true,
            "save_card" => false,
            "description" => "Cerdit",
            "statement_descriptor" => "Cerdit",
            "reference" => [
                "transaction" => $unique_id,
                "order" => $unique_id
            ],
            "receipt" => [
                "email" => true,
                "sms" => true
            ],
            "customer" => [
                "first_name" => $username,
                "middle_name" => "",
                "last_name" => '',
                "email" => $email ?: '',
                "phone" => [
                    "country_code" => "966",
                    "number" => $phone
                ]
            ],
            'metadata' => [
                "udf1" => $order_id,
                "udf2" => $platform,
                "udf3" => $amount,
                "udf4" => $user_id,
                "udf5" => $complete_order ? "1" : "0",
                "udf6" => $with_balance,
                "udf7" => $balance,
            ],
            "source" => ["id" => "src_all"],
            "post" => ["url" => $this->verify_route_name],
            "redirect" => ["url" => $this->verify_route_name]
        ])->json();

        return [
            'status' => 200,
            'payment_id' => $response['id'],
            'redirect_url' => $response['transaction']['url'],
            'html' => ""
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function verify(): array
    {
        $response = Http::withHeaders([
            "authorization" => "Bearer " . $this->tap_secret_key,
        ])->get('https://api.tap.company/v2/charges/' . request()->tap_id)->json();
        if (isset($response['status']) && $response['status'] == "CAPTURED") {
            return [
                'status' => 200,
                'success' => true,
                'payment_id' => request()->tap_id,
                'process_data' => $response
            ];
        } else {
            return [
                'status' => 400,
                'success' => false,
                'payment_id' => request()->tap_id,
                'process_data' => $response,
                'error' => $response['response']['message'],
            ];
        }
    }
}
