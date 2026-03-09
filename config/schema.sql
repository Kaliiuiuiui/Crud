CREATE DATABASE IF NOT EXISTS auth_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE auth_system;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'user') DEFAULT 'user',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Inserir usuário Admin padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha, nivel) 
VALUES ('Administrador', 'admin@email.com', '$2y$10$MTJNIq9lZuAPSA2QlhlSUeLYQ7KdWmnS8JI50Qy2rEEOhKY5ADyTS', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Inserir usuário comum de teste (senha: user123)
INSERT INTO usuarios (nome, email, senha, nivel) 
VALUES ('Usuário Teste', 'user@email.com', '$2y$10$OriCZiB463hmjga/ZvbBmeBzHlqas25jseDWo1.qcWvn.582/4erK', 'user')
ON DUPLICATE KEY UPDATE email=email;
