<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: /index.php?pagina=dashboard');
    exit;
}

$erro    = $_SESSION['erro_cadastro'] ?? null;
$sucesso = $_SESSION['sucesso_cadastro'] ?? null;
unset($_SESSION['erro_cadastro'], $_SESSION['sucesso_cadastro']);
$tituloPagina = 'Cadastro — Mini Sistema';
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
  <div class="w-100" style="max-width: 420px; padding: 1rem;">

    <div class="text-center mb-4">
      <div class="bg-primary text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-2"
           style="width:50px;height:50px;font-size:1.5rem;">📦</div>
      <h1 class="h4 fw-semibold">Criar conta</h1>
      <p class="text-muted small">Preencha os dados abaixo</p>
    </div>

    <?php if ($erro): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
      <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <div class="card p-4">
      <form method="POST" action="/index.php?pagina=cadastro&acao=cadastrar" id="formCadastro">
        <input type="hidden" name="csrf_token" value="<?= gerarCsrfToken() ?>">

        <div class="mb-3">
          <label for="nome" class="form-label fw-medium">Nome completo</label>
          <input type="text" class="form-control" id="nome" name="nome"
                 placeholder="Seu nome" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label fw-medium">E-mail</label>
          <input type="email" class="form-control" id="email" name="email"
                 placeholder="seu@email.com" required>
        </div>

        <div class="mb-3">
          <label for="senha" class="form-label fw-medium">Senha</label>
          <input type="password" class="form-control" id="senha" name="senha"
                 placeholder="••••••••" required minlength="6">
          <div class="form-text">
            🔒 Armazenada com hash SHA-256
          </div>
        </div>

        <div class="mb-4">
          <label for="senha_confirm" class="form-label fw-medium">Confirmar senha</label>
          <input type="password" class="form-control" id="senha_confirm" name="senha_confirm"
                 placeholder="••••••••" required minlength="6">
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-medium">Criar conta</button>
      </form>

      <hr class="my-3">

      <p class="text-center text-muted small mb-0">
        Já tem conta?
        <a href="/index.php?pagina=login" class="text-primary fw-medium text-decoration-none">Entrar</a>
      </p>
    </div>

  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('formCadastro').addEventListener('submit', function(e) {
  const senha = document.getElementById('senha').value;
  const confirm = document.getElementById('senha_confirm').value;
  if (senha !== confirm) {
    e.preventDefault();
    alert('As senhas não coincidem!');
  }
});
</script>
</body>
</html>