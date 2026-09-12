<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = ['Informática', 'Livros', 'Papelaria', 'Eletrônicos'];

        foreach ($categorias as $nome) {
            Categoria::create([
                'nome' => $nome,
                'descricao' => "Produtos da categoria {$nome}",
            ]);
        }
    }
}
