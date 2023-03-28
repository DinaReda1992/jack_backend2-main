<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentSettings;
use FCM;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\Models\Ads;
use App\Models\Hall;
use App\Models\User;
use App\Models\Banks;
use App\Models\Likes;
use App\Models\Posts;
use App\Models\Cobons;
use App\Models\Orders;
use App\Models\Prices;
use App\Models\Rating;
use App\Models\Stores;
use http\Env\Response;
use App\Models\Balance;
use App\Models\Feature;
use App\Models\Flights;
use App\Models\Follows;
use App\Models\Reports;
use App\Models\Tickets;
use App\Models\UserCar;
use App\Models\CartItem;
use App\Models\CarTrips;
use App\Models\Comments;
use App\Models\Contacts;
use App\Models\Favorite;
use App\Models\Invoices;
use App\Models\Messages;
use App\Models\Products;
use App\Models\Projects;
use App\Models\Settings;
use App\Models\Addresses;
use App\Models\AdsPhotos;
use App\Models\AdsOptions;
use App\Models\UserRating;
use App\Models\DamageOffer;
use App\Models\DamagePhoto;
use App\Models\HallFeature;
use App\Models\OrderOffers;
use App\Models\OrderPhotos;
use App\Models\Transaction;
use App\Models\BankAccounts;
use App\Models\BankTransfer;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\PricingOffer;
use App\Models\PricingOrder;
use App\Models\Reservations;
use App\Models\UserServices;
use Illuminate\Http\Request;
use App\Models\ProductPhotos;
use App\Models\ProductRating;
use App\Models\ProjectPhotos;
use App\Models\DamageEstimate;
use App\Models\InvoiceDetails;
use App\Models\OrderShipments;
use App\Models\Purchase_order;
use App\Models\ActivationCodes;
use App\Models\CobonsProviders;
use App\Models\MandoobPayments;
use App\Models\ProductsRegions;
use App\Models\CobonsCategories;
use App\Models\PricingOrderPart;
use App\Models\CancellationTypes;
use App\Services\SendNotification;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationFeatures;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\CartResources;
use App\Http\Resources\HallsResource;
use App\Http\Resources\UsersResource;
use App\Models\MessagesNotifications;
use App\Models\RequestRepresentative;
use App\Repositories\OrderRepository;
use App\Repositories\TmaraRepository;
use App\Http\Resources\MyAdsResources;
use LaravelFCM\Message\OptionsBuilder;
use App\Http\Resources\CobonsResourses;
use App\Http\Resources\MessageResources;
use App\Http\Resources\CommentsResources;
use App\Http\Resources\MyOrdersResources;
use App\Http\Resources\UserCarsResources;
use LaravelFCM\Message\PayloadDataBuilder;
use \Tymon\JWTAuth\Exceptions\JWTException;
use Google\Cloud\Firestore\FirestoreClient;
use App\Http\Resources\DamageOfferResources;
use App\Http\Resources\HallsFeaturesResource;
use App\Http\Resources\HallsFinishedResource;
use App\Http\Resources\HallsReservedResource;
use App\Http\Resources\OrderDetailsResources;
use App\Http\Resources\TransactionsResources;
use App\Http\Resources\DamageRequestResources;
use App\Http\Resources\PricingRequestResources;
use App\Http\Resources\MyWaitingOrdersResources;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Google\Cloud\Firestore\V1\StructuredQuery\Order;
use App\Http\Resources\Notifications as NotificationsResource;

class AuthenticateController extends Controller
{

