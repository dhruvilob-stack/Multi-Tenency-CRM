<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\SupplierInvitationController;
use App\Http\Controllers\UserInvitationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/super-admin', '/login');
Route::redirect('/super-admin/login', '/login');

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('auth.google.callback');

Route::get('/supplier/invitations/{token}', [SupplierInvitationController::class, 'show'])
    ->name('supplier.invitation.show');
Route::post('/supplier/invitations/{token}', [SupplierInvitationController::class, 'accept'])
    ->name('supplier.invitation.accept');

Route::get('/invitations/{token}', [UserInvitationController::class, 'show'])
    ->name('user.invitation.show');
Route::post('/invitations/{token}', [UserInvitationController::class, 'accept'])
    ->name('user.invitation.accept');
