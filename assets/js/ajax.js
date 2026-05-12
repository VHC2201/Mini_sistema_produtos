function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? window.CSRF_TOKEN ?? '';
}

async function carregarProdutos() {
  const resposta = await fetch('/api/produto.php?acao=listar');
  const json = await resposta.json();

  if (!json.sucesso) {
    alert('Erro ao carregar produtos.');
    return;
  }

  const tbody = document.querySelector('#tabela-produtos tbody');
  tbody.innerHTML = '';

  json.dados.forEach(p => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${p.id}</td>
      <td>${p.nome}</td>
      <td>${p.fornecedor_nome ?? '—'}</td>
      <td>R$ ${parseFloat(p.preco).toFixed(2)}</td>
      <td>${p.estoque}</td>
      <td>
        <button class="btn btn-sm btn-warning" onclick="abrirEdicaoProduto(${p.id})">Editar</button>
        <button class="btn btn-sm btn-danger"  onclick="deletarProduto(${p.id})">Excluir</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

async function salvarProduto(dados) {
  const acao = dados.id ? 'atualizar' : 'criar';
  const resposta = await fetch(`/api/produto.php?acao=${acao}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ ...dados, csrf_token: getCsrfToken() }),
  });

  const json = await resposta.json();
  if (json.sucesso) { 
    carregarProdutos();
    fecharModal();
  } else {
    alert('Erro ao salvar produto.');
  }
}

async function deletarProduto(id) {
  if (!confirm('Confirma exclusão?')) return;
  const resposta = await fetch('/api/produto.php?acao=deletar', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, csrf_token: getCsrfToken() }),
  });
  const json = await resposta.json();
  if (json.sucesso) carregarProdutos();
}

document.addEventListener('DOMContentLoaded', carregarProdutos);