<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Arbpg
{


    const ARB_HOSTED_ENDPOINT = 'https://digitalpayments.alrajhibank.com.sa/pg/payment/hosted.htm';
    const ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION = 'https://digitalpayments.alrajhibank.com.sa/pg/payment/tranportal.htm';

    private $ARB_SUCCESS_STATUS = 'CAPTURED';
    private $Tranportal_ID = "cDJgl9I0Y85uf7U";
    private $Tranportal_Password = 'S3v#geCv1$!4G3K';
    private $resource_key = "20109082530220109082530220109082";
    private $website_url = '';


    private $PRODUCTION = false;
    private $HOSTED_URL = '';


    /*test*/
    // const ARB_HOSTED_ENDPOINT = 'https://securepayments.alrajhibank.com.sa/pg/payment/hosted.htm';
    // const ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION = 'https://securepayments.alrajhibank.com.sa/pg/payment/tranportal.htm';
    // const ARB_SUCCESS_STATUS = 'CAPTURED';
    // const Tranportal_ID = "qLQa25T5PsT9bq2";
    // const Tranportal_Password = "X6vH01@nB3V#my!";
    // const resource_key = "13549833313413549833313413549833";
    // const website_url = 'https://goldenroad.liv';

    public function __construct()
    {
        $this->PRODUCTION = intval(PaymentSettings::find(10)->value) == 1;
        $this->HOSTED_URL = $this->PRODUCTION ?
            $this->ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION : $this->ARB_HOSTED_ENDPOINT;
        $this->Tranportal_ID = PaymentSettings::find(11)->value;
        $this->Tranportal_Password = PaymentSettings::find(12)->value;
        $this->resource_key = PaymentSettings::find(13)->value;
        $this->website_url = url('/');
    }


    /*test*/
    public function test()
    {
        echo "working";
    }

    public function initiatePayment($request)
    {
        $card_number = $request->card_number;
        $expiry_month = $request->expiry_month;
        $expiry_year = $request->expiry_year;
        $cvv = $request->cvv;
        $card_holder = $request->holder_name;
        $amount = $request->amount;
        $order_id = $request->order_id;
        $platform = $request->platform;


        $arbPg = new Arbpg();

        //        $arbPg->test();

        $url = $arbPg->getmerchanthostedPaymentid(
            $card_number,
            $expiry_month,
            $expiry_year,
            $cvv,
            $card_holder,
            $order_id,
            $amount,
            $platform
        );


        return response()->json($url);


        // $url= $ARB_PAYMENT_ENDPOINT_TESTING . $paymentId; //in Production use Production End Point
        return response()->redirectTo($url, 302);
    }


    public function paymentResult(Request $request)
    {

        $trandata = $request->trandata;
        //        var_dump($trandata);
        $arbPg = new Arbpg();

        $result = $arbPg->getresult($trandata);
        if ($result['status'] == 'success') {
            if ($result['orderType'] == 1) {
                return $this->shopPayment($result['orderId']);
            } elseif ($result['orderType'] == 2) {
                return $this->pricingSendPaymentOrder($result['orderId']);
            } elseif ($result['orderType'] == 3) {
                return $this->damageSendPaymentOrder($result['orderId']);
            }
        }

        return redirect('/api/v1/payment-error');
    }


    public function getmerchanthostedPaymentid(
        $card_number,
        $expiry_month,
        $expiry_year,
        $cvv,
        $card_holder,
        $order_id,
        $amount,
        $platform = 'web',
        $complete_order = false,
        $with_balance = 0,
        $balance = 0
    )
    {


        $exp_year = "20" . $expiry_year;
        $amount = $amount ?: 0;
        if (auth()->id() == 754 || auth()->id() == 439) {
            $amount = 1;
        }
        $trackId = $order_id . (string)rand(1, 1000000); // TODO: Change to real value
        $order = Orders::find($order_id);
        $order->update(['per_track_id' => $trackId, 'check_mada_with_balance' => $with_balance]);
        $data = [
            "id" => $this->Tranportal_ID,
            "password" => $this->Tranportal_Password,
            "expYear" => $exp_year,
            "expMonth" => $expiry_month,
            "member" => $card_holder,
            "cvv2" => $cvv,
            "cardNo" => $card_number,
            "cardType" => "C",
            "action" => "1",
            "udf1" => $order_id,
            "udf2" => $platform,
            "udf3" => $amount,
            "udf4" => $order->user_id,
            "udf5" => $complete_order ? "1" : "0",
            "udf6" => $with_balance,
            "udf7" => $balance,
            "currencyCode" => "682",
            "responseURL" => $this->website_url . '/payment-result',
            "errorURL" => $platform == 'web' ? $this->website_url . '/payment-error' : $this->website_url . '/api/v1/payment-error',
            "trackId" => $trackId,
            "amt" => $amount,
        ];

        $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        $wrappedData = $this->wrapData($data);
        $encData = [
            "id" => $this->Tranportal_ID,
            "trandata" => $this->aesEncrypt($wrappedData),
            //            "responseURL" => "https://goldenroad.sa/payment-result",
            "responseURL" => $this->website_url . '/payment-result',
            //            "errorURL" => $platform=='web'?"https://goldenroad.sa/payment-error":"https://goldenroad.sa/api/v1/payment-error",
            "errorURL" => $platform == 'web' ? $this->website_url . '/payment-error' : $this->website_url . '/api/v1/payment-error',
        ];

        $wrappedData = $this->wrapData(json_encode($encData, JSON_UNESCAPED_SLASHES));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $wrappedData,

            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Accept-Language: application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // parse response and get id
        $response_data = json_decode($response, true)[0];
        // Log::info($response_data);
        if ($response_data["status"] == "1") {
            $url = "https:" . explode(":", $response_data["result"])[2];
            return ['status' => 200, 'url' => $url];
        } else {
            // handle error either refresh on contact merchant
            //            return $this->getResult($response_data['trandata']);
            return ['status' => 402, 'reason' => $response_data];
        }
    }

    public function getPaymentId()
    {
        $plainData = $this->getRequestData();
        $wrappedData = $this->wrapData($plainData);
        $encData = [
            "id" => $this->Tranportal_ID,
            "trandata" => $this->aesEncrypt($wrappedData),
            "errorURL" => "https://tocars.net/api/v1/payment-result",
            "responseURL" => "https://tocars.net/api/v1/payment-result",
        ];
        $wrappedData = $this->wrapData(json_encode($encData, JSON_UNESCAPED_SLASHES));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->ARB_HOSTED_ENDPOINT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $wrappedData,

            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Accept-Language: application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        print_r($response);
        curl_close($curl);

        // parse response and get id
        $data = json_decode($response, true)[0];
        print_r($data);
        if ($data["status"] == "1") {
            $id = explode(":", $data["result"])[0];
            return $id;
        } else {
            // handle error either refresh on contact merchant
            return -1;
        }
    }


    public function getResult($trandata)
    {

        $decrypted = $this->aesDecrypt($trandata);
        $raw = urldecode($decrypted);
        $dataArr = json_decode($raw, true);
        //        dd($dataArr);
        //        var_dump($dataArr);
        if (isset($dataArr[0]['errorText'])) {
            return ["status" => 400, 'data' => $dataArr[0]];
        }
        $paymentStatus = $dataArr[0]["result"];
        if (isset($paymentStatus) && $paymentStatus === $this->ARB_SUCCESS_STATUS) {
            return ["status" => 200, 'data' => $dataArr[0]];
        }
        return ["status" => 400, 'data' => $dataArr[0]];
    }

    private function getRequestData()
    {

        // $this->load->model('checkout/order');

        $amount = 100;

        $trackId = (string)rand(1, 1000000); // TODO: Change to real value

        $data = [
            "id" => $this->Tranportal_ID,
            "password" => $this->Tranportal_Password,
            "action" => "1",
            "currencyCode" => "682",
            "errorURL" => "https://tocars.net/api/v1/payment-result",
            "responseURL" => "https://tocars.net/api/v1/payment-result",
            "trackId" => $trackId,
            "amt" => $amount,

        ];

        $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        //var_dump($data);
        return $data;
    }


    private function wrapData($data)
    {
        $data = <<<EOT
[$data]
EOT;
        return $data;
    }

    private function aesEncrypt($plainData)
    {
        $key = $this->resource_key;
        $iv = "PGKEYENCDECIVSPC";
        $str = $this->pkcs5_pad($plainData);
        $encrypted = openssl_encrypt($str, "aes-256-cbc", $key, OPENSSL_ZERO_PADDING, $iv);
        $encrypted = base64_decode($encrypted);
        $encrypted = unpack('C*', ($encrypted));
        $encrypted = $this->byteArray2Hex($encrypted);
        $encrypted = urlencode($encrypted);
        return $encrypted;
    }

    private function pkcs5_pad($text, $blocksize = 16)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function byteArray2Hex($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        $bin = join($chars);
        return bin2hex($bin);
    }

    private function aesDecrypt($code)
    {
        $code = $this->hex2ByteArray(trim($code));
        $code = $this->byteArray2String($code);
        $iv = "PGKEYENCDECIVSPC";
        $key = $this->resource_key;
        $code = base64_encode($code);
        $decrypted = openssl_decrypt(
            $code,
            'AES-256-CBC',
            $key,
            OPENSSL_ZERO_PADDING,
            $iv
        );

        return $this->pkcs5_unpad($decrypted);
    }

    private function pkcs5_unpad($text)
    {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    private function hex2ByteArray($hexString)
    {
        $string = hex2bin($hexString);
        return unpack('C*', $string);
    }

    private function byteArray2String($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        return join($chars);
    }

    public function getMerchantHostedPaymentIdApplePay(
        $paymentData,
        $transactionIdentifier,
        $paymentMethod,
        $order_id,
        $amount,
        $platform = 'web',
        $complete_order = false
    )
    {
        ini_set('serialize_precision', -1);
        $amount = $amount ?: 0;
        $trackId = $order_id . (string)rand(1, 1000000); // TODO: Change to real value
        $order = Orders::find($order_id);
        $data = [
            "id" => $this->Tranportal_ID,
            "password" => $this->Tranportal_Password,
            // "paymentData" =>  $this->aesEncrypt($paymentData),
            "paymentData" => $paymentData,
            "paymentMethod" => $paymentMethod,
            "transactionIdentifier" => $transactionIdentifier,
            "action" => "1",
            "udf1" => $order_id,
            "udf2" => $platform,
            "udf3" => $amount,
            "udf4" => $order->user_id,
            "udf6" => $complete_order ? "1" : "0",
            "udf5" => "Select",
            "currencyCode" => "682",
            "responseURL" => $this->website_url . '/payment-result',
            "errorURL" => $platform == 'web' ? $this->website_url . '/payment-error' : $this->website_url . '/api/v1/payment-error',
            "trackId" => $trackId,
            "amt" => $amount,
        ];

        $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        $wrappedData = $this->wrapData($data);
        // dd($wrappedData);
        $encData = [
            "id" => $this->Tranportal_ID,
            "trandata" => $this->aesEncrypt($wrappedData),
            //            "responseURL" => "https://goldenroad.sa/payment-result",
            "responseURL" => $this->website_url . '/payment-result',
            //            "errorURL" => $platform=='web'?"https://goldenroad.sa/payment-error":"https://goldenroad.sa/api/v1/payment-error",
            "errorURL" => $platform == 'web' ? $this->website_url . '/payment-error' : $this->website_url . '/api/v1/payment-error',
        ];

        $wrappedData = $this->wrapData(json_encode($encData, JSON_UNESCAPED_SLASHES));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $wrappedData,

            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Accept-Language: application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // parse response and get id
        $response_data = json_decode($response, true)[0];
        dd($response_data);
        if ($response_data["status"] == "1") {
            $url = "https:" . explode(":", $response_data["result"])[2];
            return ['status' => 200, 'url' => $url];
        } else {
            // handle error either refresh on contact merchant
            //            return $this->getResult($response_data['trandata']);
            Log::info($response_data);
            return ['status' => 402, 'reason' => $response_data];
        }
    }


    public function checkPayment($trackId, $amount)
    {
        $data = [
            "amt" => $amount,
            "id" => $this->Tranportal_ID,
            "password" => $this->Tranportal_Password,
            "action" => "8",
            "udf5" => "TrackID",
            "currencyCode" => "682",
            "transId" => $trackId
        ];

        $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        $wrappedData = $this->wrapData($data);
        $encData = [
            "id" => $this->Tranportal_ID,
            "trandata" => $this->aesEncrypt($wrappedData),
        ];

        $wrappedData = $this->wrapData(json_encode($encData, JSON_UNESCAPED_SLASHES));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->ARB_MERCHANT_HOSTED_ENDPOINT_PRODUCTION,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $wrappedData,

            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Accept-Language: application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // parse response and get id
        $response_data = json_decode($response, true)[0];
        Log::info($response_data);
        return $response_data;
    }
}
