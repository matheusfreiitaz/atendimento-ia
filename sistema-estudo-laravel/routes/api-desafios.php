<?php
/*
 * NÃO é um arquivo pra rodar sozinho.
 * Cole este bloco DENTRO do Route::prefix('v1')->group(function () { ... })
 * que já existe em routes/api.php, exatamente na ordem abaixo.
 *
 * DESAFIO 5 está escondido na ORDEM das rotas. Depois de colar,
 * tente acessar GET /api/v1/pedidos/relatorio e veja o que acontece.
 */

use App\Http\Controllers\Api\V1\PedidoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('pedidos', PedidoController::class)->except(['update']);

Route::patch('pedidos/{pedido}/pagar', [PedidoController::class, 'marcarComoPago'])
    ->name('pedidos.pagar');

// Rota "solta" propositalmente na posição errada (parte do Desafio 5)
Route::get('pedidos/relatorio', function () {
    return response()->json([
        'total_pedidos' => \App\Models\Pedido::count(),
        'total_faturado' => \App\Models\Pedido::sum('total'),
    ]);
})->name('pedidos.relatorio');
