<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Settings;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Services\TapPayment;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\TapRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class TapPaymentController extends Controller
{

    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        App::setLocale($language);
        $this->middleware('jwt.auth')->except('errorPayment', 'successPayment');
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
        $responseTap = (new TapRepository())->checkout2($request, 'api', false);
        $responseTap = $responseTap->original;
        if ($responseTap['status'] == 200 && $responseTap['url']) {
            return response()->json(['status' => 200, 'url' => $responseTap['url'], 'message' => 'تم انشاء الطلب بنجاح وسيتم تحويلك لصفحة الدفع']);
        } else {
            return response()->json(['status' => 400, 'message' => 'حدث خطأ ما أثناء معالجة بيانات الطلب']);
        }
    }

    public function errorPayment()
    {
        return response()->json();
    }

    public function successPayment()
    {
        return response()->json();
    }
}
