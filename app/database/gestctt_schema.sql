/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 10.4.32-MariaDB : Database - ctt
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ctt` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;

USE `ctt`;

/*Table structure for table `ala` */

DROP TABLE IF EXISTS `ala`;

CREATE TABLE `ala` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `ala` */

insert  into `ala`(`id`,`nome`,`descricao`,`status`,`created_at`,`updated_at`) values 
(40,'Sala 01','Sala de Aula','ativo','2025-11-13 14:09:44','2025-11-13 14:09:44');

/*Table structure for table `equipamentos` */

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `equipamentos` */

insert  into `equipamentos`(`id`,`nome`,`tipo`,`descricao`,`imagem`,`fk_ala`,`status`,`created_at`,`updated_at`) values 
(13,'Projetor Epson','Projetor','Projetor Epson ','uploads/equipamentos/6916112415dc1.webp',40,'problema','2025-11-13 14:11:00','2025-11-13 14:11:50'),
(14,'Projetor Epson','Projetor','Projetor Epson ','uploads/equipamentos/6916115daec51.webp',40,'ativo','2025-11-13 14:11:57','2025-11-13 14:11:57');

/*Table structure for table `manutencoes` */

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

/*Data for the table `manutencoes` */

/*Table structure for table `prestadores` */

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `prestadores` */

insert  into `prestadores`(`id`,`nome`,`empresa`,`telefone`,`email`,`especialidade`,`status`,`created_at`,`updated_at`) values 
(1,'João Silva','Climatização Total','(11) 3333-4444','joao@climatizacao.com','Ar Condicionado','inativo','2025-10-07 19:20:19','2025-11-13 12:20:09'),
(2,'Maria Santos','Elétrica Express','(11) 5555-6666','maria@eletrica.com','Elétrica','inativo','2025-10-07 19:20:19','2025-11-13 12:20:11'),
(3,'Pedro Costa','TI Solutions','(11) 7777-8888','pedro@tisolutions.com','Informática','inativo','2025-10-07 19:20:19','2025-11-13 12:20:13'),
(4,'Airan','AR Refrigerações','8932016244','gayep87325@dropeso.com','Ar Condicionado','inativo','2025-10-31 08:35:51','2025-11-13 12:20:07');

/*Table structure for table `problemas` */

DROP TABLE IF EXISTS `problemas`;

CREATE TABLE `problemas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_equipamento` int(11) NOT NULL,
  `fk_usuario` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('aberto','em_atendimento','resolvido','fechado') NOT NULL DEFAULT 'aberto',
  `data_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_equipamento` (`fk_equipamento`),
  KEY `fk_usuario` (`fk_usuario`),
  CONSTRAINT `problemas_ibfk_1` FOREIGN KEY (`fk_equipamento`) REFERENCES `equipamentos` (`id`),
  CONSTRAINT `problemas_ibfk_2` FOREIGN KEY (`fk_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `problemas` */

insert  into `problemas`(`id`,`fk_equipamento`,`fk_usuario`,`titulo`,`descricao`,`status`,`data_reporte`) values 
(3,13,12,'Projetor não liga','quando coloco na tomada ou aperto o interruptor o projetor não liga','aberto','2025-11-13 14:11:50');

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipo` enum('admin','funcionario') DEFAULT 'funcionario',
  `status` enum('ativo','inativo','pendente','rejeitado') DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL,
  `remember_token_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`nome`,`email`,`senha`,`telefone`,`tipo`,`status`,`created_at`,`updated_at`,`remember_token`,`remember_token_expires_at`) values 
(12,'Gabriel','jooaogabriel08.2007@gmail.com','$2y$10$f5gfbFFUs4enaRBev3/lter0zOhnICp9vV8ho6bMrbktJ/MRSLdEe','86995545082','admin','ativo','2025-10-24 09:39:15','2025-11-21 09:26:42',NULL,NULL),
(13,'funcionario','funcionario@gestctt.com','$2y$10$aAqE4VVGHT5ao79EhTLLEOIii2K5S0fFWvX.L86VQUzivWXZQroaG','(89) 3381-2499','funcionario','inativo','2025-10-24 10:09:22','2025-11-21 13:36:46',NULL,NULL),
(14,'miguel','miguel@gmail.com','$2y$10$M2iKxxqgBPDdwqJdCfZn2.Fh9Tio8a3BsH6.3Wgy3yODOdVQEt.na','995545082','funcionario','inativo','2025-10-31 08:00:57','2025-11-21 13:36:52',NULL,NULL),
(15,'Marcos','marcos@gmail.com','$2y$10$11K8urhjsWtm24NNayW9E.q9gASJw99930IJIZ8ZllpIx96hpKlou','995545082','funcionario','inativo','2025-10-31 08:30:36','2025-11-21 13:36:50',NULL,NULL),
(16,'ar','ar@gmail.com','$2y$10$EoKW1mZPgu6nl0eLkhfuledMSKD/XH1bcVrTYkapYM1M6dDvqiGQy','86988695049','funcionario','inativo','2025-11-13 12:20:43','2025-11-21 13:36:48',NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
