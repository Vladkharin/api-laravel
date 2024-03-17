<?php

use App\Http\Controllers\AuthContorller;
use App\Http\Controllers\FileAccessConroller;
use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/registration', [AuthContorller::class, 'registration']);
Route::post('/authorization', [AuthContorller::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthContorller::class, 'logout']);
    Route::post('/files', [FileController::class, 'upload']);

    Route::post('/files/{file:file_id}/accesses', [FileAccessConroller::class, 'addAccess'])->can('manage,file');
    Route::delete('/files/{file:file_id}/accesses', [FileAccessConroller::class, 'deleteAccess'])->can('manage,file');
    Route::get('/files/disk', [FileController::class, 'showMyFiles']);
    Route::get('/shared', [FileAccessConroller::class, 'showMyAccess']);

    Route::get('/file/{file:file_id}', [FileController::class, 'download'])->name('download')->can('view,file');
    Route::patch('/files/{file:file_id}', [FileController::class, 'edit'])->can('manage,file');
    Route::delete('/files/{file:file_id}', [FileController::class, 'delete'])->can('manage,file');
});
