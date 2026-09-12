<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    // GET /api/v1/categorias
    public function index(Request $request): JsonResponse
    {
        $categorias = Categoria::query()
            ->when($request->boolean('com_produtos'), fn ($q) => $q->with('produtos'))
            ->paginate($request->integer('por_pagina', 10));

        return CategoriaResource::collection($categorias)->response();
    }

    // POST /api/v1/categorias
    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $categoria = Categoria::create($request->validated());

        return (new CategoriaResource($categoria))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/v1/categorias/{categoria}
    // O {categoria} vira automaticamente um model via Route Model Binding
    public function show(Categoria $categoria): JsonResponse
    {
        return (new CategoriaResource($categoria->load('produtos')))->response();
    }

    // PUT/PATCH /api/v1/categorias/{categoria}
    public function update(StoreCategoriaRequest $request, Categoria $categoria): JsonResponse
    {
        $categoria->update($request->validated());

        return (new CategoriaResource($categoria))->response();
    }

    // DELETE /api/v1/categorias/{categoria}
    public function destroy(Categoria $categoria): JsonResponse
    {
        $categoria->delete();

        return response()->json(['mensagem' => 'Categoria removida com sucesso.'], 200);
    }
}
