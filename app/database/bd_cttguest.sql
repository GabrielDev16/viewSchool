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
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*Data for the table `ala` */

insert  into `ala`(`id`,`nome`) values 
(3,'sala 1'),
(4,'sala 2'),
(5,'sala 3');

/*Table structure for table `equipamentos` */

DROP TABLE IF EXISTS `equipamentos`;

CREATE TABLE `equipamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) NOT NULL,
  `imagem` mediumblob DEFAULT NULL,
  `situacao` varchar(60) DEFAULT NULL,
  `fk_ala` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `equipamentos_ibfk_1` (`fk_ala`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*Data for the table `equipamentos` */

insert  into `equipamentos`(`id`,`nome`,`imagem`,`situacao`,`fk_ala`) values 
(28,'data show','uploads/datashow.avif',NULL,3),
(29,'datashow','uploads/datashow.avif',NULL,3),
(30,'ar condicionado','uploads/arcondicionado.webp',NULL,3),
(31,'ar condicionado','uploads/arcondicionado.webp',NULL,4);

/*Table structure for table `perfis` */

DROP TABLE IF EXISTS `perfis`;

CREATE TABLE `perfis` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `matricula` varchar(45) DEFAULT NULL,
  `perfiscol` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*Data for the table `perfis` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
