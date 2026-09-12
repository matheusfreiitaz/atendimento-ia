<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = Categoria::all();

        foreach (range(1, 20) as $i) {
            $categoria = $categorias->random();

            Produto::create([
                'categoria_id' => $categoria->id,
                'sku' => 'SKU-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'nome' => "Produto de estudo {$i}",
                'descricao' => 'Produto gerado pelo seeder para testes de API.',
                'preco' => rand(1000, 50000) / 100,
                'estoque' => rand(0, 100),
                'ativo' => true,
            ]);
        }
    }
}
