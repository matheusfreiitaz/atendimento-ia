<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cliente_nome' => $this->cliente_nome,
            'quantidade' => $this->quantidade,
            'preco_unitario' => (float) $this->preco_unitario,
            'total' => $this->total !== null ? (float) $this->total : null,
            'status' => $this->status,
            // DESAFIO 3 (performance): isso aqui acessa a relação produto.
            // Se o controller que busca a lista de pedidos não usar with('produto'),
            // cada pedido dispara uma query extra pra buscar o produto (N+1).
            'produto' => $this->whenLoaded('produto', fn () => [
                'id' => $this->produto->id,
                'nome' => $this->produto->nome,
            ]),
            'criado_em' => $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
