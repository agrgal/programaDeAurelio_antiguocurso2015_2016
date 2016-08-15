-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 18-04-2013 a las 17:25:28
-- Versión del servidor: 5.5.29
-- Versión de PHP: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `bdtutorlab`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_alumno`
--

CREATE TABLE IF NOT EXISTS `tb_alumno` (
  `idalumno` int(4) NOT NULL AUTO_INCREMENT,
  `alumno` varchar(41) DEFAULT NULL,
  `unidad` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`idalumno`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

--
-- Volcado de datos para la tabla `tb_alumno`
--

INSERT INTO `tb_alumno` (`idalumno`, `alumno`, `unidad`) VALUES
(1, 'NIETO SERRANO, EDUARDO', '1 ESO A'),
(2, 'SAEZ GARRIDO, VICTOR MANUEL', '1 ESO A'),
(3, 'SOLER AGUILAR, CESAR', '1 ESO A'),
(4, 'GALLEGO SOLER, ANA', '1 ESO A'),
(5, 'HERRERO CASTILLO, MARIA VICTORIA', '1 ESO A'),
(6, 'TORRES DELGADO, MARIA PILAR', '1 ESO A'),
(7, 'SANTOS VEGA, MARIANO', '1 ESO A'),
(8, 'SOLER FERNANDEZ, AURORA', '1 ESO A'),
(9, 'CANO GOMEZ, FRANCISCO JAVIER', '1 ESO A'),
(10, 'VICENTE HIDALGO, JUAN ANTONIO', '1 ESO A'),
(11, 'VIDAL MEDINA, JOSE IGNACIO', '1 ESO A'),
(12, 'IBAÑEZ DELGADO, CLAUDIA', '1 ESO A'),
(13, 'TORRES RAMIREZ, ANTONIA', '1 ESO A'),
(14, 'DIEZ VICENTE, EUGENIO', '1 ESO A'),
(15, 'ALONSO CALVO, LIDIA', '1 ESO A'),
(16, 'RODRIGUEZ SANTANA, MARCOS', '1 ESO A'),
(17, 'HERRERA ALVAREZ, MIRIAM', '1 ESO A'),
(18, 'SAEZ MARTIN, LIDIA', '1 ESO A'),
(19, 'CRESPO MARQUEZ, DAVID', '1 ESO A'),
(20, 'VELASCO SANZ, YOLANDA', '1 ESO A'),
(21, 'GOMEZ VELASCO, JOAQUIN', '1 ESO A'),
(22, 'CRUZ AGUILAR, JOSEFA', '1 ESO A'),
(23, 'NUÑEZ SAEZ, MARINA', '1 ESO A'),
(24, 'ESTEBAN VIDAL, MARIA JOSE', '1 ESO A'),
(25, 'CORTES LORENZO, MIGUEL ANGEL', '1 ESO B'),
(26, 'NUÑEZ IBAÑEZ, EVA', '1 ESO B'),
(27, 'REYES ORTEGA, OLGA', '1 ESO B'),
(28, 'PEÑA RAMOS, JOSE MANUEL', '1 ESO B'),
(29, 'GALLEGO RUIZ, PAULA', '1 ESO B'),
(30, 'LEON LOPEZ, JOSE LUIS', '1 ESO B'),
(31, 'ORTEGA HERNANDEZ, JOSEFINA', '1 ESO B'),
(32, 'CALVO CARRASCO, JUANA', '1 ESO B'),
(33, 'FERNANDEZ LOPEZ, DOLORES', '1 ESO B'),
(34, 'BENITEZ ROJAS, PABLO', '1 ESO B'),
(35, 'IGLESIAS CORTES, MARIA VICTORIA', '1 ESO B'),
(36, 'MENDEZ ARIAS, LUCIA', '1 ESO B'),
(37, 'ORTEGA CRUZ, MARIA NIEVES', '1 ESO B'),
(38, 'MOLINA RUIZ, RAUL', '1 ESO B'),
(39, 'MARIN FUENTES, TOMAS', '1 ESO B'),
(40, 'FERNANDEZ NAVARRO, GONZALO', '1 ESO B'),
(41, 'SANCHEZ GALLEGO, JOSE FRANCISCO', '1 ESO B'),
(42, 'FERNANDEZ MARTINEZ, PABLO', '1 ESO B'),
(43, 'REYES GOMEZ, JUAN ANTONIO', '1 ESO B'),
(44, 'GIL SOTO, DIEGO', '1 ESO B'),
(45, 'MARIN MOLINA, CONCEPCION', '1 ESO B'),
(46, 'SAEZ PRIETO, JAVIER', '1 ESO B'),
(47, 'MOYA VIDAL, DIEGO', '1 ESO B'),
(48, 'DIEZ VEGA, JOSE MIGUEL', '1 ESO B'),
(49, 'PASTOR GALLEGO, ALBERT', '1 ESO B'),
(50, 'RAMIREZ SANTIAGO, ANGEL', '1 ESO B'),
(51, 'PRIETO FUENTES, ANA', '1 ESO B'),
(52, 'MORALES GIMENEZ, MARIA PILAR', '1 ESO B'),
(53, 'GOMEZ NAVARRO, SARA', '1 ESO B'),
(54, 'RODRIGUEZ RODRIGUEZ, JOSE CARLOS', '1 ESO B'),
(55, 'RAMIREZ CRESPO, ISMAEL', '1 BACH A'),
(56, 'ARIAS FERRER, MARIA CONCEPCION', '1 BACH A'),
(57, 'ROMAN LORENZO, JOSE CARLOS', '1 BACH A'),
(58, 'FERNANDEZ MARIN, DAVID', '1 BACH A'),
(59, 'ORTIZ RAMIREZ, FRANCISCA', '1 BACH A'),
(60, 'SANTIAGO GALLEGO, SEBASTIAN', '1 BACH A'),
(61, 'VELASCO RODRIGUEZ, JOSE ANTONIO', '1 BACH A'),
(62, 'GUERRERO CASTRO, ANGELES', '1 BACH A'),
(63, 'GONZALEZ GARRIDO, MANUEL', '1 BACH A'),
(64, 'CABALLERO CORTES, JOSE ANTONIO', '1 BACH A'),
(65, 'MARTINEZ HERRERO, MARIA ELENA', '1 BACH A'),
(66, 'MEDINA HERRERA, SILVIA', '1 BACH A'),
(67, 'RAMIREZ CASTILLO, AITOR', '1 BACH A'),
(68, 'CABALLERO CANO, INMACULADA', '1 BACH A'),
(69, 'REYES ARIAS, CARLOS', '1 BACH A'),
(70, 'RAMOS NIETO, MARIA ISABEL', '1 BACH A'),
(71, 'DIEZ FLORES, ELENA', '1 BACH A'),
(72, 'CABALLERO GOMEZ, JOSEFINA', '1 BACH A'),
(73, 'GUERRERO JIMENEZ, NICOLAS', '1 BACH A'),
(74, 'CANO IBAÑEZ, NOELIA', '1 BACH A'),
(75, 'CALVO BENITEZ, FRANCISCA', '1 BACH A'),
(76, 'GARRIDO VAZQUEZ, PABLO', '1 BACH A'),
(77, 'RUIZ PRIETO, ANTONIA', '1 BACH A'),
(78, 'VIDAL ALVAREZ, JAVIER', '1 BACH A'),
(79, 'DELGADO ARIAS, VICTORIA', '1 BACH A'),
(80, 'NUÑEZ GOMEZ, ANTONIA', '1CFGSA'),
(81, 'GALLARDO SANTANA, VICENTE', '1CFGSA'),
(82, 'FLORES SANZ, ALFONSO', '1CFGSA'),
(83, 'HERRERA CARMONA, MARIANO', '1CFGSA'),
(84, 'ESTEBAN CASTRO, LUIS', '1CFGSA'),
(85, 'DURAN CARMONA, MARIA NIEVES', '1CFGSA'),
(86, 'ORTIZ BLANCO, JUAN ANTONIO', '1CFGSA'),
(87, 'LOZANO IGLESIAS, TERESA', '1CFGSA'),
(88, 'FERNANDEZ MOYA, ANA ISABEL', '1CFGSA'),
(89, 'PEREZ BLANCO, JUANA', '1CFGSA'),
(90, 'ESTEBAN NIETO, MARGARITA', '1CFGSA'),
(91, 'CALVO CABALLERO, JOSE FRANCISCO', '1CFGSA'),
(92, 'REYES SERRANO, MARIA CONCEPCION', '1CFGSA'),
(93, 'SOLER SANTIAGO, IRENE', '1CFGSA'),
(94, 'SUAREZ DURAN, CARLOS', '1CFGSA'),
(95, 'IGLESIAS PEÑA, LUISA', '1CFGSA'),
(96, 'DELGADO SOLER, RUBEN', '1CFGSA'),
(97, 'MENDEZ IGLESIAS, CONCEPCION', '1CFGSA'),
(98, 'MUÑOZ SANTOS, FRANCISCA', '1CFGSA'),
(99, 'BRAVO MENDEZ, NOELIA', '1CFGSA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_anotaciones`
--

