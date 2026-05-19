-- =============================================================
--  Mini Sistema de Gestão de Produtos
--  Schema completo — criação do banco e de todas as tabelas
--  Compatível com MySQL 8.0+
-- =============================================================

CREATE DATABASE IF NOT EXISTS `mini_sistema`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `mini_sistema`;

-- -------------------------------------------------------------
-- Tabela: usuarios
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nome`       VARCHAR(100)    NOT NULL,
    `email`      VARCHAR(150)    NOT NULL,
    `senha_hash` VARCHAR(64)     NOT NULL COMMENT 'SHA-256 em hexadecimal (64 chars)',
    `criado_em`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuarios_email` (`email`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Tabela: fornecedores
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fornecedores` (
    `id`        INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nome`      VARCHAR(100)    NOT NULL,
    `cnpj`      VARCHAR(18)         NULL DEFAULT NULL,
    `telefone`  VARCHAR(20)         NULL DEFAULT NULL,
    `email`     VARCHAR(150)        NULL DEFAULT NULL,
    `criado_em` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Tabela: produtos
-- (depende de fornecedores — criada depois)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `fornecedor_id` INT UNSIGNED    NOT NULL,
    `nome`          VARCHAR(150)    NOT NULL,
    `descricao`     TEXT                NULL DEFAULT NULL,
    `preco`         DECIMAL(10,2)   NOT NULL,
    `estoque`       INT             NOT NULL DEFAULT 0,
    `criado_em`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_produtos_fornecedor`
        FOREIGN KEY (`fornecedor_id`)
        REFERENCES `fornecedores` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Tabela: cestas
-- (depende de usuarios — criada depois)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cestas` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED    NOT NULL,
    `nome`       VARCHAR(100)    NOT NULL,
    `criado_em`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_cestas_usuario`
        FOREIGN KEY (`usuario_id`)
        REFERENCES `usuarios` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Tabela: cesta_itens
-- (depende de cestas e produtos — criada por último)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cesta_itens` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `cesta_id`   INT UNSIGNED    NOT NULL,
    `produto_id` INT UNSIGNED    NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_cesta_produto` (`cesta_id`, `produto_id`),
    CONSTRAINT `fk_cesta_itens_cesta`
        FOREIGN KEY (`cesta_id`)
        REFERENCES `cestas` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_cesta_itens_produto`
        FOREIGN KEY (`produto_id`)
        REFERENCES `produtos` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
