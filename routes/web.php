<?php

use Illuminate\Support\Facades\Route;
// IMPORTANTE: Não te esqueças de importar o teu controlador aqui em cima!
use App\Http\Controllers\TshirtImageController;
use Illuminate\Support\Facades\Gate;

Route::get('/', function () {
    return redirect()->route('catalog.index');
});

// A tua nova rota do catálogo público:
Route::get('/catalogo', [TshirtImageController::class, 'index'])->name('catalog.index');
Route::get('/catalogo/{tshirt_image}', [TshirtImageController::class, 'show'])->name('catalog.show');

// Carrinho (persistido na sessão) - acessível a todos
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout / pagamento
Route::get('/cart/payment', [OrderController::class, 'payment'])->name('cart.payment');
Route::post('/cart/process-payment', [OrderController::class, 'processPayment'])->name('cart.processPayment');

Route::get('/checkout', function () {
    return redirect()->route('cart.payment');
})->name('checkout');

use App\Http\Controllers\AuthController;

// Rotas de Autenticação (Acessíveis apenas a convidados/não logados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rotas acessíveis apenas a utilizadores autenticados
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Perfil do utilizador (visualizar/editar)
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Alterar palavra-passe
    Route::get('/password', [AuthController::class, 'showChangePassword'])->name('password.show');
    Route::post('/password', [AuthController::class, 'updatePassword'])->name('password.update');

    // Orders (customer history) and resend receipt
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/resend-receipt', [OrderController::class, 'resendReceipt'])->name('orders.resendReceipt');

    // Secure receipt download/view
    Route::get('/orders/{order}/receipt', [OrderController::class, 'downloadReceipt'])->name('orders.receipt');
    // Preview (generate and view inline) available to owner and admin
    Route::get('/orders/{order}/preview', [OrderController::class, 'preview'])->name('orders.preview');

    // G5: Client Private Images CRUD
    Route::get('/profile/images', [\App\Http\Controllers\Customer\PersonalImageController::class, 'index'])->name('profile.images.index');
    Route::post('/profile/images', [\App\Http\Controllers\Customer\PersonalImageController::class, 'store'])->name('profile.images.store');
    Route::post('/profile/images/{tshirt_image}/destroy', [\App\Http\Controllers\Customer\PersonalImageController::class, 'destroy'])->name('profile.images.destroy');

    // Secure private image streaming
    Route::get('/private/tshirt-images/{filename}', [TshirtImageController::class, 'streamPrivateImage'])->name('tshirt-images.private');
});

// Employee routes (employees and admins)
Route::middleware(['auth', \App\Http\Middleware\IsEmployee::class])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Employee\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Employee\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/close', [\App\Http\Controllers\Employee\OrderController::class, 'close'])->name('orders.close');
});

// Admin routes (only admin users)
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PriceController;

Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-block', [UserController::class, 'toggleBlock'])->name('users.toggleBlock');
    Route::post('/users/{user}/destroy', [UserController::class, 'destroy'])->name('users.destroy');

    // Categories management (resource-like)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{category}/destroy', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Prices management (single config)
    Route::get('/prices/edit', [PriceController::class, 'edit'])->name('prices.edit');
    Route::post('/prices', [PriceController::class, 'update'])->name('prices.update');

    // Orders management (reuse admin orders controller views)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/close', [AdminOrderController::class, 'close'])->name('orders.close');
    Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

    // New: generate & send receipt, and preview (generate if missing then redirect to receipt)
    Route::post('/orders/{order}/generate-send', [AdminOrderController::class, 'generateAndSend'])->name('orders.generateSend');
    Route::get('/orders/{order}/preview', [AdminOrderController::class, 'preview'])->name('orders.preview');

    // Designs management (CRUD)
    Route::get('/designs', [\App\Http\Controllers\Admin\TshirtImageController::class, 'index'])->name('designs.index');
    Route::get('/designs/create', [\App\Http\Controllers\Admin\TshirtImageController::class, 'create'])->name('designs.create');
    Route::post('/designs', [\App\Http\Controllers\Admin\TshirtImageController::class, 'store'])->name('designs.store');
    Route::get('/designs/{tshirt_image}/edit', [\App\Http\Controllers\Admin\TshirtImageController::class, 'edit'])->name('designs.edit');
    Route::post('/designs/{tshirt_image}', [\App\Http\Controllers\Admin\TshirtImageController::class, 'update'])->name('designs.update');
    Route::post('/designs/{tshirt_image}/destroy', [\App\Http\Controllers\Admin\TshirtImageController::class, 'destroy'])->name('designs.destroy');
});

// TEMP DEBUG: show current authenticated user and gate checks (remove after debugging)
Route::get('/_admin_debug', function() {
    if (! auth()->check()) return response()->json(['authenticated' => false]);
    return response()->json([
        'authenticated' => true,
        'user' => auth()->user()->only(['id','name','email','user_type','blocked']),
        'manage_users' => Gate::allows('manage-users'),
        'process_orders' => Gate::allows('process-orders'),
    ]);
})->middleware('auth');
