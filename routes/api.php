<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ClinicController;
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

// Grouping api to user 'api' middleware with prefix /auth/ on URL
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    // API for login
    Route::post('/login', [AuthController::class, 'login']);
    // API for register as a customer
    Route::post('/register', [AuthController::class, 'register']);
    // API for logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // API for refresh current jwt token TTL
    Route::post('/refresh', [AuthController::class, 'refresh']);
    // API for get current user
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    // API for update current user password
    Route::post('/password/update', [AuthController::class, 'updatePassword']);
    // API to update current logged in user (staff / customer)
    Route::post('/update', [AuthController::class, 'updateProfile']);
});

Route::middleware('authOnly')->group(function (){
    // API grouping for AdminOnly middleware
    Route::middleware('adminOnly')->group(function (){
        // API to show staff list
        Route::get('/staff-list', [AdminController::class, 'indexStaff']);
        // API to register staff
        Route::post('/staff-register', [AdminController::class, 'storeStaff']);
        // API to show staff by id
        Route::get('/staff-show/{id}', [AdminController::class, 'showStaff']);
        // API to update staff by id
        Route::post('/staff-update/{id}', [AdminController::class, 'updateStaff']);
        // API to delete staff by id
        Route::post('/staff-delete/{id}', [AdminController::class, 'destroyStaff']);
    });

    // API grouping for StaffOnly middleware with prefix /staff/ on URL
    Route::group([
        'middleware' => 'staffOnly',
        'prefix' => 'staff',
    ],function (){
        // API to get list of ticket from clinic by status (only show ticket for specific staff's clinic)
        Route::get('/tickets', [TicketController::class, 'index']);
        // API to approve ticket
        Route::post('/ticket/approve/{id}', [TicketController::class, 'approve']);
        // API to reject ticket
        Route::post('/ticket/reject/{id}', [TicketController::class, 'reject']);
        // API to get schedule list
        Route::get('/schedule', [ScheduleController::class, 'show']);
        //API to get schedule list based on clinic Id
        Route::get('/schedule-by-clinic', [ScheduleController::class, 'showById']);
        // API to insert schedule for doctor
        Route::post('/schedule-create', [ScheduleController::class, 'store']);
    });
    // API to get list of clinics
    Route::get('/clinics', [ClinicController::class, 'index']);
    // API to get schedule by clinic_id
    Route::get('/schedule/clinic/{id}', [ScheduleController::class, 'indexByClinicID']);
    // API to get list of customer tickets
    Route::get('/tickets', [TicketController::class,'indexCustomer']);
    // API to create/booking ticket
    Route::post('/ticket-create', [TicketController::class,'store']);
});
