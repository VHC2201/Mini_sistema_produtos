<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('ROOT_PATH', realpath(__DIR__ . '/../..'));

require_once ROOT_PATH . '/controllers/AuthController.php';
verificarLogin();
require_once ROOT_PATH . '/models/Fornecedor.php';

$fornecedores = (new Fornecedor())->buscarTodos();
$tituloPagina = 'Produtos — Mini Sistema';
include ROOT_PATH . '/views/partials/header.php';
include ROOT_PATH . '/views/partials/navbar.php';
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="h4 fw-semibold mb-0">Gerenciar produtos <span class="badge-ajax ms-2">⚡ AJAX</span></h2>
      <p class="text-muted small mb-0">Adição e edição sem recarregar a página</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProduto" onclick="abrirModalNovo()">
      + Novo produto
    </button>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="tabela-produtos">
        <thead class="table-light">
          <tr><th>ID</th><th>Nome</th><th>Fornecedor</th><th>Preço</th><th>Estoque</th><th>Ações</th></tr>
        </thead>
        <tbody>
          <tr><td colspan="6" class="text-center text-muted py-4">Carregando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="modalProduto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitulo">Novo produto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="produtoId">
        <div class="mb-3">
          <label class="form-label fw-medium">Nome do produto</label>
          <input type="text" class="form-control" id="produtoNome" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-medium">Descrição</label>
          <input type="text" class="form-control" id="produtoDescricao" placeholder="Opcional">
        </div>
        <div class="row g-2 mb-3">
          <div class="col">
            <label class="form-label fw-medium">Preço (R$)</label>
            <input type="number" step="0.01" min="0" class="form-control" id="produtoPreco">
          </div>
          <div class="col">
            <label class="form-label fw-medium">Estoque</label>
            <input type="number" min="0" class="form-control" id="produtoEstoque" value="0">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-medium">Fornecedor</label>
          <select class="form-select" id="produtoFornecedor">
            <option value="">Selecione...</option>
            <?php foreach ($fornecedores as $f): ?>
              <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div id="modalErro" class="alert alert-danger d-none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="salvarProduto()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<?php include ROOT_PATH . '/views/partials/footer.php'; ?>
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

async function carregarProdutos() {
  const res  = await fetch('/api/produto.php?acao=listar');
  const json = await res.json();
  const tbody = document.querySelector('#tabela-produtos tbody');
  if (!json.sucesso || json.dados.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Nenhum produto cadastrado.</td></tr>';
    return;
  }
  tbody.innerHTML = json.dados.map(p => `
    <tr>
      <td>${p.id}</td><td>${p.nome}</td>
      <td><span class="badge bg-primary-subtle text-primary-emphasis">${p.fornecedor_nome ?? '—'}</span></td>
      <td>R$ ${parseFloat(p.preco).toFixed(2)}</td><td>${p.estoque}</td>
      <td>
        <button class="btn btn-sm btn-outline-warning me-1" onclick='abrirModalEditar(${JSON.stringify(p)})'>Editar</button>
        <button class="btn btn-sm btn-outline-danger" onclick="deletarProduto(${p.id})">Excluir</button>
      </td>
    </tr>
  `).join('');
}

function abrirModalNovo() {
  document.getElementById('modalTitulo').textContent = 'Novo produto';
  ['produtoId','produtoNome','produtoDescricao','produtoPreco'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('produtoEstoque').value = '0';
  document.getElementById('produtoFornecedor').value = '';
  document.getElementById('modalErro').classList.add('d-none');
}

function abrirModalEditar(p) {
  document.getElementById('modalTitulo').textContent = 'Editar produto';
  document.getElementById('produtoId').value          = p.id;
  document.getElementById('produtoNome').value        = p.nome;
  document.getElementById('produtoDescricao').value   = p.descricao ?? '';
  document.getElementById('produtoPreco').value       = p.preco;
  document.getElementById('produtoEstoque').value     = p.estoque;
  document.getElementById('produtoFornecedor').value  = p.fornecedor_id;
  document.getElementById('modalErro').classList.add('d-none');
  new bootstrap.Modal(document.getElementById('modalProduto')).show();
}

async function salvarProduto() {
  const id = document.getElementById('produtoId').value;
  const nome = document.getElementById('produtoNome').value.trim();
  const descricao = document.getElementById('produtoDescricao').value.trim();
  const preco = document.getElementById('produtoPreco').value;
  const estoque = document.getElementById('produtoEstoque').value;
  const fornecedorId = document.getElementById('produtoFornecedor').value;
  if (!nome || !preco || !fornecedorId) {
    const err = document.getElementById('modalErro');
    err.textContent = 'Preencha nome, preço e fornecedor.';
    err.classList.remove('d-none');
    return;
  }
  const acao = id ? 'atualizar' : 'criar';
  const res  = await fetch(`/api/produto.php?acao=${acao}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, nome, descricao, preco, estoque, fornecedor_id: fornecedorId, csrf_token: CSRF_TOKEN }),
  });
  const json = await res.json();
  if (json.sucesso) {
    bootstrap.Modal.getInstance(document.getElementById('modalProduto')).hide();
    carregarProdutos();
  } else {
    const err = document.getElementById('modalErro');
    err.textContent = json.mensagem ?? 'Erro ao salvar.';
    err.classList.remove('d-none');
  }
}

async function deletarProduto(id) {
  if (!confirm('Excluir este produto?')) return;
  const res  = await fetch('/api/produto.php?acao=deletar', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, csrf_token: CSRF_TOKEN }),
  });
  const json = await res.json();
  if (json.sucesso) carregarProdutos();
  else alert(json.mensagem ?? 'Erro ao excluir.');
}

document.addEventListener('DOMContentLoaded', carregarProdutos);
</script>