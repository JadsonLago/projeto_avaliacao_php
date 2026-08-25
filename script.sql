CREATE DATABASE IF NOT EXISTS jm_informatica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jm_informatica;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    valor_comissao DECIMAL(10, 2) DEFAULT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_finalizacao TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Inserindo um usuário de teste (A senha é '123456' gerada com password_hash do PHP) - Omitir senha em produção
INSERT INTO usuarios (nome, email, senha) 
VALUES ('Gestor Teste', 'admin@jminformatica.com', '$2y$10$w6DqV.K7P3H5/x/X2OaQ.O4O/hI2e5s4ZzM5G5v5T/m7s5m7s5m7s'); 