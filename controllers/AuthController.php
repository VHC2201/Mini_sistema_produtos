<?php
require_once __DIR__ . '/../models/Usuario.php';

function gerarCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarCsrfToken(string $token): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function verificarLogin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['usuario_id'])) {
        header('Location: /index.php?pagina=login');
        exit;
    }
}

if (($_GET['acao'] ?? '') === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Token CSRF inválido.');
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    $usuario = (new Usuario())->autenticar($email, $senha);

    if ($usuario) {
        session_regenerate_id(true);
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header('Location: /index.php?pagina=dashboard');
        exit;
    }

    $_SESSION['erro_login'] = 'E-mail ou senha incorretos.';
    header('Location: /index.php?pagina=login');
    exit;
}

if (($_GET['acao'] ?? '') === 'cadastrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Token CSRF inválido.');
    }

    $nome           = htmlspecialchars(trim($_POST['nome'] ?? ''));
    $email          = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha          = $_POST['senha'] ?? '';
    $senhaConfirm   = $_POST['senha_confirm'] ?? '';

    if ($senha !== $senhaConfirm) {
        $_SESSION['erro_cadastro'] = 'As senhas não coincidem.';
        header('Location: /index.php?pagina=cadastro');
        exit;
    }

    if (strlen($senha) < 6) {
        $_SESSION['erro_cadastro'] = 'A senha deve ter ao menos 6 caracteres.';
        header('Location: /index.php?pagina=cadastro');
        exit;
    }

    $ok = (new Usuario())->cadastrar($nome, $email, $senha);

    if ($ok) {
        $_SESSION['sucesso_cadastro'] = 'Conta criada com sucesso! Faça login.';
        header('Location: /index.php?pagina=login');
    } else {
        $_SESSION['erro_cadastro'] = 'E-mail já cadastrado.';
        header('Location: /index.php?pagina=cadastro');
    }
    exit;
}

if (($_GET['pagina'] ?? '') === 'logout') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    session_destroy();
    header('Location: /index.php?pagina=login');
    exit;
}