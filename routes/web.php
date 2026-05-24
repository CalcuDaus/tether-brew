<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\RiderDailySaleController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\RiderFinanceController;
use App\Http\Controllers\RiderMinusController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\RiderSalesReportController;
use App\Http\Controllers\JournalCategoryController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\OwnerRiderDetailController;
use Illuminate\Support\Facades\Route;

// ==========================================
// PUBLIC ROUTES
// ==========================================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/api/carts-map', [LandingController::class, 'cartsMapData'])->name('api.carts-map');
Route::get('/artikel', [ArtikelController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{slug}', [ArtikelController::class, 'show'])->name('artikel.show');

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
    // Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    // Route::post('/register', [AuthController::class, 'register']);
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

        // Artikel CRUD
        Route::prefix('admin/artikel')->name('admin.artikel.')->group(function () {
            Route::get('/', [ArtikelController::class, 'adminIndex'])->name('index');
            Route::get('/create', [ArtikelController::class, 'create'])->name('create');
            Route::post('/', [ArtikelController::class, 'store'])->name('store');
            Route::get('/{artikel}/edit', [ArtikelController::class, 'edit'])->name('edit');
            Route::put('/{artikel}', [ArtikelController::class, 'update'])->name('update');
            Route::delete('/{artikel}', [ArtikelController::class, 'destroy'])->name('destroy');
        });

        // Rider Daily Sales Input
        Route::get('/rider-sales', [RiderDailySaleController::class, 'index'])->name('admin.rider_sales.index');
        Route::get('/rider-sales/create', [RiderDailySaleController::class, 'create'])->name('admin.rider_sales.create');
        Route::post('/rider-sales', [RiderDailySaleController::class, 'store'])->name('admin.rider_sales.store');
        Route::get('/rider-sales/{riderSale}/edit', [RiderDailySaleController::class, 'edit'])->name('admin.rider_sales.edit');
        Route::put('/rider-sales/{riderSale}', [RiderDailySaleController::class, 'update'])->name('admin.rider_sales.update');

        // General Journal
        Route::resource('journals', JournalController::class)->except(['show', 'edit', 'update'])->names([
            'index' => 'admin.journals.index',
            'create' => 'admin.journals.create',
            'store' => 'admin.journals.store',
            'destroy' => 'admin.journals.destroy',
        ]);

        Route::resource('journal-categories', JournalCategoryController::class)->except(['create', 'show', 'edit'])->names([
            'index' => 'admin.journal_categories.index',
            'store' => 'admin.journal_categories.store',
            'update' => 'admin.journal_categories.update',
            'destroy' => 'admin.journal_categories.destroy',
        ]);

        // Rider Finances (Kasbon & Uang Makan)
        Route::get('/rider-finances', [RiderFinanceController::class, 'index'])->name('admin.rider_finances.index');
        Route::post('/rider-finances', [RiderFinanceController::class, 'store'])->name('admin.rider_finances.store');
        Route::delete('/rider-finances/{riderFinance}', [RiderFinanceController::class, 'destroy'])->name('admin.rider_finances.destroy');
        Route::get('/api/rider-cups', [RiderFinanceController::class, 'getCups'])->name('admin.api.rider_cups');

        // Laporan Minus
        Route::get('/rider-minus', [RiderMinusController::class, 'index'])->name('admin.rider_minus.index');

        // Payroll
        Route::get('/payroll', [PayrollController::class, 'index'])->name('admin.payroll.index');

        // Rider Sales Report (Printout Penjualan)
        Route::get('/rider-sales-report', [RiderSalesReportController::class, 'index'])->name('admin.rider_sales_report.index');

        // Admin Chat Monitoring
        Route::get('/admin/chats', [AdminChatController::class, 'index'])->name('admin.chats.index');
        Route::get('/admin/chats/{conversation}', [AdminChatController::class, 'show'])->name('admin.chats.show');
        Route::get('/admin/chats/{conversation}/messages', [AdminChatController::class, 'getMessages'])->name('admin.chats.messages');
    });

    // ==========================================
    // OWNER ONLY ROUTES
    // ==========================================
    Route::middleware('role:owner')->group(function () {
        // Accounts CRUD
        Route::resource('accounts', AccountController::class);

        // Owner - Rider Performance Detail
        Route::get('/owner/rider-performance/{rider}', [OwnerRiderDetailController::class, 'show'])->name('owner.rider_performance.show');
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

// ==========================================
// CUSTOMER AUTH ROUTES (Phone-based)
// ==========================================
Route::prefix('customer')->name('customer.')->middleware('guest')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
});
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])
    ->middleware('auth')->name('customer.logout');

// ==========================================
// CUSTOMER CHAT ROUTES
// ==========================================
Route::middleware(['auth'])->prefix('chat')->name('chat.')->group(function () {
    Route::post('/start', [ChatController::class, 'startConversation'])->name('start');
    Route::get('/{conversation}/messages', [ChatController::class, 'getMessages'])->name('messages');
    Route::post('/{conversation}/send', [ChatController::class, 'sendMessage'])->name('send');
    Route::post('/{conversation}/read', [ChatController::class, 'markAsRead'])->name('read');
});

// ==========================================
// RIDER CHAT ROUTES (inside auth)
// ==========================================
Route::middleware(['auth', 'role:rider'])->prefix('rider/chat')->name('rider.chat.')->group(function () {
    Route::get('/', [ChatController::class, 'riderConversations'])->name('index');
    Route::get('/conversations-json', [ChatController::class, 'riderConversationsJson'])->name('conversations.json');
    Route::get('/{conversation}', [ChatController::class, 'riderChat'])->name('show');
    Route::get('/{conversation}/messages', [ChatController::class, 'getMessages'])->name('messages');
    Route::post('/{conversation}/send', [ChatController::class, 'sendMessage'])->name('send');
    Route::post('/{conversation}/send-qris', [ChatController::class, 'sendQris'])->name('qris');
});

