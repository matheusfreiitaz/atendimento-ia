<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePedidoRequest;
use App\Http\Resources\PedidoResource;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // DESAFIO 3: repare se essa query carrega o produto de cada pedido
    // de forma eficiente ou não. Ative o log de queries (veja DESAFIOS.md)
    // e conte quantas queries rodam pra listar 10 pedidos.
    public function index(): JsonResponse
    {
        $pedidos = Pedido::query()->latest()->paginate(10);

        return PedidoResource::collection($pedidos)->response();
    }

    public function store(StorePedidoRequest $request): JsonResponse
    {
        $produto = Produto::findOrFail($request->validated('produto_id'));

        // DESAFIO 1 continua aqui: 'quantidade' é usada pra calcular o total,
        // mas o create() abaixo tenta salvar os campos validados de uma vez.
        $total = $produto->preco * $request->validated('quantidade');

        $pedido = Pedido::create([
            ...$request->validated(),
            'preco_unitario' => $produto->preco,
            'total' => $total,
        ]);

        return (new PedidoResource($pedido->load('produto')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Pedido $pedido): JsonResponse
    {
        return (new PedidoResource($pedido->load('produto')))->response();
    }

    // DESAFIO 4: tente marcar um pedido como pago e depois consultar
    // de novo. O status realmente muda?
    public function marcarComoPago(Pedido $pedido): JsonResponse
    {
        if ($pedido->status === 'Pendente') {
            $pedido->status = 'pago';
            $pedido->save();
        }

        return (new PedidoResource($pedido->load('produto')))->response();
    }

    public function destroy(Pedido $pedido): JsonResponse
    {
        $pedido->delete();

        return response()->json(['mensagem' => 'Pedido removido.'], 200);
    }
}
