<?php

use Illuminate\Support\Facades\Route;
// IMPORTANTE: Não te esqueças de importar o teu controlador aqui em cima!
use App\Http\Controllers\TshirtImageController;

Route::get('/', function () {
    return view('welcome');
});

// A tua nova rota do catálogo público:
Route::get('/catalogo', [TshirtImageController::class, 'index'])->name('catalog.index');

use App\Http\Controllers\AuthController;

// Rotas de Autenticação (Acessíveis apenas a convidados/não logados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rota de Logout (Acessível apenas a utilizadores logados)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');