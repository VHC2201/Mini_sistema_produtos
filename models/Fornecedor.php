<?php
require_once __DIR__ . '/Model.php';

class Fornecedor extends Model {
    protected string $tabela = 'fornecedores';

    public function criar(string $nome, string $cnpj = '', string $telefone = '', string $email = ''): bool {
        $stmt = $this->db->prepare("
            INSERT INTO fornecedores (nome, cnpj, telefone, email)
            VALUES (:nome, :cnpj, :telefone, :email)
        ");
        return $stmt->execute([
            ':nome'     => $nome,
            ':cnpj'     => $cnpj ?: null,
            ':telefone' => $telefone ?: null,
            ':email'    => $email ?: null,
        ]);
    }

    public function atualizar(int $id, string $nome, string $cnpj = '', string $telefone = '', string $email = ''): bool {
        $stmt = $this->db->prepare("
            UPDATE fornecedores
            SET nome = :nome, cnpj = :cnpj, telefone = :telefone, email = :email
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'       => $id,
            ':nome'     => $nome,
            ':cnpj'     => $cnpj ?: null,
            ':telefone' => $telefone ?: null,
            ':email'    => $email ?: null,
        ]);
    }
}