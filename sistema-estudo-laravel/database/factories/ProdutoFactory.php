<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public function definition(): array
    {
        return [
            'categoria_id' => Categoria::factory(),
            'sku' => 'SKU-' . $this->faker->unique()->numerify('#####'),
            'nome' => $this->faker->words(3, true),
            'descricao' => $this->faker->sentence(),
            'preco' => $this->faker->randomFloat(2, 10, 500),
            'estoque' => $this->faker->numberBetween(0, 100),
            'ativo' => true,
        ];
    }
}
