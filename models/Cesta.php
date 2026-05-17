<?php
require_once __DIR__ . '/Model.php';

class Cesta extends Model {
    protected string $tabela = 'cestas';

    public function criar(string $nome, int $usuarioId): bool {
        $stmt = $this->db->prepare("
            INSERT INTO cestas (nome, usuario_id) VALUES (:nome, :usuario_id)
        ");
        return $stmt->execute([':nome' => $nome, ':usuario_id' => $usuarioId]);
    }

    public function atualizar(int $id, string $nome, int $usuarioId): bool {
        $stmt = $this->db->prepare("
            UPDATE cestas SET nome = :nome WHERE id = :id AND usuario_id = :usuario_id
        ");
        return $stmt->execute([':id' => $id, ':nome' => $nome, ':usuario_id' => $usuarioId]);
    }

    public function buscarPorUsuario(int $usuarioId): array {
        $stmt = $this->db->prepare("SELECT * FROM cestas WHERE usuario_id = :uid ORDER BY id DESC");
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function buscarCestaAtiva(int $usuarioId): array|false {
        $stmt = $this->db->prepare("SELECT * FROM cestas WHERE usuario_id = :uid ORDER BY id DESC LIMIT 1");
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetch();
    }

    public function buscarItens(int $cestaId): array {
        $stmt = $this->db->prepare("
            SELECT ci.produto_id, p.nome, p.preco, f.nome AS fornecedor_nome
            FROM cesta_itens ci
            JOIN produtos p ON p.id = ci.produto_id
            LEFT JOIN fornecedores f ON f.id = p.fornecedor_id
            WHERE ci.cesta_id = :cesta_id
        ");
        $stmt->execute([':cesta_id' => $cestaId]);
        return $stmt->fetchAll();
    }

    public function adicionarItens(int $cestaId, array $produtoIds): bool {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO cesta_itens (cesta_id, produto_id) VALUES (:cesta_id, :produto_id)
        ");
        foreach ($produtoIds as $produtoId) {
            $stmt->execute([':cesta_id' => $cestaId, ':produto_id' => (int)$produtoId]);
        }
        return true;
    }

    public function removerItem(int $cestaId, int $produtoId): bool {
        $stmt = $this->db->prepare("DELETE FROM cesta_itens WHERE cesta_id = :cesta_id AND produto_id = :produto_id");
        return $stmt->execute([':cesta_id' => $cestaId, ':produto_id' => $produtoId]);
    }

    public function esvaziarCesta(int $cestaId): bool {
        $stmt = $this->db->prepare("DELETE FROM cesta_itens WHERE cesta_id = :cesta_id");
        return $stmt->execute([':cesta_id' => $cestaId]);
    }
}