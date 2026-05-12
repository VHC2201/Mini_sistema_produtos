<?php
session_start();

$pagina = $_GET['pagina'] ?? 'login';

$paginasPublicas = ['login', 'cadastro'];

if (!in_array($pagina, $paginasPublicas) && empty($_SESSION['usuario_id'])) {
    header('Location: /index.php?pagina=login');
    exit;
}

$map = [
    'login'        => 'views/auth/login.php',
    'cadastro'     => 'views/auth/cadastro.php',
    'dashboard'    => 'views/dashboard.php',
    'produtos'     => 'views/produtos/index.php',
    'fornecedores' => 'views/fornecedores/index.php',
    'cestas'       => 'views/cestas/index.php',
    'vitrine'      => 'views/produtos/vitrine.php',
    'carrinho'     => 'views/carrinho/index.php',
];

$arquivo = $map[$pagina] ?? 'views/auth/login.php';

if (file_exists($arquivo)) {
    include $arquivo;
} else {
    http_response_code(404);
    echo '<h1>Página não encontrada</h1>';
}