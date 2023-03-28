<?php

namespace App\Repositories;

// Import the class namespaces first, before using it directly


use App\Models\CartItem;
use App\Models\Orders;
use App\Models\Products;
use App\Models\ReservationOrder;
use App\Models\Reservations;
use App\Models\Settings;
use Google\Service\Vision\Product;
use Illuminate\Support\Facades\Log;

class TabbyRepository
{

    private const PUBLIC_KEY = 'pk_11a5ac6d-7206-44db-b768-b45f4447811d';
    private const SECRET_KEY = 'sk_606e275d-05d5-40a5-a060-f768bdfabb35';


    private const URL = 'https://api.tabby.ai';
    private const TEST_URL = 'https://api.tabby.ai';
    private const PRODUCTION = true;


    public static function pay($credit, $order, $items, $couponCode, $platform)
    {
        $auth_key = self::PUBLIC_KEY;
        $tabbyUrl = self::PRODUCTION ? self::URL : self::TEST_URL;
        $url = $tabbyUrl . "/api/v2/checkout";
        $production = self::PRODUCTION;

        $allItems = [];
        $collect_shipments = collect($items);
        for ($x = 0; $x < $collect_shipments->count(); $x++) {
            $cart_items = collect($collect_shipments[$x]['cart_items']);
            for ($y = 0; $y < $cart_items->count(); $y++) {
                $product = Products::withTrashed()->where(['id' => $cart_items[$y]['product_id']])->first();

                $allItems[] = [
                    'title' => \App::getLocale() == 'en' ? $product->title_en : $product->title,
                    'quantity' => $cart_items[$y]['quantity'],
                    'unit_price' => $cart_items[$y]['offer_price'] > 0 ? $cart_items[$y]['offer_price'] : $cart_items[$y]['price'],
                    'category' => \App::getLocale() == 'en' ? $product->category->name_en : $product->category->name,
                ];
            }
        }

        $data = [
            'payment' => [
                'amount' => $credit,
                'currency' => 'SAR',
                'buyer' => [
                    'phone' => $order->user->phone,
                    'email' => $order->user->email,
                    'name' => $order->user->username,
                ],
                'shipping_address' => [
                    'city' => @$order->address->state->name,
                    'address' => @$order->address->street,
                    'zip' => '234',
                ],
                'order' => [
                    'reference_id' => $order->id . '',
                    'items' => $allItems
                ],
                'buyer_history' => [
                    'registered_since' => str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime($order->user->created_at))) . 'Z',
                    'loyalty_level' => Orders::withTrashed()->where(['user_id' => $order->user_id, 'status' => 7])->count(),
                ],
                'order_history' => [
                    [
                        'purchased_at' => str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime($order->created_at))) . 'Z',
                        'amount' => $credit,
                        'status' => 'new'
                    ]
                ]
            ],
            'lang' => \App::getLocale(),
            'merchant_code' => self::SECRET_KEY,
            'merchant_urls' => [
                'success' => url('/api/v1/tabby/payment/status/process?order_id=' . $order->id . '&user_id=' . $order->user_id . '&code=' . $couponCode . '&platform=' . $platform),
                'cancel' => url('/api/v1/tabby/payment/status/error' . '?platform=' . $platform),
                'failure' => url('/api/v1/tabby/payment/status/error' . '?platform=' . $platform),
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $auth_key));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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


    public static function getPaymentUrl($request, $user, $platform = 'api')
    {
        if ($request->has('order_id')) {
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


            $result = TabbyRepository::pay(number_format((float)$total_cost, 2, '.', ''), $order, $objects, $code, $platform);

            if (isset($result['warnings']) && count($result['warnings']) > 0) {
                return [
                    'message' => trans('messages.phone_and_email_required')
                ];
            } else if (isset($result['id']) && isset($result['configuration']) && isset($result['configuration']['available_products'])
                && isset($result['configuration']['available_products']['installments'])
                && count($result['configuration']['available_products']['installments']) > 0) {
                return $result['configuration']['available_products']['installments'][0]['web_url'];
            }
        }
        return false;
    }

    public static function getServicesPaymentUrl($total_cost, $order, $user, $platform = 'api')
    {
        $reservation = Reservations::where(['order_id' => $order->id])->first();
        $objects = $reservation->reservationItems;
        $result = TabbyRepository::servicePay($total_cost, $order, $reservation, $objects, $user, $platform);

        if (isset($result['warnings']) && count($result['warnings']) > 0) {
            return [
                'message' => trans('messages.phone_and_email_required')
            ];
        } else if (isset($result['id']) && isset($result['configuration'])
            && count($result['configuration']['available_products']['installments']) > 0) {
            return $result['configuration']['available_products']['installments'][0]['web_url'];
        }
        return false;
    }

    public static function servicePay($credit, $order, $reservation, $items, $user, $platform)
    {
        $auth_key = self::PUBLIC_KEY;
        $tabbyUrl = self::PRODUCTION ? self::URL : self::TEST_URL;
        $url = $tabbyUrl . "/api/v2/checkout";
        $production = self::PRODUCTION;

        $allItems = [];

        foreach ($items as $item) {
            $service = $item->offer_item->service;
            $allItems[] = [
                'title' => \App::getLocale() == 'en' ? $service->name_en : $service->name_ar,
                'quantity' => 1,
                'unit_price' => $item->final_price,
                'category' => \App::getLocale() == 'en' ? $service->category->name_en : $service->category->name,
            ];
        }

        $data = [
            'payment' => [
                'amount' => $credit,
                'currency' => 'SAR',
                'buyer' => [
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'name' => $user->username,
                ],
                'shipping_address' => [
                    'city' => $reservation->provider->userData->address_en,
                    'address' => $reservation->provider->userData->address_en,
                    'zip' => '234',
                ],
                'order' => [
                    'reference_id' => $order->id . '',
                    'items' => $allItems
                ],
                'buyer_history' => [
                    'registered_since' => str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime($user->created_at))) . 'Z',
                    'loyalty_level' => ReservationOrder::where(['user_id' => $user->id, 'status' => 7])->count(),
                ],
                'order_history' => [
                    [
                        'purchased_at' => str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime($order->created_at))) . 'Z',
                        'amount' => $credit,
                        'status' => 'new'
                    ]
                ]
            ],
            'lang' => \App::getLocale(),
            'merchant_code' => self::SECRET_KEY,
            'merchant_urls' => [
                'success' => url('/api/v1/reservation/tabby/payment/status/process?order_id=' . $order->id . '&user_id=' . $user->id . '&platform=' . $platform),
                'cancel' => url('/api/v1/reservation/tabby/payment/status/error' . '?platform=' . $platform . '&order_id=' . $order->id),
                'failure' => url('/api/v1/reservation/tabby/payment/status/error' . '?platform=' . $platform . '&order_id=' . $order->id),
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $auth_key));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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


    public static function retrievePayment($paymentId)
    {
        $auth_key = self::SECRET_KEY;
        $tabbyUrl = self::PRODUCTION ? self::URL : self::TEST_URL;
        $url = $tabbyUrl . "/api/v2/payments/" . $paymentId;
        $production = self::PRODUCTION;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $auth_key));
        curl_setopt($ch, CURLOPT_POST, 0);
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

    public static function capturePayment($order)
    {
        $objects = CartItem::
        select('cart_items.id', 'cart_items.shipment_id', 'cart_items.item_id', 'cart_items.cobon_discount', 'cart_items.motivation_discount', 'cart_items.type',
            'order_shipments.status', 'order_shipments.shop_id',
            'orders.payment_method', 'cart_items.price', 'cart_items.quantity', 'orders.id as order_id')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE 
             cart_items.shipment_id = ' . $order->id . ') as gift_count')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.shipment_id =order_shipments.id and cart_items.status = 5) as items_canceled ')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.shipment_id =order_shipments.id) as items_count ')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.order_id =orders.id) as shipments_count')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.order_id =orders.id and order_shipments.status = 5) as shipments_canceled')
            ->selectRaw('(SELECT sum((cart_items.price * cart_items.quantity)+(cart_items.price_vat)-(cart_items.discount_price)-(cart_items.cobon_discount)) FROM cart_items
              WHERE cart_items.shipment_id =' . $order->id . ') as return_price')
            ->where('cart_items.shipment_id', $order->id)
            ->join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->join('orders', 'orders.id', 'order_shipments.order_id')
            ->where('order_shipments.status', 5)
            ->get();
        $return_price = 0;
        foreach ($objects as $object) {
            $return_price += $object->return_price;
        }

        $final = ($order->final_price + $order->delivery_price) - $return_price;

        $auth_key = self::SECRET_KEY;
        $tabbyUrl = self::PRODUCTION ? self::URL : self::TEST_URL;
        $url = $tabbyUrl . "/api/v1/payments/" . $order->getOrder->tabby_payment_id . "/captures";
        $production = self::PRODUCTION;

        $data = [
            'amount' => number_format($final, 2, '.', ''),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $auth_key));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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

    public static function captureServicePayment($order)
    {
        $final = $order->final_price;

        $auth_key = self::SECRET_KEY;
        $tabbyUrl = self::PRODUCTION ? self::URL : self::TEST_URL;
        $url = $tabbyUrl . "/api/v1/payments/" . $order->tabby_payment_id . "/captures";
        $production = self::PRODUCTION;

        $data = [
            'amount' => number_format($final, 2, '.', ''),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $auth_key));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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
}
