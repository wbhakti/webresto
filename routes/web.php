<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//homepage
Route::get('/', 'App\Http\Controllers\HomeController@menu')->name('menu');
Route::get('/restoran', 'App\Http\Controllers\HomeController@menu')->name('menu');
Route::get('/syaratDanKetentuan', 'App\Http\Controllers\HomeController@termconditions')->name('termconditions');
Route::get('/contact', 'App\Http\Controllers\HomeController@contact')->name('contact');

//cart
Route::post('/cart/add', 'App\Http\Controllers\CartController@addToCart')->name('cart.add');
Route::get('/cart', 'App\Http\Controllers\CartController@viewCart')->name('cart.view');
Route::delete('/cart/{id}', 'App\Http\Controllers\CartController@remove')->name('cart.remove');
Route::post('/update-cart/{id}', 'App\Http\Controllers\CartController@update')->name('cart.update');
Route::post('/checkout', 'App\Http\Controllers\CartController@checkout')->name('checkout');
Route::get('/success/{id}', 'App\Http\Controllers\CartController@success')->name('success');
Route::post('/upload-pembayaran', 'App\Http\Controllers\CartController@upload')->name('upload');

//admin
Route::get('/login', 'App\Http\Controllers\AdminController@Login')->name('Login');
Route::post('/postlogin', 'App\Http\Controllers\AdminController@postlogin');
Route::get('/logout', 'App\Http\Controllers\AdminController@logout')->name('logout');
Route::get('/dashboard', 'App\Http\Controllers\AdminController@dashboard')->name('dashboard');
Route::get('/dashboard/masterpromo', 'App\Http\Controllers\AdminController@MasterPromo')->name('MasterPromo');
Route::post('/postPopupPromo', 'App\Http\Controllers\AdminController@postPopupPromo');
Route::get('/dashboard/mastermerchant', 'App\Http\Controllers\AdminController@MasterMerchant')->name('MasterMerchant');
Route::post('/postmerchant', 'App\Http\Controllers\AdminController@postmerchant');
Route::get('/dashboard/mastermenu', 'App\Http\Controllers\AdminController@MasterMenu')->name('MasterMenu');
Route::post('/postmenu', 'App\Http\Controllers\AdminController@postmenu');
Route::post('/ActivedMenu', 'App\Http\Controllers\AdminController@ActivedMenu');
Route::post('/CloseOrder', 'App\Http\Controllers\AdminController@CloseOrder');
Route::get('/dashboard/masterkategori', 'App\Http\Controllers\AdminController@MasterKategori')->name('MasterKategori');
Route::post('/postkategori', 'App\Http\Controllers\AdminController@postkategori');

Route::get("/dashboard/transaction", 'App\Http\Controllers\AdminController@transaction')->name('transaction');
Route::get("/dashboard/settingorder", 'App\Http\Controllers\AdminController@settingorder')->name('settingorder');
Route::get('/reportTransaction', 'App\Http\Controllers\AdminController@ReportTransaction')->name('ReportTransaction');

Route::post('/updatestatus', 'App\Http\Controllers\AdminController@UpdateStatus')->name('updatestatus');
