<?php
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\TherapistController;
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

Route::group(['middleware'=>['auth:sanctum']],function(){
    // Route::get('/patients/{id}/appointments',[PatientController::class,'appointments']);
    // Route::resource('patients',PatientController::class);
    // Route::resource('appointments',AppointmentController::class);
    Route::post('changePassword/{id}', [UserController::class, 'change Password']);
    Route::put('/changeAccountInfo/{id}', [UserController::class, 'changeAccountInfo']);
});

