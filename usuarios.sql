CREATE DATABASE IF NOT EXISTS `login-simple`;

USE `login-simple`;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cookie` int NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `token` VARCHAR(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3;