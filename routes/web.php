<?php

use App\Http\Controllers\Blog\ImagesController;
use App\Http\Controllers\Blog\ListController;
use App\Http\Controllers\Blog\PostsController;
use Illuminate\Support\Facades\Route;

Route::get('/', PostsController::class);
Route::get('/posts', PostsController::class);
Route::get('/posts/list', ListController::class);
Route::get('/posts/{id}', PostsController::class);
Route::get('/images', ImagesController::class);
