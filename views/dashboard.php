<?php
require_once __DIR__ . '/../controllers/AuthController.php';
verificarLogin();

require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Fornecedor.php';
require_once __DIR__ . '/../models/Cesta.php';

$totalProdutos    = count((new Produto())->buscarTodos());
$totalFornecedores = count((new Fornecedor())->buscarTodos());
$cestaAtiva       = (new Cesta())->buscarCestaAtiva((int)$_SESSION['usuario_id']);
$totalCarrinho    = $cestaAtiva ? count((new Cesta())->buscarItens((int)$cestaAtiva['id'])) : 0;

$tituloPagina = 'Dashboard — Mini Sistema';
include __DIR__ . '/partials/header.php';
include __DIR__ . '/partials/navbar.php';
?>

<div class="container py-4">
  <div class="mb-4">
    <h2 class="h4 fw-semibold">Dashboard</h2>
    <p class="text-muted small">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</p>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card p-3 h-100">
        <div class="text-muted small mb-1">📦 Total de produtos</div>
        <div class="fs-2 fw-semibold"><?= $totalProdutos ?></div>
        <a href="/index.php?pagina=produtos" class="stretched-link text-decoration-none small text-primary mt-1">Gerenciar →</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 h-100">
        <div class="text-muted small mb-1">🏭 Fornecedores</div>
        <div class="fs-2 fw-semibold"><?= $totalFornecedores ?></div>
        <a href="/index.php?pagina=fornecedores" class="stretched-link text-decoration-none small text-primary mt-1">Gerenciar →</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 h-100">
        <div class="text-muted small mb-1">🛒 Itens na cesta</div>
        <div class="fs-2 fw-semibold"><?= $totalCarrinho ?></div>
        <a href="/index.php?pagina=carrinho" class="stretched-link text-decoration-none small text-primary mt-1">Ver carrinho →</a>
      </div>
    </div>
  </div>

  <h5 class="fw-semibold mb-3">Acesso rápido</h5>
  <div class="row g-3">
    <div class="col-md-4">
      <a href="/index.php?pagina=produtos" class="card p-3 text-decoration-none text-dark d-flex flex-row align-items-center gap-3">
        <span style="font-size:1.8rem">📦</span>
        <div><div class="fw-medium">Gerenciar produtos</div><div class="text-muted small">CRUD + AJAX</div></div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="/index.php?pagina=fornecedores" class="card p-3 text-decoration-none text-dark d-flex flex-row align-items-center gap-3">
        <span style="font-size:1.8rem">🏭</span>
        <div><div class="fw-medium">Gerenciar fornecedores</div><div class="text-muted small">CRUD + AJAX</div></div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="/index.php?pagina=vitrine" class="card p-3 text-decoration-none text-dark d-flex flex-row align-items-center gap-3">
        <span style="font-size:1.8rem">🛍️</span>
        <div><div class="fw-medium">Vitrine de produtos</div><div class="text-muted small">Checkbox + carrinho</div></div>
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>