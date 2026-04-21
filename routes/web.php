<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

// ==========================================
// PUBLIC ROUTES
// ==========================================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/api/carts-map', [LandingController::class, 'cartsMapData'])->name('api.carts-map');

// SEO Routes
Route::get('/sitemap.xml', function () {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>' . url('/') . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
</urlset>';
    return response($xml, 200)->header('Content-Type', 'text/xml');
});

Route::get('/robots.txt', function () {
    $txt = "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
    return response($txt, 200)->header('Content-Type', 'text/plain');
});

// ==========================================
// AUTH ROUTES (Guest only)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ==========================================
// AUTHENTICATED ROUTES
// ==========================================
Route::middleware('auth')->group(function () {

    // Dashboard (role-based)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // OWNER & ADMIN ROUTES
    // ==========================================
    Route::middleware('role:owner,admin')->group(function () {
        // Carts CRUD & Map Data
        Route::get('/carts/map-data', [CartController::class, 'mapData'])->name('carts.map_data');
        Route::resource('carts', CartController::class);
        Route::patch('/carts/{cart}/toggle-status', [CartController::class, 'toggleStatus'])->name('carts.toggle_status');

        // Products CRUD
        Route::resource('products', ProductController::class);

        // Riders CRUD
        Route::resource('riders', RiderController::class);

        // Inventories
        Route::get('/inventories', [InventoryController::class, 'index'])->name('inventories.index');
        Route::post('/inventories', [InventoryController::class, 'update'])->name('inventories.update');

        // All Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    });

    // ==========================================
    // OWNER ONLY ROUTES
    // ==========================================
    Route::middleware('role:owner')->group(function () {
        // Accounts CRUD
        Route::resource('accounts', AccountController::class);
    });

    // ==========================================
    // DRIVER ROUTES
    // ==========================================
    Route::middleware('role:rider')->prefix('rider')->name('rider.')->group(function () {
        // Update location
        Route::post('/location', [CartController::class, 'updateLocation'])->name('location.update');
        Route::post('/location/live', [CartController::class, 'updateLocationLive'])->name('location.live');

        // POS
        Route::get('/pos', [TransactionController::class, 'posIndex'])->name('pos');
        Route::post('/pos', [TransactionController::class, 'posStore'])->name('pos.store');

        // Transaction history
        Route::get('/transactions', [TransactionController::class, 'riderHistory'])->name('transactions');

        // Update stock
        Route::post('/stock', [InventoryController::class, 'riderUpdateStock'])->name('stock.update');
    });
});

