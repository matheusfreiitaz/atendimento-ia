# Desafios — Módulo de Pedidos (propositalmente quebrado)

O módulo `Pedido` (model, controller, request, rotas) tem **5 bugs de propósito**.
Cada um treina uma coisa diferente. Eu não vou te dar a solução aqui — só o
sintoma, onde procurar e como confirmar que você acertou.

Regra do jogo: **não altere as asserções dos testes** em
`tests/Feature/PedidoTest.php`. Você só pode mexer em `app/` e `routes/`.
Quando os 5 testes passarem (`php artisan test --filter=PedidoTest`), os 5
desafios estão resolvidos.

---

### Desafio 1 — Mass assignment (nível: iniciante)
**Sintoma:** ao criar um pedido, o campo `quantidade` some. `total` também
não bate a conta.
**Onde olhar:** `app/Models/Pedido.php` e `app/Http/Controllers/Api/V1/PedidoController.php::store`.
**Conceito envolvido:** `$fillable` no Eloquent — o que acontece quando você
tenta preencher em massa (`create()`) um campo que não está autorizado.
**Como confirmar:** `test_criar_pedido_calcula_total_corretamente` passa.

### Desafio 2 — Validação permissiva demais (nível: iniciante)
**Sintoma:** dá pra criar um pedido com `quantidade = 0` ou negativa.
**Onde olhar:** `app/Http/Requests/StorePedidoRequest.php`.
**Conceito envolvido:** regras de validação do Laravel (`required`, `min`,
`integer`) e por que `nullable` sozinho não impede valores "errados".
**Como confirmar:** `test_nao_permite_quantidade_zero_ou_negativa` passa.

### Desafio 3 — Consulta N+1 (nível: intermediário)
**Sintoma:** listar pedidos funciona, mas fica lento à medida que a
quantidade de pedidos cresce.
**Onde olhar:** `app/Http/Controllers/Api/V1/PedidoController.php::index`.
**Conceito envolvido:** eager loading (`with()`) vs lazy loading no Eloquent.
**Como investigar de verdade (não só rodar o teste):**
```php
// Cole isso temporariamente no topo de qualquer rota pra "ver" as queries:
\DB::listen(fn ($query) => logger($query->sql));
// depois: tail -f storage/logs/laravel.log
```
Conte quantas linhas de SQL aparecem pra listar 5 pedidos.
**Como confirmar:** `test_lista_pedidos_nao_gera_consultas_n_mais_1` passa.

### Desafio 4 — Bug de comparação de string (nível: intermediário)
**Sintoma:** o endpoint de "marcar como pago" não muda nada, mas também não
dá erro nenhum — silêncio total.
**Onde olhar:** `app/Http/Controllers/Api/V1/PedidoController.php::marcarComoPago`
e o valor `default` da coluna `status` na migration.
**Conceito envolvido:** comparação de strings sensível a maiúsculas/minúsculas
em PHP (`===`), e por que bugs "silenciosos" (sem exception) são os mais
perigosos em produção.
**Como confirmar:** `test_marcar_pedido_como_pago_atualiza_status` passa.

### Desafio 5 — Ordem das rotas (nível: avançado / clássico do Laravel)
**Sintoma:** `GET /api/v1/pedidos/relatorio` não retorna o relatório —
retorna um erro dizendo que não encontrou um pedido, ou um erro de tipo.
**Onde olhar:** `routes/api-desafios.php` — repare a ORDEM em que as rotas
foram declaradas.
**Conceito envolvido:** o Laravel casa rotas na ordem em que foram
registradas. Uma rota `pedidos/{pedido}` (vinda do `apiResource`) "casa" com
`pedidos/relatorio` antes que a rota literal `pedidos/relatorio` tenha
chance de ser considerada, porque `relatorio` vira o valor de `{pedido}`.
**Dica de caminho, não de solução:** o `php artisan route:list` mostra a
ordem real das rotas registradas. Rotas literais (sem `{}`) que colidem com
rotas de recurso geralmente precisam vir **antes** do `apiResource`.
**Como confirmar:** `test_rota_de_relatorio_nao_e_interpretada_como_id_de_pedido` passa.

---

## Como rodar o "placar" dos desafios

```bash
php artisan test --filter=PedidoTest
```

Cada teste que fica verde = um desafio resolvido. Isso é literalmente TDD:
o teste já existe descrevendo o comportamento correto, e você ajusta o
código até ele passar — sem precisar adivinhar se "parece certo".

## Depois de resolver os 5

Tente escrever, sem ajuda, **mais um teste** pra um comportamento que ainda
não está coberto: por exemplo, "não deve ser possível marcar como pago um
pedido que já está cancelado". Implemente a regra e o teste ao mesmo tempo.
Esse é o próximo nível: sair de "conserta o que já existe" para "especifica
o que deveria existir".
