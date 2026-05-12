<?php
require_once __DIR__ . '/Model.php';

class Usuario extends Model {
    protected string $tabela = 'usuarios';

    public function cadastrar(string $nome, string $email, string $senha): bool {
        $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            return false;
        }

        $senhaHash = hash('sha256', $senha); // SHA-256

        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)
        ");
        return $stmt->execute([
            ':nome'       => $nome,
            ':email'      => $email,
            ':senha_hash' => $senhaHash,
        ]);
    }

    public function autenticar(string $email, string $senha): array|false {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            return false;
        }

        $senhaHash = hash('sha256', $senha);

        if (!hash_equals($usuario['senha_hash'], $senhaHash)) {
            return false;
        }

        return $usuario;
    }
}