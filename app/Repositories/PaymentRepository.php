<?php

namespace App\Repositories;

use App\Models\Orders;
use App\Models\Balance;

class PaymentRepository
{
    public function checkPayment($user)
    {
        ini_set('serialize_precision', -1);
        $with_balance = request()->with_balance == 1 || request()->with_balance == '1' ? 1 : 0;
        $complete_order = request()->complete_order == 1 || request()->complete_order == '1' ? 1 : 0;

        if ((request()->complete_order == 1 || request()->complete_order == '1')) {
            $order = Orders::where(['user_id' => $user->id, 'id' => request()->order_id])->where('payment_method', 5)->first();
        } else {
            $order = Orders::where(['user_id' => $user->id, 'id' => request()->order_id, 'status' => 0])->where('payment_method', 0)->first();
        }

        if (!$order) {
            return response()->json(['status' => 201, 'message' => trans('messages.there_is_no_order')], 201);
        }
        $balance = Balance::where('user_id', $user->id)->sum('price');
        if ($order && $order->balance != null) {
            $remaining_money = round(($order->final_price + $order->balance->price), 2);
            $payed_balance = round($order->balance->price, 2);
        } elseif ($order && $with_balance == 1 &&  round($balance, 2) > 0) {
            $remaining_money = round($order->final_price, 2) - round($balance, 2);
            $payed_balance = 0;
        } else {
            $remaining_money = round($order->final_price, 2);
            $payed_balance = 0;
        }
        $amount = $remaining_money;

        if ($amount <= 0) {
            return response()->json(['status' => 201, 'message' => 'المبلغ اقل من الصفر'], 201);
        }

        if ($with_balance == 1 && round($balance, 2) <= 0) {
            return response()->json([
                'status' => 400, 'message' => 'الرصيد غير كافي', 'get_balance' => round($balance, 2),
                'payed_balance' => round($payed_balance, 2), 'total' => round($order->final_price, 2),
            ]);
        }

        if ($with_balance == 1 && $order->balance) {
            $with_balance = 0;
        }

        return response()->json(['status' => 200, 'amount' => $amount, 'order' => $order, 'complete_order' => $complete_order, 'with_balance' => $with_balance, 'balance' => round($balance, 2)]);
    }
}
