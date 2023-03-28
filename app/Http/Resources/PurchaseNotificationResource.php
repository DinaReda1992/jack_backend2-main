<?php

namespace App\Http\Resources;

use App\Models\Orders;
use App\Models\Settings;
use App\Models\Purchase_order;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->user_type_id == 3) {
            if ($this->PurchaseOrder) {
                $data = Purchase_order::where('provider_id', $user->id)
                    ->select(
                        'id',
                        'provider_id',
                        'final_price',
                        'order_price',
                        'taxes',
                        'delivery_date',
                        'delivery_time',
                        'provider_delivery_date',
                        'provider_delivery_time',
                        'delivery_method',
                        'transfer_photo',
                        'details',
                        'payment_terms',
                        'status'
                    )
                    ->selectRaw('(CASE WHEN code = "" THEN "' . "" . '" ELSE (CONCAT ("' . URL::to('/') . '/p/", code)) END) AS invoice_url')
                    ->with(['purchase_item.product' => function ($query) {
                        $query->select('id', 'title');
                        $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
                    }])
                    ->selectRaw('DATE_FORMAT(purchase_orders.delivery_date, "%Y-%m-%d") As delivery_date')

                    ->with('orderStatusSupplier', 'purchase_item', 'paymentMethod', 'paymentTerm')
                    ->withCount('purchase_item')->where('id', $this->PurchaseOrder->id)->first();
                $array = ['order' => @new PurchaseOrders($data)];
            } else {
                $array = ['order' => null];
            }
        } elseif ($user->user_type_id == 2 && $this->type == 88) {
            $select_status = App::getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';
            $order = Orders::where('orders.id', $this->order_id)->select('orders.id', 'orders.final_price', 'orders.marketed_date', 'orders.is_edit as has_second_order', 'orders.parent_order as has_parent_order', 'orders.status', $select_status, 'order_status.color', 'orders.payment_method', 'payment_methods.name as payment_method_name')
                ->selectRaw('(CONCAT ("' . url('/') . '/i/", orders.short_code)) as download_url')
                ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.order_id =orders.id) as products_count')
                ->leftJoin('order_status', 'order_status.id', 'orders.status')
                ->join('payment_methods', 'orders.payment_method', 'payment_methods.id')
                ->where('orders.payment_method', '<>', 0)
                ->with('transfer_photo.to_bank', 'balance', 'transaction')->first();
            $array = ['order' => @new MyOrdersResources($order)];
        } else {
            $array = ['order' => @new PurchaseOrderResource($this->PurchaseOrder)];
        } 
        ini_set('serialize_precision', -1);
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'getSender' => [
                'username' => $this->sender_id == 1 ?  'مدير التطبيق' :  @$this->getSender->username,
                'photo' => $this->sender_id == 1 ? url('/') . "/images/" . Settings::find(1)->value : $this->getSender->photo,
            ],
            'order_id' => $this->order_id,
            'type' => $this->type,
            'message' => $this->message,
            'created_at' => $this->created_at->diffForHumans(),
        ] + $array;
    }
}