CREATE TABLE IF NOT EXISTS `tb_anotaciones` (
  `idanotacion` int(11) NOT NULL AUTO_INCREMENT,
  `asignacion` int(8) NOT NULL,
  `alumno` int(4) NOT NULL,
  `fecha` date NOT NULL,
  `anotacion` blob NOT NULL,
  PRIMARY KEY (`idanotacion`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Volcado de datos para la tabla `tb_anotaciones`
--

INSERT INTO `tb_anotaciones` (`idanotacion`, `asignacion`, `alumno`, `fecha`, `anotacion`) VALUES
(39, 4, 3, '2013-04-27', 0x3c70207374796c653d226d617267696e3a20307078203070783b223e6673667320616166206164663c2f703e),
(40, 4, 3, '2013-05-08', 0x3c70207374796c653d226d617267696e3a20307078203070783b223e2661637574653b6f266f61637574653b266f61637574653b6f2661637574653b6f266f61637574653b3c7374726f6e673e3c7370616e207374796c653d22636f6c6f723a20236666303030303b223e2655756d6c3b552655756d6c3b266e74696c64653b266e74696c64653b266e74696c64653b3c2f7370616e3e3c2f7374726f6e673e264e74696c64653b264e74696c64653b264e74696c64653b264e74696c64653b3c2f703e),
(43, 1, 3, '2013-04-10', 0x3c703e6161613c2f703e),
(44, 1, 3, '2013-04-25', 0x3c703e62626262623c2f703e),
(45, 1, 4, '2013-04-05', 0x3c703e7373736173612061732061732061733c2f703e),
(46, 4, 3, '2013-04-17', 0x3c703e266561637574653b266561637574653b266561637574653b266561637574653b266961637574653b266961637574653b266961637574653b266f61637574653b266f61637574653b266f61637574653b266f61637574653b3c2f703e);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_asignaciones`
--

CREATE TABLE IF NOT EXISTS `tb_asignaciones` (
  `idasignacion` int(10) NOT NULL AUTO_INCREMENT,
  `profesor` int(2) NOT NULL,
  `materia` int(2) NOT NULL,
  `datos` varchar(1000) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `tutorada` tinyint(1) NOT NULL,
  PRIMARY KEY (`idasignacion`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `tb_asignaciones`
--

INSERT INTO `tb_asignaciones` (`idasignacion`, `profesor`, `materia`, `datos`, `descripcion`, `tutorada`) VALUES
(1, 1, 39, '1 ESO A', 'TECNOLOGIA APLICADA -  1Âº ESO A', 0),
(2, 2, 31, '1 ESO A', 'Yo les doy matemÃ¡ticas', 0),
(3, 3, 29, '15#9#25#19#22#14#33#40#53#5#39#36#52#1#26#37#28#50#43#54#2#3#13#10', 'AquÃ­ se les imparte Lengua', 0),
(4, 4, 4, '1 ESO A', 'Soy tutor y les doy naturales', 1),
(5, 5, 27, '15#19#14#4#17#12#23#16#7#8#6#20#11', 'And I teach English here', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_asignaturas`
--

CREATE TABLE IF NOT EXISTS `tb_asignaturas` (
  `idmateria` int(2) DEFAULT NULL,
  `Materias` varchar(46) DEFAULT NULL,
  `Abr` varchar(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_asignaturas`
--

INSERT INTO `tb_asignaturas` (`idmateria`, `Materias`, `Abr`) VALUES
(1, 'BIOLOGÍA Y GEOLOGÍA', 'BYG'),
(2, 'CAMBIOS SOCIALES Y DE GÉNERO', ''),
(3, 'CAMBIOS SOCIALES Y N.R.G.', ''),
(4, 'CIENCIAS NATURALES', 'CNA'),
(5, 'CIENCIAS PARA EL MUNDO CONTEMPORÁNEO', ''),
(6, 'CIENCIAS SOCIALES', 'CSO'),
(7, 'CULTURA CLÁSICA', ''),
(8, 'DIBUJO ARTÍSTICO', ''),
(9, 'DIBUJO TÉCNICO', ''),
(10, 'ECONOMíA', ''),
(11, 'ED. PLÁSTICA Y VISUAL', 'EPV'),
(12, 'EDUC CIUDADANÍA Y LOS DERECHOS HUMANOS', ''),
(13, 'EDUC ÉTICO-CÍVICA', ''),
(14, 'EDUCACIÓN FÍSICA', 'EF'),
(15, 'ELECTROTECNIA', ''),
(16, 'ENS. DE RELIGIÓN', ''),
(17, 'FILOSOFÍA', 'FIL'),
(18, 'FÍSICA Y QUÍMICA', 'FYQ'),
(19, 'FRANCÉS', 'FR'),
(20, 'GEOGRAFÍA', 'GEO'),
(21, 'GEOGRAFíA E HISTORIA', 'GEH'),
(22, 'GRIEGO', ''),
(23, 'HISTORIA DE ESPAÑA', ''),
(24, 'HISTORIA DE ESPAÑA', ''),
(25, 'HISTORIA DE LA FILOSOFÍA', ''),
(26, 'INFORMÁTICA', 'INF'),
(27, 'INGLES', 'ING'),
(28, 'LATÍN', ''),
(29, 'LENGUA CASTELLANA', 'LCL'),
(30, 'LENGUA CASTELLANA Y LITERATURA', 'LCL'),
(31, 'MATEMÁTICAS', 'MAT'),
(32, 'MATEMÁTICAS A', ''),
(33, 'MATEMÁTICAS B', ''),
(34, 'MÚSICA', 'MUS'),
(35, 'OPTATIVA', ''),
(36, 'PROYECTO INTEGRADO', 'PI'),
(37, 'RELIGIÓN O CULTURA RELIGIOSA', 'REL'),
(38, 'TECNOLOGÍA', 'TEC'),
(39, 'TECNOLOGÍA APLICADA', 'TAP'),
(40, 'TECNOLOGÍA DE LA INFORMACIÓN Y COMUNICACIÓN', 'TIC'),
(41, 'TECNOLOGÍA INDUSTRIAL', 'TIN'),
(42, 'VIDA MORAL', 'VMO'),
(43, 'FÍSICA', 'FIS'),
(44, 'QUÍMICA', 'QUI'),
(45, 'CIENCIAS DE LA TIERRA Y MEDIO AMBIENTE', 'CTM'),
(46, 'Ámbito Científico Tecnológico', 'ACT'),
(47, 'Ámbito Socio Lingüístico', 'ASL'),
(49, 'HISTORIA DEL MUNDO CONTEMPORÁNEO', 'HMC'),
(48, 'Ámbito Práctico', 'APR'),
(50, 'HISTORIA DEL ARTE', 'HAR'),
(51, 'LITERATURA UNIVERSAL', 'LUN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_edicionevaluaciones`
--

CREATE TABLE IF NOT EXISTS `tb_edicionevaluaciones` (
  `ideval` int(4) NOT NULL AUTO_INCREMENT,
  `nombreeval` varchar(50) NOT NULL,
  PRIMARY KEY (`ideval`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Volcado de datos para la tabla `tb_edicionevaluaciones`
--

INSERT INTO `tb_edicionevaluaciones` (`ideval`, `nombreeval`) VALUES
(1, 'Evaluación Inicial'),
(11, 'EVALUACION OCTUBRE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_evaluacion`
--

CREATE TABLE IF NOT EXISTS `tb_evaluacion` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `alumno` int(4) NOT NULL,
  `asignacion` int(8) NOT NULL,
  `eval` int(4) NOT NULL,
  `items` varchar(300) NOT NULL,
  `observaciones` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Volcado de datos para la tabla `tb_evaluacion`
--

INSERT INTO `tb_evaluacion` (`id`, `fecha`, `alumno`, `asignacion`, `eval`, `items`, `observaciones`) VALUES
(1, '2013-03-24', 1, 1, 1, '12#10#18#35#25#26#27', 0x3c703e313c2f703e),
(25, '2013-04-14', 1, 4, 11, '12#10#11', 0x3c703e4a616a6a616a61206b6c666b7364666b3c2f703e),
(2, '2013-01-18', 2, 1, 1, '', 0x3c703e323c2f703e),
(3, '2013-01-18', 3, 1, 1, '', 0x3c703e333c2f703e),
(4, '2013-01-18', 4, 1, 1, '', 0x3c703e343c2f703e),
(5, '2013-01-18', 5, 1, 1, '12#10#7#21#22#34#36#25#27', ''),
(6, '2013-01-18', 24, 1, 1, '12#13#34#36', 0x3c703e506f7220617175266961637574653b2061207665723c2f703e),
(7, '2013-01-18', 1, 2, 1, '12#33#26#27', 0x3c703e413c2f703e),
(8, '2013-01-18', 2, 2, 1, '18#20#37', 0x3c703e423c2f703e),
(9, '2013-01-18', 24, 2, 1, '12#13', 0x3c703e5a3c2f703e),
(10, '2013-01-18', 23, 2, 1, '12#10#21#22#36#27', 0x3c703e593c2f703e),
(11, '2013-01-18', 1, 3, 1, '12#35#24', 0x3c703e613c2f703e),
(12, '2013-01-18', 2, 3, 1, '13#7#35#36#27#28', 0x3c703e623c2f703e),
(13, '2013-01-18', 3, 3, 1, '12#13#10#21#22#35#26#28', 0x3c703e633c2f703e),
(14, '2013-01-18', 22, 3, 1, '14#23#9#11#17#7#21#34', 0x3c703e267561637574653b6c74696d612064656c20413c2f703e),
(15, '2013-01-18', 25, 3, 1, '12#13#10#21#22#36#28#30', 0x3c703e7072696d65726f2064656c20423c2f703e),
(16, '2013-01-18', 40, 3, 1, '13', 0x3c703e267561637574653b6c74696d6f2064656c20423c2f703e),
(17, '2013-01-18', 1, 4, 1, '14#23#9#11#17#7#8#18#20#33', 0x3c703e5072696d65726f206465204e61747572616c65733c2f703e),
(18, '2013-01-18', 24, 4, 1, '12#13#10#21#22#36#29', 0x3c703e265561637574653b6c74696d6f206465206e61747572616c65733c2f703e),
(19, '2013-01-18', 11, 4, 1, '14#23#9#11#17#7#8#18#20#33#24#25#26#27#28#29#30#31#32', 0x3c703e506f7220656c206d6564696f3c2f703e),
(20, '2013-01-18', 4, 5, 1, '12#13#20#25#26', 0x3c703e46697273743c2f703e),
(21, '2013-01-18', 6, 5, 1, '12#13#9#10#7#8#36#26#27', ''),
(22, '2013-01-18', 7, 5, 1, '18#20#37', 0x3c703e54686972643c2f703e),
(23, '2013-01-18', 23, 5, 1, '12#13#7#8#33#34', 0x3c703e4c617374206f6e653c2f703e),
(24, '2013-01-18', 20, 5, 1, '12#13#7#8#34#35#28', 0x3c703e6c61737420627574206f6e653c2f703e),
(26, '2013-04-14', 3, 4, 11, '33#34', 0x3c703e6466616661736466613c2f703e);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_itemsevaluacion`
--

CREATE TABLE IF NOT EXISTS `tb_itemsevaluacion` (
  `iditem` int(4) NOT NULL AUTO_INCREMENT,
  `item` varchar(250) NOT NULL,
  `grupo` varchar(50) NOT NULL,
  `positivo` int(1) NOT NULL,
  PRIMARY KEY (`iditem`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

--
-- Volcado de datos para la tabla `tb_itemsevaluacion`
--

INSERT INTO `tb_itemsevaluacion` (`iditem`, `item`, `grupo`, `positivo`) VALUES
(24, 'Adaptar metodología e instr. de evaluación', 'SUPERAR DIFICULTADES', 2),
(25, 'Aplicar actividades de refuerzo y apoyo', 'SUPERAR DIFICULTADES', 2),
(23, 'Podría esforzarse más', 'ACTITUD', 0),
(26, 'Proponer actividades de ampliación', 'SUPERAR DIFICULTADES', 2),
(7, 'Muestra dificultades en aprendizajes básicos', 'DIFICULTADES', 0),
(8, 'Dificultades de relación e integración en el aula', 'DIFICULTADES', 0),
(9, 'Se distrae con frecuencia. No atiende', 'COMPORTAMIENTO', 0),
(10, 'Respeta al profesorado y a sus compañeros/as', 'COMPORTAMIENTO', 1),
(11, 'Su comportamiento en clase es negativo', 'COMPORTAMIENTO', 0),
(12, 'Muestra esfuerzo por aprender y superarse', 'ACTITUD', 1),
(13, 'Muestra actitud positiva en el aula y en el centro', 'ACTITUD', 1),
(14, 'Muestra abandono del área', 'ACTITUD', 0),
(17, 'No participa en clase', 'COMPORTAMIENTO', 0),
(18, 'No estudia', 'RENDIMIENTO Y TAREAS', 0),
(22, 'Trabaja con regularidad', 'RENDIMIENTO Y TAREAS', 1),
(20, 'No trabaja con regularidad', 'RENDIMIENTO Y TAREAS', 0),
(21, 'Estudia', 'RENDIMIENTO Y TAREAS', 1),
(27, 'Mejorar métodos de estudio y hábitos de trabajo', 'SUPERAR DIFICULTADES', 2),
(28, 'Proponer estudio psicopedagógico o elaborar ACIs', 'SUPERAR DIFICULTADES', 2),
(29, 'Aumentar control periódico tareas-cuadernos', 'SUPERAR DIFICULTADES', 2),
(30, 'Incidir en el control del comportamiento en clase', 'SUPERAR DIFICULTADES', 2),
(31, 'Ampliar la acción tutorial y la orientación profesional', 'SUPERAR DIFICULTADES', 2),
(32, 'Proponer contrato educativo con familia-alumnado', 'SUPERAR DIFICULTADES', 2),
(33, 'Alta probabilidad de suspender (menos de 3)', 'NOTA ORIENTATIVA', 3),
(34, 'Posibilidad de suspender (3 ó 4)', 'NOTA ORIENTATIVA', 3),
(35, 'Aprobado (5 ó 6)', 'NOTA ORIENTATIVA', 3),
(36, 'Buen trabajo (6, 7 y 8)', 'NOTA ORIENTATIVA', 3),
(37, 'Trabajo excelente (entre 8 y 10)', 'NOTA ORIENTATIVA', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_opiniongeneral`
--

CREATE TABLE IF NOT EXISTS `tb_opiniongeneral` (
  `idopiniongeneral` int(10) NOT NULL AUTO_INCREMENT,
  `eval` int(4) NOT NULL,
  `asignacion` int(8) NOT NULL,
  `opinion` blob NOT NULL,
  `actuaciones` blob NOT NULL,
  `mejora` blob NOT NULL,
  PRIMARY KEY (`idopiniongeneral`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `tb_opiniongeneral`
--

INSERT INTO `tb_opiniongeneral` (`idopiniongeneral`, `eval`, `asignacion`, `opinion`, `actuaciones`, `mejora`) VALUES
(1, 1, 1, 0x3c703e506f636f7320616c756d6e6f7320636f6e206469666963756c74616465732e3c2f703e, 0x3c703e5265667565727a6f7320637572726963756c6172657320656e2063696572746f73206361736f732e3c2f703e, 0x3c703e526576697369266f61637574653b6e206672656375656e746573206465207461726561732e3c2f703e),
(2, 1, 3, 0x3c703e413c2f703e, 0x3c703e423c2f703e, 0x3c703e433c2f703e),
(3, 1, 4, 0x3c703e437572736f2062617374616e746520646973747261266961637574653b646f2e204e6f207375656c656e206174656e6465722e3c2f703e, 0x3c703e436f6e736567756972206c6120636f6c61626f72616369266f61637574653b6e206465206c6f73207061647265732f6d61647265732e3c2f703e, 0x3c703e4c6c657661722061206361626f20756e2073656775696d69656e746f206578686175737469766f20646520616c67756e6f7320616c756d6e6f732e3c2f703e),
(4, 1, 5, 0x3c703e436f6e666c69637469766f3c2f703e, 0x3c703e52657065746972207920616669616e7a61722063696572746f7320636f6e74656e69646f733c2f703e, 0x3c703e4e6f2063616d6269617220646520736974696f2061206c6f7320616c756d6e6f733c2f703e);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_profesores`
--

CREATE TABLE IF NOT EXISTS `tb_profesores` (
  `idprofesor` int(2) NOT NULL AUTO_INCREMENT,
  `Empleado` varchar(200) DEFAULT NULL,
  `DNI` varchar(9) DEFAULT NULL,
  `IDEA` varchar(10) DEFAULT NULL,
  `tutorde` varchar(8) DEFAULT NULL,
  `email` varchar(200) DEFAULT 'correo@prueba.es',
  `administrador` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idprofesor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Volcado de datos para la tabla `tb_profesores`
--

INSERT INTO `tb_profesores` (`idprofesor`, `Empleado`, `DNI`, `IDEA`, `tutorde`, `email`, `administrador`) VALUES
(1, 'GALLARDO RODRÍGUEZ, AURELIO', '31667329D', 'agalrod329', '', 'agr1971gal@yahoo.es\n', 1),
(2, 'NUÑEZ FUENTES, PILAR', '12345678A', 'valor1', '', 'correo@prueba.es', 0),
(3, 'FERNANDEZ ORTIZ, JOAQUIN', '12345678B', 'valor2', '', 'correo@prueba.es', 0),
(4, 'MARQUEZ DIAZ, CONCEPCION', '87654321A', 'valor3', '1 ESO A', 'correo@prueba.es', 0),
(5, 'RAMOS CASTILLO, MARTIN', '87654321B', 'valor4', '', 'correo@prueba.es', 0),
(6, 'FUENTES SANTOS, LUCIA', '98765432A', 'valor5', '', 'correo@prueba.es', 0),
(7, 'BRAVO GARRIDO, LUCIA', '98765432B', 'valor6', '1 ESO B', 'correo@prueba.es', 0),
(8, 'GONZALEZ PASCUAL, CRISTIAN', '12345679A', 'valor7', '1CFGSA', 'correo@prueba.es', 0),
(9, 'RUBIO GUTIERREZ, GABRIEL', '12345679B', 'valor8', '1 BACH A', 'correo@prueba.es', 0),
(10, 'ALVAREZ NAVARRO, ANA ISABEL', '12312312A', 'valor9', '', 'correo@prueba.es', 0),
(11, 'REYES SOTO, JOSEFINA', '12312312B', 'valor10', '', 'correo@prueba.es', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
