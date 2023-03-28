<?php

namespace App\Http\Controllers\Panel;

use FCM;
use App\Models\User;
use App\Models\Menus;
use App\Http\Requests;
use App\Models\Orders;
use App\Models\Messages;
use App\Models\Packages;
use App\Models\Projects;
use App\Models\Settings;
use App\Models\Countries;
use App\Models\Main_menus;
use App\Models\BankTransfer;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\UserServices;
use Illuminate\Http\Request;
use App\Models\SupplierCategory;
use App\Models\ServicesCategories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class UserRequestsController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }



    public function send_fcm_notification($notification_title, $notification_message, $notification55, $object_in_push, $sound = "default")
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'data' => [
                'notification_type' => (int)$notification55->type,
                'notification_title' => $notification_title,
                'notification_message' => $notification_message,
                'notification_data' => $object_in_push
            ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $token = @$notification55->getReciever->devices->count();
        $tokens = DeviceTokens::where('user_id', $notification55->reciever_id)->pluck('device_token')->toArray();
        $notification_ = @$notification55->getReciever->notification;
        if ($token > 0 && $notification_) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->status;
        $objects = User::where('user_type_id', 5)
            ->where(function ($query) use ($status) {
                if ($status == 'waiting') {
                    $query->where('approved', 0);
                } elseif ($status == 'canceled') {
                    $query->where('approved', 2);
                } else {
                    $query->whereIn('approved', [0, 2]);
                }
            })->get();
        return view('admin.user_requests.all', ['objects' => $objects]);
    }



    public function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [0,  1,  2,  3,  4,  5,  6,  7,  8,  9];
        return str_replace($arabic, $english, $number);
    }

    public function active_user($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'لا يوجد عضو بهذا العنوان');
        }
        if ($user->approved == 0) {
            $user->approved = 1;
            $user->save();
            $smsMessage = 'تم تفعيل حساب في منصة جاك';
            $final_num = $this->convertNum(ltrim($user->phone, '0'));
            $phone_number = '+' . $user->phonecode . $final_num;
            $customer_id = Settings::find(25)->value;
            $api_key = Settings::find(26)->value;
            $message_type = "OTP";
            // $resp =$this->send4SMS($customer_id,$api_key,$smsMessage,$phone_number,'GoldenRoad');
            return redirect()->back()->with('success', 'تم تفعيل المستخدم بنجاح');
        } else {
            $user->approved = 0;
            $user->save();
            return redirect()->back()->with('success', 'تم الغاء التفعيل عن المستخدم بنجاح .');
        }
    }

    public function cancel_user($id = 0, Request $request)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'لا يوجد عضو بهذا العنوان');
        }
        if ($user->approved == 0) {
            $user->approved = 2;
            $user->cancel_reason = $request->reason_of_cancel ?: '';
            $user->save();
            return redirect()->back()->with('success', 'تم رفض المستخدم بنجاح');
        } else {
            $user->approved = 0;
            $user->save();
            return redirect()->back()->with('success', 'تم الغاء التفعيل عن المستخدم بنجاح .');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        return view('admin.user_requests.add', ['object' => User::where('id', $id)->where('user_type_id', 5)->first()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object = User::where('id', $id)->where('user_type_id', 5)->first();
        $this->validate($request, [
            'username' => 'required|unique:users,username,' . $object->id . ',id',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,' . $object->id . ',id',
            'email' => $request->email ? 'required|email|unique:users,email,' . $object->id . ',id' : '',
            //            'state_id' => 'required',
            //            'currency_id' => 'required',
            'client_type' => 'required',
            //            'address' => 'required',
            'photo' => $request->photo ?  'required|image' : '',
            'password' => $request->password ? 'same:password_confirmation|min:6' : '',
            'password_confirmation' => $request->password ? 'same:password' : '',
        ]);

        $object->username = $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
        $object->country_id = $request->country_id ?: 188;
        $object->state_id = $request->state_id ?: '';
        $object->region_id = $request->region_id ?: '';
        $object->currency_id = $request->currency_id ?: 1;
        $object->phonecode = $request->phonecode ?: 966;
        $object->address = $request->address ?: '';
        $object->longitude = $request->longitude ?: '';
        $object->latitude = $request->latitude ?: '';
        $object->accept_pricing = $request->accept_pricing ? 1 : 0;
        $object->accept_estimate = $request->accept_estimate ? 1 : 0;
        $object->add_product = $request->add_product ? 1 : 0;

        $object->shop_type = $request->shop_type ?: 1;
        $object->client_type = $request->client_type ?: 1;
        $object->commercial_no = $request->commercial_no ?: '';
        $object->tax_number = $request->tax_number ?: '';
        $object->commercial_end_date = $request->commercial_end_date ?: '';

        $object->user_type_id = 5;
        $object->profit_rate = $request->profit_rate;
        if ($request->save_request == 'save_accept') {
            $object->activate = 1;
            $object->approved = 1;
        }
        //        $object->gender = $request->gender;
        if ($request->password) {
            $object->password = bcrypt($request->password);
        }

        $commercial_id = $request->file('commercial_id');
        if ($request->hasFile('commercial_id')) {
            $old_file = 'uploads/' . $object->commercial_id;
            if (is_file($old_file))    unlink($old_file);
            $fileName = 'commercial-' . time() . '-' . uniqid() . '.' . $commercial_id->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('commercial_id')->move($destinationPath, $fileName);
            $object->commercial_id = $fileName;
        }

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/' . $object->photo;
            if (is_file($old_file))    unlink($old_file);
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo = $fileName;
        }

        $object->save();

        return redirect()->back()->with('success', 'تم تعديل حساب المتجر بنجاح .');
    }
}
