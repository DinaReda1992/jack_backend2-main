<?php

namespace App\Models;

use LaravelFCM\Facades\FCM;
use App\Models\BankTransfer;
use Illuminate\Support\Facades\DB;
use App\Helpers\SendFcmNotification;
use LaravelFCM\Message\OptionsBuilder;
use App\Http\Resources\NewCartResource;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\MyOrdersResources;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use MFrouh\ScopeStatistics\Traits\ScopeStatistics;

class Orders extends Model
{
    use ScopeStatistics;

    protected $table = 'orders';
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at', 'edit_date'];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function added()
    {
        return $this->belongsTo('App\Models\User', 'added_by', 'id');
    }
    public function accepted()
    {
        return $this->belongsTo('App\Models\User', 'accepted_by', 'id');
    }
    public function reviewd()
    {
        return $this->belongsTo('App\Models\User', 'reviewd_by', 'id');
    }
    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function getDetails()
    {
        return $this->hasMany('App\Models\OrdersDetails', 'order_id', 'id');
    }
    public function cart_items()
    {
        return $this->hasMany(CartItem::class, 'order_id', 'id');
    }
    public function transfer_photo()
    {
        return $this->hasOne(BankTransfer::class, 'order_id', 'id')->latest();
    }

    public function transferParentPhoto()
    {
        return $this->hasOne(BankTransfer::class, 'order_id', 'has_parent_order')->latest();
    }

    public function parentOrder()
    {
        return $this->belongsTo(Orders::class, 'has_parent_order', 'id');
    }

    public function shipment()
    {
        return $this->hasOne('App\Models\OrderShipments', 'order_id');
    }
    public function shipments()
    {
        return $this->hasMany('App\Models\OrderShipments', 'order_id');
    }
    public function orderStatus()
    {
        return $this->belongsTo('App\Models\OrdersStatus', 'status', 'id');
    }

