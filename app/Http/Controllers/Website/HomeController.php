<?php

namespace App\Http\Controllers\Website;

use FCM;
use Mail;
use \Carbon\Carbon;
use App\Models\Faqs;
use App\Models\Hall;
use App\Models\User;
use App\Models\Arbpg;
use App\Models\Cobons;
use App\Models\Orders;
use App\Models\Slider;
use App\Models\States;
use App\Models\Balance;
use App\Models\Content;
use App\Models\Regions;
use App\Models\CartItem;
use App\Models\Contacts;
use App\Models\Favorite;
use App\Models\Messages;
use App\Models\Products;
use App\Models\Settings;
use App\Models\Addresses;
use App\Models\Countries;
use Illuminate\View\View;
use App\Models\Categories;
use App\Models\BankTransfer;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\PageCategory;
use App\Models\SupplierData;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use App\Models\MainCategories;
use App\Models\OrderShipments;
use App\Models\Purchase_order;
use App\Models\ProductsRegions;
use App\Models\RequestProvider;
use App\Models\SupplierCategory;
use Illuminate\Http\UploadedFile;
use App\Models\ServicesCategories;
use App\Services\SendNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Repositories\TmaraRepository;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use LaravelFCM\Message\OptionsBuilder;
use MuktarSayedSaleh\ZakatTlv\Encoder;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\MessageResources;
use App\Http\Resources\ProductResources;
use App\Repositories\CartOrderRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        //  $this->middleware('auth');
    }

    public static function uploadImage($path, $fileName, $destinationPath, $width, $height)
    {
        $image = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($destinationPath . $fileName);
    }

    // public function test()
    // {

    //     ini_set('memory_limit', '512M');
    //     $products = Products::where('id', 2229)->get();
    //     foreach ($products as $product) {
    //         $isExists = File::exists('uploads/' . $product->photo);
    //         if ($isExists) {
    //             // Log::alert('error thumb ' . $product->id);
    //             File::delete('uploads/thumb-' . $product->photo);

    //             if (File::exists('uploads/thumb-' . $product->photo)) {
    //                 continue;
    //             }

    //             $file = url('uploads/' . $product->photo);
    //             $fileName = 'thumb-' . $product->photo;
    //             $destinationPath = 'uploads/';
    //             $width = 500;
    //             $height = 500;
    //             $this->uploadImage($file, $fileName, $destinationPath, $width, $height);
    //             $product->thumb = $fileName;
    //             $product->save();
    //             // Log::info('done');
    //         } else {
    //             // Log::alert('error thumb ' . $product->id);
    //         }
    //     }
    // }

    // public function initiatePayment(Request $request)
    // {
    //     $card_number = $request->card_number;

    //     $expiry_month = $request->expiry_month;
    //     $expiry_year = $request->expiry_year;
    //     $cvv = $request->cvv;
    //     $card_holder = $request->holder_name;

    //     $arbPg = new Arbpg();

    //     //        $arbPg->test();

    //     $url = $arbPg->getmerchanthostedPaymentid($card_number, $expiry_month, $expiry_year, $cvv, $card_holder, "554", '1', 100.00);

    //     // $url= $ARB_PAYMENT_ENDPOINT_TESTING . $paymentId; //in Production use Production End Point
    //     return redirect($url);
    // }

    // public function paymentResult(Request $request)
    // {

    //     $trandata = $request->trandata;
    //     //    var_dump($trandata);
    //     $arbPg = new Arbpg();

    //     $result = $arbPg->getresult($trandata);

    //     return response()->json($result);
    // }

    public function index(Request $request)
    {
        $main_sliders = Slider::where('main_slider_id', 1)->select('*')->selectPhoto()->get();
        $top_sliders = Slider::where('main_slider_id', 2)->select('*')->take(2)->selectPhoto()->get();
        $bottom_sliders = Slider::where('main_slider_id', 3)->select('*')->take(2)->selectPhoto()->get();
        $top_icon_sliders = Slider::where('main_slider_id', 4)->select('*')->take(3)->selectPhoto()->get();
        $bottom_icon_sliders = Slider::where('main_slider_id', 5)->select('*')->take(3)->selectPhoto()->get();
        $select_name = app()->getLocale() == "en" ? 'name_en as name' : 'name_ar as name';
        $product_categories = Categories::where('stop', 0)->where('is_archived', 0)->whereHas('products')->orderBy('sort', 'asc')->get();
        request()->merge(['take' => 6, 'is_offer' => 1]);
        $products = ProductResources::collection(Products::getProducts('web')->get());
        $page_categories = PageCategory::where(function ($query) {
            $query->whereHas('products')->OrWhereHas('category')->OrWhereHas('subcategory');
        })->when($products->count() > 0, function ($query) {
            $query->orWhere('is_offer', 1);
        })->with('products', 'category.products', 'subcategory.products')
            ->select($select_name, 'id', 'is_offer', 'category_id', 'sub_category_id')->get();
        request()->merge(['take' => 4, 'is_offer' => 1, 'sort' => 'random']);
        $products = json_encode($products);
        $product = Products::getProducts('web')->first();
        $random_product  = $product ? json_encode(ProductResources::make($product)) : $product;
        return view('website.home', compact('random_product', 'main_sliders', 'top_sliders', 'bottom_sliders', 'top_icon_sliders', 'bottom_icon_sliders', 'page_categories', 'product_categories', 'products'));
    }

    public function productPage($id = 0)
    {
        $product = Products::getProducts('web')->where('id', $id)->first();
        if (!$product) {
            abort(404);
        }

        $ratings = ProductRating::where('item_id', $product->id)->paginate(20);
        $relatedProducts = ProductResources::collection(Products::getProducts('web')->where('category_id', $product->category_id)
            ->where('id', '<>', $product->id)->paginate(12));
        return view('website.product_page', ['product' => json_encode(ProductResources::make($product)), 'ratings' => $ratings, 'relatedProducts' => json_encode($relatedProducts)]);
    }

    // public function getSuppliersCategory(Request $request)
    // {
    //     $category_id = $request->category_id;

    //     $suppliers = User::whereIn('id', function ($query) use ($category_id) {
    //         $query->select('user_id')
    //             ->from(with(new SupplierCategory())->getTable())
    //             ->where('category_id', $category_id);
    //     })->paginate(15);
    //     foreach ($suppliers as $supplier) {

    //         echo View::make("items.supplier")->with('supplier', $supplier)->render();
    //     }

    //     return view('items.supplier')->with('suppliers', $suppliers);
    // }

    public function sendMessageEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'email' => 'email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = new Contacts();
        $user->subject = $request->subject ?: 'رسالة جديدة';
        $user->message = $request->message;
        $user->name = $request->name ?: '';
        $user->email = $request->email ?: '';
        $user->phone = $request->phone ?: '';
        $user->save();
        $system_email = Settings::find(19)->value;
        // if ($system_email) {
        try {
            \Illuminate\Support\Facades\Mail::send('emails.contact', ['subject' => $user->subject, 'contact_message' => $user->message], function ($m) use ($user) {
                $m->from('support@jak.sa.com', 'Falcon parts contact');
                $m->to('mohamedfrouh96@gmail.com', $user->username)->subject($user->subject);
            });
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
        // }

        return redirect()->back()->with('success', 'تم الارسال بنجاح');
    }

    // public function searchHalls(Request $request)
    // {
    //     $user_id = Auth::User() ? Auth::id() : 0;
    //     $latitude = $request->search_lat ?: (isset($_COOKIE['latitude']) ?: '24.7135517');
    //     $longitude = $request->search_lng ?: isset($_COOKIE['longitude']) ?: '46.6752957';
    //     $halls = Hall::where(function ($query) use ($request) {
    //         if ($request->input('title')) {
    //             $query->where('title', 'LIKE', "%" . $request->input('title') . "%");
    //             $query->orWhere('title_en', 'LIKE', "%" . $request->input('title') . "%");
    //         }
    //     })
    //         ->where(function ($query) use ($request) {
    //             if ($request->input('chairs')) {
    //                 $query->where('chairs', '>=', $request->input('chairs'));
    //             }
    //             if ($request->input('hall_type')) {
    //                 $query->whereIn('id', function ($query) use ($request) {
    //                     $query->select('hall_id')
    //                         ->from(with(new SupplierCategory())->getTable())
    //                         ->where('category_id', $request->hall_type);
    //                 });
    //             }
    //         })
    //         ->where('status', 1)
    //         ->select(
    //             "id",
    //             "title",
    //             'address',
    //             'longitude',
    //             'latitude',
    //             'currency',
    //             'chairs',
    //             'price_per_hour',
    //             DB::raw("6371 * acos(cos(radians(" . $latitude . "))
    //     * cos(radians(halls.latitude))
    //     * cos(radians(halls.longitude) - radians(" . $longitude . "))
    //     + sin(radians(" . $latitude . "))
    //     * sin(radians(halls.latitude))) AS distance")
    //         )
    //         ->orderBy("distance", 'ASC')
    //         ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
    //         ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_id . ' AND likes.hall_id=halls.id) as is_liked')
    //         ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
    //         ->paginate(20);
    //     return view('hallsResults', ['halls' => $halls]);
    // }

    // public function getActivate($activation_code = '')
    // {
    //     $user = User::where('activation_code', $activation_code)->first();
    //     if (!$user) {
    //         return redirect('/')->with('error', 'عذرا هذا الرابط غير صالح');
    //     } else {
    //         $user->activate = 1;
    //         $user->save();
    //         return redirect('/')->with('success', trans('local.activate_success'));
    //     }
    // }

    // public function resendActivation(Request $request)
    // {
    //     $user = Auth::User();
    //     if ($user->activate) {
    //         return \redirect()->back()->with("error", "تم تفعيل حسابك سابقا");
    //     }
    //     $activation_code = sha1($user->username) . time();
    //     $user->activation_code = $activation_code;
    //     $user->save();
    //     \Illuminate\Support\Facades\Mail::send('emails.reminder', ['activation_code' => $activation_code, 'username' => $user->username, 'is_site' => true], function ($m) use ($user) {
    //         $m->from('info@meetings-room.com', 'Meetings rooms and halls');
    //         $m->to($user->email, $user->username)->subject(__('messages.activate_account'));
    //     });

    //     return redirect()->back()->with("success", trans('local.activate_sent'));
    // }

    public function page($slug)
    {
        $page = Content::where('slug', $slug)->first();
        if (!$page) {
            return abort(404);
        }
        return view('website.page', ['object' => $page]);
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

    public function appPage(Request $request, $slug)
    {
        $select_name = $request->lang == "en" ? 'page_name_en as page_name' : 'page_name';
        $select_content = $request->lang == "en" ? 'content_en as content' : 'content';

        $page = Content::select('id', $select_name, $select_content, 'meta_title', 'meta_keywords')->where('slug', $slug)->first();
        if (!$page) {
            return abort(404);
        }
        return view('app-page', ['object' => $page]);
    }

    // public function favorites(Request $request)
    // {
    //     $tax = floatval(Settings::find(38)->value);

    //     $favoriteProducts = Products::select('products.*', DB::raw('ROUND((price +(price * ' . ($tax / 100) . ')),2) as price'))->where('stop', 0)->where('is_archived', 0)->whereIn('id', function ($query) {
    //         $query->select('item_id')
    //             ->from(with(new Favorite())->getTable())
    //             ->where('user_id', \auth('client')->id());
    //     })->paginate(20);
    //     return view('favorites', ['favoriteProducts' => $favoriteProducts]);
    // }

    public function wishlist(Request $request)
    {
        $favoriteProducts = auth('client')->user()->wishlist()->paginate(20);
        return view('website.wishlist', ['favoriteProducts' => $favoriteProducts]);
    }

    public function contact(Request $request)
    {
        $contacts = Settings::select('option_name', 'name', 'value')->where('input_type', 'contact_options')
            ->orWhere('input_type', 'app_links')
            ->orWhere('input_type', 'social_media')
            ->get();
        $contact_phone = '';
        $contact_whatsapp = '';
        $contact_email = '';
        $contact_address = '';
        $facebook = '';
        $instagram = '';
        $youtube = '';
        $twitter = '';
        foreach ($contacts as $contact) {
            if ($contact->option_name == 'phone') {
                $contact_phone = $contact->value;
            }

            if ($contact->option_name == 'whatsapp') {
                $contact_whatsapp = $contact->value;
            }

            if ($contact->option_name == 'email') {
                $contact_email = $contact->value;
            }

            if ($contact->option_name == 'address') {
                $contact_address = $contact->value;
            }

            if ($contact->option_name == 'facebook') {
                $facebook = $contact->value;
            }

            if ($contact->option_name == 'instagram') {
                $instagram = $contact->value;
            }

            if ($contact->option_name == 'youtube') {
                $youtube = $contact->value;
            }

            if ($contact->option_name == 'twitter') {
                $twitter = $contact->value;
            }
        }

        return view('website.contact', ['email' => $contact_email, 'whatsapp' => $contact_whatsapp, 'address' => $contact_address]);
    }

    // public function messages(Request $request)
    // {
    //     return view('messages');
    // }

    // public function notifications(Request $request)
    // {
    //     return view('notifications');
    // }

    // public function payment(Request $request)
    // {
    //     return view('payment');
    // }

    public function account(Request $request)
    {
        return view('website.account');
    }

    // public function checkout(Request $request)
    // {
    //     $user = auth('client')->user();
    //     $current_balance = Balance::where('user_id', $user->id)->sum('price');
    //     $user_address = Addresses::where(['user_id' => $user->id, 'is_home' => 1, 'is_archived' => 0])->first();
    //     $address_id = @$user_address ? $user_address->region_id : 0;
    //     $current_items = CartItem::where('user_id', $user->id)->where('type', 1)
    //         ->with(['product' => function ($q) {
    //             $q->withoutGlobalScope('user');
    //         }])->where('order_id', 0)->where('type', 1)->get();
    //     $messages = [];
    //     foreach ($current_items as $item) {
    //         $product = $item->product;
    //         $edit_mount = $product->calculateMinWareHouseQty($item->quantity);
    //         // dd($edit_mount);
    //         if ($product->quantity == 0 || $product->stop == 1 || $edit_mount == -1) {
    //             $item->delete();
    //             $messages[] = $product->title . ' لم يعد متاح الان ';
    //         } elseif ($edit_mount == -2) {
    //             $item->quantity = $product->min_quantity;
    //             $item->save();
    //             $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
    //         } elseif ($edit_mount > 0 && $edit_mount != $item->quantity) {
    //             $item->quantity = $edit_mount;
    //             $item->save();
    //             $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
    //         } elseif ($item->quantity > @$product->quantity) {
    //             $item->quantity = $product->quantity;
    //             $item->save();
    //             $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
    //         }
    //         // if ($product->quantity == 0 || $product->stop==1 ) {
    //         //     $item->delete();
    //         //     $messages[] = $product->title . ' لم يعد متاح الان ';
    //         // }
    //         if ($address_id != 0) {
    //             if ($product->has_regions1 == 1) {
    //                 $pro = ProductsRegions::where('product_id', $product->id)->where('region_id', $address_id)->first();
    //                 if (!$pro) {
    //                     $item->delete();
    //                     $messages[] = $product->title . ' لم يعد متاح الان فى منطقتك الحالية';
    //                 }
    //                 $pro = ProductsRegions::where('product_id', $product->id)->where('state_id', $user_address->state_id)->first();
    //                 if (!$pro) {
    //                     $item->delete();
    //                     $messages[] = $product->title . ' لم يعد متاح الان فى مدينتك الحالية';
    //                 }
    //             }
    //             /* $pro=Products::whereId($product->id)->whereHas('product_regions', function (Builder $query)use($address_id) {
    //                         $query->where('region_id', $address_id);
    //                     })->first();
    //                 if($pro){
    //                     $item->delete();
    //                     $messages[] = $product->title . ' لم يعد متاح الان فى منطقتك الحالية';
    //                 }*/
    //         } elseif ($item->quantity > $product->quantity) {
    //             $item->quantity = $product->quantity;
    //             $item->save();
    //             $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
    //         }
    //         $price = $product->price_after_discount ?: $product->price;
    //         if ($product->quantity && $item->price != $price) {
    //             $item->price = $price;
    //             $item->save();
    //             $messages[] = ' تم تعديل سعر المنتج ' . $product->title;
    //         }
    //         if ($product->stop == 1) {
    //             $item->delete();
    //             $messages[] = ' تم حذف المنتج  ' . $product->title . ' لعدم توفره ';
    //         }
    //     }
    //     /**/
    //     $items = CartItem::where('type', 1)->where(['status' => 0, 'order_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
    //     $items = json_encode(CartItemResource::collection($items));
    //     $address = Addresses::where(['user_id' => $user->id, 'is_home' => 1])->first();
    //     if (!$address) {
    //         return redirect('/addresses');
    //     }
    //     $shipment_price = Settings::find(22)->value;
    //     return view('website.checkout', compact('items', 'shipment_price', 'address', 'messages', 'current_balance'));
    // }

    public function update_profile(Request $request)
    {
        $user = User::find(auth('client')->id());
        $this->validate($request, [
            'username' => 'required|unique:users,username,' . Auth::user()->id . ',id',
            'phone' => 'image',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id . ',id',
        ]);

        if ($request->password) {
            $this->validate($request, [
                'password' => 'sometimes|min:6',
                'password_confirmation' => 'same:password',
            ]);
        }
        $user->username = $request->input('username');
        $user->email = $request->email ?: '';

        if ($request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'user-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $user->photo = $fileName;
        }
        $user->save();

        return redirect()->back()->with('success', 'تم تعديل بيانات الحساب بنجاح .');
    }

    public function faq(Request $request)
    {
        $objects = Faqs::all();
        return view('faq', ['objects' => $objects]);
    }

    public function appFaq(Request $request)
    {
        $objects = Faqs::all();
        return view('app-faq', ['objects' => $objects]);
    }

    // public function get_messages_user(Request $request)
    // {
    //     $user = Auth::User();
    //     $last_messages = Messages::where(function ($query1) use ($user) {
    //         $query1->where(function ($query) use ($user) {
    //             $query->where('reciever_id', $user->id);
    //         })->orWhere(function ($query) use ($user) {
    //             $query->where('sender_id', $user->id);
    //         });
    //     })->orderBy('id', 'DESC')
    //         ->first();
    //     $hall_id = $request->hall ?: ($last_messages ? $last_messages->hall_id : 0);
    //     $this_hall = Hall::find($hall_id);
    //     $all_messages = Messages::select("*")
    //         ->join(\DB::raw('(SELECT MAX(Id) as id,sender_id+reciever_id as mm FROM messages where sender_id=' . $user->id . ' or reciever_id=' . $user->id . ' GROUP BY hall_id) AS n2'), function ($join) {
    //             $join->on('messages.id', '=', 'n2.id');
    //         })
    //         ->with(['getSenderUser' => function ($query) {
    //             $query->select('id', 'username', 'user_type_id');
    //             $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
    //         }, 'getRecieverUser' => function ($query) {
    //             $query->select('id', 'username', 'user_type_id');
    //             $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
    //         }, 'hall' => function ($query) {
    //             $query->select('id', 'title');
    //         }])
    //         ->whereIn('sender_id', function ($query) {
    //             $query->select('id')
    //                 ->from(with(new User())->getTable());
    //         })
    //         ->whereIn('reciever_id', function ($query) {
    //             $query->select('id')
    //                 ->from(with(new User())->getTable());
    //         })
    //         ->where('sender_id', '=', $user->id)
    //         ->orWhere('reciever_id', $user->id)
    //         ->orderBy('created_at', 'DESC')
    //         ->paginate(10);

    //     $messages = Messages::where(function ($query1) use ($user) {
    //         $query1->where(function ($query) use ($user) {
    //             $query->where('reciever_id', $user->id);
    //         })->orWhere(function ($query) use ($user) {
    //             $query->where('sender_id', $user->id);
    //         });
    //     })
    //         ->where('hall_id', $hall_id)
    //         ->with(['getSenderUser' => function ($query) {
    //             $query->select('id', 'username', 'user_type_id');
    //             $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
    //         }, 'getRecieverUser' => function ($query) {
    //             $query->select('id', 'username', 'user_type_id');
    //             $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
    //         }, 'hall' => function ($query) {
    //             $query->select('id', 'title');
    //         }])
    //         ->orderBy('id', 'DESC')
    //         ->paginate(30);
    //     foreach ($messages as $messageasd) {

    //         Messages::where('id', $messageasd->id)->where('reciever_id', $user->id)->where('status', 0)
    //             ->update(['status' => 1]);
    //     }
    //     return view('messages', ['all_messages' => $all_messages, 'messages' => $messages, 'hall_id' => $hall_id, 'this_hall' => $this_hall]);
    // }

    // public function add_message(Request $request)
    // {
    //     $user = Auth::User();

    //     $validator = Validator::make($request->all(), [
    //         'to' => 'required',
    //         'hall_id' => 'required',
    //         'message' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }
    //     $hall = Hall::find($request->hall_id);
    //     if (!$hall && ($hall->user_id != Auth::Id() || $hall->user_id != $request->id)) {
    //         return abort(404);
    //     }
    //     $comment = new Messages;
    //     $comment->sender_id = $user->id;
    //     $comment->reciever_id = $request->to;
    //     $comment->hall_id = $request->hall_id;
    //     $comment->message = $request->input('message') ? $request->input('message') : '';
    //     $comment->save();
    //     $comment = Messages::find($comment->id);
    //     $comment->load(['getSenderUser' => function ($query) {
    //         $query->select('id', 'username', 'user_type_id');
    //         $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
    //     }]);
    //     $comment->load(['getRecieverUser' => function ($query) {
    //         $query->select('id', 'username', 'user_type_id');
    //         $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
    //     }]);
    //     if (\app()->getLocale() == "ar") {
    //         $comment->load(['hall' => function ($query) {
    //             $query->select('id', 'title');
    //         }]);
    //     } else {
    //         $comment->load(['hall' => function ($query) {
    //             $query->select('id', 'title_en as title');
    //         }]);
    //     }

    //     $comment_a = new MessageResources($comment);

    //     $notification55 = new Notification();
    //     $notification55->sender_id = $comment->sender_id;
    //     $notification55->reciever_id = $comment->reciever_id;
    //     //        $notification55->order_id = $comment->order_id;
    //     $notification55->type = 2;
    //     $notification55->message = "قام " . $comment->getSenderUser->username . " بارسال رسالة لك";
    //     //        $notification55->message_en =  $comment->getSenderUser->username . " send you message";
    //     $notification55->save();

    //     $notification_title = "رسالة جديدة";
    //     $notification_message = $notification55->message;

    //     if (@$notification55->getReciever->notification == 1) {
    //         $this->send_fcm_notification($notification_title, $notification_message, $notification55, $comment_a, 'default');
    //     }

    //     return redirect()->back()->with('success', 'تم ارسال الرساله بنجاح .');
    // }

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
                'notification_type' => (int) $notification55->type,
                'notification_title' => $notification_title,
                'notification_message' => $notification_message,
                'notification_data' => $object_in_push,
            ],
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
    public function getRegions($id = 0)
    {
        echo "<option value=''>اختر المنطقة</option>";
        foreach (Regions::where('country_id', $id)->get() as $state) {
            echo "<option value='" . $state->id . "'>" . $state->name . "</option>";
        }
    }
    public function getRegionStates($id = 0)
    {
        echo "<option value=''>اختر المدينة</option>";
        foreach (States::where('region_id', $id)->get() as $state) {
            echo "<option value='" . $state->id . "'>" . $state->name . "</option>";
        }
    }

    public function addresses()
    {
        $addresses = Addresses::where(['user_id' => auth('client')->id(), 'is_archived' => 0])->get();
        $country = Countries::where('id', auth('client')->user()->country_id)->select('id', 'name')->with('getRegions.getStates:id,name,country_id,region_id')
            ->with('getRegions:id,name,country_id')->get();
        return view('website.addresses', compact('addresses', 'country'));
    }

    public function store_address(Request $request)
    {
        $input = $request->all();
        $input['user_id'] = \auth('client')->id();
        $addresses_count = Addresses::where('user_id', auth('client')->id())->count();

        if ($addresses_count == 0) {
            $input['is_home'] = 1;
        }
        Addresses::create($input);
        $addresses = Addresses::where(['user_id' => auth('client')->id(), 'is_archived' => 0])->get();
        return response()->json([
            'message' => 'تم اضافة العنوان بنجاح',
            'addresses' => $addresses,
        ]);
    }

    public function update_address(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $input = $request->all();
        $input['user_id'] = \auth('client')->id();
        $address = Addresses::where('id', $id)->where('user_id', \auth('client')->id())->first();
        $address->update($input);
        $addresses = Addresses::where(['user_id' => auth('client')->id(), 'is_archived' => 0])->get();
        return response()->json([
            'message' => 'تم تعديل العنوان بنجاح',
            'addresses' => $addresses,
        ]);
    }

    public function delete_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $input['user_id'] = \auth('client')->id();
        $address = Addresses::where('id', $request->id)->where('user_id', \auth('client')->id())->first();
        //$addresses=Addresses::where(['user_id'=>auth('client')->id(),'is_archived'=>1])->get();
        /*if($address->is_home==1){
        return response()->json([
        'message' => 'تم حذف العنوان بنجاح',
        'addresses' =>$addresses,
        ]);
        }*/
        $address->update(['is_archived' => 1]);
        //        $address->delete();
        $addresses = Addresses::where(['user_id' => auth('client')->id(), 'is_archived' => 0])->get();
        return response()->json([
            'message' => 'تم حذف العنوان بنجاح',
            'addresses' => $addresses,
        ]);
    }

    public function is_home(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $address = Addresses::where('id', $request->id)->where('user_id', auth('client')->id())->first();
        if ($address) {
            $addresses = Addresses::where('user_id', auth('client')->id())->update(['is_home' => 0]);
            $address->is_home = 1;
            $address->save();
        }

        $addresses = Addresses::where(['user_id' => auth('client')->id(), 'is_archived' => 0])->get();
        return response()->json([
            'message' => 'تم تغيير العنوان للرئيسي بنجاح',
            'addresses' => $addresses,
            'address' => $address,
        ]);
    }

    /*CART*/
    public function store_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $user_address = Addresses::where(['user_id' => auth('client')->id(), 'is_home' => 1, 'is_archived' => 1])->first();
        $address_id = @$user_address->id ? $user_address->id : 0;

        $product = Products::whereId($request->id)
            ->first();

        // $mount = $product->calculateMinWareHouseQty(intval($request->quantity));
        // if ($mount == -1) {
        //     return response()->json(
        //         [
        //             'status' => 400,
        //             'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
        //             'min_quantity' => $product->quantity - $product->min_warehouse_quantity,
        //         ],
        //         202
        //     );
        // }
        // if ($mount == -2) {
        //     return response()->json(
        //         [
        //             'status' => 400,
        //             'message' => ' اقل كمية متاحة للبيع هي ' . $product->min_quantity,
        //             'min_quantity' => $product->min_quantity,
        //         ],
        //         202
        //     );
        // }
        // if ($mount > $product->quantity) {
        //     return response()->json(
        //         [
        //             'status' => 400,
        //             'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
        //             'min_quantity' =>  $product->quantity - $product->min_warehouse_quantity,
        //         ],
        //         201
        //     );
        // }
        // if (intval($product->min_quantity) > intval($request->quantity)) {
        //     return response()->json(
        //         [
        //             'status' => 400,
        //             'message' => ' اقل كمية متاحة للبيع هي ' . $product->min_quantity,
        //             'min_quantity' => $product->min_quantity,
        //         ],
        //         201
        //     );
        // }

        return CartRepository::addToCart($request, auth('client')->user());
    }

    public function update_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $user_address = Addresses::where(['user_id' => auth('client')->id(), 'is_home' => 1, 'is_archived' => 1])->first();
        $address_id = @$user_address->id ? $user_address->id : 0;

        $input = $request->all();
        $input['user_id'] = \auth('client')->id();
        $items = CartItem::whereHas('product')->where('item_id', $request->id)->where('user_id', auth('client')->id())->where('shipment_id', 0)->first();
        $product = Products::whereId($items->item_id)->first();

        // $mount = $product->calculateMinWareHouseQty(intval($request->quantity));
        // if ($mount == -1) {
        //     $items->update(['quantity' => $product->quantity - $product->min_warehouse_quantity, 'price' => $product->price] + $input);
        //     $items = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
        //     return response()->json(
        //         [
        //             'status' => 400,
        //             'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
        //             'min_quantity' => $product->quantity - $product->min_warehouse_quantity,
        //             'items' => CartItemResource::collection($items),
        //         ],
        //         202
        //     );
        // }
        // if ($mount == -2) {
        //     $items->update(['quantity' => $product->min_quantity] + $input);
        //     $items = CartItem::where('type', 1)->where(['status' => 0, 'order_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
        //     return response()->json(
        //         [
        //             'status' => 400,
        //             'message' => ' اقل كمية متاحة للبيع هي ' . $product->min_quantity,
        //             'min_quantity' => $product->min_quantity,
        //             'items' => CartItemResource::collection($items),
        //         ],
        //         202
        //     );
        // }
        // if ($mount > $product->quantity) {
        //     $items = CartItem::where('type', 1)->where(['status' => 0, 'order_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
        //     return response()->json(
        //         [
        //             'status' => 400,
        //             'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
        //             'min_quantity' => $product->quantity,
        //             'items' => CartItemResource::collection($items),
        //         ],
        //         400
        //     );
        // }
        // if (intval($product->min_quantity) > intval($request->quantity)) {
        //     $items = CartItem::where('type', 1)->where(['status' => 0, 'order_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
        //     return response()->json(
        //         [
        //             'status' => 400,
        //             'message' => ' اقل كمية متاحة للبيع هي ' . $product->min_quantity,
        //             'min_quantity' => $product->min_quantity,
        //             'items' => CartItemResource::collection($items),
        //         ],
        //         202
        //     );
        // }
        return CartRepository::addToCart($request, auth('client')->user());
    }

    public function getCart()
    {
        $items = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
        $items = json_encode(CartItemResource::collection($items));
        return view('website.cart', compact('items'));
    }

    public function delete_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $item = CartItem::where('id', $request->id)->where('user_id', \auth('client')->id())->first();
        if ($item) {
            $item->delete();
        }

        $items = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
        return response()->json([
            'message' => 'تم حذف المنتج من السلة بنجاح',
            'items' => CartItemResource::collection($items),
            'count_items' => $items->count(),
        ]);
    }

    public function fav_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $fav = Favorite::where(['user_id' => auth('client')->id(), 'item_id' => $request->id])->first();
        if ($fav) {
            $fav = $fav->delete();
            $message = 'تم الحذف من المفضلة';
        } else {
            $fav = Favorite::create(['user_id' => auth('client')->id(), 'item_id' => $request->id]);
            $message = 'تم الأضافة إلي المفضلة';
        }

        return response()->json([
            'wishlist_count' => auth('client')->user()->wishlist->count(),
            'message' => $message,
        ]);
    }

    public function get_search(Request $request)
    {
        $products = Products::getProducts('web')->paginate(8);
        $products->{$products} = ProductResources::collection($products);
        if ($request->ajax() && $request->ajax == 1) {
            return response()->json(['status' => 200, 'data' => $products, 'url' => trim($_SERVER['QUERY_STRING'], '&ajax=1!')]);
        }

        $categories = MainCategories::orderBy('sort')->where('stop', 0)->with('subCategories')->withCount('subCategories')->get();
        $categories = json_encode(CategoryResource::collection($categories));
        return view('website.search', compact('categories'));
    }

    public function getApp(Request $request)
    {
        // android store
        if (preg_match('#android#i', $_SERVER['HTTP_USER_AGENT'])) {
            header('Location:' . Settings::find(27)->value);
            exit;
        }

        // ios
        if (preg_match('#(iPad|iPhone|iPod)#i', $_SERVER['HTTP_USER_AGENT'])) {
            header('Location:' . Settings::find(28)->value);
            exit;
        }
        header('Location:' . Settings::find(27)->value);
    }

    public function fetch(Request $request)
    {
        if ($request->get('query')) {
            request()->merge(['keyword' => $request->get('query')]);
            $data = Products::getProducts('web')->get();
            if ($request->mobile != 1) {
                $output = '<ul id="search_box_result" class="dropdown-menu" aria-labelledby="navbarDropdown" style="display:block; position:absolute;width: 268px">';
                foreach ($data as $row) {
                    $title=app()->getLocale() == "ar" ? $row->title : $row->title_en;
                    $output .= '<li class="px-2 py-2"><a class="text-dark" href="/product/' . $row->id . '">
                        <img class="mx-1 rounded-circle" src="/uploads/' . $row->photo . '" width="30" height="30"  />' . $title . '</a></li>';
                }
                if (count($data) == 0) {
                    $output .= '<li class="px-2 py-2" class="alert alert-danger text-center">' . __('dashboard.not_found_product') . '</li>';
                }
                $output .= '</ul>';
                echo $output;
            } else {
                $output = '';
                foreach ($data as $row) {
                    $title=app()->getLocale() == "ar" ? $row->title : $row->title_en;
                    $output .= '<a class="dropdown-item" href="/product/' . $row->id . '">
                        <img class="mx-1 rounded-circle" src="/uploads/' . $row->photo . '" width="30" height="30"  />' . $title . '</a>';
                }
                echo $output;
            }
        }
    }

    public function orders(Request $request)
    {
        $data = Orders::where(['user_id' => auth('client')->id()])->where('payment_method', '<>', 0)->latest()->paginate(15);
        return view('website.my-orders', compact('data'));
    }

    // public function get_providers(Request $request)
    // {
    //     $providers = User::where('user_type_id', 3)->where(['block' => 0, 'is_archived' => 0])
    //         ->whereHas('supplier', function (Builder $query) {
    //             return $query->where('stop', 0);
    //         })
    //         ->when($request->category_id, function ($q) use ($request) {
    //             return $q->whereHas('cats', function (Builder $query) use ($request) {
    //                 return $query->whereIn('category_id', json_decode($request->category_id) ?: []);
    //             });
    //         })

    //         ->when($request->search, function ($q) use ($request) {
    //             return $q->where(function ($query) use ($request) {
    //                 $query->where('title', 'LIKE', $request->search . '%');
    //                 // $query->orWhere('description', 'LIKE', '%' . $request->search . '%');
    //             });
    //         })
    //         ->select('id', 'username')
    //         ->with('cats')
    //         ->with(['supplier' => function ($query) {
    //             $query->select('*');
    //             $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
    //         }])
    //         ->withCount('products')
    //         ->paginate(9);
    //     if ($request->ajax() && $request->ajax == 1) {
    //         return response()->json(
    //             [
    //                 'status' => 200,
    //                 'data' => $providers,
    //                 'url' => trim($_SERVER['QUERY_STRING'], '&ajax=1!'),
    //             ]
    //         );
    //     }
    //     $categories = ServicesCategories::orderBy('sort')
    //         ->select('name', 'sort', 'id', 'photo')->get();
    //     return view('providers', compact('categories'));
    // }

    public function get_products(Request $request, $id)
    {

        $tax = floatval(Settings::find(38)->value);
        $products = Products::where('provider_id', $id)
            //->select('*')
            ->select('products.*', 'supplier_data.stop as supplier_stop', 'products.stop as products_stop', DB::raw('ROUND((price +(price * ' . ($tax / 100) . ')),2) as price'))
            ->where('products.is_archived', 0)->where('products.stop', 0)
            ->join('supplier_data', 'products.provider_id', 'supplier_data.user_id')

            ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
            ->paginate(9);
        if ($request->ajax() && $request->ajax == 1) {
            return response()->json(
                [
                    'status' => 200,
                    'data' => $products,
                    'url' => trim($_SERVER['QUERY_STRING'], '&ajax=1!'),
                ]
            );
        }
        $supplier = User::where('id', $id)->where('user_type_id', 3)->whereHas('supplier')
            ->select('id', 'username')
            ->with('cats')
            ->with(['supplier' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])->first();
        return view('provider-profile', compact('supplier'));
    }

    // public function become_provider(Request $request)
    // {
    //     $countries = Countries::select('id', 'name')->with('getRegions.getStates:id,name,country_id,region_id')
    //         ->with('getRegions:id,name,country_id')->get();
    //     return view('become-provider', compact('countries'));
    // }

    // public function become_provider_post(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'phone' => 'required',
    //         //            'email' => 'required',
    //         'country_id' => 'required',
    //         'region_id' => 'required',
    //         'state_id' => 'required',
    //     ]);

    //     if (!$validator->passes()) {
    //         return response()->json([
    //             'status' => 201,
    //             'errors' => $validator->errors()->all(),
    //         ], 201);
    //     }

    //     $input = $request->all();
    //     $data = RequestProvider::create($input);
    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'تم اضافة طلبك بنجاح',
    //     ], 200);
    // }

    public function invoice($code)
    {
        $tax = floatval(Settings::find(38)->value);
        $order = Orders::select('*')
            ->where('orders.short_code', $code)
            ->with('shipments.cart_items', 'address', 'user')
            ->whereHas('shipment')->with('cart_items')->wherehas('cart_items')->first();
        if (!$order) {
            return abort(404);
        }
        //$total= CartItem::where('order_id',$order->id)->first()->total;
        //        $order->final_price = $total+$shipment_price+(($total+$shipment_price)*$taxs/100);
        //        $order->order_price = $total;
        //        $order->delivery_price = $shipment_price;
        //        $order->taxes = (($total+$shipment_price)*$taxs/100);
        //        $order->save();
        $object = $order;
        $final_price = $object->final_price;
        $encoder = new Encoder();
        $qr_signature = $encoder->encode(
            'شركة الطريق الذهبي للتوزيع' . '',
            "310414274700003",
            date('Y-m-d H:i:s', strtotime($object->created_at)),
            round($final_price, 2),
            round($object->order_vat, 2)
        );
        return view('admin.orders.invoice', compact('object', 'qr_signature', 'tax'));
    }

    public function cart_invoice($code)
    {
        $tax = floatval(Settings::find(38)->value);
        $order = Orders::select(
            'orders.*',
            \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as subtotal'),
            \Illuminate\Support\Facades\DB::raw('((sum(cart_items.price * cart_items.quantity)+orders.delivery_price)*' . $tax . '/100) as order_vat')
        )
            ->where('orders.short_code', $code)
            ->join('cart_items', 'cart_items.order_id', 'orders.id')

            ->with('shipments.cart_items', 'address', 'user')->first();
        //$total= CartItem::where('order_id',$order->id)->first()->total;
        //        $order->final_price = $total+$shipment_price+(($total+$shipment_price)*$taxs/100);
        //        $order->order_price = $total;
        //        $order->delivery_price = $shipment_price;
        //        $order->taxes = (($total+$shipment_price)*$taxs/100);
        //        $order->save();
        $object = $order;
        $final_price = $object->subtotal + $object->cobon_discount + $object->delivery_price + $object->order_vat;
        $encoder = new Encoder();
        $qr_signature = $encoder->encode(
            'شركة الطريق الذهبي للتوزيع' . '',
            "310414274700003",
            date('Y-m-d H:i:s', strtotime($object->created_at)),
            round($final_price, 2),
            round($object->order_vat, 2)
        );
        return view('admin.orders.cart-invoice', compact('object', 'qr_signature'));
    }

    // public function purchase_invoice($code)
    // {
    //     $order = Purchase_order::where('code', $code)->with('purchase_item', 'provider', 'orderStatus')->first();
    //     $object = $order;
    //     return view('purchase_invoice', compact('object'));
    // }

    public function cancle_client_order($id = 0, Request $request)
    {
        $order = Orders::where('id', $id)->where('user_id', auth('client')->id())
            ->where('payment_method', '!=', 0)->where('status', 0)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'طلب غير موجود او تم الغاءه بالفعل .');
        };
        $order->status = 5;
        $order->save();
        OrderShipments::where('order_id', $order->id)->update(['status' => 5]);
        if ($order->balance != null) {
            $balance = new Balance();
            $balance->user_id = auth('client')->id();
            $balance->price = -1 * $order->balance->price;
            $balance->balance_type_id = 3;
            $balance->status = 1;
            $balance->order_id = $order->id;
            $balance->notes = '  الغاء الطلب رقم ' . $order->id;
            $balance->save();
        }

        if ($order->payment_method == 8 && $order->tmara_capture_id !== null) {
            $tmara = new TmaraRepository();
            $tmara->refund($order->tmara_capture_id, $order->tmara_order_id, $order->final_price);
        }

        SendNotification::cancelOrder($order->id);
        return redirect()->back()->with('success', 'تمت الغاء الطلب بنجاح .');
    }

    public function upload_invoice($id, Request $request)
    {
        $order = Orders::where('id', $id)->where('user_id', auth('client')->id())->first();
        $transfer = BankTransfer::where('order_id', $order->id)->first();
        if (!$transfer) {
            $transfer = new BankTransfer();
            $transfer->order_id = $order->id;
            $transfer->user_id = $order->user_id;
        }
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'transfer-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $transfer->photo = $fileName;
            $transfer->save();
            $order->payment_method = 4;
            $order->save();
            return redirect()->back()->with('success', 'تم اضافة صورة التحويل بنجاح');
        } else {
            return redirect()->back()->with('error', 'لا يوجد صورة!');
        }
    }

    public function uploadOne(UploadedFile $file, $folder = null, $filename = null, $disk = 'public')
    {
        $name = !is_null($filename) ? $filename : \Illuminate\Support\Str::random(25);

        $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            $disk
        );
        return $name . "." . $file->getClientOriginalExtension();
    }

    // public function recalc_order1()
    // {
    //     $objects = CartItem::select(
    //         'cart_items.shop_id',
    //         'cart_items.user_id',
    //         'cart_items.type',
    //         'users.username as shop_name',
    //         'users.shipment_price',
    //         'users.taxes',
    //         'users.shipment_days'
    //     )
    //         ->join('users', 'cart_items.shop_id', 'users.id')
    //         ->where('cart_items.order_id', 2282)
    //         //            ->where('cart_items.type', 1)
    //         ->groupBy('users.id')->get();
    //     if ($objects) {
    //         $order = Orders::find(2282);
    //         $order->payment_method = 5;
    //         $order->save();
    //         foreach ($objects as $object) {

    //             $cart_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'cart_items.shop_id')
    //                 ->where('cart_items.type', 1)
    //                 ->where('cart_items.order_id', 2282)
    //                 ->where('shop_id', $object->shop_id)
    //                 ->where('cart_items.user_id', $object->user_id)
    //                 ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
    //                 ->join('products', 'cart_items.item_id', 'products.id')->get();

    //             $shipment = new OrderShipments();
    //             $shipment->order_id = $order->id;
    //             $shipment->user_id = 359;
    //             $shipment->shop_id = $object->shop_id;
    //             $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
    //             $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

    //             $shipment->delivery_price = $object->shipment_price;
    //             $shipment->taxes = $object->taxes;

    //             $shipment->status = 1;
    //             $shipment->save();
    //             foreach ($cart_items as $item) {
    //                 $cart_item = CartItem::find($item->id);
    //                 if ($cart_item) {
    //                     $cart_item->order_id = $order->id;
    //                     $cart_item->shipment_id = $shipment->id;
    //                     $cart_item->status = 1;
    //                     $cart_item->save();
    //                     //                        $product = Products::find($item->item_id);
    //                     //                        $product->quantity = $product->quantity - $item->quantity;
    //                     //                        $product->save();
    //                 }
    //             }

    //             //                $notification55 = new Notification();
    //             //                $notification55->sender_id = $user->id;
    //             //                $notification55->reciever_id = $object->shop_id;
    //             //                $notification55->ads_id = $shipment->id;
    //             //                $notification55->type = 13;
    //             //                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
    //             //                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
    //             //                $notification55->message_en = @$user->username . " bought products from your shop.";
    //             //                $notification55->save();
    //         }
    //         $order->payment_method = 5;
    //         $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
    //         $order->save();

    //         return \response()->json([
    //             'status' => 200,
    //             'order_id' => $order->id,
    //             'cart_count' => 0,
    //             'message' => 'تم ارسال الطلب بنجاح',
    //         ]);
    //     }
    //     return CartItem::where('user_id', 359)->where('order_id', 2282)->update(['status' => 1]);
    // }

    // public function recalc_order()
    // {

    //     $order = Orders::find(2282);
    //     $total = CartItem::where(['order_id' => $order->id])->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
    //     if (!$total) {
    //         return \response()->json([
    //             'status' => 400,
    //             'message' => 'لا يوجد شئ فى السلة',
    //         ]);
    //     }
    //     $shipment_price = Settings::find(22)->value;
    //     $taxs = Settings::find(38)->value;
    //     $cobon = 0;

    //     /**/
    //     $shipment_price = Settings::find(22)->value;
    //     $subtotal = $total;
    //     $total = $total + $shipment_price;
    //     $code = Cobons::where('code', $order->cobon)->first();
    //     //        if(!$cobon)abort(500);
    //     $percent = $code->percent;
    //     $final_percent_price = ($total * $percent) / 100; // الخصم بالنسبه
    //     $final_money_price = $code->max_money; //اعلي مبلغ خصم
    //     if ($final_percent_price >= $final_money_price) {
    //         $final_cobon_money = $final_money_price;
    //     } else {
    //         $final_cobon_money = $final_percent_price;
    //     }
    //     if ($final_money_price == 0) {
    //         $final_cobon_money = $final_percent_price;
    //     }
    //     $cobon = $final_cobon_money;

    //     /**/

    //     $order->final_price = $subtotal + $shipment_price + (($subtotal + $shipment_price) * $taxs / 100);
    //     $order->order_price = $subtotal;
    //     $order->delivery_price = $shipment_price;
    //     $order->taxes = (($subtotal + $shipment_price - $cobon) * $taxs / 100);

    //     $order->final_price = ($subtotal + $shipment_price - $cobon) + (($subtotal + $shipment_price - $cobon) * $taxs / 100);

    //     $order->order_price = $subtotal;
    //     $order->delivery_price = $shipment_price;
    //     $order->taxes = (($subtotal + $shipment_price - $cobon) * $taxs / 100);
    //     $order->cobon_discount = $cobon;

    //     $order->save();
    //     return $order;
    // }

    public function updateToken(Request $request)
    {
        $user = auth('client')->user();
        if ($user && $request->has('device_token') && !empty($request->device_token)) {
            if ($device = DeviceTokens::where('device_token', $request->device_token)->first()) {
                $device->user_id = $user->id;
                $device->save();
            } else {
                $device = new DeviceTokens();
                $device->device_token = $request->device_token;
                $device->user_id = $user->id;
                $device->save();
            }
        }
    }

    // public static function generate_pattern($search_string)
    // {
    //     $patterns = array("/(ا|إ|أ|آ)/", "/(ه|ة)/", "/(ى|ي|ئ)/");
    //     $replacements = array("[ا|إ|أ|آ]", "[ه|ة]", "[ى|ي|ئ]");
    //     return preg_replace($patterns, $replacements, $search_string);
    // }
}
