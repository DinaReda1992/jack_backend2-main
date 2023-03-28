<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/Driver'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('activate', 'AuthController@activate');
    Route::post('logout', 'AuthController@logout');
    Route::post('update-information', 'AuthController@updateInformation');

    Route::group(['namespace' => 'Customer'], function () {
        Route::get('driver-orders', 'DriverController@index');
        Route::get('driver-order-details/{id}', 'DriverController@orderDetails');
        Route::post('change-order-status/{id}', 'DriverController@changeOrderStatus');
        Route::post('complete-order/{id}', 'DriverController@completeOrder');

        Route::get('warehouse-orders', 'WarehouseController@index');
        Route::get('warehouse-order-details/{id}', 'WarehouseController@orderDetails');
        Route::post('warehouse-change-order-status/{id}', 'WarehouseController@changeOrderStatus');
    });

    // Route::group(['prefix' => 'supplier', 'namespace' => 'Supplier'], function () {
    //     Route::get('driver-orders', 'DriverController@index');
    //     Route::get('driver-order-details/{id}', 'DriverController@orderDetails');
    //     Route::post('change-order-status/{id}', 'DriverController@changeOrderStatus');
    //     Route::post('driver-receive-order/{id}', 'DriverController@receiveOrder');
    //     Route::post('driver-refuse-order/{id}', 'DriverController@refuseReceiveOrder');

    //     Route::get('warehouse-orders', 'WarehouseController@index');
    //     Route::get('warehouse-order-details/{id}', 'WarehouseController@orderDetails');
    //     Route::post('warehouse-receive-order/{id}', 'WarehouseController@receiveOrder');
    // });
});
