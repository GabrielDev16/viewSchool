-- Database: GestCTT - Sistema de Gestão de Equipamentos
-- Created: 2025

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Database creation
CREATE DATABASE IF NOT EXISTS `ctt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ctt`;

-- Table: ala (Alas/Salas)
DROP TABLE IF EXISTS `ala`;
CREATE TABLE `ala` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for ala
INSERT INTO `ala` (`nome`, `descricao`, `status`) VALUES
('Sala 101', 'Sala de aula do primeiro andar', 'ativo'),
('Sala 102', 'Sala de aula do primeiro andar', 'ativo'),
('Laboratório de Informática', 'Laboratório com 30 computadores', 'ativo'),
('Auditório', 'Auditório principal', 'ativo'),
('Biblioteca', 'Biblioteca central', 'ativo');

-- Table: equipamentos (Equipamentos)
DROP TABLE IF EXISTS `equipamentos`;
CREATE TABLE `equipamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `fk_ala` int(11) NOT NULL,
  `status` enum('ativo','inativo','problema') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ala` (`fk_ala`),
  CONSTRAINT `equipamentos_ibfk_1` FOREIGN KEY (`fk_ala`) REFERENCES `ala` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for equipamentos
INSERT INTO `equipamentos` (`nome`, `tipo`, `descricao`, `fk_ala`, `status`) VALUES
('Ar Condicionado 12000 BTUs', 'Ar Condicionado', 'Ar condicionado Split 12000 BTUs', 1, 'ativo'),
('Projetor Epson', 'Projetor', 'Projetor multimídia Epson', 1, 'ativo'),
('Ar Condicionado 18000 BTUs', 'Ar Condicionado', 'Ar condicionado Split 18000 BTUs', 2, 'ativo'),
('Lâmpada LED 1', 'Lâmpada', 'Lâmpada LED 20W', 1, 'ativo'),
('Lâmpada LED 2', 'Lâmpada', 'Lâmpada LED 20W', 1, 'ativo'),
('Tomada Parede 1', 'Tomada', 'Tomada padrão 110V', 1, 'ativo'),
('Interruptor Principal', 'Interruptor', 'Interruptor de luz principal', 1, 'ativo');

-- Table: usuarios (Usuários do Sistema)
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipo` enum('admin','funcionario') DEFAULT 'funcionario',
  `status` enum('ativo','inativo','pendente','rejeitado') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for usuarios (password: admin123)
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `telefone`, `tipo`, `status`) VALUES
('Administrador', 'admin@gestctt.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 99999-9999', 'admin', 'ativo'),
('Funcionário Teste', 'funcionario@gestctt.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 98888-8888', 'funcionario', 'ativo');

-- Table: prestadores (Prestadores de Serviço)
DROP TABLE IF EXISTS `prestadores`;
CREATE TABLE `prestadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for prestadores
INSERT INTO `prestadores` (`nome`, `empresa`, `telefone`, `email`, `especialidade`, `status`) VALUES
('João Silva', 'Climatização Total', '(11) 3333-4444', 'joao@climatizacao.com', 'Ar Condicionado', 'ativo'),
('Maria Santos', 'Elétrica Express', '(11) 5555-6666', 'maria@eletrica.com', 'Elétrica', 'ativo'),
('Pedro Costa', 'TI Solutions', '(11) 7777-8888', 'pedro@tisolutions.com', 'Informática', 'ativo');

-- Table: manutencoes (Manutenções de Equipamentos)
DROP TABLE IF EXISTS `manutencoes`;
CREATE TABLE `manutencoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_equipamento` int(11) NOT NULL,
  `fk_prestador` int(11) DEFAULT NULL,
  `fk_usuario` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `tipo` enum('preventiva','corretiva','emergencial') DEFAULT 'corretiva',
  `status` enum('pendente','em_andamento','concluida','cancelada') DEFAULT 'pendente',
  `data_abertura` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_conclusao` timestamp NULL DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_equipamento` (`fk_equipamento`),
  KEY `fk_prestador` (`fk_prestador`),
  KEY `fk_usuario` (`fk_usuario`),
  CONSTRAINT `manutencoes_ibfk_1` FOREIGN KEY (`fk_equipamento`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `manutencoes_ibfk_2` FOREIGN KEY (`fk_prestador`) REFERENCES `prestadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `manutencoes_ibfk_3` FOREIGN KEY (`fk_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create uploads directory structure
-- Note: This should be created manually or via PHP
-- mkdir -p uploads/equipamentos/
-- chmod 755 uploads/
