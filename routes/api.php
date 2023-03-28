<?php


Route::group(['prefix' => 'api/v1'], function () {
    // Route::post('/initiate-payment', 'ApiController@initiatePayment');
    // Route::post('/payment-result', 'ApiController@paymentResult');


    // Route::post('return-payment', 'ApiController@returnPayment');
    // Route::get('testSmsa', 'ApiController@testSmsa');
    // Route::get('payment-error', 'ApiController@paymentError');
    // Route::get('payment_done', 'ApiController@paymentDone');

    // Route::get('get-slider', 'ApiController@get_slider');
    // Route::get('car_makes_list', 'ApiController@car_makes_list');
    // Route::get('car_make_years_list', 'ApiController@car_make_years_list');
    // Route::get('car_year_models_list', 'ApiController@car_year_models_list');

    // Route::get('contact_options', 'ApiController@getContactOptions');

    // Route::post('main-page', 'ApiController@main_page');

    // Route::get('search-products', 'ApiController@search_products');
    // Route::get('search-autocomplete', 'ApiController@search_autocomplete');
    // Route::post('product-page', 'ApiController@getProduct');
    // Route::post('product-rates', 'ApiController@getProductRates');

    // Route::post('favorite-shops', 'ApiController@favorite_shops');
    // Route::post('favorite-products', 'ApiController@favorite_products');
    // Route::post('shop-details', 'ApiController@shop_details');
    // Route::post('auto-parts', 'ApiController@getAutoParts');

    // Route::get('shop-categories', 'ApiController@shopCategories');
    // Route::get('suppliers', 'ApiController@getSuppliers');



    // //authenticated routes
    // Route::get('cart-items', 'AuthenticateController@getCartItems');

    // Route::get('cart-details', 'AuthenticateController@getCartDetails');
    // Route::get('cart-details2', 'AuthenticateController@getCartDetails2');
    // Route::get('order-details/{id}', 'OrderController@orderDetails');
    // Route::get('add-new-order/{id}', 'OrderController@addNewOrderOnOldOrder');
    // Route::get('return-balance/{id}', 'OrderController@returnBalance');

    // Route::post('add_car', 'AuthenticateController@add_car');
    // Route::post('edit_car', 'AuthenticateController@edit_car');
    // Route::post('user_cars', 'AuthenticateController@get_user_cars');
    // Route::post('delete_car', 'AuthenticateController@delete_car');

    // Route::post('add-damage', 'AuthenticateController@addDamageEstimate');
    // Route::post('damage-send-balance-order', 'AuthenticateController@damageSendBalanceOrder');
    // Route::post('damage-send-pay-order', 'ApiController@damageSendPayOrder');

    // Route::post('unlike-product', 'AuthenticateController@unlike_product');
    // Route::post('like-product', 'AuthenticateController@like_product');
    // Route::post('unlike-shop', 'AuthenticateController@unlike_shop');
    // Route::post('like-shop', 'AuthenticateController@like_shop');

    // Route::post('add-pricing', 'AuthenticateController@addPricingOrder');
    // Route::post('pricing-send-balance-order', 'AuthenticateController@pricingSendBalanceOrder');
    // Route::post('delete-pricing-part', 'AuthenticateController@deletePricingItem');
    // Route::post('pricing-send-pay-order', 'ApiController@pricingSendPayOrder');

    // Route::get('my-pricing-requests', 'AuthenticateController@getMyPricingRequests');
    // Route::get('my-damage-requests', 'AuthenticateController@getMyDamageRequests');
    // Route::post('damage-offers', 'AuthenticateController@getDamageOrderOffers');
    // Route::post('accept-damage-offer', 'AuthenticateController@acceptDamageOrderOffer');

    // Route::post('pricing-offers', 'AuthenticateController@getPricingOrderOffers');
    // Route::post('add-pricing-offer', 'AuthenticateController@addPricingOffer');
    // Route::post('show-pricing-order', 'AuthenticateController@showPricingOrder');
    // Route::post('add-damage-offer', 'AuthenticateController@addDamageOffer');
    // Route::post('show-damage-order', 'AuthenticateController@showDamageOrder');
    // Route::post('show-my-part-offer', 'AuthenticateController@showPartMyOffer');

    // Route::post('update-profile', 'AuthenticateController@update_profile');
    // Route::post('update-device-token', 'AuthenticateController@update_device_token');
    // Route::post('update-notification', 'AuthenticateController@update_notification');
    // Route::post('update-currency', 'AuthenticateController@update_currency');
    // Route::post('update-country', 'AuthenticateController@update_country');
    // Route::post('update-notification-follows', 'AuthenticateController@update_notification_follows');
    // Route::post('update-notification-messages', 'AuthenticateController@update_notification_messages');
    // Route::post('update-language', 'AuthenticateController@update_language');
    // Route::post('update-ring-tone', 'AuthenticateController@update_ring_tone');
    // Route::post('activate-phone', 'AuthenticateController@activate_phone');
    // Route::post('activate-email', 'AuthenticateController@activate_email');
    // Route::post('change-password', 'AuthenticateController@change_password');
    // Route::post('request-representative', 'AuthenticateController@request_representative');
    // Route::get('get-notifications-count', 'AuthenticateController@notification_count');
    // Route::get('notifications', 'AuthenticateController@notifications');
    // Route::post('upload-photos', 'AuthenticateController@upload_photos');

    // // cart and order
    // Route::post('add-to-cart', 'AuthenticateController@addToCart');
    // Route::post('remove-from-cart', 'AuthenticateController@removeFromCart');
    // Route::post('add-to-cart-from-local', 'AuthenticateController@addToCartFromLocal');
    // Route::post('check-quantity', 'ApiController@checkQuantity');
    // Route::get('my-orders', 'AuthenticateController@myOrders');
    // Route::get('my-orders-new', 'AuthenticateController@myOrdersNew');

    // Route::get('order-tracking', 'AuthenticateController@orderTracking');
    // Route::post('cancel-order', 'AuthenticateController@cancelOrder');

    // // cart offers
    // Route::post('add-offer-to-cart', 'AuthenticateController@addOfferToCart');

    // Route::post('add-order', 'AuthenticateController@addOrder');
    // Route::post('send-hand-order', 'AuthenticateController@sendHandOrder');
    // Route::post('send-balance-order', 'AuthenticateController@sendBalanceOrder');
    // Route::post('send-bank-transfer-order', 'AuthenticateController@sendBankTransferOrder');
    // Route::post('send-pay-later-order', 'AuthenticateController@sendPayLaterOrder');
    // Route::post('send-scheduling-order', 'AuthenticateController@sendSchedulingOrder');

    // Route::post('return-payment', 'ApiController@returnPayment');
    // Route::post('/return_url', 'ApiController@return_url');

    // Route::get('my-cobons', 'AuthenticateController@my_cobons');
    // Route::get('my-favourite-halls', 'AuthenticateController@my_favourite_halls');
    // Route::get('my-finished-halls', 'AuthenticateController@my_finished_halls');
    // Route::get('my-reserved-halls', 'AuthenticateController@my_reserved_halls');
    // Route::post('contact-us', 'ApiController@contact_us');
    // Route::post('suggestions', 'ApiController@suggestions');
    // Route::post('request-hotel', 'ApiController@request_hotel');
    // Route::post('check-availability', 'AuthenticateController@check_availability');
    // Route::post('hall-features', 'AuthenticateController@hall_features');
    // Route::post('add-reservation', 'AuthenticateController@add_reservation');
    // Route::post('add-bank-transfer', 'AuthenticateController@add_bank_transfer');
    // Route::post('check-cancel', 'AuthenticateController@check_cancel');
    // Route::post('cancel-reservation', 'AuthenticateController@cancel_reservation');
    // Route::post('follow-ads', 'AuthenticateController@follow_ads');
    // Route::post('report-ads', 'AuthenticateController@report_ads');
    // Route::post('add-ads', 'AuthenticateController@add_ads');

    // Route::post('delete-follow', 'AuthenticateController@delete_follow');
    // Route::post('delete-notification', 'AuthenticateController@delete_notification');
    // Route::post('delete-order-photo', 'AuthenticateController@delete_order_photo');


    // Route::get('my-favourite', 'AuthenticateController@my_favourite');
    // Route::get('my-wating-orders', 'AuthenticateController@my_wating_orders');
    // Route::get('my-running-orders', 'AuthenticateController@my_running_orders');
    // Route::get('my-removed-orders', 'AuthenticateController@my_removed_orders');
    // Route::get('my-completed-orders', 'AuthenticateController@my_completed_orders');
    // Route::get('my-active-orders', 'AuthenticateController@my_active_orders');
    // Route::get('my-finished-orders', 'AuthenticateController@my_finished_orders');
    // Route::get('my-order-offers/{id}', 'AuthenticateController@my_order_offers');

    // Route::get('my-new-orders', 'AuthenticateController@my_new_orders');
    // Route::get('my-ads', 'AuthenticateController@my_ads');
    // Route::post('add-comment', 'AuthenticateController@add_comment');
    // Route::get('my-cancelled-orders', 'AuthenticateController@my_cancelled_orders');

    // Route::post('join-store', 'AuthenticateController@join_store');
    // Route::post('delete-store', 'AuthenticateController@delete_store');
    // Route::get('my-stores', 'AuthenticateController@my_stores');
    // Route::post('current-representatives', 'AuthenticateController@current_representatives');
    // //    Route::post('add-order', 'AuthenticateController@add_order');
    // Route::post('ask-upgrade', 'AuthenticateController@ask_upgrade');
    // Route::post('ask-store', 'AuthenticateController@ask_store');
    // //    Route::post('cancel-order', 'AuthenticateController@cancel_order');
    // Route::post('cancel-order-representative', 'AuthenticateController@cancel_order_representative');
    // Route::post('delete-offer', 'AuthenticateController@delete_offer');
    // Route::post('accept-offer', 'AuthenticateController@accept_offer');
    // Route::post('add-shop-rating', 'AuthenticateController@addShopRating');
    // // tickets
    // Route::post('add-ticket', 'AuthenticateController@add_ticket');

    // Route::get('my-tickets', 'AuthenticateController@my_tickets');

    // Route::post('add-ticket-message', 'AuthenticateController@send_message_admin');
    // Route::get('get-messages-ticket/{ticket}', 'AuthenticateController@get_messages_ticket');
    // Route::post('close-ticket', 'AuthenticateController@close_ticket');
    // // delivery address
    // Route::get('delivery-addresses', 'AuthenticateController@addresses');
    // Route::post('add-delivery-address', 'AuthenticateController@store_address');
    // Route::post('edit-delivery-address/{address_id}', 'AuthenticateController@update_address');
    // Route::post('delete-delivery-address', 'AuthenticateController@delete_address');
    // Route::post('default-address', 'AuthenticateController@setDefaultAddress');

    // Route::get('regions', 'ApiController@getRegions');



    // Route::get('order/{id}', 'AuthenticateController@order');
    // Route::post('edit-order', 'AuthenticateController@edit_order');
    // Route::post('edit-ads-data', 'AuthenticateController@edit_ads_data');
    // Route::post('edit-ads-options', 'AuthenticateController@edit_ads_options');
    // Route::post('edit-ads-photos', 'AuthenticateController@edit_ads_photos');
    // Route::get('get-messages-order/{order_id}', 'AuthenticateController@get_messages_order');
    // Route::get('user-rate/{user_id}', 'AuthenticateController@user_rate');
    // Route::post('add-message', 'AuthenticateController@add_message');
    // Route::get('balance', 'AuthenticateController@balance');
    // Route::get('get-balance', 'AuthenticateController@getBalance');

    // Route::post('request-money', 'AuthenticateController@request_money');
    // Route::post('rate-product', 'AuthenticateController@rate_product');
    // Route::get('hall-ratings', 'ApiController@hall_ratings');


    // //unauthenticated routes
    // Route::post('login', 'AuthController@login');
    // Route::post('activate', 'AuthController@activate');
    // Route::post('register', 'AuthController@register');
    // Route::get('current-user', 'AuthController@me');
    // Route::post('delete-account', 'AuthController@deleteAccount');
    // Route::post('logout', 'AuthController@logout');

    // Route::post('forget', 'ApiController@getResend');
    // Route::post('set-password', 'ApiController@setPassword');
    // Route::post('social-login', 'ApiController@socialLogin');
    // Route::post('halls', 'ApiController@halls');
    // Route::get('hall/{id}', 'ApiController@hall_details');

    // Route::get('main-page', 'ApiController@MainPage');
    // Route::get('offers', 'ApiController@offers');
    // Route::get('restaurant/{id}/{delivery_time?}', 'ApiController@restaurant');
    // Route::get('product/{id}', 'ApiController@product');
    // Route::get('main-page-shops', 'ApiController@MainPageShops');
    // Route::get('about', 'ApiController@about');
    // Route::get('terms', 'ApiController@terms');
    // Route::get('privacy', 'ApiController@privacy');
    // Route::get('countries', 'ApiController@getCountries');
    // Route::get('illustrations', 'ApiController@getIllustrations');
    // Route::get('hall-types', 'ApiController@hall_types');
    // Route::get('currencies', 'ApiController@getCurrencies');
    // Route::get('banks', 'ApiController@banks');
    // Route::get('packages', 'ApiController@packages');
    // Route::get('bank-accounts', 'ApiController@bank_accounts');
    // Route::get('notification-types', 'ApiController@notification_types');
    // Route::get('categories', 'ApiController@getCategories');
    // Route::get('main-categories', 'GlobalDataController@getMainCategories');
    // Route::get('home-page', 'GlobalDataController@sliders');

    // Route::get('services-categories', 'ApiController@getServicesCategories');

    // Route::get('selection/{sub_category_id}', 'ApiController@getSelections');
    // Route::get('contact-categories', 'ApiController@contactCategories');
    // Route::get('delivery-times', 'ApiController@deliveryTimes');
    // Route::post('confirm-deliver', 'AuthenticateController@confirm_deliver');
    // Route::get('get-messages-user/{hall_id}/{other_user}', 'AuthenticateController@get_messages_user');
    // Route::get('get-all-messages', 'AuthenticateController@get_all_messages');
    // Route::get('user-page/{id}', 'ApiController@user_page');
    // Route::get('cities', 'ApiController@cities');
    // Route::post('check-coupon', 'AuthenticateController@check_coupon');
    // Route::post('check-coupon-category', 'AuthenticateController@check_coupon_category');
    // Route::get('track-order', 'AuthenticateController@trackOrder');


    // //    Route::post('login', 'AuthenticateController@login');
    // //    Route::get('get-phone-code', 'AuthenticateController@get_phone_code');
    // //    Route::post('register', 'AuthenticateController@register');
    // //    Route::post('request-representative', 'AuthenticateController@request_representative');
    // //    Route::post('update-profile', 'AuthenticateController@update_profile');
    // //    Route::post('set-coordinates', 'AuthenticateController@update_coordinates');
    // //    Route::post('change-lang', 'AuthenticateController@change_lang');
    // //    Route::post('change-notification', 'AuthenticateController@change_notification');
    // //    Route::post('change-status', 'AuthenticateController@change_status');
    // //    Route::post('update-device-token', 'AuthenticateController@update_device_token');
    // //    Route::get('test', 'ApiController@test'cities);
    // //    Route::get('test-firebase', 'AuthenticateController@test_firebase');
    // //    Route::post('activate', 'AuthenticateController@activate');
    // //    Route::post('activate-phone', 'AuthenticateController@activate_phone');
    // //    Route::get('get-user', 'AuthenticateController@getAuthenticatedUser');
    // //
    // //    Route::get('logout', 'AuthenticateController@logout');
    // //    Route::get('terms', 'ApiController@terms');
    // //    Route::get('services', 'AuthenticateController@services');
    // //    Route::get('services-price/{id}', 'ApiController@services_price');
    // //    Route::get('min-shipments/{id}', 'ApiController@min_shipments');
    // //    Route::get('min-order-price', 'ApiController@min_order_price');
    // //
    // //    Route::get('payment-methods', 'ApiController@payment_methods');
    // //    Route::post('pay-mandoob-money', 'AuthenticateController@pay_mandoob_money');
    // //
    // //    Route::post('add-order-card', 'AuthenticateController@add_order_card');
    // //
    // //    Route::post('find-representative', 'AuthenticateController@find_representative');
    // //
    // //    Route::post('add-flight', 'AuthenticateController@add_flight');
    // //    Route::post('add-car-trip', 'AuthenticateController@add_car_trip');
    // //    Route::post('edit-car-trip', 'AuthenticateController@edit_car_trip');
    // //
    // //    Route::post('search-flight', 'AuthenticateController@search_flight');
    // //    Route::get('my-flights', 'AuthenticateController@my_flights');
    // //    Route::get('my-car-trips', 'AuthenticateController@my_car_trips');
    // //    Route::post('search-car-trips', 'AuthenticateController@search_car_trips');
    // //    Route::post('add-flight-shipment', 'AuthenticateController@add_flight_shipment');
    // //    Route::post('add-car-shipment', 'AuthenticateController@add_car_shipment');
    // //
    // //    Route::post('assign-order', 'AuthenticateController@assign_order');
    // //    Route::get('about', 'ApiController@about');
    // //    Route::get('terms', 'ApiController@terms');
    // //    Route::get('policy', 'ApiController@policy');
    // //    Route::get('price-plane-shipments', 'ApiController@price_plane_shipments');
    // //    Route::get('price-car-shipments', 'ApiController@price_car_shipments');
    // //    Route::get('messages-before-chat/{service_id}/{user_type_id}', 'AuthenticateController@messages_before_chat');
    // //
    // //    Route::get('categories', 'ApiController@getCategories');
    // //    Route::get('stores/{latitude}/{longitude}', 'ApiController@getAllStores');
    // //    Route::get('stores-by-category/{id}/{latitude}/{longitude}', 'ApiController@getByCategory');
    // //    Route::get('notifications-count', 'AuthenticateController@get_notifications_count');
    // //    Route::get('notifications', 'AuthenticateController@notifications');
    // //    Route::get('test-push','AuthenticateController@notification_test');
    // //    Route::get('user-services','AuthenticateController@user_services');
    // //    Route::get('user-location/{user_id}','AuthenticateController@user_location');
    // //    Route::post('change-services','AuthenticateController@change_services');
    // //    Route::post('current-location','AuthenticateController@current_location');
    // //    Route::get('testResponse','ApiController@testResponse');
    // //    Route::post('delete-flight', 'AuthenticateController@delete_flight');
    // //    Route::post('delete-car-trip', 'AuthenticateController@delete_car_trip');
    // //    Route::post('refuse-order', 'AuthenticateController@refuse_order');
    // //    Route::post('accept-order', 'AuthenticateController@accept_order');
    // //    Route::post('export-contract','AuthenticateController@export_contract');
    // //    Route::post('confirm-deliver-shipment','AuthenticateController@confirm_deliver_shipment');
    // //    Route::post('make-invoice','AuthenticateController@make_invoice');
    // //
    // //    Route::get('my-running-orders', 'AuthenticateController@my_running_orders');
    // //    Route::get('my-finished-orders', 'AuthenticateController@my_finished_orders');
    // //    Route::get('my-deliver-running-orders', 'AuthenticateController@my_deliver_running_orders');
    // //    Route::get('my-deliver-finished-orders', 'AuthenticateController@my_deliver_finished_orders');
    // //    Route::get('order-details/{id}', 'AuthenticateController@order_details');
    // //
    // //
    // //    Route::post('charge-card', 'AuthenticateController@charge_card');
    // //
    // //    Route::get('cards', 'ApiController@cards');
    // //    Route::get('get-credentials', 'ApiController@get_credentials');
    // //    Route::get('payment', 'ApiController@payment_status');
    // //    Route::get('payment1', 'ApiController@payment_status1');
    // //    Route::get('payment-money', 'ApiController@payment_money_status');
    // //    Route::get('payment1-money', 'ApiController@payment_money_status1');
    // //    Route::get('test-buy', 'ApiController@testBuy');
    // //    Route::get('test-display', 'ApiController@testDisplay');



    // /*provider routes*/
    // Route::post('provider-login', 'Provider\AuthProviderController@login');
    // Route::post('provider-activate', 'Provider\AuthProviderController@activate');
    // Route::post('become-provider', 'Provider\AuthProviderController@become_provider');
    // Route::get('search-users', 'Provider\OrdersProviderController@search_users');


    // Route::get('get_products', 'Provider\OrdersProviderController@get_products');


    // Route::get('users-cart', 'Provider\OrdersProviderController@users_cart');
    // Route::post('create-user', 'Provider\OrdersProviderController@create_user');
    // Route::post('store_item', 'Provider\OrdersProviderController@store_item');
    // Route::post('update_item', 'Provider\OrdersProviderController@update_item');
    // Route::post('delete_item', 'Provider\OrdersProviderController@delete_item');
    // Route::get('show_cart', 'Provider\OrdersProviderController@show_cart');
    // Route::post('store-order', 'Provider\OrdersProviderController@store_order');

    // Route::get('orders-status', 'Provider\OrdersProviderController@orders_status');
    // Route::get('all-orders', 'Provider\OrdersProviderController@all_orders');
    // Route::post('change-order-status/{id}', 'Provider\OrdersProviderController@change_order_status');
    // Route::post('delete-cart/{id}', 'Provider\OrdersProviderController@delete_cart');
    // Route::get('privacy-provider', 'Provider\AuthProviderController@privacy');
    // Route::post('payment', 'PaymentController@payment');
    // Route::post('payment-apple-pay', 'PaymentController@paymentApplePay');
    // Route::get('payment-error', 'PaymentController@paymentError');
    // Route::get('payment-done', 'PaymentController@paymentDone');
    // Route::post('tap-checkout', 'TapPaymentController@checkout');
    // Route::get('payment-tap-success', 'TapPaymentController@successPayment');
    // Route::get('payment-tap-error', 'TapPaymentController@errorPayment');
    // // Route::get('verify-checkout', 'TapPaymentController@verify');

    // Route::get('provider-notifications', 'Provider\NotificationController@index');

    // Route::get('purchase-order-details/{id}', 'Provider\UpdateOrderController@orderDetails');
    // Route::post('update-order/{id}', 'Provider\UpdateOrderController@updateOrder');
    // Route::get('test-edit-order/{id}', 'AuthenticateController@testEditOrder');
    // Route::get('update-old-cart','AuthenticateController@updateOldCart');
    // Route::get('test-select-driver-order/{id}', 'AuthenticateController@testSelectDriverOrder');
    // Route::get('test-select-driver-purchases/{id}', 'AuthenticateController@testSelectDriverPurchases');

    // Route::post('tmara/checkout', 'TmaraPaymentController@checkout');
    // Route::get('tmara/payment-done', 'TmaraPaymentController@paymentDone');
    // Route::get('tmara/payment-success', 'TmaraPaymentController@paymentSuccess');
    // Route::get('tmara/payment-error', 'TmaraPaymentController@paymentError');
    // Route::get('tmara/payment-notify', 'TmaraPaymentController@paymentNotify');
    // Route::get('approve-tmara-order/{id}','AuthenticateController@approveOrderTmara');



    Route::group(['namespace' => 'Client'], function () {
        Route::group(['namespace' => 'Auth'], function () {
            Route::post('login', 'AuthController@login');
            Route::post('activate', 'AuthController@activate');
            Route::post('register', 'AuthController@register');
            Route::get('current-user', 'AuthController@me');
            Route::post('delete-account', 'AuthController@deleteAccount');
            Route::post('logout', 'AuthController@logout');
            Route::post('update-profile', 'AuthController@updateProfile');
            Route::post('update-device-token', 'AuthController@updateDeviceToken');
            Route::post('update-notification', 'AuthController@updateNotification');
            Route::get('notifications', 'AuthController@notifications');
            Route::get('get-balance', 'AuthController@getBalance');
        });
        Route::group(['namespace' => 'GlobalData'], function () {
            Route::get('main-categories', 'GlobalDataController@getCategories');
            Route::get('home-page', 'GlobalDataController@sliders');
            Route::post('contact-us', 'GlobalDataController@contact_us');
            Route::get('about', 'GlobalDataController@about');
            Route::get('terms', 'GlobalDataController@terms');
            Route::get('privacy', 'GlobalDataController@privacy');
            Route::post('upload-photos', 'GlobalDataController@uploadPhotos');
        });
        Route::group(['namespace' => 'Product'], function () {
            Route::get('search-products', 'ProductController@searchProducts');
            Route::get('search-autocomplete', 'ProductController@searchAutoComplete');
            Route::post('product-page', 'ProductController@getProduct');
            Route::get('favorite-products', 'ProductController@favoriteProducts');
            Route::post('like-product', 'ProductController@likeOrDislikeProduct');
        });

        Route::group(['namespace' => 'Address'], function () {
            Route::get('delivery-addresses', 'AddressController@index');
            Route::post('add-delivery-address', 'AddressController@store');
            Route::post('edit-delivery-address/{address_id}', 'AddressController@update');
            Route::post('delete-delivery-address', 'AddressController@destroy');
            Route::post('default-address', 'AddressController@setDefaultAddress');
        });

        Route::group(['namespace' => 'Cart'], function () {
            Route::post('add-to-cart', 'CartController@addToCart');
            Route::post('remove-from-cart', 'CartController@removeFromCart');
            Route::get('cart-items', 'CartController@getCartItems');
            Route::get('cart-summary', 'CartController@getCartSummary');
            Route::post('check-coupon', 'CartController@checkCoupon');
        });

        Route::group(['namespace' => 'Order'], function () {
            Route::post('add-order', 'OrderController@addOrder');
            Route::post('send-order', 'OrderController@sendOrder');
            Route::get('my-orders', 'OrderController@myOrders');
            Route::get('order-details', 'OrderController@orderDetails');
            Route::post('cancel-order', 'OrderController@cancelOrder');
        });
    });
});
