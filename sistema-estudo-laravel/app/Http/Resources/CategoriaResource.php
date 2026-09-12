<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'ativo' => $this->ativo,
            // só carrega produtos se o controller usou with('produtos')
            'produtos' => ProdutoResource::collection($this->whenLoaded('produtos')),
            'criado_em' => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
