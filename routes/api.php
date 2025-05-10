<?php

use App\Http\Controllers\API as Api;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('exam/start', [Api\ExamController::class, 'startExam'])->name('start-exam');
Route::post('exam/create-by-code', [Api\ExamController::class, 'createExamByCode'])->name('exam.create-by-code');