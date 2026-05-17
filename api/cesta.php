<?php
require_once __DIR__ . '/../controllers/AuthController.php';
verificarLogin();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Cesta.php';
$cestaModel = new Cesta();
$metodo     = $_SERVER['REQUEST_METHOD'];
$acao       = $_GET['acao'] ?? '';

switch ($metodo) {
    case 'GET':
        if ($acao === 'listar') {
            echo json_encode(['sucesso' => true, 'dados' => $cestaModel->buscarPorUsuario((int)$_SESSION['usuario_id'])]);

        } elseif ($acao === 'removerItem') {
            // Remoção via GET com redirect (botão de remover do carrinho)
            if (!validarCsrfToken($_GET['csrf_token'] ?? '')) {
                header('Location: /index.php?pagina=carrinho');
                exit;
            }
            $cestaModel->removerItem((int)$_GET['cesta_id'], (int)$_GET['produto_id']);
            header('Location: /index.php?pagina=carrinho');
            exit;
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
            $ok = $cestaModel->criar(
                htmlspecialchars(trim($dados['nome'] ?? '')),
                (int)$_SESSION['usuario_id']
            );
            echo json_encode(['sucesso' => $ok]);

        } elseif ($acao === 'atualizar' && isset($dados['id'])) {
            $ok = $cestaModel->atualizar(
                (int)$dados['id'],
                htmlspecialchars(trim($dados['nome'] ?? '')),
                (int)$_SESSION['usuario_id']
            );
            echo json_encode(['sucesso' => $ok]);

        } elseif ($acao === 'deletar' && isset($dados['id'])) {
            $ok = $cestaModel->deletar((int)$dados['id']);
            echo json_encode(['sucesso' => $ok]);

        } elseif ($acao === 'adicionarItens') {
            $cestaId    = (int)($dados['cesta_id'] ?? 0);
            $produtoIds = $dados['produto_ids'] ?? [];

            if (empty($produtoIds) || !$cestaId) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
                break;
            }

            $ok = $cestaModel->adicionarItens($cestaId, $produtoIds);
            echo json_encode(['sucesso' => $ok]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
}