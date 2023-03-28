<?php

namespace App\Http\Controllers\Website;

use Alhoqbani\SmsaWebService\Smsa;
use App\Http\Controllers\Api\Controller;
use App\Models\Addresses;
use App\Models\Orders;
use App\Models\OrderShipments;
use App\Models\User;
use App\Repositories\AuthRepository;
use App\Repositories\CartOrderRepository;
use App\Repositories\HyperPayRepository;
use App\Repositories\TabbyRepository;
use App\Repositories\Utils\UtilsRepository;
use Damas\Paytabs\paytabs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use function foo\func;


class CartOrderController extends Controller
{


    public function addToCart(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::addToCart($request, $user);
    }

    public function removeFromCart(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::removeFromCart($request, $user);
    }

    public function addCartGifts(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::addCartGifts($request, $user);
    }


    public function getCartItems(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::getCartItems($user, 'website');
    }

    public function getOfferGifts(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::getOfferGifts($request, $user);
    }

    public function goSummery(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::getCartSummary($request, $user);
    }

    public function choose_payment(Request $request)
    {
        $user = auth('client')->user();
        if ($request->isMethod('get')) {
            return redirect('/cart');
        }

        return CartOrderRepository::addOrder($request, $user, 'website');
    }

    public function getCartSummary(Request $request)
    {
        $user = auth('client')->user();
        if ($request->isMethod('get')) {
            return redirect('/cart');
        }
        return CartOrderRepository::getCartSummary($user, 'website');
    }

    public function summary_objects(Request $request, $tax, $order_id)
    {
        $user = auth('client')->user();
        return CartOrderRepository::summary_objects($request, $user);
    }

    public function check_coupon(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::check_coupon($request, $user);
    }

    public function check_coupon_actions(Request $request)
    {
    }

    public function addOrder(Request $request)
    {
        $user = auth('client')->user();
        $address = Addresses::where('user_id', $user->id)->where('is_home', 1)->first();
        if (!$address) {
            return redirect('/addresses');
        }

        return CartOrderRepository::addOrder($request, $user);
    }

    public function sendOrder(Request $request)
    {

        $user = auth('client')->user();
        return CartOrderRepository::sendOrder($request, $user, 'website');
    }

    public function prepareOrder(Request $request, $shipments, $tax, $code, $coupon_money, $handDeliveryCost, $order_type)
    {
    }


    public function myOrders(Request $request)
    {
    }

    public function orderObjects(Request $request)
    {
    }

    public function addProductRating(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::addProductRating($request, $user);
    }

    public function cancelOrderItem(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::cancelOrderItem($request, $user);
    }

    public function returnOrderItem(Request $request)
    {
        $user = auth('client')->user();
        return CartOrderRepository::returnOrderItem($request, $user);
    }

    public function cancel_reasons()
    {
    }


    public function orders(Request $request)
    {
        return CartOrderRepository::myOrders($request, auth('client')->user(), 'website');
    }

    public function single_order($id, Request $request)
    {
        $request->order_id = $id;
        return CartOrderRepository::shipmentDetails($request, auth('client')->user(), 'website');
    }

    public function getInvoicePrint($id)
    {
        /* $shipments=OrderShipments::where('short_code','!=',null)->get();
         foreach ($shipments as $shipment){
             $shipment->short_code=$shipment->id.str_random(4);
             $shipment->save();
         };*/
        $object = OrderShipments::select('order_shipments.*')
            ->where('short_code', $id)->first();


        if (!$object) return abort(404);

        $object->cobon_discounts = $object->cart_items()->sum('cobon_discount');
        $object->motivation_discounts = $object->cart_items()->sum('motivation_discount');
        $object->price_discounts = $object->cart_items()->sum('discount_price');

        return view('pharmacies.orders.invoice-test', [
            'object' => $object,
            'locale' => App::getLocale()
        ]);
    }

    public function checkout(Request $request)
    {
        return HyperPayRepository::requestCheckoutId($request, auth('client')->user(), 'website');
    }

    public function testTabby(Request $request)
    {
        return view('website.tabby');
    }

    public function checkoutTabby(Request $request)
    {
        $result = TabbyRepository::getPaymentUrl($request, auth('client')->user(), 'website');
        if (is_string($result)) {
            return response()->json([
                'status' => 200,
                'message' => __('trans.Redirect to payment page'),
                'data' => [
                    'url' => $result
                ]
            ], 200);
        } else if ($result !== false) {
            return response()->json([
                'status' => 400,
                'message' => $result['message']
            ], 200);
        }
        return response()->json([
            'status' => 400,
            'message' => trans('messages.some_error_happens'),
        ], 200);
    }

    public function main_checkout($type, $id, $request)
    {
        $order_id = $request->order_id;
        $code = $request->code;
        $type1 = $type == 'VISA MASTER' || $type == 'VISA' ? 'VISA' : 'MADA';
        $url = url('/send-order1?checkout_id=' . $id . '&order_id=' . @$order_id . '&payment_type=payment&type=' . $type1 . '&code=' . @$code);
        return view('website.checkout', compact('id', 'type', 'order_id', 'code', 'url'));
    }

    public function checkout_mada($id, Request $request)
    {
        $type = 'MADA';
        return $this->main_checkout($type, $id, $request);
    }

    public function checkout_visa($id, Request $request)
    {
        $type = 'VISA MASTER';
        //        $type='VISA';
        return $this->main_checkout($type, $id, $request);
    }

    public function testOrder(Request $request)
    {
        /**/
        $order_id = 652;
        $order = Orders::whereId($order_id)->with('getUser', 'address', 'cart_items.product.product:id,title,title_en', 'paymentMethod', 'orderStatus')->first();
        $user = User::whereId(434)->first();
        if ($user->active_email == 1) {
            try {
                UtilsRepository::sendEmail(
                    $user->email,
                    __('trans.New order'),
                    view('emails.order', compact('order'))->render()
                );
            } catch (\Exception $ex) {
            }
        }
        return view('emails.order', compact('order'));
        if ($user->active_phone == 1) {
            $smsMessage = 'عميلنا العزيز
(' . $user->username . ')
طلبك رقم ' . $order->id .
                ' في حالة ' . @$order->orderStatus->name . '
';
            $phone_number = '966' . ltrim($user->phone, '0');
            $resp = AuthRepository::send4SMS($smsMessage, $phone_number);
        }

        /**/
    }

    public function thank_you(Request $request)
    {
        $order = Orders::find($request->id);
        return view('website.thank-you', ['order' => $order]);
    }
}
