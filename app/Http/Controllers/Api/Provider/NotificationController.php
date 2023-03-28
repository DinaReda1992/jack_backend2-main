<?php

namespace App\Http\Controllers\Api\Provider;

use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Resources\PurchaseNotificationResource;
use App\Models\User;

class NotificationController extends Controller
{
    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        app()->setLocale($language);
        Carbon::setLocale(app()->getLocale());
        $this->middleware('jwt.auth');
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

    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_message = App::isLocale('ar') ? 'message' : 'message_en as message';
        $select_status = App::getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';
        $notifications = Notification::select('id', $select_message, 'created_at', 'sender_id', 'type', 'order_id')->whereIn('reciever_id', $user->mainSupplierUsers())
            ->with(['getSender' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->orderBy('id', 'DESC')
            ->with('PurchaseOrder')
            ->paginate(10);

        Notification::whereIn('reciever_id', $user->mainSupplierUsers())->where('status', 0)
            ->update(['status' => 1]);

        $notifications->{'notifications'} = PurchaseNotificationResource::collection($notifications);

        return response()->json($notifications, 200);
    }
}
