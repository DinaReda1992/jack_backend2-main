<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        app()->setLocale($language);
        $this->middleware('jwt.auth');
        \Carbon\Carbon::setLocale(app()->getLocale());
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                if ($user->block == 1) {
                    JWTAuth::invalidate(JWTAuth::getToken());
                    return response()->json(__('messages.you_are_blocked_from_admin'), 401);
                }
            }
        } catch (TokenExpiredException $e) {
            return "1" . $e;
        } catch (TokenInvalidException $e) {
            return "2" . $e;
        } catch (JWTException $e) {
            return "3" . $e;
        }
    }

    public function orderDetails($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = Orders::where('user_id', $user->id)->where('is_edit', 1)->find($id);

        if (!$order) {
            return response()->json(['message' => 'لا يوجد طلبات متبقية'], 400);
        }
        ini_set( 'serialize_precision', -1 );
        return response()->json($order->orderDetails());
    }

    public function addNewOrderOnOldOrder($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = Orders::where('user_id', $user->id)->where('is_edit', 1)->find($id);

        if (!$order) {
            return response()->json(['message' => 'لا يوجد طلبات متبقية'], 400);
        }

        $newOrder = $order->newOrderOnOld();

        return response()->json(['message' => 'تم اعادة الطلب بنجاح', 'order' => $newOrder]);
    }

    public function returnBalance($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = Orders::where('is_edit', 1)->where('user_id', $user->id)->find($id);
        if (!$order) {
            return response()->json(['message' => 'لا يوجد طلبات متبقية'], 400);
        }

        $order->returnBalance();

        return response()->json(['message' => 'تم اضافة رصيد الي حسابك', 'balance' => $user->balances()->sum('price')]);
    }
}
