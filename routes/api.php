<?php

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/
Route::post('/login', [UsuarioController::class, 'login']);
Route::post('/cadastrar', [UsuarioController::class, 'cadastrar']);

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Exigem Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Ver perfil (qualquer logado pode ver perfis, ou apenas o próprio, dependendo da sua regra de privacidade)
    Route::get('/usuario/{id}', [UsuarioController::class, 'ver']);

    // Editar e Excluir (A lógica de bloqueio já está no Controller)
    Route::put('/usuario/{id}', [UsuarioController::class, 'editar']);
    Route::delete('/usuario/{id}', [UsuarioController::class, 'excluir']);

    // Rota opcional para logout (revoga o token atual)
    Route::post('/logout', function () {
        auth()->user()->tokens()->delete();
        return response()->json(['res' => 'Token revogado']);
    });
});