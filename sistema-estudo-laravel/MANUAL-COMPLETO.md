# Manual Completo — Sistema de Estudo Laravel (Rotas, Eloquent, API, MongoDB e Nuvem)

Este é o documento de referência do projeto. Os outros arquivos (`README.md`,
`DESAFIOS.md`, `PLANO-DE-ESTUDOS.md`) continuam valendo — este manual amarra
tudo, explica o "porquê" das escolhas e te dá um checklist de verificação
em cada etapa.

---

## 1. Visão geral do sistema

O projeto simula, em escala pequena, uma API de e-commerce simples:

```
Categoria (1) ──< (N) Produto (1) ──< (N) Pedido
```

- **Categoria** e **Produto**: módulo de referência, 100% funcional. Use
  como "gabarito" de como o resto deveria se parecer.
- **Pedido**: módulo com 5 bugs propositais (ver `DESAFIOS.md`).
- **ApiLog** (MongoDB): não é um módulo de negócio, é infraestrutura — grava
  automaticamente cada chamada de API feita ao sistema.
- **Dashboard** (`resources/views/dashboard.blade.php`): frontend mínimo,
  só pra você ver os dados sem depender do Postman o tempo todo.

Fluxo de uma requisição típica (`POST /api/v1/produtos`):

```
Postman/Dashboard
   → routes/api.php (decide qual controller chama)
   → LogApiRequests (middleware, grava início da requisição)
   → StoreProdutoRequest (valida os dados)
   → ProdutoController::store (regra de negócio)
   → Produto::create() (Eloquent grava no MySQL)
   → ProdutoResource (formata a resposta em JSON)
   → LogApiRequests (grava no Mongo o resultado: status, tempo)
   → resposta volta pro cliente
```

Entender esse fluxo de cor é mais importante do que decorar qualquer
arquivo específico — é o roteiro mental que você vai reusar em qualquer
projeto Laravel daqui pra frente.

---

## 2. O que eu acho importante você saber (opinião de quem já viu isso dar errado)

- **Migration não é "criar tabela", é histórico.** Nunca edite uma migration
  que já rodou em algum ambiente (nem que seja só o seu `local`). Crie uma
  nova migration pra alterar. Isso evita 90% dos "funciona na minha máquina".
- **Um `$fillable` errado é o bug mais comum e mais silencioso do Laravel.**
  Ele não dá erro — ele simplesmente ignora o campo. É por isso que o
  Desafio 1 existe: pra você sentir esse comportamento na pele uma vez, e
  nunca mais esquecer de checar isso primeiro quando um campo "some".
- **Validação é regra de negócio, não burocracia.** `StoreProdutoRequest` e
  `StorePedidoRequest` não são só "chatice do framework" — são o único lugar
  garantido onde dado ruim é barrado antes de tocar no banco.
- **Route Model Binding (`Produto $produto` direto no método) economiza
  código, mas esconde uma query.** Sempre que um parâmetro de rota vira um
  objeto automaticamente, tem uma consulta ao banco acontecendo ali, antes
  do seu código rodar.
- **Testes automatizados não são "só pra quem é sênior".** O `PedidoTest.php`
  existe justamente pra você aprender testando — é mais rápido descobrir
  se algo quebrou rodando `php artisan test` do que testando manualmente
  toda vez no Postman.
- **MongoDB aqui não substitui o MySQL — eles têm papéis diferentes.**
  Dados que têm relacionamento forte e precisam de consistência (categoria,
  produto, pedido) → MySQL. Dados de volume alto, formato variável, e que
  você quase nunca vai "editar" (logs, eventos, histórico) → Mongo. Não é
  "Mongo é mais moderno", é "cada ferramenta resolve um problema".

---

## 3. Etapas sugeridas (ordem recomendada)

1. **Instalação e verificação do ambiente** (seção 4 abaixo)
2. **Explorar o módulo pronto** (Categoria/Produto) — ler o código, testar
   no Postman, testar no dashboard
3. **Resolver os 5 desafios do módulo Pedido** — usando os testes como guia
4. **Seguir o `PLANO-DE-ESTUDOS.md`** semana a semana
5. **Adicionar uma funcionalidade sua, do zero**, sem exemplo pra copiar
   (sugestão: um endpoint de "produtos mais vendidos", cruzando Produto e
   Pedido)
6. **Revisão geral**: rodar toda a suíte de testes, `route:list` completo,
   dashboard funcionando ponta a ponta

Não pule a etapa 2 para ir direto pro Pedido — entender o "certo" antes do
"errado" é o que te dá o contraste necessário pra reconhecer o bug.

---

## 4. Onde e como testar a correção do sistema

Existem 5 camadas de verificação, cada uma serve pra um tipo de erro
diferente. Use todas, não só uma.

### 4.1 `php artisan route:list` — a rota existe e está correta?
```bash
php artisan route:list --path=api
```
Confirma: método HTTP certo, URL certa, nome da rota, qual controller/ação
está amarrado. É o primeiro comando a rodar quando uma rota dá 404
inesperado.

