# Sistema de Estudo — Laravel + Eloquent + API + MongoDB

Projeto pequeno, focado em você aprender na prática:

- **Rotas** (rotas simples, `apiResource`, rotas aninhadas, prefixos, nomes de rota)
- **Eloquent** (relacionamento 1:N entre `Categoria` e `Produto`, scopes, casts)
- **API REST** consumindo vários campos (filtros por nome, preço, categoria, ordenação, paginação)
- **Postman** (coleção pronta pra importar)
- **MongoDB** (log de cada requisição da API, via `mongodb/laravel-mongodb`)
- **Um pouco de nuvem** (Docker/Docker Compose, variáveis de ambiente, ideia de deploy)

Este projeto **não é um Laravel instalado pronto** — ele contém só os arquivos que
você precisa entender e copiar por cima de um Laravel novo. É proposital: assim
você passa pelo processo real de `composer create-project` e só depois estuda
cada arquivo.

---

## 1. Como montar o projeto na sua máquina

```bash
# 1. Cria um Laravel novo do zero
composer create-project laravel/laravel sistema-estudo-laravel
cd sistema-estudo-laravel

# 2. Instala o pacote do MongoDB para Eloquent
composer require mongodb/laravel-mongodb

# 3. Copie por cima os arquivos deste pacote que te entreguei:
#    app/Models/*, app/Http/*, database/migrations/*, database/seeders/*,
#    routes/api.php, docker-compose.yml, Dockerfile, .env.example
```

Depois, aplique os dois "snippets" de configuração que não entram automaticamente:

- `config/database_snippet.php` → cole o bloco `'mongodb' => [...]` dentro de
  `config/database.php`, no array `connections`.
- `config/bootstrap_app_snippet.php` → no Laravel 11, mostra onde registrar o
  middleware `LogApiRequests` dentro de `bootstrap/app.php`.

> Se você estiver numa versão do Laravel anterior à 11 (estrutura com
> `app/Http/Kernel.php`), registre o middleware no array `$middlewareGroups['api']`
> do Kernel, no lugar do bloco `withMiddleware`.

## 2. Subindo com Docker (a parte de "nuvem")

```bash
docker compose up -d
```

Isso sobe 4 containers:

