<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth as Auth;
use App\Http\Controllers\Admin as Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect(route('admin.dashboard'));
});

Route::any('logout', [Auth\LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::group(['middleware' => ['guest']], function() {
    Route::get('login', [Auth\LoginController::class, 'create'])->name('login');
    Route::post('login', [Auth\LoginController::class, 'store']);//->middleware('throttle:login');
    Route::get('register', [Auth\RegistrationController::class, 'create'])->name('register');
    Route::post('register', [Auth\RegistrationController::class, 'store']);
});

Route::name('admin.')->middleware('auth', 'verify.institution')->prefix('admin/')->group(function() {
    Route::get('', [Admin\AdminController::class, 'index'])->name('dashboard');
    Route::get('events', [Admin\EventController::class, 'index'])->name('events.index');
    Route::get('events/sync', [Admin\EventController::class, 'syncEvents'])->name('events.sync');
    Route::get('events/{event}/show', [Admin\EventController::class, 'show'])->name('events.show');
    Route::get('events/{event}/refresh', [Admin\EventController::class, 'refreshEvent'])->name('events.refresh');
    Route::get('events/{event}/download', [Admin\EventController::class, 'download'])->name('events.download');
    Route::get('events/{event}/evaluate', [Admin\EventController::class, 'evaluateEVent'])->name('events.evaluate');
    Route::get('events/{event}/upload', [Admin\EventController::class, 'uploadEventExams'])->name('events.upload');

    Route::get('events/{event}/extend-time', [Admin\EventController::class, 'extentTimeView'])->name('events.extend-time');
    Route::post('events/{event}/extend-time', [Admin\EventController::class, 'extentTimeStore'])->name('events.extend-time.store');
    
    Route::any('events/download-by-code', [Admin\EventController::class, 'downloadByEventCode'])->name('events.download-by-code');
    
    Route::get('exams/events/{event}/index', [Admin\ExamController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}/evaluate', [Admin\ExamController::class, 'evaluateExam'])->name('exams.evaluate');
    Route::get('exams/{exam}/extend-time', [Admin\ExamController::class, 'extentTimeView'])->name('exams.extend-time');
    Route::post('exams/{exam}/extend-time', [Admin\ExamController::class, 'extentTimeStore'])->name('exams.extend-time.store');
});

Route::get('/reset-exam/{exam:exam_no}', function (\App\Models\Exam $exam) {
    $exam->markAsStarted();
    return 'Exam restarted';
});