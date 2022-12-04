<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function (){
   return [

   ];
});

Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::name('post.')->prefix('/posts')->group(function(){
    Route::post('/add', [PostController::class, 'store'])->name('store');
    Route::post('/update/{post_id}', [PostController::class, 'update'])->name('update');
    Route::post('/delete/{post_id}', [PostController::class, 'destroy'])->name('delete');
    Route::get('/user/{user_id}', [PostController::class, 'index'])->name('index');
});
