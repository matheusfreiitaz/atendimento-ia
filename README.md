# 🚀 Sistema de Estudos Laravel (API REST & Dashboard)

Projeto desenvolvido com o objetivo de aprimorar e consolidar os conhecimentos avançados em **Laravel**, abrangendo o desenvolvimento de APIs RESTful estruturadas, controle de requisições, middlewares customizados, testes automatizados e containerização com Docker.

---

## 🛠️ Tecnologias e Ferramentas Utilizadas

- **PHP 8.2+**
- **Laravel 10/11**
- **Banco de Dados:** MySQL / PostgreSQL
- **Documentação de Testes:** Postman (Collection inclusa)
- **Containerização:** Docker & Docker Compose
- **Testes:** PHPUnit / Pest (Testes de Feature)

---

## ⚙️ Funcionalidades do Sistema

- **API RESTful Versionada (v1):** Endpoints completos para gerenciamento de Categorias, Produtos e Pedidos.
- **Form Requests & Resources:** Validação estrita de dados de entrada e transformação limpa das respostas JSON.
- **Middlewares Personalizados:** Sistema de auditoria e logging automático de requisições da API (`ApiLog`).
- **Seeders e Factories:** Geração de dados sintéticos em massa para popular o banco de dados rapidamente.
- **Testes Automatizados:** Cobertura de testes de feature focados em garantir a integridade dos fluxos críticos (ex: Pedidos).
- **Ambiente Dockerizado:** Pronto para subir com apenas um comando, sem complicações com dependências locais.

---

## 📁 Estrutura do Projeto

```text
sistema-estudo-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/V1/  # Controladores da API (Categorias, Produtos, Pedidos)
│   │   ├── Middleware/          # Middleware de Log de Requisições
│   │   ├── Requests/            # Validações de formulários (Store requests)
│   │   └── Resources/           # API Resources para formatação de saída
│   └── Models/                  # Modelos Eloquent (Categoria, Produto, Pedido, ApiLog)
├── database/
│   ├── factories/               # Fábricas para geração de dados fake
│   ├── migrations/              # Estrutura do banco de dados
│   └── seeders/                 # Alimentadores de dados iniciais
├── docker/                      # Configurações do Docker
├── postman/                     # Collection do Postman para testes dos endpoints
├── routes/
│   ├── api.php                  # Rotas principais da API
│   ├── api-desafios.php         # Rotas de desafios práticos
│   └── web.php                  # Rotas web/dashboard
└── tests/                       # Testes automatizados (Feature)
