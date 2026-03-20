<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RestaurantSettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page
Route::get('/', function () {
    return Inertia::render('Landing', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('landing');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Restaurant Settings
    Route::get('/settings', [RestaurantSettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [RestaurantSettingsController::class, 'update'])->name('settings.update');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::post('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // Menu Items
    Route::get('/menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
    Route::get('/menu-items/create', [MenuItemController::class, 'create'])->name('menu-items.create');
    Route::post('/menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');
    Route::get('/menu-items/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('menu-items.edit');
    Route::post('/menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('menu-items.update');
    Route::delete('/menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');
    Route::post('/menu-items/reorder', [MenuItemController::class, 'reorder'])->name('menu-items.reorder');

    // QR Codes
    Route::get('/qr-codes', [QrCodeController::class, 'index'])->name('qr-codes.index');
    Route::post('/qr-codes', [QrCodeController::class, 'store'])->name('qr-codes.store');
    Route::delete('/qr-codes/{qrCode}', [QrCodeController::class, 'destroy'])->name('qr-codes.destroy');
    Route::get('/qr-codes/{qrCode}/download', [QrCodeController::class, 'download'])->name('qr-codes.download');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Subscriptions
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Admin Panel
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
    });
});

require __DIR__.'/auth.php';

// Public menu routes — MUST be LAST
Route::get('/{slug}', [PublicMenuController::class, 'show'])->name('public.menu');
Route::post('/{slug}/track', [PublicMenuController::class, 'trackEvent'])->name('public.track');
