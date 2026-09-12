<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pedido extends Model
{
    use HasFactory;

    // DESAFIO 1: existe um campo que o controller tenta preencher
    // via create()/update() mas que não está liberado aqui embaixo.
    // Rode os testes e leia a mensagem de erro com atenção.
    protected $fillable = [
        'produto_id', 'cliente_nome', 'preco_unitario', 'total', 'status',
    ];

    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
