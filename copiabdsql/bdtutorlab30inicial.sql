-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 04-01-2013 a las 14:20:04
-- Versión del servidor: 5.5.28
-- Versión de PHP: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `bdtutorlab30inicial`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_alumno`
--

CREATE TABLE IF NOT EXISTS `tb_alumno` (
  `idalumno` int(3) DEFAULT NULL,
  `alumno` varchar(41) DEFAULT NULL,
  `unidad` varchar(8) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_alumno`
--

INSERT INTO `tb_alumno` (`idalumno`, `alumno`, `unidad`) VALUES
(1, 'Apellido1 Apellido2, Nombre', '0 NIV A');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `tb_edicionevaluaciones`
--

INSERT INTO `tb_edicionevaluaciones` (`ideval`, `nombreeval`) VALUES
(1, 'Evaluación Inicial');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_profesores`
--

CREATE TABLE IF NOT EXISTS `tb_profesores` (
  `idprofesor` int(2) DEFAULT NULL,
  `Empleado` varchar(200) DEFAULT NULL,
  `DNI` varchar(9) DEFAULT NULL,
  `IDEA` varchar(10) DEFAULT NULL,
  `tutorde` varchar(8) DEFAULT NULL,
  `email` varchar(200) DEFAULT 'correo@prueba.es',
  `administrador` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_profesores`
--

INSERT INTO `tb_profesores` (`idprofesor`, `Empleado`, `DNI`, `IDEA`, `tutorde`, `email`, `administrador`) VALUES
(1, 'Administrador/a', '12345678', 'ADMIN', '', 'correo@prueba.es', 1),
(2, 'Profesor/a', '4444', 'PROF', '0 NIV A', 'correo@prueba.es', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
