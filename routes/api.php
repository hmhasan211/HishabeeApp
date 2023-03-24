<?php

use App\Http\Controllers\Api\AuthContoller;
use App\Http\Controllers\Api\ExpenseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;

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

Route::post('/login', [AuthContoller::class, 'login']);
Route::post('/register', [AuthContoller::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
//User logout
    Route::post('/logout', [AuthContoller::class, 'logout']);

//category
    Route::get('/categories',[CategoryController::class,'index'])->name('categories');
    Route::post('/category/create',[CategoryController::class,'store'])->name('category.create');
    Route::post('/category/update/{id}',[CategoryController::class,'update'])->name('category.update');


//expense
    Route::get('/expenses/{search?}',[ExpenseController::class,'index'])->name('expenses');
    Route::get('/expense/max',[ExpenseController::class,'getMaximumCost'])->name('expenses.max');
    Route::post('/expense/create',[ExpenseController::class,'store'])->name('expenses.store');
    Route::post('/expense/update/{exp}',[ExpenseController::class,'update'])->name('expenses.update');
    Route::delete('/expense/destroy/{id}',[ExpenseController::class,'destroy'])->name('expenses.destroy');

});
