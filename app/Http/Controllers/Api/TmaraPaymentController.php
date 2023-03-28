<?php

namespace App\Http\Controllers\Api;

use App\Models\Orders;
use App\Repositories\OrderRepository;
use App\Repositories\TmaraRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use JWTAuth;

class TmaraPaymentController extends Controller
{

    public function __construct(Request $request)
    {
        //        parent::__construct();
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        App::setLocale($language);
        $this->middleware('jwt.auth')->except('paymentDone', 'paymentSuccess', 'paymentError', 'paymentNotify');
        \Carbon\Carbon::setLocale(App::getLocale());
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
        ini_set('serialize_precision', -1);
    }

    public function checkout(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->block == 1) {
            return \response()->json([
                'status' => 400,
                'message' => __('messages.you_are_blocked_from_admin'),
            ]);
        }
        $tmara = new TmaraRepository();
        $response = $tmara->checkout($request, $request->order_id, $user, 'api');
        if ($response !== false) {
            return response()->json([
                'status' => 200,
                'url' => $response
            ]);
        }
        return \response()->json([
            'status' => 400,
            'message' => 'حدث خطأ ما أثناء معالجة بيانات الطلب'
        ]);
    }

    public function paymentDone(Request $request)
    {
        if (isset($request->ord_id) && isset($request->paymentStatus) && isset($request->orderId)) {
            $order = Orders::find($request->ord_id);
            if ($request->paymentStatus === 'approved') {
                $tmara = new TmaraRepository();
                if ($tmara->authorise($request->orderId)) {
                    $order->tmara_order_id = $request->orderId;
                    $order->save();
                    $saved = OrderRepository::saveOrder($order->id, $order->user, 8);
                    if ($saved) {
                        if ($request->platform === 'api') {
                            return redirect()->to(url('/api/v1/tmara/payment-success'));
                        } else {
                            return redirect('/my-orders')->with('message', ' تم ارسال الطلب  رقم ' . $order->id . ' بنجاح ');
                        }
                    }
                }
            }
        }
        return redirect()->to(url('/api/v1/tmara/payment-error?platform=' . $request->platform));
    }


    public function paymentSuccess(Request $request)
    {
        return response()->json();
    }

    public function paymentError(Request $request)
    {
        if ($request->platform === 'api') {
            return response()->json();
        } else {
            return redirect('/payment-status')->with('message', 'تم إلغاء عملية الدفع او حدث خطأ ما.');
        }
    }


    public function paymentNotify(Request $request)
    {
        $this->paymentDone($request);
    }
}
