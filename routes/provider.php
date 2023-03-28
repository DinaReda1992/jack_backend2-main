<?php

Route::group(['middleware' => ['web','guest.provider'], 'prefix' => 'provider-panel'], function () {
    Route::get('login', 'Auth\LoginController@login_page');
    Route::post('login', 'Auth\LoginController@login');
});
//    auth()->loginUsingId(248);
//    auth()->loginUsingId(13);

// Admin routes for login users
Route::group(['middleware' => ['web','auth.provider'], 'prefix' => 'provider-panel'], function () {
    Route::post('clear_notifications', 'AdminController@clear_notifications');

    Route::get('/getMakeYears/{id}', 'AdminController@getMakeYears');
    Route::get('/getMakeModels/{id}', 'AdminController@getMakeModels');
    Route::get('/getSubCategories/{id}', 'AdminController@getSubCategories');
    Route::get('/getCategoryMeasurement/{id}', 'AdminController@getCategoryMeasurement');

    Route::resource('products', 'ProductController');
    Route::get('product_archived_restore/{id}', 'ProductController@product_archived_restore');

    Route::resource('damage-estimates', 'DamageEstimatesController');
    Route::post('add_damage_offer/{id}','DamageEstimatesController@addDamageOffer');

    Route::resource('pricing-orders', 'PricingOrdersController');
    Route::post('add_pricing_offers/{id}','PricingOrdersController@addPricingOffer');
    Route::get('/pricing-part-order/{id}','PricingOrdersController@getPartOrder');


    Route::resource('restaurants', 'RestaurantsController');
    Route::get('/stop_open_restaurant/{id}','RestaurantsController@stopOpenRestaurant');

    Route::resource('reservations', 'ReservationsController');
//    Route::get('/extraFeatures','HallsController@extraFeatures');
    Route::get('/extraItems','ExtraCategoriesController@extraItems');
    Route::get('/delete-photo-product/{id}','ProductController@deleteProductPhoto');

    Route::resource('menus', 'MenusController');
    Route::resource('branches', 'BranchesController');
    Route::resource('posts', 'PostsController');
    Route::resource('meal-menus', 'MealMenusController');
    Route::get('/mealSizeAdd','ProductController@mealSizeAdd');
    Route::post('/remove_meal_size','ProductController@deleteProductSize');

//    Route::get('/add-product/{project_id}','ProductController@add_product');
//    Route::get('/all-products/{project_id}','ProductController@all_products');

    Route::resource('invoices', 'InvoicesController');
    Route::resource('banners', 'BannersController');
    Route::resource('privileges', 'PrivilegesController');
    Route::resource('menuItems', 'MenuItemsController');
    Route::resource('slider', 'SliderController');
    Route::resource('groups', 'GroupsController');
    Route::resource('main_slider', 'MainSliderController');
    Route::get('logout', 'Auth\LoginController@logout');
    Route::get('index', 'AdminController@getIndex');
    Route::get('/', 'AdminController@getIndex');
    Route::get('/edit-profile','ProfileController@edit_profile');
    Route::post('/edit-profile','ProfileController@edit_profile_post');
    Route::get('/getStates/{country_id}', 'HomeController@getStates');

    Route::resource('countries', 'CountriesController');
    Route::resource('styles', 'StylesController');
    Route::resource('states', 'StatesController');
    Route::resource('cities', 'CitiesController');
    Route::resource('report-types', 'ReportTypesController');
    Route::resource('currencies', 'CurrenciesController');

    Route::resource('questions', 'QuestionsController');
    Route::resource('blocks', 'BlocksController');
    Route::resource('meal-categories', 'MealCategoriesController');
    Route::resource('extra-categories', 'ExtraCategoriesController');

    Route::resource('selections', 'SelectionsController');
    Route::resource('delivery-times', 'DeliveryTimesController');
    Route::resource('packages', 'PackagesController');
    Route::resource('steps', 'StepsController');
    Route::resource('partners', 'PartnersController');
    Route::resource('why_us', 'WhyUsController');
    Route::resource('blog-categories', 'BlogCategoriesController');
    Route::resource('subcategories', 'SubcategoriesController');
    Route::resource('blog-subcategories', 'BlogSubcategoriesController');
    Route::resource('cars', 'CarsController');
    Route::resource('messages', 'MessagesController');
    Route::resource('years', 'YearsController');
    Route::resource('cobons', 'CobonsController');
    Route::resource('carsmodels', 'CarsModelsController');
    Route::resource('shop-settings', 'SettingsController');
    Route::resource('users', 'UsersController');
    Route::get('user_archived_restore/{id}', 'UsersController@user_archived_restore');

    Route::resource('all-users', 'AllUsersController');
    Route::resource('ads', 'AdsController');
    Route::resource('services', 'ServicesController');
    Route::resource('illustrations', 'IllustrationsController');
    Route::resource('marchant', 'MarchantController');
    Route::resource('companies', 'CompaniesController');
    Route::resource('museums', 'MuseumsController');
    Route::resource('content', 'ContentController');
    Route::resource('reports', 'ReportsController');
    Route::resource('projects', 'ProjectsController');
    Route::resource('send_notifications', 'SendNotificationsController');
    Route::resource('faqs', 'FaqsController');
    Route::resource('screens', 'ScreensController');
    Route::resource('advantages', 'AdvantagesController');
    Route::resource('join_us', 'JoinUsController');
    Route::resource('cancellation_types', 'CancellationTypesController');

    Route::get('delete-photo-project/{id}','ProjectsController@delete_photo');
    Route::resource('bank_accounts', 'BankAccountsController');
    Route::resource('membership_benefits', 'MembershipBenefitsController');
    Route::get('/display/new_join_us','JoinUsController@new_join_us');
    Route::delete('reportss/delete-comment/{id}', 'ReportsController@delComment');
    Route::get('/adv_users','UsersController@adv_users');
    Route::get('/representative-users','AllUsersController@representative_users');
    Route::get('/clients-users','AllUsersController@clients_users');
    Route::get('/supervisor-clients','AllUsersController@supervisor_users');
    Route::get('/hall-users','AllUsersController@hall_users');

    Route::get('/report-ads','ReportsController@index');
    Route::get('/reports-comments','ReportsController@comments');
    Route::get('/normal_users','UsersController@normal_users');
    Route::get('/seller_users','UsersController@seller_users');
    Route::get('/cancebank-transfer-memberl_package/{user_id}','AllUsersController@cancel_package');
    Route::get('/adv_user_package/{user_id}/{package_id}','AllUsersController@adv_user_package');
    Route::get('/both_users','UsersController@both_users');
    Route::get('/users/block_user/{id}','UsersController@block_user');
    Route::get('/hide-category/{id}','CategoriesController@hide_category');

    Route::get('/all-users/block_user/{id}','AllUsersController@block_user');
    Route::get('/users/supervisor/{id}','UsersController@supervisor');
    Route::get('/users/active_user/{id}','UsersController@active_user');
    Route::get('/users/active_payment/{id}/{package_id}','UsersController@active_payment');
    Route::get('/users/adv_user/{id}','UsersController@adv_user');
    Route::get('/adv_user_package/{user_id}/{package_id}','AllUsersController@adv_user_package');
    Route::get('/change_drag_name/{user_id}/{vals}','UsersController@change_drag_name');
    Route::get('/adv_ads','AdsController@adv_adss');
    Route::get('/new_projects','ProjectsController@new_projects');
    Route::get('/display/orders_adv','AdsController@orders_adv');
    Route::get('/display/new_messages','MessagesController@new_messages');
    Route::get('/normal_ads','AdsController@normal_ads');
    Route::get('/users/adv_ads/{id}','AdsController@adv_ads');
    Route::get('/approve_project/{id}','ProjectsController@approve_project');
    Route::get('/cancel_project/{id}','ProjectsController@cancel_project');
    Route::get('/approve_order/{id}','OrdersController@approve_order');
    Route::get('/approve_reservation/{id}','ReservationsController@approve_reservation');
    Route::get('/approve_post/{id}','PostsController@approve_post');
    Route::get('/cancel_post/{id}','PostsController@cancel_post');

    Route::get('/approve_payment/{id}','OrdersController@approve_payment');
    Route::get('/filter-new-order','OrdersController@filter_new_order');
    Route::get('/order-messages/{id}','OrdersController@order_messages');
    Route::get('/change_order_status/{id}','OrdersController@change_order_status');
    Route::get('/send-order-shipment/{id}','OrdersController@send_order_shipment');
    Route::post('/send-shipment/{id}','OrdersController@send_shipment');
    Route::get('/print-shipment/{shipment_no}','OrdersController@printShipmentAwb');

    Route::get('/filter-approved-order','OrdersController@filter_approved_order');
    Route::get('/filter-cancelled-order','OrdersController@filter_cancelled_order');
    Route::get('/filter-on-progress-order','OrdersController@filter_on_progress_order');
    Route::get('/filter-all','OrdersController@filter_all');
    Route::get('/on_progress_order/{id}','OrdersController@on_progress_order');
    Route::get('/cancel_order/{id}','OrdersController@cancel_order');
    Route::get('/cancel_reservation/{id}','ReservationsController@cancel_reservation');
    Route::get('/cancel_request/{id}','RepresentativesController@cancel_request');
    Route::get('/current_balance','CardsController@current_balance');
    Route::get('/cards_categories','CardsController@cards_categories');
    Route::get('/cards/{id}','CardsController@cards');
    Route::get('/current_prices','CardsController@current_prices');
    Route::get('/finish_order/{id}','OrdersController@finish_order');
    Route::get('/message/{hall_id}/{user_id}','MessagesController@message');
    Route::get('/message-d/{id}','MessagesController@message_d');
    Route::get('/get-last-message-with-user/{id}','MessagesController@last');
    Route::get('/articles/adv_article/{id}','ArticlesController@adv_articles');
    Route::get('/users/adv_slider/{id}','AdsController@adv_slider');
    Route::get('/delete_order/{id}','AdsController@delete_order');
    Route::get('/delete_order/{id}','AdsController@delete_order');
    Route::get('/bank-transfer-order','OrdersController@bank_transfer_order');
    Route::get('/bank-transfer-member','OrdersController@bank_transfer_member');
    Route::get('/all-bank-transfer-member','OrdersController@all_bank_transfer_member');
    Route::get('/order-details/{id}','OrdersController@getOrderDetails');
    Route::get('/invoice-print/{id}','OrdersController@getInvoicePrint');

    Route::resource('contacts', 'ContactsController');
    Route::resource('stores', 'StoresController');
    Route::get('delete-photo-store/{id}','StoresController@delete_photo_store');
    Route::resource('representatives', 'RepresentativesController');
    Route::get('/get-representatives-cancelled','RepresentativesController@cancelled_requests');
    Route::get('/approve-request/{id}','RepresentativesController@approve_request');
    Route::get('/display/contacts_new','ContactsController@contacts_new');
    Route::get('/new_projects','ProjectsController@new_projects');
    Route::get('/request-money/{type?}','RequestMoneyController@index');
    Route::get('/approve-money-request/{id}','RequestMoneyController@approve_request');
    Route::resource('balances', 'BalancesController');
    Route::post('/request_money','BalancesController@request_money');
    Route::resource('withdraw', 'RequestMoneyController');
    Route::get('/cancel_request_money/{id}','RequestMoneyController@cancel_request_money');
    Route::resource('req-mon-res', 'RequestMoneyController');

    Route::get('/cancelled_projects','ProjectsController@cancelled_projects');
    Route::get('/approved_projects','ProjectsController@approved_projects');
    Route::get('/new_posts','PostsController@new_posts');
    Route::get('/cancelled_posts','PostsController@cancelled_posts');
    Route::get('/approved_posts','PostsController@approved_posts');
    Route::get('/new_orders','OrdersController@new_orders');
    Route::get('/orders/search-users','OrdersController@search_users');
    Route::post('/orders/create-user','OrdersController@create_user');
    Route::get('/orders/products','OrdersController@search_products');
    Route::post('/orders/store-items','OrdersController@store_item');
    Route::post('/orders/update-item','OrdersController@update_item');
    Route::post('/orders/delete-item','OrdersController@delete_item');
    Route::post('/orders/store-order','OrdersController@store_order');
    Route::resource('orders', 'OrdersController');
    Route::resource('payments', 'PaymentsController');
    Route::resource('banks', 'BanksController');
    Route::resource('supply', 'SupplyController');
    Route::post('/supply/change_order_status/{id}','SupplyController@change_order_status');


    Route::get('/new_payments','PaymentsController@new_payments');
    Route::get('/cancelled_order','OrdersController@cancelled_orders');
    Route::get('/approved_orders','OrdersController@approved_orders');
    Route::get('/payed_orders','OrdersController@payed_orders');
    Route::get('/on_progress_orders','OrdersController@on_progress_orders');
    Route::get('/done_orders','OrdersController@done_orders');
    Route::resource('pay_account', 'PayaccountController');
    Route::get('/display/pay_account_new','PayaccountController@pay_account_new');
    Route::get('/save_order/{cat_id}/{order}','CategoriesController@save_order');
    Route::get('/save_price/{card_id}/{price}','CardsController@save_price');
    Route::get('/save_order_illustrations/{illustrations_id}/{order}','IllustrationsController@save_order_illustrations');
    Route::get('/save_order_step/{cat_id}/{order}','StepsController@save_order_step');
    Route::get('/save_order_type/{type_id}/{order}','CarsModelsController@save_order_type');
    Route::get('/save_order_museum/{museum_id}/{order}','MuseumsController@save_order_museum');
    Route::get('delete-photo-service/{id}','ServicesController@delete_photo');
    Route::get('/delete-photo-report/{id}','ReportsController@delete_photo');
    Route::get('/reports-details/{id}','ReportsController@reports_details');
    Route::post('/edit-report/{id}','ReportsController@edit_report');
    Route::get('/edit-cards-categories/{id}','CardsController@edit_cards_categories');
    Route::post('/edit-cards-categories/{id}','CardsController@edit_cards_categories_post');
    Route::post('/add-report/{id}','ReportsController@add_report');
    Route::get('/create_report/{id}','ReportsController@create_report');
    Route::get('/get-sub-categories/{id}','SubcategoriesController@getSubCategories');
    Route::resource('/reports', 'ReportsController');
    Route::delete('/reportss/delete-comment/{id}', 'ReportsController@delComment');
    Route::get('/report-projects','ReportsController@projects');
    Route::get('/reports-comments','ReportsController@comments');
    Route::resource('/messages', 'MessagesController');
    Route::resource('/mrmandoob-cards', 'MrmandoobCardsController');
    Route::get('/download-cards/{mrmandoob_card_id}','MrmandoobCardsController@download');
    Route::resource('/accounts', 'AccountsController');
    Route::get('/new_accounts','AccountsController@new_accounts');
    Route::get('/previous_accounts','AccountsController@previous_accounts');
    Route::get('/display/new_messages','MessagesController@new_messages');
    Route::get('/admin_messages','MessagesController@admin_messages');
    Route::get('/message/{id}','MessagesController@message');
    Route::get('/get-last-message-with-user/{id}','MessagesController@last');
    Route::get('/delete-ticket/{id}','MessagesController@delete_ticket');
    Route::get('/add_ticket', 'MessagesController@add_ticket');
    Route::get('/add-balance', 'BalancesController@create');
    Route::post('/add-balance', 'BalancesController@store');
    Route::get('/display-balance', 'BalancesController@index');
    Route::post('/add-ticket/{id?}', 'MessagesController@add_ticket_post');
    Route::post('/change-sort', 'PrivilegesController@change_sort');
    Route::post('/change-sort-extra-categories', 'ExtraCategoriesController@change_sort');
    Route::post('/change-sort-meal-categories', 'MealCategoriesController@change_sort');

    Route::post('/change-sort-illustrations', 'IllustrationsController@change_sort');
    Route::post('/change-sort-banners', 'BannersController@change_sort');
    Route::get('/categories-selections/{id}', 'CategoriesController@categories_selections');
    Route::post('/change-sort-selections', 'SelectionsController@change_sort');
    Route::post('/change-sort-categories-selections', 'CategoriesController@change_sort_categories_selections');
    Route::post('/change-sort-options', 'SelectionsController@change_sort_options');

    Route::get('/privileges-hidden', 'PrivilegesController@privileges_hidden');
    Route::get('/show-privileges/{id}', 'PrivilegesController@show_privileges');
    Route::get('/approve-payment/{id}', 'AccountsController@approve_payment');
    Route::get('/approve_store/{id}', 'StoresController@approve_store');
    Route::get('/get-details/{id}', 'AccountsController@get_details');
    Route::get('/messages-notifications', 'MessagesNotificationsController@index');
    Route::post('/save-messages-notifications', 'MessagesNotificationsController@store');

    Route::post('/import-excel', 'StoresController@import_excel_file');

});
