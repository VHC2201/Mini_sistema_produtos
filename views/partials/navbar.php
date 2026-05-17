<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/index.php">
      📦 Mini Sistema
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/index.php?pagina=dashboard">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/index.php?pagina=produtos">Produtos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/index.php?pagina=fornecedores">Fornecedores</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/index.php?pagina=cestas">Cestas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/index.php?pagina=vitrine">Vitrine</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/index.php?pagina=carrinho">🛒 Carrinho</a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="nav-link text-light">
            Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></strong>
          </span>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-outline-light btn-sm px-3 ms-2" href="/index.php?pagina=logout">Sair</a>
        </li>
      </ul>
    </div>
  </div>
</nav>