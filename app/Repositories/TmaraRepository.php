<?php

namespace App\Repositories;

use App\Models\Balance;
use App\Models\Orders;
use App\Models\CartItem;
use App\Models\PaymentSettings;
use Illuminate\Support\Facades\Log;

class TmaraRepository
{
    private $AUTH_TOKEN = '';
    private $NOTIFICATION_TOKEN = '484c35ab-7b8d-4916-842a-6bd2fb159af8';
    private $PUBLIC_KEY = 'e72ffbff-f9cc-47db-a74e-d64c19891812';
    private $API_TEST_URL = 'https://api-sandbox.tamara.co';
    private $API_URL = 'https://api.tamara.co';
    private $PRODUCTION = false;

    public function __construct()
    {
        $this->AUTH_TOKEN = PaymentSettings::find(2)->value;
        $this->PRODUCTION = intval(PaymentSettings::find(3)->value) == 1;
    }

    public function getPaymentTypes($data)
    {
        if (isset($data['order_id']) || isset($data['total'])) {
            $order = isset($data['order_id']) ? Orders::find($data['order_id']) : null;
            if ($order || isset($data['total'])) {
                if ($order) {
                    $data['total'] = $order->final_price;
                    $data['phone'] = '966' . $order->user->phone;
                }
                $url = ($this->PRODUCTION ? $this->API_URL : $this->API_TEST_URL) . '/checkout/payment-types';
                $url = $url . '?currency=SAR&country=SA&order_value=' . $data['total'] . '&phone=' . $data['phone'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $this->AUTH_TOKEN));
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $responseData = curl_exec($ch);
                curl_close($ch);
                $res = json_decode($responseData, true);

                if (count($res) > 0 && isset($res[0]['supported_instalments']) && count($res[0]['supported_instalments']) > 0) {
                    $supported_instalment = $res[0]['supported_instalments'][0];
                    if (
                        $data['total'] < $supported_instalment['max_limit']['amount']
                        && $data['total'] > $supported_instalment['min_limit']['amount']
                    ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function checkout($request, $order_id, $user, $platform = 'api')
    {
        $remining_details = null;
        if (($request->complete_order == 1 || $request->complete_order == '1')) {
            $order = Orders::where(['user_id' => $user->id, 'id' => $order_id])->first();
            $remining_details = OrderRepository::complete_order($order);
            if ($remining_details['status'] == 400) {
                return false;
            }
        } else {
            $order = Orders::where(['id' => $order_id, 'user_id' => $user->id, 'payment_method' => 0])->first();
        }
        if (!$order) {
            return false;
        }

        $amount = $order->final_price;
        if (!$order->balance && ($request->with_balance == 1 || $request->with_balance == '1')) {
            $remaining = OrderRepository::payWithBalance($request, $order, $user);
            $remining_details['remaining_money'] = $remaining;
            $amount = $remaining;
        }
        if (($request->complete_order == 1 || $request->complete_order == '1')) {
            $remining_details = OrderRepository::complete_order($order);
            if ($remining_details['status'] == 400) {
                return false;
            }
            $amount = round($remining_details['remaining_money'], 2);
        }

        $items = [];
        $cart_items = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.item_id',
            'cart_items.type',
            'cart_items.quantity',
            'cart_items.price'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where(function ($query) use ($remining_details, $order_id) {
                if (isset($remining_details) && $remining_details && $remining_details['status'] === 200) {
                    $query->where('cart_items.order_id', $order_id);
                } else {
                    $query->where('cart_items.order_id', 0);
                }
            })
            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->get();
        foreach ($cart_items as $item) {
            $items[] = [
                'reference_id' => $item->product->id,
                'type' => 'physical',
                'name' => $item->product->title,
                'sku' => $item->product->title,
                'quantity' => $item->quantity,
                'total_amount' => [
                    'amount' => $item->price * $item->quantity,
                    'currency' => 'SAR'
                ],
            ];
        }

        $data = [
            'order_reference_id' => $order->id,
            'description' => $order->user->username,
            'country_code' => 'SA',
            'payment_type' => 'PAY_BY_INSTALMENTS',
            'total_amount' => [
                'amount' => $amount,
                'currency' => 'SAR'
            ],
            'tax_amount' => [
                'amount' => 0.00,
                'currency' => 'SAR'
            ],
            'shipping_amount' => [
                'amount' => 0.00,
                'currency' => 'SAR'
            ],
            'items' => $items,
            'consumer' => [
                'first_name' => $order->user->username,
                'last_name' => $order->user->username,
                'phone_number' => '966' . $order->user->phone,
                'email' => $order->user->email
            ],
            'shipping_address' => [
                'first_name' => $order->user->username,
                'last_name' => $order->user->username,
                'line1' => $order->address->address,
                'city' => $order->address->region ? $order->address->region->name_en : '',
                'country_code' => 'SA'
            ],
            'merchant_url' => [
                'success' => url('/api/v1/tmara/payment-done?ord_id=' . $order->id . '&platform=' . $platform),
                'failure' => url('/api/v1/tmara/payment-error?platform=' . $platform),
                'cancel' => url('/api/v1/tmara/payment-error?platform=' . $platform),
                'notification' => url('/api/v1/tmara/payment-notify?ord_id=' . $order->id . '&platform=' . $platform),
            ]
        ];

        $url = ($this->PRODUCTION ? $this->API_URL : $this->API_TEST_URL) . '/checkout';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $this->AUTH_TOKEN));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($responseData, true);
        if (isset($res['errors']) && count($res['errors']) > 0) {
            return false;
        } else if (isset($res['checkout_url']) && !empty($res['checkout_url'])) {
            return $res['checkout_url'];
        }
        return false;
    }

    public function authorise($order_id, $platform = 'api')
    {
        $url = ($this->PRODUCTION ? $this->API_URL : $this->API_TEST_URL) . '/orders/' . $order_id . '/authorise';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $this->AUTH_TOKEN));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($responseData, true);
        if (isset($res['status']) && $res['status'] === 'authorised') {
            return true;
        }
        return false;
    }

    public function capturePayment($order, $tmara_order_id, $total)
    {
        $url = ($this->PRODUCTION ? $this->API_URL : $this->API_TEST_URL) . '/payments/capture';
        $data = [
            'order_id' => $tmara_order_id,
            'total_amount' => [
                'amount' => $total,
                'currency' => 'SAR'
            ],
            'shipping_info' => [
                'shipped_at' => date('Y-m-d H:i:s'),
                'shipping_company' => 'Golden Road'
            ]
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $this->AUTH_TOKEN));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($responseData, true);
        if (isset($res['capture_id'])) {
            $order->tmara_capture_id = $res['capture_id'];
            $order->save();
            return true;
        }
        return false;
    }

    public function refund($capture_id, $tmara_order_id, $total)
    {
        // $url = ($this->PRODUCTION ? $this->API_URL : $this->API_TEST_URL) . '/payments/refund';
        // $data = [
        //     'order_id' => $tmara_order_id,
        //     'refund' => [
        //         'capture_id' => $capture_id,
        //         'total_amount' => [
        //             'amount' => $total,
        //             'currency' => 'SAR'
        //         ]
        //     ],
        // ];
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $this->AUTH_TOKEN));
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $responseData = curl_exec($ch);
        // curl_close($ch);
        // $res = json_decode($responseData, true);
        // if (isset($res['refunds']) && count($res['refunds']) > 0) {
        //     return true;
        // }
        // return false;
    }
}
