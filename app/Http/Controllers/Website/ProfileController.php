<?php

namespace App\Http\Controllers\Website;

use App\Models\User;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\Favorite;
use Illuminate\Support\Str;
use App\Models\DeviceTokens;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use Illuminate\Http\UploadedFile;
use App\Services\SendNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth:client');
    }

    public function notifications()
    {
        $objects = Notification::whereRecieverId(auth('client')->user()->id)->orderby('id', 'desc')->get();
        return view('website.notification', compact('objects'));
    }

    public function wallet()
    {
        $objects = Balance::whereUserId(auth('client')->user()->id)->orderBy('id', 'desc')->get();
        $balance = auth('client')->user()->balance;
        return view('website.wallet', compact('objects', 'balance'));
    }

    public function wishlist(Request $request)
    {
        $favoriteProducts = auth('client')->user()->wishlist()->paginate(20);
        return view('website.wishlist', ['favoriteProducts' => $favoriteProducts]);
    }

    public function account(Request $request)
    {
        return view('website.account');
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(auth('client')->id());
        $this->validate($request, [
            'username' => 'required|unique:users,username,' . auth('client')->id() . ',id',
            'phone' => 'image',
            'email' => 'required|email|unique:users,email,' . auth('client')->id() . ',id',
        ]);

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'user-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $user->photo = $fileName;
        }
        $user->update($request->validated() + ['photo' => $file ? $fileName : $user->photo]);
        return redirect()->back()->with('success', __('messages.profile_updated_successfully'));
    }

    public function orders(Request $request)
    {
        $data = Orders::where(['user_id' => auth('client')->id()])->where('payment_method', '<>', 0)->latest()->paginate(15);
        return view('website.my-orders', compact('data'));
    }

    public function cancelOrder($id = 0)
    {
        $order = Orders::where('id', $id)->where('user_id', auth('client')->id())->where('payment_method', '!=', 0)->where('status', 0)->first();

        if (!$order) {
            return redirect()->back()->with('error', __('messages.there_is_no_order'));
        };

        $order->status = 5;
        $order->save();
        OrderShipments::where('order_id', $order->id)->update(['status' => 5]);
        if ($order->balance != null) {
            Balance::create([
                'user_id' => auth('client')->id(),'price' => -1 * $order->balance->price, 
                'balance_type_id' => 3, 'status' => 1, 'order_id' => $order->id, 'notes' => '  الغاء الطلب رقم ' . $order->id,
            ]);
        }

        SendNotification::cancelOrder($order->id);
        return redirect()->back()->with('success',__('messages.your_order_cancelled_successfully'));
    }


    private function uploadOne(UploadedFile $file, $folder = null, $filename = null, $disk = 'public')
    {
        $name = !is_null($filename) ? $filename : \Illuminate\Support\Str::random(25);

        $file->storeAs($folder, $name . "." . $file->getClientOriginalExtension(), $disk);

        return $name . "." . $file->getClientOriginalExtension();
    }

    public function updateToken(Request $request)
    {
        $user = auth('client')->user();
        if ($user && $request->has('device_token') && !empty($request->device_token)) {
            DeviceTokens::updateOrCreate(['device_token' => $request->device_token], ['user_id' => $user->id]);
        }
    }

    public function fav_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400,'message' => $validator->errors()->first(),],400);
        }

        $fav = Favorite::where(['user_id' => auth('client')->id(), 'item_id' => $request->id])->first();
        if ($fav) {
            $fav = $fav->delete();
            $message = __('messages.item_removed_from_wishlist');
        } else {
            $fav = Favorite::create(['user_id' => auth('client')->id(), 'item_id' => $request->id]);
            $message = __('messages.item_added_to_wishlist');
        }

        return response()->json([
            'wishlist_count' => auth('client')->user()->wishlist->count(),
            'message' => $message,
        ]);
    }

}
