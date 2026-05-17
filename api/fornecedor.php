<?php
require_once __DIR__ . '/../controllers/AuthController.php';
verificarLogin();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Fornecedor.php';
$fornecedor = new Fornecedor();
$metodo     = $_SERVER['REQUEST_METHOD'];
$acao       = $_GET['acao'] ?? '';

switch ($metodo) {
    case 'GET':
        if ($acao === 'listar') {
            echo json_encode(['sucesso' => true, 'dados' => $fornecedor->buscarTodos()]);
        } elseif ($acao === 'buscar' && isset($_GET['id'])) {
            $dados = $fornecedor->buscarPorId((int)$_GET['id']);
            echo json_encode($dados ? ['sucesso' => true, 'dados' => $dados] : ['sucesso' => false]);
        }
        break;

    case 'POST':
        $dados = json_decode(file_get_contents('php://input'), true);

        if (!validarCsrfToken($dados['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Token inválido.']);
            break;
        }

        if ($acao === 'criar') {
            $ok = $fornecedor->criar(
                htmlspecialchars(trim($dados['nome'] ?? '')),
                htmlspecialchars(trim($dados['cnpj'] ?? '')),
                htmlspecialchars(trim($dados['telefone'] ?? '')),
                filter_var($dados['email'] ?? '', FILTER_SANITIZE_EMAIL)
            );
            echo json_encode(['sucesso' => $ok]);

        } elseif ($acao === 'atualizar' && isset($dados['id'])) {
            $ok = $fornecedor->atualizar(
                (int)$dados['id'],
                htmlspecialchars(trim($dados['nome'] ?? '')),
                htmlspecialchars(trim($dados['cnpj'] ?? '')),
                htmlspecialchars(trim($dados['telefone'] ?? '')),
                filter_var($dados['email'] ?? '', FILTER_SANITIZE_EMAIL)
            );
            echo json_encode(['sucesso' => $ok]);

        } elseif ($acao === 'deletar' && isset($dados['id'])) {
            try {
                $ok = $fornecedor->deletar((int)$dados['id']);
                echo json_encode(['sucesso' => $ok]);
            } catch (PDOException $e) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Fornecedor possui produtos vinculados.']);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
}