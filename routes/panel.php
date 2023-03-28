<?php

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(['middleware' => ['web', 'guest.admin'], 'prefix' => 'admin-panel'], function () {
    Route::get('login', 'Auth\LoginController@login_page');
    Route::post('login', 'Auth\LoginController@login');
});

// Admin routes for login users
Route::group(['middleware' => ['web', 'auth.admin', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'], 'prefix' => LaravelLocalization::setLocale() . '/admin-panel'], function () {
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    Route::resource('shipments', 'ShipmentsController');
    Route::resource('autoparts', 'AutopartsController');
    Route::get('/sms-balance', 'AdminController@getBalance');
    Route::resource('damage-estimates', 'DamageEstimatesController');
    Route::post('add_damage_offer/{id}', 'DamageEstimatesController@addDamageOffer');
    Route::get('publish-damage-estimates/{id}', 'DamageEstimatesController@publishDamageOrder');
    Route::get('unPublish-damage-estimates/{id}', 'DamageEstimatesController@unPublishDamageOrder');

    Route::resource('pricing-orders', 'PricingOrdersController');
    Route::post('add_pricing_offers/{id}', 'PricingOrdersController@addPricingOffer');
    Route::get('/pricing-part-order/{id}', 'PricingOrdersController@getPartOrder');
    Route::get('publish-pricing-order/{id}', 'PricingOrdersController@publishPricingOrder');
    Route::get('unPublish-pricing-order/{id}', 'PricingOrdersController@unPublishPricingOrder');

    Route::post('/stop_shipment/{id}', 'ShipmentsController@stop_shipment');
    Route::get('/shipment/smsa', 'ShipmentsController@smsa_page');
    Route::post('/smsa_update', 'ShipmentsController@smsa_update');
    Route::get('/site-management', 'AdminController@updateSiteManagement');
    Route::post('/updateSiteContent', 'AdminController@updateSiteContent');
    Route::get('/delete-screenshot-photo/{id}', 'AdminController@deleteScreenshotPhoto');
    Route::get('/app-features', 'AdminController@appFeatures');
    Route::post('/updateSiteFeatures', 'AdminController@updateSiteFeatures');
    Route::get('/clear-data', 'AdminController@clearData');

    Route::resource('makes', 'MakesController');
    Route::post('/change-sort-makes', 'MakesController@change_sort');
    Route::post('/stop_car_make/{id}', 'MakesController@stop_car_make');
    Route::resource('make_years', 'MakeYearsController');
    Route::resource('models', 'MakeModelsController');
    Route::get('/getMakeYears/{id}', 'MakeModelsController@getMakeYears');
    Route::get('/search-model', 'MakeModelsController@filter_all');
    Route::get('/makes_archived_restore/{id}', 'MakesController@makes_archived_restore');
    Route::get('/make_year_archived_restore/{id}', 'MakeYearsController@make_year_archived_restore');
    Route::get('/model_archived_restore/{id}', 'MakeModelsController@model_archived_restore');


    Route::resource('restaurants', 'RestaurantsController');
    Route::get('/stop_open_restaurant/{id}', 'RestaurantsController@stopOpenRestaurant');
    Route::resource('arrangeDashboard', 'ArrangeDashboardController');
    Route::post('/setArrangeDashboard', 'ArrangeDashboardController@setArrangeDashboard');

    Route::resource('menus', 'MenusController');
    Route::resource('branches', 'BranchesController');
    Route::resource('posts', 'PostsController');
    Route::resource('products', 'ProductController');
    Route::get('/products-data', 'ProductController@productsData');
    Route::get('/export-excel-product', 'ProductController@exportExcel');
    Route::get('product_archived_restore/{id}', 'ProductController@product_archived_restore');
    Route::get('/delete-photo-product/{id}', 'ProductController@deleteProductPhoto');
    Route::post('/stop_product/{id}', 'ProductController@stop_product');
    Route::get('/main/sliders', 'SliderController@showMainSliders');

    Route::get('/add-product/{project_id}', 'ProductController@add_product');
    Route::get('/all-products', 'ProductController@all_products');
    Route::post('/change-sort-products', 'ProductController@change_sort');

    Route::get('page-categories-data', 'PageCategoryController@getPageCategoriesData');
    Route::resource('page-categories', 'PageCategoryController');
    Route::get('page-categories/get-sub_categories/{id}', 'PageCategoryController@getSubCategoriesData');

    Route::resource('reservations', 'ReservationsController');
    Route::resource('halls', 'HallsController');
    Route::get('/stop_open_hall/{id}', 'HallsController@stopOpenHall');
    Route::get('/accept_hall/{id}', 'HallsController@acceptHall');
    Route::get('/refuse_hall/{id}', 'HallsController@refuseHall');
    Route::get('/get-provider-states/{id}', 'HallsController@providerStates');
    Route::get('/delete-photo-hall/{id}', 'HallsController@deleteHallPhoto');

    Route::resource('invoices', 'InvoicesController');
    Route::resource('banners', 'BannersController');
    Route::resource('privileges', 'PrivilegesController');
    Route::get('/privilegeItems', 'PrivilegesController@privilegeItems');

    Route::get('/deletePrivilegeItem/{id}', 'PrivilegesController@deletePrivilegeItem');

    Route::resource('menuItems', 'MenuItemsController');
    Route::resource('slider', 'SliderController');
    Route::resource('groups', 'GroupsController');
    Route::resource('main_slider', 'MainSliderController');
    Route::get('logout', 'Auth\LoginController@logout');
    Route::get('/', 'AdminController@getIndex');
    Route::get('index', 'AdminController@getIndex');
    Route::get('/edit-profile', 'ProfileController@edit_profile');
    Route::post('/edit-profile', 'ProfileController@edit_profile_post');
    Route::resource('countries', 'CountriesController');
    Route::resource('features', 'FeaturesController');

    Route::resource('styles', 'StylesController');
    Route::resource('states', 'StatesController');
    Route::resource('cities', 'CitiesController');
    Route::resource('report-types', 'ReportTypesController');
    Route::resource('currencies', 'CurrenciesController');
    Route::resource('questions', 'QuestionsController');
    Route::resource('blocks', 'BlocksController');
    Route::resource('categories', 'CategoriesController');
    Route::post('/stop_category/{id}', 'CategoriesController@stop_category');

    Route::resource('main-categories', 'MainCategoriesController');
    Route::post('/stop_main_category/{id}', 'MainCategoriesController@stop_category');
    Route::post('/change-sort-main-categories', 'MainCategoriesController@change_sort');

    Route::resource('supplierCategories', 'ServicesCategoriesController');
    Route::post('/change-sort-supplierCategories', 'ServicesCategoriesController@change_sort');
    Route::resource('measurement-units', 'MeasurementUnitController');
    Route::resource('regions', 'RegionsController');
    Route::get('/getCategoryMeasurement/{id}', 'AdminController@getCategoryMeasurement');

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
    Route::resource('settings', 'SettingsController');

    Route::resource('payment-settings', 'PaymentSettingsController');


    Route::resource('users', 'UsersController');
    Route::resource('all-users', 'AllUsersController');
    Route::resource('drivers', 'DriverController');
    Route::get('/drivers/block_user/{id}', 'DriverController@block_user');
    /**/
    Route::get('/all-users/user-page/{id}', 'AllUsersController@getUserPage');
    Route::get('/all-users/transactions/{id}', 'AllUsersController@transactions');
    Route::get('/all-users/orders/{id}', 'AllUsersController@getUserOrders');
    Route::get('/all-users/addresses/{id}', 'AllUsersController@addresses');
    Route::post('/all-users/addresses/store', 'AllUsersController@store_address');
    Route::post('/all-users/addresses/update/{id}', 'AllUsersController@update_address');
    Route::post('/all-users/addresses/delete', 'AllUsersController@delete_address');
    Route::post('/all-users/addresses/home', 'AllUsersController@is_home');
    /**/

    Route::resource('user-requests', 'UserRequestsController');
    Route::get('/approve-user-request/{id}', 'UserRequestsController@active_user');
    Route::post('/cancel_user_request/{id}', 'UserRequestsController@cancel_user');

    Route::resource('provider-requests', 'ProviderRequestsController');
    Route::get('/approve-provider-request/{id}', 'ProviderRequestsController@active_user');
    Route::post('/cancel_provider_request/{id}', 'ProviderRequestsController@cancel_user');

    Route::resource('suppliers', 'SuppliersController');
    Route::get('/supplier-data/{id}', 'SuppliersController@addSupplierData');
    Route::post('/post_supplier_data', 'SuppliersController@postSupplierData');
    Route::post('/stop_supplier/{id}', 'SuppliersController@stop_supplier');
    Route::get('/all-suppliers', 'SuppliersController@all_suppliers');
    Route::post('/change-sort-suppliers', 'SuppliersController@change_sort');

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
    Route::resource('middle-section', 'MiddleSectionController');

    Route::resource('screens', 'ScreensController');
    Route::resource('advantages', 'AdvantagesController');
    Route::resource('join_us', 'JoinUsController');
    Route::get('delete-photo-project/{id}', 'ProjectsController@delete_photo');
    Route::resource('bank_accounts', 'BankAccountsController');
    Route::resource('membership_benefits', 'MembershipBenefitsController');
    Route::get('/display/new_join_us', 'JoinUsController@new_join_us');
    Route::delete('reportss/delete-comment/{id}', 'ReportsController@delComment');
    Route::get('/adv_users', 'UsersController@adv_users');
    Route::get('/representative-users', 'AllUsersController@representative_users');
    Route::get('/clients-users', 'AllUsersController@clients_users');
    Route::get('/get/clients-data', 'AllUsersController@clientsData');
    Route::get('/clients-users/excel', 'AllUsersController@clients_users_excel');
    Route::get('/supervisor-clients', 'AllUsersController@supervisor_users');
    Route::get('/provider-users', 'AllUsersController@provider_users');
    Route::get('/provider_archived_restore/{id}', 'AllUsersController@provider_archived_restore');

    Route::get('/report-ads', 'ReportsController@index');
    Route::get('/reports-comments', 'ReportsController@comments');
    Route::get('/normal_users', 'UsersController@normal_users');
    Route::get('/seller_users', 'UsersController@seller_users');
    Route::get('/cancebank-transfer-memberl_package/{user_id}', 'AllUsersController@cancel_package');
    Route::get('/adv_user_package/{user_id}/{package_id}', 'AllUsersController@adv_user_package');
    Route::get('/both_users', 'UsersController@both_users');
    Route::get('/users/block_user/{id}', 'UsersController@block_user');
    Route::get('/hide-category/{id}', 'CategoriesController@hide_category');

    Route::get('/all-users/block_user/{id}', 'AllUsersController@block_user');
    Route::get('/users/supervisor/{id}', 'UsersController@supervisor');
    Route::get('/users/active_user/{id}', 'UsersController@active_user');
    Route::get('/users/active_payment/{id}/{package_id}', 'UsersController@active_payment');
    Route::get('/users/adv_user/{id}', 'UsersController@adv_user');
    Route::get('/adv_user_package/{user_id}/{package_id}', 'AllUsersController@adv_user_package');
    Route::get('/change_drag_name/{user_id}/{vals}', 'UsersController@change_drag_name');
    Route::get('/adv_ads', 'AdsController@adv_adss');
    Route::get('/new_projects', 'ProjectsController@new_projects');
    Route::get('/display/orders_adv', 'AdsController@orders_adv');
    Route::get('/display/new_messages', 'MessagesController@new_messages');
    Route::get('/normal_ads', 'AdsController@normal_ads');
    Route::get('/users/adv_ads/{id}', 'AdsController@adv_ads');
    Route::get('/approve_project/{id}', 'ProjectsController@approve_project');
    Route::get('/cancel_project/{id}', 'ProjectsController@cancel_project');
    Route::get('/approve_order/{id}', 'OrdersController@approve_order');
    Route::get('/approve_reservation/{id}', 'ReservationsController@approve_reservation');

    Route::get('/approve_post/{id}', 'PostsController@approve_post');
    Route::get('/cancel_post/{id}', 'PostsController@cancel_post');

    Route::get('/approve_payment/{id}', 'OrdersController@approve_payment');
    Route::get('/filter-new-order', 'OrdersController@filter_new_order');
    Route::get('/order-messages/{id}', 'OrdersController@order_messages');

    Route::get('/filter-approved-order', 'OrdersController@filter_approved_order');
    Route::get('/filter-cancelled-order', 'OrdersController@filter_cancelled_order');
    Route::get('/filter-on-progress-order', 'OrdersController@filter_on_progress_order');
    Route::get('/filter-all', 'OrdersController@filter_all');
    Route::get('/on_progress_order/{id}', 'OrdersController@on_progress_order');
    Route::get('/cancel_order/{id}', 'OrdersController@cancel_order');
    Route::get('/cancel_request/{id}', 'RepresentativesController@cancel_request');
    Route::get('/current_balance', 'CardsController@current_balance');
    Route::get('/cards_categories', 'CardsController@cards_categories');
    Route::get('/cards/{id}', 'CardsController@cards');
    Route::get('/current_prices', 'CardsController@current_prices');
    Route::get('/finish_order/{id}', 'OrdersController@finish_order');
    Route::get('/message/{id}', 'MessagesController@message');
    Route::get('/close-ticket/{id}', 'MessagesController@closeTicket');

    Route::get('/message-d/{id}', 'MessagesController@message_d');
    Route::get('/get-last-message-with-user/{id}', 'MessagesController@last');
    Route::get('/articles/adv_article/{id}', 'ArticlesController@adv_articles');
    Route::get('/users/adv_slider/{id}', 'AdsController@adv_slider');
    Route::get('/delete_order/{id}', 'AdsController@delete_order');
    Route::get('/delete_order/{id}', 'AdsController@delete_order');
    Route::get('/bank-transfer-order', 'OrdersController@bank_transfer_order');
    Route::get('/bank-transfer-member', 'OrdersController@bank_transfer_member');
    Route::get('/all-bank-transfer-member', 'OrdersController@all_bank_transfer_member');
    Route::resource('contacts', 'ContactsController');
    Route::resource('suggestions', 'SuggestionsController');
    Route::resource('request_provider', 'RequestProviderController');
    Route::resource('stores', 'StoresController');
    Route::resource('hotel_requests', 'RequestProviderController');
    Route::get('delete-photo-store/{id}', 'StoresController@delete_photo_store');
    Route::resource('representatives', 'RepresentativesController');
    Route::resource('req-mon-res', 'RequestMoneyController');
    Route::get('/get-representatives-cancelled', 'RepresentativesController@cancelled_requests');
    Route::get('/approve-request/{id}', 'RepresentativesController@approve_request');
    Route::get('/display/contacts_new', 'ContactsController@contacts_new');
    Route::get('/new_suggestions', 'SuggestionsController@contacts_new');
    Route::get('/new_hotel_requests', 'RequestProviderController@contacts_new');
    Route::get('/new_projects', 'ProjectsController@new_projects');
    Route::get('/request-money/{type?}', 'RequestMoneyController@index');
    Route::get('/approve-money-request/{id}', 'RequestMoneyController@approve_request');
    Route::get('/cancel_request_money/{id}', 'RequestMoneyController@cancel_request');
    Route::get('/withdraw-order/{id}', 'RequestMoneyController@withdraw_order');
    Route::post('/sendBalance/{id}', 'RequestMoneyController@postSendBalance');
    Route::get('/balance-details/{user_id}', 'RequestMoneyController@getBalanceDetails');

    Route::get('/cancelled_projects', 'ProjectsController@cancelled_projects');
    Route::get('/approved_projects', 'ProjectsController@approved_projects');
    Route::get('/new_posts', 'PostsController@new_posts');
    Route::get('/cancelled_posts', 'PostsController@cancelled_posts');
    Route::get('/approved_posts', 'PostsController@approved_posts');
    //    Route::get('/new_orders','OrdersController@new_orders');
    Route::get('/search/users', 'OrdersController@search_users');
    Route::get('/search/products', 'OrdersController@search_products');
    Route::post('/orders/store-items', 'OrdersController@store_item');
    Route::post('/orders/update-item', 'OrdersController@update_item');
    Route::post('/orders/delete-item', 'OrdersController@delete_item');
    Route::get('/orders/create/{token?}', 'OrdersController@create');
    Route::post('/check-cobon', 'OrdersController@checkCobon');
    Route::get('/get-order/{id}', 'OrdersController@getOrder');
    Route::post('/orders/select-address', 'OrdersController@select_address');
    Route::post('/orders/select-payment-method', 'OrdersController@select_payment_method');
    Route::post('/orders/select-user', 'OrdersController@select_user');
    Route::post('/orders/confirm-order', 'OrdersController@confirm_order');
    Route::get('/orders/drafts', 'OrdersController@drafts');
    Route::get('/orders/cancle_order/{id}', 'OrdersController@cancle_order');
    Route::get('/orders/cancle_client_order/{id}', 'OrdersController@cancle_client_order');
    Route::get('/orders/suppliers-orders', 'OrdersController@suppliers_orders');

    Route::get('/orders/send-invoice/{id}', 'OrdersController@send_invoice');
    Route::post('/orders/upload-invoice/{id}', 'OrdersController@upload_invoice');
    Route::resource('orders', 'OrdersController')->except('create');
    Route::get('/schedule-orders', 'New_ordersController@schedule_orders');
    Route::get('/incomplete-orders', 'OrdersController@incomplete_orders');
    Route::get('/incomplete-orders-items/{id}', 'OrdersController@incomplete_orders_items');

    Route::get('/warehouse-products', 'WarehouseController@warehouseProducts');
    Route::get('/warehouse/wrong-status-orders', 'WarehouseController@orderHasWrongStatus');
    Route::get('/warehouse/cancel-order/{id}', 'WarehouseController@cancelOrder');
    Route::get('/warehouse/stop-order/{id}', 'WarehouseController@stopOrder');
    Route::get('/warehouse/return-to-wallet/{id}', 'WarehouseController@returnToWallet');
    Route::get('/warehouse/complete-order/{id}', 'WarehouseController@completeOrder');
    Route::resource('new-orders', 'New_ordersController')->except('create');
    Route::get('/warehouse/edit-order/{id}', 'WarehouseController@editOrder');
    Route::post('/warehouse/update-order/{id}', 'WarehouseController@updateOrder');
    Route::get('/balance-orders', 'WarehouseController@getReturnBalanceOrder');
    Route::get('/missing-orders', 'WarehouseController@getMissingOrder');
    Route::get('/edit-orders', 'WarehouseController@getEditOrder');
    Route::resource('warehouse', 'WarehouseController')->except('create');
    Route::get('/warehouse-details/{id}', 'WarehouseController@warehouseDetails');
    Route::get('/warehouse/approved_shipment/{id}', 'WarehouseController@approved_shipment')->middleware('throttle:20,1');
    Route::get('/warehouse/approve_order/{id}', 'WarehouseController@approve_order')->middleware('throttle:20,1');
    Route::get('/warehouse-purchases', 'WarehouseController@purchases');
    Route::get('/warehouse-purchases/create', 'WarehouseController@create');
    Route::get('/warehouse-purchases/items/{id}', 'WarehouseController@purchases_items');
    Route::get('/warehouse-purchases/create/{id}', 'WarehouseController@purchases_items_user');
    Route::get('/warehouse-purchases/orders/products', 'WarehouseController@search_products');
    Route::get('/warehouse-purchases/search-users', 'WarehouseController@search_users');


    //    Route::get('/purchases-orders/change_order_status/{id}','ReceivingPurchasesController@change_order_status');
    Route::post('/purchases-orders/change_order_status/{id}', 'ReceivingPurchasesController@change_order_status');
    Route::post('/purchases-orders/upload-invoice/{id}', 'ReceivingPurchasesController@upload_invoice');
    Route::post('/purchases-orders/select-driver/{id}', 'ReceivingPurchasesController@select_driver');
    Route::resource('purchases-orders', 'ReceivingPurchasesController');
    Route::get('purchases-refuse-orders', 'ReceivingPurchasesController@refuseOrders');
    Route::get('purchases-missing-orders', 'ReceivingPurchasesController@missingOrdersFromDriver');

    Route::get('/delivery-orders/change_order_status/{id}', 'DeliveryController@change_order_status');
    Route::post('/delivery-orders/select-driver/{id}', 'DeliveryController@select_driver');
    Route::resource('delivery-orders', 'DeliveryController');



    Route::post('/warehouse-purchases/confirm-order', 'WarehouseController@add_order');
    Route::get('/warehouse-purchases/order/{id}', 'WarehouseController@order_details');
    Route::post('/warehouse-purchases/upload-invoice/{id}', 'WarehouseController@upload_invoice_purchase');
    Route::get('/new-orders/approve_order/{id}', 'New_ordersController@approve_order');
    Route::get('/new-orders/cancle_order/{id}', 'New_ordersController@cancle_order');
    Route::post('/new-orders/upload-invoice/{id}', 'New_ordersController@upload_invoice');
    Route::get('/new-orders/send-invoice/{id}', 'New_ordersController@send_invoice');

    Route::resource('payments', 'PaymentsController');
    Route::resource('banks', 'BanksController');
    Route::resource('cancellation_types', 'CancellationTypesController');

    Route::get('/new_payments', 'PaymentsController@new_payments');
    Route::get('/cancelled_order', 'OrdersController@cancelled_orders');
    Route::get('/approved_orders', 'OrdersController@approved_orders');
    Route::get('/payed_orders', 'OrdersController@payed_orders');
    Route::get('/on_progress_orders', 'OrdersController@on_progress_orders');
    Route::get('/done_orders', 'OrdersController@done_orders');
    Route::resource('pay_account', 'PayaccountController');
    Route::get('/display/pay_account_new', 'PayaccountController@pay_account_new');
    Route::get('/save_order/{cat_id}/{order}', 'CategoriesController@save_order');
    Route::get('/save_price/{card_id}/{price}', 'CardsController@save_price');
    Route::get('/save_order_illustrations/{illustrations_id}/{order}', 'IllustrationsController@save_order_illustrations');
    Route::get('/save_order_step/{cat_id}/{order}', 'StepsController@save_order_step');
    Route::get('/save_order_type/{type_id}/{order}', 'CarsModelsController@save_order_type');
    Route::get('/save_order_museum/{museum_id}/{order}', 'MuseumsController@save_order_museum');
    Route::get('delete-photo-service/{id}', 'ServicesController@delete_photo');
    Route::get('/delete-photo-report/{id}', 'ReportsController@delete_photo');
    Route::get('/reports-details/{id}', 'ReportsController@reports_details');
    Route::post('/edit-report/{id}', 'ReportsController@edit_report');
    Route::get('/edit-cards-categories/{id}', 'CardsController@edit_cards_categories');
    Route::post('/edit-cards-categories/{id}', 'CardsController@edit_cards_categories_post');
    Route::post('/add-report/{id}', 'ReportsController@add_report');
    Route::get('/create_report/{id}', 'ReportsController@create_report');
    Route::get('/get-sub-categories/{id}', 'SubcategoriesController@getSubCategories');
    Route::resource('/reports', 'ReportsController');
    Route::delete('/reportss/delete-comment/{id}', 'ReportsController@delComment');
    Route::get('/report-projects', 'ReportsController@projects');
    Route::get('/reports-comments', 'ReportsController@comments');
    Route::resource('/messages', 'MessagesController');
    Route::resource('/mrmandoob-cards', 'MrmandoobCardsController');
    Route::get('/download-cards/{mrmandoob_card_id}', 'MrmandoobCardsController@download');
    Route::resource('/accounts', 'AccountsController');
    Route::get('/new_accounts', 'AccountsController@new_accounts');
    Route::get('/previous_accounts', 'AccountsController@previous_accounts');
    Route::get('/display/new_messages', 'MessagesController@new_messages');
    Route::get('/admin_messages', 'MessagesController@admin_messages');
    Route::get('/message/{id}', 'MessagesController@message');
    Route::get('/get-last-message-with-user/{id}', 'MessagesController@last');
    Route::get('/delete-ticket/{id}', 'MessagesController@delete_ticket');
    Route::get('/add_ticket', 'MessagesController@add_ticket');
    Route::get('/add-balance', 'BalancesController@create');
    Route::post('/add-balance', 'BalancesController@store');
    Route::get('/display-balance', 'BalancesController@index');
    Route::post('/add-ticket/{id?}', 'MessagesController@add_ticket_post');
    Route::post('/change-sort', 'PrivilegesController@change_sort');
    Route::post('/change-sort-categories', 'CategoriesController@change_sort');
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
    Route::get('/getStates/{country_id}', 'HomeController@getStates');
    Route::get('/getRegions/{country_id}', 'HomeController@getRegions');
    Route::get('/getRegionStates/{state_id}', 'HomeController@getRegionStates');
    Route::get('/getStatesByRegions/{regions}', 'HomeController@getStatesByRegions');


    Route::get('/client-orders', 'ShipmentController@clientOrder');
    Route::get('/client-orders/{id}', 'ShipmentController@clientOrderDetails');
    Route::post('/select-driver-client/{id}', 'ShipmentController@selectDriverClient');
    Route::get('/client-change-order-status/{id}', 'ShipmentController@changeOrderStatus');

    Route::get('/supplier-orders', 'ShipmentController@supplierOrder');
    Route::get('/supplier-orders/{id}', 'ShipmentController@supplierOrderDetails');
    Route::post('/select-driver-supplier/{id}', 'ShipmentController@selectDriverSupplier');

    Route::get('/report', 'ReportController@index');
    Route::get('/download-sales-excel', 'ReportController@excel');
    Route::resource('/main-suppliers', 'MainSupplierController');
    Route::resource('/activity', 'ActivityController');
    Route::get('export-excel-suppliers', 'SuppliersController@exportExcel');
    Route::get('warehouse-export-excel', 'WarehouseController@warehouseExportExcel');
    Route::get('all-orders', 'AllOrderController@index');
    Route::get('all-orders/{id}', 'AllOrderController@show');

    Route::resource('offers', 'OfferController');
    Route::post('/stop_offer/{id}', 'OfferController@stop_offer');
    Route::get('offer_archived_restore/{id}', 'OfferController@offer_archived_restore');
    Route::get('/download-offers-excel', 'OfferController@downloadOffersExcel');
    Route::get('/get/offers/data', 'OfferController@getOffersData');
    Route::get('/do/offers/search-products', 'OfferController@search_products');
    Route::get('/do/offers/search-users', 'OfferController@search_users');
    Route::resource('client_types', 'ClientTypesController');
});
