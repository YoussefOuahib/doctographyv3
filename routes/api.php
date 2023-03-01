<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ConditionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SessionController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UpcomingAppointmentController;

use Illuminate\Support\Facades\App;

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


Route::group(['middleware' => 'auth:sanctum'], function () {

    /* Start of Essentials Routes */
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('conditions', ConditionController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('settings', SettingController::class);
    Route::apiResource('sessions', SessionController::class);
    Route::apiResource('upcomings', UpcomingAppointmentController::class);

    /*End of Essentials Routes */


    /*Start Additional Routes */
    Route::post('payments', [AppointmentController::class, 'payment']);
    Route::get('conditions/patient/{patient}', [ConditionController::class, 'patient']);
    Route::get('patients/by/{condition}', [PatientController::class, 'getPatientsByCondition']);

    //*Search Routes */
    Route::get('search/patients/{id}', [PatientController::class, 'search']);
    Route::get('search/conditions', [ConditionController::class, 'search']);
    Route::get('search/sessions/{id}', [SessionController::class, 'search']);


    Route::get('patient/{patient}/conditions/{condition}', [ConditionController::class, 'linkCondition']);
    Route::get('nextdates', [AppointmentController::class, 'calendar']);
    Route::post('nextdates/{appointment}', [AppointmentController::class, 'deleteDate']);
    Route::get('check/{patient}/{condition}', [AppointmentController::class, 'checkIfExists']);
    // Route::get('patient/{patient}/detach/{condition}', [PatientController::class, 'detach']);
    Route::post('increment/waiting', [SettingController::class, 'increment']);
    Route::post('decrement/waiting', [SettingController::class, 'decrement']);
    Route::post('reset/waiting', [SettingController::class, 'reset']);

    Route::get('show/waiting', [SettingController::class, 'show']);

    //HomePage & Stats Routes //
    Route::get('generalData', [HomeController::class, 'generalData']);

    Route::post('/language', function (Illuminate\Http\Request $request) {
        $locale = $request->input('locale');
        App::setLocale($locale);
        session()->put('locale', $locale);

        return response()->json(['success' => true]);
    });

    /* End Additional Routes */

    Route::get('/user', function (Request $request) {

        return $request->user();
    });
    Route::post('update/password', [UserController::class, 'updatePassword']);
    Route::post('update/receptionist/password', [UserController::class, 'updateRecPassword']);

    Route::get('abilities', function (Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });
});
