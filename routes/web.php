<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApiTokenController;
use Illuminate\Support\Facades\Route;

// 1. Redireciona a raiz direto para a tela de login
Route::redirect('/', '/login');

// 2. Redireciona o /dashboard padrão do Breeze para a nova rota de docs
Route::redirect('/dashboard', '/dashboard/api-docs')->middleware(['auth']);

// A nova rota oficial da documentação com o prefixo correto
Route::get('/dashboard/api-docs', function () {
    return view('dashboard.api-docs');
})->middleware(['auth', 'verified'])->name('api-docs');

// Rotas do nosso CRUD de Tokens (Agrupadas com middleware de auth para segurança)
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/tokens', [ApiTokenController::class, 'index'])->name('tokens.index');
    Route::post('/tokens', [ApiTokenController::class, 'store'])->name('tokens.store');
    Route::delete('/tokens/{id}', [ApiTokenController::class, 'destroy'])->name('tokens.destroy');
});

// Rotas nativas do Breeze para Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';