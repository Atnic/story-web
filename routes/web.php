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

Route::domain('admin.'.config('app.domain'))->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('posts', 'PostController');
    Route::resource('posts.comments', 'Post\CommentController');
    Route::resource('users', 'UserController');
    Route::resource('users.posts', 'User\PostController');
    Route::resource('users.comments', 'User\CommentController');
});

Route::get('/{path?}', 'IndexController')->name('index');
