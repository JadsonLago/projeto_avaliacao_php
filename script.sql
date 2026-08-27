CREATE DATABASE IF NOT EXISTS jm_informatica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jm_informatica;

CREATE TABLE user (
    id_user BIGINT(20) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    email VARCHAR(100),
    password VARCHAR(65),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    update_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1
);

CREATE TABLE service (
    id_service BIGINT(20) AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(45),
    price DECIMAL(11,3),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    update_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    finished_at DATETIME NULL,
    commission_user DECIMAL(11,3) NULL,
    user_id_user BIGINT(20),
    CONSTRAINT fk_user_service FOREIGN KEY (user_id_user) REFERENCES user(id_user) ON DELETE CASCADE
);

-- A senha é apenas para desenvolvimentos e testes, não utilize em produção.
INSERT INTO user (name, email, password, ativo) 
VALUES ('Gestor Teste', 'admin@jminformatica.com', '$2y$10$EXEMPLO_DE_HASH_GERADO_PELO_PHP_AQUI...', 1);