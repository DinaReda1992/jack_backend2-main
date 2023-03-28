<?php

namespace App\Repositories;

use Validator;
use Carbon\Carbon;
use App\Models\Cobons;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Favorite;
use App\Models\Products;
use App\Models\Settings;
use App\Models\Addresses;
use App\Models\CobonsBrands;
use App\Models\RetrievalItem;
use App\Models\OrderShipments;
use App\Models\Shop_offer_item;
use App\Models\RetrievalOrder;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class CartRepository extends Controller
{
    public static function addToCart($request, $user)
    {
        request()->merge(['product_id' => $request->id, 'cart_quantity' => $request->quantity]);
        $user_like = $user->id;
        $id = $request->product_id;
        $product = Products::select('products.id', 'products.client_price', 'products.price', 'products.quantity', 'products.min_quantity')
            ->with('offer_product')
            ->selectRaw('(SELECT sum(cart_items.quantity) FROM cart_items WHERE cart_items.user_id =' . $user->id . ' AND cart_items.item_id=products.id and cart_items.shipment_id=0 and cart_items.cart_id=0) as cart_count')
            ->where('products.id', $request->product_id)->first();
        if (!$product) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'لا يوجد منتج للاضافة للسلة .',
                ],
                201
            );
        }
        $mount = isset($request->cart_quantity) ? $request->cart_quantity : (int)$product->cart_count + 1;

        // if ($mount > $product->quantity || $product->quantity <= $product->min_quantity) {
        //     return response()->json([
        //         'status' => 400,
        //         'message' => __('trans.There is no stock available for this product.'),
        //     ], 201);
        // }
        $tax = @Settings::where('option_name', 'tax_fees')->first()->value;
        if ($product->cart_count) {
            $cart_item = CartItem::where('item_id', $product->id)->where('user_id', $user_like)->where('shipment_id', 0)->first();
        } else {
            $cart_item = new CartItem();
            $cart_item->offer_id = 0;
        }

        $cart_item->item_id = $id;
        $cart_item->status = 0;
        $cart_item->user_id = $user->id;
        $cart_item->quantity = $mount;
        $cart_item->shop_id = 1;
        $cart_item->type = 1;
        $cart_item->order_id = 0;
        $cart_item->save();
        $gifts = [];
        $gift_message = '';
        $offer = @$product->offer_product;
        $price = $product->price;
        $discount_price = 0;
        if ($offer) {
            if ($offer->number_of_users || $offer->one_user_use) {
                $allUsesCount = CartItem::where('shipment_id', 0)
                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.offer_id =' . $offer->offer_id . '  AND shipment_id <> 0 and cart_items.cart_id=0) as allOfferUses')
                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.offer_id =' . $offer->offer_id . '  AND shipment_id <> 0 and cart_items.user_id=' . $user_like . ' and cart_items.cart_id=0 ) as userUses')
                    ->first();
                if ($offer->offer->users->count() > 0 && !in_array($user_like, $offer->offer->users()->pluck('user_id')->toArray())) {
                    $cart_item->price = $price;
                    $cart_item->offer_id = 0;
                    $cart_item->save();
                    $items = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')->with('product')->where('user_id', $user->id)->get();
                    return response()->json(
                        [
                            'items' => CartItemResource::collection($items),
                            'count_items' =>  (int)$items->count(),
                            'status' => 200,
                            'cart_count' => (int)$user->cartCount(),
                            'gift_message' => $gift_message,
                            'product_count' => $cart_item->quantity ?: 0,
                            'quantity' => @$offer->quantity ?: 0,
                            'get_quantity' => @$offer->get_quantity ?: 0,
                            'message' => __('trans.The product has been successfully added to the cart'),
                        ]
                    );
                }

                if ($allUsesCount && ($allUsesCount->allOfferUses >= $offer->number_of_users || $allUsesCount->userUses >= $offer->one_user_use)) {
                    $cart_item->price = $price;
                    $cart_item->offer_id = 0;
                    $cart_item->save();
                    $items = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')->with('product')->where('user_id', $user->id)->get();
                    return response()->json(
                        [
                            'items' => CartItemResource::collection($items),
                            'count_items' =>  (int)$items->count(),
                            'status' => 200,
                            'cart_count' => (int)$user->cartCount(),
                            'gift_message' => $gift_message,
                            'product_count' => $cart_item->quantity ?: 0,
                            'quantity' => @$offer->quantity ?: 0,
                            'get_quantity' => @$offer->get_quantity ?: 0,
                            'message' => __('trans.The product has been successfully added to the cart'),
                        ]
                    );
                }
            }
            switch ($offer->type_id) {
                    // discount amount
                case 1:
                    $price = $product->price;
                    if ($offer->price_discount > $price) {
                        $cart_item->offer_id = 0;
                        $cart_item->discount_price = 0;
                    } else {
                        $cart_item->offer_id = $offer->offer_id;
                        $discount_price = $cart_item->quantity * $offer->price_discount;
                        $cart_item->discount_price = $discount_price;
                    }
                    break;
                    // discount percentage
                case 2:
                    $discount = $product->price * $offer->percentage / 100;
                    $price = $product->price;
                    $cart_item->offer_id = $offer->offer_id;
                    $discount_price  = $cart_item->quantity * $discount;
                    $cart_item->discount_price = $discount_price;
                    break;
                    // buy x get y
                case 3:
                    $all_quantity = $offer->quantity + $offer->get_quantity;

                    if ($mount >= $all_quantity) {
                        $quantity_offer_in_cart = (int)($mount / ($offer->quantity + $offer->get_quantity));
                        $cart_item->offer_id = $offer->offer_id;
                        $discount = $offer->is_free ? ($product->price) : ($product->price * $offer->percentage / 100);
                        $discount_price = $quantity_offer_in_cart * $offer->get_quantity * $discount;
                        $cart_item->discount_price = $discount_price;
                    } elseif ($mount < $all_quantity) {
                        $cart_item->offer_id = 0;
                        $cart_item->discount_price = 0;
                    }
                    break;
                    //free shipment
                case 5:
                    $cart_item->offer_id = $offer->offer_id;
                    break;
            }
        } else {
        }
        $cart_item->price = $price;
        $cart_item->price_vat = ($price * $cart_item->quantity) * $tax / 100;
        $cart_item->discount_vat = $discount_price * $tax / 100;
        $cart_item->save();
        $items = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')->with('product')->where('user_id', $user->id)->get();
        return response()->json(
            [
                'items' => CartItemResource::collection($items),
                'count_items' =>  (int)$items->count(),
                'status' => 200,
                'cart_count' => (int)$user->cartCount(),
                'product_count' => $cart_item->quantity ?: 0,
                'quantity' => @$offer->quantity ?: 0,
                'get_quantity' => @$offer->get_quantity ?: 0,
                'gift_message' => $gift_message,
                'cobon' => @$cart_item->motivation_cobon_id,
                'message' => __('trans.The product has been successfully added to the cart'),
            ]
        );
    }

    public static function removeFromCart($request, $user)
    {
        $cart_item = CartItem::where('item_id', $request->id)->where('shipment_id', 0)->where('user_id', $user->id)->first();
        if (!$cart_item) {
            return response()->json(['status' => 400, 'cart_count' => (int)$user->cartCount(), 'message' => __('trans.This product is not in the cart')]);
        }

        CartItem::where('cart_id', $cart_item->id)->delete();
        $cart_item->delete();
        return response()->json(['status' => 200, 'cart_count' => (int)$user->cartCount(), 'message' => __('trans.Deleted successfully'),]);
    }

    public static function addCartGifts($request, $user)
    {
        $user_like = $user->id;
        $cart_item = CartItem::select(
            'cart_items.id as cart_id',
            'cart_items.item_id',
            'shop_products.id',
            'shop_products.product_id',
            'shop_products.user_id',
            'products.client_price',
            'cart_items.quantity',

            'shop_offer_items.id',
            'shop_offer_items.product_id',
            'shop_offer_items.offer_id',
            'shop_offers.type_id',
            'shop_offers.price_discount',
            'shop_offers.is_free',
            'shop_offers.percentage',
            'shop_offers.quantity',
            'shop_offers.get_quantity',
            'shop_offers.number_of_users',
            'shop_offers.one_user_use',
            'shop_offer_types.name_en'
        )
            ->join('shop_products', 'cart_items.item_id', 'shop_products.id')
            ->join('products', 'products.id', 'shop_products.product_id')
            ->join('shop_offers', 'cart_items.offer_id', 'shop_offers.id')
            ->join('shop_offer_items', 'shop_offers.id', 'shop_offer_items.offer_id')
            ->join('shop_offer_types', 'shop_offer_types.id', 'shop_offers.type_id')
            ->join('users', 'shop_products.user_id', 'users.id')
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->join('users as supplier_user', 'products.user_id', 'supplier_user.id')
            ->join('user_data as supplier_data', 'supplier_data.user_id', 'supplier_user.id')
            ->selectRaw('(SELECT sum(cart_items.quantity) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id=shop_products.id AND type=1 and cart_items.shipment_id=0) as cart_count')
            ->selectRaw('(SELECT count(cart_items.offer_id) FROM cart_items 
            WHERE cart_items.offer_id =shop_offers.id AND cart_items.shipment_id<>0) as all_uses_count')
            ->selectRaw('(SELECT count(cart_items.offer_id) FROM cart_items 
            WHERE cart_items.offer_id =shop_offers.id AND cart_items.shipment_id<>0 AND cart_items.user_id=' . $user_like . ') as user_uses_count')
            ->where('shop_offer_items.group', 1)->where('shop_offers.status', 1)
            ->whereDate('shop_offers.start_date', '<=', Carbon::today())
            ->whereDate('shop_offers.end_date', '>=', Carbon::today())
            ->where('shop_offers.deleted_at', null)
            ->where('users.block', 0)
            ->where('user_data.stop', 0)
            ->where('supplier_data.stop', 0)
            ->where('shop_products.id', $request->product_id)
            ->where('cart_items.shipment_id', 0)->where('cart_items.user_id', $user_like)->first();
        if (!$cart_item || $cart_item->all_uses_count >= $cart_item->number_of_users || $cart_item->user_uses_count >= $cart_item->one_user_use) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('trans.This offer cannot be used, it may have expired or the allowed limit has been used up'),
                ]
            );
        }
        $gifts = $objects = Shop_product::select(
            'shop_products.id'
        )
            ->join('products', 'products.id', 'shop_products.product_id')
            ->join('shop_offer_items', 'shop_offer_items.product_id', 'products.id')
            ->join('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
            ->where('shop_offer_items.group', 2)
            ->where('shop_offers.id', $cart_item->offer_id)
            ->pluck('shop_products.id')->toArray();

        $items = json_decode($request->items);
        foreach ($items as $item) {
            if (!empty($item) && $item->product_id) {
                if (!in_array($item->product_id, $gifts)) {
                    return response()->json(
                        [
                            'status' => 400,
                            'message' => __('trans.One of these products is no longer available'),
                        ]
                    );
                }
            }
        }
        CartItem::where('cart_id', $cart_item->cart_id)->delete();
        if ($items && count($items) > 0) {
            foreach ($items as $item) {
                if (!empty($item) && $item->product_id) {

                    $gift_product = Shop_product::select('shop_products.id', 'products.client_price', 'products.has_tax')
                        ->join('products', 'products.id', 'shop_products.product_id')
                        ->where('shop_products.id', $item->product_id)->first();
                    $gift_item = new CartItem();

                    $gift_item->item_id = $item->product_id;

                    $gift_item->user_id = $user_like;
                    $gift_item->quantity = $item->quantity;
                    $gift_item->type = 1;
                    $gift_item->shop_id = $cart_item->user_id;
                    $gift_item->cart_id = $cart_item->cart_id;
                    $gift_item->price = $gift_product->price;
                    $discount_price = $cart_item->is_free ? $gift_product->price : ($gift_product->price * $cart_item->percentage / 100);
                    $discount_price = $item->quantity * floatval($discount_price);
                    $gift_item->discount_price = $discount_price;
                    if ($gift_product->has_tax == 1) {
                        $tax = @Settings::where('option_name', 'tax_fees')->first()->value;
                        $gift_item->price_vat = $gift_product->price * $tax / 100;
                        $gift_item->discount_vat = $discount_price * $tax / 100;
                    }

                    $gift_item->offer_id = $cart_item->offer_id;
                    $gift_item->status = 1;
                    $gift_item->save();
                }
            }
        }
        return response()->json(
            [
                'status' => 200,
                'cart_count' => (int)$user->cartCount(),
                'message' => __('trans.The offer\'s products have been successfully added to the cart'),
            ]
        );
    }

    public static function getCartItems($user, $platform = 'api')
    {
        $select_title = App::getLocale() == "ar" ? 'products.title' : 'products.title_en as title';

        $current_items = CartItem::select(
            'cart_items.id',
            'cart_items.item_id',
            'cart_items.order_id',
            'cart_items.user_id',
            'products.id as shop_product_id',
            'products.category_id',
            'cart_items.quantity',
            'products.quantity as product_quantity',
            'products.min_quantity as product_min_quantity',
            $select_title,
            'cart_items.offer_id',
            'cart_items.price',
            'cart_items.discount_price',
            'cart_items.cart_id',
            'products.client_price',
            'products.price'
        )
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->join('products', 'products.id', 'cart_items.item_id')
            ->with([
                'offer' => function ($query) use ($user) {
                    $query->select(
                        'shop_offers.id',
                        'shop_offer_items.product_id',
                        'shop_offer_items.offer_id',
                        'shop_offers.type_id',
                        'shop_offers.price_discount',
                        'shop_offers.is_free',
                        'shop_offers.percentage',
                        'shop_offers.quantity',
                        'shop_offers.get_quantity',
                        'shop_offers.number_of_users',
                        'shop_offers.one_user_use',
                        'shop_offer_types.name_en',
                        'products.id AS shop_product_id'
                    )
                        ->selectRaw('(SELECT count(cart_items.offer_id) FROM cart_items
                     WHERE cart_items.offer_id =shop_offers.id AND cart_items.shipment_id<>0 AND status <> 5) as all_uses_count')
                        ->selectRaw('(SELECT count(cart_items.offer_id) FROM cart_items 
                    WHERE cart_items.offer_id =shop_offers.id AND cart_items.shipment_id<>0 AND cart_items.user_id=' . $user->id . ' AND status <> 5) as user_uses_count')
                        ->join('shop_offer_items', 'shop_offers.id', 'shop_offer_items.offer_id')
                        ->join('products', 'products.id', 'shop_offer_items.shop_product_id')
                        ->join('shop_offer_types', 'shop_offer_types.id', 'shop_offers.type_id')
                        ->where('shop_offer_items.group', 1)
                        ->where('shop_offers.status', 1)
                        ->whereDate('shop_offers.start_date', '<=', Carbon::today())
                        ->whereDate('shop_offers.end_date', '>=', Carbon::today());
                    // ->where('shop_offer_items.deleted_at', null)
                    // ->where('shop_offers.deleted_at', null);
                },
                // 'cart_gifts' => function ($query) use ($select_title) {
                //     $query->select(
                //         'cart_items.id',
                //         $select_title,
                //         'cart_items.cart_id',
                //         'cart_items.price',
                //         'cart_items.discount_price',
                //         'cart_items.item_id',
                //         'main_cart.offer_id',
                //         'products.id',
                //         'products.quantity as product_quantity',
                //         'products.min_quantity as product_min_quantity',
                //         'products.price',
                //         'cart_items.quantity',

                //         'shop_offer_items.group',
                //         'shop_offers.quantity as offer_quantity',
                //         'shop_offers.get_quantity',
                //         'shop_offers.is_free'
                //     )
                //         ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
                //         ->selectRaw('(SELECT sum(cart_items.quantity) FROM cart_items WHERE cart_items.cart_id =main_cart.id                 
                //         and main_cart.offer_id=shop_offers.id
                //      ) as gift_quantity')
                //         ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.type=1 ) as product_rate')
                //         ->join('products', 'cart_items.item_id', 'products.id')
                //         ->join('shop_offer_items', 'shop_offer_items.product_id', 'products.id')
                //         ->join('cart_items as main_cart', 'main_cart.id', 'cart_items.cart_id')
                //         ->join('shop_offers', 'main_cart.offer_id', 'shop_offers.id')
                //         //                    ->join('shop_offers as main_offer','main_offer.user_id','cart_items.shop_id')
                //         ->groupBy('cart_items.id')
                //         ->where('shop_offer_items.group', 2);
                // }
            ])
            ->where('cart_items.shipment_id', 0)
            ->where('cart_items.user_id', $user->id)
            ->where('cart_items.cart_id', 0)
            // ->with('cobon')
        ;
        if ($platform == 'api') {
            $page = (isset($data['page'])) ? $data['page'] : 1;
            $per_page = 100;
            $offset = $per_page * ($page - 1);

            $count = $current_items->count();
            $current_items = $current_items->offset($offset)
                ->skip($offset)
                ->take($per_page)
                ->get();

            //            $current_items = $current_items->paginate(100);
        } else {
            $current_items = $current_items->get();
        }
        $messages = [];
        $allItems = [];

        foreach ($current_items as $item) {
            if ($item->product->stop == 1) {
                $messages[] = $item->title . __('trans.It is no longer available');
                $item->delete();
                continue;
            } 
            // else if ($item->product_quantity <= $item->product_min_quantity) {
            //     $messages[] = $item->title . __('trans.It is no longer available');
            //     $item->delete();
            //     continue;
            // } elseif ($item->quantity > $item->product_quantity) {
            //     $item->quantity = $item->product_quantity;
            //     $item->save();
            //     $messages[] = __('trans.The quantity required for the product has been modified') . $item->title;
            // }
            $tax = 0;
            if ($item->has_tax == 1) {
                $tax = @Settings::where('option_name', 'tax_fees')->first()->value;
            } else {
                $item->discount_vat = 0;
                $item->price_vat = 0;
            }
            $price = $item->price;
            $cart_discount = $item->discount_price;
            $cart_price = $item->price;
            if ($price != $cart_price) {
                $item->price = $price;
                if ($item->has_tax == 1) {
                    $item->price_vat = $price * $tax / 100;
                }

                $item->save();
                $messages[] = ' تم تغيير السعر على  ' . $item->title;
            }
            /**/
            // if ($item->motivation_id) {
            //     $cobon = MotivationCobon::where('id', $item->motivation_cobon_id)
            //         ->whereHas('motivation', function (Builder $query) {
            //             $query->where('status', 1);
            //         })
            //         ->with('motivation')
            //         ->first();
            //     if ($cobon) {
            //         $cart_cobons = CartItem::where('status', '>', 1)->where('motivation_cobon_id', $cobon->id)->count();
            //         $if_user_used_cobon = CartItem::where('status', '>', 1)->where('motivation_cobon_id', $cobon->id)->where('user_id', $user->id)->count();
            //         if ($cart_cobons < $cobon->usage && !$if_user_used_cobon) {
            //             $cobon_value = $cobon->cobone_value;
            //             Log::alert(($cobon_value * $item->quantity) . '-' . ($item->motivation_discount));
            //             $available_codes = $cobon->usage - $item->quantity;
            //             if ((($cobon_value * $item->quantity) != ($item->motivation_discount)) || $available_codes < $item->motivation_quantity) {
            //                 $messages[] = __('trans.Coupon discount has been modified') . ' ' . $item->title;

            //                 if ($item->quantity >= $available_codes) {
            //                     $item->motivation_quantity = $available_codes;
            //                     $item->motivation_discount = $cobon_value * $available_codes;
            //                     $item->motivation_vat = $item->motivation_discount * ($tax / 100);
            //                 } else {
            //                     $item->motivation_quantity = $item->quantity;
            //                     $item->motivation_discount = $cobon_value * $item->quantity;
            //                     $item->motivation_vat = $item->motivation_discount * ($tax / 100);
            //                 }
            //                 $item->save();
            //             }
            //         } else {
            //             $messages[] = __('trans.Coupon discount has been modified') . ' ' . $item->title;
            //             $item->discount_price = 0;
            //             $item->motivation_id = 0;
            //             $item->motivation_user_id = 0;
            //             $item->motivation_cobon_id = 0;
            //             $item->motivation_discount = 0;
            //             $item->save();
            //         }
            //     }
            // }

            /**/
            $offer = @$item->offer;
            $offerItem = null;
            if ($offer) {
                $offerItem = Shop_offer_item::withoutTrashed()->where(['offer_id' => $offer->id, 'shop_product_id' => $item->item_id])
                    ->first();
            }
            //                return response()->json($offer);
            if ($item->offer_id && $offer->users->count() > 0 && !in_array($user->id, $offer->users()->pluck('user_id')->toArray())) {
                $item->offer_id = 0;
                $item->discount_price = 0;
                $item->save();
                $messages[] = __('trans.Offer expired on') . $item->title;
            }
            if ($item->offer_id && (!$offer || !$offerItem)) {
                $item->offer_id = 0;
                $item->discount_price = 0;
                $item->save();
                // $item->cart_gifts = [];
                CartItem::where('cart_id', $item->id)->delete();
                $messages[] = __('trans.Offer expired on') . $item->title;
            } elseif ($item->offer_id && (@$offer->one_user_use || @$offer->number_of_users)) {
                if ((@$offer->all_uses_count > @$offer->number_of_users || @$offer->user_uses_count > @$offer->one_user_use)) {
                    //                    $item->offer_id = 0;
                    $item->offer_id = 0;
                    $item->discount_price = 0;
                    $item->save();
                    CartItem::where('cart_id', $item->id)->delete();
                    $messages[] = __('trans.Offer expired on') . $item->title;
                }
            } elseif ($item->offer_id && $offer) {

                switch ($offer->type_id) {
                        // discount amount
                    case 1:
                        $offer_discount = $item->quantity * $offer->price_discount;
                        if ($cart_discount != $offer_discount) {
                            $item->discount_price = $offer_discount;
                            if ($item->has_tax == 1) {
                                $item->discount_vat = $offer_discount * $tax / 100;
                            }

                            $item->save();
                            $messages[] = __('trans.The offer applied has been modified on') . $item->title;
                        }
                        break;
                        // discount percentage
                    case 2:
                        $offer_discount = $item->quantity * ($price * $offer->percentage / 100);
                        if ($cart_discount != $offer_discount) {
                            $item->discount_price = $offer_discount;
                            if ($item->has_tax == 1) {
                                $item->discount_vat = $offer_discount * $tax / 100;
                            }
                            $item->save();
                            $messages[] = __('trans.The offer applied has been modified on') . $item->title;
                        }
                        break;
                        // buy x get y
                    case 3:
                        //the offer quantity must have ->
                        //  cart_quantity-(offer.quantity+offer.get_quantity)+offer.get_quantity
                        $all_quantity = $offer->quantity + $offer->get_quantity;
                        //                        $cart_offer_quantity = $item->quantity >= $all_quantity ? $offer->get_quantity : $item->quantity - $all_quantity + $offer->get_quantity;
                        $discount = $offer->is_free ? ($price) : ($price * $offer->percentage / 100);
                        $offer_discount = ($offer->get_quantity) * $discount;

                        if ($item->quantity >= $all_quantity && $item->discount_price != $offer_discount) {
                            $item->discount_price = $offer_discount;
                            if ($item->has_tax == 1) {
                                $item->discount_vat = $offer_discount * $tax / 100;
                            }

                            $item->save();
                            $messages[] = __('trans.The offer applied has been modified on') . $item->title;
                        }

                        //                        if ($cart_discount != $offer_discount) {
                        //                            $item->discount_price = $offer_discount;
                        //                            $item->save();
                        //                            $messages[] = ' تم تعديل العرض المطبق على   ' . $item->title;
                        //                        }
                        break;
                        // get gift
                    case 4:
                        if ($item->cart_gifts->count() && $item->cart_gifts[0]->gift_quantity != $offer->get_quantity) {
                            CartItem::where('cart_id', $item->id)->delete();
                            $item->cart_gifts = [];

                            $messages[] = __('trans.The offer applied has been modified on') . $item->title;
                        }
                        break;
                }
            }

            $allItems[] = $item;
        }
        $settings = Settings::where('option_name', 'tax_fees')->first();


        CartItemResource::using(['tax' => @$settings->value]);

        //        $cart = CartItemResource::collection($current_items);
        if ($platform == 'api') {
            //            $current_items->{'current_items'} = CartItemResource::collection($allItems);
            $current_items = new Paginator(CartItemResource::collection($allItems), $count, $per_page);
        } else {
            $current_items = CartItemResource::collection($allItems);
        }
        if ($platform == 'website') {
            if (\auth('client')->id() == 429) {
                //                return $current_items;
            }
            $total = 0;
            foreach ($current_items as $current_item) {
                if ($current_item->offer_price > 0) {
                    $total += ($current_item->offer_price * $current_item->quantity); // the value of the current key.
                } else {
                    $total += ($current_item->price * $current_item->quantity); // the value of the current key.
                }
            }
            $total = number_format($total, 2, '.', '');
            $items = json_encode($current_items);
            $messages = json_encode($messages);
            return view('website.cart', compact('items', 'messages', 'total'));
        }
        return response()->json(
            [
                'items' => $current_items,
                //                'offer_items' => $cart_offers,
                'messages' => $messages
            ]
        );
    }

    public static function getOfferGifts($request, $user)
    {
        $user_like = $user->id;
        $lang = App::getLocale();
        $select_product_title = $lang == 'ar' ? 'products.title' : 'products.title_en as title';

        $gifts = $objects = Shop_product::select(
            'shop_products.id',
            'shop_products.product_id',
            'shop_products.user_id',
            $select_product_title,
            'products.client_price',
            'shop_products.min_quantity',
            'shop_products.quantity',
            'products.category_id',
            'shop_offers.quantity as offer_quantity',
            'shop_offers.get_quantity',
            'shop_offers.is_free',
            'shop_offers.percentage',
            'shop_offers.id as offer_id',
            'products.has_tax'
        )
            ->join('products', 'products.id', 'shop_products.product_id')
            ->join('shop_offer_items', 'shop_offer_items.product_id', 'products.id')
            ->join('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=shop_products.id AND type=0) as is_liked')
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.type=1 ) as product_rate')
            ->selectRaw('(SELECT sum(quantity) FROM cart_items WHERE cart_items.item_id =shop_products.id                 
                        and cart_items.cart_id <> 0
                        and cart_items.shipment_id = 0
                        and cart_items.user_id=' . $user_like . '
                     ) as gift_quantity')
            ->where('shop_offer_items.group', 2)
            ->where('shop_offer_items.offer_id', $request->offer_id)
            ->groupBy('shop_products.product_id')
            ->get();
        $gift_message = '';
        if ($gifts->count()) {
            $perc = $gifts[0]->is_free ? ' 100% ' : $gifts[0]->percentage . '%';
            $gift_message = __('trans.Get a discount on the following products by') .
                $perc .
                __('trans.by adding') . $gifts[0]->get_quantity . __('trans.from_it');
        }
        $gifts = ProductsGiftsResource::collection($gifts);

        return response()->json([
            'status' => 200,
            'gifts' => $gifts,
            'gift_message' => $gift_message
        ]);
    }

    public static function getCartSummary($user, $platform = 'api')
    {
        $settings = Settings::where('option_name', 'tax_fees')->first();
        $taxes = @$settings->value;
        $address = Addresses::where('user_id', $user->id)->where('is_archived', 0)->where('is_home', 1)->first();
        $objects = [];
        if ($address) {
            $objects = (new self)->summary_objects(@$settings->value, 0, $user);
        }
        if ($platform == 'website') {
            $addresses = Addresses::where('user_id', $user->id)->where('is_archived', 0)->orderBy('is_home', 'desc')->get();
            if (!$address) {
                return redirect('/addresses')->with('message', __('trans.Add your address to complete order'));
            }
            //            $objects = (new self)->summary_objects(@$settings->value, 0, $user);

            if (\auth('client')->id() == 429) {
                //                return $objects;
            }
            $shipping_cost = Settings::find(22);
            $shipping_cost = $shipping_cost ? intval($shipping_cost->value) : 0;
            $objects = json_encode($objects);
            return view('website.summary', compact('objects', 'taxes', 'addresses', 'address', 'shipping_cost'));
        }
        return \response()->json([
            'data' => $objects,
            'taxes' => @$settings->value,
            'addresses' => $address
        ]);
    }

    public static function summary_objects($tax, $order_id, $user)
    {
        //        $select_title = App::getLocale() == "ar" ? 'shop_data.user_name as shop_name' : 'shop_data.user_name_en as shop_name';
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $cart_items = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            // 'cart_items.motivation_discount',
            // 'cart_items.motivation_quantity',
            'products.category_id',
            'cart_items.quantity',
            $select_title,
            'cart_items.offer_id',
            'cart_items.price',
            'cart_items.discount_price',
            'cart_items.cart_id',
            'cart_items.item_id',
            'addresses.latitude as user_latitude',
            'addresses.longitude as user_longitude'
            //            , 'shop_data.latitude as shop_latitude', 'shop_data.longitude as shop_longitude'
            //            , 'shop_data.shipment_days'
            //            , DB::raw("6371 * acos(cos(radians(addresses.latitude))
            //                        * cos(radians(shop_data.latitude))
            //                        * cos(radians(shop_data.longitude) - radians(addresses.longitude))
            //                        + sin(radians(addresses.latitude))
            //                        * sin(radians(shop_data.latitude))) AS distance")
        )
            ->join('users', 'cart_items.user_id', 'users.id')
            ->join('products', 'products.id', 'cart_items.item_id')
            ->leftJoin('addresses', 'addresses.user_id', 'users.id')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            //            ->selectRaw('(SELECT (customized_shipping_rates.shipment) FROM customized_shipping_rates
            //             WHERE sum((cart_items.price*cart_items.quantity)-cart_items.discount_price) >= customized_shipping_rates.from
            //               AND sum((cart_items.price*cart_items.quantity)-cart_items.discount_price) <= customized_shipping_rates.to
            //                and customized_shipping_rates.user_id=cart_items.shop_id ) as shipment_offer')
            ->where('cart_items.shipment_id', 0)
            ->where('cart_items.user_id', $user->id)
            ->where('addresses.is_home', 1)
            ->where('addresses.is_archived', 0)
            //            ->where('shop_data.stop', 0)
            ->where(function ($query) use ($order_id) {
                if ($order_id) {
                    $query->where('cart_items.order_id', $order_id);
                }
            })
            ->with('product')
            ->get();
        //        $settings = Settings::where('option_name', 'tax_fees')->first();

        CartItemResource::using(['min_distance' => 20, 'tax' => @$tax, 'order_id' => $order_id]);
        //        return CartResources::collection($cart_items);
        return CartItemResource::collection($cart_items);
    }

    public static function check_coupon($request, $user)
    {
        $results = (new self)->check_coupon_actions($request, $user);
        if ($results['result'] == "expired") {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('trans.Sorry, the coupon has expired'),
                ]
            );
        } elseif ($results['result'] == "used") {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('messages.coupon_used_before'),
                ]
            );
        } elseif ($results['result'] == "not_found") {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('messages.coupon_not_fount'),
                ]
            );
        } elseif ($results['result'] == "success") {
            return response()->json(
                [
                    'status' => 200,
                    'message' => __('messages.coupon_is_available'),
                    'money' => round($results['money'], 2),
                    'results' => $results
                ]
            );
        }
    }

    public static function check_coupon_actions($request, $user)
    {

        $code = Cobons::where('code', $request->code)->first();
        $result = '';
        $money = 0;
        $total = 0;
        $item_percentage = 0;
        if ($code) {
            $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($code->created_at)) . " +" . $code->days . " days"));
            if (date('Y-m-d') > $date_of_end) {
                $result = "expired";
            }

            $count_used = Orders::where('user_id', $user->id)->where('cobon', $request->code)->where('status', '<>', 5)->where('payment_method', '<>', 0)->count();

            if ($count_used) {
                $result = "used";
            } else {
                $cobon_categories = CobonsCategories::whereCobonId($code->id)->pluck('category_id')->toArray();
                $total = CartItem::where(['shipment_id' => 0, 'user_id' => $user->id])
                    ->whereHas('product', function ($query) use ($cobon_categories) {
                        //Todo: check is category or subcategory
                        $query->whereIn('subcategory_id', $cobon_categories);
                    })
                    ->select(\Illuminate\Support\Facades\DB::raw('sum((price * quantity)-discount_price) as total'))->first()->total;

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

                $item_percentage = $final_cobon_money == $final_percent_price ?
                    $percent : (($final_percent_price - $final_cobon_money) / $total) * 100;

                ////////// edited

                $result = "success";
                $money = $final_cobon_money;
            }
        } else {
            $result = "not_found";
        }
        $resp = [];
        if ($money == 0) {
            $result = "not_found";
        }
        $resp['result'] = $result;
        $resp['money'] = (float)$money;
        $resp['percentage'] = $item_percentage;
        $resp['total'] = $total;
        $resp['coupon'] = $code;
        return $resp;
    }

    public static function addOrder($request, $user, $platform = 'api')
    {

        $handDeliveryCost = floatval(Settings::where('option_name', 'hand_delivery_fees')->first()->value);
        if ($platform == 'website') {
            $cart_count = auth('client')->user()->cart->count();
            if ($cart_count == 0) {
                return redirect('/');
            }
            $address = Addresses::where('user_id', $user->id)->where('is_home', 1)->where('is_archived', 0)->first();
        } else {
            $address = Addresses::where('id', $request->address_id)->where('user_id', $user->id)->where('is_archived', 0)->first();
        }
        if (!$address) {
            if ($platform == 'website') {
                return redirect('/addresses');
            }
            return \response()->json([
                'status' => 400,
                'message' => 'العنوان غير متاح'
            ]);
        }
        $order = Orders::where('user_id', $user->id)->where('payment_method', 0)->first();
        if (!$order) {
            $order = new Orders();
        }
        $order->user_id = $user->id;
        $order->address_id = $address->id;
        $order->save();
        CartItem::where('user_id', $user->id)->where('shipment_id', 0)
            ->update(['order_id' => $order->id]);
        $settings = Settings::where('option_name', 'tax_fees')->first();
        $tax = @$settings->value;
        $handDeliveryCost = $handDeliveryCost + ($handDeliveryCost * $tax / 100);

        if ($platform == 'website') {
            $balance = $user->calcAllBalance();
            //            return $handDeliveryCost;
            return view('website.payment_method', compact('order', 'balance', 'handDeliveryCost'));
        }


        return \response()->json([
            'status' => 200,
            'order_id' => $order->id,
            'is_payment' => (int)Settings::find(51)->value,
            'balance' => (float)number_format((float)$user->calcAllBalance(), 2, '.', ''),
            'handDeliveryCost' => (string)$handDeliveryCost,
            'message' => __('trans.The order has been created successfully')
        ]);
    }

    public static function sendOrder($request, $user, $platform = 'api')
    {
        $settings = Settings::where('option_name', 'tax_fees')->first();
        $tax = @$settings->value;
        $order_id = $request->order_id;
        $objects = (new self)->summary_objects($tax, $order_id, $user);
        if ($platform == 'website') {
            $cart_count = auth('client')->user()->cart->count();
            if ($cart_count == 0) {
                return redirect()->to(url('/cart'))->with('message', 'تم ارسال الطلب سابقا');
                return \response()->json([
                    'status' => 401,
                    'message' => "تم ارسال الطلب سابقا ."
                ], 200);
            }
            $address = Addresses::where('user_id', $user->id)->where('is_archived', 0)->where('is_home', 1)->first();
            if (!$address) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'العنوان غير متاح'
                ]);
            }
        }
        //adding new line
        $code = $request->code;
        $coupon_money = 0;
        $coupon_percentage = 0;
        $coupon = null;
        if ($code) {
            $coupon_result = (new self)->check_coupon_actions($request, $user);
            if ($coupon_result['result'] == 'success') {
                $coupon_money = $coupon_result['money'];
                $coupon_percentage = $coupon_result['percentage'];
                $coupon = $coupon_result['coupon'];
            }
        }
        $sendOrder = false;
        switch ($request->payment_type) {
            case "hand_delivery":
                $handDeliveryCost = floatval(Settings::where('option_name', 'hand_delivery_fees')->first()->value);
                $sendOrder = (new self)->prepareOrder($request, $objects, $tax, $coupon, $coupon_money, $handDeliveryCost, 1, $user, $coupon_percentage);
                break;
            case "balance":
                $total_cost = (new self)->getSummaryCost($objects, $tax);
                $total_cost = $total_cost - $coupon_money;

                $user_balance = $user->calcAllBalance();
                if ($user_balance < $total_cost) {

                    return \response()->json([
                        'status' => 400,
                        'message' => __('trans.The balance is not enough to complete the order')
                    ]);
                }
                $sendOrder = (new self)->prepareOrder($request, $objects, $tax, $coupon, $coupon_money, 0, 3, $user, $coupon_percentage);
                if ($sendOrder) {
                    $new_balance = new Balance();
                    $new_balance->user_id = $user->id;
                    $new_balance->price = -1 * $total_cost;
                    $new_balance->order_id = $order_id;
                    $new_balance->balance_type_id = 2;
                    $new_balance->notes = 'شراء منتجات من التطبيق ';
                    $new_balance->save();
                }
                break;
            case "payment":
                if ($request->has('checkout_id') && $request->has('type')) {
                    $result = HyperPayRepository::validateCheckout($request->checkout_id, $request->type);
                    if ($result) {
                        $sendOrder = (new self)->prepareOrder($request, $objects, $tax, $coupon, $coupon_money, 0, 2, $user, $coupon_percentage);
                    }
                }
                break;
            case "tabby":
                $sendOrder = (new self)->prepareOrder($request, $objects, $tax, $coupon, $coupon_money, 0, 7, $user, $coupon_percentage);
                break;
        }
        if (!$sendOrder) {
            if ($platform == 'website') {
                return redirect(App::getLocale() . '/cart/')->with('error', __('trans.Order not sent'));

                //                return 'حدث خطأ غير متوقع أثناء اجراء الطلب';
            } else if ($platform == 'webview') {
                return false;
            }
            return \response()->json([
                'status' => 400,
                'message' => __('trans.Order not sent')
            ], 201);
        }

        /**/
        $order = Orders::whereId($order_id)->with('getUser', 'address', 'cart_items.product:id,title,title_en', 'paymentMethod', 'orderStatus')->first();
        foreach ($order->cart_items as $cart_item) {
            $shop_product = Products::where('id', $cart_item->item_id)->first();
            if ($shop_product) {
                $shop_product->quantity = $shop_product->quantity - $cart_item->quantity;
                $shop_product->save();
            }
        }
        if ($user->active_email == 1) {
            try {
                UtilsRepository::sendEmail(
                    $user,
                    __('trans.New order'),
                    'emails.order',
                    compact('order')
                );
            } catch (\Exception $ex) {
            }
        }
        if ($user->active_phone == 1) {
            $smsMessage = 'عميلنا العزيز
(' . $user->username . ')
طلبك رقم ' . $order->id .
                ' في حالة ' . @$order->orderStatus->name . '
';
            $phone_number = '966' . ltrim($user->phone, '0');
            $resp = AuthRepository::send4SMS($smsMessage, $phone_number);
        }

        //        $smsMessage = 'قام العميل ' . $user->username . ' بإنشاء طلب رقم ' . $order->id;
        //        $phone_number = '966' . ltrim($order->shipment->shop->phone, '0');
        //        $resp = AuthRepository::send4SMS($smsMessage, $phone_number);
        //

        /**/


        if ($platform == 'website' && $request->payment_type == 'payment') {
            return redirect(App::getLocale() . '/thank-you?id=' . $order_id);
        } else if ($platform == 'webview' && ($request->payment_type == 'payment' || $request->payment_type == 'tabby')) {
            return true;
        }
        return \response()->json([
            'status' => 200,
            'message' => __('trans.Order sent successfully'),
            'url' => url('/order/' . $order_id),
            //            'url' => url('/thank-you'),
            'order_id' => $order_id
        ], 200);
    }

    public function processPayment($id, Request $request)
    {
        if ($request->has('oid') && $request->has('type') && $request->has('uid')) {
            $data = $request->all('oid', 'type', 'uid', 'code');
            if ($data['type'] === HyperPayRepository::VISA) {
                $data['brands'] = "VISA MASTER";
            } else if ($data['type'] === HyperPayRepository::MADA) {
                $data['brands'] = "MADA";
            }
            $data['id'] = $id;
            return view('general_layouts.payment.hyperpay_api')->with($data);
        }
        return redirect()->to(url('/api/v1/payment/status/error'));
    }

    public static function prepareOrder($request, $shipments, $tax, $code, $coupon_money, $handDeliveryCost, $order_type, $user, $coupon_percentage = 0)
    {
        $cartItemsIds = [];
        // add order data
        $order = Orders::where('id', $request->order_id)->where('user_id', $user->id)->first();
        if (!$order) return false;
        if ($code && $coupon_money) {

            /**/
            $cartItemsIds = CartItem::where(['shipment_id' => 0, 'user_id' => $user->id, 'cart_id' => 0])
                ->whereIn('item_id', function ($query) use ($code) {
                    $query->select('products.id')
                        ->from(with(new Products())->getTable())
                        //                        ->join('products', 'products.id', 'shop_products.product_id')
                        ->where(function ($query) use ($code) {
                            if ($code->link_type == "category") {
                                $query->whereIn('products.category_id', function ($query) use ($code) {
                                    $query->select('cobons_categories.category_id')
                                        ->from(with(new CobonsCategories())->getTable())
                                        ->where('cobon_id', $code->id);
                                });
                            } elseif ($code->link_type == "brand") {
                                $query->whereIn('products.brand_id', function ($query) use ($code) {
                                    $query->select('cobons_brands.brand_id')
                                        ->from(with(new CobonsBrands())->getTable())
                                        ->where('cobon_id', $code->id);
                                });
                            }
                        });
                })
                ->pluck('id')->toArray();
            /**/

            $order->cobon = $code->code;
            $order->cobon_discount = $coupon_money;
        }
        $subtotal = 0;
        $vat = 0;
        $shipmentTotal = 0;
        $discounts = 0;
        $totalHandDeliveryCost = 0;
        $cart_items = collect($shipments);


        //        for ($x = 0; $x < $collect_shipments->count(); $x++) {
        // add shipment object
        $shipment_order = new OrderShipments();
        $shipment_order->user_id = $user->id;
        //            $shipment_order->shop_id = $collect_shipments[$x]['shop_id'];

        $shipping_cost = Settings::find(22);
        $shipping_cost = $shipping_cost ? intval($shipping_cost->value) : 0;
        $shipingVat = $shipping_cost * ($tax / 100);

        $shipment_order->delivery_price = $shipping_cost; //isset($collect_shipments[$x]['shipmen_price']) ? $collect_shipments[$x]['shipmen_price'] : 0;
        $shipment_order->delivery_vat = $shipingVat; // (isset($collect_shipments[$x]['shipmen_price']) ? $collect_shipments[$x]['shipmen_price'] : 0) * ($tax / 100);
        $handDeliveryVat = 0;
        if ($order_type == 1) {
            $shipment_order->hand_delivery_fees = $handDeliveryCost;
            $totalHandDeliveryCost += $handDeliveryCost;
            $handDeliveryVat = $handDeliveryCost * $tax / 100;
        }

        $shipment_subtotal = 0;
        $shipment_vat = 0;
        $shipment_discount = 0;
        $shipmentTotal = $shipping_cost; //(isset($collect_shipments[$x]['shipmen_price']) ? $collect_shipments[$x]['shipmen_price'] : 0);
        //            $cart_items = collect($collect_shipments[$x]['cart_items']);
        for ($y = 0; $y < $cart_items->count(); $y++) {
            $subtotal += floatval($cart_items[$y]['summary_sub_total']);

            $shipment_subtotal += floatval($cart_items[$y]['summary_sub_total']);

            $vat += floatval($cart_items[$y]['price_vat']);
            $shipment_vat += floatval($cart_items[$y]['price_vat']);

            //
            $cartCouponDiscount = 0;
            if (in_array($cart_items[$y]['id'], $cartItemsIds)) {
                $cartCouponDiscount = (($cart_items[$y]['price'] * $cart_items[$y]['quantity']) - $cart_items[$y]['discount_price']) * ($coupon_percentage / 100);
                CartItem::where(['id' => $cart_items[$y]['id']])->update(['cobon_discount' => $cartCouponDiscount]);
            }
            //
            $discounts += floatval($cart_items[$y]['discount_price']);
            //                $discounts += floatval(@$cart_items[$y]['motivation_discount'] ? @$cart_items[$y]['motivation_discount'] : 0);
            $discounts += $cartCouponDiscount;
            //                $shipment_discount += floatval($cart_items[$y]['discount_price']) + floatval($cart_items[$y]['motivation_discount']) + $cartCouponDiscount;
            $shipment_discount += floatval($cart_items[$y]['discount_price']) + $cartCouponDiscount;
        }

        $shipment_order->subtotal_price = $shipment_subtotal;
        $shipment_shipping_vat = $shipingVat; //$collect_shipments[$x]['shipmen_price'] * $tax / 100;
        Log::info([$shipingVat, $shipment_vat, $shipment_shipping_vat, $handDeliveryVat]);
        $shipment_order->taxes = $shipingVat + $shipment_vat + $handDeliveryVat;
        $shipment_order->discounts = $shipment_discount;
        $shipment_order->final_price = $shipment_subtotal + $shipment_vat + $shipingVat  + $shipping_cost - $shipment_discount + $handDeliveryCost + $handDeliveryVat;
        $shipment_order->shipment_type = 2; //$collect_shipments[$x]['is_express'] == 1 ? 1 : 2;
        $shipment_order->order_id = $order->id;
        $shipment_order->status = 1;
        $shipment_order->delivery_days = 3; //$collect_shipments[$x]['shipment_days'];
        $shipment_order->save();
        $shipment_order->short_code = $shipment_order->id . str_random(4);
        $shipment_order->save();

        //send notifications to pharmacy and moderators

        @$notificationJobs = new SendPushNotification(
            $user->id,
            1,
            'طلب جديد',
            'New order',
            'طلب جديد #' . $shipment_order->order_id . ' من ' . $user->username,
            'New order from #' . $shipment_order->order_id . ' ' . $user->username,
            3,
            $shipment_order,
            '/admin-panel/orders',
            true,
            508
        );
        @(new self)->dispatch($notificationJobs);
        /**/

        CartItem::where('user_id', $user->id)->where('order_id', $order->id)
            ->where('shipment_id', 0)
            //                ->where('shop_id', $collect_shipments[$x]['shop_id'])
            ->update(['shipment_id' => $shipment_order->id]);
        //        }
        $shipingVat = $shipmentTotal * $tax / 100;
        $totalHandDeliveryVat = $totalHandDeliveryCost * $tax / 100;
        $total = $subtotal + $vat + $shipmentTotal + $shipingVat + $handDeliveryCost + $totalHandDeliveryVat - $discounts;

        Log::info([$vat, $shipingVat, $totalHandDeliveryVat]);

        $order->delivery_price = $shipmentTotal;
        $order->order_price = $subtotal;
        $order->taxes = $vat + $totalHandDeliveryVat + $shipingVat;
        $order->final_price = $total;
        $order->payment_method = $order_type;
        $order->discounts = $discounts;
        $order->hand_delivery_fees = $totalHandDeliveryCost;
        $order->status = 1;
        if ($request->payment_type === 'tabby') {
            $order->tabby_payment_id = $request->payment_id;
        }
        $order->save();
        return true;
    }

    public static function getSummaryCost($shipments, $tax)
    {
        $subtotal = 0;
        $vat = 0;
        $shipmentTotal = 0;
        $discounts = 0;
        $cart_items = collect($shipments);

        //    dd(collect($collect_shipments[0]['shipmen_price']));

        $shipping_cost = Settings::find(22);
        $shipping_cost = $shipping_cost ? intval($shipping_cost->value) : 0;
        //        for ($x = 0; $x < $collect_shipments->count(); $x++) {
        $shipmentTotal = $shipping_cost; //$collect_shipments[$x]['shipmen_price'];
        //            $cart_items = collect($collect_shipments[$x]['cart_items']);
        for ($y = 0; $y < $cart_items->count(); $y++) {
            $subtotal += floatval($cart_items[$y]['summary_sub_total']);

            $vat += floatval($cart_items[$y]['price_vat']);
            $discounts += floatval($cart_items[$y]['discount_price']);
        }
        //        }
        $shipingVat = $shipmentTotal * $tax / 100;
        $total = $subtotal + $vat + $shipmentTotal - $discounts;
        //        dd($subtotal.'-'.$vat.'-'.$shipmentTotal.'-'.$shipingVat.'-'.$discounts);
        return $total;
    }


    public static function myOrders($request, $user, $platform = 'api')
    {
        $lang = App::getLocale();

        $select_status = $lang == 'ar' ? 'order_status.name' : 'order_status.name_en as name';

        $objects = (new self)->orderObjects($request, $user)
            ->orderBy('id', 'desc')
            ->where('payment_method', '!=', 0)
            ->paginate(15);
        if ($platform == 'website') {
            //            return $objects;
            return view('website.orders', compact('objects'));
        }
        return response()->json(
            [
                'data' => $objects
            ]
        );
    }

    public static function shipmentDetails($request, $user, $platform = 'api')
    {
        if ($platform == 'website') {
            $object = (new self)->orderObjects($request, $user)
                ->where('orders.id', $request->order_id)
                ->first();
            $select_name = App::getLocale() == 'ar' ? 'name' : 'name_en as name';
            $cancel_reasons = CancellationReason::select('id', $select_name)->get();
            //            return $object;
            if (\auth('client')->id() == 429) {
                //                return $object;
            }
            return view('website.single-order', compact('object', 'cancel_reasons'));
        }
        $objects = (new self)->orderObjects($request, $user)
            ->where('orders.id', $request->order_id)
            ->limit(1)->get();
        //        $objects = OrderShipments::
        //                select('order_shipments.order_id', 'order_shipments.id', 'order_shipments.status','order_shipments.delivery_price','order_shipments.taxes', 'order_shipments.shop_id', $supplier_name, $select_status,'order_shipments.shipment_type',DB::raw("date(order_shipments.created_at)as created_date"),'order_shipments.delivery_days')
        //                    ->selectRaw('(CASE WHEN user_data.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . url("/") . '/uploads/", user_data.photo)) END) AS shop_photo')
        //                    ->leftJoin('order_status', 'order_shipments.status', 'order_status.id')
        //                    ->join('users', 'users.id', 'order_shipments.shop_id')
        //                    ->join('user_data', 'user_data.user_id', 'users.id')
        //            ->with(['cart_items'=> function ($query) use ($select_status, $lang) {
        //                $select_title = $lang == "ar" ? 'products.title' : 'products.title_en as title';
        //
        //                $query->select('cart_items.id'
        //                    , 'cart_items.item_id'
        //                ,'cart_items.shop_id'
        //                    , 'shop_products.product_id'
        //                    , 'products.category_id'
        //                    , 'cart_items.quantity'
        //                    , $select_title
        //                    , 'cart_items.offer_id'
        //                    , 'cart_items.price'
        //                    , 'cart_items.discount_price'
        //                    , 'cart_items.cart_id',
        //                    'cart_items.shipment_id',
        //                    $select_status
        //                )
        //
        //                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
        //                    ->leftJoin('order_status', 'cart_items.status', 'order_status.id')
        //                    ->join('shop_products', 'shop_products.id', 'cart_items.item_id')
        //                    ->join('products', 'shop_products.product_id', 'products.id');
        //
        //            }])
        //            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.shipment_id = order_shipments.id) as product_count')
        //
        //            ->where('order_shipments.user_id', $user->id)
        //            ->where('order_shipments.order_id', $request->order_id)

        //            ->get();

        return response()->json(
            [
                'data' => $objects
            ]
        );
    }

    public static function orderObjects($request, $user)
    {
        $lang = App::getLocale();

        $select_status = $lang == 'ar' ? 'order_status.name' : 'order_status.name_en as name';
        $supplier_name = $lang == "ar" ? 'user_data.user_name as shop_name' : 'user_data.user_name_en as shop_name';
        $select_status = $lang == 'ar' ? 'order_status.name' : 'order_status.name_en as name';
        $current = Carbon::now();

        $trialExpires = $current->addDays(1);
        $objects = Orders::select('orders.id', 'orders.final_price', 'orders.status', 'orders.discounts', $select_status, DB::raw("date(orders.created_at)as created_date"))
            ->leftJoin('order_status', 'orders.status', 'order_status.id')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.order_id = orders.id) as shipments_count')
            ->with(['shipments' => function ($query) use ($select_status, $lang) {
                //                $supplier_name = $lang == "ar" ? 'user_data.user_name as shop_name' : 'user_data.user_name_en as shop_name';
                $query->select(
                    'order_shipments.order_id',
                    'order_shipments.id',
                    'order_shipments.status',
                    'order_shipments.short_code',
                    'order_shipments.delivery_price',
                    'order_shipments.final_price',
                    'order_shipments.hand_delivery_fees',
                    'order_shipments.taxes',
                    'order_shipments.shop_id',
                    $select_status,
                    'order_shipments.shipment_type',
                    DB::raw("date(order_shipments.created_at)as created_date"),
                    'order_shipments.delivery_days',
                    DB::raw('CONCAT ("' . url("/") . '/invoice-print/", order_shipments.short_code) AS bill_url')
                )
                    //                    ->selectRaw('(CASE WHEN user_data.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . url("/") . '/uploads/", user_data.photo)) END) AS shop_photo')
                    ->leftJoin('order_status', 'order_shipments.status', 'order_status.id');
                //                    ->join('users', 'users.id', 'order_shipments.shop_id')
                //                    ->join('user_data', 'user_data.user_id', 'users.id');
            }, 'shipments.cart_items' => function ($query) use ($select_status, $lang, $user) {
                $select_title = $lang == "ar" ? 'products.title' : 'products.title_en as title';
                $current = Carbon::now();
                $prevDay = date('Y-m-d', (strtotime('-1 day', strtotime($current))));

                $trialExpires = $current->addDays(1);

                $query->select(
                    'cart_items.id',
                    'cart_items.item_id',
                    'cart_items.shop_id',
                    'products.id as product_id',
                    'products.category_id',
                    'cart_items.quantity',
                    $select_title,
                    'products.title_en',
                    'cart_items.offer_id',
                    'cart_items.price',
                    'cart_items.price_vat',
                    'cart_items.discount_price',
                    'cart_items.discount_vat',
                    'cart_items.cart_id',
                    'cart_items.shipment_id',
                    'cart_items.status',
                    $select_status,
                    'shop_offers.type_id as offer_type_id'
                )
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
                    ->selectRaw('(SELECT count(*) FROM retrieval_items WHERE retrieval_items.cart_id = cart_items.id) as return_sent')
                    ->selectRaw('(CASE WHEN DATE(cart_items.updated_at) >= DATE("' . $prevDay . '") and  DATE(cart_items.updated_at) <= DATE("' . $trialExpires . '") THEN 1  ELSE 0 END) AS can_return1')
                    ->selectRaw('(SELECT count(*) FROM product_ratings WHERE product_ratings.user_id =' . $user->id . ' 
                    AND product_ratings.item_id=products.id) as is_rated')

                    //                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE
                    //             gift_items.cart_id = cart_items.id) as gift_count')
                    ->leftJoin('order_status', 'cart_items.status', 'order_status.id')
                    ->leftJoin('shop_offers', 'shop_offers.id', 'cart_items.offer_id')
                    //                    ->leftJoin('cart_items as gift_items', 'gift_items.cart_id', 'cart_items.id')

                    ->join('products', 'products.id', 'cart_items.item_id');
                //                    ->join('products', 'shop_products.product_id', 'products.id');

            }])
            ->where('orders.user_id', $user->id);
        return $objects;
    }

    public static function addProductRating($request, $user)
    {


        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
            'rate' => 'required',
            'comment' => 'required',
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
        $product = Products::select('products.id', 'order_shipments.order_id')
            ->selectRaw('(SELECT count(*) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.user_id=' . $user->id . ' ) as product_rate')
            // ->join('products', 'products.id', 'products.product_id')
            ->join('cart_items', 'cart_items.item_id', 'products.id')
            ->join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->where('cart_items.status', 7)
            ->where('order_shipments.user_id', $user->id)
            ->where('products.id', $request->product_id)
            ->whereNotIn('products.id', function ($query) use ($user) {
                $query->select('item_id')
                    ->from(with(new ProductRating())->getTable())
                    ->where('user_id', $user->id);
            })
            ->first();
        //        return response()->json($product);
        if (!$product) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "قمت بتقييم هذا المنتج سابقا ",
                ],
                400
            );
        }
        if ($product->product_rate > 0) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "قمت بتقييم هذا المنتج من قبل ",
                ],
                400
            );
        }
        $rate = new ProductRating();
        $rate->user_id = $user->id;
        $rate->rate = $request->rate;
        $rate->item_id = $product->id;
        $rate->type = 1;
        $rate->comment = $request->comment ?: '';
        $rate->save();
        $order_details = (new self)->orderObjects($request, $user)
            ->where('orders.id', $product->order_id)
            ->limit(1)->get();


        return response()->json(
            [
                'status' => 200,
                'order' => $order_details,
                'message' => __('trans.The product has been rated successfully'),
            ],
            200
        );
    }

    public static function cancelOrderItem($request, $user)
    {
        $object = CartItem::select(
            'cart_items.id',
            'cart_items.shipment_id',
            'cart_items.item_id',
            'cart_items.cobon_discount',
            'cart_items.motivation_discount',
            'cart_items.type',
            'order_shipments.status',
            'order_shipments.shop_id',
            'orders.payment_method',
            'cart_items.price',
            'cart_items.quantity',
            'orders.id as order_id'
        )
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE 
             cart_items.cart_id = ' . $request->item_id . ') as gift_count')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.shipment_id =order_shipments.id and cart_items.status = 5) as items_canceled ')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.shipment_id =order_shipments.id) as items_count ')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.order_id =orders.id) as shipments_count')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.order_id =orders.id and order_shipments.status = 5) as shipments_canceled')
            ->selectRaw('(SELECT sum((cart_items.price * cart_items.quantity)+(cart_items.price_vat)-(cart_items.discount_price)-(cart_items.cobon_discount)) FROM cart_items
              WHERE cart_items.id =' . $request->item_id . ' or cart_items.cart_id=' . $request->item_id . ') as return_price')
            ->where('cart_items.id', $request->item_id)
            ->join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->join('orders', 'orders.id', 'order_shipments.order_id')
            ->where('order_shipments.user_id', $user->id)
            ->where('order_shipments.status', 1)
            ->where('cart_items.cart_id', 0)
            ->first();
        $return_price = $object ? $object->return_price : 0;
        //        return response()->json($object);
        if ($object) {
            $object->status = 5;
            $object->save();
            if ($object->gift_count > 0) {
                CartItem::where('cart_id', $object->id)->update(['status' => 5]);
            }
            $shipment_order = OrderShipments::select('order_shipments.*', 'orders.cobon_discount')->where('order_shipments.id', $object->shipment_id)
                ->join('cart_items', 'order_shipments.id', 'cart_items.shipment_id')
                ->join('orders', 'orders.id', 'order_shipments.order_id')
                ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.shipment_id =' . $object->shipment_id . ' and cart_items.status = 5) as items_canceled ')
                ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.shipment_id =' . $object->shipment_id . ') as items_count ')
                ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.order_id =' . $object->order_id . ') as shipments_count')
                ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.order_id =' . $object->order_id . ' and order_shipments.status = 5) as shipments_canceled')
                ->first();
            //                    return response()->json($shipment_order);

            if ($shipment_order->items_canceled === $shipment_order->items_count) {
                $shipment_order->status = 5;
                $shipment_order->save();
                $return_price = $return_price + $shipment_order->delivery_price;
                $return_price = $return_price + $shipment_order->delivery_vat;
                if ($shipment_order->shipments_count === ($shipment_order->shipments_canceled + 1)) {
                    Orders::where('id', $shipment_order->order_id)->update(['status' => 5]);
                    $return_price = $return_price - $shipment_order->cobon_discount;
                }
            }
            if ($object->payment_method == 2 || $object->payment_method == 3) {
                $add_balance = new Balance();
                $add_balance->user_id = $user->id;
                $add_balance->price = $return_price;
                $add_balance->balance_type_id = 6;
                $add_balance->order_id = $object->order_id;
                $add_balance->save();
                $product = Shop_product::find($object->item_id);
                if ($product) {
                    $product->quantity = $product->quantity + $object->quantity;
                    $product->save();
                }
            }
            $notify_message = 'قام المستخدم ' . $user->username . ' بالغاء طلب منتج من الطلب رقم ' . $object->shipment_id;
            $notify_message_en = 'user  ' . $user->username . ' canceled product from his order no: ' . $object->shipment_id;

            SendPushNotification::dispatch(
                $user->id,
                $object->shop_id,
                'إلغاء الطلب',
                'cancel order',
                $notify_message,
                $notify_message_en,
                14,
                [],
                "/pharmacy-panel/order-details/" . $object->shipment_id
            );

            $order_details = (new self)->orderObjects($request, $user)
                ->where('orders.id', $object->order_id)
                ->limit(1)->get();

            return \response()->json([
                'status' => 200,
                'order' => $order_details,
                'balance' => $user->calcAllBalance(),
                'message' => __('trans.The product has been cancelled')
            ]);
        } else {
            return \response()->json([
                'status' => 400,
                'message' => 'حدث خطأ '
            ]);
        }
    }

    public static function returnOrderItem($request, $user)
    {
        $object = CartItem::select(
            'cart_items.id',
            'cart_items.shipment_id',
            'cart_items.item_id',
            'cart_items.type',
            'order_shipments.status',
            'order_shipments.shop_id',
            'order_shipments.shipment_company',
            'orders.payment_method',
            'cart_items.price',
            'cart_items.quantity',
            'orders.id as order_id'
        )
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.cart_id = ' . $request->item_id . ') as gift_count')
            ->where('cart_items.id', $request->item_id)
            ->join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->join('orders', 'orders.id', 'order_shipments.order_id')
            ->where('order_shipments.user_id', $user->id)
            ->where('order_shipments.status', 7)
            ->whereNotIn('cart_items.id', function ($query) {
                $query->select('cart_id')
                    ->from(with(new RetrievalItem())->getTable());
            })
            ->first();
        if (!$object) {
            return \response()->json([
                'status' => 400,
                'message' => 'حدث خطأ '
            ]);
        }
        //        $gift_arr=[];
        //        if($object->gift_count >0){
        //            $giftObjects=CartItem::where('cart_id',$object->id)->get();
        //            foreach ($giftObjects as $gift){
        //                $gift_arr[] = (array) [
        //                    'cart_id' => $gift->id
        //                    ,'cancel_id'=>$request->cancel_id
        //                    ,'cancel_reason'=>$request->cancel_reason?:""
        //                    ,'quantity'=>$object->quantity
        //                ];
        //            }
        ////            RetrievalItem::insert($gift_arr);
        //        }
        // dd($gift_arr);
        // order
        $retrievalOrder = new RetrievalOrder();
        $retrievalOrder->user_id = $user->id;
        $retrievalOrder->shipment_id = $object->shipment_company;
        $retrievalOrder->cancel_id = $request->cancel_id ?: "";
        $retrievalOrder->cancel_reason = $request->cancel_reason ?: "";
        $retrievalOrder->status = 1;
        $retrievalOrder->order_id = $object->shipment_id; //shipment id
        $retrievalOrder->save();
        // items
        $retrieval = new RetrievalItem();
        $retrieval->cart_id = $object->id;
        $retrieval->cancel_id = $request->cancel_id ?: "";
        $retrieval->cancel_reason = $request->cancel_reason ?: "";
        $retrieval->shipment_id = $object->shipment_company; //company shipment id
        $retrieval->status = 1;
        $retrieval->quantity = $object->gift_count > 0 ? $object->quantity : $request->quantity;
        //        $retrieval->quantity = $object->gift_count > 0 ? $object->quantity : ($request->quantity ?: 0);
        $retrieval->order_id = $retrievalOrder->id;
        $retrieval->save();
        $retrieval = RetrievalItem::find($retrieval->id);
        $retrievalOrder->final_price = ($retrieval->cart_item->price * $retrieval->quantity) + $retrieval->cart_item->price_vat - $retrieval->cart_item->discount_price;

        if ($object->gift_count > 0) {
            $gift_arr = [];
            $giftObjects = CartItem::where('cart_id', $object->id)->get();
            foreach ($giftObjects as $gift) {
                $gift_arr[] = (array)[
                    'cart_id' => $gift->id,
                    'is_gift' => 1, 'cancel_id' => $request->cancel_id ?: "", 'cancel_reason' => $request->cancel_reason ?: "", 'order_id' => $retrievalOrder->id, 'status' => 1, 'shipment_id' => $object->shipment_company, 'quantity' => $object->quantity
                ];
            }
            RetrievalItem::insert($gift_arr);
        }
        $order_details = (new self)->orderObjects($request, $user)
            ->where('orders.id', $object->order_id)
            ->limit(1)->get();

        $notify_message = 'قام المستخدم ' . $user->username . ' بطلب مرتجع لمنتج من الطلب رقم ' . $object->shipment_id;
        $notify_message_en = 'user  ' . $user->username . ' has Returning a product from order No: ' . $object->shipment_id;

        SendPushNotification::dispatch(
            $user->id,
            $object->shop_id,
            'طلب مرتجع',
            'returning order',
            $notify_message,
            $notify_message_en,
            14,
            [],
            ''
        );

        return \response()->json([
            'status' => 200,
            'order' => $order_details,
            'message' => __('trans.Return request has been sent')
        ]);
    }

    public static function cancel_reasons()
    {
        $select_name = App::getLocale() == 'ar' ? 'name' : 'name_en as name';

        $objects = CancellationReason::select('id', $select_name)->get();
        return response()->json($objects);
    }

    public static function like_product($request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $user = auth('client')->user();
        $fav = Favorite::where('user_id', $user->id)
            ->where('item_id', $request->item_id)
            ->where('type', 0)->first();
        if (!$fav && $request->type == 'like') {
            $like = new Favorite();
            $like->item_id = $request->item_id;
            $like->user_id = $user->id;
            $like->type = 0;
            $like->save();
            return response()->json([
                'message' => __('messages.Liked successfully'),

            ]);
        } elseif ($fav && $request->type == 'unlike') {
            $fav->delete();
            return response()->json([
                'message' => __('messages.like_deleted_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'can not action',
            ]);
        }
    }
}
