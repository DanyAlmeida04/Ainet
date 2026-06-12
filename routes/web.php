<?php

use Illuminate\Support\Facades\Route;
// IMPORTANTE: Não te esqueças de importar o teu controlador aqui em cima!
use App\Http\Controllers\TshirtImageController;

Route::get('/', function () {
    return redirect()->route('catalog.index');
});

// A tua nova rota do catálogo público:
Route::get('/catalogo', [TshirtImageController::class, 'index'])->name('catalog.index');

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
    Route::post('/orders/{order}/resend-receipt', [OrderController::class, 'resendReceipt'])->name('orders.resendReceipt');
});
