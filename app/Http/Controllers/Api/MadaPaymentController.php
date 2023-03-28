<?php

namespace App\Http\Controllers\Api;

use App\Models\Arbpg;
use App\Models\Orders;
use App\Models\Balance;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Validator;

class MadaPaymentController extends Controller
{
    public function payment(Request $request)
    {
        ini_set('serialize_precision', -1);
        $validator = Validator::make($request->all(), [
            'card_number' => 'required',
            'expiry_month' => 'required',
            'expiry_year' => 'required',
            'cvv' => 'required',
            'holder_name' => 'required',
            'order_id' => 'required',
        ]);
        $card_number = $request->card_number;
        $expiry_month = $request->expiry_month;
        $expiry_year = $request->expiry_year;
        $cvv = $request->cvv;
        $card_holder = $request->holder_name;
        $with_balance = $request->with_balance == 1 || $request->with_balance == '1' ? 1 : 0;
        $complete_order = $request->complete_order == 1 || $request->complete_order == '1' ? 1 : 0;

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => __('messages.complete_inputs'),], 201);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // if (($request->complete_order == 1 || $request->complete_order == '1')) {
        //     $order = Orders::where(['user_id' => $user->id, 'id' => $request->order_id])->where('payment_method', 5)->first();
        // } else {
        //     $order = Orders::where(['user_id' => $user->id, 'id' => $request->order_id, 'status' => 0])->where('payment_method', 0)->first();
        // }

        // if (!$order) {
        //     return response()->json(['status' => 201, 'message' => trans('messages.there_is_no_order')], 201);
        // }

        // if ($order && $order->balance != null) {
        //     $remaining_money = round(($order->final_price + $order->balance->price), 2);
        //     $payed_balance = round($order->balance->price, 2);
        // } else {
        //     $remaining_money = round($order->final_price, 2);
        //     $payed_balance = 0;
        // }
        // $amount = $remaining_money;

        // if ($amount <= 0) {
        //     return response()->json(['status' => 201, 'message' => 'المبلغ اقل من الصفر'], 201);
        // }

        // $balance = Balance::where('user_id', $user->id)->sum('price');
        // if ($with_balance && round($balance, 2) <= 0) {
        //     return [
        //         'status' => 400,
        //         'message' => 'الرصيد غير كافي',
        //         'get_balance' => round($balance, 2),
        //         'payed_balance' => round($payed_balance, 2),
        //         'total' => round($order->final_price, 2),
        //     ];
        // }

        $check = (new PaymentRepository)->checkPayment($user);
        if ($check->original['status'] == 200) {
            $amount = $check->original['amount'];
            $order = $check->original['order'];
            $complete_order = $check->original['complete_order'];
            $with_balance = $check->original['with_balance'];
            $balance = $check->original['balance'];
        } else {
            return $check;
        }

        $arbPg = new Arbpg();

        $response = $arbPg->getmerchanthostedPaymentid(
            $card_number,
            $expiry_month,
            $expiry_year,
            $cvv,
            $card_holder,
            $order->id,
            $amount,
            'api',
            $complete_order,
            $with_balance,
            $balance,
        );

        if ($response['status'] == 200) {
            return response()->json(['message' => trans('messages.payment_done'), 'url' => $response['url']], 200);
        } elseif ($response['status'] == 400) {
            return response()->json(['status' => 201, 'message' => trans('messages.error_reservation'),], 201);
        } else {
            return response()->json(['status' => 202, 'message' => trans('messages.error_card'),], 202);
        }
    }
}