| Container       | Papel                                              |
|-----------------|-----------------------------------------------------|
| `app`           | PHP + `artisan serve`, sua aplicação Laravel        |
| `mysql`         | Banco relacional principal (categorias/produtos)    |
| `mongodb`       | Banco de log das requisições                        |
| `mongo-express` | Interface web pra ver os logs (http://localhost:8081) |

**Por que isso ensina "nuvem"**: em produção, cada um desses containers vira
tipicamente um serviço gerenciado separado — ex: aplicação numa instância EC2 /
Elastic Beanstalk / App Runner, MySQL num RDS, Mongo num Atlas. Rodar com Docker
localmente já te acostuma com a ideia de **cada peça sendo independente**,
configurada só por variáveis de ambiente (o `.env`), o que é exatamente o que
muda quando você troca de "minha máquina" para "nuvem": só o `.env` muda.

Depois que os containers subirem:

```bash
docker compose exec app php artisan migrate --seed
```

## 3. Estudando as rotas (`routes/api.php`)

O arquivo tem, de propósito, vários estilos de rota:

1. **Rota fechada, sem controller** — `/api/v1/status`, só pra você ver o
   básico de `Route::get` com uma closure.
2. **`Route::apiResource`** — cria as 5 rotas de CRUD de uma vez
   (`index, store, show, update, destroy`). É o padrão que você vai usar 90%
   do tempo em API REST.
3. **Rota aninhada** — `/api/v1/categorias/{categoria}/produtos`, pra estudar
   como expressar "produtos de uma categoria" na URL.
4. **Prefixo + nome de grupo** — `Route::prefix('v1')->name('v1.')`, útil
   quando você versiona API (`v1`, `v2`...).

Rode `php artisan route:list` a qualquer momento para ver a tabela real de
rotas geradas — é o comando mais importante pra quem está aprendendo rotas.

## 4. Testando com Postman

1. Abra o Postman → **Import** → selecione
   `postman/Sistema-Estudo-Laravel.postman_collection.json`.
2. A variável `base_url` já vem configurada como `http://localhost:8000/api/v1`
   (ajuste se você rodar `php artisan serve` numa porta diferente).
3. Comece por **Status da API**, depois **Categorias - Listar**, e vá seguindo
   a ordem: criar categoria → criar produto → listar produtos com filtro →
   atualizar → remover.

Filtros de produto pra você testar mudando a query string na aba **Params** do
Postman:

- `?nome=produto`
- `?preco_min=10&preco_max=200`
- `?categoria_id=2`
- `?ordenar_por=preco&direcao=desc`
- `?por_pagina=5&pagina=2`

## 5. Onde entra o MongoDB

Toda requisição que passa pelo grupo de rotas `api` passa também pelo
middleware `App\Http\Middleware\LogApiRequests`, que grava um documento na
coleção `api_logs` do Mongo com: método HTTP, rota, parâmetros enviados,
status da resposta, tempo de resposta em ms, IP e user agent.

Pra ver os logs:

- Via **mongo-express**: http://localhost:8081 → banco `estudo_laravel_logs` →
  coleção `api_logs`.
- Via **Tinker**:
  ```bash
  docker compose exec app php artisan tinker
  >>> App\Models\ApiLog::latest('_id')->take(5)->get();
  ```

Isso te dá, na prática, o contraste entre um Model Eloquent "normal" (MySQL,
com migration e schema fixo) e um Model Eloquent sobre Mongo (schemaless,
sem migration).

## 6. Frontend simples (pra ver funcionando, não só no Postman)

Existe um dashboard bem simples em `resources/views/dashboard.blade.php`
(HTML + JS puro, sem build step, sem framework) servido pela rota `/` em
`routes/web.php`. Ele consome a mesma API via `fetch()` no navegador.

Depois de rodar `php artisan serve`, acesse `http://localhost:8000/` e você
verá abas de Categorias, Produtos e Pedidos, com formulários e tabelas.

## 7. Módulo de Pedidos — propositalmente quebrado

Além de Categorias e Produtos (que funcionam 100%), o projeto tem um
terceiro módulo, **Pedidos**, com **5 bugs colocados de propósito** — é o
seu material de estudo de debugging. Veja o arquivo `DESAFIOS.md` para os
sintomas, dicas (sem spoiler da solução) e como confirmar que você acertou
usando os testes automatizados de `tests/Feature/PedidoTest.php`.

Para habilitar o módulo:
1. Copie o conteúdo de `routes/api-desafios.php` para dentro do grupo
   `Route::prefix('v1')->group(...)` em `routes/api.php`.
2. Rode as migrations (`php artisan migrate`) — a tabela `pedidos` já está
   incluída.
3. Rode `php artisan test --filter=PedidoTest` e comece a investigar.

## 8. Plano de estudos completo

O arquivo `PLANO-DE-ESTUDOS.md` tem um cronograma de 4 semanas com tarefas
diárias, desafios, prazos sugeridos, como se autoavaliar e referências
oficiais pra cada tema (rotas, Eloquent, Postman, N+1, MongoDB, nuvem).

## 9. Próximos passos sugeridos (pra quando dominar o básico)

- Adicionar autenticação de API com Laravel Sanctum (tokens).
- Trocar `php artisan serve` por Nginx + PHP-FPM no `docker-compose.yml`.
- Subir o MySQL pra um RDS e o Mongo pra um Atlas, mantendo só o `.env` mudando.
- Adicionar cache de consultas com Redis (mais uma peça de "nuvem" pra estudar).
