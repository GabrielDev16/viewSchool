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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `ala` */

insert  into `ala`(`id`,`nome`,`descricao`,`status`,`created_at`,`updated_at`) values 
(41,'Sala de Assistência Estudantil','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(42,'Sala 01','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(43,'Sala 02','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(44,'Sala 03','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(45,'Sala 04','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(46,'Sala 05','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(47,'Sala 06','Laboratório de Informática','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(48,'Sala 07','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(49,'Sala 08','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(50,'Sala 09','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12'),
(51,'Sala 10','Sala de Aula','ativo','2025-11-21 18:06:12','2025-11-21 18:06:12');

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
  `numero_tombamento` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ala` (`fk_ala`),
  CONSTRAINT `equipamentos_ibfk_1` FOREIGN KEY (`fk_ala`) REFERENCES `ala` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=568 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `equipamentos` */

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `problemas` */

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`nome`,`email`,`senha`,`telefone`,`tipo`,`status`,`created_at`,`updated_at`,`remember_token`,`remember_token_expires_at`) values 
(18,'João Gabriel','jooaogabriel08.2007@gmail.com','$2y$10$JaafmIDXX4Ei8XlSBWwUJ.wRx/LWy7QdoTQMGlRBjh3qjpoGegOA.','995545082','admin','ativo','2025-11-21 18:12:47','2025-11-21 18:13:06',NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
