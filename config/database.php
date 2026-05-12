<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mini_sistema');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getConexao(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `" . DB_NAME . "`");
            criarTabelas($pdo);
        } catch (PDOException $e) {
            error_log("Erro de conexão: " . $e->getMessage());
            die(json_encode(['erro' => 'Falha na conexão com o banco de dados.']));
        }
    }
    return $pdo;
}

function criarTabelas(PDO $pdo): void {
    $sql = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nome       VARCHAR(100) NOT NULL,
        email      VARCHAR(150) NOT NULL UNIQUE,
        senha_hash VARCHAR(64)  NOT NULL,
        criado_em  DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS fornecedores (
        id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nome      VARCHAR(100) NOT NULL,
        cnpj      VARCHAR(18),
        telefone  VARCHAR(20),
        email     VARCHAR(150),
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS produtos (
        id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        fornecedor_id INT UNSIGNED NOT NULL,
        nome          VARCHAR(150) NOT NULL,
        descricao     TEXT,
        preco         DECIMAL(10,2) NOT NULL,
        estoque       INT DEFAULT 0,
        criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS cestas (
        id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT UNSIGNED NOT NULL,
        nome       VARCHAR(100) NOT NULL,
        criado_em  DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS cesta_itens (
        id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cesta_id   INT UNSIGNED NOT NULL,
        produto_id INT UNSIGNED NOT NULL,
        UNIQUE KEY uq_cesta_produto (cesta_id, produto_id),
        FOREIGN KEY (cesta_id)   REFERENCES cestas(id)   ON DELETE CASCADE,
        FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    foreach (explode(';', $sql) as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
}