<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
verificarLogin();
$tituloPagina = 'Cestas — Mini Sistema';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="h4 fw-semibold mb-0">Gerenciar cestas <span class="badge-ajax ms-2">⚡ AJAX</span></h2>
      <p class="text-muted small mb-0">Suas cestas de compras</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCesta" onclick="abrirModalNovo()">
      + Nova cesta
    </button>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="tabela-cestas">
        <thead class="table-light">
          <tr><th>ID</th><th>Nome</th><th>Criada em</th><th>Ações</th></tr>
        </thead>
        <tbody>
          <tr><td colspan="4" class="text-center text-muted py-4">Carregando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Cesta -->
<div class="modal fade" id="modalCesta" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitulo">Nova cesta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="cestaId">
        <div class="mb-3">
          <label class="form-label fw-medium">Nome da cesta</label>
          <input type="text" class="form-control" id="cestaNome" placeholder="Ex: Cesta principal">
        </div>
        <div id="modalErro" class="alert alert-danger d-none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="salvarCesta()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

async function carregarCestas() {
  const res  = await fetch('/api/cesta.php?acao=listar');
  const json = await res.json();
  const tbody = document.querySelector('#tabela-cestas tbody');

  if (!json.sucesso || json.dados.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Nenhuma cesta cadastrada.</td></tr>';
    return;
  }

  tbody.innerHTML = json.dados.map(c => `
    <tr>
      <td>${c.id}</td>
      <td>${c.nome}</td>
      <td>${c.criado_em}</td>
      <td>
        <button class="btn btn-sm btn-outline-warning me-1" onclick='abrirModalEditar(${JSON.stringify(c)})'>Editar</button>
        <button class="btn btn-sm btn-outline-danger" onclick="deletarCesta(${c.id})">Excluir</button>
      </td>
    </tr>
  `).join('');
}

function abrirModalNovo() {
  document.getElementById('modalTitulo').textContent = 'Nova cesta';
  document.getElementById('cestaId').value  = '';
  document.getElementById('cestaNome').value = '';
  document.getElementById('modalErro').classList.add('d-none');
}

function abrirModalEditar(c) {
  document.getElementById('modalTitulo').textContent = 'Editar cesta';
  document.getElementById('cestaId').value   = c.id;
  document.getElementById('cestaNome').value = c.nome;
  document.getElementById('modalErro').classList.add('d-none');
  new bootstrap.Modal(document.getElementById('modalCesta')).show();
}

async function salvarCesta() {
  const id   = document.getElementById('cestaId').value;
  const nome = document.getElementById('cestaNome').value.trim();

  if (!nome) {
    const err = document.getElementById('modalErro');
    err.textContent = 'O nome é obrigatório.';
    err.classList.remove('d-none');
    return;
  }

  const acao = id ? 'atualizar' : 'criar';
  const res  = await fetch(`/api/cesta.php?acao=${acao}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, nome, csrf_token: CSRF_TOKEN }),
  });
  const json = await res.json();

  if (json.sucesso) {
    bootstrap.Modal.getInstance(document.getElementById('modalCesta')).hide();
    carregarCestas();
  } else {
    const err = document.getElementById('modalErro');
    err.textContent = json.mensagem ?? 'Erro ao salvar.';
    err.classList.remove('d-none');
  }
}

async function deletarCesta(id) {
  if (!confirm('Excluir esta cesta e todos os seus itens?')) return;
  const res  = await fetch('/api/cesta.php?acao=deletar', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, csrf_token: CSRF_TOKEN }),
  });
  const json = await res.json();
  if (json.sucesso) carregarCestas();
  else alert('Erro ao excluir.');
}

document.addEventListener('DOMContentLoaded', carregarCestas);
</script>