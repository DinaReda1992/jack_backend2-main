<?php

namespace App\Http\Controllers\Api\Client\GlobalData;

use App\Models\Slider;
use App\Models\Content;
use App\Models\Contacts;
use App\Models\Settings;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\MainCategories;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Api\Client\Controller;
use App\Http\Requests\ContactUsRequest;

class GlobalDataController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function sliders()
    {
        $user = auth('api')->user();
        $slider = Slider::where('main_slider_id', 1)->select('*')->selectPhoto()->get();
        $cart_count = $user ? $user->cart->count() : 0;
        return response()->json(['slider' => $slider, 'cart_count' => $cart_count, 'activate' => 1, 'cancel_reason' => $user ? $user->cancel_reason : '',]);
    }

    public function getCategories()
    {
        $user = auth('api')->user();

        $objects = MainCategories::with('SubCategories')->where('stop', 0)->where('is_archived', 0)->orderBy('sort', 'asc')->get();

        return response()->json([
            'status' => 200,
            'categories' => CategoryResource::collection($objects),
            'new_notifications' => Notification::where('reciever_id', @$user->id)->where('status', 0)->count(),
            'contacts' => [
                'phone' => Settings::find(17)->value,
                'whatsapp' => Settings::find(18)->value,
                'email' => Settings::find(19)->value
            ],
            'delete_account' => (int) Settings::find(50)->value,
        ]);
        return response()->json(($objects));
    }

    public function contact_us(ContactUsRequest $request)
    {
        Contacts::create($request->validated());
        return response()->json(['status' => 200, 'message' => __('messages.Message was sent successfully')]);
    }

    public function getPages()
    {
        return response()->json(['status' => 200, 'data' => Content::select('id', 'page_name', 'content')->get()]);
    }

    public function about()
    {
        $data = app()->isLocale('ar') ? Content::find(1)->content : Content::find(1)->content_en;
        return response()->json(['data' => $data]);
    }

    public function terms()
    {
        $data = app()->isLocale('ar') ? Content::find(2)->content : Content::find(2)->content_en;
        return response()->json(['data' => $data]);
    }

    public function privacy()
    {
        $data = app()->isLocale('ar') ? Content::find(3)->content : Content::find(3)->content_en;
        return response()->json(['data' => $data]);
    }

    public function uploadPhotos(Request $request)
    {
        $arr_of_images = [];
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file_) {
                $fileName = 'file-' . time() . '-' . uniqid() . '.' . $file_->getClientOriginalExtension();
                $destinationPath = 'temp';
                $file_->move($destinationPath, $fileName);
                $arr_of_images[] = $fileName;
            }
        }
        return response()->json($arr_of_images);
    }
}
