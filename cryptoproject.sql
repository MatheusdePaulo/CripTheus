-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS cryptoproject;
USE cryptoproject;

-- Criação da tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL
);

-- Inserção dos dados dos usuários
-- Nota: As senhas foram geradas com password_hash('senha123', PASSWORD_DEFAULT)
INSERT INTO usuarios (id, username, email, password, is_admin, created_at) VALUES
(33, 'mariana', 'mariana@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2024-12-09 17:49:00'),
(34, 'marcos', 'marcosfda@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2024-12-09 17:50:00'),
(35, 'nikolas', 'nikolas@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2024-12-09 19:06:00'),
(38, 'matheus', 'matheusdepaulo21@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-05-19 14:27:00'),
(39, 'jackson', 'jackson@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-06-20 15:42:00'),
(40, 'cristian', 'cristian1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-06-20 16:07:00'),
(42, 'Julian', 'julian@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-06-20 17:00:00'),
(43, 'Jefessor', 'Jefessor@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-06-20 23:01:00'),
(44, 'admin', 'administrador@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-06-20 23:20:00'),
(46, 'Diemyn', 'Diemyn@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-06-25 18:55:00');

-- Atualiza o auto_increment para o próximo ID disponível
ALTER TABLE usuarios AUTO_INCREMENT = 47;