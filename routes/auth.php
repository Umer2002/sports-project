<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('register/player', [RegisteredUserController::class, 'createPlayer'])
        ->name('register.player');
    Route::post('register/player', [RegisteredUserController::class, 'storePlayer'])
        ->name('register.player.store');

    Route::get('register/club', [RegisteredUserController::class, 'createClub'])
        ->name('register.club');
    Route::post('register/club', [RegisteredUserController::class, 'storeClub'])
        ->name('register.club.store');

    Route::get('register/ambassador', [RegisteredUserController::class, 'createAmbassador'])
        ->name('register.ambassador');
    Route::post('register/ambassador', [RegisteredUserController::class, 'storeAmbassador'])
        ->name('register.ambassador.store');

    Route::get('register/college', [RegisteredUserController::class, 'createCollege'])
        ->name('register.college');
    Route::post('register/college', [RegisteredUserController::class, 'storeCollege'])
        ->name('register.college.store');

    Route::get('register/coach', [RegisteredUserController::class, 'createCoach'])
        ->name('register.coach');
    Route::post('register/coach', [RegisteredUserController::class, 'storeCoach'])
        ->name('register.coach.store');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::get('register/clubs-for-sport', [RegisteredUserController::class, 'clubsForSport'])
    ->name('register.clubs-for-sport');

Route::get('register/states', [RegisteredUserController::class, 'statesForCountry'])
    ->name('register.states-for-country');

Route::get('register/cities', [RegisteredUserController::class, 'citiesForState'])
    ->name('register.cities-for-state');

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
