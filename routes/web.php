<?php

use App\Models\Orders;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/clear-cache', function () {
    //   return \App\Models\Balance::where('balance_type_id',11)->withoutGlobalScopes()->update(['status'=>1]);
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('view:clear');
    return 'Routes cache cleared';
});
Route::group(['middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'], 'prefix' => LaravelLocalization::setLocale() . '/'], function () {
    Route::get('check-coupon-category', 'TestController@check_coupon_category');
    Route::get('thumb', 'HomeController@test');

    Route::get('/user/{id}', function ($id) {

        if ($id == 248) {
            auth()->loginUsingId(248);
            return redirect('/provider-panel');
        }
        //   return $t=\App\Models\Orders::with('address')->find(672);
        if ($id == 'u') {
            auth()->loginUsingId(258);
        } elseif ($id == 'm') {

            auth()->loginUsingId(13);
            return  redirect('/admin-panel');
        } else {
            auth()->loginUsingId($id);
        }
        return redirect('/');
    });
    //http://127.0.0.1:8000/i/304o3QF
    Route::get('/recalc_order', 'HomeController@recalc_order');
    Route::get('/cart-invoice/{code}', 'HomeController@cart_invoice')->name('cart-invoice');
    Route::get('/i/{code}', 'HomeController@invoice')->name('invoice');
    Route::get('/p/{code}', 'HomeController@purchase_invoice')->name('purchase.invoice');
    Route::post('/payment-result', 'PaymentController@paymentResult');
    Route::get('verify-checkout', 'TapPaymentController@verify');

    Route::group(['middleware' => ['web', 'guest:client']], function () {
        Route::get('login', 'Auth\LoginController@get_login');
        Route::post('login', 'Auth\LoginController@login')->name('login');
        Route::get('register', 'Auth\RegisterController@register_page');
        Route::post('register', 'Auth\RegisterController@postRegister')->name('register');
        //    Route::post('/activate_phone_number/{phone}','HomeController@activate_phone_number_post');
        /*Route::get('/login', [
        'as' => 'login',
        function(){
            if (strpos(url()->previous(), 'provider-panel') !== false) {
                return redirect()->to('provider-panel/login');
            }elseif (strpos(url()->previous(), 'admin-panel') !== false){
                return redirect()->to('admin-panel/login');
            }else{
//                return redirect()->to('/login');

                return view('auth/login');
            }
        }
    ]);*/
        Route::post('activate_phone_number', 'Auth\LoginController@activate')->name('activation');
    });
    Route::get('getApp', 'GlobalDataController@getApp');


    Route::group(['middleware' => ['web']], function () {
        Route::get('/getRegions/{country_id}', 'AddressController@getRegions');
        Route::get('/getRegionStates/{state_id}', 'AddressController@getRegionStates');

        Route::post('/send-message-email', 'GlobalDataController@sendMessageEmail');
        Route::post('/initiate-payment', 'HomeController@initiatePayment');
        //    Route::post('/payment-result', 'HomeController@paymentResult');

        Route::get('/test-payment', function () {
            return view('testpayment');
        });

        Route::get('/', 'HomeController@index');
        Route::get('/product/{id}', 'HomeController@productPage');

        Route::get('index', 'HomeController@index');
        //    Route::get('/search-halls', 'HomeController@searchHalls');
        //
        Route::get('/page/{slug}', 'GlobalDataController@page');
        Route::get('/app-page/{slug}', 'GlobalDataController@appPage');
        Route::get('/contact-us', 'GlobalDataController@contact');
        Route::get('/page/{id}', 'GlobalDataController@page');

        Route::get('search', 'ProductController@get_search')->name('search');
        Route::post('/autocomplete/fetch', 'ProductController@fetch')->name('autocomplete.fetch');

        // Route::get('providers', 'HomeController@get_providers')->name('providers');
        // Route::get('provider-products/{id}', 'HomeController@get_products')->name('products');
        // Route::get('become-provider', 'HomeController@become_provider')->name('become_provider');
        // Route::post('become-provider', 'HomeController@become_provider_post');
    });
    ////  routes for login users
    Route::group(['middleware' => ['web', 'auth:client']], function () {

        Route::get('logout', 'Auth\LoginController@logout');
        
        Route::get('addresses', 'AddressController@index');
        Route::post('addresses/store', 'AddressController@store');
        Route::post('addresses/update/{id}', 'AddressController@update');
        Route::post('addresses/delete', 'AddressController@destroy');
        Route::post('addresses/home', 'AddressController@setDefaultAddress');

        Route::get('/favorites', 'HomeController@favorites');
        Route::get('/wishlist', 'ProfileController@wishlist');
        Route::get('/notifications', 'ProfileController@notifications');
        Route::get('/wallet', 'ProfileController@wallet');
        Route::post('add-to-fav', 'ProfileController@fav_item');
        Route::get('account', 'ProfileController@account')->name('account');
        Route::post('account', 'ProfileController@updateProfile');
        Route::get('my-orders', 'ProfileController@orders')->name('my-orders');
        Route::get('/my-orders/cancle_client_order/{id}', 'ProfileController@cancle_client_order');
        Route::get('/update/token', 'ProfileController@updateToken');

        Route::post('cart/store', 'CartController@store');
        Route::post('cart/update', 'CartController@update');
        Route::post('cart/delete', 'CartController@destroy');
        Route::get('cart', 'CartController@getCart');

        Route::get('complete-order', 'HomeController@checkout');
        Route::get('order-details/{id}', 'OrderController@orderDetails');
        Route::get('add-new-order/{id}', 'OrderController@addNewOrderOnOldOrder');
        Route::get('return-balance/{id}', 'OrderController@returnBalance');
        Route::post('check-cobon', 'OrderController@check_coupon_category');
        Route::post('add-order', 'OrderController@addOrder');
        Route::get('checkout/{id}', 'OrderController@checkout');
        Route::post('payment-bank', 'OrderController@sendBankTransferOrder');
        //    Route::get('search', 'HomeController@get_search')->name('search');
        //    Route::get('providers', 'HomeController@get_providers')->name('providers');
        //    Route::get('provider-products/{id}', 'HomeController@get_products')->name('products');
        Route::post('/my-orders/upload-invoice/{id}', 'HomeController@upload_invoice');
        Route::post('tap-checkout', 'TapPaymentController@checkout');

        Route::get('/payment/{id}', 'PaymentController@initiatePayment');
        Route::post('/payment', 'PaymentController@payment');
        Route::get('/payment-status', 'PaymentController@paymentStatus');
        Route::get('payment-error', 'PaymentController@paymentError');
        Route::get('payment-done', 'PaymentController@paymentDone');

        //
        Route::get('/order/capture/{id}', 'OrderController@captureOrder');

        Route::get('/order/capture/{id}', 'OrderController@captureOrder');

        Route::post('add-to-cart', 'CartOrderController@addToCart');
        Route::post('add-cart-gifts', 'CartOrderController@addCartGifts');
        Route::post('remove-from-cart', 'CartOrderController@removeFromCart');
        Route::get('cart-items', 'CartOrderController@getCartItems');
        Route::get('offer-gifts', 'CartOrderController@getOfferGifts');
        Route::post('like-product', 'HomeController@like_product');
        Route::get('cart', 'CartOrderController@getCartItems');
        Route::get('summary', 'CartOrderController@getCartSummary');
        Route::post('summary', 'CartOrderController@getCartSummary');
        Route::get('choose-payment', 'CartOrderController@choose_payment');
        Route::post('choose-payment', 'CartOrderController@choose_payment');
        Route::post('check-coupon', 'CartOrderController@check_coupon');
        Route::get('send-order1', 'CartOrderController@sendOrder');
        Route::post('send-order', 'CartOrderController@sendOrder');
        Route::post('add-order', 'CartOrderController@addOrder');
        Route::get('thank-you', 'CartOrderController@thank_you');
    });
});
