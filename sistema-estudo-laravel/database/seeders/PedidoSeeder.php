<?php

namespace Database\Seeders;

use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = Produto::all();

        foreach (range(1, 8) as $i) {
            $produto = $produtos->random();
            $quantidade = rand(1, 5);

            Pedido::create([
                'produto_id' => $produto->id,
                'cliente_nome' => "Cliente Estudo {$i}",
                'quantidade' => $quantidade,
                'preco_unitario' => $produto->preco,
                'total' => $produto->preco * $quantidade,
                'status' => 'pendente',
            ]);
        }
    }
}
