-- MySQL dump 10.13  Distrib 5.7.20, for Linux (x86_64)
--
-- Host: localhost    Database: nupres_dev_demo01
-- ------------------------------------------------------
-- Server version	5.7.20-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `diagnosticos`
--

DROP TABLE IF EXISTS `diagnosticos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diagnosticos` (
  `refdx_id` varchar(20) NOT NULL,
  `ingresos_id` int(10) unsigned zerofill NOT NULL,
  PRIMARY KEY (`refdx_id`,`ingresos_id`),
  KEY `fk_diagnosticos_2_idx` (`ingresos_id`),
  CONSTRAINT `fk_diagnosticos_1` FOREIGN KEY (`refdx_id`) REFERENCES `ref-dx` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_diagnosticos_2` FOREIGN KEY (`ingresos_id`) REFERENCES `ingresos` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnosticos`
--

LOCK TABLES `diagnosticos` WRITE;
/*!40000 ALTER TABLE `diagnosticos` DISABLE KEYS */;
/*!40000 ALTER TABLE `diagnosticos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evoluciones`
--

DROP TABLE IF EXISTS `evoluciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evoluciones` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Identificacion del registo de ingreso del paciente',
  `ingreso_id` int(10) unsigned NOT NULL COMMENT 'Identificacion de la historia clinica',
  `manejo_id` char(5) DEFAULT 'PE',
  `formula_id` char(5) DEFAULT 'PE',
  `fecha_evolucion` datetime DEFAULT NULL,
  `peso_actual` decimal(3,1) DEFAULT '0.0' COMMENT 'Peso actual del paciente',
  `kilocalorias_kilogramo_peso` decimal(10,0) DEFAULT '0',
  `meta_volumen` int(11) DEFAULT '0',
  `gramos_proteina_diaria` int(11) DEFAULT '0',
  `volumen_infundido` int(11) DEFAULT '0',
  PRIMARY KEY (`id`,`ingreso_id`),
  KEY `fk_evoluciones_1_idx` (`ingreso_id`),
  KEY `fk_evoluciones_2` (`manejo_id`),
  KEY `fk_evoluciones_3` (`formula_id`),
  CONSTRAINT `fk_evoluciones_1` FOREIGN KEY (`ingreso_id`) REFERENCES `ingresos` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_evoluciones_2` FOREIGN KEY (`manejo_id`) REFERENCES `ref-manejos` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_evoluciones_3` FOREIGN KEY (`formula_id`) REFERENCES `ref-formulas` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evoluciones`
--

LOCK TABLES `evoluciones` WRITE;
/*!40000 ALTER TABLE `evoluciones` DISABLE KEYS */;
INSERT INTO `evoluciones` (`id`, `ingreso_id`, `manejo_id`, `formula_id`, `fecha_evolucion`, `peso_actual`, `kilocalorias_kilogramo_peso`, `meta_volumen`, `gramos_proteina_diaria`, `volumen_infundido`) VALUES (0000000002,2,'MD','PEN','2017-11-18 00:00:00',62.5,2877,300,56,50),(0000000003,2,'NE','PEN','2017-11-19 00:00:00',62.5,1402,200,45,100);
/*!40000 ALTER TABLE `evoluciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `informe_cuidado_critico_general`
--

DROP TABLE IF EXISTS `informe_cuidado_critico_general`;
/*!50001 DROP VIEW IF EXISTS `informe_cuidado_critico_general`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `informe_cuidado_critico_general` AS SELECT 
 1 AS `ingreso_cod`,
 1 AS `paciente_doc`,
 1 AS `paciente_nombres`,
 1 AS `paciente_apellidos`,
 1 AS `ingreso`,
 1 AS `dias_ingreso`,
 1 AS `fecha_mipres`,
 1 AS `dias_mipres`,
 1 AS `media_envergadura`,
 1 AS `altura_rodilla`,
 1 AS `fecha_egreso`,
 1 AS `observaciones`,
 1 AS `estado`,
 1 AS `seguimiento`,
 1 AS `motivo_egreso`,
 1 AS `evolucion_id`,
 1 AS `ubicacion`,
 1 AS `cama`,
 1 AS `nacimiento`,
 1 AS `edad`,
 1 AS `sexo`,
 1 AS `eps`,
 1 AS `manejo`,
 1 AS `formula`,
 1 AS `peso`,
 1 AS `talla`,
 1 AS `gasto_energetico_basal`,
 1 AS `indice_masa_corporal`,
 1 AS `kilocalorias_kilogramo_peso`,
 1 AS `meta_calorica`,
 1 AS `meta_volumen`,
 1 AS `gramos_proteina_diaria`,
 1 AS `gramos_proteina_kg_peso`,
 1 AS `cumplimiento_meta_calorica`,
 1 AS `volumen_infundido`,
 1 AS `cumplimiento_meta_volumen`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ingresos`
--

DROP TABLE IF EXISTS `ingresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingresos` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT 'Identificacion del registo de ingreso del paciente',
  `paciente_id` int(11) unsigned NOT NULL COMMENT 'Identificacion de la historia clinica',
  `fecha_ingreso` datetime DEFAULT NULL,
  `eps` varchar(45) DEFAULT NULL,
  `tipo_id` char(5) NOT NULL COMMENT 'Es el tipo de registro con el que se ingresa un paciente al sistema',
  `talla` decimal(3,2) unsigned NOT NULL,
  `media_envergadura` decimal(3,1) unsigned DEFAULT NULL,
  `altura_rodilla` varchar(45) DEFAULT NULL,
  `fecha_mipres` date DEFAULT NULL,
  `fecha_egreso` datetime DEFAULT NULL,
  `motivo_egreso` char(5) DEFAULT 'PE',
  `observaciones` mediumtext,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `seguimiento` enum('S','N') DEFAULT 'S',
  PRIMARY KEY (`id`,`paciente_id`),
  KEY `ingresos_ibfk_1` (`paciente_id`),
  KEY `fk_ingresos_1` (`tipo_id`),
  KEY `fk_ingresos_2` (`motivo_egreso`),
  CONSTRAINT `fk_ingresos_1` FOREIGN KEY (`tipo_id`) REFERENCES `ref-tipos` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_ingresos_2` FOREIGN KEY (`motivo_egreso`) REFERENCES `ref-egresos` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `ingresos_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingresos`
--

LOCK TABLES `ingresos` WRITE;
/*!40000 ALTER TABLE `ingresos` DISABLE KEYS */;
INSERT INTO `ingresos` (`id`, `paciente_id`, `fecha_ingreso`, `eps`, `tipo_id`, `talla`, `media_envergadura`, `altura_rodilla`, `fecha_mipres`, `fecha_egreso`, `motivo_egreso`, `observaciones`, `estado`, `seguimiento`) VALUES (0000000002,80123858,'2017-11-17 00:00:00','FAMISANAR','CC',1.68,23.0,'34','2017-11-11',NULL,'PE','NINGUNA','ACTIVO','S');
/*!40000 ALTER TABLE `ingresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pacientes`
--

DROP TABLE IF EXISTS `pacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pacientes` (
  `id` int(10) unsigned NOT NULL COMMENT 'Numero de identificacion unica del paciente, tambien conocido como historia clinica, ejemplo cedula',
  `nombres` varchar(100) NOT NULL COMMENT 'Nombres del paciente',
  `apellidos` varchar(100) NOT NULL COMMENT 'Apellidos del paciente',
  `genero` enum('F','M') NOT NULL COMMENT 'Genero del paciente',
  `fecha_nacimiento` date NOT NULL COMMENT 'Fecha de nacimiento del paciente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pacientes`
--

LOCK TABLES `pacientes` WRITE;
/*!40000 ALTER TABLE `pacientes` DISABLE KEYS */;
INSERT INTO `pacientes` (`id`, `nombres`, `apellidos`, `genero`, `fecha_nacimiento`) VALUES (80123858,'Diego','Torres','M','1981-06-17'),(125434455,'Mariana','Torres','F','2006-09-12');
/*!40000 ALTER TABLE `pacientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref-dx`
--

DROP TABLE IF EXISTS `ref-dx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref-dx` (
  `id` varchar(20) NOT NULL,
  `diagnostico` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref-dx`
--

LOCK TABLES `ref-dx` WRITE;
/*!40000 ALTER TABLE `ref-dx` DISABLE KEYS */;
INSERT INTO `ref-dx` (`id`, `diagnostico`) VALUES ('DX01','CIRUGIA ABDOMINAL/DISFUNCIÒN INTESTINAL'),('DX02','COMPROMISO INMUNE/QUEMADURAS'),('DX03','DIABETES MELLITUS'),('DX04','ENFERMEDAD RENAL CRONICA SIN TRR'),('DX05','ENFERMEDAD RENAL  CON TRR'),('DX06','ENFERMEDAD PULMONAR CRONICA'),('DX07','FALLA HEPÁTICA'),('DX08','OTROS');
/*!40000 ALTER TABLE `ref-dx` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref-egresos`
--

DROP TABLE IF EXISTS `ref-egresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref-egresos` (
  `id` char(5) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref-egresos`
--

LOCK TABLES `ref-egresos` WRITE;
/*!40000 ALTER TABLE `ref-egresos` DISABLE KEYS */;
INSERT INTO `ref-egresos` (`id`, `nombre`) VALUES ('CC','Cumplimiento Ciclo Terapeutico'),('EH','Egreso hospitalario o de cuidado domiciliario'),('FA','Fallecio'),('PE','Pendiente');
/*!40000 ALTER TABLE `ref-egresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref-formulas`
--

DROP TABLE IF EXISTS `ref-formulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref-formulas` (
  `id` char(5) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref-formulas`
--

LOCK TABLES `ref-formulas` WRITE;
/*!40000 ALTER TABLE `ref-formulas` DISABLE KEYS */;
INSERT INTO `ref-formulas` (`id`, `nombre`) VALUES ('CLI','Clinical'),('COM','Compact'),('DIB','Diben'),('ENS','Ensure'),('GLU','Glucerna'),('NEP','Nepro'),('PEN','Pendiente'),('PER','Perative');
/*!40000 ALTER TABLE `ref-formulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref-manejos`
--

DROP TABLE IF EXISTS `ref-manejos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref-manejos` (
  `id` char(5) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref-manejos`
--

LOCK TABLES `ref-manejos` WRITE;
/*!40000 ALTER TABLE `ref-manejos` DISABLE KEYS */;
INSERT INTO `ref-manejos` (`id`, `nombre`) VALUES ('MD','Modificacion de Dietas'),('NE','Nutricion Enteral'),('NP','Nutricion Parenteral'),('NS','Nurtricion Suplementaria'),('PE','Pendiente');
/*!40000 ALTER TABLE `ref-manejos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref-tipos`
--

DROP TABLE IF EXISTS `ref-tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref-tipos` (
  `id` char(5) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref-tipos`
--

LOCK TABLES `ref-tipos` WRITE;
/*!40000 ALTER TABLE `ref-tipos` DISABLE KEYS */;
INSERT INTO `ref-tipos` (`id`, `nombre`) VALUES ('CC','Cuidado Critico'),('CE','Consulta Externa'),('DO','Domiciliario'),('HO','Hospitalizacion');
/*!40000 ALTER TABLE `ref-tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref-ubicaciones`
--

DROP TABLE IF EXISTS `ref-ubicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref-ubicaciones` (
  `id` char(5) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref-ubicaciones`
--

LOCK TABLES `ref-ubicaciones` WRITE;
/*!40000 ALTER TABLE `ref-ubicaciones` DISABLE KEYS */;
INSERT INTO `ref-ubicaciones` (`id`, `nombre`) VALUES ('HOS','HOSPITALIZACION'),('UCC','UNIDAD DE CUIDADO CRITICO'),('UCI','UNIDAD DE CUIDADOS INTERMEDIOS CRITICOS'),('URG','URGENCIAS');
/*!40000 ALTER TABLE `ref-ubicaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubicaciones`
--

DROP TABLE IF EXISTS `ubicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubicaciones` (
  `refubicacion_id` char(5) NOT NULL,
  `evolucion_id` int(10) unsigned NOT NULL,
  `cama` varchar(45) DEFAULT NULL,
  `fecha_ingreso` datetime DEFAULT NULL,
  `fecha_egreso` datetime DEFAULT NULL,
  PRIMARY KEY (`refubicacion_id`,`evolucion_id`),
  KEY `fk_ubicaciones_2_idx` (`evolucion_id`),
  CONSTRAINT `fk_ubicaciones_1` FOREIGN KEY (`refubicacion_id`) REFERENCES `ref-ubicaciones` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_ubicaciones_2` FOREIGN KEY (`evolucion_id`) REFERENCES `evoluciones` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubicaciones`
--

LOCK TABLES `ubicaciones` WRITE;
/*!40000 ALTER TABLE `ubicaciones` DISABLE KEYS */;
INSERT INTO `ubicaciones` (`refubicacion_id`, `evolucion_id`, `cama`, `fecha_ingreso`, `fecha_egreso`) VALUES ('UCI',3,'3','2017-11-19 00:00:00',NULL),('URG',2,'8','2017-11-18 00:00:00','2017-11-19 00:00:00');
/*!40000 ALTER TABLE `ubicaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'nupres_dev_demo01'
--
/*!50003 DROP FUNCTION IF EXISTS `fxCubrimientoMetaCalorica` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fxCubrimientoMetaCalorica`(`aporte_kilocalorias_24h` INT, `meta_calorica` INT) RETURNS decimal(10,0)
BEGIN

	/* Calcula el cubrimiento porcentual de la meta calorica del paciente */ 

    DECLARE REF1 INT DEFAULT 100;

RETURN ((`aporte_kilocalorias_24h` * REF1) / `meta_calorica`);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fxCubrimientoMetaVolumen` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fxCubrimientoMetaVolumen`(`meta_volumen` INT, `volumen_infundido_24h` INT) RETURNS decimal(10,0)
BEGIN

	/* Calcula el cubrimiento porcentual de la meta calorica del paciente */ 

    DECLARE REF1 INT DEFAULT 100;

RETURN ((`volumen_infundido_24h` * REF1) / `meta_volumen`);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fxGastoEnergeticoBasal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fxGastoEnergeticoBasal`(`peso_actual` DECIMAL, `talla` DECIMAL, `edad` INT) RETURNS decimal(10,0)
BEGIN

	/* Calcula el gasto energetico basal del paciente*/ 

	DECLARE REF1 INT DEFAULT 10;
    DECLARE REF2 DECIMAL DEFAULT 6.25;
    DECLARE REF3 INT DEFAULT 100;
    DECLARE REF4 INT DEFAULT 5;
    DECLARE REF5 INT DEFAULT 5;

RETURN (REF1 * `peso_actual`) + (REF2 * (REF3 * `talla`)) - (REF4 * `edad`) + REF5;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fxGramosProteinaKgPeso` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fxGramosProteinaKgPeso`(`gramos_proteina_diaria` INT, `peso_actual` DECIMAL) RETURNS decimal(10,0)
BEGIN

/* Calcula los calcula los gramos de proteina por kg de peso del paciente*/ 

RETURN (`gramos_proteina_diaria` / `peso_actual`);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fxIndiceMasaCorporal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fxIndiceMasaCorporal`(`peso_actual` DECIMAL, `talla` DECIMAL) RETURNS decimal(10,0)
BEGIN

/* Calcula el indice de masa corporal del paciente*/ 

RETURN (`peso_actual` / (`talla` * `talla`));
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fxKcKgAplicadas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fxKcKgAplicadas`(`kilocalorias_kilogramo_peso` INT, `aporte_kilocalorias_24h` INT) RETURNS int(11)
BEGIN

/* Calcula la kilocalorias aplicadas por kilogramo del paciente*/ 

RETURN (`kilocalorias_kilogramo_peso` / `kilocalorias_kilogramo_peso`);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fxMetaCalorica` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fxMetaCalorica`(`kilocalorias_kilogramo_peso` INT, `peso` DECIMAL) RETURNS int(11)
    DETERMINISTIC
BEGIN

/* Calcula la meta calorica del paciente*/ 

RETURN (`kilocalorias_kilogramo_peso` * `peso`);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `informe_cuidado_critico_general`
--

/*!50001 DROP VIEW IF EXISTS `informe_cuidado_critico_general`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `informe_cuidado_critico_general` AS select `ingresos`.`id` AS `ingreso_cod`,`ingresos`.`paciente_id` AS `paciente_doc`,`pacientes`.`nombres` AS `paciente_nombres`,`pacientes`.`apellidos` AS `paciente_apellidos`,`ingresos`.`fecha_ingreso` AS `ingreso`,(to_days(now()) - to_days(`ingresos`.`fecha_ingreso`)) AS `dias_ingreso`,`ingresos`.`fecha_mipres` AS `fecha_mipres`,(to_days(now()) - to_days(`ingresos`.`fecha_mipres`)) AS `dias_mipres`,`ingresos`.`media_envergadura` AS `media_envergadura`,`ingresos`.`altura_rodilla` AS `altura_rodilla`,`ingresos`.`fecha_egreso` AS `fecha_egreso`,`ingresos`.`observaciones` AS `observaciones`,`ingresos`.`estado` AS `estado`,`ingresos`.`seguimiento` AS `seguimiento`,`ref-egresos`.`nombre` AS `motivo_egreso`,`evoluciones`.`id` AS `evolucion_id`,`ref-ubicaciones`.`nombre` AS `ubicacion`,`ubicaciones`.`cama` AS `cama`,`pacientes`.`fecha_nacimiento` AS `nacimiento`,(year(now()) - year(`pacientes`.`fecha_nacimiento`)) AS `edad`,`pacientes`.`genero` AS `sexo`,`ingresos`.`eps` AS `eps`,`ref-manejos`.`nombre` AS `manejo`,`ref-formulas`.`nombre` AS `formula`,`evoluciones`.`peso_actual` AS `peso`,`ingresos`.`talla` AS `talla`,`fxGastoEnergeticoBasal`(`evoluciones`.`peso_actual`,`ingresos`.`talla`,(year(now()) - year(`pacientes`.`fecha_nacimiento`))) AS `gasto_energetico_basal`,`fxIndiceMasaCorporal`(`evoluciones`.`peso_actual`,`ingresos`.`talla`) AS `indice_masa_corporal`,`evoluciones`.`kilocalorias_kilogramo_peso` AS `kilocalorias_kilogramo_peso`,`fxMetaCalorica`(`evoluciones`.`kilocalorias_kilogramo_peso`,`evoluciones`.`peso_actual`) AS `meta_calorica`,`evoluciones`.`meta_volumen` AS `meta_volumen`,`evoluciones`.`gramos_proteina_diaria` AS `gramos_proteina_diaria`,`fxGramosProteinaKgPeso`(`evoluciones`.`gramos_proteina_diaria`,`evoluciones`.`peso_actual`) AS `gramos_proteina_kg_peso`,`fxCubrimientoMetaCalorica`(`evoluciones`.`gramos_proteina_diaria`,`fxMetaCalorica`(`evoluciones`.`kilocalorias_kilogramo_peso`,`evoluciones`.`peso_actual`)) AS `cumplimiento_meta_calorica`,`evoluciones`.`volumen_infundido` AS `volumen_infundido`,`fxCubrimientoMetaVolumen`(`evoluciones`.`meta_volumen`,`evoluciones`.`volumen_infundido`) AS `cumplimiento_meta_volumen` from (((((((`ingresos` join `pacientes`) join `evoluciones`) join `ref-ubicaciones`) join `ubicaciones`) join `ref-manejos`) join `ref-formulas`) join `ref-egresos`) where ((`ingresos`.`paciente_id` = `pacientes`.`id`) and (`evoluciones`.`ingreso_id` = `ingresos`.`id`) and (`ubicaciones`.`evolucion_id` = `evoluciones`.`id`) and (`ref-ubicaciones`.`id` = `ubicaciones`.`refubicacion_id`) and (`ref-manejos`.`id` = `evoluciones`.`manejo_id`) and (`ref-formulas`.`id` = `evoluciones`.`formula_id`) and (`ref-egresos`.`id` = `ingresos`.`motivo_egreso`) and (`ingresos`.`id` > 0)) order by `evoluciones`.`fecha_evolucion` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-19 18:30:13
