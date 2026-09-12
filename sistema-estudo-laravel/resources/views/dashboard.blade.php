<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel - Sistema Estudo Laravel</title>
<style>
  :root { --cor-primaria:#2563eb; --cor-fundo:#0f172a; --cor-card:#1e293b; --cor-texto:#e2e8f0; --cor-borda:#334155; }
  * { box-sizing: border-box; }
  body { margin:0; font-family: system-ui, sans-serif; background: var(--cor-fundo); color: var(--cor-texto); }
  header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--cor-borda); }
  header h1 { margin:0; font-size:1.4rem; }
  header p { margin:.25rem 0 0; color:#94a3b8; font-size:.9rem; }
  nav { display:flex; gap:.5rem; padding: 0 2rem; border-bottom: 1px solid var(--cor-borda); }
  nav button { background:none; border:none; color:#94a3b8; padding: .8rem 1rem; cursor:pointer; font-size:.95rem; border-bottom: 2px solid transparent; }
  nav button.ativo { color: var(--cor-primaria); border-color: var(--cor-primaria); }
  main { padding: 1.5rem 2rem; max-width: 1100px; margin: 0 auto; }
  .painel { display:none; }
  .painel.ativo { display:block; }
  .toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; gap:1rem; flex-wrap: wrap;}
  .toolbar input, .toolbar select { background:var(--cor-card); border:1px solid var(--cor-borda); color:var(--cor-texto); padding:.5rem; border-radius:6px; }
  button.acao { background: var(--cor-primaria); color:#fff; border:none; padding:.5rem 1rem; border-radius:6px; cursor:pointer; }
  button.acao.perigo { background:#dc2626; }
  button.acao.secundario { background: var(--cor-card); border:1px solid var(--cor-borda); }
  table { width:100%; border-collapse: collapse; background: var(--cor-card); border-radius: 8px; overflow:hidden; }
  th, td { text-align:left; padding:.7rem .9rem; border-bottom:1px solid var(--cor-borda); font-size:.9rem; }
  th { color:#94a3b8; font-weight:600; }
  .tag { padding:.15rem .5rem; border-radius:999px; font-size:.75rem; }
  .tag.pendente { background:#78350f; color:#fde68a; }
  .tag.pago { background:#14532d; color:#bbf7d0; }
  .tag.cancelado { background:#7f1d1d; color:#fecaca; }
  form.form-inline { display:grid; grid-template-columns: repeat(auto-fit, minmax(140px,1fr)); gap:.6rem; background: var(--cor-card); padding:1rem; border-radius:8px; margin-bottom:1rem; }
  form.form-inline input, form.form-inline select { background:#0f172a; border:1px solid var(--cor-borda); color:var(--cor-texto); padding:.5rem; border-radius:6px; }
  .status-msg { font-size:.85rem; padding:.6rem; border-radius:6px; margin-bottom:1rem; }
  .status-msg.erro { background:#7f1d1d33; color:#fecaca; border:1px solid #7f1d1d; }
  .status-msg.sucesso { background:#14532d33; color:#bbf7d0; border:1px solid #14532d; }
  .acoes-linha { display:flex; gap:.4rem; }
  .acoes-linha button { font-size:.8rem; padding:.3rem .6rem; }
  #config { position:fixed; bottom:1rem; right:1rem; background:var(--cor-card); border:1px solid var(--cor-borda); padding:.6rem .9rem; border-radius:8px; font-size:.8rem; }
  #config input { background:#0f172a; border:1px solid var(--cor-borda); color:var(--cor-texto); padding:.3rem; border-radius:4px; width:220px; }
</style>
</head>
<body>

<header>
  <h1>Painel de Estudo — Sistema Laravel</h1>
  <p>Frontend simples (HTML + JS puro) só pra visualizar a API funcionando. Nenhum framework aqui de propósito.</p>
</header>

<nav>
  <button data-painel="categorias" class="ativo">Categorias</button>
  <button data-painel="produtos">Produtos</button>
  <button data-painel="pedidos">Pedidos</button>
</nav>

<main>
  <div id="msg"></div>

  <!-- CATEGORIAS -->
  <section id="painel-categorias" class="painel ativo">
    <form class="form-inline" id="form-categoria">
      <input name="nome" placeholder="Nome da categoria" required>
      <input name="descricao" placeholder="Descrição">
      <button class="acao" type="submit">Adicionar categoria</button>
    </form>
    <table>
      <thead><tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Produtos</th><th></th></tr></thead>
      <tbody id="tbody-categorias"></tbody>
    </table>
  </section>

  <!-- PRODUTOS -->
  <section id="painel-produtos" class="painel">
    <div class="toolbar">
      <input id="filtro-produto-nome" placeholder="Buscar por nome...">
      <select id="filtro-produto-ordenar">
        <option value="">Ordenar por...</option>
        <option value="nome">Nome</option>
        <option value="preco">Preço</option>
        <option value="estoque">Estoque</option>
      </select>
      <button class="acao secundario" id="btn-filtrar-produtos">Filtrar</button>
    </div>
    <form class="form-inline" id="form-produto">
      <select name="categoria_id" id="select-categoria-produto" required></select>
      <input name="sku" placeholder="SKU" required>
      <input name="nome" placeholder="Nome do produto" required>
      <input name="preco" type="number" step="0.01" placeholder="Preço" required>
      <input name="estoque" type="number" placeholder="Estoque" required>
      <button class="acao" type="submit">Adicionar produto</button>
    </form>
    <table>
      <thead><tr><th>SKU</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th></th></tr></thead>
      <tbody id="tbody-produtos"></tbody>
    </table>
  </section>

  <!-- PEDIDOS -->
  <section id="painel-pedidos" class="painel">
    <p style="color:#94a3b8; font-size:.85rem;">
      Este painel só funciona 100% depois que você resolver os desafios do módulo de Pedidos
      (veja DESAFIOS.md). É esperado que algumas ações aqui falhem por enquanto.
    </p>
    <form class="form-inline" id="form-pedido">
      <select name="produto_id" id="select-produto-pedido" required></select>
      <input name="cliente_nome" placeholder="Nome do cliente" required>
      <input name="quantidade" type="number" min="1" placeholder="Quantidade" required>
      <button class="acao" type="submit">Criar pedido</button>
    </form>
    <table>
      <thead><tr><th>ID</th><th>Cliente</th><th>Produto</th><th>Qtd</th><th>Total</th><th>Status</th><th></th></tr></thead>
      <tbody id="tbody-pedidos"></tbody>
    </table>
  </section>
</main>

<div id="config">
  API base:
  <input id="input-base-url" value="http://localhost:8000/api/v1">
</div>

<script>
const $ = (sel, ctx=document) => ctx.querySelector(sel);
const $$ = (sel, ctx=document) => [...ctx.querySelectorAll(sel)];

function baseUrl() { return $('#input-base-url').value.replace(/\/$/, ''); }

function mostrarMensagem(texto, tipo='sucesso') {
  const el = $('#msg');
  el.innerHTML = `<div class="status-msg ${tipo}">${texto}</div>`;
  setTimeout(() => el.innerHTML = '', 4000);
}

async function api(metodo, caminho, corpo=null) {
  const opts = { method: metodo, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' } };
  if (corpo) opts.body = JSON.stringify(corpo);
  const resp = await fetch(`${baseUrl()}${caminho}`, opts);
  const dados = await resp.json().catch(() => ({}));
  if (!resp.ok) {
    const erro = dados.message || dados.errors ? JSON.stringify(dados.errors || dados.message) : `Erro HTTP ${resp.status}`;
    throw new Error(erro);
  }
  return dados;
}

// --- Navegação entre painéis ---
$$('nav button').forEach(btn => {
  btn.addEventListener('click', () => {
    $$('nav button').forEach(b => b.classList.remove('ativo'));
    $$('.painel').forEach(p => p.classList.remove('ativo'));
    btn.classList.add('ativo');
    $(`#painel-${btn.dataset.painel}`).classList.add('ativo');
  });
});

// --- CATEGORIAS ---
async function carregarCategorias() {
  try {
    const dados = await api('GET', '/categorias?com_produtos=true&por_pagina=50');
    const linhas = dados.data.map(c => `
      <tr>
        <td>${c.id}</td><td>${c.nome}</td><td>${c.descricao ?? ''}</td>
        <td>${(c.produtos || []).length}</td>
        <td><button class="acao perigo" onclick="removerCategoria(${c.id})">Remover</button></td>
      </tr>`).join('');
    $('#tbody-categorias').innerHTML = linhas || '<tr><td colspan="5">Nenhuma categoria ainda.</td></tr>';

    const select = $('#select-categoria-produto');
    select.innerHTML = dados.data.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
  } catch (e) { mostrarMensagem('Erro ao carregar categorias: ' + e.message, 'erro'); }
}

$('#form-categoria').addEventListener('submit', async (e) => {
  e.preventDefault();
  const dados = Object.fromEntries(new FormData(e.target));
  try {
    await api('POST', '/categorias', dados);
    mostrarMensagem('Categoria criada!');
    e.target.reset();
    carregarCategorias();
  } catch (err) { mostrarMensagem(err.message, 'erro'); }
});

async function removerCategoria(id) {
  try { await api('DELETE', `/categorias/${id}`); mostrarMensagem('Categoria removida.'); carregarCategorias(); }
  catch (e) { mostrarMensagem(e.message, 'erro'); }
}

// --- PRODUTOS ---
async function carregarProdutos() {
  const nome = $('#filtro-produto-nome').value;
  const ordenar = $('#filtro-produto-ordenar').value;
  let qs = '?por_pagina=50';
  if (nome) qs += `&nome=${encodeURIComponent(nome)}`;
  if (ordenar) qs += `&ordenar_por=${ordenar}`;

  try {
    const dados = await api('GET', `/produtos${qs}`);
    $('#tbody-produtos').innerHTML = dados.data.map(p => `
      <tr>
        <td>${p.sku}</td><td>${p.nome}</td><td>${p.categoria?.nome ?? '-'}</td>
        <td>R$ ${Number(p.preco).toFixed(2)}</td><td>${p.estoque}</td>
        <td><button class="acao perigo" onclick="removerProduto(${p.id})">Remover</button></td>
      </tr>`).join('') || '<tr><td colspan="6">Nenhum produto encontrado.</td></tr>';

    const select = $('#select-produto-pedido');
    select.innerHTML = dados.data.map(p => `<option value="${p.id}">${p.nome} (R$ ${Number(p.preco).toFixed(2)})</option>`).join('');
  } catch (e) { mostrarMensagem('Erro ao carregar produtos: ' + e.message, 'erro'); }
}

$('#btn-filtrar-produtos').addEventListener('click', carregarProdutos);

$('#form-produto').addEventListener('submit', async (e) => {
  e.preventDefault();
  const dados = Object.fromEntries(new FormData(e.target));
  try {
    await api('POST', '/produtos', dados);
    mostrarMensagem('Produto criado!');
    e.target.reset();
    carregarProdutos();
  } catch (err) { mostrarMensagem(err.message, 'erro'); }
});

async function removerProduto(id) {
  try { await api('DELETE', `/produtos/${id}`); mostrarMensagem('Produto removido.'); carregarProdutos(); }
  catch (e) { mostrarMensagem(e.message, 'erro'); }
}

// --- PEDIDOS (módulo com desafios - pode falhar até você corrigir os bugs) ---
async function carregarPedidos() {
  try {
    const dados = await api('GET', '/pedidos');
    $('#tbody-pedidos').innerHTML = dados.data.map(p => `
      <tr>
        <td>${p.id}</td><td>${p.cliente_nome}</td><td>${p.produto?.nome ?? '-'}</td>
        <td>${p.quantidade ?? '<em>null?!</em>'}</td>
        <td>${p.total !== null ? 'R$ ' + Number(p.total).toFixed(2) : '<em>null?!</em>'}</td>
        <td><span class="tag ${p.status}">${p.status}</span></td>
        <td class="acoes-linha">
          <button class="acao secundario" onclick="pagarPedido(${p.id})">Marcar pago</button>
          <button class="acao perigo" onclick="removerPedido(${p.id})">Remover</button>
        </td>
      </tr>`).join('') || '<tr><td colspan="7">Nenhum pedido ainda.</td></tr>';
  } catch (e) { mostrarMensagem('Erro ao carregar pedidos: ' + e.message, 'erro'); }
}

$('#form-pedido').addEventListener('submit', async (e) => {
  e.preventDefault();
  const dados = Object.fromEntries(new FormData(e.target));
  try {
    await api('POST', '/pedidos', dados);
    mostrarMensagem('Pedido criado!');
    e.target.reset();
    carregarPedidos();
  } catch (err) { mostrarMensagem(err.message, 'erro'); }
});

async function pagarPedido(id) {
  try { await api('PATCH', `/pedidos/${id}/pagar`); mostrarMensagem('Pedido atualizado.'); carregarPedidos(); }
  catch (e) { mostrarMensagem(e.message, 'erro'); }
}
async function removerPedido(id) {
  try { await api('DELETE', `/pedidos/${id}`); mostrarMensagem('Pedido removido.'); carregarPedidos(); }
  catch (e) { mostrarMensagem(e.message, 'erro'); }
}

// --- Boot ---
carregarCategorias();
carregarProdutos();
carregarPedidos();
</script>
</body>
</html>
