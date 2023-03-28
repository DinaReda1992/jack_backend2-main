<?php

namespace App\Services;

use App\Models\User;
use App\Models\Regions;
use App\Models\Settings;
use App\Models\ClientTypes;
use Illuminate\Support\Str;
use App\Models\DeviceTokens;
use App\Models\ActivationCodes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UsersResource;
use MFrouh\Sms4jawaly\Facades\Sms4jawaly;

class AuthService
{
    public function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        return str_replace($arabic, $english, $number);
    }

    public function uploadOne(UploadedFile $file, $folder = null, $filename = null, $disk = 'public')
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            $disk
        );
        return $name . "." . $file->getClientOriginalExtension();
    }

    public function login($request)
    {
        $phone = $this->convertNum($request->phone);
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        $phone2 = "0" . $phone;
        $phone_code = '966';
        $is_sms_active = Settings::find(24)->value;
        $block_user = User::where('phonecode', $phone_code)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 1)->first();
        $user = User::where('phonecode', $phone_code)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 0)->first();

        if ($block_user) {
            return response()->json(['message' => trans('messages.you_are_blocked'), 'status' => 400], 400);
        }

        if (!$user) {

            if ($activation = ActivationCodes::where('phonecode', $phone_code)->whereIn('phone', [$phone1, $phone, $phone2])->first()) {
                $activation->activation_code = mt_rand(1000, 9999);
                $activation->save();
            } else {
                $activation = ActivationCodes::create(['phonecode' => $request->phonecode ?: 966, 'phone' => $phone, 'activation_code' => mt_rand(1000, 9999),]);
            }

            if ($is_sms_active == '0') {
                $smsMessage = 'كود تفعيل حسابك فى تطبيق جاك : ' . $activation->activation_code;
                $final_num = $this->convertNum(ltrim($activation->phone, '0'));
                Sms4jawaly::sendSms($smsMessage, $final_num, $phone_code);
            }

            return response()->json(['login_status' => 0, 'message' => trans('messages.please_activate_your_phone'), 'sms_active' => (int)$is_sms_active, 'activation_code' => $activation->activation_code], 200);
        }

        if ($user->is_archived == 1) {
            return response()->json(['message' => trans('messages.you_are_blocked'), 'status' => 400], 400);
        }

        if ($user->approved == 0) {
            return response()->json(['message' => __('messages.Waiting for the data to be reviewed by the administration'), 'status' => 400], 400);
        }

        if ($user) {
            $activation = ActivationCodes::where('user_id', $user->id)->whereIn('phone', [$phone, $phone1, $phone2])->first();

            if ($activation) {
                $phone == "123456789" ? $activation->activation_code = 1234 : $activation->activation_code = mt_rand(1000, 9999);
                $activation->save();
            } else {
                $activation = ActivationCodes::create(['user_id' => $user->id, 'phonecode' => $request->phonecode ?: 966, 'phone' => $phone, 'activation_code' => $phone == "123456789" ? 1234 : mt_rand(1000, 9999),]);
            }

            if ($is_sms_active == '0') {
                $smsMessage = 'كود تفعيل حسابك فى تطبيق جاك : ' . $activation->activation_code;
                $final_num = $this->convertNum(ltrim($user->phone, '0'));
                Sms4jawaly::sendSms($smsMessage, $final_num, $phone_code);
            }

            return response()->json(['message' => trans('messages.please_activate_your_phone'), 'sms_active' => (int)$is_sms_active, 'activation_code' => $activation->activation_code,], 200);
        }
    }

    public function register($request, $guard = 'api')
    {
        $phone = ltrim($request->phone, '0');
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        $phone2 = "0" . $phone;
        $input = [];
        $activation = ActivationCodes::whereIn('phone', [$phone, $phone1, $phone2])->where('activate', 1)->first();

        if (!$activation) {
            return response()->json(['status' => 400, 'message' => __('messages.this phone number is not activated')], 400);
        }

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'profile-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $input['photo'] = $fileName;
        }

        $file2 = $request->file('commercial_id');
        if ($request->hasFile('commercial_id')) {
            $name = 'commercial_id-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file2, $destinationPath, $name);
            $input['commercial_id'] = $fileName;
        }

        $user = User::create($input + $request->validated());
        $user = User::where('id', $user->id)->first();

        $activation_code = ActivationCodes::whereIn('phone', [$user->phone, ltrim($user->phone, 0)])->first();
        if ($activation_code) {
            $activation_code->user_id = $user->id;
            $activation_code->save();
        }
        if ($guard == 'api') {
            $token = Auth::guard('api')->login($user);
            $user->token = $token;
        }
        $user->save();
        if ($guard != 'api') {
            Auth::guard($guard)->login($user);
        }
        $items = json_decode($request->items);

        if ($items && count($items) > 0) {
            foreach ($items as $item) {
                if (!empty($item) && $item->product_id) {
                    CartRepository::addToCart($request, $user);
                }
            }
        }

        if ($request->device_token) {
            DeviceTokens::updateOrCreate(['device_token' => $request->device_token])->update(['user_id' => $user->id]);
        }

        return response()->json(new UsersResource($user), 200);
    }

    public function activate($request, $guard = 'api')
    {
        $activation_code = $request->activation_code;
        $phone = $this->convertNum($request->phone);
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        $phone2 = "0" . +$phone1;
        $phone_code = 966;

        $activation = ActivationCodes::where('activation_code', $activation_code)->whereIn('phone', [$phone, $phone1, $phone2])->where('phonecode', $phone_code)->first();

        if (!$activation) {
            return response()->json(['message' => trans('messages.error_code'),], 400);
        }

        $user = User::whereIn('phone', [$phone, $phone1, $phone2])->first();
        if ($user) {
            $user = User::where('id', $user->id)->first();
            $activation->user_id = $user->id;
            $activation->save();
            $user->last_login = date('Y-m-d H:i:s');
            $user->device_token = $request->device_token;
            $request->device_type ?: null;
            if ($request->device_type) {
                $user->device_type = $request->device_type;
            }
            $user->save();
            if ($request->device_token) {
                DeviceTokens::updateOrCreate(['device_token' => $request->device_token])->update(['user_id' => $user->id]);
            }
            if ($guard == 'api') {
                $token = Auth::guard('api')->login($user);
                $user->token = $token;
            }
            $user->phonecode = 966;
            $user->activate = 1;
            $user->save();
            if ($guard != 'api') {
                Auth::guard($guard)->login($user);
            }
            $items = json_decode($request->items);
            if ($items && count($items) > 0) {
                foreach ($items as $item) {
                    if (!empty($item) && $item->product_id) {
                        CartRepository::addToCart($request, $user);
                    }
                }
            }
            return response()->json(new UsersResource($user), 200);
        } else {
            $activation->activate = 1;
            $activation->save();
            $select_name = App::getLocale() == "ar" ? 'name' : 'name_en as name';
            $regions = Regions::select('id', $select_name)->with(['getStates' => function ($query) use ($select_name) {
                $query->select('id', 'region_id', $select_name);
            }])->where('country_id', 188)->where('is_archived', 0)->get();
            $client_types = ClientTypes::select('id', $select_name)->get();
            return response()->json(['message' => trans('messages.go_to_register_page'), 'regions' => $regions, 'client_types' => $client_types], 202);
        }
    }
}
