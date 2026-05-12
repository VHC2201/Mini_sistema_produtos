<?php
require_once __DIR__ . '/Model.php';

class Produto extends Model {
    protected string $tabela = 'produtos';

    public function criar(string $nome, string $descricao, float $preco, int $estoque, int $fornecedorId): bool {
        $stmt = $this->db->prepare("
            INSERT INTO produtos (nome, descricao, preco, estoque, fornecedor_id)
            VALUES (:nome, :descricao, :preco, :estoque, :fornecedor_id)
        ");
        return $stmt->execute([
            ':nome'          => $nome,
            ':descricao'     => $descricao,
            ':preco'         => $preco,
            ':estoque'       => $estoque,
            ':fornecedor_id' => $fornecedorId,
        ]);
    }

    public function atualizar(int $id, string $nome, string $descricao, float $preco, int $estoque, int $fornecedorId): bool {
        $stmt = $this->db->prepare("
            UPDATE produtos
            SET nome = :nome, descricao = :descricao, preco = :preco,
                estoque = :estoque, fornecedor_id = :fornecedor_id
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'            => $id,
            ':nome'          => $nome,
            ':descricao'     => $descricao,
            ':preco'         => $preco,
            ':estoque'       => $estoque,
            ':fornecedor_id' => $fornecedorId,
        ]);
    }

    public function buscarComFornecedor(): array {
        $stmt = $this->db->query("
            SELECT p.*, f.nome AS fornecedor_nome
            FROM produtos p
            LEFT JOIN fornecedores f ON f.id = p.fornecedor_id
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll();
    }
}