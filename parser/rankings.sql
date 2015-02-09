
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
