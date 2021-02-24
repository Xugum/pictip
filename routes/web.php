<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    session(['home_url' => url()->previous()]);
    return redirect('https://id.twitch.tv/oauth2/authorize?client_id=g6tn1oza2uwbnruov3kfz1zm4gsttc&force_verify=true&redirect_uri=' . urlencode(route('login-callback')) . '&response_type=token&scope=user:read:email', 302);
})->name('login');

Route::get('/login-callback', 'Auth\LoginController@callback')->name('login-callback');
Route::get('/login-auth', 'Auth\LoginController@authenticate')->name('login-auth');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/payment/{referenceId?}', 'PayController@payment')->name('payment-order');
Route::get('/payment-history', 'PayController@history')->name('payment-history');
Route::post('/payment-callback', 'PayController@callback')->name('payment-callback');
Route::post('/payment', 'PayController@pay')->name('make-payment');

Route::get('/received', 'PayController@received')->name('payment-received');

Route::get('/streamer', 'StreamerController@register')->name('streamer-register');
Route::get('/{username}', 'StreamerController@home')->name('streamer-page');
Route::post('/streamer', 'StreamerController@new')->name('new-streamer');
Route::post('/test-alert', 'StreamerController@test')->name('test-alert');