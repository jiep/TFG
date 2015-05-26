
CREATE DATABASE IF NOT EXISTS `rankings` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `rankings`;

CREATE TABLE IF NOT EXISTS `partidos` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `temporada` varchar(9) COLLATE utf8_unicode_ci NOT NULL,
  `jornada` int(2) unsigned NOT NULL,
  `equipo_local` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `equipo_visitante` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `goles_local` int(2) NOT NULL,
  `goles_visitante` int(2) NOT NULL,
  `dif_goles` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `equipos` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8 NOT NULL,
  `escudo` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `rankings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `equipo` varchar(50) CHARACTER SET utf8 NOT NULL,
  `posicion` int(2) unsigned NOT NULL,
  `puntos` int(4) unsigned NOT NULL,
  `partidos_ganados` int(4) unsigned NOT NULL,
  `partidos_empatados` int(4) unsigned NOT NULL,
  `partidos_perdidos` int(4) unsigned NOT NULL,
  `goles_favor` int(4) unsigned NOT NULL,
  `goles_contra` int(4) unsigned NOT NULL,
  `diferencia_goles` tinyint(4) NOT NULL,
  `jornada` int(2) unsigned NOT NULL,
  `temporada` varchar(10) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `apiKey` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `competitivity_graph` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `id_user` int(3) NOT NULL,
  `nms` double NOT NULL,
  `eficiency` double NOT NULL,
  `cpl` double NOT NULL,
  `diameter` double NOT NULL,
  `nmd` double NOT NULL,
  `kendall` double NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_user`) REFERENCES users(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `graph_vertex` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `id_graph` int(3) NOT NULL,
  `source` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `weight` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_graph`) REFERENCES competitivity_graph(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
