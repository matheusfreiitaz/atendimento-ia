<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria_id', 'sku', 'nome', 'descricao', 'preco', 'estoque', 'ativo',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
        'ativo' => 'boolean',
    ];

    // Relacionamento inverso -> um produto pertence a uma categoria
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    // Scopes reutilizáveis para os filtros da API (aprender a "consumir vários campos")
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeComPrecoEntre($query, $min, $max)
    {
        if ($min !== null) {
            $query->where('preco', '>=', $min);
        }
        if ($max !== null) {
            $query->where('preco', '<=', $max);
        }
        return $query;
    }

    public function scopeBuscaPorNome($query, $termo)
    {
        return $termo ? $query->where('nome', 'like', "%{$termo}%") : $query;
    }
}
