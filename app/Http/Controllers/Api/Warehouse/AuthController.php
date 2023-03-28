<?php

namespace App\Http\Controllers\Api\Warehouse;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Settings;
use App\Models\DeviceTokens;
use Illuminate\Http\Request;
use App\Models\ActivationCodes;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UsersResource;
use Illuminate\Support\Facades\Validator;
use MFrouh\Sms4jawaly\Facades\Sms4jawaly;

class AuthController extends Controller
{
    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        app()->setLocale($language);
        Carbon::setLocale(app()->getLocale());
        $this->middleware('auth:api')->only('logout', 'changePassword', 'updateInformation');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required']);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }
        $phone = $this->convertNum($request->phone);
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        $phone2 = "0" . $phone;
        $phonecode = '966';
        $is_sms_active = Settings::find(24)->value;
        $blockUser = User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 1)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('user_type_id', 6);
                    $query->oRwhere('user_type_id', 2)->where('privilege_id', 15);
                });
            })->first();

        $user = User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 0)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('user_type_id', 6);
                    $query->oRwhere('user_type_id', 2)->where('privilege_id', 15);
                });
            })->first();

        if ($blockUser) {
            return response()->json([
                'message' => trans('messages.you_are_blocked'),
            ], 400);
        } elseif ($user) {
            $user->activate = 0;
            $user->save();
            $activation = ActivationCodes::where('user_id', $user->id)->whereIn('phone', [$phone, $phone1, $phone2])->first();
            if ($activation && $user) {
                if ($phone == "999999909" || $phone == "535306566") {
                    $activation->activation_code = 1234;
                } else {
                    $activation->activation_code = mt_rand(1000, 9999);
                }
                $activation->save();
            } else if ($user && !$activation) {
                $activation = new ActivationCodes();
                $activation->user_id = $user->id;
                $activation->phonecode = $request->phonecode ?: 966;
                $activation->phone = $phone;
                if ($phone == "999999909" || $phone == "535306566") {
                    $activation->activation_code = 1234;
                } else {
                    $activation->activation_code = mt_rand(1000, 9999);
                }
                $activation->save();
            } else {
                return response()->json([
                    'message' => __('messages.incorrect_login_data'),
                ], 400);
            }
            if ($is_sms_active == '0') {

                $smsMessage = 'كود تفعيل حسابك فى تطبيق جاك : ' . $activation->activation_code;
                $final_num = $this->convertNum(ltrim($user->phone, '0'));

                // $phone_number = '+' . $user->phonecode . $final_num;
                // $customer_id = Settings::find(25)->value;
                // $api_key = Settings::find(26)->value;
                // Sms4jawaly::sendSms($smsMessage, $final_num, $phonecode);
            }
            // if no errors are encountered we can return a JWT
            return response()->json([
                'message' => trans('messages.please_activate_your_phone'),
                'sms_active' => (int) $is_sms_active,
                'activation_code' => $activation->activation_code,
            ], 200);
        } else {
            return response()->json([
                'status' => 202,
                'message' => trans('messages.incorrect_login_data'),
            ], 202);
        }
    }

    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'activation_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $activation_code = $request->activation_code;
        $phone = $this->convertNum($request->phone);
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        $phone2 = "0" . +$phone1;
        $phonecode = $request->phonecode;

        $user = ActivationCodes::where('activation_code', $activation_code)->whereIn('phone', [$phone, $phone1, $phone2])->where('phonecode', $phonecode)->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => trans('messages.error_code'),
                ],
                400
            );
        } else {
            $this_user1 = User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('user_type_id', 6);
                        $query->orWhere('user_type_id', 2)->where('privilege_id', 15);
                    });
                })->first();
            if ($user->getUser || $this_user1) {
                $user = $user->getUser ? $user->getUser : $this_user1;
                $this_user = User::where('id', $user->id)
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->where('user_type_id', 6);
                            $query->orWhere('user_type_id', 2)->where('privilege_id', 15);
                        });
                    })
                    ->select('*')
                    ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo')
                    ->first();

                $this_user->last_login = date('Y-m-d H:i:s');
                $this_user->device_token = $request->device_token;
                if ($request->device_type) {
                    $this_user->device_type = $request->device_type;
                }
                $this_user->activate = 1;
                $this_user->save();

                if ($request->device_token) {
                    if ($device = DeviceTokens::where('device_token', $request->device_token)->first()) {
                        $device->user_id = $this_user->id;
                        $device->save();
                    } else {
                        $device = new DeviceTokens();
                        $device->device_token = $request->device_token;
                        $device->user_id = $this_user->id;
                        $device->save();
                    }
                }
                $token = JWTAuth::fromUser($this_user);
                $this_user->token = $token;
                $this_user->save();

                return response()->json(new UsersResource($this_user), 200);
            } else {
                return response()->json(
                    [
                        'message' => trans('messages.invalid_activation_code'),
                    ],
                    202
                );
            }
        }
    }

    public function logout(Request $request)
    {
        if ($request->device_token) {
            DeviceTokens::where('device_token', $request->device_token)->delete();
        }
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => __('messages.logged_out')], 200);
    }

    public function changePassword(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|same:password_confirmation|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (Hash::check($request->old_password, $user->password)) {
            $this_user = User::find($user->id);
            $this_user->password = bcrypt($request->password);
            $this_user->save();
            return response()->json(['message' => 'تم تعديل كلمة السر'], 200);
        } else {
            return response()->json(
                [
                    'message' => trans('messages.old_password_innot_correct'),
                ],
                400
            );
        }
    }

    public function updateInformation(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'email' => 'nullable',
            'photo' => 'nullable|image',
            'username' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $user->update($request->except('_token'));
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $user->photo = $fileName;
            $user->save();
        }
        $user = User::where('id', $user->id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = ""  THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')->first();
        $user = UsersResource::make($user);
        return response()->json(['message' => 'تم تعديل البيانات الشخصية', 'user' => $user], 200);
    }

    public function send4SMS($oursmsusername, $oursmspassword, $messageContent, $mobileNumber, $senderName)
    {
        $user = $oursmsusername;
        $password = $oursmspassword;
        $sendername = $senderName;
        $text = $messageContent;
        $to = $mobileNumber;

        $getdata = http_build_query(
            $fields = array(
                "username" => $user,
                "password" => $password,
                "message" => $text,
                "numbers" => $to,
                "sender" => $sendername,
                "unicode" => 'e',
                "return" => 'json',
            )
        );

        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Content-Type: text/html; charset=utf-8',
            ),
        );
        $context = stream_context_create($opts);
        $response = $this->get_data('https://www.4jawaly.net/api/sendsms.php?' . $getdata, false, $context);
        return $response;
    }

    public function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        return str_replace($arabic, $english, $number);
    }

    public function get_data($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
