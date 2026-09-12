<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * GET /api/v1/produtos
     *
     * Exemplos de query string para estudar (mande no Postman):
     *  ?nome=mouse
     *  ?preco_min=10&preco_max=100
     *  ?categoria_id=2
     *  ?ordenar_por=preco&direcao=desc
     *  ?por_pagina=5&pagina=2
     */
    public function index(Request $request): JsonResponse
    {
        $produtos = Produto::query()
            ->with('categoria')
            ->ativos()
            ->buscaPorNome($request->query('nome'))
            ->comPrecoEntre($request->query('preco_min'), $request->query('preco_max'))
            ->when($request->filled('categoria_id'), fn ($q) => $q->where('categoria_id', $request->query('categoria_id')))
            ->orderBy(
                in_array($request->query('ordenar_por'), ['nome', 'preco', 'estoque']) ? $request->query('ordenar_por') : 'id',
                $request->query('direcao') === 'desc' ? 'desc' : 'asc'
            )
            ->paginate($request->integer('por_pagina', 10));

        return ProdutoResource::collection($produtos)->response();
    }

    public function store(StoreProdutoRequest $request): JsonResponse
    {
        $produto = Produto::create($request->validated());

        return (new ProdutoResource($produto->load('categoria')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Produto $produto): JsonResponse
    {
        return (new ProdutoResource($produto->load('categoria')))->response();
    }

    public function update(StoreProdutoRequest $request, Produto $produto): JsonResponse
    {
        $produto->update($request->validated());

        return (new ProdutoResource($produto->load('categoria')))->response();
    }

    public function destroy(Produto $produto): JsonResponse
    {
        $produto->delete();

        return response()->json(['mensagem' => 'Produto removido com sucesso.'], 200);
    }
}
