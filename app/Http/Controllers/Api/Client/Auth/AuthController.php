<?php

namespace App\Http\Controllers\Api\Client\Auth;

use App\Facades\AuthServiceFacade;
use App\Models\User;
use App\Models\Balance;
use App\Models\DeviceTokens;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Resources\Notifications;
use App\Http\Resources\UsersResource;
use Intervention\Image\Facades\Image;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Controllers\Api\Client\Controller;
use App\Http\Requests\UpdateNotificationRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth:api', ['except' => ['login', 'activate', 'register']]);
    }

    public function login(Request $request)
    {
        return AuthServiceFacade::login($request);
    }

    public function register(RegisterRequest $request)
    {
        return AuthServiceFacade::register($request);
    }

    public function activate(Request $request)
    {
        return AuthServiceFacade::activate($request);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth('api')->user();
        $user = User::find($user->id);
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads/';
            Image::make($file->getRealPath())->resize('500', '500', function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . $fileName);
        }
        $user->update($request->validated() + ['photo' => $file ? $fileName : $user->photo]);
        $user = User::where('id', $user->id)->with('state', 'region')->first();
        return response()->json(new UsersResource($user), 200);
    }

    public function notifications(Request $request)
    {
        $user = auth('api')->user();
        $notifications = Notification::where('reciever_id', $user->id)->with('getSender')->orderBy('id', 'DESC')->with('order')->paginate(10);

        Notification::where('reciever_id', $user->id)->where('status', 0)->update(['status' => 1]);

        $notifications->{'notifications'} = Notifications::collection($notifications);

        return response()->json($notifications, 200);
    }

    public function updateNotification(UpdateNotificationRequest $request)
    {
        $user = auth('api')->user();
        User::find($user->id)->update(['notification' => $request->notification]);
        $message = $request->notification == 0 ? __('messages.notification_off') : __('messages.notification_on');
        return response()->json(['message' => $message], 200);
    }

    public function me()
    {
        return response()->json(new UsersResource(auth('api')->user()));
    }

    public function logout()
    {
        if (request()->device_token) {
            DeviceTokens::where('device_token', request()->device_token)->delete();
        }
        auth('api')->logout();

        return response()->json(['message' => __('messages.logged_out')], 200);
    }

    public function deleteAccount()
    {
        if (request('device_token')) {
            DeviceTokens::where('device_token', request('device_token'))->delete();
        }

        User::find(auth('api')->id())->update(['block' => 1]);
        auth('api')->logout();

        return response()->json(['message' => __('messages.logged_out')], 200);
    }

    public function updateDeviceToken(Request $request)
    {
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        if ($request->device_token) {
            DeviceTokens::updateOrCreate(['device_token' => $request->device_token])->update(['user_id' => $user->id]);
        }

        return response()->json(['data' => $user], 200);
    }


    public function getBalance()
    {
        $user = auth('api')->user();
        $sum_balance = Balance::where('user_id', $user->id)->sum('price');
        $user_transactions = Balance::select('id', 'order_id', 'price', 'notes', 'created_at')
            ->orderBy('created_at', 'desc')->where('user_id', $user->id)->paginate(20);
        return response()->json(['balance' => $sum_balance, 'data' => $user_transactions]);
    }
}