### 4.2 Postman — o contrato da API está certo?
Importe `postman/Sistema-Estudo-Laravel.postman_collection.json`. Para cada
endpoint, teste:
- **Caminho feliz**: dados válidos → espera 200/201 e o JSON certo.
- **Caminho de erro**: dado inválido/faltando → espera 422 com mensagem
  clara.
- **Caminho de "não existe"**: ID inexistente → espera 404.

### 4.3 `php artisan test` — o comportamento continua correto depois que eu mexi no código?
```bash
php artisan test                       # roda tudo
php artisan test --filter=PedidoTest   # só o módulo de pedidos
```
Essa é a rede de segurança: sempre que você alterar algo em `app/`, rode os
testes de novo antes de seguir em frente.

### 4.4 `php artisan tinker` — o dado no banco está do jeito que eu acho que está?
```bash
php artisan tinker
>>> App\Models\Produto::with('categoria')->first();
>>> App\Models\Pedido::where('status', 'pago')->count();
>>> App\Models\ApiLog::latest('_id')->first();
```
Use isso quando o Postman devolve algo estranho e você precisa confirmar se
o problema é no banco ou no controller/resource.

### 4.5 Dashboard + mongo-express — visualmente, está tudo consistente?
- Dashboard (`http://localhost:8000`) pra ver os dados como um usuário veria.
- mongo-express (`http://localhost:8081`) pra ver os logs crus da API,
  incluindo status code e tempo de resposta de cada chamada — útil pra achar
  picos de lentidão (relacionado ao Desafio 3, de N+1).

### Tabela-resumo: qual ferramenta usar pra qual sintoma

| Sintoma | Primeira ferramenta a usar |
|---|---|
| "Dá 404 numa rota que eu jurava que existia" | `route:list` |
| "A API aceitou um dado que não devia" | Postman (mande o dado inválido de propósito) |
| "Eu arrumei um bug mas não sei se quebrei outra coisa" | `php artisan test` |
| "O JSON de resposta veio com campo errado/faltando" | `tinker` (ver o dado puro) + o `Resource` correspondente |
| "Está lento" | mongo-express (ver `tempo_resposta_ms` nos logs) + `DB::listen` |

---

## 5. O que esperar deste projeto (e o que não esperar)

**Está incluso:**
- API REST completa (Categoria/Produto) como referência de "código correto"
- Um módulo (Pedido) propositalmente com bugs, para prática de debugging
- Testes automatizados que funcionam como gabarito de correção
- Frontend simples só pra visualização (não é um sistema de produção)
- Ambiente Docker básico (MySQL + Mongo + app)

**NÃO está incluso (de propósito, pra não desviar o foco do que você pediu
estudar primeiro):**
- Autenticação/autorização (login, tokens, permissões) — próximo passo natural
- Filas/jobs assíncronos
- Cache (Redis)
- Testes de carga/performance real
- Deploy real em nuvem (o Docker Compose é só uma simulação didática local)
- Frontend com framework (React/Vue) — o dashboard é intencionalmente cru

Se em algum momento o comportamento do sistema não bater com o que este
manual descreve, o objetivo é você primeiro tentar diagnosticar usando a
seção 4 antes de pedir ajuda — é assim que o hábito de debugging se forma.

---

## 6. Comandos essenciais (cola rápida)

```bash
# Setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Desenvolvimento
php artisan serve
php artisan route:list --path=api
php artisan tinker

# Testes
php artisan test
php artisan test --filter=PedidoTest

# Docker
docker compose up -d
docker compose exec app php artisan migrate --seed
docker compose logs -f app

# Banco
php artisan migrate:fresh --seed   # reseta tudo do zero
```

---

## 7. Glossário rápido (pra quando bater a dúvida "o que é isso mesmo?")

- **Rota (route):** a "porta de entrada" — associa uma URL + verbo HTTP a um
  código que vai rodar.
- **Controller:** onde fica a lógica do que fazer quando a rota é chamada.
- **Model (Eloquent):** representa uma tabela do banco como uma classe PHP.
- **Migration:** um "commit" da estrutura do banco (criar/alterar tabela).
- **Seeder:** popula o banco com dados de teste.
- **Factory:** gera dados fake de forma programática (usado nos testes).
- **Request (Form Request):** classe dedicada só pra validar dados de
  entrada antes de chegar no controller.
- **Resource (API Resource):** formata o Model em JSON antes de responder.
- **Middleware:** código que roda "no meio do caminho" de toda requisição
  (ex: nosso `LogApiRequests`).
- **Eager loading / N+1:** carregar relacionamentos de uma vez (`with()`) em
  vez de disparar uma query por item de uma lista.
- **Route Model Binding:** o Laravel busca o registro no banco
  automaticamente a partir do ID na URL.

---

## 8. Como usar este manual no dia a dia

Não é pra ler de uma vez só. Sugestão de uso:
- Antes de começar a semana do `PLANO-DE-ESTUDOS.md`, releia a seção 3
  (etapas) pra saber onde você está.
- Quando algo quebrar, vá direto na seção 4 (tabela-resumo) antes de sair
  procurando no Google.
- Quando terminar os desafios, releia a seção 2 — provavelmente vai fazer
  mais sentido na segunda leitura do que na primeira.
