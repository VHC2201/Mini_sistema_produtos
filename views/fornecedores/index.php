<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
verificarLogin();
$tituloPagina = 'Fornecedores — Mini Sistema';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="h4 fw-semibold mb-0">
        Gerenciar fornecedores
        <span class="badge-ajax ms-2">⚡ AJAX</span>
      </h2>
      <p class="text-muted small mb-0">Adição e edição sem recarregar a página</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFornecedor" onclick="abrirModalNovo()">
      + Novo fornecedor
    </button>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="tabela-fornecedores">
        <thead class="table-light">
          <tr>
            <th>ID</th><th>Nome</th><th>CNPJ</th><th>Telefone</th><th>E-mail</th><th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="6" class="text-center text-muted py-4">Carregando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Fornecedor -->
<div class="modal fade" id="modalFornecedor" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitulo">Novo fornecedor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="fornecedorId">
        <div class="mb-3">
          <label class="form-label fw-medium">Nome</label>
          <input type="text" class="form-control" id="fornecedorNome" required>
        </div>
        <div class="row g-2 mb-3">
          <div class="col">
            <label class="form-label fw-medium">CNPJ</label>
            <input type="text" class="form-control" id="fornecedorCnpj" placeholder="00.000.000/0000-00">
          </div>
          <div class="col">
            <label class="form-label fw-medium">Telefone</label>
            <input type="text" class="form-control" id="fornecedorTelefone" placeholder="(00) 00000-0000">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-medium">E-mail</label>
          <input type="email" class="form-control" id="fornecedorEmail">
        </div>
        <div id="modalErro" class="alert alert-danger d-none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="salvarFornecedor()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

async function carregarFornecedores() {
  const res  = await fetch('/api/fornecedor.php?acao=listar');
  const json = await res.json();
  const tbody = document.querySelector('#tabela-fornecedores tbody');

  if (!json.sucesso || json.dados.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Nenhum fornecedor cadastrado.</td></tr>';
    return;
  }

  tbody.innerHTML = json.dados.map(f => `
    <tr>
      <td>${f.id}</td>
      <td>${f.nome}</td>
      <td>${f.cnpj ?? '—'}</td>
      <td>${f.telefone ?? '—'}</td>
      <td>${f.email ?? '—'}</td>
      <td>
        <button class="btn btn-sm btn-outline-warning me-1" onclick='abrirModalEditar(${JSON.stringify(f)})'>Editar</button>
        <button class="btn btn-sm btn-outline-danger" onclick="deletarFornecedor(${f.id})">Excluir</button>
      </td>
    </tr>
  `).join('');
}

function abrirModalNovo() {
  document.getElementById('modalTitulo').textContent = 'Novo fornecedor';
  ['fornecedorId','fornecedorNome','fornecedorCnpj','fornecedorTelefone','fornecedorEmail'].forEach(id => {
    document.getElementById(id).value = '';
  });
  document.getElementById('modalErro').classList.add('d-none');
}

function abrirModalEditar(f) {
  document.getElementById('modalTitulo').textContent = 'Editar fornecedor';
  document.getElementById('fornecedorId').value       = f.id;
  document.getElementById('fornecedorNome').value     = f.nome;
  document.getElementById('fornecedorCnpj').value     = f.cnpj ?? '';
  document.getElementById('fornecedorTelefone').value = f.telefone ?? '';
  document.getElementById('fornecedorEmail').value    = f.email ?? '';
  document.getElementById('modalErro').classList.add('d-none');
  new bootstrap.Modal(document.getElementById('modalFornecedor')).show();
}

async function salvarFornecedor() {
  const id       = document.getElementById('fornecedorId').value;
  const nome     = document.getElementById('fornecedorNome').value.trim();
  const cnpj     = document.getElementById('fornecedorCnpj').value.trim();
  const telefone = document.getElementById('fornecedorTelefone').value.trim();
  const email    = document.getElementById('fornecedorEmail').value.trim();

  if (!nome) {
    const err = document.getElementById('modalErro');
    err.textContent = 'O nome é obrigatório.';
    err.classList.remove('d-none');
    return;
  }

  const acao = id ? 'atualizar' : 'criar';
  const res  = await fetch(`/api/fornecedor.php?acao=${acao}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, nome, cnpj, telefone, email, csrf_token: CSRF_TOKEN }),
  });
  const json = await res.json();

  if (json.sucesso) {
    bootstrap.Modal.getInstance(document.getElementById('modalFornecedor')).hide();
    carregarFornecedores();
  } else {
    const err = document.getElementById('modalErro');
    err.textContent = json.mensagem ?? 'Erro ao salvar.';
    err.classList.remove('d-none');
  }
}

async function deletarFornecedor(id) {
  if (!confirm('Excluir este fornecedor?')) return;
  const res  = await fetch('/api/fornecedor.php?acao=deletar', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, csrf_token: CSRF_TOKEN }),
  });
  const json = await res.json();
  if (json.sucesso) carregarFornecedores();
  else alert(json.mensagem ?? 'Não é possível excluir: fornecedor possui produtos vinculados.');
}

document.addEventListener('DOMContentLoaded', carregarFornecedores);
</script>