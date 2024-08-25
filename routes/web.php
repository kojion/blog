<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Blog\ImagesController;
use App\Http\Controllers\Blog\ListController;
use App\Http\Controllers\Blog\PostsController;
use Illuminate\Support\Facades\Route;

Route::get('/', PostsController::class);
Route::get('/posts', PostsController::class);
Route::get('/posts/list', ListController::class);
Route::get('/posts/{id}', PostsController::class);
Route::get('/images', ImagesController::class);
Route::get('/login', LoginController::class)->name('login');
Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class);

Route::group(['middleware' => 'auth', 'namespace' => '\App\Http\Controllers\Admin'], function () {
    // ダッシュボード
    Route::get('/admin', 'DashboardController');

    // 記事
    Route::get('/admin/posts/create', 'PostsController@create');
    Route::post('/admin/posts/create', 'PostsController@create');
    Route::post('/admin/posts', 'PostsController@store');
    Route::get('/admin/posts/edit/{id}', 'PostsController@edit');
    Route::put('/admin/posts/edit/{id}', 'PostsController@edit');
    Route::put('/admin/posts/{id}', 'PostsController@update');

    // 画像
    Route::get('/admin/images/create', 'ImagesController@create');
    Route::post('/admin/images', 'ImagesController@store');
    Route::get('/admin/images/edit/{id}', 'ImagesController@edit');
    Route::put('/admin/images/{id}', 'ImagesController@update');
    Route::delete('/admin/images/{id}', 'ImagesController@destroy');

    // 予定
    Route::get('/admin/plans/create', 'PlansController@create');
    Route::post('/admin/plans/create', 'PlansController@create');
    Route::post('/admin/plans', 'PlansController@store');
    Route::get('/admin/plans/edit/{id}', 'PlansController@edit');
    Route::put('/admin/plans/edit/{id}', 'PlansController@edit');
    Route::put('/admin/plans/{id}', 'PlansController@update');
    Route::delete('/admin/plans/{id}', 'PlansController@destroy');

    // タスク
    Route::get('/admin/tasks', 'TasksController@index');
    Route::post('/admin/tasks', 'TasksController@store');
    Route::put('/admin/tasks/{id}', 'TasksController@update');
    Route::delete('/admin/tasks/{id}', 'TasksController@destroy');

    // Wiki
    Route::get('/admin/wikis/create', 'WikisController@create');
    Route::post('/admin/wikis/create', 'WikisController@create');
    Route::get('/admin/wikis/{id}', 'WikisController@show');
    Route::post('/admin/wikis', 'WikisController@store');
    Route::get('/admin/wikis/edit/{id}', 'WikisController@edit');
    Route::put('/admin/wikis/edit/{id}', 'WikisController@edit');
    Route::put('/admin/wikis/{id}', 'WikisController@update');
    Route::delete('/admin/wikis/{id}', 'WikisController@destroy');

    // 履歴
    Route::get('/admin/histories/{id}', 'HistoriesController@show');

    // タグ
    Route::get('/admin/tags', 'TagsController@index');
});
