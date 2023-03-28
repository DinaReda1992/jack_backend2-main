<?php

namespace App\Http\Controllers\Api\Client\Product;

use App\Models\Favorite;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResources;
use App\Http\Resources\ProductsResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\Client\Controller;
use App\Http\Resources\SearchProductResource;

class ProductController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth:api')->only('favoriteProducts', 'likeOrDislikeProduct');
    }

    public function searchAutoComplete(Request $request)
    {
        $objects = Products::getProducts('api')->take(10)->get();
        SearchProductResource::using(['user_id' => auth('api')->user()]);
        // $objects->{'products'} = SearchProductResource::collection($objects);

        return response()->json(['data' => SearchProductResource::collection($objects), 'status' => 200]);
    }

    public function searchProducts(Request $request)
    {
        $objects = Products::getProducts('api')->paginate(20);
        ProductResources::using(['user_id' => auth('api')->user()]);

        $objects->{'products'} = ProductResources::collection($objects);

        return response()->json(['data' => $objects, 'status' => 200]);
    }

    public function getProduct(Request $request)
    {
        $resp = [];
        $object = Products::getProducts('api')->where('is_archived', 0)->where('stop', 0)->with('photos')->where('id', $request->product_id)->first();
        if (!$object) {
            return response()->json(['status' => 400, 'message' => __('messages.product_not_found')], 400);
        }
        ProductResources::using(['user_id' => auth('api')->user()]);
        $resp['product'] = new ProductResources($object);
        return response()->json($resp);
    }

    public function favoriteProducts(Request $request)
    {
        request()->merge(['is_favorite' => 1]);
        $objects = Products::getProducts('api')->paginate(20);
        ProductResources::using(['user_id' => auth('api')->user()]);
        $objects->{'products'} = ProductResources::collection($objects);
        return response()->json(['data' => $objects, 'status' => 200]);
    }

    public function likeOrDislikeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(),], 400);
        }

        if (!Products::find($request->item_id)) {
            return response()->json(['message' => __('messages.product_not_found')], 400);
        }

        $user = auth('api')->user();
        $like = Favorite::where('user_id', $user->id)->where('item_id', $request->item_id)->where('type', 0)->first();
        if (!$like) {
            $like = Favorite::create(['user_id' => $user->id, 'item_id' => $request->item_id, 'type' => 0]);
            return response()->json(['message' => __('messages.like_product')], 200);
        } else {
            $like->delete();
            return response()->json(['message' => __('messages.like_deleted_successfully')]);
        }
    }
}
