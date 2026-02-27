<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApiTokenController;
use Illuminate\Support\Facades\Route;

// 1. Redireciona o /dashboard padrão do Breeze direto para a nossa nova rota
Route::redirect('/dashboard', '/api-docs')->middleware(['auth']);

// A Landing Page (com botões que detectam se o usuário tá logado ou não)
Route::get('/', function () {
    return view('welcome');
});

// Rotas do nosso CRUD de Tokens
Route::get('/tokens', [ApiTokenController::class, 'index'])->name('tokens.index');
Route::post('/tokens', [ApiTokenController::class, 'store'])->name('tokens.store');
Route::delete('/tokens/{id}', [ApiTokenController::class, 'destroy'])->name('tokens.destroy');

// 2. A nova rota oficial da documentação
Route::get('/api-docs', function () {
    return view('dashboard.api-docs'); // Apontando para a pasta dashboard/
})->middleware(['auth', 'verified'])->name('api-docs');

// Rotas nativas do Breeze para Perfil (editar nome, email, senha)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';