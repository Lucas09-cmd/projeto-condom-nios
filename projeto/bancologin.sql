CREATE DATABASE IF NOT EXISTS bancologin
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE bancologin;

CREATE TABLE IF NOT EXISTS usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(255) NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(255) NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS solicitacao (
  ALTER TABLE solicitacao
ADD COLUMN valor DECIMAL(10,2) NULL AFTER descricao;
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    descricao VARCHAR(500) NOT NULL,
    data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    status ENUM('pendente', 'aprovado', 'rejeitado')
    NOT NULL DEFAULT 'pendente',

    CONSTRAINT fk_solicitacao_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id)
    ON DELETE CASCADE
);

INSERT INTO admin (login, nome, senha)
SELECT
'admin@admin.com',
'Administrador',
'$2y$10$vikazXe.mFtyQ60cTAAdd.aGuvfjbOVDwraz0doau5fxdAHNxYspq'
WHERE NOT EXISTS (
    SELECT 1
    FROM admin
    WHERE login = 'admin@admin.com'
);