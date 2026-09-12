# Plano de Estudos — Rotas, Eloquent, API, MongoDB e Nuvem

Duração sugerida: **4 semanas**, ~1h/dia em dias de semana (ajuste ao seu ritmo —
o importante é não pular a validação de cada etapa antes de avançar).

---

## Semana 1 — Fundamentos de rotas e Eloquent (projeto base: Categorias/Produtos)

**Objetivo:** entender de olhos fechados a diferença entre `Route::get` com
closure, `Route::apiResource`, rotas aninhadas e Route Model Binding.

| Dia | Tarefa | Prazo |
|---|---|---|
| 1 | Instalar o projeto, rodar migrations + seeders, ver o dashboard funcionando | 1 dia |
| 2 | Rodar `php artisan route:list` e desenhar (no papel mesmo) a URL de cada rota | 1 dia |
| 3 | Criar uma rota nova do zero: `GET /api/v1/produtos/{produto}/relacionados` que devolve outros produtos da mesma categoria | 1-2 dias |
| 4-5 | Ler o código de `CategoriaController` e `ProdutoController` linha por linha; explicar em voz alta (ou escrevendo) o que cada scope do `Produto` faz | 2 dias |

**Desafio da semana:** adicione um campo novo em `produtos` (ex: `marca`)
com migration, atualize o `Resource`, o `Request` e teste no Postman sem
olhar nenhum exemplo pronto.

**Como saber se está indo bem:** você consegue explicar, sem consultar nada,
por que `Route::apiResource` cria 5 rotas e quais são os verbos HTTP de
cada uma.

**Referências:**
- Documentação oficial de rotas: https://laravel.com/docs/routing
- Documentação de Eloquent: https://laravel.com/docs/eloquent
- Documentação de Eloquent Resources: https://laravel.com/docs/eloquent-resources

---

## Semana 2 — API "de verdade" + Postman

**Objetivo:** parar de pensar em "rota" isolada e começar a pensar em
contrato de API: request, validação, resposta, código HTTP.

| Dia | Tarefa | Prazo |
|---|---|---|
| 1 | Testar todos os endpoints de Categorias e Produtos no Postman, incluindo casos de **erro** (nome duplicado, campo faltando) | 1 dia |
| 2 | Criar um **Postman Environment** (não só a collection) com variáveis pra trocar entre local e Docker | 1 dia |
| 3 | Adicionar testes automáticos dentro do próprio Postman (aba "Tests" de cada request) validando `status code` e formato da resposta | 1-2 dias |
| 4-5 | Estudar HTTP status codes (200, 201, 204, 404, 422, 500) e revisar se cada endpoint do projeto devolve o código certo | 2 dias |

**Desafio da semana:** force cada tipo de erro de propósito (mande um
`categoria_id` que não existe, mande preço negativo, mande JSON quebrado) e
documente, num arquivo `TESTES-MANUAIS.md`, o que a API respondeu em cada
caso.

**Como saber se está indo bem:** você consegue prever o status code de uma
resposta *antes* de mandar a requisição.

**Referências:**
- Postman Learning Center: https://learning.postman.com/docs/getting-started/overview/
- Lista de status HTTP (MDN): https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Status
- Documentação de validação do Laravel: https://laravel.com/docs/validation

---

## Semana 3 — Módulo de Pedidos: aprendendo através de bugs

**Objetivo:** treinar debugging real — ler sintoma, formular hipótese,
confirmar com teste automatizado.

Siga o `DESAFIOS.md` do projeto. Ele tem 5 bugs, do mais fácil ao mais
difícil. **Não pule etapas olhando o código pronto de outro lugar** — o
ganho de aprendizado está no processo de investigar.

| Dia | Tarefa | Prazo |
|---|---|---|
| 1 | Rodar `php artisan test --filter=PedidoTest` e ler cada mensagem de erro com calma (a mensagem quase sempre diz o que está errado) | 1 dia |
| 2 | Resolver Desafio 1 e 2 (mass assignment + validação) | 1 dia |
| 3 | Resolver Desafio 3 (N+1) usando `DB::listen` pra *ver* o problema, não só confiar no teste | 1 dia |
| 4 | Resolver Desafio 4 (bug silencioso de comparação) | 1 dia |
| 5 | Resolver Desafio 5 (ordem de rotas) e rodar `php artisan route:list` antes/depois pra comparar | 1 dia |

**Desafio da semana (extra):** depois dos 5 resolvidos, escreva você mesmo
um sexto teste pra uma regra nova (ex: pedido cancelado não pode ser
"pago") e implemente.

**Como saber se está indo bem:** todos os testes de `PedidoTest` verdes,
e você consegue explicar cada bug em uma frase, sem reler o código.

**Referências:**
- Eager loading / problema N+1: https://laravel.com/docs/eloquent-relationships#eager-loading
- Testes em Laravel: https://laravel.com/docs/testing
- Debugging de queries (`DB::listen`, Laravel Telescope): https://laravel.com/docs/telescope

---

## Semana 4 — MongoDB + um pouco de nuvem

**Objetivo:** consolidar a diferença entre banco relacional (MySQL) e
documento (MongoDB), e entender a lógica de containers/deploy.

| Dia | Tarefa | Prazo |
|---|---|---|
| 1 | Subir com `docker compose up -d`, abrir o mongo-express e ler os logs gerados em `api_logs` | 1 dia |
| 2 | Adicionar um campo novo ao log (ex: `tempo_total_ms` já existe — adicione `payload_tamanho_bytes`) | 1 dia |
| 3 | Criar um endpoint `GET /api/v1/relatorios/erros` que consulta o MongoDB e devolve quantas requisições deram erro (status >= 400) nas últimas 24h | 1-2 dias |
| 4 | Ler sobre RDS (MySQL gerenciado) e MongoDB Atlas; escrever, em texto simples, o que mudaria no `.env` se você migrasse pra lá | 1 dia |
| 5 | Revisão geral: rode `php artisan test` (todo o projeto) e `php artisan route:list`, confira se tudo ainda funciona ponta a ponta pelo dashboard | 1 dia |

**Desafio da semana:** faça o endpoint de relatório de erros funcionar
consultando o Mongo diretamente via `ApiLog::where('status_code', '>=', 400)`.

**Como saber se está indo bem:** você consegue explicar pra alguém, em 2
frases, quando usar MySQL e quando usar MongoDB num sistema real.

**Referências:**
- Pacote MongoDB para Laravel: https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/
- Docker Compose: https://docs.docker.com/compose/
- Diferenças SQL vs NoSQL (visão geral): https://www.mongodb.com/resources/basics/databases/nosql-explained

---

## Checklist final de autoavaliação

- [ ] Consigo criar uma rota nova (com controller, request e resource) sem
      copiar de um exemplo pronto
- [ ] Sei explicar a diferença entre `Route::resource` e `Route::apiResource`
- [ ] Sei o que é N+1 e como resolver com `with()`
- [ ] Sei ler uma mensagem de erro de validação do Laravel e saber qual
      regra está falhando
- [ ] Consigo testar qualquer endpoint no Postman sem precisar de ajuda
- [ ] Sei por que um log de eventos combina melhor com Mongo do que com
      MySQL
- [ ] Sei o que muda (e o que não muda) no código quando se troca "rodar
      localmente" por "rodar em produção/nuvem"

Quando marcar todos, você já está pronto pro próximo passo natural:
autenticação de API (Sanctum) e talvez filas (queues) pra processar pedidos
de forma assíncrona.
