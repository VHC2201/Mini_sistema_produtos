<?php
require_once __DIR__ . '/../controllers/AuthController.php';
verificarLogin();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Produto.php';
$produto = new Produto();

$metodo = $_SERVER['REQUEST_METHOD'];
$acao   = $_GET['acao'] ?? ''; 

switch ($metodo) {
    case 'GET':
        if ($acao === 'listar') {
            echo json_encode(['sucesso' => true, 'dados' => $produto->buscarComFornecedor()]);
        } elseif ($acao === 'buscar' && isset($_GET['id'])) {
            $dados = $produto->buscarPorId((int)$_GET['id']);
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
            $ok = $produto->criar(
                htmlspecialchars(trim($dados['nome'] ?? '')),
                htmlspecialchars(trim($dados['descricao'] ?? '')),
                (float)($dados['preco'] ?? 0),
                (int)($dados['estoque'] ?? 0),
                (int)($dados['fornecedor_id'] ?? 0)
            );
            echo json_encode(['sucesso' => $ok]);
        } elseif ($acao === 'atualizar' && isset($dados['id'])) {
            $ok = $produto->atualizar(
                (int)$dados['id'],
                htmlspecialchars(trim($dados['nome'] ?? '')),
                htmlspecialchars(trim($dados['descricao'] ?? '')),
                (float)($dados['preco'] ?? 0),
                (int)($dados['estoque'] ?? 0),
                (int)($dados['fornecedor_id'] ?? 0)
            );
            echo json_encode(['sucesso' => $ok]);
        } elseif ($acao === 'deletar' && isset($dados['id'])) {
            $ok = $produto->deletar((int)$dados['id']);
            echo json_encode(['sucesso' => $ok]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
}