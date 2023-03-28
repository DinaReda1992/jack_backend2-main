<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests;
use App\Http\Resources\HallsResource;
use App\Http\Resources\MessageResources;
use App\Models\Addresses;
use App\Models\Arbpg;
use App\Models\BankTransfer;
use App\Models\CartItem;
use App\Models\Categories;
use App\Models\Cobons;
use App\Models\Contacts;
use App\Models\Content;
use App\Models\Countries;
use App\Models\DeviceTokens;
use App\Models\Faqs;
use App\Models\Favorite;
use App\Models\Hall;
use App\Models\Orders;
use App\Models\OrderShipments;
use App\Models\ProductRating;
use App\Models\Products;
use App\Models\Purchase_order;
use App\Models\Regions;
use App\Models\RequestProvider;
use App\Models\ServicesCategories;
use App\Models\States;
use App\Models\SupplierCategory;
use App\Models\Messages;
use App\Models\MiddleSection;
use App\Models\Notification;
use App\Models\RequestRepresentative;
use App\Models\RequestUserService;
use App\Models\Settings;
use App\Models\SiteContent;
use App\Models\SiteFeature;
use App\Models\Slider;
use App\Models\SupplierData;
use App\Models\User;
use App\Repositories\Utils\UtilsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;
use Mail;
use Illuminate\Support\Facades\Input;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Illuminate\Database\Eloquent\Builder;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use MuktarSayedSaleh\ZakatTlv\Encoder;

class TestController extends Controller
{
    public function check_coupon_category(Request $request)
    {
        $user = User::find(759);
        $code = Cobons::where('code', 'Golden5')->first();
        if ($code) {
            $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($code->created_at)) . " +" . $code->days . " days"));
            if (date('Y-m-d') > $date_of_end) {
                return   [
                    'status' => 400,
                    'message' => "عفوا انتهت صلاحية الكوبون",
                ];
            }

            $count_used = Orders::where('user_id', $user->id)->where('cobon', $request->code)->where('payment_method','<>',0)->where('status','<>' ,5)->count();

            if ($count_used) {
                return [
                    'status' => 400,
                    'message' => __('messages.coupon_used_before'),
                ];
            } else {
                $total= CartItem::where(['order_id'=>0,'user_id'=>$user->id])->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))
                    ->where('type',1)
                    ->first()->total;
                $shipment_price=Settings::find(22)->value;
                $total=$total+$shipment_price;
                $percent=$code->percent;

                $final_percent_price=($total*$percent)/100; // الخصم بالنسبه

                $final_money_price=$code->max_money;//اعلي مبلغ خصم

                if($final_percent_price>=$final_money_price ){
                    $final_cobon_money=$final_money_price;
                }else{
                    $final_cobon_money=$final_percent_price;
                }
                if( $final_money_price==0){
                    $final_cobon_money=$final_percent_price;
                }



                return [
                    'status' => 200,
                    'message' => __('messages.coupon_is_available'),
                    'money' => $final_cobon_money
                ];

            }
        } else {
            return [
                'status' => 400,
                'message' => __('messages.coupon_not_fount'),
            ];
        }

    }

}
