<?php

use App\Http\Controllers\Api\V1\CategoriaController;
use App\Http\Controllers\Api\V1\ProdutoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API - ponto principal de estudo deste projeto
|--------------------------------------------------------------------------
|
| Todas as rotas aqui já entram automaticamente com o prefixo /api
| (definido no bootstrap/app.php ou RouteServiceProvider, dependendo
| da versão do Laravel). Por isso os exemplos abaixo apontam para
| /api/v1/...
|
*/

// Rota simples, sem controller - boa para testar se a API está no ar
Route::get('/v1/status', function () {
    return response()->json([
        'status' => 'ok',
        'hora' => now()->toDateTimeString(),
    ]);
})->name('status');

Route::prefix('v1')->name('v1.')->group(function () {

    // apiResource cria de uma vez: index, store, show, update, destroy
    // GET    /api/v1/categorias
    // POST   /api/v1/categorias
    // GET    /api/v1/categorias/{categoria}
    // PUT    /api/v1/categorias/{categoria}
    // DELETE /api/v1/categorias/{categoria}
    Route::apiResource('categorias', CategoriaController::class);

    Route::apiResource('produtos', ProdutoController::class);

    // Exemplo de rota "aninhada" para estudar rotas dentro de outra rota:
    // GET /api/v1/categorias/{categoria}/produtos
    Route::get('categorias/{categoria}/produtos', function (\App\Models\Categoria $categoria) {
        return \App\Http\Resources\ProdutoResource::collection(
            $categoria->produtos()->paginate(10)
        );
    })->name('categorias.produtos');
});
