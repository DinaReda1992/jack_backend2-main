<?php

namespace App\Http\Controllers\Api\Provider;

use Carbon\Carbon;
use App\Models\Settings;
use App\Models\OrdersStatus;
use Illuminate\Http\Request;
use App\Models\Purchase_item;
use App\Models\Purchase_order;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\SupplierPurcheseStatus;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\PurchaseItemResource;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class UpdateOrderController extends Controller
{
    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        app()->setLocale($language);
        Carbon::setLocale(app()->getLocale());
        $this->middleware('jwt.auth');
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
    }

    public function orderDetails($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $order = Purchase_order::whereIn('provider_id', $user->mainSupplierUsers())->where('id', $id)->first();
        if (!$order) {
            return response()->json(['status' => 400, 'message' => 'هذا الطلب لا يمكنك التعامل معه'], 400);
        }

        $items = Purchase_item::with('provider', 'product', 'product.measurement')->where('order_id', $order->id)->get();

        return response()->json([
            'items' => PurchaseItemResource::collection($items),
        ]);
    }

    public function updateOrder($id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $order = Purchase_order::whereIn('provider_id', $user->mainSupplierUsers())->whereIn('status', [2, 3, 4])->where('id', $id)->first();
        if (!$order) {
            return response()->json(['status' => 400, 'message' => 'هذا الطلب لا يمكنك تعديله'], 400);
        }

        $validator = Validator::make($request->all(), [
            'provider_delivery_time' => $order->status == 2 ? 'required' : '',
            'provider_delivery_date' =>  $order->status == 2 ? 'required' : '',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }
        $order->update([
            'provider_delivery_date' => $request->provider_delivery_date ?: $order->provider_delivery_date,
            'provider_delivery_time' => $request->provider_delivery_time ?: $order->provider_delivery_time,
            'status' => $order->status == 2 ? 3 : $order->status
        ]);
        foreach (json_decode(request('items')) as $item) {
            $purchase_item = Purchase_item::where('order_id', $id)->where('id', $item->id)->first();
            if ($purchase_item) {
                $purchase_item->update([
                    'quantity' => $item->quantity,
                    'quantity_difference' => $purchase_item->quantity_difference + $purchase_item->quantity - $item->quantity,
                ]);
            }
        }

        $total = Purchase_item::where('order_id', $order->id)->whereIn('provider_id', $user->mainSupplierUsers())
            ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;

        $taxes = Settings::find(38)->value;
        $tax_price = (($total) * $taxes / 100);
        $order->update(['paid_price' => $order->final_price > $total + $tax_price ? $order->final_price : 0, 'final_price' => $total + $tax_price, 'order_price' => $total, 'taxes' => $tax_price]);
        if ($order->paid_price > $order->final_price) {
            $order->update(['paid_price' => $order->paid_price > 0 ? $order->paid_price : $order->final_price, 'is_edit' => 1]);
        }

        $btn_text = SupplierPurcheseStatus::where('id', $order->status)->first();
        return response()->json(
            [
                'status' => 200,
                'new_status' => $order->status,
                'btn_text' => $btn_text,
                'status_object' => $btn_text,
                'message' => __('messages.change_order_status_successfully'),
            ],
            200
        );
        // return response()->json(['order_id' => $order->id, 'message' => 'تم التعديل بنجاح']);
    }
}
