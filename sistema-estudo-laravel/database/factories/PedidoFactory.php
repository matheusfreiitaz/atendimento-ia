<?php

namespace Database\Factories;

use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        $quantidade = $this->faker->numberBetween(1, 5);

        return [
            'produto_id' => Produto::factory(),
            'cliente_nome' => $this->faker->name(),
            'quantidade' => $quantidade,
            'preco_unitario' => $this->faker->randomFloat(2, 10, 200),
            'total' => null, // propositalmente null - veja DESAFIOS.md
            'status' => 'pendente',
        ];
    }
}
