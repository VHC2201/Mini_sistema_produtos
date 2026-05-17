<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('ROOT_PATH', realpath(__DIR__ . '/../..'));

require_once ROOT_PATH . '/controllers/AuthController.php';
verificarLogin();
require_once ROOT_PATH . '/models/Cesta.php';

$cestaModel = new Cesta();
$cestaAtiva = $cestaModel->buscarCestaAtiva((int)$_SESSION['usuario_id']);
$itens      = $cestaAtiva ? $cestaModel->buscarItens((int)$cestaAtiva['id']) : [];
$total      = array_sum(array_column($itens, 'preco'));

$acao = $_GET['acao'] ?? '';

if ($acao === 'esvaziar' && $cestaAtiva) {
    $cestaModel->esvaziarCesta((int)$cestaAtiva['id']);
    header('Location: /index.php?pagina=carrinho');
    exit;
}

if ($acao === 'finalizar' && $cestaAtiva && !empty($itens)) {
    $cestaModel->esvaziarCesta((int)$cestaAtiva['id']);
    $_SESSION['pedido_finalizado'] = true;
    header('Location: /index.php?pagina=carrinho');
    exit;
}

$pedidoFinalizado = $_SESSION['pedido_finalizado'] ?? false;
unset($_SESSION['pedido_finalizado']);

$tituloPagina = 'Carrinho — Mini Sistema';
include ROOT_PATH . '/views/partials/header.php';
include ROOT_PATH . '/views/partials/navbar.php';
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="h4 fw-semibold mb-0">🛒 Minha cesta</h2>
      <p class="text-muted small mb-0"><?= $cestaAtiva ? htmlspecialchars($cestaAtiva['nome']) : 'Nenhuma cesta ativa' ?></p>
    </div>
    <a href="/index.php?pagina=vitrine" class="btn btn-outline-primary">+ Adicionar mais produtos</a>
  </div>

  <?php if ($pedidoFinalizado): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      ✅ <strong>Pedido finalizado com sucesso!</strong> Sua cesta foi esvaziada.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (!$cestaAtiva): ?>
    <div class="alert alert-warning">
      Você não tem cesta ativa. <a href="/index.php?pagina=cestas" class="alert-link">Crie uma cesta</a>.
    </div>
  <?php elseif (empty($itens)): ?>
    <div class="alert alert-secondary">
      Sua cesta está vazia. <a href="/index.php?pagina=vitrine">Ir para a vitrine</a>.
    </div>
  <?php else: ?>
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Produto</th>
                  <th>Fornecedor</th>
                  <th>Preço unit.</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($itens as $i => $item): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($item['nome']) ?></td>
                    <td>
                      <span class="badge bg-primary-subtle text-primary-emphasis">
                        <?= htmlspecialchars($item['fornecedor_nome'] ?? '—') ?>
                      </span>
                    </td>
                    <td>R$ <?= number_format((float)$item['preco'], 2, ',', '.') ?></td>
                    <td>
                      <a href="/api/cesta.php?acao=removerItem&cesta_id=<?= $cestaAtiva['id'] ?>&produto_id=<?= $item['produto_id'] ?>&csrf_token=<?= gerarCsrfToken() ?>"
                         class="btn btn-sm btn-outline-danger"
                         onclick="return confirm('Remover este item?')">🗑</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot class="table-light">
                <tr>
                  <td colspan="3" class="fw-medium">
                    Total de itens: <strong><?= count($itens) ?> produto(s)</strong>
                  </td>
                  <td colspan="2" class="fw-medium">
                    Valor total: <strong class="text-primary">R$ <?= number_format($total, 2, ',', '.') ?></strong>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card p-4">
          <h5 class="fw-semibold mb-3">📋 Resumo do pedido</h5>
          <div class="d-flex justify-content-between mb-2 small">
            <span class="text-muted">Total de produtos</span>
            <strong><?= count($itens) ?></strong>
          </div>
          <?php foreach ($itens as $item): ?>
            <div class="d-flex justify-content-between mb-1 small">
              <span class="text-muted text-truncate me-2" style="max-width:150px">
                <?= htmlspecialchars($item['nome']) ?>
              </span>
              <span>R$ <?= number_format((float)$item['preco'], 2, ',', '.') ?></span>
            </div>
          <?php endforeach; ?>
          <hr>
          <div class="d-flex justify-content-between fw-semibold fs-6">
            <span>Valor total</span>
            <span class="text-primary">R$ <?= number_format($total, 2, ',', '.') ?></span>
          </div>

          <a href="/index.php?pagina=carrinho&acao=finalizar"
             class="btn btn-success w-100 mt-3 fw-medium"
             onclick="return confirm('Confirmar finalização do pedido?\n\n<?= count($itens) ?> produto(s) · R$ <?= number_format($total, 2, ',', '.') ?>')">
            ✅ Finalizar pedido
          </a>

          <a href="/index.php?pagina=carrinho&acao=esvaziar"
             class="btn btn-outline-danger w-100 mt-2 btn-sm"
             onclick="return confirm('Esvaziar toda a cesta sem finalizar?')">
            🗑 Esvaziar sem finalizar
          </a>

          <div class="text-muted text-center mt-2" style="font-size:0.75rem">
            Cesta: "<?= htmlspecialchars($cestaAtiva['nome']) ?>"
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include ROOT_PATH . '/views/partials/footer.php'; ?>