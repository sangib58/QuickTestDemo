<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentsController;

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
    return view('start');
});
Route::get('/registration', [PaymentsController::class, 'indexRegistration']);
Route::post('/transaction', [PaymentsController::class, 'registrationPayment'])->name('registration-payment');
Route::get('/payment', [PaymentsController::class, 'indexPayment']);
Route::post('/transactionQuiz', [PaymentsController::class, 'quizPayment'])->name('quiz-payment');
