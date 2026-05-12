<?php
require_once __DIR__ . '/../config/database.php';

abstract class Model {
    protected PDO $db;
    protected string $tabela;  
    public function __construct() {
        $this->db = getConexao();
    }

    public function buscarTodos(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->tabela} ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabela} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function deletar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->tabela} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}