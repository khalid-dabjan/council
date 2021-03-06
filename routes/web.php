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
    return view('welcome');
});

Auth::routes();

Route::view('scan','scan');

Route::get('/home', 'HomeController@index');
Route::get('/threads', 'ThreadsController@index');
Route::post('/threads', 'ThreadsController@store')->middleware('must-be-confirmed');
Route::get('/threads/create', 'ThreadsController@create');
Route::get('/threads/search', 'SearchController@show');
Route::get('/threads/{channel}', 'ThreadsController@index');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');
Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index');

Route::post('/locked-thread/{thread}', 'LockedThreadController@store')->name('locked-thread.store')->middleware('admin');
Route::delete('/locked-thread/{thread}', 'LockedThreadController@destroy')->name('locked-thread.destroy')->middleware('admin');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy');
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('reply.destroy');
Route::patch('/replies/{reply}', 'RepliesController@update');

Route::post('/replies/{reply}/best-reply', 'BestReplyController@store')->name('best-reply.store');

Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')->middleware('auth');


Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');
Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy');

Route::get('/api/users', 'api\UsersController@index');
Route::post('/api/users/{user}/avatar', 'api\AvatarController@store')->middleware('auth')->name('avatar');
Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index');