    public function __construct(Request $request)
    {
        //        parent::__construct();
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        App::setLocale($language);
        $this->middleware('jwt.auth')->except('trackOrder');
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

    public function add_car(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'make_id' => 'required',
            'year_id' => 'required',
            'model_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in add car param'], 400);
        }
        $car = UserCar::where('make_id', $request->make_id)->where('is_deleted', 0)->where('year_id', $request->year_id)
            ->where('model_id', $request->model_id)->where('user_id', $user->id)->get();
        if ($car->count()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.car_found'),
                ]
            );
        }

        $car = new UserCar();
        $car->make_id = $request->make_id;
        $car->year_id = $request->year_id;
        $car->model_id = $request->model_id;
        $car->user_id = $user->id;
        $car->structure_no = $request->structure_no ?: '';
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'structure-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $car->structure_photo = $fileName;
        }
        UserCar::where('user_id', '=', $user->id)
            ->update(['is_default' => 0]);

        $car->is_default = 1;

        $car->save();

        $user_cars = UserCar::select('*')->where('user_id', $user->id)->where('is_deleted', 0)->with(['make' => function ($query) {
            if (App::getLocale() == "ar") {
                $query->select('id', 'name')
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", image)) as image');
            } else {
                $query->select('id', 'name_en as name')
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", image)) as image');
            }
        }, 'year' => function ($query) {
            $query->select('id', 'year');
        }, 'model' => function ($query) {
            if (App::getLocale() == "ar") {

                $query->select('id', 'name');
            } else {
                $query->select('id', 'name_en as name');
            }
        }])->get();
        $user_dd = UserCarsResources::collection($user_cars);
        //        return response()->json($user_dd);
        return response()->json([
            'message' => "car added successfully",
            'data' => $user_dd,
        ], 200);
    }

    public function get_user_cars(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $user_cars = UserCar::select('*')->where('user_id', $user->id)->where('is_deleted', 0)->with(['make' => function ($query) {
            if (App::getLocale() == "ar") {
                $query->select('id', 'name')
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", image)) as image');
            } else {
                $query->select('id', 'name_en as name')
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", image)) as image');
            }
        }, 'year' => function ($query) {
            $query->select('id', 'year');
        }, 'model' => function ($query) {
            if (App::getLocale() == "ar") {

                $query->select('id', 'name');
            } else {
                $query->select('id', 'name_en as name');
            }
        }])->get();
        $user_dd = UserCarsResources::collection($user_cars);
        return response()->json($user_dd);
    }

    public function edit_car(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'make_id' => 'required',
            'year_id' => 'required',
            'model_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in add car param'], 400);
        }
        $car = UserCar::find($request->car_id);
        if (!$car) {
            return response()->json(['messaage' => 'no car found'], 400);
        }
        $checkCar = UserCar::where('make_id', $request->make_id)->where('year_id', $request->year_id)
            ->where('model_id', $request->model_id)->where('user_id', $user->id)->where('id', '!=', $car->id)->where('is_deleted', 0)->get();
        if ($checkCar->count()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.car_found'),
                ]
            );
        }

        $car->make_id = $request->make_id;
        $car->year_id = $request->year_id;
        $car->model_id = $request->model_id;
        $car->user_id = $user->id;
        $car->structure_no = $request->structure_no;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'structure-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $car->structure_photo = $fileName;
        }
        $car->save();

        $user_cars = UserCar::select('*')->where('user_id', $user->id)->where('is_deleted', 0)->with(['make' => function ($query) {
            if (App::getLocale() == "ar") {
                $query->select('id', 'name')
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", image)) as image');
            } else {
                $query->select('id', 'name_en as name')
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", image)) as image');
            }
        }, 'year' => function ($query) {
            $query->select('id', 'year');
        }, 'model' => function ($query) {
            if (App::getLocale() == "ar") {

                $query->select('id', 'name');
            } else {
                $query->select('id', 'name_en as name');
            }
        }])->get();
        $user_dd = UserCarsResources::collection($user_cars);
        //        return response()->json($user_dd);
        return response()->json([
            'message' => "car edited successfully",
            'data' => $user_dd,
        ], 200);
    }

    public function delete_car(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $car = UserCar::find($request->car_id);
        if (!$car) {
            return response()->json(['messaage' => 'no car found'], 400);
        }
        $car->is_deleted = 1;
        $car->save();
        return response()->json([
            'message' => "car deleted successfully",
        ], 200);
    }

    public function like_product(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (!Products::find($request->item_id)) {
            return response()->json(
                [
                    'message' => "No Product found",
                ],
                400
            );
        }

        $user = JWTAuth::parseToken()->authenticate();

        if (!Favorite::where('user_id', $user->id)->where('item_id', $request->item_id)->where('type', 0)->first()) {
            $like = new Favorite();
            $like->item_id = $request->item_id;
            $like->user_id = $user->id;
            $like->type = 0;
            $like->save();
            return response()->json([
                'message' => "Liked successfully",
            ]);
        } else {
            return response()->json([
                'message' => "You likes this product before .",
            ], 400);
        }
    }

    public function unlike_product(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $like = Favorite::where('user_id', $user->id)->where('item_id', $request->item_id)->where('type', 0)->first();
        if (!$like) {
            return response()->json([
                'message' => "No thing to delete ",
            ], 400);
        } else {
            $like->delete();
            return response()->json([
                'message' => __('messages.like_deleted_successfully'),
            ]);
        }
    }

    public function like_shop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (!User::where('id', $request->item_id)->where('user_type_id', 3)->first()) {
            return response()->json(
                [
                    'message' => "No shop found",
                ],
                400
            );
        }

        $user = JWTAuth::parseToken()->authenticate();

        if (!Favorite::where('user_id', $user->id)->where('item_id', $request->item_id)->where('type', 1)->first()) {
            $like = new Favorite();
            $like->item_id = $request->item_id;
            $like->user_id = $user->id;
            $like->type = 1;
            $like->save();
            return response()->json([
                'message' => "Liked successfully",
            ]);
        } else {
            return response()->json([
                'message' => "You likes this product before .",
            ], 400);
        }
    }

    public function unlike_shop(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $like = Favorite::where('user_id', $user->id)->where('item_id', $request->item_id)->where('type', 1)->first();
        if (!$like) {
            return response()->json([
                'message' => "No thing to delete ",
            ], 400);
        } else {
            $like->delete();
            return response()->json([
                'message' => __('messages.like_deleted_successfully'),
            ]);
        }
    }

    public function latest_products(Request $request)
    {

        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $products = [];

        $latest_products = Products::select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->limit(10)
            ->orderBy('id', 'DESC')
            ->get();

        $products['latest_products'] = $latest_products;

        $more_views = Products::select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->limit(10)
            ->orderBy('views', 'DESC')
            ->get();

        $products['more_views'] = $more_views;

        $best_sellings = Products::select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->whereIn('id', function ($query) {
                $query->select('product_id')
                    ->from(with(new InvoiceDetails())->getTable())
                    ->whereIn('invoice_id', function ($query1) {
                        $query1->select('id')
                            ->from(with(new Invoices())->getTable())
                            ->where('status', 2);
                    });
            })
            ->groupBy('id')
            ->limit(10)
            ->get();

        $products['best_sellings'] = $best_sellings;

        return response()->json([
            'status' => 200,
            'data' => $products,
        ]);
    }

    public function my_cart()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $products = [];

        $user_like = 0;
        $sum = 0;
        $sum_before = 0;

        $invoices = \App\Models\InvoiceDetails::where('user_id', $user->id)->where('invoice_id', 0)
            ->groupBy('product_id')
            ->with(['getProduct' => function ($query) {
                $query->with(['photoImage' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }]);
                $query->with(['getUser' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }]);
            }])
            ->get();

        foreach ($invoices as $invoice) {
            if ($invoice->getProduct->discount) {
                $discount_value = $invoice->getProduct->price - (($invoice->getProduct->price * $invoice->getProduct->discount) / 100);
            } else {
                $discount_value = $invoice->getProduct->price;
            }
            $sum_before = $sum_before + (@$invoice->getProduct->price * $invoice->quantity);
            $discount_value = $discount_value * $invoice->quantity;
            $sum += $discount_value;
        }

        $products['products'] = $invoices;
        $products['sum_before_discount'] = $sum_before;
        $products['sum_after_discount'] = $sum;

        return response()->json([
            'status' => 200,
            'data' => $products,
        ]);
    }

    public function update_notification(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'notification' => 'required|in:1,0',
        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in notification param'], 400);
        }

        $user = User::find($user->id);
        $user->notification = $request->notification;
        $user->save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;
        return response()->json(['message' => 'updated successfully but don\'t display this message to user'], 200);
    }

    public function update_currency(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'currency_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in notification param'], 400);
        }
        $user_ = User::find($user->id);
        $user_->currency_id = $request->currency_id;
        $user_->save();
        $user = User::where('id', $user->id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->with(['country' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name', 'phonecode');
                } else {
                    $query->select('id', 'name_en as name', 'phonecode');
                }
            }])
            ->with(['currency' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name', 'code');
                } else {
                    $query->select('id', 'name_en as name', 'code');
                }
            }])
            ->first();
        $user{
            'token'} = str_replace('Bearer ', '', \request()->header('Authorization'));

        return response()->json(new UsersResource($user), 200);
    }

    public function update_country(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'country_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in notification param'], 400);
        }

        $user_ = User::find($user->id);
        $user_->country_id = $request->country_id;
        $user_->save();
        $user = User::where('id', $user->id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->with(['country' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name', 'phonecode');
                } else {
                    $query->select('id', 'name_en as name', 'phonecode');
                }
            }])
            ->with(['currency' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name', 'code');
                } else {
                    $query->select('id', 'name_en as name', 'code');
                }
            }])
            ->first();
        $user{
            'token'} = str_replace('Bearer ', '', \request()->header('Authorization'));

        return response()->json(new UsersResource($user), 200);
    }

    public function update_notification_messages(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'notification' => 'required|in:1,0',
        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in notification param'], 400);
        }

        $user = User::find($user->id);
        $user->notification_messages = $request->notification;
        $user->save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;
        return response()->json(['message' => 'updated successfully but don\'t display this message to user'], 200);
    }

    public function update_notification_follows(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'notification' => 'required|in:1,0',
        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in notification param'], 400);
        }

        $user = User::find($user->id);
        $user->notification_follows = $request->notification;
        $user->save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;
        return response()->json(['message' => 'updated successfully but don\'t display this message to user'], 200);
    }

    public function update_ring_tone(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'ring_tone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['mesasge' => 'Missed param ring tone'], 400);
        }

        $user = User::find($user->id);
        $user->ring_tone = $request->ring_tone;
        $user->save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;
        return response()->json(['message' => 'updated successfully but don\'t display this message to user'], 200);
    }

    public function update_language(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'lang' => 'required|in:ar,en',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'error in lang param'], 400);
        }

        $user = User::find($user->id);
        $user->lang = $request->lang;
        $user->save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;
        return response()->json(['message' => 'updated successfully but don\'t display this message to user'], 200);
    }

    public function activate(Request $request)
    {
        $activation_code = $request->activation_code;
        $phone = $request->phone;
        $phone1 = ltrim($request->phone, '0');
        $phone2 = "0" . $request->phone;
        $phonecode = $request->phonecode;

        $user = ActivationCodes::where('activation_code', $activation_code)->whereIn('phone', [$phone, $phone1, $phone2])->where('phonecode', $phonecode)->first();
        if (!$user) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.error_code'),
                ]
            );
        } else {
            $this_user1 = User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->first();
            if ($user->getUser || $this_user1) {
                $user = $user->getUser ? $user->getUser : $this_user1;
                $this_user = User::where('id', $user->id)
                    ->select('*')
                    ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo')
                    ->first();

                //              $this_user -> username = $this_user -> username == null ?  '' : $this_user -> username;
                $this_user->last_login = date('Y-m-d H:i:s');
                $this_user->device_token = $request->device_token;
                if ($request->device_type) {
                    $this_user->device_type = $request->device_type;
                }
                $this_user->save();

                $token = JWTAuth::fromUser($this_user);
                $this_user->token = $token;
                $this_user->activate = 1;
                $this_user->save();
                return response()->json(
                    [
                        'status' => 200,
                        'data' => $this_user,
                    ]
                );
            } else {
                $user->activate = 1;
                $user->save();
                return response()->json(
                    [
                        'status' => 202,
                        'message' => trans('messages.go_to_register_page'),
                    ]
                );
            }
        }
    }

    public function activate_phone(Request $request)
    {
        $activation_code = $request->activation_code;
        $phone = $request->phone;
        $user = User::where('activation_code', $activation_code)->where('phone_edited', $phone)->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => trans('messages.error_code'),
                ],
                400
            );
        } else {
            $user = User::where('phone_edited', $phone)->first();
            $user->phone = $user->phone_edited;
            $user->phone_edited = "";
            $user->save();
            $user = User::where('id', $user->id)
                ->select('*')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->with(['state' => function ($query) {
                    if (App::getLocale() == "ar") {
                        $query->select('id', 'name');
                    } else {
                        $query->select('id', 'name_en as name');
                    }
                    //                $query->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.url('/').'/flags/", photo)) END) AS photo');
                }])
                ->first();
            $token = JWTAuth::fromUser($user);
            $user->{"token"} = $token;
            return response()->json(
                [
                    'message' => __('messages.your_phone_updated_successfully'),
                    'data' => new UsersResource($user),
                ]
            );
        }
    }

    public function activate_email(Request $request)
    {
        $activation_code = $request->activation_code;
        $phone = $request->phone;
        $user = User::where('activation_code', $activation_code)->where('email_edited', $phone)->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => trans('messages.error_code'),
                ],
                400
            );
        } else {
            $user = User::where('email_edited', $phone)->first();
            $user->email = $user->email_edited;
            $user->email_edited = "";
            $user->save();
            $user = User::where('id', $user->id)
                ->select('*')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->with(['country' => function ($query) {
                    if (App::getLocale() == "ar") {
                        $query->select('id', 'name', 'phonecode');
                    } else {
                        $query->select('id', 'name_en as name', 'phonecode');
                    }
                }])
                ->with(['currency' => function ($query) {
                    if (App::getLocale() == "ar") {
                        $query->select('id', 'name', 'code');
                    } else {
                        $query->select('id', 'name_en as name', 'code');
                    }
                }])
                ->first();
            $token = JWTAuth::fromUser($user);
            $user->{"token"} = $token;
            return response()->json(new UsersResource($user));
        }
    }

    public function check_upgrade()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->user_type_id == 2) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'You Are golden member',
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'You ar normal user',
                ]
            );
        }
    }

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => "User not found"], 401);
            }
        } catch (TokenExpiredException $e) {

            return response()->json(['message' => "Token expired"], 401);
        } catch (TokenInvalidException $e) {

            return response()->json(['message' => "Token invalid"], 401);
        } catch (JWTException $e) {

            return response()->json(['message' => "Token absent"], 401);
        }
        $user = User::where('id', $user->id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->first();
        $user{
            'token'} = str_replace('Bearer ', '', \request()->header('Authorization'));
        //        $likes_count = Likes::whereIn('offer_id', function ($query) use ($user) {
        //            $query->select('id')
        //                ->from(with(new Offers())->getTable())
        //                ->where('user_id',$user->id)
        //            ;
        //        })->count();

        //        $user->{'likes_count'} = $likes_count;

        // the token is valid and we have found the user via the sub claim
        return response()->json(new UsersResource($user), 200);
    }

    public function logout(Request $request)
    {
        if ($request->device_token) {
            DeviceTokens::where('device_token', $request->device_token)->delete();
        }
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => __('messages.logged_out')], 200);
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function test()
    {
        $response = \request()->input('response');
        if (preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $response)) {
            echo "the url $response contains guru";
        } else {
            echo "the url $response does not contain guru";
        }
    }

    public function setPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'activation_code' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'data' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $activation_code = $request->activation_code;
        $email = $request->email;
        $passsword = bcrypt($request->password);
        $user = User::where('activation_code', $activation_code)->where('email', $email)->first();
        if (!$user) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.error_code'),
                ]
            );
        } else {
            $user = User::where('id', $user->id)->select('*')->first();
            $user->last_login = date('Y-m-d H:i:s');
            $user->password = $passsword;
            $user->save();

            $token = JWTAuth::fromUser($user);
            $user->{"token"} = $token;
            return response()->json(
                [
                    'status' => 200,
                    'message' => __('messages.password_resigned_successfully'),
                    'data' => $user,
                ]
            );
        }
    }

    public function sendSMSWK($userAccount, $passAccount, $numbers, $sender, $msg, $viewResult = 1)
    {
        global $arraySendMsgWK;
        $url = "www.mobily.ws/api/msgSend.php";
        $applicationType = "68";
        $msg = $msg;
        $sender = urlencode($sender);
        $stringToPost = "mobile=" . $userAccount . "&password=" . $passAccount . "&numbers=" . $numbers . "&sender=" . $sender . "&msg=" . $msg . "&applicationType=" . $applicationType . "&lang=3";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
        $result = curl_exec($ch);

        if ($viewResult) {
            $result = trim($result);
        }

        // echo $result;
        return $result;
    }

    public function update_coordinates(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $user = User::find($user->id);
        $user->longitude = $request->longitude ? $request->longitude : '';
        $user->latitude = $request->latitude ? $request->latitude : '';
        $user->save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;

        $db = new FirestoreClient(
            ['projectId' => 'mr-mandob']
        );
        $docRef = $db->collection('users')->document($user->id);
        $docRef->set([
            'longitude' => $user->longitude,
            'latitude' => $user->latitude,
        ]);

        return response()->json(
            [
                'status' => 200,
                'data' => $user,
                'message' => __('messages.profile_updated_successfully'),
            ]
        );
    }

    public function check_coupon(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $code = Cobons::where('code', $request->code)->first();
        if ($code) {

            $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($code->created_at)) . " +" . $code->days . " days"));
            if (date('Y-m-d') > $date_of_end) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => "عفوا انتهت صلاحية الكوبون",
                    ]
                );
            }

            $count_used = Orders::where('user_id', $user->id)->where('cobon', $request->code)->whereIn('status', [1, 2, 4])->count();

            if ($count_used) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => __('messages.coupon_used_before'),
                    ]
                );
            } else {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => __('messages.coupon_is_available'),
                        'percent' => $code->percent,
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('messages.coupon_not_fount'),
                ]
            );
        }
    }

    public function check_coupon_category(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $code = Cobons::where('code', $request->code)->first();
        if ($code) {
            $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($code->created_at)) . " +" . $code->days . " days"));
            if (date('Y-m-d') > $date_of_end) {
                return [
                    'status' => 400,
                    'message' => "عفوا انتهت صلاحية الكوبون",
                ];
            }
            $count_used = Orders::where('cobon', $request->code)->where('payment_method', '<>', 0)->where('status', '<>', 5)->count();
            if ($code->usage_quota <= $count_used) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => 'عفوا انتهت صلاحية الكوبون',
                    ]
                );
            }
            /*  $discount_prices = CartItem::
            select('cart_items.id', 'cart_items.price',
            'cart_items.quantity', 'cart_items.item_id','products.category_id',
            \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as total'))

            ->join('products', 'products.id', 'cart_items.item_id')
            ->whereIn('products.category_id', function ($query) use ($code) {
            $query->select('category_id')
            ->from(with(new CobonsCategories())->getTable())
            ->where('cobon_id', $code->id);
            })
            //                  ->selectRaw('(SELECT sum(cart_items.quantity*cart_items.price)
            //                     FROM cart_items where cart_items.item_id=products.id) as total_item_price')
            ->where('cart_items.order_id', 0)
            ->where('type', 1)
            ->where('cart_items.user_id', $user->id)->first();*/

            $discount_prices = CartItem::select(
                'cart_items.id',
                'cart_items.price',
                'cart_items.quantity',
                'cart_items.item_id',
                'products.category_id',
                \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as total')
            )
                ->join('products', 'products.id', 'cart_items.item_id')
                ->where(function ($q) use ($code) {
                    if ($code->link_type == 'category') {
                        $q->whereIn('products.category_id', function ($query) use ($code) {
                            $query->select('category_id')
                                ->from(with(new CobonsCategories())->getTable())
                                ->where('cobon_id', $code->id);
                        });
                    } else {
                        $q->whereIn('products.provider_id', function ($query) use ($code) {
                            $query->select('user_id')
                                ->from(with(new CobonsProviders())->getTable())
                                ->where('cobon_id', $code->id);
                        });
                    }
                })

                //                  ->selectRaw('(SELECT sum(cart_items.quantity*cart_items.price)
                //                     FROM cart_items where cart_items.item_id=products.id) as total_item_price')

                ->where('cart_items.order_id', 0)
                ->where('type', 1)
                ->where('cart_items.user_id', $user->id)->first();
            $total = $discount_prices ? $discount_prices->total : 0;
            /* $total= CartItem::where(['order_id'=>0,'user_id'=>$user->id])->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))
            ->where('type',1)
            ->first()->total;*/
            $shipment_price = Settings::find(22)->value;
            $shipment_price = 0;
            $total = $total + $shipment_price;
            $percent = $code->percent;

            $final_percent_price = ($total * $percent) / 100; // الخصم بالنسبه

            $final_money_price = $code->max_money; //اعلي مبلغ خصم

            if ($final_percent_price >= $final_money_price) {
                $final_cobon_money = $final_money_price;
            } else {
                $final_cobon_money = $final_percent_price;
            }
            if ($final_money_price == 0) {
                $final_cobon_money = $final_percent_price;
            }

            return [
                'status' => 200,
                'message' => __('messages.coupon_is_available'),
                'money' => $final_cobon_money,
            ];
        } else {
            return [
                'status' => 400,
                'message' => __('messages.coupon_not_fount'),
            ];
        }
    }

    //    public function check_coupon_category(Request $request)
    //    {
    //        $user = JWTAuth::parseToken()->authenticate();
    //
    //        $code = Cobons::where('code', $request->code)->first();
    //        if ($code) {
    //
    //
    //            $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($code->created_at)) . " +" . $code->days . " days"));
    //            if (date('Y-m-d') > $date_of_end) {
    //                return response()->json(
    //                    [
    //                        'status' => 400,
    //                        'message' => "عفوا انتهت صلاحية الكوبون",
    //                    ]);
    //            }
    //
    //            $count_used = Orders::where('user_id', $user->id)->where('cobon', $request->code)->where('status', '<>', 5)->count();
    //
    //            if ($count_used) {
    //                return response()->json(
    //                    [
    //                        'status' => 400,
    //                        'message' => __('messages.coupon_used_before'),
    //                    ]);
    //            } else {
    //
    //                $total = CartItem::where(['order_id' => 0, 'user_id' => $user->id])->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
    //
    //                $percent = $code->percent;
    //
    //                $final_percent_price = ($total * $percent) / 100; // الخصم بالنسبه
    //
    //                $final_money_price = $code->max_money;//اعلي مبلغ خصم
    //
    //                if ($final_percent_price >= $final_money_price) {
    //                    $final_cobon_money = $final_money_price;
    //                } else {
    //                    $final_cobon_money = $final_percent_price;
    //                }
    //                if ($final_money_price == 0) {
    //                    $final_cobon_money = $final_percent_price;
    //
    //                }
    //
    //
    //                return response()->json(
    //                    [
    //                        'status' => 200,
    //                        'message' => __('messages.coupon_is_available'),
    //                        'money' => $final_cobon_money
    //                    ]);
    //            }
    //        } else {
    //            return response()->json(
    //                [
    //                    'status' => 400,
    //                    'message' => __('messages.coupon_not_fount'),
    //                ]);
    //        }
    //
    //    }

    public function change_lang(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'lang' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $user = User::find($user->id);
        $user->lang = $request->lang;
        $user->save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;

        return response()->json(
            [
                'status' => 200,
                'data' => $user,
                'message' => __('messages.profile_updated_successfully'),
            ]
        );
    }

    public function change_status(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'ready' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $user = User::find($user->id);
        $user->ready = $request->ready;
        $user->save();

        return response()->json(
            [
                'status' => 200,
                'data' => $user,
                'message' => __('messages.profile_updated_successfully'),
            ]
        );
    }

    public function change_notification(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'notification' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $user = User::find($user->id);
        $user->notification = $request->notification;
        $user->save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;

        return response()->json(
            [
                'status' => 200,
                'data' => $user,
                'message' => __('messages.profile_updated_successfully'),
            ]
        );
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required',
            'phone' => 'required',
            'phonecode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        if (!ActivationCodes::where('phonecode', $request->phonecode)->whereIn('phone', [$request->phone, ltrim($request->phone, 0)])->where('activate', 1)->first()) {
            return response()->json(
                [
                    'status' => 402,
                    'message' => trans('messages.you_have_to_activate_your_phone_first'),
                ]
            );
        }

        $user = new User();
        $user->username = $request->username;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->password = bcrypt('000000');
        $user->phone = $request->phone;
        $user->activate = 0;
        $user->user_type_id = 3;
        $user->phonecode = $request->phonecode;
        $user->lang = $request->lang ? $request->lang : 'ar';
        $user->notification = 1;
        $user->device_token = $request->device_token ? $request->device_token : "";
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $user->photo = $fileName;
        }
        //        if($request->device_type){
        //            $user -> device_type = $request->device_type;
        //        }
        $user->save();
        $user = User::where('id', $user->id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo')
            ->first();
        $activation_code = ActivationCodes::where('phonecode', $user->phonecode)->whereIn('phone', [$user->phone, ltrim($user->phone, 0)])->first();
        $activation_code->user_id = $user->id;
        $activation_code->save();

        $token = JWTAuth::fromUser($user);
        $user->token = $token;
        $user->save();

        return response()->json(
            [
                'status' => 200,
                'data' => $user,
                'message' => trans('messages.you_are_registered_successfully'),
            ]
        );
    }

    public function authenticate(Request $request)
    {

        $phone = $request->phone;
        $phone1 = ltrim($request->phone, '0');
        $phonecode = $request->phonecode;

        if (User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1])->where('block', 1)->first()) {
            return response()->json([
                'status' => 400,
                'message' => trans('messages.you_are_blocked'),

            ]);
        } elseif (User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1])->where('block', 0)->first()) {
            $user = User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1])->where('block', 0)->first();
            $user->activate = 0;
            $user->save();

            $activation = ActivationCodes::where('user_id', $user->id)->first();
            $activation->activation_code = mt_rand(1000, 9999);
            $activation->save();

            $smsMessage = 'عميلنا العزيز كود تفعيل الدخول في إفحص هو: ' . $activation->activation_code;
            $phone_number = $user->phonecode . ltrim($user->phone, '0');
            $send_sms_response = $this->sendSMS('Efhes', 'Efhes@2018', $smsMessage, $phone_number, "Efhes");
            // if no errors are encountered we can return a JWT
            return response()->json([
                'status' => 200,
                'message' => trans('messages.please_activate_your_pnone'),
                'code' => $send_sms_response,
                'activation_code' => $activation->activation_code,
                'lang' => App::getLocale(),
            ]);
        } else {

            if ($activation = ActivationCodes::where('phonecode', $phonecode)->whereIn('phone', [$phone1, $phone])->first()) {
                $activation->activation_code = mt_rand(1000, 9999);
                $activation->save();
            } else {
                $activation = new ActivationCodes();
                $activation->phonecode = $request->phonecode;
                $activation->phone = $request->phone;
                $activation->activation_code = mt_rand(1000, 9999);
                $activation->save();
            }

            $smsMessage = 'عميلنا العزيز كود تفعيل الدخول في إفحص هو: ' . $activation->activation_code;
            $phone_number = $activation->phonecode . ltrim($activation->phone, '0');
            $send_sms_response = $this->sendSMS('Efhes', 'Efhes@2018', $smsMessage, $phone_number, "Efhes");
            // if no errors are encountered we can return a JWT
            return response()->json([
                'status' => 200,
                'message' => trans('messages.please_activate_your_pnone'),
                'code' => $send_sms_response,
                'activation_code' => $activation->activation_code,
            ]);
        }
    }

    public function get_phone_code(Request $request)
    {

        $phone = $request->phone;
        $phone1 = ltrim($request->phone, '0');
        $phonecode = $request->phonecode;

        if ($activation = ActivationCodes::where('phonecode', $phonecode)->whereIn('phone', [$phone1, $phone])->first()) {
            $activation->activation_code = mt_rand(1000, 9999);
            $activation->save();
            echo $activation->activation_code;
        } else {
            $activation = new ActivationCodes();
            $activation->phonecode = $request->phonecode;
            $activation->phone = $request->phone;
            $activation->activation_code = mt_rand(1000, 9999);
            $activation->save();
            echo $activation->activation_code;
        }
    }

    public function my_orders(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $orders = Orders::where('user_id', $user->id)->with('getDetails')->paginate(10);
        return response()->json(
            [
                'status' => 200,
                'data' => $orders,
            ]
        );
    }

    public function my_posts(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $orders = Posts::where('user_id', $user->id)->paginate(10);
        return response()->json(
            [
                'status' => 200,
                'data' => $orders,
            ]
        );
    }

    public function all_posts(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $posts = Posts::select('*')
            ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user->id . ' AND likes.post_id=posts.id	) as if_user_like_post')
            ->with(['getUser' => function ($query) {
                $query->select('*');
            }])
            ->orderBy('id', 'DESC')->paginate(10);
        $res = [];
        foreach ($posts as $offer) {
            $offer->{"created_time"} = Carbon::parse($offer->created_at)->diffForHumans();
            $res[] = $offer;
        }

        return response()->json([
            'status' => 200,
            'data' => $posts,
        ]);
    }

    public function add_comment(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'ads_id' => 'required',
            'comment' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $comment = new Comments();
        $comment->user_id = $user->id;
        $comment->ads_id = $request->input('ads_id');
        $comment->comment = $request->input('comment');
        $comment->save();

        $comment->load(['getUser' => function ($query) {
            $query->select('id', 'username', 'user_type_id')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
        }]);
        $comment->{'created_time'} = Carbon::parse($comment->created_at)->diffForHumans();
        $notification55 = new Notification();
        $notification55->sender_id = $user->id;
        $notification55->reciever_id = @$comment->getAds->user_id;
        $notification55->ads_id = $comment->ads_id;
        $notification55->type = 3;
        $notification55->message = "قام " . $user->username . " بالتعليق على اعلانك";
        $notification55->save();
        $notification_title = "تعليق على إعلانك";
        $notification_message = $notification55->message;
        if (@$notification55->getReciever->notification == 1) {
            $this->send_fcm_notification($notification_title, $notification_message, $notification55, $comment, 'default');
        }

        return response()->json(new CommentsResources($comment));
    }

    public function current_representatives(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'place_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $user = JWTAuth::parseToken()->authenticate();

        $stores = Stores::where('place_id', $request->place_id)->count();
        $status = Stores::where('place_id', $request->place_id)->where('user_id', $user->id)->first() ? true : false;

        return response()->json([
            'representatives_count' => $stores,
            'is_user_joined' => $status,
        ]);
    }

    public function join_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'place_id' => 'required',
            'name' => 'required',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $user = JWTAuth::parseToken()->authenticate();

        if (!Stores::where('user_id', $user->id)->where('place_id', $request->place_id)->first()) {
            $like = new Stores();
            $like->place_id = $request->place_id;
            $like->user_id = $user->id;
            $like->photo = $request->photo;
            $like->name = $request->name;
            $like->save();
            return response()->json([
                'message' => __('messages.you_joined_successfully'),
            ]);
        } else {

            return response()->json([
                'message' => "انت أعجبت بالمتجر قبل كده",
            ], 400);
        }
    }

    public function delete_store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $like = Stores::where('user_id', $user->id)->where('id', $request->store_id)->first();
        if (!$like) {
            return response()->json([
                'message' => "لا يوجد شئ لحذفه من الفضلة ",
            ], 400);
        } else {
            $like->delete();
            return response()->json([
                'message' => __('messages.you_deleted_store_successfully'),
            ]);
        }
    }

    public function check_availability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required',
            'date' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (!Hall::find($request->hall_id)) {
            return response()->json(
                [
                    'message' => "No Hall found",
                ],
                400
            );
        }

        $user = JWTAuth::parseToken()->authenticate();

        if (!Reservations::where('hall_id', $request->hall_id)->where('date', $request->date)
            ->whereBetween('from_time', array($request->from_time, $request->to_time))
            ->where('payment_method', '!=', 0)
            ->where('status', '!=', 2)
            ->first()) {

            return response()->json([
                'message' => "Available",
            ]);
        } else {

            return response()->json([
                'message' => "Not Available .",
            ], 400);
        }
    }

    public function hall_features(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (!Hall::find($request->hall_id)) {
            return response()->json(
                [
                    'message' => "No Hall found",
                ],
                400
            );
        }

        $user = JWTAuth::parseToken()->authenticate();
        if (App::isLocale('ar')) {
            $hall_features = HallFeature::select('id', 'hall_id', 'feature_id', 'price', 'description')
                ->where('hall_id', $request->hall_id)
                ->with(['feature' => function ($query) {
                    $query->select('id', 'name', 'is_one');
                }])
                ->get();
        } else {
            $hall_features = HallFeature::select('id', 'hall_id', 'feature_id', 'price', 'description_en as description')
                ->with(['feature' => function ($query) {
                    $query->select('id', 'name_en as name', 'is_one');
                }])
                ->where('hall_id', $request->hall_id)->get();
        }
        return response()->json(HallsFeaturesResource::collection($hall_features));
    }

    public function add_reservation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hall_id' => 'required',
            'features' => 'required',
            'number' => 'required',
            'date' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
            //                'payment_method' => 'required|in:0,1',
            //                'money_transfered' => $request->payment_mehod ==0 ? 'required':'',
            //                'bank_name' => $request->payment_mehod ==0 ? 'required':'',
            //                'account_name' => $request->payment_mehod ==0 ? 'required':'',
            //                'account_number' => $request->payment_mehod ==0 ? 'required':'',
            //                'photo' => $request->payment_mehod ==0 ? 'required|image':'',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (!$hall = Hall::find($request->hall_id)) {
            return response()->json(
                [
                    'message' => "No Hall found",
                ],
                400
            );
        }

        $user = JWTAuth::parseToken()->authenticate();

        if (!Reservations::where('hall_id', $request->hall_id)->where('date', $request->date)
            ->whereBetween('from_time', array($request->from_time, $request->to_time))
            ->where('payment_method', '!=', 0)
            ->where('status', '!=', 2)
            ->first()) {

            $time1 = $request->from_time;
            $time2 = $request->to_time;
            $t1 = strtotime($time1);
            $t2 = strtotime($time2);
            $Hours = (($t2 - $t1) / 60) / 60;
            $reservation_hours = number_format($Hours, 2, '.', '');

            $reservation = new Reservations();
            $reservation->date = $request->date;
            $reservation->from_time = $request->from_time;
            $reservation->to_time = $request->to_time;
            $reservation->hall_id = $request->hall_id;
            $reservation->number = $request->number;
            $reservation->user_id = $user->id;
            $reservation->reservation_hours = $reservation_hours;
            $reservation->reservation_price = $reservation_hours * $hall->price_per_hour;
            $reservation->save();

            $final_price = 0;

            $features = json_decode($request->features);
            if ($features && count($features) > 0) {
                foreach ($features as $feature) {
                    if (!empty($feature)) {
                        $ads_option = new ReservationFeatures();
                        $ads_option->feature_id = $feature->feature_id;
                        $ads_option->reservation_id = $reservation->id;
                        $ads_option->number = @Feature::find($feature->feature_id)->is_one == 1 ? 1 : $reservation->number;
                        $ads_option->price = @@HallFeature::where('feature_id', $feature->feature_id)->where('hall_id', $reservation->hall_id)->first()->price;
                        $ads_option->save();
                        $final_price += $ads_option->price * $ads_option->number;
                    }
                }
            }
            $reservation->features_price = $final_price;
            $reservation->final_price = $final_price + $reservation->reservation_price;
            $reservation->save();
            return response()->json([
                'message' => "Reservation added successfully .",
                'reservation_id' => $reservation->id,
                'features_price' => $reservation->features_price,
                'reservation_price' => $reservation->reservation_price,
                'final_price' => $reservation->final_price,
            ]);
        } else {
            return response()->json([
                'message' => "Sorry someone made a reservation before you  .",
            ], 400);
        }
    }

    public function add_bank_transfer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required',
            'money_transfered' => $request->payment_mehod == 0 ? 'required' : '',
            'bank_name' => $request->payment_mehod == 0 ? 'required' : '',
            'account_name' => $request->payment_mehod == 0 ? 'required' : '',
            'account_number' => $request->payment_mehod == 0 ? 'required' : '',
            'photo' => $request->payment_mehod == 0 ? 'required|image' : '',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (!$reservation = Reservations::find($request->reservation_id)) {
            return response()->json(
                [
                    'message' => "No reservation found",
                ],
                400
            );
        }

        $new_bank_transfer = new BankTransfer();
        $new_bank_transfer->reservation_id = $request->reservation_id;
        $new_bank_transfer->money_transfered = $request->money_transfered;
        $new_bank_transfer->user_id = $user->id;
        $new_bank_transfer->bank_name = $request->bank_name;
        $new_bank_transfer->account_name = $request->account_name;
        $new_bank_transfer->account_number = $request->account_number;
        $new_bank_transfer->reservation_id = $request->reservation_id;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'bank-transfer-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $new_bank_transfer->photo = $fileName;
        }
        $new_bank_transfer->save();

        $reservation->payment_method = 1;
        $reservation->save();

        return response()->json([
            'message' => "Bank transfer sent successfully  .",
        ], 200);
    }

    public function check_cancel(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (!$reservation = Reservations::whereIn('status', [0, 1])->where('id', $request->reservation_id)->where('user_id', $user->id)->first()) {
            return response()->json(
                [
                    'message' => "No reservation found",
                ],
                400
            );
        }

        $now = time(); // or your date as well
        $your_date = strtotime($reservation->date);
        $datediff = $your_date - $now;

        $days = round($datediff / (60 * 60 * 24));

        //            return $days;

        if ($days < 1) {
            return response()->json(
                [
                    'message' => "You can't cancel reservation at day of reservation",
                ],
                400
            );
        } elseif ($days == 1) {
            $message = \app()->getLocale() == "ar" ? CancellationTypes::find(3)->description : CancellationTypes::find(3)->description_en;
        } elseif ($days > CancellationTypes::find(3)->days && $days > CancellationTypes::find(2)->days) {
            $message = \app()->getLocale() == "ar" ? CancellationTypes::find(2)->description : CancellationTypes::find(2)->description_en;
        } elseif ($days >= CancellationTypes::find(2)->days && $days > CancellationTypes::find(1)->days) {
            $message = \app()->getLocale() == "ar" ? CancellationTypes::find(1)->description : CancellationTypes::find(1)->description_en;
        } else {
            $message = "You can cancel your reservation ";
        }

        return response()->json([
            'message' => $message,
        ], 200);
    }

    public function cancel_reservation(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (!$reservation = Reservations::whereIn('status', [0, 1])->where('id', $request->reservation_id)->where('user_id', $user->id)->first()) {
            return response()->json(
                [
                    'message' => "No reservation found",
                ],
                400
            );
        }

        $now = time(); // or your date as well
        $your_date = strtotime($reservation->date);
        $datediff = $your_date - $now;

        $days = round($datediff / (60 * 60 * 24));

        //            return $days;

        if ($days < 1) {
            return response()->json(
                [
                    'message' => "You can't cancel reservation at day of reservation",
                ],
                400
            );
        } elseif ($days == 1) {

            $fees_on_cancel = $reservation->final_price - ($reservation->final_price * CancellationTypes::find(3)->percent / 100);
            $reservation->status = 2;
            $reservation->cancellation_date = date('Y-m-d G:i:s');
            $reservation->price_after_cancel = $fees_on_cancel;
            $reservation->save();
        } elseif ($days > CancellationTypes::find(3)->days && $days > CancellationTypes::find(2)->days) {
            $fees_on_cancel = $reservation->final_price - ($reservation->final_price * CancellationTypes::find(2)->percent / 100);
            $reservation->status = 2;
            $reservation->cancellation_date = date('Y-m-d G:i:s');
            $reservation->price_after_cancel = $fees_on_cancel;
            $reservation->save();
        } elseif ($days >= CancellationTypes::find(2)->days && $days > CancellationTypes::find(1)->days) {
            $fees_on_cancel = $reservation->final_price - ($reservation->final_price * CancellationTypes::find(1)->percent / 100);
            $reservation->status = 2;
            $reservation->cancellation_date = date('Y-m-d G:i:s');
            $reservation->price_after_cancel = $fees_on_cancel;
            $reservation->save();
        } else {
            $reservation->status = 2;
            $reservation->cancellation_date = date('Y-m-d G:i:s');
            $reservation->price_after_cancel = $reservation->final_price;
            $reservation->save();
        }

        return response()->json([
            'message' => "cancelled successfully",
        ], 200);
    }

    public function follow_ads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $user = JWTAuth::parseToken()->authenticate();
        //
        //            if($user->id == @Ads::find($request->ads_id)->user_id){
        //                return response()->json(
        //                    [
        //                        'message' => "عفوا لا يمكنك الاعجاب باعلان حاص بك ",
        //                    ],400
        //                );
        //            }

        if (!Follows::where('user_id', $user->id)->where('followed_user', $request->user_id)->first()) {
            $like = new Follows();
            $like->followed_user = $request->user_id;
            $like->user_id = $user->id;
            $like->save();

            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $request->user_id;
            $notification55->type = 5;
            $notification55->message = "قام " . $user->username . " بمتابعة حسابك ";
            $notification55->save();

            $notification_title = "متابعة حسابك";
            $notification_message = $notification55->message;
            if (@$notification55->getReciever->notification == 1) {
                $this->send_fcm_notification($notification_title, $notification_message, $notification55, $like, 'default');
            }

            return response()->json([
                'message' => "تم متابعة المستخدم",
            ]);
        } else {
            return response()->json([
                'message' => "تم المتابعة من قبل",
            ], 400);
        }
    }

    public function add_offer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $order = Orders::where('status', 0)
            ->where('id', $request->order_id)
            ->whereIn('id', function ($query) use ($user) {
                $query->select('order_id')
                    ->from(with(new Notification())->getTable())
                    ->where('reciever_id', $user->id);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'message' => __('messages.sorry_order_not_available_any_more'),
            ], 400);
        } elseif (OrderOffers::where('order_id', $order->id)->where('user_id', $user->id)->first()) {
            return response()->json([
                'message' => __('messages.sorry_you_added_offer_on_this_order_before'),
            ], 400);
        } elseif ($request->price < Settings::find(14)->value) {
            return response()->json([
                'message' => __('messages.sorry_price_should_be') . " " . Settings::find(14)->value . " SAR",
            ], 400);
        } else {

            $offer = new OrderOffers();
            $offer->user_id = $user->id;
            $offer->price = $request->price;
            $offer->order_id = $order->id;
            $offer->save();

            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $order->user_id;
            $notification55->order_id = $order->id;
            $notification55->type = 12;
            $notification55->message = "قام " . $user->username . " بتقديم عرض على طلبك ";
            $notification55->message_en = @$user->username . " made offer on your order.";
            $notification55->save();
            $notification55 = Notification::find($notification55->id);
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            if ($order->getUser->lang == "en") {
                $notification_title = "Offer on your order";
                $notification_message = $notification55->message_en;
            } else {
                $notification_title = "عرض مقدم على طلبك";
                $notification_message = $notification55->message;
            }
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
                    'notification_data' => new NotificationsResource($notification55),
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

            return response()->json([
                'message' => __('messages.your_offer_added_successfully'),
            ]);
        }
    }

    public function add_ads(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'description' => 'required',
            'state_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $current_ads = Ads::where('user_id', $user->id)->whereDate('created_at', date('Y-m-d'))->count();
        if ($current_ads == @$user->getPackage->allowed_ads) {
            return response()->json(
                [
                    'message' => 'عفوا غير مسموج بإضافة أكثر من ' . @$user->getPackage->allowed_ads . ' اعلانات في اليوم الواحد ضمن باقتك الحالية',
                ],
                400
            );
        }

        $ads = new Ads();
        $ads->title = $request->title;
        $ads->description = $request->description;
        $ads->category_id = $request->category_id;
        $ads->sub_category_id = $request->sub_category_id;
        $ads->user_id = $user->id;
        $ads->state_id = $request->state_id;
        $ads->price = $request->price;
        $ads->longitude = $request->longitude ?: '';
        $ads->latitude = $request->latitude ?: '';
        $ads->save();

        $options = json_decode($request->options);
        if ($options && count($options) > 0) {
            foreach ($options as $option) {
                if (!empty($option)) {
                    $ads_option = new AdsOptions();
                    $ads_option->ads_id = $ads->id;
                    $ads_option->selection_id = $option->selection_id;
                    $ads_option->option_id = $option->option_id;
                    $ads_option->option_name = $option->option_name;
                    $ads_option->option_value = $option->option_value;
                    $ads_option->save();
                }
            }
        }
        foreach (Follows::where('followed_user', $ads->user_id)->get() as $this_user) {
            if (@$this_user->getUser->notification == 1) {

                $notification55 = new Notification();
                $notification55->sender_id = $user->id;
                $notification55->reciever_id = $this_user->user_id;
                $notification55->ads_id = $ads->id;
                $notification55->type = 6;
                $notification55->message = "قام " . $user->username . " بإضافة إعلان جديد ";
                $notification55->save();

                $notification_title = "اعلان جديد";
                $notification_message = $notification55->message;
                $this->send_fcm_notification($notification_title, $notification_message, $notification55, $ads, 'default');
            }
        }

        return response()->json(
            [
                'message' => 'تم إضافة إعلانك بنجاح',
            ]
        );
    }

    public function edit_ads_data(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'ads_id' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'description' => 'required',
            'state_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $ads = Ads::where('id', $request->ads_id)->where('user_id', $user->id)->first();
        if (!$ads) {
            return response()->json(
                [
                    'message' => "عفوا لا يوجد اعلان",
                ],
                400
            );
        }

        $ads->title = $request->title;
        $ads->description = $request->description;
        $ads->category_id = $request->category_id;
        $ads->sub_category_id = $request->sub_category_id;
        //            $ads->user_id = $user->id;
        $ads->state_id = $request->state_id;
        $ads->price = $request->price;
        //            $ads->longitude = $request->longitude?:'';
        //            $ads->latitude = $request->latitude?:'';
        $ads->save();

        return response()->json(
            [
                'message' => 'تم تعديل إعلانك بنجاح',
            ]
        );
    }

    public function edit_ads_options(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'ads_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $ads = Ads::where('id', $request->ads_id)->where('user_id', $user->id)->first();

        if (!$ads) {
            return response()->json(
                [
                    'message' => "عفوا لا يوجد اعلان",
                ],
                400
            );
        }

        AdsOptions::where('ads_id', $ads->id)->delete();

        $options = json_decode($request->options);
        if ($options && count($options) > 0) {
            foreach ($options as $option) {
                if (!empty($option)) {
                    $ads_option = new AdsOptions();
                    $ads_option->ads_id = $ads->id;
                    $ads_option->selection_id = $option->selection_id;
                    $ads_option->option_id = $option->option_id;
                    $ads_option->option_name = $option->option_name;
                    $ads_option->option_value = $option->option_value;
                    $ads_option->save();
                }
            }
        }

        return response()->json(
            [
                'message' => 'تم التعديل بنجاح',
            ]
        );
    }

    public function edit_ads_photos(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'ads_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $ads = Ads::where('id', $request->ads_id)->where('user_id', $user->id)->first();

        if (!$ads) {
            return response()->json(
                [
                    'message' => "عفوا لا يوجد اعلان",
                ],
                400
            );
        }

        $photos = json_decode($request->photos);
        if ($photos && count($photos) > 0) {
            foreach ($photos as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo->photo;
                    $new_main = "uploads/" . $photo->photo;
                    if (is_file($old_main)) {
                        File::move($old_main, $new_main);
                        $other_photos = new AdsPhotos();
                        $other_photos->ads_id = $ads->id;
                        $other_photos->photo = $photo->photo;
                        $other_photos->type = $photo->type;
                        $other_photos->save();
                    }
                }
            }
        }

        return response()->json(
            [
                'message' => 'تم التعديل بنجاح',
            ]
        );
    }

    public function delete_follow(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $like = Follows::where('user_id', $user->id)->where('followed_user', $request->user_id)->first();
        if (!$like) {
            return response()->json([
                'message' => "لا يوجد مستخدم ",
            ], 400);
        } else {
            $like->delete();
            return response()->json([
                'message' => "تم الغاء المتابعة",
            ]);
        }
    }

    public function get_notifications_count()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $profits = \App\Models\Balance::where('user_id', $user->id)->where('status', 0)->sum('price');

        return response()->json(
            [
                'notification_count' => Notification::where('reciever_id', $user->id)->where('status', 0)->orderBy('id', 'DESC')->count(),

            ]
        );
    }

    public function notifications_read()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notifications = Notification::where('reciever_id', $user->id)->where('status', 0)->get();
        foreach ($notifications as $notification) {
            $notification->status = 1;
            $notification->save();
        }
        return response()->json(
            [
                'status' => 200,
                'message' => 'Notification read',
            ]
        );
    }

    public function refuse_order(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $notification = Notification::where('id', $request->notification_id)->where('reciever_id', $user->id)->first();
        if ($notification) {
            $order = Orders::find($notification->order_id);
            if ($order) {
                if ($order->service_id == 4 || $order->service_id == 5) {
                    $this_order = Orders::where('id', $order->id)->where('status', 0)->where('representative_id', $user->id)->first();
                    if ($this_order) {
                        $this_order->status = 3;
                        $this_order->save();

                        if ($this_order->service_id == 4) {
                            $this_order->load('getFlight');
                        } elseif ($this_order->service_id == 5) {
                            $this_order->load('getCarTrip');
                        }

                        $notification55 = new Notification();
                        $notification55->sender_id = $notification->reciever_id;
                        $notification55->reciever_id = $this_order->user_id;
                        $notification55->order_id = $order->id;
                        $notification55->type = 7;
                        $notification55->message = "قام " . @$order->getRepresentative->username . " برفض طلبك للتوصيل من مدينة لأخرى ";
                        $notification55->message_en = @$order->getRepresentative->username . " Refused to transfer your shipment from city to other .";
                        $notification55->save();

                        $optionBuilder = new OptionsBuilder();
                        $optionBuilder->setTimeToLive(60 * 20);

                        if ($order->getUser->lang == "en") {
                            $notification_title = "Refused your order";
                            $notification_message = $notification55->message_en;
                        } else {
                            $notification_title = "رفض طلبك";
                            $notification_message = $notification55->message;
                        }
                        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                        $notificationBuilder->setBody($notification_message)
                            ->setSound('default');

                        $dataBuilder = new PayloadDataBuilder();
                        $dataBuilder->addData([
                            'data' => [
                                'notification_type' => (int)$notification55->type,
                                'notification_title' => $notification_title,
                                'notification_message' => $notification_message,
                                'notification_data' => $this_order,
                            ],
                        ]);

                        $option = $optionBuilder->build();
                        $notification = $notificationBuilder->build();
                        $data = $dataBuilder->build();

                        $token = @$notification55->getReciever->device_token;

                        if ($token) {
                            $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                            $downstreamResponse->numberSuccess();
                            $downstreamResponse->numberFailure();
                            $downstreamResponse->numberModification();
                        }

                        return response()->json(
                            [
                                'status' => 200,
                                'message' => trans('messages.order_was_refused_successfully'),
                            ]
                        );
                    } elseif (Orders::where('id', $order->id)->whereIn('status', [1, 2, 4])->where('representative_id', $user->id)->first()) {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.order_was_accepted_before'),
                            ]
                        );
                    } elseif (Orders::where('id', $order->id)->where('status', 3)->where('representative_id', $user->id)->first()) {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.order_was_refused_before'),
                            ]
                        );
                    } else {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.not_allowed_to_refuse_other_one_order'),
                            ]
                        );
                    }
                } else {
                    //other services
                }
            } else {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => trans('messages.there_is_no_order'),
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.this_notification_not_for_you'),
                ]
            );
        }
    }

    public function accept_order(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $notification = Notification::where('id', $request->notification_id)->where('reciever_id', $user->id)->first();
        if ($notification) {
            $order = Orders::find($notification->order_id);
            if ($order) {
                if ($order->service_id == 4 || $order->service_id == 5) {
                    $this_order = Orders::where('id', $order->id)->where('status', 0)->where('representative_id', $user->id)->first();
                    if ($this_order) {
                        $this_order->status = 1;
                        $this_order->accept_date = date('Y-m-d H:i:s');
                        $this_order->save();
                        $this_order->load('getService');
                        if ($this_order->service_id == 4) {
                            $this_order->load('getFlight');
                        } elseif ($this_order->service_id == 5) {
                            $this_order->load('getCarTrip');
                        }
                        $notification55 = new Notification();
                        $notification55->sender_id = $notification->reciever_id;
                        $notification55->reciever_id = $this_order->user_id;
                        $notification55->order_id = $order->id;
                        $notification55->type = 8;
                        $notification55->message = "تم قبول طلبك من " . @$order->getRepresentative->username;
                        $notification55->message_en = " Your order approved from " . @$order->getRepresentative->username;
                        $notification55->save();

                        $optionBuilder = new OptionsBuilder();
                        $optionBuilder->setTimeToLive(60 * 20);

                        if ($order->getUser->lang == "en") {
                            $notification_title = "Accepted your order";
                            $notification_message = $notification55->message_en;
                        } else {
                            $notification_title = "موافقة على الطلب";
                            $notification_message = $notification55->message;
                        }
                        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                        $notificationBuilder->setBody($notification_message)
                            ->setSound('default');

                        $dataBuilder = new PayloadDataBuilder();
                        $dataBuilder->addData([
                            'data' => [
                                'notification_type' => (int)$notification55->type,
                                'notification_title' => $notification_title,
                                'notification_message' => $notification_message,
                                'notification_data' => $this_order,
                            ],
                        ]);

                        $option = $optionBuilder->build();
                        $notification = $notificationBuilder->build();
                        $data = $dataBuilder->build();

                        $token = @$notification55->getReciever->device_token;

                        if ($token) {
                            $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                            $downstreamResponse->numberSuccess();
                            $downstreamResponse->numberFailure();
                            $downstreamResponse->numberModification();
                        }

                        return response()->json(
                            [
                                'status' => 200,
                                'message' => trans('messages.order_was_accepted_successfully'),
                            ]
                        );
                    } elseif (Orders::where('id', $order->id)->whereIn('status', [1, 2, 4])->where('representative_id', $user->id)->first()) {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.order_was_accepted_before'),
                            ]
                        );
                    } elseif (Orders::where('id', $order->id)->where('status', 3)->where('representative_id', $user->id)->first()) {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.order_was_refused_before'),
                            ]
                        );
                    } else {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.not_allowed_to_accept_other_one_order'),
                            ]
                        );
                    }
                } else {
                    $this_order = Orders::where('id', $order->id)->where('status', 0)->first();
                    if ($this_order) {
                        $this_order->status = 1;
                        $this_order->representative_id = $user->id;
                        $this_order->accept_date = date('Y-m-d H:i:s');
                        $this_order->save();
                        $this_order->load('getService');

                        $notification55 = new Notification();
                        $notification55->sender_id = $notification->reciever_id;
                        $notification55->reciever_id = $this_order->user_id;
                        $notification55->order_id = $order->id;
                        $notification55->type = 8;
                        $notification55->message = "تم قبول طلبك من " . @$this_order->getRepresentative->username;
                        $notification55->message_en = " Your order approved from " . @$this_order->getRepresentative->username;
                        $notification55->save();

                        $optionBuilder = new OptionsBuilder();
                        $optionBuilder->setTimeToLive(60 * 20);

                        if ($order->getUser->lang == "en") {
                            $notification_title = "Accepted your order";
                            $notification_message = $notification55->message_en;
                        } else {
                            $notification_title = "موافقة على الطلب";
                            $notification_message = $notification55->message;
                        }
                        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                        $notificationBuilder->setBody($notification_message)
                            ->setSound('default');

                        $dataBuilder = new PayloadDataBuilder();
                        $dataBuilder->addData([
                            'data' => [
                                'notification_type' => (int)$notification55->type,
                                'notification_title' => $notification_title,
                                'notification_message' => $notification_message,
                                'notification_data' => $this_order,
                            ],
                        ]);

                        $option = $optionBuilder->build();
                        $notification = $notificationBuilder->build();
                        $data = $dataBuilder->build();

                        $token = @$notification55->getReciever->device_token;

                        if ($token) {
                            $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                            $downstreamResponse->numberSuccess();
                            $downstreamResponse->numberFailure();
                            $downstreamResponse->numberModification();
                        }

                        return response()->json(
                            [
                                'status' => 200,
                                'message' => trans('messages.order_was_accepted_successfully'),
                            ]
                        );
                    } elseif (Orders::where('id', $order->id)->whereIn('status', [1, 2, 4])->where('representative_id', $user->id)->first()) {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.order_was_accepted_before'),
                            ]
                        );
                    } elseif (Orders::where('id', $order->id)->where('status', 3)->where('representative_id', $user->id)->first()) {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.order_was_refused_before'),
                            ]
                        );
                    } else {
                        return response()->json(
                            [
                                'status' => 400,
                                'message' => trans('messages.not_allowed_to_accept_other_one_order'),
                            ]
                        );
                    }
                }
            } else {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => trans('messages.there_is_no_order'),
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.this_notification_not_for_you'),
                ]
            );
        }
    }

    public function export_contract(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $order = Orders::where('id', $request->order_id)->where('representative_id', $user->id)->where('status', 1)->first();
        if ($order) {
            if ($order->service_id == 4 || $order->service_id == 5) {
                $this_order = Orders::where('id', $order->id)->where('status', 1)->where('representative_id', $user->id)->first();
                if ($this_order) {
                    $this_order->status = 4;
                    $this_order->save();

                    if ($this_order->service_id == 4) {
                        $this_order->load('getFlight');
                    } elseif ($this_order->service_id == 5) {
                        $this_order->load('getCarTrip');
                    }

                    $notification55 = new Notification();
                    $notification55->sender_id = $user->id;
                    $notification55->reciever_id = $order->user_id;
                    $notification55->order_id = $order->id;
                    $notification55->type = 9;
                    $notification55->message = "قام " . @$order->getRepresentative->username . " يتأكيد استلام الارسالية ";
                    $notification55->message_en = @$order->getRepresentative->username . " Confirmed receiving shipment.";
                    $notification55->save();

                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 20);

                    if ($order->getUser->lang == "en") {
                        $notification_title = "Confirm receive shipment";
                        $notification_message = $notification55->message_en;
                    } else {
                        $notification_title = "تاكيد استلام الارسالية";
                        $notification_message = $notification55->message;
                    }
                    $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                    $notificationBuilder->setBody($notification_message)
                        ->setSound('default');

                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData([
                        'data' => [
                            'notification_type' => (int)$notification55->type,
                            'notification_title' => $notification_title,
                            'notification_message' => $notification_message,
                            'notification_data' => $this_order,
                        ],
                    ]);

                    $option = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data = $dataBuilder->build();

                    $token = @$notification55->getReciever->device_token;

                    if ($token) {
                        $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                        $downstreamResponse->numberSuccess();
                        $downstreamResponse->numberFailure();
                        $downstreamResponse->numberModification();
                    }

                    return response()->json(
                        [
                            'status' => 200,
                            'message' => trans('messages.confirm_export_contract'),
                        ]
                    );
                } elseif (Orders::where('id', $order->id)->whereIn('status', [0, 4])->where('representative_id', $user->id)->first()) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => trans('messages.you_must_recieve_request_first'),
                        ]
                    );
                } elseif (Orders::where('id', $order->id)->where('status', 3)->where('representative_id', $user->id)->first()) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => trans('messages.order_was_refused_before'),
                        ]
                    );
                } else {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => trans('messages.not_allowed_to_accept_other_one_order'),
                        ]
                    );
                }
            } else {

                $this_order = Orders::where('id', $order->id)->where('status', 1)->where('representative_id', $user->id)->first();

                if ($this_order) {
                    $this_order->status = 4;
                    $this_order->save();
                    $this_order->load('getService');
                    $notification55 = new Notification();
                    $notification55->sender_id = $user->id;
                    $notification55->reciever_id = $order->user_id;
                    $notification55->order_id = $order->id;
                    $notification55->type = 9;
                    $notification55->message = "قام " . @$order->getRepresentative->username . " يتأكيد استلام الارسالية ";
                    $notification55->message_en = @$order->getRepresentative->username . " Confirmed receiving shipment.";
                    $notification55->save();

                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 20);

                    if ($order->getUser->lang == "en") {
                        $notification_title = "Confirm receive shipment";
                        $notification_message = $notification55->message_en;
                    } else {
                        $notification_title = "تاكيد استلام الارسالية";
                        $notification_message = $notification55->message;
                    }
                    $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                    $notificationBuilder->setBody($notification_message)
                        ->setSound('default');

                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData([
                        'data' => [
                            'notification_type' => (int)$notification55->type,
                            'notification_title' => $notification_title,
                            'notification_message' => $notification_message,
                            'notification_data' => $this_order,
                        ],
                    ]);

                    $option = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data = $dataBuilder->build();

                    $token = @$notification55->getReciever->device_token;

                    if ($token) {
                        $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                        $downstreamResponse->numberSuccess();
                        $downstreamResponse->numberFailure();
                        $downstreamResponse->numberModification();
                    }

                    if ($this_order->service_id == 1) {
                        return response()->json(
                            [
                                'status' => 200,
                                'message' => trans('messages.confirm_access_to_store'),
                            ]
                        );
                    } else {
                        return response()->json(
                            [
                                'status' => 200,
                                'message' => trans('messages.confirm_export_contract'),
                            ]
                        );
                    }
                } elseif (Orders::where('id', $order->id)->whereIn('status', [0, 4])->where('representative_id', $user->id)->first()) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => trans('messages.you_must_recieve_request_first'),
                        ]
                    );
                } elseif (Orders::where('id', $order->id)->where('status', 3)->where('representative_id', $user->id)->first()) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => trans('messages.order_was_refused_before'),
                        ]
                    );
                } else {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => trans('messages.not_allowed_to_accept_other_one_order'),
                        ]
                    );
                }
            }
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.there_is_no_order'),
                ]
            );
        }
    }

    public function confirm_deliver(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->errors()->first(),
                ],
                400
            );
        }

        $order = Orders::where('id', $request->order_id)->where('representative_id', $user->id)->where('status', 1)->first();
        if ($order) {
            $order->status = 2;
            $order->deliver_date = date('Y-m-d H:i:s');
            $order->save();
            if ($order->payment == 0) {
                $balance = new Balance();
                $balance->order_id = $order->id;
                $balance->user_id = $order->representative_id;
                if (!$order->cobon) {
                    $balance->price = - (@$order->getOffer->price * Settings::find(10)->value / 100);
                    $balance->site_profits = $order->getOffer->price * Settings::find(10)->value / 100;
                } else {
                    $balance->price = - ((@$order->getOffer->price * Settings::find(10)->value / 100) - (@$order->getOffer->price * $order->getCobon->percent / 100));
                    $balance->site_profits = ($order->getOffer->price * Settings::find(10)->value / 100) - (@$order->getOffer->price * $order->getCobon->percent / 100);
                }
                $balance->balance_type_id = 1;
                $balance->save();
            } else {
                //                       //online payment

            }

            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $order->user_id;
            $notification55->order_id = $order->id;
            $notification55->type = 10;
            $notification55->message = "قام " . @$order->getRepresentative->username . " يتأكيد تسليم الطلب لك ";
            $notification55->message_en = @$order->getRepresentative->username . " Confirmed deliver your order.";
            $notification55->save();
            $notification55 = Notification::find($notification55->id);
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            if ($order->getUser->lang == "en") {
                $notification_title = "Confirm deliver order";
                $notification_message = $notification55->message_en;
            } else {
                $notification_title = "تاكيد تسليم الطلب";
                $notification_message = $notification55->message;
            }
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
                    'notification_data' => new NotificationsResource($notification55),
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

            return response()->json(
                [
                    'message' => trans('messages.order_delivered'),
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.there_is_no_order'),
                ]
            );
        }
    }

    public function make_invoice(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'order_price' => 'required|numeric',
            'delivery_price' => 'required|numeric',
            'all_price' => 'required|numeric',
            'photo' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $order = Orders::where('id', $request->order_id)->where('representative_id', $user->id)->where('status', 4)->first();
        if ($order) {

            $invoice = new Invoices();
            $invoice->representative_id = $user->id;
            $invoice->order_id = $order->id;
            $invoice->price = $request->all_price;
            $invoice->order_price = $request->order_price;
            $invoice->delivery_price = $request->delivery_price;
            $file = $request->file('photo');
            if ($request->hasFile('photo')) {
                $fileName = 'invoice-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $request->file('photo')->move($destinationPath, $fileName);
                $invoice->photo = $fileName;
            }
            $invoice->save();

            $order->invoice_status = 1;
            $order->save();
            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $order->user_id;
            $notification55->order_id = $order->id;
            $notification55->type = 14;
            $notification55->message = "قام " . @$order->getRepresentative->username . " بإصدار فاتورة بطلبك ";
            $notification55->message_en = @$order->getRepresentative->username . " made invoice for your order .";
            $notification55->save();

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            if ($order->getUser->lang == "en") {
                $notification_title = "Export invoice";
                $notification_message = $notification55->message_en;
            } else {
                $notification_title = "إصدار فاتورة";
                $notification_message = $notification55->message;
            }
            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => (int)$notification55->type,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification_message,
                    'notification_data' => $invoice,
                ],
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $token = @$notification55->getReciever->device_token;

            if ($token) {
                $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }

            return response()->json(
                [
                    'status' => 200,
                    'message' => trans('messages.invoice_sent_successfully'),
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => trans('messages.there_is_no_order'),
                ]
            );
        }
    }

    public function my_cobons(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cobons = Cobons::orderBy('id', 'desc')->get();

        return response()->json(CobonsResourses::collection($cobons), 200);
    }

    public function notifications(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_message = App::isLocale('ar') ? 'message' : 'message_en as message';
        $select_status = App::getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';
        $notifications = Notification::select('id', $select_message, 'created_at', 'sender_id', 'type', 'order_id')->where('reciever_id', $user->id)
            ->with(['getSender' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->orderBy('id', 'DESC')
            //            ->with('order')

            ->with(['order' => function ($q) use ($select_status) {
                $q->select('orders.id', 'orders.final_price', 'orders.marketed_date', 'orders.is_edit as has_second_order', 'order_status.color', 'orders.parent_order as has_parent_order', 'orders.status', $select_status, 'orders.payment_method', 'payment_methods.name as payment_method_name')
                    ->selectRaw('(CONCAT ("' . url('/') . '/i/", orders.short_code)) as download_url')
                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.order_id =orders.id) as products_count')
                    ->leftJoin('order_status', 'order_status.id', 'orders.status')
                    ->join('payment_methods', 'orders.payment_method', 'payment_methods.id')
                    ->with('transfer_photo.to_bank', 'balance', 'transaction');
            }])
            ->paginate(10);

        Notification::where('reciever_id', '=', $user->id)->where('status', 0)
            ->update(['status' => 1]);

        $notifications->{'notifications'} = NotificationsResource::collection($notifications);

        return response()->json($notifications, 200);
    }

    public function my_bills(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($request->headers->get('lang') == "ar") {
            $bills = BankTransfer::where('user_id', $user->id)
                ->select('id', 'money_transfered as money', 'created_at')
                ->selectRaw('(CASE WHEN type = "order" THEN "طلب خدمة" ELSE "طلب ترقية عضوية" END) AS name')
                ->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' => $bills,
                ]
            );
        } else {
            $bills = BankTransfer::where('user_id', $user->id)
                ->select('id', 'money_transfered as money', 'created_at')
                ->selectRaw('(CASE WHEN type = "order" THEN "Order" ELSE "Membership" END) AS name')
                ->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' => $bills,
                ]
            );
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $orders,
            ]
        );
    }

    public function my_reports()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (App::isLocale('ar')) {
            $reports = Reports::whereIn('order_id', function ($query) use ($user) {
                $query->select('id')
                    ->from(with(new Orders())->getTable())
                    ->where('user_id', $user->id)
                    ->where('status', 4);
            })->where('order_id', "!=", 0)
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getOrder.getCurrency' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->select('id', 'date_of_report', 'service_id', 'order_id')
                ->selectRaw('(CONCAT ("' . url('/') . '/' . App::getLocale() . '/single-report/", id)) as url')
                ->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' => $reports,
                ]
            );
        } else {
            $reports = Reports::whereIn('order_id', function ($query) use ($user) {
                $query->select('id')
                    ->from(with(new Orders())->getTable())
                    ->where('user_id', $user->id)
                    ->where('status', 4);
            })->where('order_id', "!=", 0)
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getOrder.getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->select('id', 'date_of_report', 'service_id', 'order_id')
                ->selectRaw('(CONCAT ("' . url('/') . '/' . App::getLocale() . '/single-report/", id)) as url')
                ->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' => $reports,
                ]
            );
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $orders,
            ]
        );
    }

    public function get_messages(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $other_user = $request->admin_id;
        $messages = Messages::select('id', 'message', 'reciever_id', 'sender_id', 'type', 'status', 'sender_name', 'created_at')
            ->selectRaw('(CASE WHEN image = "" THEN image ELSE (CONCAT ("' . url('') . '/uploads/", image)) END) AS image')
            ->selectRaw('(CASE WHEN sender_id = ' . $user->id . ' THEN true ELSE false END) AS sender')
            ->where('sender_id', $user->id)->orWhere('reciever_id', $user->id)
            ->orderBy('id', 'DESC')
            //            ->with('getCreatedAtAttribute')
            ->paginate(10);

        $res = [];
        foreach ($messages as $message) {
            $message->{"created_time"} = Carbon::parse($message->created_at)->diffForHumans();
            $res[] = $message;
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $messages,
            ]
        );
    }

    public function get_price(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'service_id' => 'required',
            'state_id' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        } else {

            $price = Prices::where('state_id', $request->state_id)
                ->where('service_id', $request->service_id)
                ->with('getCurrency')
                ->first();
            if ($price) {
                return response()->json(
                    [
                        'status' => 200,
                        'data' => $price,
                    ]
                );
            } else {

                return response()->json(
                    [
                        'status' => 400,
                        'message' => trans('messages.price_doesnot_determined_yet'),
                    ]
                );
            }
        }
    }

    public function find_representative(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $order = Orders::where('id', $request->order_id)->whereIn('status', [0, 1])->where('user_id', $user->id)->first();
        if (!$order) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا لا يوجد طلبات بهذا العنوان او ان الطلب غير جديد وتم اسناده الى مندوب بالفعل",
                ]
            );
        }

        if ($order->status == 1) {
            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $order->representative_id;
            $notification55->order_id = $order->id;
            $notification55->type = 12;
            $notification55->message = "قام " . @$order->getUser->username . " بالغاء الطلب رقم  " . $order->id;
            $notification55->message_en = @$order->getUser->username . " Cancelled order number " . $order->id;
            $notification55->save();

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            if ($order->getUser->lang == "en") {
                $notification_title = "Canecel order";
                $notification_message = $notification55->message_en;
            } else {
                $notification_title = "إلغاء الطلب";
                $notification_message = $notification55->message;
            }
            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => (int)$notification55->type,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification_message,
                    'notification_data' => $order,
                ],
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $token = @$notification55->getReciever->device_token;

            if ($token) {
                $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        }

        $order->status = 0;
        $order->representative_id = 0;
        $order->save();
        $representatives = User::where('user_type_id', 4)->where('ready', 1)->where('block', 0)
            ->whereIn('id', function ($query) use ($user, $order) {
                $query->select('user_id')
                    ->from(with(new UserServices())->getTable())
                    ->where('service_id', $order->service_id);
            })
            ->select(
                "*",
                DB::raw("6371 * acos(cos(radians(" . $order->from_lat . "))
        * cos(radians(users.latitude))
        * cos(radians(users.longitude) - radians(" . $order->from_long . "))
        + sin(radians(" . $order->from_lat . "))
        * sin(radians(users.latitude))) AS distance")
            )
            //            ->having('distance','<=',Settings::find(13)->value)
            ->orderBy("distance", 'ASC')
            ->get();

        foreach ($representatives as $representative) {

            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $representative->id;
            $notification55->order_id = $order->id;
            $notification55->type = 11;
            $notification55->message = "قام " . @$order->getUser->username . " بارسال طلب " . @$order->getService->name;
            $notification55->message_en = @$order->getUser->username . " Send you request " . @$order->getService->name_en;
            $notification55->save();

            $order->{'notification_id'} = $notification55->id;

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            if ($representative->lang == "en") {
                $notification_title = "Request " . @$order->getService->name_en;
                $notification_message = $notification55->message_en;
            } else {
                $notification_title = " طلب " . @$order->getService->name;
                $notification_message = $notification55->message;
            }

            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => (int)$notification55->type,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification_message,
                    'notification_data' => $order,
                ],
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $token = @$notification55->getReciever->device_token;

            if ($token) {
                $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        }

        return response()->json(
            [
                'status' => 200,
                'message' => "تم ارسال الطلب الى " . count($representatives) . " مناديب وفي انتظار قبول أحدهم",
                'order_id' => $order->id,
                //                'representatives' => $representatives
            ]
        );
    }

    public function add_order(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'from_long' => 'required',
            'from_lat' => 'required',
            'to_long' => 'required',
            'to_lat' => 'required',
            'from_address' => 'required',
            'to_address' => 'required',
            'description' => 'required',
            'delivery_time' => 'required',
            'store_name' => 'required',
            'store_photo' => 'required',
            'store_icon' => 'required',
            'place_id' => 'required',
            'distance' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if ($request->coupon) {
            $code = Cobons::where('code', $request->coupon)->first();
            if ($code) {
                $used_cobons = Orders::where('cobon', $code->code)->whereIn('status', [1, 2])->count();
                if ($code->usage_quota <= $used_cobons) {
                    return response()->json(
                        [
                            'message' => "عفوا تم استخدام العدد المسموح به لهذا الكوبون",
                        ],
                        400
                    );
                } elseif (Orders::where('user_id', $user->id)->where('cobon', $code->code)->whereIn('status', [1, 2])->count()) {
                    //                    return response()->json(
                    //                        [
                    //                            'message' => "عفوا لا يمكنك إستخدام الكوبون مرة أخرى",
                    //                        ], 400);
                } else {
                    $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($code->created_at)) . " +" . $code->days . " days"));
                    if (date('Y-m-d') > $date_of_end) {
                        return response()->json(
                            [
                                'message' => "عفوا انتهت صلاحية الكوبون",
                            ],
                            400
                        );
                    }
                }
            } else {
                return response()->json(
                    [
                        'message' => __('messages.coupon_used_before'),
                    ],
                    400
                );
            }
        }

        $order = new Orders();
        $order->user_id = $user->id;
        $order->cobon = $request->coupon ? $request->coupon : '';
        $order->from_address = $request->from_address ? $request->from_address : "";
        $order->from_long = $request->from_long ? $request->from_long : "";
        $order->from_lat = $request->from_lat ? $request->from_lat : "";
        $order->to_address = $request->to_address ? $request->to_address : "";
        $order->to_long = $request->to_long ? $request->to_long : "";
        $order->to_lat = $request->to_lat ? $request->to_lat : "";
        $order->description = $request->description ? $request->description : "";
        $order->store_name = $request->store_name;
        $order->store_photo = $request->store_photo;
        $order->store_icon = $request->store_icon;
        $order->distance = $request->distance ? $request->distance : 0;
        $order->place_id = $request->place_id ? $request->place_id : "";
        $order->delivery_time = $request->delivery_time ? $request->delivery_time : 0;

        $order->save();

        $photos = json_decode($request->photos);
        if ($photos && count($photos) > 0) {
            foreach ($photos as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo->photo;
                    $new_main = "uploads/" . $photo->photo;
                    if (is_file($old_main)) {
                        File::move($old_main, $new_main);
                        $other_photos = new OrderPhotos();
                        $other_photos->order_id = $order->id;
                        $other_photos->photo = $photo->photo;
                        $other_photos->save();
                    }
                }
            }
        }

        $representatives = User::where('user_type_id', 4)->where('activate', 1)->where('block', 0)
            ->whereIn('id', function ($query) use ($user, $order) {
                $query->select('user_id')
                    ->from(with(new Stores())->getTable())
                    ->where('place_id', $order->place_id);
            })
            ->orderBy("id", 'desc')
            ->get();

        $tokens = [];

        foreach ($representatives as $representative) {
            $arr_of_tokens = [];
            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $representative->id;
            $notification55->order_id = $order->id;
            $notification55->type = 5;
            $notification55->message = "لديك طلب توصيل جديد من متجر" . @$order->store_name;
            $notification55->message_en = " You have a new deliver order from " . @$order->store_name . " store .";
            $notification55->save();
            $order->{'notification_id'} = $notification55->id;
            $arr_of_tokens = DeviceTokens::where('user_id', $representative->id)->pluck('device_token')->toArray();
            if ($representative->notification == 1 && count($arr_of_tokens) > 0) {
                $tokens = array_merge($tokens, $arr_of_tokens);
            }
        }

        if (count($tokens) > 0) {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            if ($representative->lang == "en") {
                $notification_title = "Request Deliver order";
                $notification_message = $notification55->message_en;
            } else {
                $notification_title = " طلب توصيل طلب";
                $notification_message = $notification55->message;
            }
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
                    'notification_data' => $order,
                ],
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            if (count($tokens) > 0) {
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        }

        return response()->json(
            [
                'message' => __('messages.order_added_successfully'),
            ]
        );
    }

    public function ask_upgrade(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
            'photo' => 'required|image',
            'package_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $order = new BankTransfer();
        $order->user_id = $user->id;
        $order->bank_name = $request->bank_name;
        $order->account_name = $request->account_name;
        $order->account_number = $request->account_number;
        $order->package_id = $request->package_id;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'bank-transfer-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $order->photo = $fileName;
        }
        $order->save();

        return response()->json(
            [
                'message' => "تم إرسال الطلب بنجاح .. انتظر موافقة الادارة",
            ]
        );
    }

    public function ask_store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'state_id' => 'required',
            'photo' => 'required|image',
            'commercial_registration_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $order = new Stores();
        $order->user_id = $user->id;
        $order->name = $request->name;
        $order->address = $request->address;
        $order->commercial_registration_no = $request->commercial_registration_no;
        $order->state_id = $request->state_id;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'bank-transfer-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $order->photo = $fileName;
        }
        $order->save();

        return response()->json(
            [
                'message' => "تم إرسال الطلب بنجاح .. انتظر موافقة الادارة",
            ]
        );
    }

    public function edit_order(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'from_long' => 'required',
            'from_lat' => 'required',
            'to_long' => 'required',
            'to_lat' => 'required',
            'from_address' => 'required',
            'to_address' => 'required',
            'description' => 'required',
            'delivery_time' => 'required',
            'store_name' => 'required',
            'store_photo' => 'required',
            'store_icon' => 'required',
            'place_id' => 'required',
            'distance' => 'required',
            'order_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $order = Orders::where('id', $request->order_id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json(
                [
                    'message' => "عفوا هذه الطلب ليس لك",
                ],
                400
            );
        }

        if ($order->status != 0) {
            return response()->json(
                [
                    'message' => __('messages.you_can_not_edit_running_order'),
                ],
                400
            );
        }

        $order->from_address = $request->from_address ? $request->from_address : "";
        $order->from_long = $request->from_long ? $request->from_long : "";
        $order->from_lat = $request->from_lat ? $request->from_lat : "";
        $order->to_address = $request->to_address ? $request->to_address : "";
        $order->to_long = $request->to_long ? $request->to_long : "";
        $order->to_lat = $request->to_lat ? $request->to_lat : "";
        $order->description = $request->description ? $request->description : "";
        $order->store_name = $request->store_name;
        $order->store_photo = $request->store_photo;
        $order->store_icon = $request->store_icon;
        $order->distance = $request->distance ? $request->distance : 0;
        $order->place_id = $request->place_id ? $request->place_id : "";
        $order->delivery_time = $request->delivery_time ? $request->delivery_time : 0;
        $order->save();

        $photos = json_decode($request->photos);
        if ($photos && count($photos) > 0) {
            foreach ($photos as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo->photo;
                    $new_main = "uploads/" . $photo->photo;
                    if (is_file($old_main)) {
                        File::move($old_main, $new_main);
                        $other_photos = new OrderPhotos();
                        $other_photos->order_id = $order->id;
                        $other_photos->photo = $photo->photo;
                        $other_photos->save();
                    }
                }
            }
        }

        return response()->json(
            [
                'message' => __('messages.order_edited_successfully'),
            ]
        );
    }

    public function request_representative(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'bank_id' => 'required',
            'bank_account' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'car_plate' => 'required',
            'liscense' => $request->liscense ? 'required|image' : '',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $user_ = new RequestRepresentative();
        $user_->user_id = $user->id;
        $user_->full_name = $request->input('full_name');
        $user_->brand = $request->input('brand');
        $user_->model = $request->input('model');
        $user_->car_plate = $request->input('car_plate');
        $user_->bank_id = $request->input('bank_id');
        $user_->bank_account = $request->input('bank_account');

        $file = $request->file('liscense');
        if ($request->hasFile('liscense')) {
            $fileName = 'liscense-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('liscense')->move($destinationPath, $fileName);
            $user_->liscense = $fileName;
        }

        $user_->save();

        $notification55 = new Notification();
        $notification55->sender_id = 1;
        $notification55->reciever_id = $user->id;
        //        $notification55 -> message_id = $object->id;
        $notification55->type = 2;
        $notification55->message = "تم ارسال بياناتك الى الادارة انتظر الموافقة في أقرب وقت";
        $notification55->message_en = "Data was sent to administration wait for approval soon";
        $notification55->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notification_title = \app()->getLocale() == "ar" ? "تأكيد ارسال طلب" : "Confirm sending request";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification55->message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'data' => [
                'notification_type' => (int)$notification55->type,
                'notification_title' => $notification_title,
                'notification_message' => $notification55->message,
                'notification_data' => null,
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

        return response()->json(
            [
                'message' => __('messages.request_representative_sent_successfully'),
            ]
        );
    }

    public function add_order_card(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'payment_type' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $cards = json_decode($request->cards);
        if (count($cards) > 0) {
            $final_price = 0;
            $order = new Orders();
            $order->user_id = $user->id;
            $order->service_id = 8;
            $order->status = 1;
            $order->save();

            foreach ($cards as $key => $card) {
                $order_details = new OrderShipments();
                $order_details->order_id = $order->id;
                $order_details->card_id = $card->id;
                $order_details->price = $card->price;
                $order_details->quantity = $card->quantity;
                $order_details->save();
                $final_price = $final_price + ($order_details->price * $order_details->quantity);
            }

            $order->final_price = number_format((float)$final_price, 2, '.', '');
            $order->price_after_discount = number_format((float)$final_price, 2, '.', '');

            $order->save();

            $payment_id = $this->checkout($order, $request);

            $order->load('getService');
            return response()->json(
                [
                    'status' => 200,
                    'message' => __('messages.order_added_successfully'),
                    'order_id' => @$order->id,
                    'payment_id' => $payment_id,

                    //                'representatives' => $representatives
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا لا يوجد بطاقات مختارة",
                    //                'representatives' => $representatives
                ]
            );
        }
    }

    public function pay_mandoob_money(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $url = "https://test.oppwa.com/v1/checkouts";
        $email = @$user->email ?: "info@mrmandoob.com";
        $data = "entityId=" . Settings::find(18)->value .
            "&amount=" . $request->money .
            "&currency=SAR" .
            "&paymentType=" . $request->payment_type .
            "&customer.email=" . $email .
            "&notificationUrl=https://mrmandoob.com/api/v1/payment-money1" .
            "&merchantTransactionId=" . uniqid() .
            "&testMode=EXTERNAL";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . Settings::find(17)->value
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);

        //        echo "<pre>";
        $values = json_decode($responseData);
        //        echo $values->id;
        $MandoobPayments = new MandoobPayments();
        $MandoobPayments->payment_id = $values->id;
        $MandoobPayments->user_id = $user->id;
        $MandoobPayments->money = $request->money;
        $MandoobPayments->save();

        return response()->json(
            [
                'status' => 200,
                'message' => "Done Successfully",
                'payment_id' => $MandoobPayments->payment_id,
            ]
        );
    }

    public function checkout($order, $request)
    {

        $order = Orders::where('id', $order->id)->where('service_id', 8)->first();
        $url = "https://test.oppwa.com/v1/checkouts";
        $email = @$order->getUser->email ?: "info@mrmandoob.com";
        $data = "entityId=" . Settings::find(18)->value .
            "&amount=" . $request->amount .
            "&currency=SAR" .
            "&paymentType=" . $request->payment_type .
            "&customer.email=" . $email .
            "&notificationUrl=https://mrmandoob.com/api/v1/payment1" .
            "&merchantTransactionId=" . $order->id .
            "&testMode=EXTERNAL";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . Settings::find(17)->value
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);

        //        echo "<pre>";
        $values = json_decode($responseData);
        //        echo $values->id;
        $order->payment_id = $values->id;
        $order->price_after_fees = $request->amount;
        $order->save();
        return $values->id;
    }

    public function notification_test(Request $request)
    {
        $device_token = "dMXZh0dEZxA:APA91bF_0XMJ8ffJtJExpxXwEsBeBepu30QS-Y4-iIjNiuEq3it4_ZSo804DvkjOOSUwT0JVwAZBn-DoS0QW6oQTXLzVYYnxcRjNr7RXwm0NrQFNhrMLTg7ID5GF8txxa5XN5o2J3wkA";
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('New message');
        $notificationBuilder->setBody("Hi Mohammed Antar Notification")
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['message' => 'Hi Mohammed Antar Data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $downstreamResponse = FCM::sendTo($request->device_token, $option, $notification, $data);
        echo 'success : ' . $downstreamResponse->numberSuccess() . "<br>";
        echo 'error : ' . $downstreamResponse->numberFailure() . "<br>";
        echo 'modification : ' . $downstreamResponse->numberModification() . "<br>";

        echo "<pre>";
        echo "return Array - you must remove all this tokens in your database";
        print_r($downstreamResponse->tokensToDelete());
        echo "return Array (key : oldToken, value : new token - you must change the token in your database )";
        //return Array (key : oldToken, value : new token - you must change the token in your database )
        print_r($downstreamResponse->tokensToModify());
        echo "return Array - you should try to resend the message to the tokens in the array";

        //return Array - you should try to resend the message to the tokens in the array
        print_r($downstreamResponse->tokensToRetry());
        echo "return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array";

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
        print_r($downstreamResponse->tokensWithError());
    }

    public function add_flight_shipment(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            //            'from_long' => 'required',
            //            'from_lat' => 'required',
            //            'to_long' => 'required',
            //            'to_lat' => 'required',
            'flight_id' => 'required',
            'national_id' => 'required',
            'description' => 'required',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        if ($request->cobon) {
            $code = Cobons::where('code', $request->cobon)->first();
            if ($code) {
                if ($code->used == 1) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => __('messages.coupon_used_before'),
                        ]
                    );
                } else {
                    $code->used = 1;
                    $code->save();
                }
            } else {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => __('messages.coupon_not_fount'),
                    ]
                );
            }
        }

        $flight = Flights::find($request->flight_id);
        if (!$flight) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('messages.error_in_flight_id'),
                ]
            );
        }

        $order = new Orders();
        $order->user_id = $user->id;
        $order->representative_id = $flight->user_id;
        $order->service_id = 4;
        $order->flight_id = $request->flight_id;
        if ($request->cobon) {
            $order->cobon = $code ? $code->id : '';
        }
        //        $order->from_name= $request->from_name ? $request->from_name : "" ;
        //        $order->from_address= $request->from_address ? $request->from_address : "" ;
        //        $order->from_long= $request->from_long;
        //        $order->from_lat= $request->from_lat;
        //        $order->to_name= $request->to_name ? $request->to_name : "" ;
        //        $order->to_address= $request->to_address ? $request->to_address : "" ;
        //        $order->shipments= $request->shipments ? $request->shipments : "" ;
        //
        //        $order->to_long= $request->to_long;
        //        $order->to_lat= $request->to_lat;

        $order->description = $request->description;
        $order->national_id = $request->national_id;

        $order->final_price = $flight->price_for_shipment;
        $order->price_after_discount = $request->final_price ? $request->final_price : 0;

        $order->site_percent = Settings::find(10)->value;
        $order->site_profits = ($flight->price_for_shipment * Settings::find(10)->value) / 100;
        $order->site_profits = -$order->site_profits;

        //        $order->distance= $request->distance;
        //        $order->price_for_km= $request->price_for_km;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $order->photo = $fileName;
        }
        $order->save();
        if ($order->photo) {
            $order->{'photo'} = url('/') . "/uploads/" . $order->photo;
        }
        $order->load(['getUser' => function ($query) {
            $query->select('*');
            $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
        }]);
        $order->load('getFlight');
        $order->load('getService');

        $notification55 = new Notification();
        $notification55->sender_id = $order->user_id;
        $notification55->reciever_id = $flight->user_id;
        $notification55->order_id = $order->id;
        $notification55->type = 5;
        $notification55->message = "لديك طلب جديد من  " . @$order->getUser->username;
        $notification55->message_en = " You have a new order from " . @$order->getUser->username;
        $notification55->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        if ($flight->getUser->lang == "en") {
            $notification_title = "Request Inter-city consignments (flying)";
            $notification_message = $notification55->message_en;
        } else {
            $notification_title = "طلب ارسالية بالطائرة";
            $notification_message = $notification55->message;
        }

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'data' => [
                'notification_type' => (int)$notification55->type,
                'notification_title' => $notification_title,
                'notification_message' => $notification_message,
                'notification_data' => $order,
            ],
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$notification55->getReciever->device_token;

        if ($token) {
            $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }

        if (isset($code)) {
            $code->order_id = $order->id;
            $code->save();
        }

        return response()->json(
            [
                'status' => 200,
                'message' => __('messages.order_added_successfully'),
                //                'order'=> $order
            ]
        );
    }

    public function add_car_shipment(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            //            'from_long' => 'required',
            //            'from_lat' => 'required',
            //            'to_long' => 'required',
            //            'to_lat' => 'required',
            'car_trip_id' => 'required',
            'national_id' => 'required',
            'description' => 'required',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        if ($request->cobon) {
            $code = Cobons::where('code', $request->cobon)->first();
            if ($code) {
                if ($code->used == 1) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => __('messages.coupon_used_before'),
                        ]
                    );
                } else {
                    $code->used = 1;
                    $code->save();
                }
            } else {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => __('messages.coupon_not_fount'),
                    ]
                );
            }
        }

        $car = CarTrips::find($request->car_trip_id);
        if (!$car) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('messages.error_in_flight_id'),
                ]
            );
        }

        $order = new Orders();
        $order->user_id = $user->id;
        $order->representative_id = $car->user_id;
        $order->service_id = 5;
        $order->car_trip_id = $request->car_trip_id;
        if ($request->cobon) {
            $order->cobon = $code ? $code->id : '';
        }
        //        $order->from_name= $request->from_name ? $request->from_name : "" ;
        //        $order->from_address= $request->from_address ? $request->from_address : "" ;
        //        $order->from_long= $request->from_long;
        //        $order->from_lat= $request->from_lat;
        //        $order->to_name= $request->to_name ? $request->to_name : "" ;
        //        $order->to_address= $request->to_address ? $request->to_address : "" ;
        //        $order->shipments= $request->shipments ? $request->shipments : "" ;
        //
        //        $order->to_long= $request->to_long;
        //        $order->to_lat= $request->to_lat;

        $order->description = $request->description;
        $order->national_id = $request->national_id;

        $order->final_price = $car->price_for_shipment;
        $order->price_after_discount = $request->final_price ? $request->final_price : 0;

        $order->site_percent = Settings::find(10)->value;
        $order->site_profits = ($car->price_for_shipment * Settings::find(10)->value) / 100;
        $order->site_profits = -$order->site_profits;

        //        $order->distance= $request->distance;
        //        $order->price_for_km= $request->price_for_km;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $order->photo = $fileName;
        }
        $order->save();
        if ($order->photo) {
            $order->{'photo'} = url('/') . "/uploads/" . $order->photo;
        }
        $order->load(['getUser' => function ($query) {
            $query->select('*');
            $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
        }]);

        $order->load('getCarTrip');
        $order->load('getService');

        $notification55 = new Notification();
        $notification55->sender_id = $order->user_id;
        $notification55->reciever_id = $car->user_id;
        $notification55->order_id = $order->id;
        $notification55->type = 6;
        $notification55->message = "لديك طلب جديد من  " . @$order->getUser->username;
        $notification55->message_en = " You have a new order from " . @$order->getUser->username;
        $notification55->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        if ($car->getUser->lang == "en") {
            $notification_title = "Request Intercity Shipping (Car)";
            $notification_message = $notification55->message_en;
        } else {
            $notification_title = "طلب ارسالية بالسيارة";
            $notification_message = $notification55->message;
        }

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'data' => [
                'notification_type' => (int)$notification55->type,
                'notification_title' => $notification_title,
                'notification_message' => $notification_message,
                'notification_data' => $order,
            ],
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$notification55->getReciever->device_token;

        if ($token) {
            $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }

        if (isset($code)) {
            $code->order_id = $order->id;
            $code->save();
        }

        if (isset($code)) {
            $code->order_id = $order->id;
            $code->save();
        }

        return response()->json(
            [
                'status' => 200,
                'message' => __('messages.order_added_successfully'),
                //                'order' => $order
            ]
        );
    }

    public function add_flight(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'from_city' => 'required',
            'to_city' => 'required',
            'date_of_leave' => 'required',
            'time_of_leave' => 'required',
            'flight_num' => 'required',
            'price_for_shipment' => 'required',
            'max_shipments' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $flight = new Flights();
        $flight->user_id = $user->id;
        $flight->from_city = $request->from_city;
        $flight->to_city = $request->to_city;
        $flight->date_of_leave = $request->date_of_leave;
        $flight->time_of_leave = $request->time_of_leave;
        $flight->flight_num = $request->flight_num;
        $flight->price_for_shipment = $request->price_for_shipment;
        $flight->max_shipments = $request->max_shipments;
        $flight->save();

        return response()->json(
            [
                'status' => 200,
                'message' => __('messages.flight_added_successfully'),
            ]
        );
    }

    public function edit_flight(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'from_city' => 'required',
            'to_city' => 'required',
            'date_of_leave' => 'required',
            'time_of_leave' => 'required',
            'flight_num' => 'required',
            'price_for_shipment' => 'required',
            'max_shipments' => 'required',
            'flight_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $flight = Flights::where('id', $request->flight_id)->where('user_id', $user->id)->first();

        if (!$flight) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا هذه الرحلة ليست لك",
                ]
            );
        }

        if (Orders::where('flight_id', $flight->id)->where('status', '!=', 0)->count() > 0) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا لا يمكنك التعديل لان الرحلة بها حجوزات",
                ]
            );
        }

        $flight->from_city = $request->from_city;
        $flight->to_city = $request->to_city;
        $flight->date_of_leave = $request->date_of_leave;
        $flight->time_of_leave = $request->time_of_leave;
        $flight->flight_num = $request->flight_num;
        $flight->price_for_shipment = $request->price_for_shipment;
        $flight->max_shipments = $request->max_shipments;
        $flight->save();

        return response()->json(
            [
                'status' => 200,
                'message' => "تم التعديل بنجاح",
            ]
        );
    }

    public function add_car_trip(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'from_city' => 'required',
            'to_city' => 'required',
            'date_of_leave' => 'required',
            'time_of_leave' => 'required',
            'car_model' => 'required',
            'price_for_shipment' => 'required',
            'max_shipments' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $flight = new CarTrips();
        $flight->user_id = $user->id;
        $flight->from_city = $request->from_city;
        $flight->to_city = $request->to_city;
        $flight->date_of_leave = $request->date_of_leave;
        $flight->time_of_leave = $request->time_of_leave;
        $flight->car_model = $request->car_model;
        $flight->price_for_shipment = $request->price_for_shipment;
        $flight->max_shipments = $request->max_shipments;
        $flight->save();

        return response()->json(
            [
                'status' => 200,
                'message' => __('messages.car_trip_added_successfully'),
            ]
        );
    }

    public function edit_car_trip(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'from_city' => 'required',
            'to_city' => 'required',
            'date_of_leave' => 'required',
            'time_of_leave' => 'required',
            'car_model' => 'required',
            'price_for_shipment' => 'required',
            'max_shipments' => 'required',
            'car_trip_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $flight = CarTrips::where('id', $request->car_trip_id)->where('user_id', $user->id)->first();

        if (!$flight) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا هذه الرحلة ليست لك",
                ]
            );
        }

        if (Orders::where('car_trip_id', $flight->id)->where('status', '!=', 0)->count() > 0) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا لا يمكنك التعديل لان الرحلة بها حجوزات",
                ]
            );
        }

        $flight->from_city = $request->from_city;
        $flight->to_city = $request->to_city;
        $flight->date_of_leave = $request->date_of_leave;
        $flight->time_of_leave = $request->time_of_leave;
        $flight->car_model = $request->car_model;
        $flight->price_for_shipment = $request->price_for_shipment;
        $flight->max_shipments = $request->max_shipments;
        $flight->save();

        return response()->json(
            [
                'status' => 200,
                'message' => "تم التعديل بنجاح",
            ]
        );
    }

    public function search_flight(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'from_city' => 'required',
            'to_city' => 'required',
            'days' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $d_o_l = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $request->days . ' day'));

        $flights = Flights::where('from_city', $request->from_city)
            ->where('to_city', $request->to_city)
            ->where('date_of_leave', '<=', $d_o_l)
            ->where('date_of_leave', '>=', date('Y-m-d'))
            ->select('*')
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->with(['fromCity' => function ($query) {
                if (App::isLocale('ar')) {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
            }])
            ->with(['toCity' => function ($query) {
                if (App::isLocale('ar')) {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
            }])
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM user_rating WHERE user_rating.rated_user=flights.user_id ) as user_rate')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.flight_id=flights.id) as shipments_count')
            ->paginate(10);

        return response()->json(
            [
                'status' => 200,
                'data' => $flights,
            ]
        );
    }

    public function my_flights()
    {
        $user = JWTAuth::parseToken()->authenticate();

        //        $d_o_l = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$request->days.' day'));

        $flights = Flights::where('user_id', $user->id)
            ->select('*')
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->with(['fromCity' => function ($query) {
                if (App::isLocale('ar')) {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
            }])
            ->with(['toCity' => function ($query) {
                if (App::isLocale('ar')) {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
            }])
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM user_rating WHERE user_rating.rated_user=flights.user_id ) as user_rate')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.flight_id=flights.id AND status!=0) as shipments_count')
            ->paginate(10);

        foreach ($flights as $flight) {
            $flight->{'is_valid'} = $flight->shipments_count > 0 ? false : true;
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $flights,
            ]
        );
    }

    public function order_details($id = 0)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$order = Orders::find($id)) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "لا يوجد طلب",
                ]
            );
        }

        if ($order->photo) {
            $order->{'photo'} = url('/') . '/uploads/' . $order->photo;
        }

        if (App::isLocale('ar')) {
            $order->load(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
            }]);
            $order->load(['getRepresentative' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
            }]);
        } else {

            $order->load(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
            }]);
            $order->load(['getRepresentative' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
            }]);
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $order,
            ]
        );
    }

    public function my_deliver_running_orders()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (App::isLocale('ar')) {
            $orders = Orders::where('representative_id', $user->id)
                ->whereIn('status', [1, 4])
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['getUser' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('representative_id', $user->id)
                ->whereIn('status', [1, 4])
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name_en as name');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['getUser' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $orders,
            ]
        );
    }

    public function my_deliver_finished_orders()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (App::isLocale('ar')) {
            $orders = Orders::where('representative_id', $user->id)
                ->whereIn('status', [2, 3])
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('representative_id', $user->id)
                ->whereIn('status', [2, 3])
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name_en as name');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $orders,
            ]
        );
    }

    public function search_car_trips(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'from_city' => 'required',
            'to_city' => 'required',
            'days' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $d_o_l = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $request->days . ' day'));

        $flights = CarTrips::where('from_city', $request->from_city)
            ->where('to_city', $request->to_city)
            ->where('date_of_leave', '<=', $d_o_l)
            ->where('date_of_leave', '>=', date('Y-m-d'))
            ->select('*')
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->with(['fromCity' => function ($query) {
                if (App::isLocale('ar')) {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
            }])
            ->with(['toCity' => function ($query) {
                if (App::isLocale('ar')) {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
            }])
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM user_rating WHERE user_rating.rated_user=car_trips.user_id ) as user_rate')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.car_trip_id=car_trips.id) as shipments_count')
            ->paginate(10);

        return response()->json(
            [
                'status' => 200,
                'data' => $flights,
            ]
        );
    }

    public function my_car_trips()
    {
        $user = JWTAuth::parseToken()->authenticate();

        //        $d_o_l = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$request->days.' day'));

        $flights = CarTrips::where('user_id', $user->id)
            ->select('*')
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->with(['fromCity' => function ($query) {
                if (App::isLocale('ar')) {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
            }])
            ->with(['toCity' => function ($query) {
                if (App::isLocale('ar')) {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
            }])
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM user_rating WHERE user_rating.rated_user=car_trips.user_id ) as user_rate')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.car_trip_id=car_trips.id AND status!=0) as shipments_count')
            ->paginate(10);

        foreach ($flights as $flight) {
            $flight->{'is_valid'} = $flight->shipments_count > 0 ? false : true;
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $flights,
            ]
        );
    }

    public function add_post(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $order = new Posts();
        $order->title = $request->title;
        $order->description = $request->description;
        $order->user_id = $user->id;
        $order->save();

        $notification = new Notification();
        $notification->sender_id = $user->id;
        $notification->reciever_id = 1;
        $notification->order_id = $order->id;
        $notification->url = "/admin/orders/" . $order->id;
        $notification->type = 5;
        $notification->save();

        return response()->json(
            [
                'status' => 200,
                'message' => "تم اضافة موضوعك بنجاح انتظر تفعيل الادارة ",
                //                'data'=> $order
            ]
        );
    }

    public function send_message(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'message' => $request->type == 0 ? 'required' : '',
            'image' => $request->type == 1 ? 'required' : '',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $message = new Messages();
        $message->sender_id = $user->id;
        $message->reciever_id = 0;
        $message->message = $request->message ? $request->message : "";
        $message->type = $request->type;
        $message->save();

        $file = $request->file('image');
        if ($request->hasFile('image')) {
            $fileName = 'message-photo-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileName);
            $message->image = $fileName;
        }
        $message->save();

        $message = Messages::where('id', $message->id)->select('id', 'message', 'type', 'created_at')
            ->selectRaw('(CASE WHEN image = "" THEN image ELSE (CONCAT ("' . url('') . '/uploads/", image)) END) AS image')
            ->first();
        $message->{'created_time'} = $message->created_at->diffForHumans();

        return response()->json(
            [
                'status' => 200,
                'message' => trans('messages.your_message_sent_successfully'),
                'data' => $message,
            ]
        );
    }

    public function cancel_order(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $order = Orders::where('id', $request->order_id)->whereIn('status', [0, 1])->where('user_id', $user->id)->first();

        if ($order) {
            if ($order->status == 1) {

                // make fees for cancellation order

                $notification55 = new Notification();
                $notification55->sender_id = $user->id;
                $notification55->reciever_id = $order->representative_id;
                $notification55->order_id = $order->id;
                $notification55->type = 9;
                $notification55->message = "قام " . @$order->getUser->username . " بالغاء الطلب رقم  " . $order->id;
                $notification55->message_en = @$order->getUser->username . " Cancelled order number " . $order->id;
                $notification55->save();

                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);

                if ($order->getUser->lang == "en") {
                    $notification_title = "Canecel order";
                    $notification_message = $notification55->message_en;
                } else {
                    $notification_title = "إلغاء الطلب";
                    $notification_message = $notification55->message;
                }
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
                        'notification_data' => $order,
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

            $order->status = 3;
            $order->cancel_date = date('Y-m-d H:i:s');
            $order->save();
            return response()->json(
                [
                    'message' => trans('messages.your_order_cancelled_successfully'),
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => "this is not your order",
                ],
                400
            );
        }
    }

    public function cancel_order_representative(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $order = Orders::where('id', $request->order_id)->where('status', 1)->where('representative_id', $user->id)->first();

        if ($order) {

            // make fees for cancellation order

            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $order->user_id;
            $notification55->order_id = $order->id;
            $notification55->type = 8;
            $notification55->message = "قام " . @$order->getRepresentative->username . " بالغاء طلبك رقم  " . $order->id;
            $notification55->message_en = @$order->getRepresentative->username . " Cancelled your order number " . $order->id;
            $notification55->save();

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            if ($order->getUser->lang == "en") {
                $notification_title = "Canecel order";
                $notification_message = $notification55->message_en;
            } else {
                $notification_title = "إلغاء الطلب";
                $notification_message = $notification55->message;
            }
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
                    'notification_data' => $order,
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

            $order->status = 3;
            $order->cancel_date = date('Y-m-d H:i:s');
            $order->save();
            return response()->json(
                [
                    'message' => trans('messages.your_order_cancelled_successfully'),
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => "this is not your order",
                ],
                400
            );
        }
    }

    public function delete_offer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'offer_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $offer = OrderOffers::where('id', $request->offer_id)
            ->whereIn('order_id', function ($query) use ($user) {
                $query->select('id')
                    ->from(with(new Orders())->getTable())
                    ->where('user_id', $user->id)
                    ->where('status', 0);
            })
            ->first();

        if ($offer) {
            $offer->delete();
            return response()->json(
                [
                    'message' => trans('messages.offer_deleted_successfully'),
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => "this is not your offer",
                ],
                400
            );
        }
    }

    public function accept_offer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'offer_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $offer = OrderOffers::where('id', $request->offer_id)
            ->whereIn('order_id', function ($query) use ($user) {
                $query->select('id')
                    ->from(with(new Orders())->getTable())
                    ->where('user_id', $user->id)
                    ->where('status', 0);
            })
            ->first();

        if ($offer) {

            $order = Orders::find($offer->order_id);
            $order->representative_id = $offer->user_id;
            $order->status = 1;
            $order->offer_id = $offer->id;
            $order->final_price = $offer->price;
            if ($order->getCobon) {
                $order->price_after_discount = $offer->price - ($offer->price * @$order->getCobon->percent / 100);
            } else {
                $order->price_after_discount = $offer->price;
            }

            $order->save();

            $notification55 = new Notification();
            $notification55->sender_id = $user->id;
            $notification55->reciever_id = $offer->user_id;
            $notification55->order_id = $order->id;
            $notification55->type = 6;
            $notification55->message = "قام " . $user->username . " بقبول عرضك على طلبه بالتوصيل من " . $order->store_name;
            $notification55->message_en = $user->username . " Accepted your offer on his order deliver from " . $order->store_name;
            $notification55->save();

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            $notification_title = \app()->getLocale() == "ar" ? "موافقة على عرض" : "Accept offer";

            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification55->message)
                ->setSound('default');
            $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => (int)$notification55->type,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification55->message,
                    'notification_data' => null,
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

            return response()->json(
                [
                    'message' => trans('messages.offer_accepted_successfully'),
                ]
            );
        } else {
            return response()->json(
                [
                    'message' => "this is not your offer",
                ],
                400
            );
        }
    }

    public function delete_notification(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $order = Notification::where('id', $request->notification_id)->where('reciever_id', $user->id)->first();

        if ($order) {

            $order->delete();
            return response()->json(
                [
                    'status' => 200,
                    'message' => trans('messages.your_notification_deleted_successfully'),
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "this is not your notification",
                ]
            );
        }
    }

    public function update_profile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
            'username' => 'required',
            'photo' => $request->photo ? 'mimes:jpeg,png,jpg,gif' : '',
            //            'state_id' => 'required',
            //            'phone' => 'required|unique:users,phone,' . $user->id . ',id',
            //            'phonecode' => 'required',
            //            'gender' => 'required',
            //            'password' => $request->password?'same:password_confirmation|min:6':'',
            //            'password_confirmation' =>$request->password_confirmation ? 'same:password':'',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $user = User::find($user->id);
        $user->username = $request->input('username');
        $user->email = $request->input('email') ?: '';
        $user->state_id = $request->state_id ?: '';
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads/';
            $upload_success = \Intervention\Image\Facades\Image::make($file->getRealPath())->resize('500', '500', function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . $fileName);
            //            $destinationPath = 'uploads';
            //            $request->file('photo')->move($destinationPath, $fileName);
            $user->photo = $fileName;
        }

        $user->save();

        $user = User::where('id', $user->id)
            ->select('*')
            ->with(['state' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
                //                $query->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.url('/').'/flags/", photo)) END) AS photo');
            }])
            ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/public/') . '/uploads/", photo)) END) AS photo')
            ->first();

        return response()->json(new UsersResource($user), 200);
    }

    public function update_device_token(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if ($request->device_token) {
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
        //        $token = JWTAuth::fromUser($user);
        //        $user->{"token"}=$token;

        return response()->json(
            [
                'data' => $user,
                //                'message' => trans('messages.your_profile_updated_successfully') ,
            ]
        );
    }

    public function bank_transfer_order(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'image' => 'required',
            'money_transfered' => 'required',
            'account_name' => 'required',
            'reference_number' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $transfer = new BankTransfer();
        $transfer->user_id = $user->id;
        $transfer->order_id = $request->order_id;
        $transfer->type = "order";
        $transfer->money_transfered = $request->money_transfered ? $request->money_transfered : "";
        $transfer->account_name = $request->account_name ? $request->account_name : "";
        $transfer->bank_name = $request->bank_name ? $request->bank_name : "";
        $transfer->reference_number = $request->reference_number ? $request->reference_number : "";
        $file = $request->file('image');
        if ($request->hasFile('image')) {
            $fileName = 'bank-transfer-photo-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileName);
            $transfer->image = $fileName;
        }

        $transfer->save();

        return response()->json(
            [
                'status' => 200,
                'message' => trans('messages.your_bank_transfer_sent_successfully'),
                'data' => $transfer,
            ]
        );
    }

    public function bank_transfer_membership(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'package_id' => 'required',
            'image' => 'required',
            'money_transfered' => 'required',
            'account_name' => 'required',
            'reference_number' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $transfer = new BankTransfer();
        $transfer->user_id = $user->id;
        $transfer->package_id = $request->package_id;
        $transfer->type = "membership";
        $transfer->money_transfered = $request->money_transfered ? $request->money_transfered : "";
        $transfer->account_name = $request->account_name ? $request->account_name : "";
        $transfer->reference_number = $request->reference_number ? $request->reference_number : "";
        $transfer->bank_name = $request->bank_name ? $request->bank_name : "";

        $file = $request->file('image');
        if ($request->hasFile('image')) {
            $fileName = 'bank-transfer-photo-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileName);
            $transfer->image = $fileName;
        }

        $transfer->save();

        return response()->json(
            [
                'status' => 200,
                'message' => trans('messages.your_bank_transfer_sent_successfully'),
                'data' => $transfer,
            ]
        );
    }

    public function get_all_messages(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (\app()->getLocale() == "ar") {

            $messages = Messages::select("*")
                ->join(\DB::raw('(SELECT MAX(Id) as id,sender_id+reciever_id as mm FROM messages where sender_id=' . $user->id . ' or reciever_id=' . $user->id . ' GROUP BY hall_id) AS n2'), function ($join) {
                    $join->on('messages.id', '=', 'n2.id');
                })
                ->with(['getSenderUser' => function ($query) {
                    $query->select('id', 'username', 'user_type_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }, 'getRecieverUser' => function ($query) {
                    $query->select('id', 'username', 'user_type_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }, 'hall' => function ($query) {
                    $query->select('id', 'title');
                }])
                ->whereIn('sender_id', function ($query) {
                    $query->select('id')
                        ->from(with(new User())->getTable());
                })
                ->whereIn('reciever_id', function ($query) {
                    $query->select('id')
                        ->from(with(new User())->getTable());
                })
                ->where('sender_id', '=', $user->id)
                ->orWhere('reciever_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        } else {
            $messages = Messages::select("*")
                ->join(\DB::raw('(SELECT MAX(Id) as id,sender_id+reciever_id as mm FROM messages where sender_id=' . $user->id . ' or reciever_id=' . $user->id . ' GROUP BY hall_id) AS n2'), function ($join) {
                    $join->on('messages.id', '=', 'n2.id');
                })
                ->with(['getSenderUser' => function ($query) {
                    $query->select('id', 'username', 'user_type_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }, 'getRecieverUser' => function ($query) {
                    $query->select('id', 'username', 'user_type_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }, 'hall' => function ($query) {
                    $query->select('id', 'title_en as title');
                }])
                ->whereIn('sender_id', function ($query) {
                    $query->select('id')
                        ->from(with(new User())->getTable());
                })
                ->whereIn('reciever_id', function ($query) {
                    $query->select('id')
                        ->from(with(new User())->getTable());
                })
                ->where('sender_id', '=', $user->id)
                ->orWhere('reciever_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        }

        $messages->{'messages'} = MessageResources::collection($messages);

        return response()->json($messages);
    }

    public function messages_before_chat($service_id = 0, $user_type_id = 0, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (\app()->getLocale() == "ar") {
            $messages = MessagesNotifications::select('message')->where('service_id', $service_id)->where('user_type_id', $user_type_id)->get();
        } else {
            $messages = MessagesNotifications::select('message_en as message')->where('service_id', $service_id)->where('user_type_id', $user_type_id)->get();
        }

        return response()->json(
            [
                'status' => 200,
                'data' => $messages,
            ]
        );
    }

    public function notification_count()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notififcation_cunt = Notification::where('reciever_id', $user->id)->where('status', 0)->count();
        return response()->json(
            [
                'notification_count' => $notififcation_cunt,
            ]
        );
    }

    public function get_messages_order($order_id = 0)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$order = Orders::where('id', $order_id)->whereIn('status', [1, 2])->first()) {
            return response()->json(
                [
                    'message' => "You can go chat on this order ",
                ],
                400
            );
        }
        if ($user->id != $order->user_id && $user->id != $order->representative_id) {
            return response()->json(
                [
                    'message' => "this is not your order ",
                ],
                400
            );
        }
        $user = JWTAuth::parseToken()->authenticate();
        if (Messages::where('order_id', $order_id)->count() == 0) {
            $one_message = new Messages();
            $one_message->sender_id = $order->user_id;
            $one_message->reciever_id = $order->representative_id;
            $one_message->order_id = $order->id;
            $one_message->message = $order->description;
            $one_message->is_main = 1;
            $one_message->save();
        }
        $messages = Messages::where('order_id', $order_id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->with(['getSenderUser' => function ($query) {
                $query->select('*')
                    ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                    ->with(['state' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->with(['getRecieverUser' => function ($query) {
                $query->select('*')
                    ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                    ->with(['state' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->with(['getOrder' => function ($query) {
                $query->select('user_id', 'representative_id');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $messages->{'messages'} = MessageResources::collection($messages);

        foreach ($messages as $messageasd) {
            $notifications = Messages::where('id', $messageasd->id)->where('reciever_id', $user->id)->where('status', 0)->first();
            if ($notifications) {
                $notifications->status = 1;
                $notifications->save();
            }
        }
        if (App::isLocale('ar')) {
            $order = Orders::where('id', $order->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getOffer' => function ($query) {
                    $query->select('id', 'price', 'order_id');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])->first();
        } else {
            $order = Orders::where('id', $order->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getOffer' => function ($query) {
                    $query->select('id', 'price', 'order_id');
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])->first();
        }

        return response()->json(
            [
                'data' => $messages,
                'user_id' => @Orders::find($order_id)->user_id,
                'representative_id' => @Orders::find($order_id)->representative_id,
                'order' => new OrderDetailsResources($order),
            ]
        );
    }

    public function get_messages_user($hall_id = 0, $other_user = 0)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $messages = Messages::where(function ($query1) use ($other_user, $user) {
            $query1->where(function ($query) use ($other_user, $user) {
                $query->where('sender_id', $other_user)
                    ->where('reciever_id', $user->id);
            })->orWhere(function ($query) use ($other_user, $user) {
                $query->where('sender_id', $user->id)
                    ->where('reciever_id', $other_user);
            });
        })
            ->where('hall_id', $hall_id)
            ->with(['getSenderUser' => function ($query) {
                $query->select('id', 'username', 'user_type_id');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }, 'getRecieverUser' => function ($query) {
                $query->select('id', 'username', 'user_type_id');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }, 'hall' => function ($query) {
                $query->select('id', 'title_en as title');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $messages->{'messages'} = MessageResources::collection($messages);

        foreach ($messages as $messageasd) {
            $notifications = Messages::where('id', $messageasd->id)->where('reciever_id', $user->id)->where('status', 0)->first();
            if ($notifications) {
                $notifications->status = 1;
                $notifications->save();
            }
        }

        return response()->json($messages);
    }

    public function messages_count()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notififcation_cunt = Messages::where('reciever_id', $user->id)->where('status', 0)->count();
        return response()->json(
            [
                'status' => 200,
                'notification_count' => $notififcation_cunt,
            ]
        );
    }

    public function add_message(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'to' => 'required',
            'hall_id' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $comment = new Messages;
        $comment->sender_id = $user->id;
        $comment->reciever_id = $request->to;
        $comment->hall_id = $request->hall_id;
        $comment->message = $request->input('message') ? $request->input('message') : '';
        $comment->save();
        $comment = Messages::find($comment->id);
        $comment->load(['getSenderUser' => function ($query) {
            $query->select('id', 'username', 'user_type_id');
            $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
        }]);
        $comment->load(['getRecieverUser' => function ($query) {
            $query->select('id', 'username', 'user_type_id');
            $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
        }]);
        if (\app()->getLocale() == "ar") {
            $comment->load(['hall' => function ($query) {
                $query->select('id', 'title');
            }]);
        } else {
            $comment->load(['hall' => function ($query) {
                $query->select('id', 'title_en as title');
            }]);
        }

        $comment_a = new MessageResources($comment);

        $notification55 = new Notification();
        $notification55->sender_id = $comment->sender_id;
        $notification55->reciever_id = $comment->reciever_id;
        //        $notification55->order_id = $comment->order_id;
        $notification55->type = 2;
        $notification55->message = "قام " . $comment->getSenderUser->username . " بارسال رسالة لك";
        //        $notification55->message_en =  $comment->getSenderUser->username . " send you message";
        $notification55->save();

        $notification_title = "رسالة جديدة";
        $notification_message = $notification55->message;

        if (@$notification55->getReciever->notification == 1) {
            $this->send_fcm_notification($notification_title, $notification_message, $notification55, $comment_a, 'default');
        }

        return response()->json(
            [
                'message' => 'تم ارسال الرسالة بنجاح',
                'data' => $comment_a,
            ],
            200
        );
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

    //    public function request_money(Request $request)
    //    {
    //        $user = JWTAuth::parseToken()->authenticate();
    //
    //        $validator = Validator::make($request->all(), [
    //            'price' => 'required',
    //        ]);
    //
    //
    //        if ($validator->fails()) {
    //            return response()->json(
    //                [
    //                    'message' => $validator->errors()->first(),
    //                ],400
    //            );
    //        }
    //
    //
    //        $user_balance = Balance::where('user_id',$user->id)->sum('price');
    //        if($request->price>$user_balance){
    //            return response()->json(
    //                [
    //                    'message' => "Sorry you don't have that amount of money" ,
    //                ],400);
    //        }
    //
    //
    //        $comment = new RequestMoney();
    //        $comment->user_id = $user->id;
    //        $comment->price = $request->price;
    //        $comment->save();
    //
    //
    //
    //        return response()->json(
    //            [
    //                'message' => 'تم ارسال الطلب بنجاح',
    //            ],200);
    //
    //
    //    }
    //
    //
    //    public function add_project(Request $request)
    //    {
    //        $user =   JWTAuth::parseToken()->authenticate();
    //        $validator = Validator::make($request->all(), [
    //            'title' => 'required',
    //            'description' => 'required',
    //            'country_id' => 'required',
    //            'state_id' => 'required',
    //            'category_id' => 'required',
    //            'sub_category_id' => 'required',
    //        ]);
    //        $this_user=User::find($user->id);
    //        if($this_user->project_activate == 0){
    //            return response()->json(
    //                [
    //                    'status' => 400 ,
    //                    'message' => trans('messages.you_have_to_join_project_owner_package') ,
    //                ]
    //            );
    //        }
    //        if ($validator->fails()) {
    //            return response()->json(
    //                [
    //                    'status' => 400 ,
    //                    'errors' => $validator->errors()->all(),
    //                    'message' => trans('messages.some_error_happened') ,
    //                ]
    //            );
    //        }
    //        $order = new Projects();
    //        $order->title= $request->title;
    //        $order->description= $request->description;
    //        $order->category_id = $request->category_id;
    //        $order->sub_category_id = $request->sub_category_id;
    //        $order->user_id = $user->id;
    //        $order->country_id = $request->country_id;
    //        $order->state_id = $request->state_id;
    //        $order -> save();
    //
    //        return response()->json(
    //            [
    //                'status' => 200 ,
    //                'message' => trans('messages.project_added_successfully') ,
    //                'data'=> $order
    //            ]);
    //    }

    public function delete_project_photo(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'photo_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => 'حدثت بعض الاخطاء التالية',
                ]
            );
        }
        $id = $request->photo_id;
        $photo = ProjectPhotos::where('id', $id)->whereIn('project_id', function ($query) use ($user) {
            $query->select('id')
                ->from(with(new Projects())->getTable())
                ->where('user_id', $user->id);
        })->first();

        if (!$photo) {
            return response()->json([
                'status' => 400,
                'message' => "لا يوجد صورة لمنتجك بهذا العنوان",
            ]);
        } else {
            if ($photo != false) {
                $photo = ProjectPhotos::find($photo->id);
                unlink("uploads/" . $photo->photo);
                $photo->delete();
            }
            return response()->json([
                'status' => 200,
                'message' => "تم حذف صورة مشروعك بنجاح",
            ]);
        }
    }

    public function edit_project(Request $request, $id = 0)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = Projects::where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "Sorry this is not your project .",
                ]
            );
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }
        $order->title = $request->title;
        $order->description = $request->description;
        $order->category_id = $request->category_id;
        $order->sub_category_id = $request->sub_category_id;
        $order->user_id = $user->id;
        $order->country_id = $request->country_id;
        $order->state_id = $request->state_id;
        $order->project_status = $request->project_status ? $request->project_status : 0;
        $order->save();

        return response()->json(
            [
                'status' => 200,
                'message' => trans('messages.project_edited_successfully'),
                'data' => $order,
            ]
        );
    }

    public function product($id = 0)
    {

        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $product = Products::where('id', $id)->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['getComments' => function ($query) {
                $query->select('*');
            }])
            ->with(['getPhotos' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->first();
        if (!$product) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'لا يوجد منتج بهذا العنوان',
                ]
            );
        }
        ++$product->views;
        $product->save();

        return response()->json(
            [
                'status' => 200,
                'postData' => $product,
            ]
        );
    }

    public function delete_product_from_cart(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        if ($prod = InvoiceDetails::where('invoice_id', 0)->where('product_id', $request->product_id)->where('user_id', $user->id)->first()) {
            $prod->delete();
        }
        return response()->json(
            [
                'status' => 200,
                'message' => 'تم حذف المنتج بنجاح',
            ]
        );
    }

    public function report_ads(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'ads_id' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ]
            );
        }

        if (!Ads::find($request->ads_id)) {
            return response()->json(
                [
                    'message' => "عفوا لا يوجد اعلان بهذا العنوان",
                ],
                400
            );
        }

        $report = new Reports();
        $report->user_id = $user->id;
        $report->ads_id = $request->input('ads_id');
        $report->message = $request->input('message');
        $report->save();

        return response()->json(
            [
                'message' => 'تم  التبليغ عن الاعلان بنجاح',
            ]
        );
    }

    public function user_rate($user_id = 0)
    {

        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'message' => 'Sorry wrong user',
            ], 400);
        }

        $user = User::where('id', $user->id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->with(['state' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
                //                $query->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.url('/').'/flags/", photo)) END) AS photo');
            }])
            ->first();

        $user['user_ratings'] = UserRating::where('rated_user', $user->id)
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->paginate(10);
        $user = new UsersResource($user);

        return response()->json($user);
    }

    public function getBalance()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $sumBalane = Balance::where('user_id', $user->id)->sum('price');
        $user_transactions = Balance::select('id', 'order_id', 'price', 'notes', 'created_at')
            ->orderBy('created_at', 'desc')->where('user_id', $user->id)->paginate(20);
        return response()->json(['balance' => $sumBalane, 'data' => $user_transactions,]);
    }

    public function balance()
    {

        $user = JWTAuth::parseToken()->authenticate();
        $balane = Balance::where('user_id', $user->id)->sum('price');
        $user_transactions = Orders::where('representative_id', $user->id)
            ->where('status', 2)
            ->with(['getUser' => function ($query) {
                $query->select('*')
                    ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->with('getOffer')
            ->paginate(20);
        $user_transactions->{'user_transactions'} = TransactionsResources::collection($user_transactions);
        return response()->json(
            [
                'balance' => $balane,
                'data' => $user_transactions,
            ]
        );
    }

    public function change_password(Request $request)
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
        } else {
            return response()->json(
                [
                    'message' => trans('messages.old_password_innot_correct'),
                ],
                400
            );
        }

        return response()->json(['message' => __('messages.password_was_edited_successfully')], 200);
    }

    public function change_count_cart(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $amount = 0;
        $id = $request->product_id;
        $num = $request->num;
        $ads = Products::find($id);
        if ($ads) {
            $amount = $ads->quantity;
        }

        if ($ads->quantity < $num) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'لا يوجد عدد كافي من المنتجات في المخزن',
                ]
            );
        } else {

            if ($product = InvoiceDetails::where('invoice_id', 0)->where('product_id', $id)->where('user_id', $user->id)->first()) {
                $product->quantity = $num;
                $product->save();
            }
        }
        return response()->json(
            [
                'status' => 200,
                'message' => 'تم  تغيير الكمية بنجاح',
            ]
        );
    }

    public function send_invoice()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $unique_id = uniqid();
        if (InvoiceDetails::where('user_id', $user->id)->where('invoice_id', 0)->count() > 0) {
            foreach (InvoiceDetails::where('user_id', $user->id)->where('invoice_id', 0)->get() as $invoice_details) {
                if ($invoice = Invoices::where('status', 0)->where('user_id', $user->id)->where('seller_id', @$invoice_details->getProduct->user_id)->first()) {
                    $invoice_details = InvoiceDetails::find($invoice_details->id);
                    $invoice_details->invoice_id = $invoice->id;
                    $invoice_details->save();
                } else {
                    $invoice = new Invoices();
                    $invoice->seller_id = @$invoice_details->getProduct->user_id;
                    $invoice->user_id = $user->id;
                    $invoice->collection = $unique_id;
                    $invoice->save();

                    $invoice_details = InvoiceDetails::find($invoice_details->id);
                    $invoice_details->invoice_id = $invoice->id;
                    $invoice_details->save();
                }
            }
            foreach (Invoices::where('status', 0)->where('user_id', $user->id)->get() as $inv) {
                $inv->status = 1;
                $inv->save();
            }
        }

        return response()->json(
            [
                'status' => 200,
                'message' => 'تم ارسال الفاتورة بنجاح',
            ]
        );
    }

    public function incomming_invoices()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $invoices = Invoices::whereIn('status', [1, 2])->where('seller_id', $user->id)->orderBy('id', 'DESC')
            ->with('getDetails.getProduct.photoImage')
            ->paginate(10);

        foreach ($invoices as $in) {
            $sum_before = 0;
            $sum = 0;
            foreach ($in->getDetails as $invoice) {
                if ($invoice->getProduct->discount) {
                    $discount_value = $invoice->getProduct->price - (($invoice->getProduct->price * $invoice->getProduct->discount) / 100);
                } else {
                    $discount_value = $invoice->getProduct->price;
                }
                $sum_before = $sum_before + (@$invoice->getProduct->price * $invoice->quantity);
                $discount_value = $discount_value * $invoice->quantity;
                $sum += $discount_value;
            }
            $in->{"sum_before_dicount"} = $sum_before;
            $in->{"sum_after_dicount"} = $sum;
            $in->{"created_time"} = Carbon::parse($in->created_at)->format('Y-m-d');
            $in->{"buyer"} = @$in->getUser->username;
        }

        return response()->json(
            [
                'status' => 200,
                'postData' => $invoices,
            ]
        );
    }

    public function my_projects()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (App::isLocale('ar')) {
            $projects = Projects::select('*')
                ->where('user_id', $user->id)
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getSubcategory' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getPhotos' => function ($query) {
                    $query->select('id', 'project_id');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['user' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with('state')
                ->with('country')
                ->orderBy('id', 'DESC')->paginate(10);
            $res = [];
            foreach ($projects as $project) {
                $project->{"created_time"} = Carbon::parse($project->created_at)->diffForHumans();
                $res[] = $project;
            }
        } else {
            $projects = Projects::select('*')
                ->where('user_id', $user->id)
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getSubcategory' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getPhotos' => function ($query) {
                    $query->select('id', 'project_id');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['user' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with('state')
                ->with('country')
                ->orderBy('id', 'DESC')->paginate(10);
            $res = [];
            foreach ($projects as $project) {
                $project->{"created_time"} = Carbon::parse($project->created_at)->diffForHumans();
                $res[] = $project;
            }
        }

        return response()->json([
            'status' => 200,
            'data' => $projects,
        ]);
    }

    public function my_city_projects()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (App::isLocale('ar')) {
            $projects = Projects::select('*')
                ->where('state_id', $user->state_id)
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getSubcategory' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getPhotos' => function ($query) {
                    $query->select('id', 'project_id');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['user' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with('state')
                ->with('country')
                ->orderBy('id', 'DESC')->paginate(10);
            $res = [];
            foreach ($projects as $project) {
                $project->{"created_time"} = Carbon::parse($project->created_at)->diffForHumans();
                $res[] = $project;
            }
        } else {
            $projects = Projects::select('*')
                ->where('user_id', $user->id)
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getSubcategory' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getPhotos' => function ($query) {
                    $query->select('id', 'project_id');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['user' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with('state')
                ->with('country')
                ->orderBy('id', 'DESC')->paginate(10);
            $res = [];
            foreach ($projects as $project) {
                $project->{"created_time"} = Carbon::parse($project->created_at)->diffForHumans();
                $res[] = $project;
            }
        }

        return response()->json([
            'status' => 200,
            'data' => $projects,
        ]);
    }

    public function upload_photos(Request $request)
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

    public function upload_photo_project(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object = new ProjectPhotos();
            $object->project_id = $request->project_id;
            $object->photo = $fileName;
            $object->save();
        }

        return response()->json(
            [
                'status' => 200,
                'message' => trans('messages.project_photo_was_uploaded_successfully'),
            ]
        );
    }

    public function outcomming_invoices()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $invoices = Invoices::whereIn('status', [1, 2])->where('user_id', $user->id)
            ->with('getDetails.getProduct.photoImage')
            ->orderBy('id', 'DESC')->paginate(10);

        foreach ($invoices as $in) {
            $sum_before = 0;
            $sum = 0;
            foreach ($in->getDetails as $invoice) {
                if ($invoice->getProduct->discount) {
                    $discount_value = $invoice->getProduct->price - (($invoice->getProduct->price * $invoice->getProduct->discount) / 100);
                } else {
                    $discount_value = $invoice->getProduct->price;
                }
                $sum_before = $sum_before + (@$invoice->getProduct->price * $invoice->quantity);
                $discount_value = $discount_value * $invoice->quantity;
                $sum += $discount_value;
            }
            $in->{"sum_before_dicount"} = $sum_before;
            $in->{"sum_after_dicount"} = $sum;
            $in->{"created_time"} = Carbon::parse($in->created_at)->format('Y-m-d');
            $in->{"seller"} = $in->getSeller->username;
        }
        return response()->json(
            [
                'status' => 200,
                'postData' => $invoices,
            ]
        );
    }

    public function product_delivered(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => "حدثت بعض الاخطاء التالية",
                ]
            );
        }

        $id = $request->invoice_id;
        $invoice = Invoices::where('seller_id', $user->id)->where('id', $id)->first();

        if (!$invoice) {
            return response()->json([
                'status' => 400,
                'message' => "لا يوجد فاتورة بهذا العنوان",
            ]);
        } else {
            $invoice = Invoices::find($id);
            $invoice->status = 2;
            $invoice->save();

            foreach ($invoice->getDetails as $invoice_d) {
                $product = Products::find($invoice_d->product_id);
                if ($product) {
                    $product->quantity = $product->quantity - $invoice_d->quantity;
                    $product->save();
                }
            }
            return response()->json([
                'status' => 200,
                'message' => "تم اعتماد تسليم منتجات الفاتورة بنجاح",
            ]);
        }
    }

    public function seller($id = 0)
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $data = [];
        $seller = User::where('id', $id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo')->first();
        if (!$seller) {
            return response()->json([
                'status' => 400,
                'message' => "لا يوجد بائع بهذا العنوان",
            ]);
        }

        $data['seller'] = $seller;

        $data["products"] = Products::where('user_id', $seller->id)->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->limit(10)
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(
            [
                'status' => 200,
                'postData' => $data,
            ]
        );
    }

    public function seller_products($id = 0)
    {

        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $seller = User::find($id);
        if (!$seller) {
            return response()->json([
                'status' => 400,
                'message' => "لا يوجد بائع بهذا العنوان",
            ]);
        }

        $products = Products::where('user_id', $seller->id)->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json(
            [
                'status' => 200,
                'message' => $products,
            ]
        );
    }

    public function all_products()
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $products = Products::select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json(
            [
                'status' => 200,
                'message' => $products,
            ]
        );
    }

    public function products_sub_category($id = 0)
    {

        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $products = Products::where('sub_category_id', $id)->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json(
            [
                'status' => 200,
                'message' => $products,
            ]
        );
    }

    public function my_products()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $products = Products::where('user_id', $user->id)->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user->id . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json(
            [
                'status' => 200,
                'message' => $products,
            ]
        );
    }

    public function my_favourite_halls()
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        if (App::isLocale('ar')) {
            $halls = Hall::whereIn('id', function ($query) use ($user_like) {
                $query->select('hall_id')
                    ->from(with(new Likes())->getTable())
                    ->where('user_id', $user_like);
            })
                ->select("id", "title", 'address', 'longitude', 'latitude', 'chairs', 'currency', 'price_per_hour')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name', 'code');
                }])
                ->paginate(10);
            $halls->{'halls'} = HallsResource::collection($halls);
            return response()->json($halls);
        } else {
            $halls = Hall::whereIn('id', function ($query) use ($user_like) {
                $query->select('hall_id')
                    ->from(with(new Likes())->getTable())
                    ->where('user_id', $user_like);
            })
                ->select("id", "title_en as title", 'address_en as address', 'longitude', 'latitude', 'currency', 'chairs', 'price_per_hour')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->selectRaw('(SELECT count(*) FROM ratings WHERE ratings.user_id =' . $user_like . ' AND ratings.hall_id=halls.id) as is_rated')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name', 'code');
                }])
                ->paginate(10);
            $halls->{'halls'} = HallsResource::collection($halls);
            return response()->json($halls);
        }
    }

    public function my_finished_halls()
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        if (App::isLocale('ar')) {
            $halls = Hall::whereIn('id', function ($query) use ($user_like) {
                $query->select('hall_id')
                    ->from(with(new Reservations())->getTable())
                    ->where('user_id', $user_like)
                    ->where('status', 1)
                    ->whereDate('date', '<', date('Y-m-d'));
            })
                ->select("id", "title", 'address', 'longitude', 'latitude', 'chairs', 'currency', 'price_per_hour')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT count(*) FROM ratings WHERE ratings.user_id =' . $user_like . ' AND ratings.hall_id=halls.id) as is_rated')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name', 'code');
                }])
                ->paginate(10);
            $halls->{'halls'} = HallsFinishedResource::collection($halls);
            foreach ($halls as $hall) {
                $reservation = Reservations::where('hall_id', $hall->id)
                    ->where('user_id', $user_like)
                    ->where('status', 1)
                    ->whereDate('date', '<', date('Y-m-d'))->orderBy('id', 'desc')->get();
                $hall->{'reservations'} = $reservation;
            }
            return response()->json($halls);
        } else {
            $halls = Hall::whereIn('id', function ($query) use ($user_like) {
                $query->select('hall_id')
                    ->from(with(new Reservations())->getTable())
                    ->where('user_id', $user_like)
                    ->where('status', 1)
                    ->whereDate('date', '<', date('Y-m-d'));
            })
                ->select("id", "title_en as title", 'address_en as address', 'longitude', 'latitude', 'currency', 'chairs', 'price_per_hour')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT count(*) FROM ratings WHERE ratings.user_id =' . $user_like . ' AND ratings.hall_id=halls.id) as is_rated')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name', 'code');
                }])
                ->paginate(10);
            foreach ($halls as $hall) {
                $reservation = Reservations::where('hall_id', $hall->id)
                    ->where('user_id', $user_like)
                    ->where('status', 1)
                    ->whereDate('date', '<', date('Y-m-d'))->orderBy('id', 'desc')->get();
                $hall->{'reservations'} = $reservation;
            }
            $halls->{'halls'} = HallsFinishedResource::collection($halls);

            return response()->json($halls);
        }
    }

    public function my_reserved_halls()
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        if (App::isLocale('ar')) {
            $halls = Hall::whereIn('id', function ($query) use ($user_like) {
                $query->select('hall_id')
                    ->from(with(new Reservations())->getTable())
                    ->where('user_id', $user_like)
                    ->where('status', 1)
                    ->where('date', '>=', date('Y-m-d'));
            })
                ->select("id", "title", 'address', 'longitude', 'latitude', 'chairs', 'currency', 'price_per_hour')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name', 'code');
                }])
                ->paginate(10);
            $halls->{'halls'} = HallsReservedResource::collection($halls);
            foreach ($halls as $hall) {
                $reservation = Reservations::where('hall_id', $hall->id)->where('user_id', $user_like)->whereIn('status', [0, 1])
                    ->where('date', '>=', date('Y-m-d'))->orderBy('id', 'desc')->get();
                $hall->{'reservations'} = $reservation;
            }
            return response()->json($halls);
        } else {
            $halls = Hall::whereIn('id', function ($query) use ($user_like) {
                $query->select('hall_id')
                    ->from(with(new Reservations())->getTable())
                    ->where('user_id', $user_like)
                    ->where('status', 1)
                    ->where('date', '>=', date('Y-m-d'));
            })
                ->select("id", "title_en as title", 'address_en as address', 'longitude', 'latitude', 'currency', 'chairs', 'price_per_hour')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name', 'code');
                }])
                ->paginate(10);
            $halls->{'halls'} = HallsReservedResource::collection($halls);
            foreach ($halls as $hall) {
                $reservation = Reservations::where('hall_id', $hall->id)->where('user_id', $user_like)->whereIn('status', [0, 1])
                    ->where('date', '>=', date('Y-m-d'))->orderBy('id', 'desc')->get();
                $hall->{'reservations'} = $reservation;
            }
            return response()->json($halls);
        }
    }

    public function my_wating_orders()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $orders = Orders::where('status', 0)
                ->whereIn('id', function ($query) use ($user) {
                    $query->select('order_id')
                        ->from(with(new Notification())->getTable())
                        ->where('reciever_id', $user->id);
                })
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('status', 0)
                ->whereIn('id', function ($query) use ($user) {
                    $query->select('order_id')
                        ->from(with(new Notification())->getTable())
                        ->where('reciever_id', $user->id);
                })
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }
        $orders->{'orders'} = MyWaitingOrdersResources::collection($orders);
        return response()->json($orders);
    }

    public function my_new_orders()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $orders = Orders::where('status', 0)
                ->where('user_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('status', 0)
                ->where('user_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }
        $orders->{'orders'} = MyOrdersResources::collection($orders);
        return response()->json($orders);
    }

    public function my_ads()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $ads = Ads::select('*')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.ads_id=ads.id	) as likes_count')
                ->where('user_id', $user->id)
                ->with(['user' => function ($query) {
                    $query->select('id', 'username', 'user_type_id')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['mainPhoto' => function ($query) {
                    $query->select('id', 'ads_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
        }
        $ads->{'ads'} = MyAdsResources::collection($ads);
        return response()->json($ads);
    }

    public function my_active_orders()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $orders = Orders::where('status', 1)
                ->where('user_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('status', 1)
                ->where('user_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }
        $orders->{'orders'} = MyOrdersResources::collection($orders);
        return response()->json($orders);
    }

    public function my_running_orders()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $orders = Orders::where('status', 1)
                ->where('representative_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('status', 1)
                ->where('representative_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }
        $orders->{'orders'} = MyOrdersResources::collection($orders);
        return response()->json($orders);
    }

    public function my_completed_orders()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $orders = Orders::where('status', 2)
                ->where('representative_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('status', 2)
                ->where('representative_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }
        $orders->{'orders'} = MyOrdersResources::collection($orders);
        return response()->json($orders);
    }

    public function my_removed_orders()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $orders = Orders::where('status', 3)
                ->where('representative_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('status', 3)
                ->where('representative_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }
        $orders->{'orders'} = MyOrdersResources::collection($orders);
        return response()->json($orders);
    }

    public function my_finished_orders()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $orders = Orders::where('status', 2)
                ->where('user_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('status', 2)
                ->where('user_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }
        $orders->{'orders'} = MyOrdersResources::collection($orders);
        return response()->json($orders);
    }

    public function order($id = 0)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!Orders::find($id)) {
            return response()->json(
                ['message' => 'no order'],
                400
            );
        }

        if (App::getLocale() == "ar") {
            $orders = Orders::where('id', $id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->first();
        } else {
            $orders = Orders::where('id', $id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getRepresentative' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->first();
        };
        return response()->json(new OrderDetailsResources($orders));
    }

    public function my_cancelled_orders()
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (App::getLocale() == "ar") {
            $orders = Orders::where('status', 3)
                ->where('user_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            $orders = Orders::where('status', 3)
                ->where('user_id', $user->id)
                ->with(['getUser' => function ($query) {
                    $query->select('*')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                        ->with(['state' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }]);
                }])
                ->with(['getDeliveryTime' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['photos' => function ($query) {
                    $query->select('id', 'order_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }
        $orders->{'orders'} = MyOrdersResources::collection($orders);
        return response()->json($orders);
    }

    public function my_stores()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $likes = Stores::where('user_id', $user->id)
            ->select('*')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.place_id=stores.place_id AND id!=0) as orders_count')
            ->orderBy('id', 'DESC')
            ->paginate(10);
        return response()->json($likes);
    }

    public function evaluated_products()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $products = Products::whereIn('id', function ($query) use ($user) {
            $query->select('product_id')
                ->from(with(new Comments())->getTable())
                ->where('user_id', $user->id);
        })->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user->id . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json(
            [
                'status' => 200,
                'message' => $products,
            ]
        );
    }

    public function buyed_products()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $products = Products::whereIn('id', function ($query) use ($user) {
            $query->select('product_id')
                ->from(with(new InvoiceDetails())->getTable())
                ->whereIn('invoice_id', function ($query1) {
                    $query1->select('id')
                        ->from(with(new Invoices())->getTable())
                        ->where('status', 2);
                });
            $query->where('user_id', $user->id);
        })->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user->id . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json(
            [
                'status' => 200,
                'message' => $products,
            ]
        );
    }

    public function add_product(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'project_id' => 'required',
            'price' => 'required',
            'photo' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'data' => $validator->errors(),
                    'message' => "حدثت بعض الأخطاء التالية.",
                ]
            );
        }

        $project = Projects::where('id', $request->input('project_id'))->where('user_id', $user->id)->first();
        if (!$project) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "هذا المشروع ليس مشروعك ولا يمكنك اضافة منتجات فيه.",
                ]
            );
        }

        $ads = new Products();
        $ads->project_id = $request->input('project_id');
        $ads->price = $request->input('price');
        $ads->title = str_replace("/", '-', $request->input('title'));
        $ads->description = $request->input('description');

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'product-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $ads->photo = $fileName;
        }

        $ads->save();

        return response()->json(
            [
                'status' => 200,
                'data' => $ads,
                'message' => "تم اضافة منتجك بنجاح ",
                //                'device' => $device
            ]
        );
    }

    public function edit_product(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'project_id' => 'required',
            'product_id' => 'required',
            'price' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'data' => $validator->errors(),
                    'message' => "حدثت بعض الأخطاء التالية.",
                ]
            );
        }

        $product = Products::where('id', $request->product_id)->whereIn('project_id', function ($query) use ($user) {
            $query->select('id')
                ->from(with(new Projects())->getTable())
                ->where('user_id', $user->id);
        })->first();

        if ($product == false) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا لا يمكنك تعديل منتج ليس لك",
                ]
            );
        }

        $object = Products::find($request->product_id);
        $object->title = $request->title;
        $object->project_id = $request->project_id;
        $object->price = $request->price;
        $object->description = $request->description;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/' . $object->photo;
            if (is_file($old_file)) {
                unlink($old_file);
            }

            $fileName = 'product-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo = $fileName;
        }
        $object->save();

        return response()->json(
            [
                'status' => 200,
                'data' => $object,
                'message' => "تم تعديل منتجك بنجاح ",
                //                'device' => $device
            ]
        );
    }

    public function add_photo_image(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => "حدثت بعض الاخطاء التالية.",
                ]
            );
        }

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object = new ProductPhotos();
            $object->product_id = $request->product_id;
            $object->photo = $fileName;
            $object->save();
        }

        return response()->json(
            [
                'status' => 200,
                'message' => "تم رفع الصورة بنجاح",
            ]
        );
    }

    public function delete_order_photo(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'photo_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $id = $request->photo_id;
        $photo = OrderPhotos::where('photo', $id)->whereIn('order_id', function ($query) use ($user) {
            $query->select('id')
                ->from(with(new Orders())->getTable())
                ->where('user_id', $user->id);
        })->first();

        if (!$photo) {
            return response()->json([
                'message' => "لا يوجد صورة لطلبك بهذا العنوان",
            ], 400);
        } else {
            if ($photo != false) {
                $photo = OrderPhotos::find($photo->id);
                unlink("uploads/" . $photo->photo);
                $photo->delete();
            }
            return response()->json([
                'message' => "تم حذف صورة طلبك بنجاح",
            ]);
        }
    }

    public function cancel_request(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'reason_of_cancel' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }
        $id = $request->order_id;
        $order = Orders::where('id', $id)
            ->whereIn('status', [0, 1])
            ->where('user_id', $user->id)
            ->first();
        if (!$order) {
            return response()->json([
                'status' => 400,
                'message' => __('messages.this_not_your_order'),
            ]);
        } else {
            if ($order != false) {
                $order = Orders::find($order->id);
                if ($order->status == 1) {
                    $notification55 = new Notification();
                    $notification55->sender_id = $user->id;
                    $notification55->reciever_id = $order->representative_id;
                    $notification55->order_id = $order->id;
                    $notification55->type = 12;
                    $notification55->message = "قام " . @$order->getUser->username . " بالغاء الطلب رقم  " . $order->id;
                    $notification55->message_en = @$order->getUser->username . " Cancelled order number " . $order->id;
                    $notification55->save();

                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 20);

                    if ($order->getUser->lang == "en") {
                        $notification_title = "Canecel order";
                        $notification_message = $notification55->message_en;
                    } else {
                        $notification_title = "إلغاء الطلب";
                        $notification_message = $notification55->message;
                    }
                    $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                    $notificationBuilder->setBody($notification_message)
                        ->setSound('default');

                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData([
                        'data' => [
                            'notification_type' => (int)$notification55->type,
                            'notification_title' => $notification_title,
                            'notification_message' => $notification_message,
                            'notification_data' => $order,
                        ],
                    ]);

                    $option = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data = $dataBuilder->build();

                    $token = @$notification55->getReciever->device_token;

                    if ($token) {
                        $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                        $downstreamResponse->numberSuccess();
                        $downstreamResponse->numberFailure();
                        $downstreamResponse->numberModification();
                    }
                }

                $order->status = 3;
                //                $order->representative_id=0;
                $order->reason_of_cancel = $request->reason_of_cancel;
                $order->cancel_date = date('Y-m-d H:i:s');
                $order->save();
            }
            return response()->json([
                'status' => 200,
                'message' => __('messages.your_order_was_cancelled_successfully'),
            ]);
        }
    }

    public function assign_order(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'representative_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }
        $id = $request->order_id;
        $product = Orders::where('id', $id)
            //            ->whereIn('project_id', function ($query) use($user){
            //            $query->select('id')
            //                ->from(with(new Projects())->getTable())
            //                ->where('user_id', $user->id);
            //        })
            ->where('user_id', $user->id)
            ->first();
        if (!$product) {
            return response()->json([
                'status' => 400,
                'message' => __('messages.this_not_your_order'),
            ]);
        } else {
            if ($product != false) {
                $product = Orders::find($product->id);
                $product->status = 1;
                $product->representative_id = $request->representative_id;
                $product->save();
            }
            return response()->json([
                'status' => 200,
                'message' => __('messages.your_order_in_progress'),
            ]);
        }
    }

    public function delete_flight(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'flight_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }
        $id = $request->flight_id;
        $flight = Flights::where('id', $id)->where('user_id', $user->id)->first();

        if (!$flight) {
            return response()->json([
                'status' => 400,
                'message' => "هذه الرحلة ليس لك",
            ]);
        } else {

            if (Orders::where('flight_id', $flight->id)->where('status', '!=', 0)->count() == 0) {
                $flight = Flights::find($flight->id);
                Orders::where('flight_id', $flight->id)->where('status', '!=', 0)->delete();
                $flight->delete();
                return response()->json([
                    'status' => 200,
                    'message' => "تم حذف الرحلة ",
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => "عفوا لا يمكن حذف هذه الرحلة",
                ]);
            }
        }
    }

    public function delete_car_trip(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'car_trip_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }
        $id = $request->car_trip_id;
        $flight = CarTrips::where('id', $id)->where('user_id', $user->id)->first();

        if (!$flight) {
            return response()->json([
                'status' => 400,
                'message' => "هذه الرحلة ليس لك",
            ]);
        } else {

            if (Orders::where('car_trip_id', $flight->id)->where('status', '!=', 0)->count() == 0) {
                $flight = CarTrips::find($flight->id);
                Orders::where('flight_id', $flight->id)->where('status', '!=', 0)->delete();
                $flight->delete();
                return response()->json([
                    'status' => 200,
                    'message' => "تم حذف الرحلة ",
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => "عفوا لا يمكن حذف هذه الرحلة",
                ]);
            }
        }
    }

    public function delete_project(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }
        $id = $request->project_id;
        $product = Projects::where('id', $id)->where('user_id', $user->id)->first();
        if (!$product) {
            return response()->json([
                'status' => 400,
                'message' => trans('messages.no_project_found'),
            ]);
        } else {
            if ($product != false) {
                $product = Projects::find($product->id);
                foreach (ProjectPhotos::where('project_id', $id)->get() as $photo) {
                    $old_file = 'uploads/' . $photo->photo;
                    if (is_file($old_file)) {
                        unlink($old_file);
                    }

                    $photo->delete();
                }
                $product->delete();
            }
            return response()->json([
                'status' => 200,
                'message' => trans('messages.your_project_deleted_successfully'),
            ]);
        }
    }

    public function search_name(Request $request)
    {

        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $offers = Products::where(function ($query) use ($request) {
            if ($request->input('title')) {
                $query->where('title', 'LIKE', "%" . $request->input('title') . "%");
            }
        })
            ->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['getComments' => function ($query) {
                $query->select('*');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->orderBy('id', 'DESC')->get();

        foreach ($offers as $offer) {
            $offer->{"created_time"} = Carbon::parse($offer->created_at)->diffForHumans();
        }

        return response()->json([
            'status' => 200,
            'data' => $offers,
        ]);
    }

    public function search_price(Request $request)
    {

        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $offers = Products::where(function ($query) use ($request) {
            if ($request->input('from')) {
                $query->where('price', '>=', $request->input('from'));
            }
            if ($request->input('sub_category_id')) {
                $query->where('sub_category_id', $request->input('sub_category_id'));
            }
            if ($request->input('to')) {
                $query->where('price', '<=', $request->input('to'));
            }
        })
            ->select('*')
            ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM comments WHERE comments.product_id=products.id ) as product_rate')
            ->selectRaw('(SELECT count(*) FROM product_likes WHERE product_likes.user_id =' . $user_like . ' AND product_likes.product_id=products.id	) as if_user_like_product')
            ->with(['getCategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getSubcategory' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getUser' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->with(['getComments' => function ($query) {
                $query->select('*');
            }])
            ->with(['photoImage' => function ($query) {
                $query->select('*');
                $query->selectRaw('(CONCAT ("' . \url('/') . '/uploads/", photo)) as photo');
            }])
            ->orderBy('id', 'DESC')->get();

        foreach ($offers as $offer) {
            $offer->{"created_time"} = Carbon::parse($offer->created_at)->diffForHumans();
        }

        return response()->json([
            'status' => 200,
            'data' => $offers,
        ]);
    }

    public function add_profile_image(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => "حدثت بعض الاخطاء التالية.",
                ]
            );
        }

        $user = User::find($user->id);
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $user->photo = $fileName;
        }
        $user->save();

        return response()->json(
            [
                'status' => 200,
                'message' => "تم رفع الصورة بنجاح",
                'photo' => url('/') . '/uploads/' . $user->photo,
            ]
        );
    }

    public function like_project(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }
        $id = $request->project_id;
        $ads = Projects::find($id);
        $user = JWTAuth::parseToken()->authenticate();

        if (!$ads) {
            return response()->json([
                'status' => 400,
                'message' => trans('messages.project_not_found'),
            ]);
        } elseif (!Likes::where('user_id', $user->id)->where('project_id', $id)->first()) {
            $like = new Likes();
            $like->project_id = $id;
            $like->user_id = $user->id;
            $like->save();
            return response()->json([
                'status' => 200,
                'message' => trans('messages.like_added_successfully'),
                'like_status' => 1,
            ]);
        } else {
            $like = Likes::where('user_id', $user->id)->where('project_id', $id)->first();
            $like->delete();
            return response()->json([
                'status' => 200,
                'message' => trans('messages.like_deleted_successfully'),
                'like_status' => 0,
            ]);
        }
    }

    public function favourite_projects(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (App::isLocale('ar')) {

            $offers = Likes::where('user_id', $user->id)->with(['getProject' => function ($q1) use ($user) {
                $q1->select('*')
                    ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user->id . ' AND likes.project_id=projects.id	) as if_user_like_project')
                    ->with(['getCategory' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    ->with(['getSubcategory' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    ->with(['getPhotos' => function ($query) {
                        $query->select('id', 'project_id');
                        $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                    }])
                    ->with(['user' => function ($query) {
                        $query->select('*');
                        $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                    }]);
            }])->paginate(10);

            foreach ($offers as $offer) {
                $offer->{"created_time"} = Carbon::parse($offer->created_at)->diffForHumans();
            }
        } else {
            $offers = Likes::where('user_id', $user->id)->with(['getOffer' => function ($q1) use ($user) {
                $q1->select('*')
                    ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user->id . ' AND likes.project_id=projects.id	) as if_user_like_project')
                    ->with(['getCategory' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    ->with(['getSubcategory' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    ->with(['getPhotos' => function ($query) {
                        $query->select('id', 'project_id');
                        $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                    }])
                    ->with(['user' => function ($query) {
                        $query->select('*');
                        $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                    }]);
            }])->paginate(10);

            foreach ($offers as $offer) {
                $offer->{"created_time"} = Carbon::parse($offer->created_at)->diffForHumans();
            }
        }
        return response()->json([
            'status' => 200,
            'data' => $offers,
        ]);
    }

    public function get_user_package($user_id = 0)
    {
        $object = User::find($user_id)->load('getPackage');
        if ($object->package_id != 0) {
            $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($object->date_of_package)) . " +" . $object->days . " days"));
        } else {
            $date_of_end = "";
        }

        return [
            'user' => $object,
            'date_of_end' => $date_of_end,
        ];
    }

    public function contact_us(Request $request)
    {
        $users = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'name' => $request->complain == 0 ? 'required' : "",
            'email' => $request->complain == 0 ? 'required|email' : '',
            'message_type_id' => $request->complaint == 1 ? 'required' : '',
            'message' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $user = new Contacts();
        $user->name = $request->name ? $request->name : "";
        $user->email = $request->email ? $request->email : "";
        $user->message_type_id = $request->message_type_id ?: 0;
        $user->order_id = $request->order_id ?: 0;
        $user->user_id = $users->id;
        $user->message = $request->message;
        $user->save();

        return response()->json(
            [
                'message' => __('messages.message_sent'),
            ]
        );
    }

    public function all_projects()
    {

        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        if (App::isLocale('ar')) {
            $projects = Projects::select('*')
                ->where('status', 1)
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.project_id=projects.id	) as if_user_like_project')
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getSubcategory' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getPhotos' => function ($query) {
                    $query->select('id', 'project_id');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['user' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with('state')
                ->with('country')
                ->orderBy('id', 'DESC')->paginate(10);
            $res = [];
            foreach ($projects as $project) {
                $project->{"created_time"} = Carbon::parse($project->created_at)->diffForHumans();
                $res[] = $project;
            }
        } else {
            $projects = Projects::select('*')
                ->where('status', 1)
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.project_id=projects.id	) as if_user_like_project')
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getSubcategory' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getPhotos' => function ($query) {
                    $query->select('id', 'project_id');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with(['user' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                }])
                ->with('state')
                ->with('country')
                ->orderBy('id', 'DESC')->paginate(10);
            $res = [];
            foreach ($projects as $project) {
                $project->{"created_time"} = Carbon::parse($project->created_at)->diffForHumans();
                $res[] = $project;
            }
        }

        return response()->json([
            'status' => 200,
            'data' => $projects,
        ]);
    }

    //    public function project($id=0)
    //    {
    //
    //        $user_like = 0;
    //        try {
    //            if ($user = JWTAuth::parseToken()->authenticate()) {
    //                $user_like = $user->id;
    //            }
    //        } catch (TokenExpiredException $e) {
    //        } catch (TokenInvalidException $e) {
    //        } catch (JWTException $e) {
    //
    //        }
    //
    //        if(App::isLocale('ar')) {
    //            $project = Projects::where('id',$id)->where('status',1)->select('*')
    //                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.project_id=projects.id    ) as if_user_like_project')
    //                ->with(['getCategory' => function ($query) {
    //                    $query->select('id', 'name');
    //                }])
    //                ->with(['getSubcategory' => function ($query) {
    //                    $query->select('id', 'name');
    //                }])
    //                ->with(['getPhotos' => function ($query) {
    //                    $query->select('id', 'project_id');
    //                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
    //                }])
    //                ->with(['user' => function ($query) {
    //                    $query->select('*');
    //                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
    //                }])
    //                ->with('getProducts')
    //                ->with('getRatings.getUser')
    //                ->with('state')
    //                ->with('country')
    //                ->first();
    //            $project->{"created_time"}= Carbon::parse($project->created_at)->diffForHumans();
    //        }else{
    //            $project = Projects::where('id',$id)->where('status',1)->select('*')
    //                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.project_id=projects.id    ) as if_user_like_project')
    //                ->with(['getCategory' => function ($query) {
    //                    $query->select('id', 'name_en as name');
    //                }])
    //                ->with(['getSubcategory' => function ($query) {
    //                    $query->select('id', 'name_en as name');
    //                }])
    //                ->with(['getPhotos' => function ($query) {
    //                    $query->select('id', 'project_id');
    //                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
    //                }])
    //                ->with(['user' => function ($query) {
    //                    $query->select('*');
    //                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
    //                }])
    //                ->with('getProducts')
    //                ->with('state')
    //                ->with('getRatings.getUser')
    //
    //                ->with('country')
    //                ->first();
    //            $project->{"created_time"}= Carbon::parse($project->created_at)->diffForHumans();
    //        }
    //        return response()->json([
    //            'status'=> 200 ,
    //            'data'=>$project,
    //        ]);
    //
    //    }

    //    public function all_famous_projects(){
    //
    //        $user_like = 0;
    //        try {
    //            if ($user = JWTAuth::parseToken()->authenticate()) {
    //                $user_like = $user->id;
    //            }
    //        } catch (TokenExpiredException $e) {
    //        } catch (TokenInvalidException $e) {
    //        } catch (JWTException $e) {
    //
    //        }
    //        if (App::isLocale('ar')) {
    //            $projects = Projects::select('*')
    //                ->where('status',1)
    //                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.project_id=projects.id    ) as if_user_like_project')
    //                ->whereIn('user_id', function ($query) {
    //                    $query->select('id')
    //                        ->from(with(new User())->getTable())
    //                        ->where('package_id',2)
    //                        ->where('project_activate',1)
    //                    ;
    //                })
    //                ->with(['getCategory' => function ($query) {
    //                    $query->select('id', 'name');
    //                }])
    //                ->with(['getSubcategory' => function ($query) {
    //                    $query->select('id', 'name');
    //                }])
    //                ->with(['getPhotos' => function ($query) {
    //                    $query->select('id', 'project_id');
    //                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
    //                }])
    //                ->with(['user' => function ($query) {
    //                    $query->select('*');
    //                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
    //                }])
    //                ->with('state')
    //                ->with('country')
    //                ->orderBy('id', 'DESC')->paginate(10);
    //            $res = [];
    //            foreach ($projects as $project) {
    //                $project->{"created_time"} = Carbon::parse($project->created_at)->diffForHumans();
    //                $res[] = $project;
    //            }
    //        } else {
    //            $projects = Projects::select('*')
    //                ->where('status',1)
    //                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.project_id=projects.id    ) as if_user_like_project')
    //                ->whereIn('user_id', function ($query) {
    //                    $query->select('id')
    //                        ->from(with(new User())->getTable())
    //                        ->where('package_id',2)
    //                        ->where('project_activate',1)
    //                    ;
    //                })
    //                ->with(['getCategory' => function ($query) {
    //                    $query->select('id', 'name_en as name');
    //                }])
    //                ->with(['getSubcategory' => function ($query) {
    //                    $query->select('id', 'name_en as name');
    //                }])
    //                ->with(['getPhotos' => function ($query) {
    //                    $query->select('id', 'project_id');
    //                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
    //                }])
    //                ->with(['user' => function ($query) {
    //                    $query->select('*');
    //                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
    //                }])
    //                ->with('state')
    //                ->with('country')
    //                ->orderBy('id', 'DESC')->paginate(10);
    //            $res = [];
    //            foreach ($projects as $project) {
    //                $project->{"created_time"} = Carbon::parse($project->created_at)->diffForHumans();
    //                $res[] = $project;
    //            }
    //
    //        }
    //
    //        return response()->json([
    //            'status'=> 200 ,
    //            'data'=>$projects,
    //        ]);
    //    }

    //    public function charge_card(Request $request)
    //    {
    //        $user =   JWTAuth::parseToken()->authenticate();
    //
    //        $validator = Validator::make($request->all(), [
    //            'card_number' => 'required',
    //        ]);
    //
    //        if ($validator->fails()) {
    //            return response()->json(
    //                [
    //                    'status' => 400 ,
    //                    'errors' => $validator->errors(),
    //                    'message' => trans('messages.some_error_happened') ,
    //                ]
    //            );
    //        }
    //
    //        $card = MrmandoobCardsDetails::where('code',$request->card_number)->first();
    //        $is_used = MrmandoobCardsDetails::where('code',$request->card_number)->where('used',0)->first();
    //        if($card && $is_used){
    //            $card->used=1;
    //            $card->user_id=$user->id;
    //            $card->save();
    //
    //            $object = new Balance();
    //            $object->user_id = $user->id;
    //            $object->price = $card->type;
    //            $object->balance_type_id = 3;
    //            $object->notes = "شحن كارت " .$card->type ;
    //            $object->save();
    //
    //
    //            $notification55 = new Notification();
    //            $notification55 -> sender_id = 1 ;
    //            $notification55 -> reciever_id = $user->id;
    //            $notification55 -> type = 15;
    //            $notification55 -> message =  "تم شحن رصيد بقيمة ".$object->price." ريال  " ;
    //            $notification55 -> message_en =  "Your balance charged with ".$object->price." SAR " ;
    //            $notification55 ->save();
    //
    //
    //
    //            $optionBuilder = new OptionsBuilder();
    //            $optionBuilder->setTimeToLive(60*20);
    //
    //            if(@$notification55->getReciever->lang == "en"){
    //                $notification_title="Charge Mr.mandoob card";
    //                $notification_message = $notification55->message_en;
    //            }else{
    //                $notification_title="شحن بطاقة مستر مندوب";
    //                $notification_message = $notification55->message;
    //            }
    //            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
    //            $notificationBuilder->setBody($notification_message)
    //                ->setSound('default');
    //
    //            $dataBuilder = new PayloadDataBuilder();
    //            $dataBuilder->addData(['data' =>[
    //                'notification_type'=> (int)$notification55 -> type,
    //                'notification_title'=> $notification_title ,
    //                'notification_message'=> $notification_message ,
    //                'notification_data' => null
    //            ]
    //            ]);
    //
    //            $option = $optionBuilder->build();
    //            $notification = $notificationBuilder->build();
    //            $data = $dataBuilder->build();
    //
    //            $token = @$notification55->getReciever->device_token;
    //
    //            if($token) {
    //                $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ?  null : $notification, $data);
    //                $downstreamResponse->numberSuccess();
    //                $downstreamResponse->numberFailure();
    //                $downstreamResponse->numberModification();
    //            }
    //
    //            return response()->json(
    //                [
    //                    'status' => 200 ,
    //                    'message' => 'تم  شحن رصيدك بقيمة '.$card->type.' ريال بنجاح .' ,
    //                ]);
    //        }elseif (!$is_used && $card){
    //            return response()->json(
    //                [
    //                    'status' => 400 ,
    //                    'message' => "تم استخدام هذا الكارت من قبل" ,
    //                ]
    //            );
    //        }else{
    //            return response()->json(
    //                [
    //                    'status' => 400 ,
    //                    'message' => "لا يوجد كارت بهذا الرقم" ,
    //                ]
    //            );
    //        }
    //
    //
    //
    //
    //    }

    // damage estimate
    public function addDamageEstimate(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'service_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in add car param'], 400);
        }
        $is_review = Settings::where('option_name', 'damage_review')->first();
        $object = new DamageEstimate();
        $object->address_id = $request->address_id;
        $object->service_id = $request->service_id;
        $object->description = $request->description ?: '';
        $object->user_id = $user->id;
        $object->published = $is_review && $is_review->value == '1' ? 0 : 1;

        $object->save();

        $photos = json_decode($request->photos);
        if (count($photos) > 0) {
            foreach ($photos as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo->photo;
                    $new_main = "uploads/" . $photo->photo;
                    if (is_file($old_main)) {
                        File::move($old_main, $new_main);
                        $other_photos = new DamagePhoto();
                        $other_photos->damage_id = $object->id;
                        $other_photos->photo = $photo->photo;
                        //                        $other_photos->type = $photo->type;
                        $other_photos->save();
                    }
                }
            }
        }

        // damage requests
        $select_name = 'name';
        if (App::getLocale() == "en") {
            $select_name = 'name_en as name';
        }
        //        $requests = DamageEstimate::select('damage_estimates.id', 'damage_estimates.user_id', 'damage_estimates.description',
        //            'damage_estimates.car_id', 'damage_estimates.created_at', 'makes.' . $select_name . ' as car_name', 'models.' . $select_name . ' as model_name', 'make_years.year')
        //            ->where('damage_estimates.user_id', $user->id)
        //            ->where('damage_estimates.payment_method','<>', 0)
        //
        //            ->with(['photos' => function ($query) {
        //                $query->select('damage_id');
        //                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", damage_photos.photo)) END) AS photo');
        //            }])
        //            ->selectRaw('(CASE WHEN makes.image = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", makes.image)) END) AS car_photo')
        //            ->join('user_cars', 'user_cars.id', 'damage_estimates.car_id')
        //            ->join('makes', 'makes.id', 'user_cars.make_id')
        //            ->join('models', 'models.id', 'user_cars.model_id')
        //            ->join('make_years', 'make_years.id', 'user_cars.year_id')
        //            ->paginate(20);

        $damage_fees = Settings::find(20)->value;
        $balance = Balance::where('user_id', $user->id)->sum('price');

        return response()->json(
            [
                'balance' => $balance,
                'fees' => $damage_fees,
                'order_id' => $object->id,
                'message' => 'تم إضافة الطلب بنجاح',
                //                'requests' => DamageRequestResources::collection($requests)
            ]
        );
    }

    // pricing order
    public function addPricingOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'parts' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in add order param'], 400);
        }
        $object = new PricingOrder();
        $object->address_id = $request->address_id;
        $object->user_id = $user->id;
        $object->description = $request->description ?: '';
        $is_review = Settings::where('option_name', 'pricing_review')->first();
        $object->published = $is_review && $is_review->value == '1' ? 0 : 1;
        $object->save();

        $parts = json_decode($request->parts);
        if ($parts && count($parts) > 0) {
            foreach ($parts as $part) {
                if (!empty($part)) {
                    $order_part = new PricingOrderPart();
                    $order_part->category_id = $part->category_id;
                    $order_part->subcategory_id = $part->subcategory_id;
                    $order_part->part_name = $part->part_name;
                    $order_part->quantity = $part->quantity;
                    $order_part->measurement_id = $part->measurement_id;

                    $order_part->order_id = $object->id;
                    if ($part->photo) {
                        $old_main = "temp/" . $part->photo;
                        $new_main = "uploads/" . $part->photo;
                        if (is_file($old_main)) {
                            File::move($old_main, $new_main);
                            $order_part->photo = $part->photo;
                        }
                    }
                    $order_part->save();
                }
            }
        }

        $pricing_fees = Settings::find(21)->value;
        $balance = Balance::where('user_id', $user->id)->sum('price');
        $select_name = App::getLocale() == "ar" ? 'categories.name as category_name' : 'categories.name_en as category_name';
        $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';

        $orderParts = PricingOrderPart::select('pricing_order_parts.id', 'pricing_order_parts.part_name', 'pricing_order_parts.quantity', $select_name, $select_measurement)
            ->selectRaw('(CASE WHEN categories.photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", categories.photo)) END) AS category_photo')
            ->join('categories', 'categories.id', 'pricing_order_parts.category_id')
            ->join('measurement_units', 'measurement_units.id', 'pricing_order_parts.measurement_id')
            ->where('order_id', $object->id)->get();
        return response()->json(
            [
                'balance' => $balance,
                'fees' => $pricing_fees,
                'order_id' => $object->id,
                'order_parts' => $orderParts,
                'order_description' => $request->description ?: '',
                'message' => 'تم إضافة الطلب بنجاح',
            ]
        );
    }

    public function deletePricingItem(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $part = PricingOrderPart::find($request->part_id);
        if (!$part) {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد قطعة',
            ]);
        }
        if (!$part->pricing_order) {
            return \response()->json([
                'status' => 400,
                'message' => 'القطعة ليست لها طلب',
            ]);
        }
        if ($part->pricing_order->user_id != $user->id) {
            return \response()->json([
                'status' => 400,
                'message' => 'القطعة ليست لهذا المستخدم',
            ]);
        }
        if ($part->photo) {
            unlink("uploads/" . $part->photo);
        }
        $part->delete();
        return \response()->json([
            'status' => 200,
            'message' => 'تم حذف القطعة',
        ]);
    }

    public function getMyPricingRequests(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_name = 'name';
        if (App::getLocale() == "en") {
            $select_name = 'name_en as name';
        }
        $objects = PricingOrder::select('pricing_orders.id', 'pricing_orders.status', 'pricing_orders.user_id', 'pricing_orders.description', 'pricing_orders.created_at')
            ->where('pricing_orders.user_id', $user->id)
            ->where('pricing_orders.payment_method', '<>', 0)
            ->with(['parts' => function ($query) {
                $select_category = App::getLocale() == "ar" ? 'categories.name as category_name' : 'categories.name_en as category_name';
                $select_subcategory = App::getLocale() == "ar" ? 'sub_categories.name as subcategory_name' : 'sub_categories.name_en as subcategory_name';
                $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measure_unit' : 'measurement_units.name_en as measure_unit';

                $query->select(
                    'pricing_order_parts.id',
                    'pricing_order_parts.category_id',
                    $select_category,
                    $select_subcategory,
                    $select_measurement,
                    'pricing_order_parts.quantity',
                    'pricing_order_parts.order_id'
                )
                    ->join('categories', 'categories.id', 'pricing_order_parts.category_id')
                    ->join('sub_categories', 'sub_categories.id', 'pricing_order_parts.subcategory_id')
                    ->join('measurement_units', 'measurement_units.id', 'pricing_order_parts.measurement_id')
                    ->selectRaw('(SELECT count(*) FROM pricing_offers WHERE pricing_offers.part_id =pricing_order_parts.id ) as offers_count')
                    ->selectRaw('(CASE WHEN categories.photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", categories.photo)) END) AS category_photo')
                    ->selectRaw('(CASE WHEN pricing_order_parts.photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", pricing_order_parts.photo)) END) AS photo');
            }])
            ->selectRaw('(SELECT tickets.id FROM tickets WHERE tickets.order_id =pricing_orders.id AND type="pricing"	) as ticket_id')
            ->orderBy('pricing_orders.id', 'DESC')
            ->paginate(15);

        $objects->{'objects'} = PricingRequestResources::collection($objects);

        return response()->json($objects, 200);
    }

    public function getMyDamageRequests(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_name = 'name';
        if (App::getLocale() == "en") {
            $select_name = 'name_en as name';
        }

        $objects = DamageEstimate::select(
            'damage_estimates.id',
            'damage_estimates.status',
            'damage_estimates.user_id',
            'damage_estimates.description',
            'damage_estimates.service_id',
            'damage_estimates.created_at',
            'services_categories.' . $select_name . ' as service_name'
        )
            ->where('damage_estimates.user_id', $user->id)
            ->where('damage_estimates.payment_method', '<>', 0)
            ->with(['photos' => function ($query) {
                $query->select('damage_id');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", damage_photos.photo)) END) AS photo');
            }])
            ->selectRaw('(CASE WHEN services_categories.photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", services_categories.photo)) END) AS service_photo')
            ->join('services_categories', 'services_categories.id', 'damage_estimates.service_id')
            ->orderBy('damage_estimates.id', 'desc')
            ->paginate(20);
        $objects->{'objects'} = DamageRequestResources::collection($objects);
        return response()->json($objects, 200);
    }

    public function getPricingOrderOffers(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $part_id = $request->part_id;
        $select_name = App::getLocale() == "ar" ? 'name' : 'name_en';
        $objects = PricingOffer::select(
            'pricing_offers.part_id',
            'pricing_offers.id',
            'pricing_offers.prepare_time',
            'pricing_offers.price',
            'pricing_offers.manufacture_country',
            'pricing_offers.brand',
            'pricing_offers.available_quantity',
            'users.username as shop_name',
            'manufacture_types.' . $select_name . ' as manufacture_type'
            //            'shop_types.' . $select_name . ' as shop_type'
        )
            ->selectRaw('(CASE WHEN users.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS shop_photo')
            ->selectRaw('(pricing_offers.available_quantity * pricing_offers.price) AS total_price')
            ->join('users', 'users.id', 'pricing_offers.provider_id')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.item_id =pricing_offers.id AND cart_items.type=2 ) as is_carted')
            //            ->join('shop_types', 'users.shop_type', 'shop_types.id')
            ->join('manufacture_types', 'manufacture_types.id', 'pricing_offers.manufacture_type')
            ->where('pricing_offers.part_id', $part_id)
            ->orderBy('pricing_offers.id', 'desc')
            ->paginate(20);
        return response()->json($objects);
    }

    public function getDamageOrderOffers(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order_id = $request->order_id;
        $select_name = 'name';
        if (App::getLocale() == "en") {
            $select_name = 'name_en as name';
        }
        $objects = DamageOffer::select('*')
            ->with(['shop' => function ($query) use ($user) {
                $query->select('id', 'username', 'address', 'longitude', 'latitude', 'email', 'phone');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                $query->selectRaw('(SELECT count(*) FROM ratings WHERE ratings.user_id =' . $user->id . ' AND ratings.item_id=users.id AND type=1) as is_rated');
                $query->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.item_id=users.id and ratings.type=1 ) as shop_rate');
            }])
            ->where('order_id', $order_id)
            ->orderBy('status', 'DESC')
            ->paginate(20);

        $order = DamageEstimate::select(
            'damage_estimates.id',
            'damage_estimates.status',
            'damage_estimates.user_id',
            'damage_estimates.description',
            'damage_estimates.service_id',
            'damage_estimates.created_at',
            'services_categories.' . $select_name . ' as service_name'
        )
            ->where('damage_estimates.user_id', $user->id)
            ->where('damage_estimates.payment_method', '<>', 0)
            ->with(['photos' => function ($query) {
                $query->select('damage_id');
                $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", damage_photos.photo)) END) AS photo');
            }])
            ->selectRaw('(SELECT tickets.id FROM tickets WHERE tickets.order_id =damage_estimates.id AND type="damage"	) as ticket_id')
            ->selectRaw('(CASE WHEN services_categories.photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", services_categories.photo)) END) AS service_photo')
            ->join('services_categories', 'services_categories.id', 'damage_estimates.service_id')
            ->where('damage_estimates.id', $order_id)
            ->first();

        $resp = [];
        $objects->{'objects'} = DamageOfferResources::collection($objects);
        $resp['order'] = new DamageRequestResources($order);

        $resp['offers'] = $objects;
        return response()->json($resp);
    }

    // add shop offers
    public function addPricingOffer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $part = PricingOrderPart::where('id', $request->part_id)
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('part_id')
                    ->from(with(new PricingOffer())->getTable())
                    ->where('provider_id', $user->id);
            })->first();
        if (!$part || @$part->pricing_order->status != 0) {
            return response()->json(
                [
                    'status' => 400,

                    'message' => 'can\'t add offer on this order .',
                ]
            );
        }

        $validator = Validator::make($request->all(), [
            'manufacture_type' => 'required',
            'prepare_time' => 'required',
            'available_quantity' => 'required',
            'price' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in add car param'], 400);
        }
        $provider_id = $user->user_type_id == 3 ? $user->id : $user->main_provider;
        $object = new PricingOffer();
        $object->part_id = $request->part_id;
        $object->provider_id = $provider_id;
        $object->manufacture_type = $request->manufacture_type ?: '';
        $object->available_quantity = $request->available_quantity;
        $object->prepare_time = $request->prepare_time;
        $object->price = $request->price;
        $object->order_type = $request->order_type ?: '';
        $object->manufacture_country = $request->manufacture_country ?: '';
        $object->brand = $request->brand ?: '';
        $object->save();

        $notify_message = 'قام التاجر ' . $user->username . ' باضافة عرض جديد على طلبك رقم #' . $part->pricing_order->id;
        $notify = new Notification();
        $notify->sender_id = $user->id;
        $notify->reciever_id = $part->pricing_order->user_id;
        $notify->type = 5;
        $notify->url = '/provider-panel/pricing-orders/' . $object->id;
        $notify->message = $notify_message;
        $notify->message_en = 'new parts pricing offer  by ' . $user->username . ' to your order number #' . $part->pricing_order->id;
        $notify->ads_id = @$part->pricing_order->id;
        $notify->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $optionBuilder->setContentAvailable(true);

        $notification_title = "عرض تسعير جديد";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notify_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');

        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'data' => [
                'notification_type' => 5,
                'notification_title' => $notification_title,
                'notification_message' => $notify_message,
                'notification_data' => '{ads_id:' . @$part->pricing_order->id . '}',
            ],
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = DeviceTokens::whereIn('user_id', function ($query) use ($part) {
            $query->select('id')
                ->from(with(new User())->getTable())
                ->where('id', $part->pricing_order->user_id)
                ->where('block', 0)
                ->where('notification', 1);
        })->pluck('device_token')->toArray();
        if (count($tokens)) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
        $part_id = $request->part_id;
        $select_name = App::getLocale() == "ar" ? 'name' : 'name_en';
        $offer = PricingOffer::select(
            'pricing_offers.part_id',
            'pricing_offers.id',
            'pricing_offers.prepare_time',
            'pricing_offers.price',
            'pricing_offers.manufacture_country',
            'pricing_offers.brand',
            'pricing_offers.available_quantity',
            'users.username as shop_name',
            'manufacture_types.' . $select_name . ' as manufacture_type',
            'shop_types.' . $select_name . ' as shop_type'
        )
            ->selectRaw('(CASE WHEN users.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS shop_photo')
            ->selectRaw('(pricing_offers.available_quantity * pricing_offers.price) AS total_price')
            ->join('users', 'users.id', 'pricing_offers.provider_id')
            ->join('shop_types', 'users.shop_type', 'shop_types.id')
            ->join('manufacture_types', 'manufacture_types.id', 'pricing_offers.manufacture_type')
            ->where('pricing_offers.part_id', $part_id)
            ->where('provider_id', $user->id)->first();

        return response()->json(
            [
                'message' => 'تم إضافة العرض بنجاح',
                'offer' => $offer,
            ]
        );
    }

    public function showPricingOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $objects = PricingOrder::select('pricing_orders.id', 'pricing_orders.status', 'pricing_orders.user_id', 'pricing_orders.description', 'pricing_orders.created_at')
            ->where('pricing_orders.id', $request->order_id)
            ->with(['parts' => function ($query) use ($user) {
                $select_category = App::getLocale() == "ar" ? 'categories.name as category_name' : 'categories.name_en as category_name';
                $select_subcategory = App::getLocale() == "ar" ? 'sub_categories.name as subcategory_name' : 'sub_categories.name_en as subcategory_name';
                $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measure_unit' : 'measurement_units.name_en as measure_unit';

                $query->select(
                    'pricing_order_parts.id',
                    'pricing_order_parts.category_id',
                    $select_category,
                    $select_subcategory,
                    $select_measurement,
                    'pricing_order_parts.quantity',
                    'pricing_order_parts.order_id'
                )
                    ->join('categories', 'categories.id', 'pricing_order_parts.category_id')
                    ->join('sub_categories', 'sub_categories.id', 'pricing_order_parts.subcategory_id')
                    ->join('measurement_units', 'measurement_units.id', 'pricing_order_parts.measurement_id')
                    ->selectRaw('(SELECT count(*) FROM pricing_offers WHERE pricing_offers.provider_id =' . $user->id . ' AND pricing_offers.part_id=pricing_order_parts.id	) as if_offer')
                    ->selectRaw('(CASE WHEN categories.photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", categories.photo)) END) AS category_photo')
                    ->selectRaw('(CASE WHEN pricing_order_parts.photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", pricing_order_parts.photo)) END) AS photo');
            }])
            ->selectRaw('(SELECT tickets.id FROM tickets WHERE tickets.order_id =pricing_orders.id AND type="pricing"	) as ticket_id')
            ->first();
        return response()->json($objects, 200);

        //        return response()->json(new PricingRequestResources($objects), 200);
    }

    public function showPartMyOffer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_name = 'name';
        if (App::getLocale() == "en") {
            $select_name = 'name_en as name';
        }
        $part_id = $request->part_id;
        $objects = PricingOffer::select(
            'pricing_offers.part_id',
            'pricing_offers.id',
            'pricing_offers.prepare_time',
            'pricing_offers.price',
            'pricing_offers.manufacture_country',
            'pricing_offers.brand',
            'pricing_offers.available_quantity',
            'users.username as shop_name',
            'manufacture_types.' . $select_name . ' as manufacture_type',
            //            'pricing_order_types.' . $select_name . ' as order_type',
            'shop_types.' . $select_name . ' as shop_type'
        )
            ->selectRaw('(CASE WHEN users.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS shop_photo')
            ->selectRaw('(pricing_offers.available_quantity * pricing_offers.price) AS total_price')
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.item_id=users.id and ratings.type=1 ) as shop_rate')
            ->join('users', 'users.id', 'pricing_offers.provider_id')
            ->join('pricing_order_parts', 'pricing_order_parts.id', 'pricing_offers.part_id')
            ->join('pricing_orders', 'pricing_orders.id', 'pricing_order_parts.order_id')
            ->join('shop_types', 'users.shop_type', 'shop_types.id')
            ->join('manufacture_types', 'manufacture_types.id', 'pricing_offers.manufacture_type')
            //            ->join('pricing_order_types', 'pricing_order_types.id', 'pricing_offers.order_type')
            ->where('pricing_orders.user_id', $user->id)
            ->where('pricing_offers.part_id', $part_id)
            //            ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->paginate(1);

        return response()->json($objects);
    }

    public function addDamageOffer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $order = DamageEstimate::where('id', $request->order_id)
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('order_id')
                    ->from(with(new DamageOffer())->getTable())
                    ->where('provider_id', $user->id);
            })
            ->first();
        if (!$order || $order->status != 0) {
            return response()->json(
                [
                    'status' => 400,

                    'message' => 'can\'t add offer on this order .',
                ]
            );
        }

        $validator = Validator::make($request->all(), [
            'cost_from' => 'required',
            'cost_to' => 'required',
            'time' => 'required',
            'description' => 'required|max:1500',

        ]);

        if ($validator->fails()) {
            return response()->json(['messaage' => 'error in add car param'], 400);
        }
        $provider_id = $user->user_type_id == 3 ? $user->id : $user->main_provider;
        $object = new DamageOffer();
        $object->order_id = $request->order_id;
        $object->provider_id = $provider_id;
        $object->cost_from = $request->cost_from;
        $object->cost_to = $request->cost_to;
        $object->time = $request->time;
        $object->description = $request->description;
        $object->save();

        $notify_message = 'قام التاجر ' . $user->username . ' باضافة عرض جديد على طلبك رقم #' . $order->id;
        $notify = new Notification();
        $notify->sender_id = $user->id;
        $notify->reciever_id = $order->user_id;
        $notify->type = 6;
        $notify->url = '/provider-panel/damage-estimates/' . $object->id;
        $notify->message = $notify_message;
        $notify->message_en = 'new parts pricing offer  by ' . $user->username . ' to your order number #' . $order->id;
        $notify->ads_id = $order->id;
        $notify->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $optionBuilder->setContentAvailable(true);

        $notification_title = "عرض تقدير اضرار جديد";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notify_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');

        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'data' => [
                'notification_type' => 6,
                'notification_title' => $notification_title,
                'notification_message' => $notify_message,
                'key' => $order->id,

                'notification_data' => '{ads_id:' . $order->id . '}',
            ],
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = DeviceTokens::whereIn('user_id', function ($query) use ($order) {
            $query->select('id')
                ->from(with(new User())->getTable())
                ->where('id', $order->user_id)
                ->where('block', 0)
                ->where('notification', 1);
        })->pluck('device_token')->toArray();
        if (count($tokens)) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
        $order_id = $request->order_id;
        //        $select_name=App::getLocale() == "ar"?'name':'name_en';
        $offer = DamageOffer::select('*')
            ->with(['shop' => function ($query) {
                $query->select('id', 'username', 'address', 'longitude', 'latitude');
                $query->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.item_id=users.id and ratings.type=1 ) as shop_rate');

                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->where('order_id', $order_id)
            ->where('provider_id', $user->id)
            ->paginate(20);

        $offer->{'offer'} = DamageOfferResources::collection($offer);

        return response()->json(
            [
                'message' => 'تم إضافة العرض بنجاح',
                'offer' => $offer,
            ]
        );
    }

    public function showDamageOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_name = 'name';
        if (App::getLocale() == "en") {
            $select_name = 'name_en as name';
        }

        $order_id = $request->order_id;
        $objects = DamageEstimate::select(
            'damage_estimates.id',
            'damage_estimates.user_id',
            'damage_estimates.description',
            'damage_estimates.status',
            'damage_estimates.service_id',
            'damage_estimates.created_at',
            'services_categories.' . $select_name . ' as service_name'
        )
            ->with([
                'photos' => function ($query) {
                    $query->select('damage_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", damage_photos.photo)) END) AS photo');
                }, 'offers' => function ($query) use ($user) {
                    $query->select('id', 'order_id', 'cost_from', 'cost_to', 'time', 'description', 'created_at', 'provider_id', 'status')
                        ->with(['shop' => function ($query) {
                            $query->select('id', 'username', 'address', 'longitude', 'latitude');
                            $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                            $query->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.item_id=users.id and ratings.type=1 ) as shop_rate');
                        }])->where('provider_id', $user->id);
                },
            ])
            ->selectRaw('(CASE WHEN services_categories.photo = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", services_categories.photo)) END) AS service_photo')
            ->selectRaw('(SELECT count(*) FROM damage_offers WHERE damage_offers.provider_id =' . $user->id . ' AND damage_offers.order_id=damage_estimates.id	) as if_offer')
            ->selectRaw('(SELECT tickets.id FROM tickets WHERE tickets.order_id =damage_estimates.id AND type="damage"	) as ticket_id')
            ->join('services_categories', 'services_categories.id', 'damage_estimates.service_id')
            ->where('damage_estimates.id', $order_id)
            ->first();

        //        return \response()->json($objects);
        return response()->json(new DamageRequestResources($objects), 200);
    }

    public function acceptDamageOrderOffer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $offer_id = $request->offer_id;
        $object = DamageOffer::where('id', $offer_id)->whereIn('order_id', function ($query) use ($user) {
            $query->select('id')->from(with(new DamageEstimate())->getTable())
                ->where('payment_method', '<>', 0)
                ->where('user_id', $user->id);
        })->first();
        if (!$object) {
            return response()->json(
                [
                    'status' => 400,

                    'message' => 'error happened .',
                ]
            );
        }

        $object->status = 1;
        $object->save();
        $order = DamageEstimate::find($object->order_id);
        $order->status = 1;
        $order->shop_id = $object->provider_id;
        $order->save();

        $shop = User::select('id', 'username', 'address', 'longitude', 'latitude', 'phone', 'email')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->where('id', $object->provider_id)->first();
        $notify_message = "تم الموافقه على عرضك الذى قدمته على طلب صيانة من العضو : " . @$order->user->username;
        $notify = new Notification();
        $notify->sender_id = $user->id;
        $notify->reciever_id = $shop->id;
        $notify->type = 8;
        $notify->url = '/provider-panel/damage-estimates/' . $order->id;
        $notify->message = $notify_message;
        $notify->message_en = 'your offer has been accepted from user : ' . $user->username;
        $notify->ads_id = $order->id;
        $notify->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $optionBuilder->setContentAvailable(true);

        $notification_title = "موافقه على طلب صيانة";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notify_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');

        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'data' => [
                'notification_type' => 8,
                'notification_title' => $notification_title,
                'notification_message' => $notify_message,
                'key' => $order->id,
                'notification_data' => '{ads_id:' . $order->id . '}',
            ],
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = DeviceTokens::whereIn('user_id', function ($query) use ($shop) {
            $query->select('id')
                ->from(with(new User())->getTable())
                ->where('accept_pricing', 1)
                ->where('block', 0)
                ->where('is_archived', 0)
                ->where('id', $shop->id)
                ->where('notification', 1);
        })->pluck('device_token')->toArray();
        if (count($tokens)) {

            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }

        return response()->json([
            'message' => "you accept offer successfully ",
            'shop' => $shop,
        ], 200);
    }

    public function addShopRating(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'rate' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        if (Rating::where('item_id', $request->item_id)->where('user_id', $user->id)->where('type', '1')->first()) {
            return response()->json(
                [
                    'message' => "قمت بتقييم هذا التاجر من قبل ",
                ],
                400
            );
        } else {
            $rate = new Rating();
            $rate->user_id = $user->id;
            $rate->rate = $request->rate;
            $rate->item_id = $request->item_id;
            $rate->type = 1;
            $rate->comment = $request->input('comment') ?: '';
            $rate->save();

            return response()->json(
                [
                    'message' => "تم تقييم التاجر بنجاح .",
                ]
            );
        }
    }

    // tickets
    public function add_ticket(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $object = new Tickets();
        $object->name = $request->name;
        $object->user_id = $user->id;
        $object->order_id = $request->order_id ?: 0;
        $object->type = $request->type ?: 'none';
        $object->save();

        $comment = new Messages;
        $comment->ticket_id = $object->id;
        $comment->sender_id = $user->id;
        $comment->reciever_id = 1;
        $comment->message = $request->input('message');
        $comment->save();

        $object->{"created_time"} = Carbon::parse($object->created_at)->diffForHumans();

        return response()->json(
            [
                'status' => 200,
                'message' => 'تم  انشاء التذكرة بنجاح .',
                'data' => $object,
            ]
        );
    }

    public function send_message_admin(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'message' => "required",
            'ticket_id' => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $message = new Messages();
        $message->sender_id = $user->id;
        $message->reciever_id = 1;
        //        $message->type=0;
        $message->message = $request->message ?: "";
        $message->ticket_id = $request->ticket_id;
        $message->save();

        $message->{'created_time'} = $message->created_at->diffForHumans();

        return response()->json(
            [
                'status' => 200,
                'message' => "تم ارسال رسالتك بنجاح .",
                'data' => $message,
            ]
        );
    }

    public function my_tickets(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $tickets = Tickets::select('tickets.id', 'tickets.name', 'tickets.order_id', 'tickets.type', 'tickets.created_at', 'tickets.closed', 'messages.message')
            ->join('messages', 'messages.ticket_id', '=', 'tickets.id')
            ->join(\DB::raw('(SELECT MAX(Id) as id,sender_id+reciever_id as mm FROM messages where sender_id=' . $user->id . ' or reciever_id=' . $user->id . ' GROUP BY ticket_id) AS n2'), function ($join) {
                $join->on('messages.id', '=', 'n2.id');
            })
            ->where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);

        foreach ($tickets as $ticket) {
            $ticket->{"created_time"} = Carbon::parse($ticket->created_at)->diffForHumans();
        }
        return response()->json($tickets);
    }

    public function get_messages_ticket($ticket_id = 0)
    {
        $user = JWTAuth::parseToken()->authenticate();
        //        $ticket = Tickets::where('id',$ticket_id)->where('user_id',$user->id)->first();
        $messages = Messages::select('id', 'message', 'sender_id', 'reciever_id', 'created_at')->where('ticket_id', $ticket_id)
            ->where(function ($query1) use ($user) {
                $query1->where(function ($query) use ($user) {
                    $query->where('sender_id', 1)
                        ->where('reciever_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', $user->id)
                        ->where('reciever_id', 1);
                });
            })->whereIn('ticket_id', function ($query) use ($ticket_id) {
                $query->select('id')->from(with(new Tickets())->getTable())
                    ->where('id', $ticket_id);
            })
            ->with(['getSenderUser' => function ($query) {
                $query->select('id', 'username');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }, 'getRecieverUser' => function ($query) {
                $query->select('id', 'username');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        foreach ($messages as $message) {
            $message->{"created_time"} = Carbon::parse($message->created_at)->diffForHumans();
        }

        foreach ($messages as $messageasd) {
            $notifications = Notification::where('message_id', $messageasd->id)->where('reciever_id', $user->id)->first();
            if ($notifications) {
                $notifications->status = 1;
                $notifications->save();
            }
        }

        return response()->json(
            $messages
        );
    }

    public function close_ticket(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'ticket_id' => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $ticket = Tickets::where('user_id', $user->id)->where('id', $request->ticket_id)->first();
        if (!$ticket) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('messages.sorry_not_your_ticket'),
                ]
            );
        }

        $ticket->closed = 1;
        $ticket->save();

        return response()->json(
            [
                'status' => 200,
                'message' => __('messages.your_ticket_closed_successfully'),
            ]
        );
    }

    public function rate_ticket(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'ticket_id' => "required",
            'rate' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $ticket = Tickets::where('user_id', $user->id)->where('id', $request->ticket_id)->first();
        if (!$ticket) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('messages.sorry_not_your_ticket'),
                ]
            );
        }

        $ticket->rate = $request->rate;
        $ticket->save();

        return response()->json(
            [
                'status' => 200,
                'message' => __('messages.your_ticket_rated_successfully'),
            ]
        );
    }

    public function addToCart(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $id = $request->product_id;
        $product = Products::find($id);

        if (!$product) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'لا يوجد منتج للاضافة للسلة .',
                ]
            );
        }
        if ($product->quantity < $product->min_quantity) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
                ]
            );
        }
        $cart_item = CartItem::where('item_id', $product->id)->where('type', 1)
            ->where('order_id', 0)->where('user_id', $user->id)->first();
        $mount = $product->calculateMinWareHouseQty(intval($request->quantity));
        if ($cart_item) {
            if ($request->type && $request->type == 'plus') {
                $mount = $product->calculateMinWareHouseQty($request->quantity);
                if ($mount == -1) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
                            'quantity' => $product->quantity - $product->min_warehouse_quantity,
                        ],
                        202
                    );
                }

                if ($mount == -2) {
                    return response()->json([
                        'status' => 400,
                        'message' => ' اقل كمية متاحة للبيع هي ' . $product->min_quantity,
                        'min_quantity' => $product->min_quantity,
                    ], 202);
                }

                if ($mount > $product->quantity) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
                            'quantity' => $product->quantity - $product->min_warehouse_quantity,

                        ]
                    );
                }
            } else {
                $mount = $product->calculateMinWareHouseQty($cart_item->quantity - 1);
                if ($mount == -1) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
                            'quantity' => $product->quantity - $product->min_warehouse_quantity,
                        ],
                        202
                    );
                }

                if ($mount == -2) {
                    $mount = $product->min_quantity;
                    return response()->json([
                        'status' => 400,
                        'message' => ' اقل كمية متاحة للبيع هي ' . $product->min_quantity,
                        'min_quantity' => $product->min_quantity,
                    ], 202);
                }

                if ($mount > $product->quantity) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
                            'quantity' => $product->quantity - $product->min_warehouse_quantity,

                        ]
                    );
                }
                if ($mount < $product->min_quantity) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => 'يجب الا تتخطى الحد الأدنى للطلب .',
                        ]
                    );
                }
            }
        } else {
            if ($mount == -1) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
                        'quantity' => $product->quantity - $product->min_warehouse_quantity,
                    ],
                    202
                );
            }

            if ($mount == -2) {
                $mount = $product->min_quantity;
                return response()->json([
                    'status' => 400,
                    'message' => ' اقل كمية متاحة للبيع هي ' . $product->min_quantity,
                    'min_quantity' => $product->min_quantity,
                ], 202);
            }

            if ($mount > $product->quantity) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => 'لا يوجد كمية متوفرة فى المخزن لهذا المنتج .',
                        'quantity' => $product->quantity - $product->min_warehouse_quantity,

                    ]
                );
            }
            $cart_item = new CartItem();
        }
        $cart_item->item_id = $id;

        $cart_item->user_id = $user->id;
        $cart_item->quantity = $mount;
        $cart_item->shop_id = $product->provider_id;
        $cart_item->type = 1;
        $cart_item->price = $product->price;
        $cart_item->save();

        return response()->json(
            [
                'status' => 200,
                'cart_count' => $user->cart->count(),
                'message' => 'تم اضافة المنتج الى السلة بنجاح',
            ]
        );
    }

    public function removeFromCart(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $id = $request->id;
        $type = $request->type ?: 1;

        $cart_item = CartItem::where('item_id', $request->id)
            ->where('order_id', 0)->where('user_id', $user->id)->where('type', $type)->first();
        if ($cart_item) {
            $cart_item->delete();
        }
        return response()->json(
            [
                'status' => 200,
                'cart_count' => $user->cart->count(),

                'message' => 'تم الحذف بنجاح',
            ]
        );
    }

    public function addToCartFromLocal(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $items = json_decode($request->items);
        if ($items && count($items) > 0) {
            foreach ($items as $item) {
                if (!empty($item) && $item->product_id) {
                    $product = Products::find($item->product_id);

                    if ($product && $product->quantity > 0) {
                        $cart_item = CartItem::where('item_id', $product->id)->where('type', 1)
                            ->where('order_id', 0)->where('user_id', $user->id)->first();
                        $mount = $item->quantity;
                        if ($cart_item) {
                            $mount = $mount + $cart_item->quantity;
                        } else {
                            $cart_item = new CartItem();
                        }
                        if ($mount > $product->quantity) {
                            $mount = $product->quantity;
                        }

                        $cart_item->item_id = $item->product_id;

                        $cart_item->user_id = $user->id;
                        $cart_item->quantity = $mount;
                        $cart_item->type = 1;
                        $cart_item->shop_id = $product->provider_id;
                        $cart_item->price = $product->price_after_discount ?: $product->price;
                        $cart_item->save();
                    }
                }
            }
        }

        return response()->json(
            [
                'status' => 200,
                'message' => 'تم اضافة المنتجات الى السلة بنجاح',
            ]
        );
    }

    public function getCartItems(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $tax = floatval(Settings::find(38)->value);
        $select_description = App::getLocale() == "ar" ? 'description' : 'description_en as description';
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';

        $current_items = CartItem::where('user_id', $user->id)->where('order_id', 0)->where('type', 1)
            ->whereHas('product')
            ->with('product')
            ->get();
        $user_address = Addresses::where(['user_id' => $user->id, 'is_home' => 1, 'is_archived' => 0])->first();
        $address_id = @$user_address ? $user_address->region_id : 0;
        $state_id = @$user_address ? $user_address->state_id : 0;
        $messages = [];
        foreach ($current_items as $item) {
            if ($item->type == 1) {
                $product = $item->product;
                $edit_mount = $product->calculateMinWareHouseQty($item->quantity);
                if ($product->quantity == 0 || $product->stop == 1 || $edit_mount == -1) {
                    $item->delete();
                    $messages[] = $product->title . ' لم يعد متاح الان ';
                } elseif ($edit_mount == -2) {
                    $item->quantity = $product->min_quantity;
                    $item->save();
                    $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
                } elseif ($edit_mount > 0 && $edit_mount != $item->quantity) {
                    $item->quantity = $edit_mount;
                    $item->save();
                    $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
                } elseif ($item->quantity > @$product->quantity) {
                    $item->quantity = $product->quantity;
                    $item->save();
                    $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
                }
                if ($address_id != 0) {
                    if ($product->has_regions1 == 1) {
                        $pro = ProductsRegions::where('product_id', $product->id)->where('region_id', $address_id)->first();
                        if (!$pro) {
                            $item->delete();
                            $messages[] = $product->title . ' لم يعد متاح الان فى منطقتك الحالية';
                        }
                        if ($state_id != 0) {
                            $pro = ProductsRegions::where('product_id', $product->id)->where('state_id', $state_id)->first();
                            if (!$pro) {
                                $item->delete();
                                $messages[] = $product->title . ' لم يعد متاح الان فى مدينتك الحالية';
                            }
                        }
                    }
                }
                $price = $product->price_after_discount ?: $product->price;
                if (@$product->stop == 1) {
                    $item->delete();
                    $messages[] = ' المنتج  ' . $product->title . ' غير متاح للطلب الان';
                }
            }
        }

        $objects = CartItem::select(
            'cart_items.id',
            DB::raw('ROUND((products.price +(products.price * ' . ($tax / 100) . ')),2) as price'),
            'cart_items.quantity',
            'cart_items.item_id',
            'products.title',
            'cart_items.type',
            'products.min_quantity',
            'products.quantity as product_quantity',
            $select_measurement
        )
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
            ->join('products', 'products.id', 'cart_items.item_id')
            ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')
            ->where('cart_items.order_id', 0)
            ->where('type', 1)
            ->where('user_id', $user->id)->get();
        //        $cart_offers = CartItem::select('cart_items.id', 'cart_items.price', 'cart_items.quantity', 'cart_items.item_id', 'pricing_order_parts.part_name as title', 'cart_items.type')
        //            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", pricing_order_parts.photo)) END) AS photo')
        //
        ////            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", pricing_order_parts.photo)) as photo')
        //            ->join('pricing_offers', 'pricing_offers.id', 'cart_items.item_id')
        //            ->join('pricing_order_parts', 'pricing_order_parts.id', 'pricing_offers.part_id')
        //            ->join('pricing_orders', 'pricing_orders.id', 'pricing_order_parts.order_id')
        //            ->where('cart_items.order_id', 0)
        //            ->where('cart_items.type', 2)
        //            ->where('cart_items.user_id', $user->id)->get();

        return response()->json(
            [
                'items' => $objects,
                //                'offer_items' => $cart_offers,
                'messages' => $messages,
            ]
        );
    }

    public function addOfferToCart(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $id = $request->offer_id;
        $offer = PricingOffer::select('pricing_offers.id', 'pricing_offers.available_quantity', 'pricing_offers.price', 'pricing_offers.provider_id')->where('pricing_offers.id', $id)
            ->join('pricing_order_parts', 'pricing_order_parts.id', 'pricing_offers.part_id')
            ->join('pricing_orders', 'pricing_orders.id', 'pricing_order_parts.order_id')
            ->where('pricing_orders.user_id', $user->id)->first();

        if (!$offer) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'لا يوجد عرض للاضافة للسلة .',
                ]
            );
        }
        $cart_item = CartItem::where('item_id', $offer->id)->where('type', 2)
            ->where('order_id', 0)->where('user_id', $user->id)->first();

        if ($cart_item) {
            return response()->json(
                [
                    'status' => 200,
                    'cart_count' => $user->cart->count(),
                    'message' => 'تم الاضافة من قبل',
                ]
            );
        } else {
            $cart_item = new CartItem();
        }
        $cart_item->item_id = $id;

        $cart_item->user_id = $user->id;
        $cart_item->quantity = $offer->available_quantity;
        $cart_item->type = 2;
        $cart_item->shop_id = $offer->provider_id;
        $cart_item->price = $request->price ?: $offer->price;
        $cart_item->save();

        return response()->json(
            [
                'status' => 200,
                'cart_count' => $user->cart->count(),
                'message' => 'تم اضافة العرض الى السلة بنجاح',
            ]
        );
    }

    // delivery address
    public function addresses(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $select_name = App::getLocale() == "ar" ? 'name' : 'name_en';

        $objects = DB::table('addresses')->select(
            'addresses.id',
            'addresses.is_home',
            'addresses.address',
            'addresses.details',
            'addresses.region_id',
            'addresses.state_id',
            'regions.' . $select_name . ' as region_name',
            'states.' . $select_name . ' as state_name',
            'addresses.longitude',
            'addresses.latitude',
            'addresses.phone1',
            'addresses.phone2',
            'addresses.email'
        )
            ->join('regions', 'regions.id', 'addresses.region_id')
            ->join('states', 'states.id', 'addresses.state_id')
            ->where('addresses.is_archived', 0)
            ->where('addresses.user_id', $user->id)->get();

        return response()->json(
            [
                'address' => $objects,
            ]
        );
    }

    public function store_address(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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
        $input['user_id'] = $user->id;

        $addresses_count = Addresses::where('user_id', $user->id)->count();

        if ($addresses_count == 0) {
            $input['is_home'] = 1;
        }
        Addresses::create($input);
        return response()->json([
            'message' => 'تم اضافة العنوان بنجاح',
        ]);
    }

    public function update_address(Request $request, $address_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
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
        $input['user_id'] = $user->id;
        $address = Addresses::where('id', $address_id)->where('user_id', $user->id)->first();
        if (!$address) {
            return response()->json([
                'message' => 'ليس لديك صلاحية حذف هذا العنوان او غير موجود',
                'status' => 400,
            ]);
        }
        $address->update($input);
        return response()->json([
            'message' => 'تم تعديل العنوان بنجاح',
        ]);
    }

    public function delete_address(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
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
        $address = Addresses::where('id', $request->address_id)->where('user_id', $user->id)->first();
        if (!$address) {
            return response()->json([
                'message' => 'ليس لديك صلاحية حذف هذا العنوان او غير موجود',
                'status' => 400,
            ]);
        }
        $address->is_archived = 1;
        $address->save();
        return response()->json([
            'message' => 'تم حذف العنوان بنجاح',
        ]);
    }

    public function setDefaultAddress(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
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
        $address = Addresses::where('id', $request->address_id)->where('user_id', $user->id)->first();
        if ($address) {
            $addresses = Addresses::where('user_id', $user->id)->update(['is_home' => 0]);
            $address->is_home = 1;
            $address->save();
        }

        return response()->json([
            'message' => 'تم تغيير العنوان للرئيسي بنجاح',
        ]);
    }

    public function getCartDetails(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';

        $cart_items = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'products.title',
            'users.username as shop_name',
            'users.shipment_price',
            $select_measurement
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->join('products', 'products.id', 'cart_items.item_id')
            ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')
            ->where('cart_items.order_id', 0)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('cart_items.shop_id')->get();
        $delivery_price = Settings::find(22)->value;
        return \response()->json([
            'data' => $cart_items->{'cart_items'} = CartResources::collection($cart_items),
            'delivery_price' => (int)$delivery_price,
            'taxes' => (int)Settings::find(38)->value,
        ]);
    }

    public function getCartDetails2(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';

        $cart_items = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'products.title',
            'users.username as shop_name',
            'users.shipment_price',
            $select_measurement
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->join('products', 'products.id', 'cart_items.item_id')
            ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')
            ->where('cart_items.order_id', 0)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('cart_items.shop_id')->get();
        $delivery_price = Settings::find(22)->value;
        return \response()->json([
            'data' => $cart_items->{'cart_items'} = CartResources::collection($cart_items),
            'delivery_price' => (int)$delivery_price,
            'taxes' => (int)Settings::find(38)->value,
        ]);
    }

    public function addOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->block == 1) {
            return \response()->json([
                'status' => 400,
                'message' => 'تم حظر حسابك',
            ]);
        }
        $order = Orders::where('user_id', $user->id)->where(['payment_method' => 0, 'status' => 0])->where('added_by', null)->latest()->first();
        $order_id = 0;
        if (!$order) {
            $order = new Orders();
            $order->user_id = $user->id;
            $order->save();
        }
        if ($request->address_id) {
            $address = Addresses::find($request->address_id);
            $order->longitude = $address->longitude;
            $order->address_name = $address->address;
            $order->address_desc = $address->details;
            $order->country_id = $address->country_id;
            $order->region_id = $address->region_id;
            $order->state_id = $address->state_id;
            if ($order->short_code == null) {
                $order->short_code = $order->id . str_random(4);
            }
        }

        //        $order->final_price = $request->final_price ?: 0;
        //        $order->order_price = $request->order_price ?: 0;
        //        $order->delivery_price = $request->delivery_price ?: 0;

        /**/

        $total = CartItem::where(['order_id' => $order_id, 'user_id' => $user->id])->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
        CartItem::where(['order_id' => $order_id, 'user_id' => $user->id])->update(['calculated' => 1]);
        if (!$total) {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة',
            ]);
        }
        $shipment_price = floatval(Settings::find(22)->value);
        $taxs = floatval(Settings::find(38)->value);
        $payment_atcive = Settings::find(34)->value;

        $cobon = 0;
        if ($request->cobon != '') {
            $cobon = floatval($request->cobon_discount);
            //            $order->final_price = ($total + $shipment_price - $cobon) + (($total + $shipment_price - $cobon) * $taxs / 100) ;
        }
        $tax_price = (($total + $shipment_price - $cobon) * $taxs / 100);
        $order->final_price = ($total + $shipment_price - $cobon) + (($total + $shipment_price - $cobon) * $taxs / 100);
        $order->order_price = $total;
        $order->delivery_price = $shipment_price;
        $order->taxes = $tax_price;
        $order->cobon = $request->cobon ?: '';
        $order->cobon_discount = $request->cobon_discount ?: 0;
        /**/

        $order->address_id = $request->address_id ?: 0;
        $order->status = 0;
        $order->created_at = Carbon::now();
        $order->save();

        @$is_min_price = Settings::find(41)->value;
        if (@$is_min_price == 1) {
            $min_price_for_order = Settings::find(42)->value;
            $order->refresh();
            if ($order->final_price < floatval($min_price_for_order)) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'الحد الادني للطلب هو ' . $min_price_for_order . ' ريال ',
                ], 400);
            }
        }

        $select_title = App::getLocale() == "ar" ? 'name' : 'name_en as name';

        $banks = Banks::select('id', $select_title)->get();

        $select_bank_name = App::getLocale() == "ar" ? 'bank_name' : 'bank_name_en as name';
        $bank_accounts = BankAccounts::select('id', $select_bank_name, 'account_name', 'account_number', 'account_ipan')->get();
        $balance = Balance::where('user_id', $user->id)->sum('price');


        if ((int)PaymentSettings::find(1)->value === 1 && !empty($order->user->phone) && !empty($order->user->email)) {
            $data = [
                'order_id' => $order->id,
            ];
            $tmara = new TmaraRepository();
            $tmara = $tmara->getPaymentTypes($data);
        }
        return \response()->json([
            'status' => 200,
            'show_tmara' => isset($tmara) && $tmara ? 1 : 0,
            'order_id' => $order->id,
            'order' => $order,
            'balance' => $balance,
            'banks' => $banks,
            'bank_accounts' => $bank_accounts,
            'rajhy_payment' => (int)PaymentSettings::find(9)->value,
            'tap_payment' => (int)PaymentSettings::find(4)->value,
            'pay_later' => (int)PaymentSettings::find(8)->value,
            'pay_balance' => (int)PaymentSettings::find(7)->value,
            'order_days' => 5,
            'message' => 'تم انشاء الطلب بنجاح',
        ]);

        if ($request->payment_type == 3) {
            $options = [
                'order_id' => $order->id,
            ];
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add($options);
            $order_by_balance = $this->sendBalanceOrder($myRequest);
            if ($order_by_balance[0] == 200) {
                $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
                $order->save();
                return \response()->json([
                    'status' => 200,
                    'order_id' => $order->id,
                    'balance' => $order_by_balance[2],
                    'message' => $order_by_balance[1],
                ]);
            } else {
                return \response()->json([
                    'status' => 400,
                    'message' => $order_by_balance[1],
                ]);
            }
        } elseif ($request->payment_type == 5) {
            $options = [
                'order_id' => $order->id,
                'formdata' => true,
                'type' => $request->payment_type == 5 ? 'payment_later' : '',
            ];
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add($options);
            $order_by_balance = $this->sendBankTransferOrder($myRequest);
            if ($order_by_balance[0] == 200) {
                $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
                $order->save();
                return \response()->json([
                    'status' => 200,
                    'order_id' => $order->id,
                    'message' => $order_by_balance[1],
                ]);
            } else {
                return \response()->json([
                    'status' => 400,
                    'message' => $order_by_balance[1],
                ]);
            }
        }
        $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
        $order->save();
        return \response()->json([
            'status' => 200,
            'order_id' => $order->id,
            'balance' => $balance,
            'message' => 'تم انشاء الطلب بنجاح وسيتم تحويلك لصفحة الدفع',
        ]);
    }

    //    public function addOrder(Request $request)
    //    {
    //        $user = JWTAuth::parseToken()->authenticate();
    //        $order = Orders::where('user_id', $user->id)->where('payment_method', 0)->first();
    //        if (!$order) {
    //            $order = new Orders();
    //        }
    //        $order->user_id = $user->id;
    //        $order->final_price = $request->final_price ?: 0;
    //        $order->order_price = $request->order_price ?: 0;
    //        $order->delivery_price = $request->delivery_price ?: 0;
    //        $order->address_id = $request->address_id ?: 0;
    //        $order->payment_method = 0;
    //        $order->status = 1;
    //        $order->cobon = $request->cobon ?: '';
    //        $order->taxes = $request->taxes ?: 0;
    //        $order->cobon_discount = $request->cobon_discount ?: 0;
    //        $order->save();
    //        $balance = Balance::where('user_id', $user->id)->sum('price');
    //
    //        return \response()->json([
    //            'status' => 200,
    //            'order_id' => $order->id,
    //            'balance' => $balance,
    //            'message' => 'تم انشاء الطلب بنجاح'
    //        ]);
    //    }
    public function withBalance($request, $user, $order, $ifByOrder = false)
    {
        ini_set('serialize_precision', -1);
        if ($request->with_balance == 1 || $request->with_balance == '1') {
            //            $total = CartItem::where(['order_id' => 0, 'user_id' => $user->id])
            //                ->orWhere(['order_id' => $order->id, 'user_id' => $user->id])
            //                ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
            $total = CartItem::where(function ($q) use ($ifByOrder, $order, $user) {
                if ($ifByOrder) {
                    $q->where(['order_id' => $order->id, 'user_id' => $user->id]);
                    $q->orWhere(['order_id' => 0, 'user_id' => $user->id]);
                } else {
                    $q->where(['order_id' => 0, 'user_id' => $user->id]);
                }
            })
                ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
            if ($ifByOrder) {
                $total = $order->final_price;
            }
            $money_transfered = 0;
            if ($request->money_transfered) {
                $money_transfered = round(floatval($request->money_transfered), 2);
            }
            $get_balance = Balance::where('user_id', $user->id)->sum('price');
            $payed_balance = $total - $money_transfered; //100 -10
            if ($get_balance < $payed_balance) {
                $payed_balance = $get_balance;
            }
            if (round($get_balance, 2) <= 0) {
                return [
                    'status' => 400,
                    'message' => 'الرصيد غير كافي',
                    'get_balance' => round($get_balance, 2),
                    'payed_balance' => round($payed_balance, 2),
                    'total' => round($total, 2),
                ];
            } else {
                return [
                    'status' => 200,
                    'get_balance' => round($get_balance, 2),
                    'payed_balance' => round($payed_balance, 2),
                    'total' => round($total, 2),
                ];
            }
        }
        return [
            'status' => 200,
        ];
    }

    public function sendBankTransferOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        // Log::alert($request->all());
        $order = Orders::whereId($request->order_id)->where('user_id', $user->id)->first();
        if (!$order) {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد طلب بهذا الرقم',
            ]);
        }

        if (($request->complete_order == 1 || $request->complete_order == '1')) {
            $order = Orders::find($request->order_id);
            $remining_details = $this->complete_order($request);
            if ($remining_details['status'] == 400) {
                return \response()->json($remining_details);
            }

            $transaction = new Transaction();
            $transaction->order_id = $order->id;
            $transaction->transfer_id = $order->id;
            $transaction->payment_method = 4;
            $transaction->price = $remining_details['remaining_money'];
            $transaction->payed_price = $remining_details['payed_money'];
            $transaction->status = 0;
            $transaction->save();
            if ($order) {
                $order->payment_method = 4;
                $order->save();
            }
        }
        $get_balance = $this->withBalance($request, $user, $order);
        if ($get_balance['status'] == 400) {
            return \response()->json($get_balance);
        }
        if (($request->complete_order == 1 || $request->complete_order == '1')) { //complete order
            $transfer = null;
        } else {
            $transfer = BankTransfer::where('order_id', $request->order_id)->where('user_id', $user->id)->latest()->first();
        }

        if (!$transfer) {
            $transfer = new BankTransfer();
            $transfer->user_id = $user->id;
            if (($request->complete_order == 1 || $request->complete_order == '1')) { //complete order
                $transfer->transaction_id = $transaction->id;
                $transfer->order_id = $request->order_id;
            } else {
                $transfer->order_id = $request->order_id;
            }
        }
        $transfer->bank_id = $request->bank_id ?: 4;
        $transfer->money_transfered = $request->money_transfered;
        $transfer->account_name = $request->account_name;
        $transfer->account_number = $request->account_number;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'transfer-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $transfer->photo = $fileName;
        }
        $transfer->save();
        if (($request->complete_order == 1 || $request->complete_order == '1')) { //complete order
            $transaction->transfer_id = $transfer->id;
            $transaction->save();
            $new_balance = Balance::where('user_id', $user->id)->sum('price');
            SendNotification::newOrder($order->id);
            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'cart_count' => 0,
                'new_balance' => $new_balance,
                'message' => 'تم ارسال الطلب بنجاح',
            ]);
        }
        /* if ($request->order_id) {
        $order = Orders::find($request->order_id);
        if ($order) {
        $order->payment_method = 4;
        $order->save();
        return \response()->json([
        'status' => 200,
        'order_id' => $order->id,
        'message' => 'تم ارسال الطلب بنجاح'
        ]);
        }
        }*/

        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'users.username as shop_name',
            'users.shipment_price',
            'users.taxes',
            'users.shipment_days'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {
            $order = Orders::find($request->order_id);
            $order->payment_method = 4;
            $order->save();
            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.calculated', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.calculated', 1)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();

                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $user->id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;

                $shipment->status = 1;
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        $cart_item->status = 1;
                        $cart_item->save();
                        //                        $product = Products::find($item->item_id);
                        //                        $product->quantity = $product->quantity - $item->quantity;
                        //                        $product->save();
                    }
                }

                //                $notification55 = new Notification();
                //                $notification55->sender_id = $user->id;
                //                $notification55->reciever_id = $object->shop_id;
                //                $notification55->ads_id = $shipment->id;
                //                $notification55->type = 13;
                //                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                //                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                //                $notification55->message_en = @$user->username . " bought products from your shop.";
                //                $notification55->save();
            }
            if ($request->is_schedul == 1) {
                $order->is_schedul = 1;
                $order->scheduling_date = $request->scheduling_date;
            }
            $order->payment_method = 4;
            $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
            $order->save();

            if ($request->with_balance == 1 || $request->with_balance == '1') {
                $get_balance = $this->withBalance($request, $user, $order, true);
                if ($get_balance['status'] == 400) {
                    return \response()->json($get_balance);
                } else {
                    $balance = Balance::where(['order_id' => $order->id, 'user_id' => $user->id])->first();
                    if (!$balance) {
                        $balance = new Balance();
                    }
                    $balance->user_id = $user->id;
                    $balance->price = -$get_balance['payed_balance'];
                    $balance->balance_type_id = 12;
                    $balance->status = 1;
                    $balance->order_id = $order->id;
                    $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
                    $balance->method_name = 'api-sendBankTransferOrder';
                    $balance->save();
                    $order->with_balance = 1;
                    $order->save();
                }
            }
            $new_balance = Balance::where('user_id', $user->id)->sum('price');
            SendNotification::newOrder($order->id);
            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'cart_count' => 0,
                'new_balance' => $new_balance,
                'message' => 'تم ارسال الطلب بنجاح',
            ]);
        } else {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة',
            ]);
        }
    }

    public function complete_order($request)
    {
        $order = Orders::find($request->order_id);
        $final_price = $order->final_price;
        $remaining_money = 0;
        $payed_money = 0;
        if ($order->balance != null) {
            $remaining_money = round(($final_price + $order->balance->price), 2);
            $payed_money = ($order->balance->price * -1);
        } else {
            $remaining_money = round($final_price, 2);
        }
        if ($remaining_money == 0 && $order->payment_method != 5) {
            return [
                'status' => 400,
                'message' => 'لا يوجد طلب',
            ];
        }

        return [
            'status' => 200,
            'message' => '',
            'remaining_money' => $remaining_money,
            'payed_money' => $payed_money,
        ];
    }

    public function sendPayLaterOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = Orders::find($request->order_id);
        if (($request->complete_order == 1 || $request->complete_order == '1')) {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يمكنك الدفع لاحقا',
            ]);
        }
        $get_balance = $this->withBalance($request, $user, $order);
        if ($get_balance['status'] == 400) {
            return \response()->json($get_balance);
        }
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'users.username as shop_name',
            'users.shipment_price',
            'users.taxes',
            'users.shipment_days'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {
            $order = Orders::find($request->order_id);

            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.calculated', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.calculated', 1)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();

                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $user->id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;

                $shipment->status = 1;
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        $cart_item->status = 1;
                        $cart_item->save();
                        //                        $product = Products::find($item->item_id);
                        //                        $product->quantity = $product->quantity - $item->quantity;
                        //                        $product->save();
                    }
                }

                //                $notification55 = new Notification();
                //                $notification55->sender_id = $user->id;
                //                $notification55->reciever_id = $object->shop_id;
                //                $notification55->ads_id = $shipment->id;
                //                $notification55->type = 13;
                //                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                //                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                //                $notification55->message_en = @$user->username . " bought products from your shop.";
                //                $notification55->save();
            }
            $order->payment_method = 5;
            $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
            if ($request->is_schedul == 1) {
                $order->is_schedul = 1;
                $order->scheduling_date = $request->scheduling_date;
            }
            $order->save();
            if ($request->with_balance == 1 || $request->with_balance == '1') {
                $get_balance = $this->withBalance($request, $user, $order, true);
                // Log::alert($get_balance);
                if ($get_balance['status'] == 400) {
                    return \response()->json($get_balance);
                } else {
                    if ($get_balance['get_balance'] >= $get_balance['total']) {
                        $price = $get_balance['total'];
                    } else {
                        $price = $get_balance['get_balance'];
                    }
                    $balance = Balance::where(['order_id' => $order->id, 'user_id' => $user->id])->first();
                    if (!$balance) {
                        $balance = new Balance();
                    }
                    $balance->user_id = $user->id;
                    $balance->price = -$price;
                    $balance->status = 1;
                    $balance->balance_type_id = 12;
                    $balance->order_id = $order->id;
                    $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
                    $balance->method_name = 'api-sendPayLaterOrder';
                    $balance->save();
                    $order->with_balance = 1;
                    $order->save();
                }
            }
            $new_balance = Balance::where('user_id', $user->id)->sum('price');
            SendNotification::newOrder($order->id);
            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'balance' => $new_balance,
                'message' => 'تم ارسال الطلب بنجاح',
            ]);
        } else {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة',
            ]);
        }
    }

    public function sendSchedulingOrder(Request $request)
    {
        // Log::alert($request->all());
        $user = JWTAuth::parseToken()->authenticate();
        if (($request->complete_order == 1 || $request->complete_order == '1')) {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يمكنك الدفع لاحقا',
            ]);

            return $this->complete_order($request);
        }
        if ($request->scheduling_date == null) {
            return \response()->json([
                'status' => 400,
                'message' => 'قم باضافة تاريخ الدفع',
            ]);
        }
        $order = Orders::find($request->order_id);
        $get_balance = $this->withBalance($request, $user, $order);
        if ($get_balance['status'] == 400) {
            return \response()->json($get_balance);
        }
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'users.username as shop_name',
            'users.shipment_price',
            'users.taxes',
            'users.shipment_days'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {
            $order = Orders::find($request->order_id);

            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.calculated', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.calculated', 1)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();

                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $user->id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;

                $shipment->status = 1;
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        $cart_item->status = 1;
                        $cart_item->save();
                        //                        $product = Products::find($item->item_id);
                        //                        $product->quantity = $product->quantity - $item->quantity;
                        //                        $product->save();
                    }
                }

                //                $notification55 = new Notification();
                //                $notification55->sender_id = $user->id;
                //                $notification55->reciever_id = $object->shop_id;
                //                $notification55->ads_id = $shipment->id;
                //                $notification55->type = 13;
                //                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                //                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                //                $notification55->message_en = @$user->username . " bought products from your shop.";
                //                $notification55->save();
            }
            $order->payment_method = 7;
            $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
            $order->scheduling_date = $request->scheduling_date;
            $order->save();
            if ($request->with_balance == 1 || $request->with_balance == '1') {
                $get_balance = $this->withBalance($request, $user, $order, true);
                // Log::alert($get_balance);
                if ($get_balance['status'] == 400) {
                    return \response()->json($get_balance);
                } else {
                    if ($get_balance['get_balance'] >= $get_balance['total']) {
                        $price = $get_balance['total'];
                    } else {
                        $price = $get_balance['get_balance'];
                    }
                    $balance = Balance::where(['order_id' => $order->id, 'user_id' => $user->id])->first();
                    if (!$balance) {
                        $balance = new Balance();
                    }
                    $balance->user_id = $user->id;
                    $balance->price = -$price;
                    $balance->balance_type_id = 12;
                    $balance->status = 1;
                    $balance->order_id = $order->id;
                    $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
                    $balance->method_name = 'api-sendSchedulingOrder';
                    $balance->save();
                    $order->with_balance = 1;
                    $order->save();
                }
            }
            SendNotification::newOrder($order->id);
            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'message' => 'تم ارسال الطلب بنجاح',
            ]);
        } else {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة',
            ]);
        }
    }

    public function sendBalanceOrderr(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'users.username as shop_name',
            'users.shipment_price',
            'users.taxes',
            'users.shipment_days'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {

            $balance = Balance::where('user_id', $user->id)->sum('price');
            $order = Orders::find($request->order_id);

            if (!$order) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'لا يوجد طلب بهذا الرقم',
                ]);
            }
            if ($balance < $order->final_price) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'الرصيد غير كافى',
                ]);
            }
            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.calculated', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.calculated', 1)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();

                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $user->id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;

                $shipment->status = 1;
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        $cart_item->status = 1;
                        $cart_item->save();
                        //                        $product = Products::find($item->item_id);
                        //                        $product->quantity = $product->quantity - $item->quantity;
                        //                        $product->save();

                    }
                }

                //                $notification55 = new Notification();
                //                $notification55->sender_id = $user->id;
                //                $notification55->reciever_id = $object->shop_id;
                //                $notification55->ads_id = $shipment->id;
                //                $notification55->type = 13;
                //                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                //                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                //                $notification55->message_en = @$user->username . " bought products from your shop.";
                //                $notification55->save();

            }
            if ($request->is_schedul == 1) {
                $order->is_schedul = 1;
                $order->scheduling_date = $request->scheduling_date;
            }
            $order->payment_method = 3;
            $order->save();
            $balance = new Balance();
            $balance->user_id = $user->id;
            $balance->price = $order->final_price * -1;
            $balance->status = 1;
            $balance->balance_type_id = 3;
            $balance->order_id = $order->id;
            $balance->notes = 'شراء من السله لطلب رقم ' . $order->id;
            $balance->save();
            $new_balance = Balance::where('user_id', $user->id)->sum('price');

            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'balance' => $new_balance,
                'message' => 'تم ارسال الطلب بنجاح',
            ]);
        } else {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة',
            ]);
        }
    }

    public function sendHandOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'users.username as shop_name',
            'users.shipment_price',
            'users.taxes',
            'users.shipment_days'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {
            $order = Orders::find($request->order_id);

            foreach ($objects as $object) {
                $cart_items = [];
                $pricing_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'pricing_order_parts.part_name as title', 'cart_items.shop_id')
                    ->where('cart_items.type', 2)
                    ->where('cart_items.order_id', 0)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN pricing_order_parts.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", pricing_order_parts.photo)) END) AS photo')
                    ->join('pricing_offers', 'cart_items.item_id', 'pricing_offers.id')
                    ->join('pricing_order_parts', 'pricing_order_parts.id', 'pricing_offers.part_id')->get();

                $shop_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();

                $cart_items = $shop_items->merge($pricing_items);
                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $user->id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;

                $shipment->status = 1;
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        $cart_item->status = 1;
                        $cart_item->save();
                    }
                }
                foreach ($shop_items as $shop_item) {
                    $product = Products::find($shop_item->item_id);
                    $product->quantity = $product->quantity - $shop_item->quantity;
                    $product->save();
                }
                $notification55 = new Notification();
                $notification55->sender_id = $user->id;
                $notification55->reciever_id = $object->shop_id;
                $notification55->ads_id = $shipment->id;
                $notification55->type = 13;
                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                $notification55->message_en = @$user->username . " bought products from your shop.";
                $notification55->save();
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);

                if ($order->getUser->lang == "en") {
                    $notification_title = "new order";
                    $notification_message = $notification55->message_en;
                } else {
                    $notification_title = "طلب شراء جديد";
                    $notification_message = $notification55->message;
                }
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
                        'notification_data' => new NotificationsResource($notification55),
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
            if ($request->is_schedul == 1) {
                $order->is_schedul = 1;
                $order->scheduling_date = $request->scheduling_date;
            }
            $order->payment_method = 1;
            $order->save();
            $new_balance = Balance::where('user_id', $user->id)->sum('price');
            SendNotification::newOrder($order->id);
            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'balance' => $new_balance,
                'message' => 'تم ارسال الطلب بنجاح',
            ]);
        } else {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة',
            ]);
        }
    }

    public function myOrders()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_status = App::getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';

        $objects = Orders::select('orders.id', 'orders.final_price', 'orders.marketed_date', 'orders.status', $select_status, 'order_status.color', 'orders.payment_method', 'payment_methods.name as payment_method_name')
            ->selectRaw('(CONCAT ("' . url('/') . '/i/", orders.short_code)) as download_url')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.order_id =orders.id) as products_count')
            ->leftJoin('order_status', 'order_status.id', 'orders.status')
            ->join('payment_methods', 'orders.payment_method', 'payment_methods.id')
            ->where('user_id', $user->id)->where('orders.payment_method', '<>', 0)
            ->with('transfer_photo.to_bank', 'balance', 'transaction')
            ->orderBy('orders.marketed_date', 'desc')
            ->orderBy('orders.created_at', 'desc')
            ->paginate(15);
        $objects->{'objects'} = MyOrdersResources::collection($objects);
        $select_title = App::getLocale() == "ar" ? 'name' : 'name_en as name';

        $banks = Banks::select('id', $select_title)->get();

        $select_bank_name = App::getLocale() == "ar" ? 'bank_name' : 'bank_name_en as name';
        $bank_accounts = BankAccounts::select('id', $select_bank_name, 'account_name', 'account_number', 'account_ipan')->get();
        $balance = Balance::where('user_id', $user->id)->sum('price');
        $payment_atcive = Settings::find(34)->value;
        return \response()->json($objects);

        return \response()->json([
            'status' => 200,
            'my-orders' => $objects,
            'balance' => $balance,
            'banks' => $banks,
            'bank_accounts' => $bank_accounts,
            'online_payment_active' => (int)Settings::find(43)->value,
            'pay_later' => (int)Settings::find(44)->value,
            'pay_balance' => (int)Settings::find(45)->value,
            'payment_active' => (int)$payment_atcive,
        ]);
    }

    public function myOrdersNew()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $select_status = App::getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';

        $objects = Orders::select('orders.id', 'orders.final_price', 'orders.marketed_date', 'orders.is_edit as has_second_order', 'orders.parent_order as has_parent_order', 'orders.status', $select_status, 'order_status.color', 'orders.payment_method', 'payment_methods.name as payment_method_name')
            ->selectRaw('(CONCAT ("' . url('/') . '/i/", orders.short_code)) as download_url')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.order_id =orders.id) as products_count')
            ->leftJoin('order_status', 'order_status.id', 'orders.status')
            ->join('payment_methods', 'orders.payment_method', 'payment_methods.id')
            ->where('user_id', $user->id)->where('orders.payment_method', '<>', 0)
            ->with('transfer_photo.to_bank', 'balance', 'transaction', 'transferParentPhoto.to_bank')
            ->orderBy('orders.marketed_date', 'desc')
            ->orderBy('orders.created_at', 'desc')
            ->paginate(15);
        $objects->{'objects'} = MyOrdersResources::collection($objects);
        $select_title = App::getLocale() == "ar" ? 'name' : 'name_en as name';

        $banks = Banks::select('id', $select_title)->get();

        $select_bank_name = App::getLocale() == "ar" ? 'bank_name' : 'bank_name_en as name';
        $bank_accounts = BankAccounts::select('id', $select_bank_name, 'account_name', 'account_number', 'account_ipan')->get();
        $balance = Balance::where('user_id', $user->id)->sum('price');
        $payment_atcive = Settings::find(34)->value;

        return \response()->json([
            'status' => 200,
            'my-orders' => $objects,
            'balance' => $balance,
            'banks' => $banks,
            'bank_accounts' => $bank_accounts,
            'online_payment_active' => (int)Settings::find(43)->value,
            'pay_later' => (int)Settings::find(44)->value,
            'pay_balance' => (int)Settings::find(45)->value,
            'payment_active' => (int)$payment_atcive,
            'tap_payment' => (int)Settings::find(49)->value,
        ]);
    }

    public function orderTracking(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_like = $user->id;
        $select_status = App::getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';

        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';
        $select_supplier_name = App::getLocale() == "ar" ? 'supplier_data.supplier_name' : 'supplier_data.supplier_name_en as supplier_name';

        $shipments = OrderShipments::select(
            'order_shipments.id',
            'order_shipments.user_id',
            'order_shipments.shop_id',
            'order_shipments.status',
            'order_shipments.order_id',
            'order_shipments.created_at',
            'cart_items.type'
        )
            ->with(['cart_items' => function ($query) use ($select_title, $select_measurement) {

                $query->select(
                    'cart_items.id',
                    'cart_items.shipment_id',
                    'cart_items.status',
                    'cart_items.item_id',
                    'cart_items.type',
                    'cart_items.user_id',
                    'cart_items.price',
                    'cart_items.quantity',
                    'products.' . $select_title,
                    'cart_items.shop_id',
                    $select_measurement
                )
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')
                    ->join('measurement_units', 'measurement_units.id', 'products.measurement_id');
            }, 'shop' => function ($query) use ($select_supplier_name) {
                $query->select($select_supplier_name, 'users.id', 'users.longitude', 'users.latitude')
                    ->join('supplier_data', 'supplier_data.user_id', 'users.id')
                    ->selectRaw('(CASE WHEN supplier_data.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", supplier_data.photo)) END) AS photo');
            }])
            ->join('cart_items', 'cart_items.shipment_id', 'order_shipments.id')
            ->where('order_shipments.user_id', $user->id)
            ->where('order_shipments.order_id', $request->order_id)
            ->groupBy('order_shipments.id')
            ->orderBy('id', 'desc')
            ->paginate(15);
        $order = Orders::find($request->order_id);
        $banks = [];
        $bank_accounts = [];
        if ($order && $order->payment_method == 5) {
            $select_name = App::getLocale() == "ar" ? 'name' : 'name_en as name';

            $banks = Banks::select('id', $select_name)->get();

            $select_bank_name = App::getLocale() == "ar" ? 'bank_name' : 'bank_name_en as name';
            $bank_accounts = BankAccounts::select('id', $select_bank_name, 'account_name', 'account_number', 'account_ipan')->get();
        }
        if ((int)PaymentSettings::find(1)->value === 1 && !empty($order->user->phone) && !empty($order->user->email)) {
            $data = [
                'order_id' => $order->id,
            ];

            $tmara = new TmaraRepository();
            $tmara = $tmara->getPaymentTypes($data);
        }

        return \response()->json([
            'data' => $shipments,
            'bank_accounts' => $bank_accounts,
            'banks' => $banks,
            'show_tmara' => isset($tmara) && $tmara ? 1 : 0,
        ]);
    }

    public function cancelOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = Orders::where('id', $request->order_id)->where('user_id', $user->id)->where('payment_method', 5)->where('status', 0)->first();
        if (!$order) {
            return \response()->json([
                'status' => 400,
                'message' => 'الطلب غير موجود',
            ]);
        };
        $order->status = 5;
        $order->save();
        OrderShipments::where('order_id', $order->id)->update(['status' => 5]);
        if ($order->balance != null) {
            $balance = new Balance();
            $balance->user_id = $user->id;
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

        return \response()->json([
            'status' => 200,
            'message' => 'تم إلغاء الطلب',
        ]);
    }

    public function rate_product(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cart_item = CartItem::where('item_id', $request->product_id)->where('type', $request->type)->where('order_id', '<>', 0)
            ->where('user_id', $user->id)->where('status', 4)->first();
        if (!$cart_item) {
            return response()->json(['message' => 'do not get this product yet '], 400);
        }
        $rate = new ProductRating();
        $rate->rate = $request->rate ?: 1;
        $rate->comment = $request->comment ?: '';
        $rate->item_id = $request->product_id;
        $rate->type = $request->type;
        $rate->user_id = $user->id;
        $rate->save();
        return response()->json(
            [
                'status' => 200,
                'message' => 'تم التقييم بنجاح',
            ]
        );
    }

    public function sendBalanceOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (($request->complete_order == 1 || $request->complete_order == '1')) {
            $order = Orders::find($request->order_id);
            $remining_details = $this->complete_order($request);
            $balance = Balance::where('user_id', $user->id)->sum('price');
            if ($remining_details['status'] == 400) {
                return \response()->json($remining_details);
            }
            if ($balance < $remining_details['remaining_money']) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'الرصيد غير كافى',
                ]);
            }
            $transaction = new Transaction();
            $transaction->order_id = $order->id;
            $transaction->payment_method = 3;
            $transaction->price = $remining_details['remaining_money'];
            $transaction->payed_price = $remining_details['payed_money'];
            $transaction->status = 1;
            $transaction->save();
            $order->save();

            $balance = new Balance();
            $balance->user_id = $user->id;
            $balance->price = $remining_details['remaining_money'] * -1;
            $balance->balance_type_id = 13;
            $balance->status = 1;
            $balance->order_id = $order->id;
            $balance->notes = ' استكمال الدفع علي طلب رقم ' . $order->id;
            $balance->save();
            $new_balance = Balance::where('user_id', $user->id)->sum('price');

            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'balance' => $new_balance,
                'message' => 'تم ارسال الطلب بنجاح',
            ]);
        }
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select('cart_items.shop_id', 'cart_items.user_id', 'cart_items.type', 'users.username as shop_name', 'users.shipment_price', 'users.taxes')
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {
            $balance = Balance::where('user_id', $user->id)->sum('price');
            $order = Orders::find($request->order_id);

            if (!$order) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'لا يوجد طلب بهذا الرقم',
                ]);
            }

            if ($balance < $order->final_price) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'الرصيد غير كافى',
                ]);
            }
            foreach ($objects as $object) {
                $cart_items = CartItem::select('cart_items.id', 'cart_items.calculated', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.calculated', 1)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();
                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $user->id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;

                $shipment->status = 1;
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        $cart_item->status = 1;
                        $cart_item->save();
                        //                        $product = Products::find($item->item_id);
                        //                        $product->quantity = $product->quantity - $item->quantity;
                        //                        $product->save();

                    }
                }
                //
                //                $notification55 = new Notification();
                //                $notification55->sender_id = $user->id;
                //                $notification55->reciever_id = $object->shop_id;
                //                $notification55->ads_id = $shipment->id;
                //                $notification55->type = 13;
                //                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                //                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                //                $notification55->message_en = @$user->username . " bought products from your shop.";
                //                $notification55->save();
                //                $optionBuilder = new OptionsBuilder();
                //                $optionBuilder->setTimeToLive(60 * 20);
                //
                //                if ($order->getUser->lang == "en") {
                //                    $notification_title = "new order";
                //                    $notification_message = $notification55->message_en;
                //                } else {
                //                    $notification_title = "طلب شراء جديد";
                //                    $notification_message = $notification55->message;
                //                }
                //                $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                //                $notificationBuilder->setBody($notification_message)
                //                    ->setSound('default');
                //                $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
                //
                //
                //                $dataBuilder = new PayloadDataBuilder();
                //                $dataBuilder->addData(['data' => [
                //                    'notification_type' => (int)$notification55->type,
                //                    'notification_title' => $notification_title,
                //                    'notification_message' => $notification_message,
                //                    'notification_data' => new NotificationsResource($notification55)
                //                ]
                //                ]);
                //
                //                $option = $optionBuilder->build();
                //                $notification = $notificationBuilder->build();
                //                $data = $dataBuilder->build();
                //
                //
                //                $token = @$notification55->getReciever->devices->count();
                //                $tokens = DeviceTokens::where('user_id', $notification55->reciever_id)->pluck('device_token')->toArray();
                //                $notification_ = @$notification55->getReciever->notification;
                //
                //                if ($token > 0 && $notification_) {
                //                    $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                //                    $downstreamResponse->numberSuccess();
                //                    $downstreamResponse->numberFailure();
                //                    $downstreamResponse->numberModification();
                //                }
                //
                //            }
            }
            $order->payment_method = 3;
            $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');

            $order->save();

            $balance = new Balance();
            $balance->user_id = $user->id;
            $balance->price = $order->final_price * -1;
            $balance->balance_type_id = 3;
            $balance->status = 1;
            $balance->order_id = $order->id;
            $balance->notes = 'شراء من السله لطلب رقم ' . $order->id;
            $balance->save();
            $new_balance = Balance::where('user_id', $user->id)->sum('price');

            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'balance' => $new_balance,
                'message' => 'تم ارسال الطلب بنجاح',
            ]);
        } else {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة',
            ]);
        }
    }

    public function damageSendBalanceOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = DamageEstimate::where('id', $request->order_id)->where('user_id', $user->id)->where('payment_method', 0)->first();
        $balance = Balance::where('user_id', $user->id)->sum('price');

        if (!$order) {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد طلب بهذا الرقم',
            ]);
        }
        $pricing_fees = Settings::find(20)->value;
        if ($balance < $pricing_fees) {
            return \response()->json([
                'status' => 400,
                'message' => 'الرصيد غير كافى',
            ]);
        }
        $order->payment_method = 3;
        $order->save();

        $balance = new Balance();
        $balance->user_id = $user->id;
        $balance->price = $pricing_fees * -1;
        $balance->balance_type_id = 3;
        $balance->order_id = $order->id;
        $balance->notes = 'طلب تقدير اضرار ' . $order->id;
        $balance->save();
        $new_balance = Balance::where('user_id', $user->id)->sum('price');
        $select_name = 'name';
        if (App::getLocale() == "en") {
            $select_name = 'name_en as name';
        }
        $shops = User::where('accept_estimate', 1)->where('is_archived', 0)
            ->where('block', 0)->get();
        $notify_message = 'قام المستخدم ' . $user->username . ' باضافة طلب خدمة جديد ';
        foreach ($shops as $shop) {
            $notify = new Notification();
            $notify->sender_id = $user->id;
            $notify->reciever_id = $shop->id;
            $notify->type = 4;
            $notify->url = '/provider-panel/damage-estimates/' . $order->id;
            $notify->message = $notify_message;
            $notify->message_en = 'new damage estimate order by ' . $user->username;
            $notify->ads_id = $order->id;
            $notify->save();
        }
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $optionBuilder->setContentAvailable(true);

        $notification_title = "طلب تقدير اضرار جديد";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notify_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');

        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'data' => [
                'notification_type' => 4,
                'notification_title' => $notification_title,
                'notification_message' => $notify_message,
                'key' => $order->id,
                'notification_data' => '{ads_id:' . $order->id . '}',
            ],
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = DeviceTokens::whereIn('user_id', function ($query) use ($shop) {
            $query->select('id')
                ->from(with(new User())->getTable())
                ->where('accept_estimate', 1)
                ->where('block', 0)
                ->where('is_archived', 0)
                ->where('notification', 1);
        })->pluck('device_token')->toArray();
        if (count($tokens)) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
        return \response()->json([
            'status' => 200,
            'order_id' => $order->id,
            'balance' => $new_balance,
            'message' => 'تم ارسال الطلب بنجاح',
        ]);
    }

    public function pricingSendBalanceOrder(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = PricingOrder::where('id', $request->order_id)->where('user_id', $user->id)->where('payment_method', 0)->first();
        $balance = Balance::where('user_id', $user->id)->sum('price');

        if (!$order) {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد طلب بهذا الرقم',
            ]);
        }
        $pricing_fees = Settings::find(21)->value;
        if ($balance < $pricing_fees) {
            return \response()->json([
                'status' => 400,
                'message' => 'الرصيد غير كافى',
            ]);
        }
        $order->payment_method = 3;
        $order->save();

        $balance = new Balance();
        $balance->user_id = $user->id;
        $balance->price = $pricing_fees * -1;
        $balance->balance_type_id = 3;
        $balance->order_id = $order->id;
        $balance->notes = 'طلب تسعير رقم ' . $order->id;
        $balance->save();
        $select_name = 'name';
        if (App::getLocale() == "en") {
            $select_name = 'name_en as name';
        }
        $shops = User::where('accept_pricing', 1)->where('is_archived', 0)
            ->where('block', 0)->get();
        $notify_message = 'قام المستخدم ' . $user->username . ' باضافة طلب جديد لتسعير قطع الغيار';
        foreach ($shops as $shop) {
            $notify = new Notification();
            $notify->sender_id = $user->id;
            $notify->reciever_id = $shop->id;
            $notify->type = 3;
            $notify->url = '/provider-panel/pricing-orders/' . $order->id;
            $notify->message = $notify_message;
            $notify->message_en = 'new parts pricing order by ' . $user->username;
            $notify->ads_id = $order->id;
            $notify->save();
        }
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $optionBuilder->setContentAvailable(true);

        $notification_title = "طلب تسعير جديد";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notify_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');

        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData([
            'data' => [
                'notification_type' => 3,
                'notification_title' => $notification_title,
                'notification_message' => $notify_message,
                'key' => $order->id,
                'notification_data' => '{ads_id:' . $order->id . '}',
            ],
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = DeviceTokens::whereIn('user_id', function ($query) {
            $query->select('id')
                ->from(with(new User())->getTable())
                ->where('accept_pricing', 1)
                ->where('block', 0)
                ->where('is_archived', 0)
                ->where('notification', 1);
        })->pluck('device_token')->toArray();
        if (count($tokens)) {

            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
        $new_balance = Balance::where('user_id', $user->id)->sum('price');

        return \response()->json([
            'status' => 200,
            'order_id' => $order->id,
            'balance' => $new_balance,
            'message' => 'تم ارسال الطلب بنجاح',
        ]);
    }

    public function deleteAccount()
    {
        if (request('device_token')) {
            DeviceTokens::where('device_token', request('device_token'))->delete();
        }

        $user1 = JWTAuth::parseToken()->authenticate();
        $user = User::find($user1->id);
        $user->block = 1;
        $user->save();

        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => __('messages.logged_out')], 200);
    }

    public function testEditOrder($id)
    {
        $order = Orders::where('id', $id)->first();
        dd($order);
        $cobon = 0;
        if ($order->cobon_discount > 0) {
            $code = Cobons::where('code', $order->cobon)->first();
            $discount_prices = CartItem::select(
                'cart_items.id',
                'cart_items.price',
                'cart_items.quantity',
                'cart_items.item_id',
                'products.category_id',
                \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as total')
            )
                ->join('products', 'products.id', 'cart_items.item_id')
                ->where(function ($q) use ($code) {
                    if ($code->link_type == 'category') {
                        $q->whereIn('products.category_id', function ($query) use ($code) {
                            $query->select('category_id')
                                ->from(with(new CobonsCategories())->getTable())
                                ->where('cobon_id', $code->id);
                        });
                    } else {
                        $q->whereIn('products.provider_id', function ($query) use ($code) {
                            $query->select('user_id')
                                ->from(with(new CobonsProviders())->getTable())
                                ->where('cobon_id', $code->id);
                        });
                    }
                })
                ->where('cart_items.order_id', $order->id)
                ->where('type', 1)
                ->first();
            $total1 = $discount_prices ? $discount_prices->total : 0;
            $percent = $code->percent;
            $final_percent_price = ($total1 * $percent) / 100; // الخصم بالنسبه
            $final_money_price = $code->max_money; //اعلي مبلغ خصم
            if ($final_percent_price >= $final_money_price && $code->max_money != 0) {
                $final_cobon_money = $final_money_price;
            } else {
                $final_cobon_money = $final_percent_price;
            }

            if ($final_cobon_money == 0) {
                $order->cobon_discount = 0;
                $order->save();
            } else {
                $order->cobon_discount = $final_cobon_money;
                $order->save();
                $cobon = $final_cobon_money;
            }
        }
        $total = CartItem::where('order_id', $order->id)->where('type', 1)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $shipment_price = Settings::find(22)->value;
        $taxs = Settings::find(38)->value;
        // $cobon = $order->cobon_discount;
        $order->final_price = ($total + $shipment_price - $cobon) + (($total + $shipment_price - $cobon) * $taxs / 100);
        $order->order_price = $total;
        $order->delivery_price = $shipment_price;
        $order->taxes = (($total + $shipment_price - $cobon) * $taxs / 100);
        $order->save();
    }


    public function testSelectDriverOrder($id)
    {
        $order = Orders::where('id', $id)->first();
        $order->newOrderDriverNotification();
    }

    public function testSelectDriverPurchases($id)
    {
        $order = Purchase_order::where('id', $id)->first();
        $order->newOrderDriverNotification();
    }

    public function trackOrder(Request $request)
    {
        $select_status = App::getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';
        $order = Orders::where('orders.id', $request->order_id)
            ->select('orders.id', 'users.username', $select_status, 'payment_methods.name as payment_type', 'orders.final_price')
            ->selectRaw('(CONCAT ("' . url('/') . '/i/", orders.short_code)) as download_url')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.order_id =orders.id) as products_count')
            ->leftJoin('order_status', 'order_status.id', 'orders.status')
            ->leftJoin('users', 'users.id', 'orders.user_id')
            ->join('payment_methods', 'orders.payment_method', 'payment_methods.id')
            ->where('orders.payment_method', '<>', 0)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'لا يوجد طلب بهذا الرقم'], 400);
        }
        return response()->json($order);
    }

    public function approveOrderTmara($id)
    {
        $user = User::find(1788);
        $saved = OrderRepository::saveOrder($id, $user, 8);
    }

    public function updateOldCart()
    {
        $carts = CartItem::with('product')->where('order_id', 0)->get();
        foreach ($carts as $cart) {
            $product = Products::find($cart->item_id);
            if ($product && $cart->price != $product->price) {
                Log::info([$cart->price, $product->price, $product->id]);
                $cart->update(['price' => $product->price]);
                // dd([$cart->price, $product->price,$product->id]);
            }
        }
    }
}
