<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('ROOT_PATH', realpath(__DIR__ . '/../..'));

require_once ROOT_PATH . '/controllers/AuthController.php';
verificarLogin();
require_once ROOT_PATH . '/models/Produto.php';
require_once ROOT_PATH . '/models/Cesta.php';

$produtos   = (new Produto())->buscarComFornecedor();
$cestaAtiva = (new Cesta())->buscarCestaAtiva((int)$_SESSION['usuario_id']);

$tituloPagina = 'Vitrine — Mini Sistema';
include ROOT_PATH . '/views/partials/header.php';
include ROOT_PATH . '/views/partials/navbar.php';
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="h4 fw-semibold mb-0">Vitrine de produtos</h2>
      <p class="text-muted small mb-0">Selecione produtos e adicione à sua cesta</p>
    </div>
    <button class="btn btn-primary" id="btn-adicionar-carrinho" disabled onclick="adicionarAoCarrinho()">
      🛒 Adicionar ao carrinho
    </button>
  </div>

  <?php if (!$cestaAtiva): ?>
    <div class="alert alert-warning">
      Você não tem cesta ativa. <a href="/index.php?pagina=cestas" class="alert-link">Crie uma cesta</a> primeiro.
    </div>
  <?php else: ?>
    <div class="alert alert-info small mb-3">
      Cesta ativa: <strong><?= htmlspecialchars($cestaAtiva['nome']) ?></strong>
      — <span id="contador-selecionados">0 produto(s) selecionado(s)</span>
    </div>
    <input type="hidden" id="cestaAtivaId" value="<?= $cestaAtiva['id'] ?>">
  <?php endif; ?>

  <?php if (empty($produtos)): ?>
    <div class="alert alert-secondary">
      Nenhum produto cadastrado. <a href="/index.php?pagina=produtos">Cadastre produtos</a> primeiro.
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($produtos as $p): ?>
        <div class="col-md-4 col-sm-6">
          <div class="card produto-card h-100" id="card-<?= $p['id'] ?>">
            <div class="img-placeholder">📦</div>
            <div class="card-body">
              <h6 class="card-title fw-semibold"><?= htmlspecialchars($p['nome']) ?></h6>
              <p class="text-muted small mb-1"><?= htmlspecialchars($p['fornecedor_nome'] ?? '—') ?></p>
              <p class="fs-5 fw-semibold text-primary mb-0">R$ <?= number_format((float)$p['preco'], 2, ',', '.') ?></p>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <div class="form-check mb-0">
                <input class="form-check-input chk-produto" type="checkbox"
                       id="chk-<?= $p['id'] ?>" value="<?= $p['id'] ?>" onchange="atualizarBotao()">
                <label class="form-check-label small" for="chk-<?= $p['id'] ?>">Selecionar</label>
              </div>
              <span class="badge bg-primary d-none" id="badge-<?= $p['id'] ?>">✓</span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="d-flex justify-content-end mt-4">
      <button class="btn btn-primary btn-lg" id="btn-adicionar-carrinho2" disabled onclick="adicionarAoCarrinho()">
        🛒 Adicionar ao carrinho
      </button>
    </div>
  <?php endif; ?>
</div>

<?php include ROOT_PATH . '/views/partials/footer.php'; ?>
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const CESTA_ID   = document.getElementById('cestaAtivaId')?.value;

function atualizarBotao() {
  const total = document.querySelectorAll('.chk-produto:checked').length;
  document.querySelectorAll('#btn-adicionar-carrinho, #btn-adicionar-carrinho2').forEach(b => b.disabled = total === 0);
  const contador = document.getElementById('contador-selecionados');
  if (contador) contador.textContent = `${total} produto(s) selecionado(s)`;
  document.querySelectorAll('.chk-produto').forEach(chk => {
    document.getElementById(`card-${chk.value}`).classList.toggle('selecionado', chk.checked);
    document.getElementById(`badge-${chk.value}`).classList.toggle('d-none', !chk.checked);
  });
}

async function adicionarAoCarrinho() {
  const selecionados = [...document.querySelectorAll('.chk-produto:checked')].map(c => parseInt(c.value));
  if (selecionados.length === 0) { alert('Selecione pelo menos um produto.'); return; }
  if (!CESTA_ID) { alert('Crie uma cesta antes de adicionar produtos.'); return; }
  const res  = await fetch('/api/cesta.php?acao=adicionarItens', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ cesta_id: CESTA_ID, produto_ids: selecionados, csrf_token: CSRF_TOKEN }),
  });
  const json = await res.json();
  if (json.sucesso) {
    alert(`${selecionados.length} produto(s) adicionado(s) à cesta!`);
    window.location.href = '/index.php?pagina=carrinho';
  } else {
    alert(json.mensagem ?? 'Erro ao adicionar.');
  }
}
</script>