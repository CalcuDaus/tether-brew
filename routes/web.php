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
use App\Http\Controllers\BarController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DailyProductionController;
use App\Http\Controllers\SpoiledProductController;
use App\Http\Controllers\OfficeKasbonController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CustomerAppController;
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
    // OWNER, ADMIN & BAR SHARED ROUTES
    // (Productions, Spoiled Products, Rider Sales)
    // ==========================================
    Route::middleware('role:owner,admin,bar')->group(function () {
        // Daily Productions (Stok Produksi)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('productions', DailyProductionController::class);
            Route::resource('spoiled-products', SpoiledProductController::class)->names('spoiled_products');
        });

        // Rider Daily Sales Input
        Route::get('/rider-sales', [RiderDailySaleController::class, 'index'])->name('admin.rider_sales.index');
        Route::get('/rider-sales/available-stock', [RiderDailySaleController::class, 'availableStock'])->name('admin.rider_sales.available_stock');
        Route::get('/rider-sales/create', [RiderDailySaleController::class, 'create'])->name('admin.rider_sales.create');
        Route::post('/rider-sales', [RiderDailySaleController::class, 'store'])->name('admin.rider_sales.store');
        Route::get('/rider-sales/{riderSale}/edit', [RiderDailySaleController::class, 'edit'])->name('admin.rider_sales.edit');
        Route::put('/rider-sales/{riderSale}', [RiderDailySaleController::class, 'update'])->name('admin.rider_sales.update');
        Route::post('/rider-sales/confirm-journal', [RiderDailySaleController::class, 'confirmJournal'])->name('admin.rider_sales.confirmJournal');
    });

    // ==========================================
    // OWNER & ADMIN ROUTES
    // ==========================================
    Route::middleware('role:owner,admin')->group(function () {
        // Rider Sales Rollback
        Route::post('/admin/rider-sales/rollback-journal', [RiderDailySaleController::class, 'rollbackJournal'])->name('admin.rider_sales.rollbackJournal');
        // Carts CRUD & Map Data
        Route::get('/carts/map-data', [CartController::class, 'mapData'])->name('carts.map_data');
        Route::resource('carts', CartController::class);
        Route::patch('/carts/{cart}/toggle-status', [CartController::class, 'toggleStatus'])->name('carts.toggle_status');

        // Products CRUD
        Route::resource('products', ProductController::class);

        // Riders CRUD
        Route::resource('riders', RiderController::class);

        // Bars CRUD
        Route::resource('bars', BarController::class);

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
        Route::get('/rider-finances/kasbon', [RiderFinanceController::class, 'kasbon'])->name('admin.rider_finances.kasbon');
        Route::get('/rider-finances/uang-makan', [RiderFinanceController::class, 'uangMakan'])->name('admin.rider_finances.uang_makan');
        Route::post('/rider-finances', [RiderFinanceController::class, 'store'])->name('admin.rider_finances.store');
        Route::delete('/rider-finances/{riderFinance}', [RiderFinanceController::class, 'destroy'])->name('admin.rider_finances.destroy');
        Route::get('/api/rider-cups', [RiderFinanceController::class, 'getCups'])->name('admin.api.rider_cups');

        // Office Kasbon
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/office-kasbon', [OfficeKasbonController::class, 'index'])->name('office_kasbon.index');
            Route::post('/office-kasbon', [OfficeKasbonController::class, 'store'])->name('office_kasbon.store');
            Route::delete('/office-kasbon/{officeKasbon}', [OfficeKasbonController::class, 'destroy'])->name('office_kasbon.destroy');
            Route::post('/office-kasbon/{officeKasbon}/payment', [OfficeKasbonController::class, 'storePayment'])->name('office_kasbon.payment');
        });

        // Laporan Minus
        Route::get('/rider-minus', [RiderMinusController::class, 'index'])->name('admin.rider_minus.index');

        // Payroll
        Route::get('/payroll', [PayrollController::class, 'index'])->name('admin.payroll.index');
        Route::post('/payroll', [PayrollController::class, 'store'])->name('admin.payroll.store');
        Route::get('/payroll/history', [PayrollController::class, 'history'])->name('admin.payroll.history');
        Route::get('/payroll/{payrollRecord}', [PayrollController::class, 'show'])->name('admin.payroll.show');
        Route::post('/payroll/{payrollRecord}/confirm', [PayrollController::class, 'confirm'])->name('admin.payroll.confirm');
        Route::post('/payroll/{payrollRecord}/rollback', [PayrollController::class, 'rollback'])->name('admin.payroll.rollback');

        // Rider Sales Report (Printout Penjualan)
        Route::get('/rider-sales-report', [RiderSalesReportController::class, 'index'])->name('admin.rider_sales_report.index');

        // Admin Chat Monitoring
        Route::get('/admin/chats', [AdminChatController::class, 'index'])->name('admin.chats.index');
        Route::get('/admin/chats/{conversation}', [AdminChatController::class, 'show'])->name('admin.chats.show');
        Route::get('/admin/chats/{conversation}/messages', [AdminChatController::class, 'getMessages'])->name('admin.chats.messages');

        // Settings
        Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::post('/admin/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    });

    // ==========================================
    // OWNER ONLY ROUTES
    // ==========================================
    Route::middleware('role:owner')->group(function () {
        // Accounts CRUD
        Route::resource('accounts', AccountController::class);

        // Owner - Rider Performance Detail
        Route::get('/owner/rider-performance/{rider}', [OwnerRiderDetailController::class, 'show'])->name('owner.rider_performance.show');

        // Branches CRUD
        Route::resource('branches', BranchController::class);
        Route::post('/branches/{branch}/switch', [BranchController::class, 'switchBranch'])->name('branches.switch');
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

    // ==========================================
    // CUSTOMER APP ROUTES
    // ==========================================
    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerAppController::class, 'dashboard'])->name('dashboard');
        Route::get('/menu', [CustomerAppController::class, 'menu'])->name('menu');
        Route::get('/chats', [CustomerAppController::class, 'chats'])->name('chats');
        Route::get('/me', [CustomerAppController::class, 'me'])->name('me');
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
