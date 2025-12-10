<?php

use App\Http\Controllers\Api\AccommodationController;
use App\Http\Controllers\Api\ReservationController as ApiReservationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ValidateController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
 //    return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [ValidateController::class, 'register']);
Route::post('/login', [ValidateController::class, 'login']);
Route::post('/logout', [ValidateController::class, 'logout'])->name('logout');

// Password reset
Route::post('/password/forgot', [ValidateController::class, 'forgotPassword']);
Route::post('/password/reset', [ValidateController::class, 'resetPassword']);

// Public accommodation listings
Route::get('/accommodations', [AccommodationController::class, 'index']);
Route::get('/accommodations/search', [AccommodationController::class, 'search']);
Route::get('/accommodations/{id}', [AccommodationController::class, 'show']);
Route::get('/accommodations/{id}/amenities', [AccommodationController::class, 'getAmenities']);
Route::get('/accommodations/{id}/images', [AccommodationController::class, 'getImages']);

// Municipal data (for filtering)
Route::get('/municipalities/bulan', [AccommodationController::class, 'getBulanMunicipalityData']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // User management
    Route::get('/user', [UserController::class, 'getCurrentUser']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/user/profile/photo', [UserController::class, 'updateProfilePhoto']);
    // Route::get('/user/reservations', [ReservationController::class, 'getUserReservations']);
    
    // Accommodation management (Owner specific)
    Route::middleware(['check.owner'])->group(function () {
        Route::post('/owner/accommodations', [AccommodationController::class, 'store']);
        Route::put('/owner/accommodations/{id}', [AccommodationController::class, 'update']);
        Route::delete('/owner/accommodations/{id}', [AccommodationController::class, 'destroy']);
        Route::get('/owner/accommodations', [AccommodationController::class, 'getOwnerAccommodations']);
        Route::post('/owner/accommodations/{id}/images', [AccommodationController::class, 'uploadImages']);
        // Route::get('/owner/reservations', [ReservationController::class, 'getOwnerReservations']);
        // Route::get('/owner/dashboard', [DashboardController::class, 'ownerDashboard']);
    });
    
    // Reservation management
    Route::post('/reservations', [ApiReservationController::class, 'store']);
    Route::put('/reservations/{id}/cancel', [ApiReservationController::class, 'cancel']);
    Route::put('/reservations/{id}/extend', [ApiReservationController::class, 'extend']);
    Route::get('/reservations/{id}', [ApiReservationController::class, 'show']);
    
    // Admin routes
    Route::middleware(['check.admin'])->group(function () {
        Route::get('/admin/users', [UserController::class, 'getAllUsers']);
        Route::put('/admin/users/{id}/status', [UserController::class, 'updateUserStatus']);
        Route::get('/admin/accommodations', [AccommodationController::class, 'getAllAccommodations']);
    });
    
    // Notifications
    Route::get('/notifications', [UserController::class, 'getNotifications']);
    Route::put('/notifications/{id}/mark-read', [UserController::class, 'markNotificationAsRead']);
    Route::put('/notifications/mark-all-read', [UserController::class, 'markAllNotificationsAsRead']);
});