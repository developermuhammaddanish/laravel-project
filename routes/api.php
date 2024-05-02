<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::group(['middleware' => ['scopes:*']],function () {

        Route::controller(ProductController::class)->group(function(){
            Route::get('show_product','ShowProduct');
            Route::get('fatch_product/{id}','FatchProduct');
            Route::post('add_product','AddProduct');
            Route::post('update_product/{id}','UpdateProduct');
            Route::delete('delete_product/{id}','DeleteProduct');
            Route::get('search_product','SearchProduct');
        });
       
        Route::controller(CategoryController::class)->group(function(){
            Route::get('show_category','ShowCategory');
            Route::get('fatch_category/{id}','FatchCategory');
            Route::post('add_category','AddCategory');
            Route::post('update_category/{id}','UpdateCategory');
            Route::delete('delete_category/{id}','DeleteCategory');
        });

        Route::get('auth/user',function(){
            return Request()->user();
        });

        Route::post('logout',[AuthController::class,'logout']);
    });
    
    Route::group(['middleware' => ['scopes:verify-otp']],function () {
        Route::post('verify-email', [AuthController::class,'verifyEmail']);
    });

});

Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
