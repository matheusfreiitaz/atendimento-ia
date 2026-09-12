<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'preco' => (float) $this->preco,
            'estoque' => $this->estoque,
            'ativo' => $this->ativo,
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'criado_em' => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
