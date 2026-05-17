<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: /index.php?pagina=dashboard');
    exit;
}

$erro = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
$tituloPagina = 'Login — Mini Sistema';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $tituloPagina ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-light">
<div class="min-vh-100 d-flex align-items-center justify-content-center">
  <div class="w-100" style="max-width: 400px; padding: 1rem;">

    <div class="text-center mb-4">
      <div class="bg-primary text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-2"
           style="width:50px;height:50px;font-size:1.5rem;">📦</div>
      <h1 class="h4 fw-semibold">Mini Sistema de Produtos</h1>
      <p class="text-muted small">Faça login para continuar</p>
    </div>

    <?php if ($erro): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="card p-4">
      <form method="POST" action="/index.php?pagina=login&acao=login" id="formLogin">
        <input type="hidden" name="csrf_token" value="<?= gerarCsrfToken() ?>">

        <div class="mb-3">
          <label for="email" class="form-label fw-medium">E-mail</label>
          <input type="email" class="form-control" id="email" name="email"
                 placeholder="seu@email.com" required autocomplete="email">
        </div>

        <div class="mb-4">
          <label for="senha" class="form-label fw-medium">Senha</label>
          <input type="password" class="form-control" id="senha" name="senha"
                 placeholder="••••••••" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-medium">Entrar</button>
      </form>

      <hr class="my-3">

      <p class="text-center text-muted small mb-0">
        Não tem conta?
        <a href="/index.php?pagina=cadastro" class="text-primary fw-medium text-decoration-none">Cadastre-se</a>
      </p>
    </div>

  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>