    public function address()
    {
        return $this->belongsTo('App\Models\Addresses', 'address_id', 'id');
    }
    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethods', 'payment_method', 'id');
    }
    public function cartPaymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethods', 'cart_payment_method', 'id');
    }
    public function country()
    {
        return $this->belongsTo(Categories::class, 'country_id', 'id');
    }
    public function region()
    {
        return $this->belongsTo(Regions::class, 'region_id', 'id');
    }
    public function state()
    {
        return $this->belongsTo(States::class, 'state_id', 'id');
    }
    public function balance()
    {
        return $this->hasOne(Balance::class, 'order_id', 'id')->withoutGlobalScopes();
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id', 'id')->where('status', 1);
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(User::class, 'warehouse_id', 'id');
    }

    public function orderDetails()
    {
        $code = Cobons::where('code', $this->cobon)->first();
        $cart_items = CartItem::with('product')
            ->where('order_id', $this->id)
            ->where('quantity_difference', '!=', 0)
            ->select(\Illuminate\Support\Facades\DB::raw('*,(price * quantity_difference) as total'))
            ->get();
        $final_cobon_money = 0;
        if ($code) {
            $discount_prices = CartItem::select(
                'cart_items.id',
                'cart_items.price',
                'cart_items.quantity',
                'cart_items.item_id',
                'products.category_id',
                \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity_difference) as total')
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
                ->where('cart_items.order_id', $this->id)
                ->where('cart_items.quantity_difference', '!=', 0)
                ->where('cart_items.type', 1)
                ->first();
            $total = $discount_prices ? $discount_prices->total : 0;
            $percent = $code->percent;
            $final_percent_price = ($total * $percent) / 100; // الخصم بالنسبه
            $final_money_price = $code->max_money; //اعلي مبلغ خصم
            if ($final_percent_price >= $final_money_price && $code->max_money != 0) {
                $final_cobon_money = $final_money_price;
            } else {
                $final_cobon_money = $final_percent_price;
            }
        }
        $cobon = $final_cobon_money;
        $taxes = (int) Settings::find(38)->value;
        $total_taxes = (($cart_items->sum('total') - $cobon) * $taxes) / 100;
        $total = $cart_items->sum('total');
        $final_price = ($cart_items->sum('total') - $cobon) + (($cart_items->sum('total') - $cobon) * $taxes) / 100;

        $select_name = app()->getLocale() == "ar" ? 'name' : 'name_en';
        $address = DB::table('addresses')->select(
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
            ->where('addresses.id', $this->address_id)
            ->first();

        $select_measurement = app()->getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';
        $cart_items = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.order_id',
            'cart_items.type',
            'products.title',
            'users.username as shop_name',
            'users.shipment_price',
            $select_measurement
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->join('products', 'products.id', 'cart_items.item_id')
            ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')
            ->where('cart_items.order_id', $this->id)
            ->where('cart_items.quantity_difference', '!=', 0)
            ->select(\Illuminate\Support\Facades\DB::raw('*,(cart_items.price * cart_items.quantity_difference) as total'))
            ->groupBy('cart_items.shop_id')
            ->get();

        return [
            'address' => $address,
            'delivery_price' => 0,
            'total_taxes' => $total_taxes,
            'total' => $total,
            'final_price' => $final_price,
            'cobon' => $cobon,
            'data' => NewCartResource::collection($cart_items),
        ];
    }

    public function newOrderOnOld()
    {
        $newOrder = Orders::Create([
            'taxes' => $this->orderDetails()['total_taxes'], 'final_price' => $this->orderDetails()['final_price'],
            'order_price' => $this->orderDetails()['total'], 'cobon_discount' => $this->orderDetails()['cobon'],
            'cobon' => $this->cobon, 'shop_id' => $this->shop_id,
            'address_id' => $this->address_id, 'user_id' => $this->user_id, 'delivery_price' => 0,
            'payment_method' => $this->payment_method, 'status' => 1, 'parent_order' => $this->id,
            'platform' => $this->platform, 'cart_payment_method' => $this->cart_payment_method, 'longitude' => $this->longitude, 'latitude' => $this->latitude, 'address_name' => $this->address_name, 'address_desc' => $this->address_desc, 'country_id' => $this->country_id, 'region_id' => $this->region_id, 'state_id' => $this->state_id, 'financial_date' => now()
        ]);

        $newOrder->update(['short_code' => $newOrder->id . str_random(4)]);
        $cart_items = CartItem::where('order_id', $this->id)
            ->where('quantity_difference', '!=', 0)
            ->select(\Illuminate\Support\Facades\DB::raw('*,(price * quantity_difference) as total'))
            ->get();
        foreach ($cart_items as $item) {
            CartItem::create([
                'user_id' => $item->user_id, 'shop_id' => $item->shop_id, 'item_id' => $item->item_id, 'price' => $item->price,
                'type' => 1, 'status' => 1, 'order_id' => $newOrder->id, 'quantity' => $item->quantity_difference, 'shipment_id' => 0
            ]);
        }
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
            ->where('cart_items.order_id', $newOrder->id)
            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $newOrder->user_id)
            ->groupBy('users.id')->get();

        foreach ($objects as $key => $object) {
            $cart_items = CartItem::where('order_id', $newOrder->id)
                ->where('type', 1)
                ->where('shop_id', $object->shop_id)
                ->where('user_id', $object->user_id)
                ->get();
            $shipment = new OrderShipments();
            $shipment->order_id = $newOrder->id;
            $shipment->user_id = $newOrder->user_id;
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
                    $cart_item->shipment_id = $shipment->id;
                    $cart_item->status = 1;
                    $cart_item->save();
                }
            }
        }
        $this->update(['is_edit' => 0]);
        return $newOrder;
    }

    public function returnBalance()
    {
        $balance = new Balance();
        $balance->user_id = $this->user_id;
        $balance->price = $this->paid_price - $this->final_price;
        $balance->order_id = $this->id;
        $balance->balance_type_id = 15;
        $balance->status = 1;
        $balance->notes = 'باقي طلبك الناقص سيتم تحويله الي رصيدك للطلب رقم' . $this->id;
        $balance->save();
        $this->update(['is_edit' => 0]);
        $items = CartItem::where('order_id', $this->id)
            ->select('id', 'item_id', 'quantity', 'quantity_difference')
            ->where('quantity_difference', '>', 0)->get();
        foreach ($items as $item) {
            Products::where('id', $item->item_id)->increment('quantity', $item->quantity_difference);
        }
        $notification55 = new Notification();
        $notification55->sender_id = 1;
        $notification55->reciever_id = $balance->user_id;
        $notification55->type = 44;
        $notification55->message = 'باقي طلبك الناقص سيتم تحويله الي رصيدك للطلب رقم' . $this->id;
        $notification55->save();
        $notification_title = "رسالة جديدة";
        $notification_message = $notification55->message;

        if ($balance->getUser->notification == 1) {
            $this->send_fcm_notification($notification_title, $notification_message, $notification55, 44, 'default');
        }
        return true;
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

    public function getReport($fixed = 0)
    {
        return Orders::where('payment_method', '!=', 0)
            ->when($fixed == 0 && request('from') && request('to'), function ($query) {
                $query->whereBetween('created_at', [request('from') . ' ' . '00:00:00' ?: now()->subYears(4) . ' ' . '23:59:59', request('to') . ' ' . '23:59:59' ?: now() . ' ' . '23:59:59']);
            })->when($fixed == 0 && request('user_id'), function ($query) {
                $query->where('user_id', request('user_id'));
            })->when($fixed == 0 && request('region_id'), function ($query) {
                $query->where('region_id', request('region_id'));
            })->when($fixed == 0 && request('employee_id'), function ($query) {
                $query->where(function ($query) {
                    $query->where('added_by', request('employee_id'));
                });
            })->whereHas('cart_items', function ($query) use ($fixed) {
                $query->when($fixed == 0 && request('supplier_id'), function ($query) {
                    $query->where('shop_id', request('supplier_id'));
                });
            });
    }

    public function allOrders()
    {
        $data['all_new_orders'] = $this->getReport(1)->where('status', 0)->where('is_schedul', 0)->count();
        $data['all_pending_orders'] =  $this->getReport(1)->where('financial_date', '!=', null)->where('status', 1)->count();
        $data['all_preparing_orders'] =  $this->getReport(1)->where('financial_date', '!=', null)->where('status', 2)->count();
        $data['all_ready_to_ship_orders'] =  $this->getReport(1)->where('financial_date', '!=', null)->where('status', 3)->count();
        $data['all_shipped_orders'] =  $this->getReport(1)->where('financial_date', '!=', null)->where('status', 4)->count();
        $data['all_canceled_orders'] =  $this->getReport(1)->where('financial_date', '!=', null)->where('status', 5)->count();
        $data['all_delivering_orders'] =  $this->getReport(1)->where('financial_date', '!=', null)->where('status', 6)->count();
        $data['all_completed_orders'] =  $this->getReport(1)->where('financial_date', '!=', null)->where('status', 7)->count();
        $data['all_orders'] =  $this->getReport(1)->where('financial_date', '!=', null)->whereIn('status', [1, 2, 3, 4, 5, 6, 7])->count() + $data['all_new_orders'];

        return $data;
    }

    public function completedOrders()
    {
        $data['completed_orders_price'] = $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 7)->sum('final_price');
        $data['completed_orders'] =  $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 7)->count();
        return $data;
    }

    public function canceledOrders()
    {
        $data['canceled_orders_price'] = $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 5)->sum('final_price');
        $data['canceled_orders'] =  $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 5)->count();
        return $data;
    }

    public function pendingOrders()
    {
        $data['pending_orders_price'] = $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 1)->sum('final_price');
        $data['pending_orders'] =  $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 1)->count();
        return $data;
    }

    public function preparingOrders()
    {
        $data['preparing_orders_price'] = $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 2)->sum('final_price');
        $data['preparing_orders'] =  $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 2)->count();
        return $data;
    }


    public function readyToShipOrders()
    {
        $data['ready_to_ship_orders_price'] = $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 3)->sum('final_price');
        $data['ready_to_ship_orders'] =  $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 3)->count();
        return $data;
    }

    public function shippedOrders()
    {
        $data['shipped_orders_price'] = $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 4)->sum('final_price');
        $data['shipped_orders'] =  $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 4)->count();
        return $data;
    }

    public function deliveringOrders()
    {
        $data['delivering_orders_price'] = $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 6)->sum('final_price');
        $data['delivering_orders'] =  $this->getReport()->where('financial_date', '!=', null)->where('status', '=', 6)->count();
        return $data;
    }

    public function newOrders()
    {
        $data['new_orders_price'] = $this->getReport()->where('status', '=', 0)->where('is_schedul', 0)->sum('final_price');
        $data['new_orders'] = $this->getReport()->where('status', '=', 0)->where('is_schedul', 0)->count();
        return $data;
    }

    public function newOrderDriverNotification()
    {
        $notification55 = new Notification();
        $notification55->sender_id = auth()->user()->id;
        $notification55->reciever_id = $this->driver_id;
        $notification55->order_id = $this->id;
        $notification55->type = 25;
        $notification55->save();
        $notification_title = '';
        $notification_message = '';
        $notification55->message = 'لديك طلب جديد للتوصيله طلب رقم ' . $this->id;
        $notification55->message_en = 'You have new order to deliver  order number ' . $this->id;
        if ($this->user->lang == "en") {
            $notification_title = "New Order To Deliver";
            $notification_message = $notification55->message_en;
        } else {
            $notification_title = "طلب جديد للتوصيله";
            $notification_message = $notification55->message;
        }
        $notification55->save();
        SendFcmNotification::send_fcm_notification(
            $notification_title,
            $notification_message,
            $notification55,
            ['id' => $this->id, 'type' => 1],
        );
    }

    public function NewOrderNeedToPrepareNotification($order)
    {
        $users = User::where('user_type_id', 2)->where('privilege_id', 15)->where('block', 0)->get();
        foreach ($users as $key => $user) {
            $notification_for_client = new Notification();
            $notification_for_client->order_id = $this->id;
            $notification_for_client->sender_id = auth()->user()->id;
            $notification_for_client->reciever_id = $user->id;
            $notification_for_client->message = 'طلب عميل جديد يحتاج الي التجهيز رقم الطلب' . $this->id;
            $notification_for_client->message_en = 'New Client Order, Order Num #' . $this->id . ' Need to prepare ';
            $notification_for_client->type = 88;
            $notification_title = '';
            $notification_message = '';

            if ($user->lang == "en") {
                $notification_title = "New Client Order Need To Prepare";
                $notification_message = $notification_for_client->message_en;
            } else {
                $notification_title = "طلب عميل جديد للتجهيز";
                $notification_message = $notification_for_client->message;
            }
            $notification_for_client->save();
            SendFcmNotification::send_fcm_notification(
                $notification_title,
                $notification_message,
                $notification_for_client,
                new MyOrdersResources($order)
            );
        }
    }
}
