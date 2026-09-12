<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Estes testes descrevem o COMPORTAMENTO CORRETO do módulo de Pedidos.
 * Rode com: php artisan test --filter=PedidoTest
 *
 * Enquanto os desafios do DESAFIOS.md não forem resolvidos, alguns
 * destes testes vão falhar. Seu objetivo: deixar todos verdes (PASS)
 * sem alterar as asserções dos testes - só o código de app/.
 */
class PedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_criar_pedido_calcula_total_corretamente(): void
    {
        $produto = Produto::factory()->create(['preco' => 50.00]);

        $response = $this->postJson('/api/v1/pedidos', [
            'produto_id' => $produto->id,
            'cliente_nome' => 'Paulo Estudante',
            'quantidade' => 3,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.quantidade', 3);
        $response->assertJsonPath('data.total', 150.0); // 50 * 3
    }

    public function test_nao_permite_quantidade_zero_ou_negativa(): void
    {
        $produto = Produto::factory()->create();

        $response = $this->postJson('/api/v1/pedidos', [
            'produto_id' => $produto->id,
            'cliente_nome' => 'Cliente Teste',
            'quantidade' => 0,
        ]);

        $response->assertStatus(422);
    }

    public function test_marcar_pedido_como_pago_atualiza_status(): void
    {
        $produto = Produto::factory()->create();
        $pedido = Pedido::factory()->for($produto)->create(['status' => 'pendente']);

        $response = $this->patchJson("/api/v1/pedidos/{$pedido->id}/pagar");

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', 'pago');
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'status' => 'pago']);
    }

    public function test_lista_pedidos_nao_gera_consultas_n_mais_1(): void
    {
        $produto = Produto::factory()->create();
        Pedido::factory()->for($produto)->count(5)->create();

        DB::enableQueryLog();
        $this->getJson('/api/v1/pedidos')->assertStatus(200);
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        // 1 query pra paginar pedidos + no máximo mais 1-2 pra carregar produtos
        // de uma vez (eager loading). Se aparecer 1 query POR pedido, algo está errado.
        $this->assertLessThanOrEqual(4, $queries, "Rodaram {$queries} queries - suspeita de N+1.");
    }

    public function test_rota_de_relatorio_nao_e_interpretada_como_id_de_pedido(): void
    {
        $response = $this->getJson('/api/v1/pedidos/relatorio');

        $response->assertStatus(200);
        $response->assertJsonStructure(['total_pedidos', 'total_faturado']);
    }
}
