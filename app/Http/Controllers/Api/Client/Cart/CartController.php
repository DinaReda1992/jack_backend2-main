<?php

namespace App\Http\Controllers\Api\Client\Cart;

use Illuminate\Http\Request;
use App\Repositories\CartRepository;
use App\Repositories\CartOrderRepository;
use App\Http\Controllers\Api\Client\Controller;

class CartController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth:api');
    }


    public function addToCart(Request $request)
    {
        $user = auth('api')->user();
        return CartRepository::addToCart($request, $user);
    }

    public function removeFromCart(Request $request)
    {
        $user = auth('api')->user();
        return CartRepository::removeFromCart($request, $user);
    }

    public function getCartItems(Request $request)
    {
        $user = auth('api')->user();
        return CartOrderRepository::getCartItems($user, 'api');
    }

    public function getCartSummary(Request $request)
    {
        $user = auth('api')->user();
        return CartOrderRepository::getCartSummary($user);
    }

    public function checkCoupon(Request $request)
    {
        $user = auth('api')->user();
        return CartOrderRepository::check_coupon($request, $user);
    }
}
