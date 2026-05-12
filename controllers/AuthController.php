<?php
require_once __DIR__ . '/../models/Usuario.php';

function verificarLogin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['usuario_id'])) {
        header('Location: /views/auth/login.php');
        exit;
    }
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['acao'] ?? '') === 'login') {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Token inválido.');
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    $usuario = (new Usuario())->autenticar($email, $senha);
    if ($usuario) {
        session_regenerate_id(true); 
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header('Location: /views/dashboard.php');
        exit;
    }

    $_SESSION['erro_login'] = 'E-mail ou senha incorretos.';
    header('Location: /views/auth/login.php');
    exit;
}