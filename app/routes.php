<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::controller('news','NewsController');
Route::controller('pay','PayController');
Route::controller('plan', 'PlanController');
Route::controller('bindcard','BindCardController');
Route::controller('banner','BannerController');
Route::controller('bill','BillController');
Route::controller('user','UserController');
Route::controller('fee', 'FeeController');
