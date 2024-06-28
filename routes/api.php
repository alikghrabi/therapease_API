<?php
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\TherapistController;
use App\Http\Controllers\API\AppointmentController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\VitalController;


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
Route::post('email/verify/{id}/{hash}', 'App\Http\Controllers\API\VerificationController@verify')->name('verification.verify');


Route::post('/login',[UserController::class,'login']);
Route::post('/register',[UserController::class,'register']);
Route::get('/therapists', [TherapistController::class, 'getAllTherapists']);



Route::post('/loginTherapist', [TherapistController::class, 'login']);
Route::post('/registerTherapist',[TherapistController::class,'register']);

Route::get('/user/{id}', [UserController::class, 'show']);
Route::middleware('auth:sanctum')->get('/therapist/{id}', [TherapistController::class, 'show']);

Route::middleware('auth:sanctum')->get('/therapist/RequestedBookings/{id}', [TherapistController::class, 'getRequestedBookings']);
Route::middleware('auth:sanctum')->get('/therapist/ApprovedBookings/{id}', [TherapistController::class, 'getApprovedBookings']);
Route::middleware('auth:sanctum')->get('/user/RequestedBookings/{id}', [AppointmentController::class, 'getRequestedBookings']);
Route::middleware('auth:sanctum')->get('/user/ApprovedBookings/{id}', [AppointmentController::class, 'getRequestedBookings']);
Route::middleware('auth:sanctum')->get('/therapist/OldBookings/{id}', [AppointmentController::class, 'getOldBookings']);
Route::middleware('auth:sanctum')->get('/user/OldBookings/{id}', [AppointmentController::class, 'getOldBookings']);
Route::middleware('auth:sanctum')->post('/AddBooking', [AppointmentController::class, 'addBooking']);
Route::middleware('auth:sanctum')->delete('/DeleteBooking/{id}', [AppointmentController::class, 'DeleteBooking']);
Route::middleware('auth:sanctum')->put('/UpdateBooking/{id}', [AppointmentController::class, 'updateBooking']);

Route::middleware('auth:sanctum')->get('/therapistInfoById/{id}', [TherapistController::class, 'getTherapistInfoById']);
Route::group(['middleware'=>['auth:sanctum']],function(){
    Route::post('changePassword/{id}', [UserController::class, 'change Password']);
    Route::put('/changeAccountInfo/{id}', [UserController::class, 'changeAccountInfo']);
});

