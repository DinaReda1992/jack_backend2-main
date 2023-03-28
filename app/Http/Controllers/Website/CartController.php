<?php

namespace App\Http\Controllers\Website;

use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CartRepository;
use App\Http\Resources\CartItemResource;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()], 202);
        }
        return CartRepository::addToCart($request, auth('client')->user());
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()], 202);
        }

        return CartRepository::addToCart($request, auth('client')->user());
    }

    public function getCart()
    {
        $items = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
        $items = json_encode(CartItemResource::collection($items));
        return view('website.cart', compact('items'));
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first(),], 400);
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
}
