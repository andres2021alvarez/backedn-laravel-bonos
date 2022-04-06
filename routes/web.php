<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use Illuminate\Http\Request;

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
Route::group(['middleware' => ['cors']], function () {
    Route::post('/api/file', [FileController::class, 'store']);
    Route::get('/api/file/status', [FileController::class, 'index']);
    Route::post('/api/file/status', [FileController::class, 'update']);

    Route::get('/token', function (Request $request) {
        $token = $request->session()->token();
     
        $token = csrf_token();
        return $token;
    });
});
