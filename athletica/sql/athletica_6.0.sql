-- phpMyAdmin SQL Dump
-- version 3.2.2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Erstellungszeit: 25. Juni 2012 um 14:40
-- Server Version: 5.1.26
-- PHP-Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `athletica`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `anlage`
--

DROP TABLE IF EXISTS `anlage`;
CREATE TABLE IF NOT EXISTS `anlage` (
  `xAnlage` int(11) NOT NULL AUTO_INCREMENT,
  `Bezeichnung` varchar(20) NOT NULL DEFAULT '',
  `Homologiert` enum('y','n') NOT NULL DEFAULT 'y',
  `xStadion` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xAnlage`),
  KEY `xStadion` (`xStadion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `anlage`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `anmeldung`
--

DROP TABLE IF EXISTS `anmeldung`;
CREATE TABLE IF NOT EXISTS `anmeldung` (
  `xAnmeldung` int(11) NOT NULL AUTO_INCREMENT,
  `Startnummer` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Erstserie` enum('y','n') NOT NULL DEFAULT 'n',
  `Bezahlt` enum('y','n') NOT NULL DEFAULT 'y',
  `Gruppe` char(2) NOT NULL DEFAULT '',
  `BestleistungMK` float NOT NULL DEFAULT '0',
  `Vereinsinfo` varchar(150) NOT NULL DEFAULT '',
  `xAthlet` int(11) NOT NULL DEFAULT '0',
  `xMeeting` int(11) NOT NULL DEFAULT '0',
  `xKategorie` int(11) DEFAULT NULL,
  `xTeam` int(11) NOT NULL DEFAULT '0',
  `BaseEffortMK` enum('y','n') NOT NULL DEFAULT 'n',
  `Anmeldenr_ZLV` int(11) DEFAULT '0',
  `KidID` int(11) DEFAULT '0',
  `Angemeldet` enum('y','n') DEFAULT 'n',
  `VorjahrLeistungMK` int(11) DEFAULT '0',
  PRIMARY KEY (`xAnmeldung`),
  UNIQUE KEY `AthleteMeetingKat` (`xAthlet`,`xMeeting`,`xKategorie`),
  KEY `xAthlet` (`xAthlet`),
  KEY `xMeeting` (`xMeeting`),
  KEY `xKategorie` (`xKategorie`),
  KEY `Startnummer` (`Startnummer`),
  KEY `xTeam` (`xTeam`),
  KEY `Vereinsinfo` (`Vereinsinfo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `anmeldung`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `athlet`
--

DROP TABLE IF EXISTS `athlet`;
CREATE TABLE IF NOT EXISTS `athlet` (
  `xAthlet` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL DEFAULT '',
  `Vorname` varchar(50) NOT NULL DEFAULT '',
  `Jahrgang` year(4) DEFAULT NULL,
  `xVerein` int(11) NOT NULL DEFAULT '0',
  `xVerein2` int(11) NOT NULL DEFAULT '0',
  `Lizenznummer` int(11) NOT NULL DEFAULT '0',
  `Geschlecht` enum('m','w') NOT NULL DEFAULT 'm',
  `Land` char(3) NOT NULL DEFAULT '',
  `Geburtstag` date NOT NULL DEFAULT '0000-00-00',
  `Athleticagen` enum('y','n') NOT NULL DEFAULT 'n',
  `Bezahlt` enum('y','n') NOT NULL DEFAULT 'n',
  `xRegion` int(11) NOT NULL DEFAULT '0',
  `Lizenztyp` tinyint(2) NOT NULL DEFAULT '0',
  `Manuell` int(1) NOT NULL DEFAULT '0',
  `Adresse` varchar(50) DEFAULT NULL,
  `Plz` int(6) DEFAULT '0',
  `Ort` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`xAthlet`),
  UNIQUE KEY `Athlet` (`Name`,`Vorname`,`Geburtstag`,`xVerein`),
  KEY `Name` (`Name`),
  KEY `xVerein` (`xVerein`),
  KEY `Lizenznummer` (`Lizenznummer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `athlet`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `base_account`
--

DROP TABLE IF EXISTS `base_account`;
CREATE TABLE IF NOT EXISTS `base_account` (
  `account_code` varchar(30) NOT NULL DEFAULT '',
  `account_name` varchar(255) NOT NULL DEFAULT '',
  `account_short` varchar(255) NOT NULL DEFAULT '',
  `account_type` varchar(100) NOT NULL DEFAULT '',
  `lg` varchar(100) NOT NULL DEFAULT '',
  KEY `account_code` (`account_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `base_account`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `base_athlete`
--

DROP TABLE IF EXISTS `base_athlete`;
CREATE TABLE IF NOT EXISTS `base_athlete` (
  `id_athlete` int(11) NOT NULL AUTO_INCREMENT,
  `license` int(11) NOT NULL DEFAULT '0',
  `license_paid` enum('y','n') NOT NULL DEFAULT 'y',
  `license_cat` varchar(4) NOT NULL DEFAULT '',
  `lastname` varchar(100) NOT NULL DEFAULT '',
  `firstname` varchar(100) NOT NULL DEFAULT '',
  `sex` enum('m','w') NOT NULL DEFAULT 'm',
  `nationality` char(3) NOT NULL DEFAULT '',
  `account_code` varchar(30) NOT NULL DEFAULT '',
  `second_account_code` varchar(30) NOT NULL DEFAULT '',
  `birth_date` date NOT NULL DEFAULT '0000-00-00',
  `account_info` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_athlete`),
  KEY `account_code` (`account_code`),
  KEY `second_account_code` (`second_account_code`),
  KEY `license` (`license`),
  KEY `lastname` (`lastname`),
  KEY `firstname` (`firstname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `base_athlete`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `base_log`
--

DROP TABLE IF EXISTS `base_log`;
CREATE TABLE IF NOT EXISTS `base_log` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT '',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `global_last_change` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_log`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `base_log`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `base_performance`
--

DROP TABLE IF EXISTS `base_performance`;
CREATE TABLE IF NOT EXISTS `base_performance` (
  `id_performance` int(11) NOT NULL AUTO_INCREMENT,
  `id_athlete` int(11) NOT NULL DEFAULT '0',
  `discipline` smallint(6) NOT NULL DEFAULT '0',
  `category` varchar(10) NOT NULL DEFAULT '',
  `best_effort` varchar(15) NOT NULL DEFAULT '',
  `best_effort_date` date NOT NULL DEFAULT '0000-00-00',
  `best_effort_event` varchar(100) NOT NULL DEFAULT '',
  `season_effort` varchar(15) NOT NULL DEFAULT '',
  `season_effort_date` date NOT NULL DEFAULT '0000-00-00',
  `season_effort_event` varchar(100) NOT NULL DEFAULT '',
  `notification_effort` varchar(15) NOT NULL DEFAULT '',
  `notification_effort_date` date NOT NULL DEFAULT '0000-00-00',
  `notification_effort_event` varchar(100) NOT NULL DEFAULT '',
  `season` enum('I','O') NOT NULL DEFAULT 'O',
  PRIMARY KEY (`id_performance`),
  UNIQUE KEY `id_athlete_discipline_season` (`id_athlete`,`discipline`,`season`),
  KEY `id_athlete` (`id_athlete`),
  KEY `discipline` (`discipline`),
  KEY `season` (`season`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `base_performance`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `base_relay`
--

DROP TABLE IF EXISTS `base_relay`;
CREATE TABLE IF NOT EXISTS `base_relay` (
  `id_relay` int(11) NOT NULL DEFAULT '0',
  `is_athletica_gen` enum('y','n') NOT NULL DEFAULT 'y',
  `relay_name` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(10) NOT NULL DEFAULT '',
  `discipline` varchar(10) NOT NULL DEFAULT '',
  `account_code` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_relay`),
  KEY `account_code` (`account_code`),
  KEY `discipline` (`discipline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `base_relay`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `base_svm`
--

DROP TABLE IF EXISTS `base_svm`;
CREATE TABLE IF NOT EXISTS `base_svm` (
  `id_svm` int(11) NOT NULL DEFAULT '0',
  `is_athletica_gen` enum('y','n') NOT NULL DEFAULT 'y',
  `svm_name` varchar(255) NOT NULL DEFAULT '',
  `svm_category` varchar(10) NOT NULL DEFAULT '',
  `account_code` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_svm`),
  KEY `account_code` (`account_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `base_svm`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `disziplin_de`
--

DROP TABLE IF EXISTS `disziplin_de`;
CREATE TABLE IF NOT EXISTS `disziplin_de` (
  `xDisziplin` int(11) NOT NULL AUTO_INCREMENT,
  `Kurzname` varchar(15) NOT NULL DEFAULT '',
  `Name` varchar(40) NOT NULL DEFAULT '',
  `Anzeige` int(11) NOT NULL DEFAULT '1',
  `Seriegroesse` int(4) NOT NULL DEFAULT '0',
  `Staffellaeufer` int(11) DEFAULT NULL,
  `Typ` int(11) NOT NULL DEFAULT '0',
  `Appellzeit` time NOT NULL DEFAULT '00:00:00',
  `Stellzeit` time NOT NULL DEFAULT '00:00:00',
  `Strecke` float NOT NULL DEFAULT '0',
  `Code` int(11) NOT NULL DEFAULT '0',
  `xOMEGA_Typ` int(11) NOT NULL DEFAULT '0',
  `aktiv` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`xDisziplin`),
  UNIQUE KEY `Kurzname` (`Kurzname`),
  KEY `Anzeige` (`Anzeige`),
  KEY `Staffel` (`Staffellaeufer`),
  KEY `Code` (`Code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=203 ;

--
-- Daten für Tabelle `disziplin_de`
--

INSERT INTO `disziplin_de` (`xDisziplin`, `Kurzname`, `Name`, `Anzeige`, `Seriegroesse`, `Staffellaeufer`, `Typ`, `Appellzeit`, `Stellzeit`, `Strecke`, `Code`, `xOMEGA_Typ`, `aktiv`) VALUES
(38, '50', '50 m', 10, 6, 0, 2, '01:00:00', '00:15:00', 50, 10, 1, 'y'),
(39, '55', '55 m', 20, 6, 0, 2, '01:00:00', '00:15:00', 55, 20, 1, 'y'),
(40, '60', '60 m', 30, 6, 0, 2, '01:00:00', '00:15:00', 60, 30, 1, 'y'),
(41, '80', '80 m', 35, 6, 0, 1, '01:00:00', '00:15:00', 80, 35, 1, 'y'),
(42, '100', '100 m', 40, 6, 0, 1, '01:00:00', '00:15:00', 100, 40, 1, 'y'),
(43, '150', '150 m', 48, 6, 0, 1, '01:00:00', '00:15:00', 150, 48, 1, 'y'),
(44, '200', '200 m', 50, 6, 0, 1, '01:00:00', '00:15:00', 200, 50, 1, 'y'),
(45, '300', '300 m', 60, 6, 0, 2, '01:00:00', '00:15:00', 300, 60, 1, 'y'),
(46, '400', '400 m', 70, 6, 0, 2, '01:00:00', '00:15:00', 400, 70, 1, 'y'),
(47, '600', '600 m', 80, 12, 0, 7, '01:00:00', '00:15:00', 600, 80, 1, 'y'),
(48, '800', '800 m', 90, 6, 0, 7, '01:00:00', '00:15:00', 800, 90, 1, 'y'),
(49, '1000', '1000 m', 100, 15, 0, 7, '01:00:00', '00:15:00', 1000, 100, 1, 'y'),
(50, '1500', '1500 m', 110, 12, 0, 7, '01:00:00', '00:15:00', 1500, 110, 1, 'y'),
(51, '1MEILE', '1 Meile', 120, 15, 0, 7, '01:00:00', '00:15:00', 1609, 120, 1, 'y'),
(52, '2000', '2000 m', 130, 15, 0, 7, '01:00:00', '00:15:00', 2000, 130, 1, 'y'),
(53, '3000', '3000 m', 140, 15, 0, 7, '01:00:00', '00:15:00', 3000, 140, 1, 'y'),
(54, '5000', '5000 m', 160, 15, 0, 7, '01:00:00', '00:15:00', 5000, 160, 1, 'y'),
(55, '10000', '10 000 m', 170, 20, 0, 7, '01:00:00', '00:15:00', 10000, 170, 1, 'y'),
(56, '20000', '20 000 m', 180, 20, 0, 7, '01:00:00', '00:15:00', 20000, 180, 1, 'y'),
(57, '1STUNDE', '1 Stunde', 171, 20, 0, 7, '01:00:00', '00:15:00', 1, 182, 1, 'y'),
(58, '25000', '25 000 m', 181, 20, 0, 7, '01:00:00', '00:15:00', 25000, 181, 1, 'y'),
(59, '30000', '30 000 m', 182, 20, 0, 7, '01:00:00', '00:15:00', 30000, 195, 1, 'y'),
(61, 'HALBMARATH', 'Halbmarathon', 183, 20, 0, 7, '01:00:00', '00:15:00', 0, 190, 1, 'y'),
(62, 'MARATHON', 'Marathon', 184, 20, 0, 7, '01:00:00', '00:15:00', 0, 200, 1, 'y'),
(64, '50H106.7', '50 m Hürden 106.7', 232, 6, 0, 1, '01:00:00', '00:15:00', 50, 232, 4, 'y'),
(65, '50H99.1', '50 m Hürden 99.1', 233, 6, 0, 2, '01:00:00', '00:15:00', 50, 233, 4, 'y'),
(66, '50H91.4', '50 m Hürden 91.4', 234, 6, 0, 2, '01:00:00', '00:15:00', 50, 234, 4, 'y'),
(67, '50H84.0', '50 m Hürden 84.0', 235, 6, 0, 2, '01:00:00', '00:15:00', 50, 235, 4, 'y'),
(68, '50H76.2', '50 m Hürden 76.2  U18 W', 236, 6, 0, 2, '01:00:00', '00:15:00', 50, 236, 4, 'y'),
(69, '60H106.7', '60 m Hürden 106.7', 241, 6, 0, 2, '01:00:00', '00:15:00', 60, 252, 4, 'y'),
(70, '60H99.1', '60 m Hürden 99.1', 242, 6, 0, 2, '01:00:00', '00:15:00', 60, 253, 4, 'y'),
(71, '60H91.4', '60 m Hürden 91.4', 243, 6, 0, 2, '01:00:00', '00:15:00', 60, 254, 4, 'y'),
(72, '60H84.0', '60 m Hürden 84.0', 244, 6, 0, 2, '01:00:00', '00:15:00', 60, 255, 4, 'y'),
(73, '60H76.2', '60 m Hürden 76.2  U18 W', 245, 6, 0, 2, '01:00:00', '00:15:00', 60, 256, 4, 'y'),
(74, '80H76.2', '80 m Hürden 76.2', 264, 6, 0, 1, '01:00:00', '00:15:00', 80, 258, 4, 'y'),
(75, '100H84.0', '100 m Hürden 84.0', 266, 6, 0, 1, '01:00:00', '00:15:00', 100, 261, 4, 'y'),
(76, '100H76.2', '100 m Hürden 76.2', 267, 6, 0, 1, '01:00:00', '00:15:00', 100, 259, 4, 'y'),
(77, '110H106.7', '110 m Hürden 106.7', 268, 6, 0, 1, '01:00:00', '00:15:00', 110, 271, 4, 'y'),
(78, '110H99.1', '110 m Hürden 99.1', 269, 6, 0, 1, '01:00:00', '00:15:00', 110, 269, 4, 'y'),
(79, '110H91.4', '110 m Hürden 91.4', 270, 6, 0, 1, '01:00:00', '00:15:00', 110, 268, 4, 'y'),
(80, '200H', '200 m Hürden', 280, 6, 0, 1, '01:00:00', '00:15:00', 200, 280, 4, 'y'),
(81, '300H84.0', '300 m Hürden 84.0', 290, 6, 0, 2, '01:00:00', '00:15:00', 300, 290, 4, 'y'),
(82, '300H76.2', '300 m Hürden 76.2', 291, 6, 0, 2, '01:00:00', '00:15:00', 300, 291, 4, 'y'),
(83, '400H91.4', '400 m Hürden 91.4', 298, 6, 0, 2, '01:00:00', '00:15:00', 400, 301, 4, 'y'),
(84, '400H76.2', '400 m Hürden 76.2', 301, 6, 0, 2, '01:00:00', '00:15:00', 400, 298, 4, 'y'),
(85, '1500ST', '1500 m Steeple', 302, 6, 0, 7, '01:00:00', '00:15:00', 1500, 209, 6, 'y'),
(86, '2000ST', '2000 m Steeple', 303, 6, 0, 7, '01:00:00', '00:15:00', 2000, 210, 6, 'y'),
(87, '3000ST', '3000 m Steeple', 304, 6, 0, 7, '01:00:00', '00:15:00', 3000, 220, 6, 'y'),
(88, '5XFREI', '5x frei', 395, 6, 5, 3, '01:00:00', '00:15:00', 5, 497, 1, 'y'),
(89, '5X80', '5x80 m', 396, 6, 5, 3, '01:00:00', '00:15:00', 400, 498, 1, 'y'),
(90, '6XFREI', '6x frei', 394, 6, 6, 3, '01:00:00', '00:15:00', 6, 499, 1, 'y'),
(91, '4X100', '4x100 m', 397, 6, 4, 3, '01:00:00', '00:15:00', 400, 560, 1, 'y'),
(92, '4X200', '4x200 m', 398, 6, 4, 3, '01:00:00', '00:15:00', 800, 570, 1, 'y'),
(93, '4X400', '4x400 m', 399, 6, 4, 3, '01:00:00', '00:15:00', 1600, 580, 1, 'y'),
(94, '3X800', '3x800 m', 400, 6, 3, 3, '01:00:00', '00:15:00', 2400, 589, 1, 'y'),
(95, '4X800', '4x800 m', 401, 6, 4, 3, '01:00:00', '00:15:00', 3200, 590, 1, 'y'),
(96, '3X1000', '3x1000 m', 402, 6, 3, 3, '01:00:00', '00:15:00', 3000, 595, 1, 'y'),
(97, '4X1500', '4x1500 m', 403, 6, 4, 3, '01:00:00', '00:15:00', 6000, 600, 1, 'y'),
(98, 'OLYMPISCHE', 'Olympische', 404, 12, 4, 3, '01:00:00', '00:15:00', 0, 601, 1, 'y'),
(99, 'AMERICAINE', 'Américaine', 405, 12, 3, 3, '01:00:00', '00:15:00', 0, 602, 1, 'y'),
(100, 'HOCH', 'Hoch', 310, 15, 0, 6, '01:00:00', '00:20:00', 0, 310, 1, 'y'),
(101, 'STAB', 'Stab', 320, 15, 0, 6, '01:30:00', '00:40:00', 0, 320, 1, 'y'),
(102, 'WEIT', 'Weit', 330, 15, 0, 4, '01:00:00', '00:20:00', 0, 330, 1, 'y'),
(103, 'DREI', 'Drei', 340, 15, 0, 4, '01:00:00', '00:20:00', 0, 340, 1, 'y'),
(104, 'KUGEL7.26', 'Kugel 7.26 kg', 347, 15, 0, 8, '01:00:00', '00:20:00', 0, 351, 1, 'y'),
(105, 'KUGEL6.00', 'Kugel 6.00 kg', 348, 15, 0, 8, '01:00:00', '00:20:00', 0, 348, 1, 'y'),
(106, 'KUGEL5.00', 'Kugel 5.00 kg', 349, 15, 0, 8, '01:00:00', '00:20:00', 0, 347, 1, 'y'),
(107, 'KUGEL4.00', 'Kugel 4.00 kg', 350, 15, 0, 8, '01:00:00', '00:20:00', 0, 349, 1, 'y'),
(108, 'KUGEL3.00', 'Kugel 3.00 kg', 352, 15, 0, 8, '01:00:00', '00:20:00', 0, 352, 1, 'y'),
(109, 'KUGEL2.50', 'Kugel 2.50 kg', 353, 15, 0, 8, '01:00:00', '00:20:00', 0, 353, 1, 'y'),
(110, 'DISKUS2.00', 'Diskus 2.00 kg', 356, 15, 0, 8, '01:00:00', '00:20:00', 0, 361, 1, 'y'),
(111, 'DISKUS1.75', 'Diskus 1.75 kg', 357, 15, 0, 8, '01:00:00', '00:20:00', 0, 359, 1, 'y'),
(112, 'DISKUS1.50', 'Diskus 1.50 kg', 358, 15, 0, 8, '01:00:00', '00:20:00', 0, 358, 1, 'y'),
(113, 'DISKUS1.00', 'Diskus 1.00 kg', 359, 15, 0, 8, '01:00:00', '00:20:00', 0, 357, 1, 'y'),
(114, 'DISKUS0.75', 'Diskus 0.75 kg', 361, 15, 0, 8, '01:00:00', '00:20:00', 0, 356, 1, 'y'),
(115, 'HAMMER7.26', 'Hammer 7.26 kg', 375, 15, 0, 8, '01:00:00', '00:20:00', 0, 381, 1, 'y'),
(116, 'HAMMER6.00', 'Hammer 6.00 kg', 376, 15, 0, 8, '01:00:00', '00:20:00', 0, 378, 1, 'y'),
(117, 'HAMMER5.00', 'Hammer 5.00 kg', 377, 15, 0, 8, '01:00:00', '00:20:00', 0, 377, 1, 'y'),
(118, 'HAMMER4.00', 'Hammer 4.00 kg', 378, 15, 0, 8, '01:00:00', '00:20:00', 0, 376, 1, 'y'),
(119, 'HAMMER3.00', 'Hammer 3.00 kg', 381, 15, 0, 8, '01:00:00', '00:20:00', 0, 375, 1, 'y'),
(120, 'SPEER800', 'Speer 800 gr', 387, 15, 0, 8, '01:00:00', '00:20:00', 0, 391, 1, 'y'),
(121, 'SPEER700', 'Speer 700 gr', 388, 15, 0, 8, '01:00:00', '00:20:00', 0, 389, 1, 'y'),
(122, 'SPEER600', 'Speer 600 gr', 389, 15, 0, 8, '01:00:00', '00:20:00', 0, 388, 1, 'y'),
(123, 'SPEER400', 'Speer 400 gr', 391, 15, 0, 8, '01:00:00', '00:20:00', 0, 387, 1, 'y'),
(124, 'BALL200', 'Ball 200 g', 392, 15, 0, 8, '01:00:00', '00:20:00', 0, 386, 1, 'y'),
(125, '5KAMPF_W_U20W_I', 'Fünfkampf W / U20 W Indoor', 408, 6, 0, 9, '01:00:00', '00:15:00', 5, 394, 1, 'y'),
(126, '5KAMPF_U18W_I', 'Fünfkampf U18 W Indoor', 409, 6, 0, 9, '01:00:00', '00:15:00', 5, 395, 1, 'y'),
(127, '7KAMPF_M_I', 'Siebenkampf M Indoor', 413, 6, 0, 9, '01:00:00', '00:15:00', 7, 396, 1, 'y'),
(128, '7KAMPF_U20M_I', 'Siebenkampf U20 M Indoor', 414, 6, 0, 9, '01:00:00', '00:15:00', 7, 397, 1, 'y'),
(129, '7KAMPF_U18M_I', 'Siebenkampf U18 M Indoor', 415, 6, 0, 9, '01:00:00', '00:15:00', 7, 398, 1, 'y'),
(130, '10KAMPF_M', 'Zehnkampf M', 434, 6, 0, 9, '01:00:00', '00:15:00', 10, 410, 1, 'y'),
(131, '10KAMPF_U20M', 'Zehnkampf  U20 M', 435, 6, 0, 9, '01:00:00', '00:15:00', 10, 411, 1, 'y'),
(132, '10KAMPF_U18M', 'Zehnkampf   U18 M', 436, 6, 0, 9, '01:00:00', '00:15:00', 10, 412, 1, 'y'),
(133, '10KAMPF_W', 'Zehnkampf W', 437, 6, 0, 9, '01:00:00', '00:15:00', 10, 413, 1, 'y'),
(134, '7KAMPF', 'Siebenkampf', 430, 6, 0, 9, '01:00:00', '00:15:00', 7, 400, 1, 'y'),
(135, '7KAMPF_U18W', 'Siebenkampf   U18 W', 431, 6, 0, 9, '01:00:00', '00:15:00', 7, 401, 1, 'y'),
(136, '6KAMPF_U16M', 'Sechskampf  U16 M', 429, 6, 0, 9, '01:00:00', '00:15:00', 6, 402, 1, 'y'),
(137, '5KAMPF_U16W', 'Fünfkampf U16 W', 426, 6, 0, 9, '01:00:00', '00:15:00', 5, 399, 1, 'y'),
(138, 'UKC', 'UBS Kids Cup', 439, 6, 0, 9, '01:00:00', '00:15:00', 3, 408, 1, 'y'),
(139, 'MILEWALK', 'Mile walk', 450, 50, 0, 7, '01:00:00', '00:15:00', 1609, 415, 5, 'y'),
(140, '3000WALK', '3000 m walk', 452, 50, 0, 7, '01:00:00', '00:15:00', 3000, 420, 5, 'y'),
(141, '5000WALK', '5000 m walk', 453, 50, 0, 7, '01:00:00', '00:15:00', 5000, 430, 5, 'y'),
(142, '10000WALK', '10000 m walk', 454, 50, 0, 7, '01:00:00', '00:15:00', 10000, 440, 5, 'y'),
(143, '20000WALK', '20000 m walk', 455, 50, 0, 7, '01:00:00', '00:15:00', 20000, 450, 5, 'y'),
(144, '50000WALK', '50000 m walk', 456, 50, 0, 7, '01:00:00', '00:15:00', 50000, 460, 5, 'y'),
(145, '3KMWALK', '3 km walk', 470, 50, 0, 7, '01:00:00', '00:15:00', 3000, 470, 5, 'y'),
(146, '5KMWALK', '5 km walk', 480, 50, 0, 7, '01:00:00', '00:15:00', 5000, 480, 5, 'y'),
(147, '10KMWALK', '10 km walk', 490, 50, 0, 7, '01:00:00', '00:15:00', 10000, 490, 5, 'y'),
(150, '20KMWALK', '20 km walk', 500, 50, 0, 7, '01:00:00', '00:15:00', 20000, 500, 5, 'y'),
(152, '35KMWALK', '35 km walk', 530, 50, 0, 7, '01:00:00', '00:15:00', 35000, 530, 5, 'y'),
(154, '50KMWALK', '50 km walk', 550, 50, 0, 7, '01:00:00', '00:15:00', 50000, 550, 5, 'y'),
(156, '10KM', '10 km', 440, 50, 0, 7, '01:00:00', '00:15:00', 10000, 491, 1, 'y'),
(157, '15KM', '15 km', 441, 50, 0, 7, '01:00:00', '00:15:00', 15000, 494, 1, 'y'),
(158, '20KM', '20 km', 442, 50, 0, 7, '01:00:00', '00:15:00', 20000, 501, 1, 'y'),
(159, '25KM', '25 km', 443, 50, 0, 7, '01:00:00', '00:15:00', 25000, 505, 1, 'y'),
(160, '30KM', '30 km', 444, 50, 0, 7, '01:00:00', '00:15:00', 30000, 511, 1, 'y'),
(162, '1HWALK', '1 h  walk', 555, 50, 0, 7, '01:00:00', '00:15:00', 1, 555, 5, 'y'),
(163, '2HWALK', '2 h  walk', 556, 50, 0, 7, '01:00:00', '00:15:00', 2, 556, 5, 'y'),
(164, '100KMWALK', '100 km walk', 457, 50, 0, 7, '01:00:00', '00:15:00', 100000, 559, 5, 'y'),
(165, 'BALL80', 'Ball 80 g', 393, 15, 0, 8, '01:00:00', '00:20:00', 0, 385, 1, 'y'),
(166, '300H91.4', '300 m Hürden 91.4', 289, 6, 0, 2, '01:00:00', '00:15:00', 300, 289, 4, 'y'),
(167, '...KAMPF', '...kampf', 799, 6, 0, 9, '01:00:00', '00:15:00', 4, 799, 1, 'y'),
(168, '75', '75 m', 31, 6, 0, 1, '01:00:00', '00:15:00', 75, 31, 1, 'y'),
(169, '50H68.6', '50 m Hürden 68.6', 240, 6, 0, 2, '01:00:00', '00:15:00', 50, 237, 1, 'y'),
(170, '60H68.6', '60 m Hürden 68.6', 252, 6, 0, 2, '01:00:00', '00:15:00', 60, 257, 1, 'y'),
(171, '80H84.0', '80 m Hürden 84.0', 263, 6, 0, 1, '01:00:00', '00:15:00', 80, 260, 1, 'y'),
(172, '80H68.6', '80 m Hürden 68.6', 265, 6, 0, 1, '01:00:00', '00:15:00', 80, 262, 1, 'y'),
(173, '300H68.6', '300 m Hürden 68.6', 292, 6, 0, 2, '01:00:00', '00:15:00', 300, 295, 1, 'y'),
(174, 'SPEER500', 'Speer 500 gr', 390, 15, 0, 8, '01:00:00', '00:20:00', 0, 390, 1, 'y'),
(175, '5KAMPF_M', 'Fünfkampf M', 418, 6, 0, 9, '01:00:00', '00:15:00', 5, 392, 1, 'y'),
(176, '5KAMPF_U20M', 'Fünfkampf U20 M', 420, 6, 0, 9, '01:00:00', '00:15:00', 5, 393, 1, 'y'),
(177, '5KAMPF_U18M', 'Fünfkampf U18 M', 421, 6, 0, 9, '01:00:00', '00:15:00', 5, 405, 1, 'y'),
(178, '5KAMPF_W', 'Fünfkampf W', 423, 6, 0, 9, '01:00:00', '00:15:00', 5, 416, 1, 'y'),
(180, '5KAMPF_U18W', 'Fünfkampf U18 W', 425, 6, 0, 9, '01:00:00', '00:15:00', 5, 418, 1, 'y'),
(181, '10KAMPF_MASTER', 'Zehnkampf Master', 438, 6, 0, 9, '01:00:00', '00:15:00', 10, 414, 1, 'y'),
(182, '2000WALK', '2000 m walk', 451, 50, 0, 7, '01:00:00', '00:15:00', 2000, 419, 1, 'y'),
(183, '...LAUF', '...lauf', 796, 6, 0, 9, '01:00:00', '00:15:00', 4, 796, 1, 'y'),
(184, '...SPRUNG', '...sprung', 797, 6, 0, 9, '01:00:00', '00:20:00', 4, 797, 1, 'y'),
(185, '...WURF', '...wurf', 798, 6, 0, 9, '01:00:00', '00:20:00', 4, 798, 1, 'y'),
(186, 'WEIT Z', 'Weit (Zone)', 331, 15, 0, 5, '01:00:00', '00:20:00', 0, 331, 1, 'y'),
(187, '50H76.2U16', '50 m Hürden 76.2  U16W/U14M', 237, 6, 0, 2, '01:00:00', '00:15:00', 50, 246, 4, 'y'),
(188, '50H76.2U14', '50 m Hürden 76.2  U14 W (In)', 238, 6, 0, 2, '01:00:00', '00:15:00', 50, 247, 4, 'y'),
(189, '50H60-76.2', '50 m Hürden 60-76.2 U12 (In)', 239, 6, 0, 2, '01:00:00', '00:15:00', 50, 248, 4, 'y'),
(190, '60H76.2U16', '60 m Hürden 76.2  U16W/U14M', 247, 6, 0, 2, '01:00:00', '00:15:00', 60, 275, 4, 'y'),
(191, '60H76.2U14I', '60 m Hürden 76.2  U14W (In)', 248, 6, 0, 2, '01:00:00', '00:15:00', 60, 276, 4, 'y'),
(192, '60H60-76.2', '60 m Hürden 60-76.2  U12 (In)', 250, 6, 0, 2, '01:00:00', '00:15:00', 60, 277, 4, 'y'),
(193, '60H76.2U14O', '60 m Hürden 76.2  U14 W (Out)', 251, 6, 0, 2, '01:00:00', '00:15:00', 60, 278, 4, 'y'),
(194, '60H60-76.2U12', '60 m Hürden 60-76.2 U12', 254, 6, 0, 2, '01:00:00', '00:15:00', 60, 279, 4, 'y'),
(195, '5KAMPF_U16M', 'Fünfkampf U16 M', 422, 6, 0, 9, '01:00:00', '00:15:00', 5, 406, 1, 'y'),
(196, '5KAMPF_U18M_I', 'Fünfkampf U18 M Indoor', 406, 6, 0, 9, '01:00:00', '00:15:00', 5, 424, 1, 'y'),
(197, '5KAMPF_U23M', 'Fünfkampf U23 M', 419, 6, 0, 9, '01:00:00', '00:15:00', 5, 407, 1, 'y'),
(198, '5KAMPF_U20W', 'Fünfkampf U20 W', 424, 6, 0, 9, '01:00:00', '00:15:00', 5, 417, 1, 'y'),
(199, '5KAMPF_U16M_I', 'Fünfkampf U16 M Indoor', 407, 6, 0, 9, '01:00:00', '00:15:00', 5, 425, 1, 'y'),
(200, '5KAMPF_U16W_I', 'Fünfkampf U16 W Indoor', 410, 6, 0, 9, '01:00:00', '00:15:00', 5, 426, 1, 'y'),
(201, '8KAMPF_U18M', 'Achtkampf U18 M', 433, 6, 0, 9, '01:00:00', '00:15:00', 5, 427, 1, 'y'),
(202, 'Schwedenstaffel', 'Schwedenstaffel', 404, 12, 4, 3, '01:00:00', '00:15:00', 0, 603, 1, 'y');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `disziplin_fr`
--

DROP TABLE IF EXISTS `disziplin_fr`;
CREATE TABLE IF NOT EXISTS `disziplin_fr` (
  `xDisziplin` int(11) NOT NULL AUTO_INCREMENT,
  `Kurzname` varchar(15) NOT NULL DEFAULT '',
  `Name` varchar(40) NOT NULL DEFAULT '',
  `Anzeige` int(11) NOT NULL DEFAULT '1',
  `Seriegroesse` int(4) NOT NULL DEFAULT '0',
  `Staffellaeufer` int(11) DEFAULT NULL,
  `Typ` int(11) NOT NULL DEFAULT '0',
  `Appellzeit` time NOT NULL DEFAULT '00:00:00',
  `Stellzeit` time NOT NULL DEFAULT '00:00:00',
  `Strecke` float NOT NULL DEFAULT '0',
  `Code` int(11) NOT NULL DEFAULT '0',
  `xOMEGA_Typ` int(11) NOT NULL DEFAULT '0',
  `aktiv` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`xDisziplin`),
  UNIQUE KEY `Kurzname` (`Kurzname`),
  KEY `Anzeige` (`Anzeige`),
  KEY `Staffel` (`Staffellaeufer`),
  KEY `Code` (`Code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=203 ;

--
-- Daten für Tabelle `disziplin_fr`
--

INSERT INTO `disziplin_fr` (`xDisziplin`, `Kurzname`, `Name`, `Anzeige`, `Seriegroesse`, `Staffellaeufer`, `Typ`, `Appellzeit`, `Stellzeit`, `Strecke`, `Code`, `xOMEGA_Typ`, `aktiv`) VALUES
(38, '50', '50 m', 10, 6, 0, 2, '01:00:00', '00:15:00', 50, 10, 1, 'y'),
(39, '55', '55 m', 20, 6, 0, 2, '01:00:00', '00:15:00', 55, 20, 1, 'y'),
(40, '60', '60 m', 30, 6, 0, 2, '01:00:00', '00:15:00', 60, 30, 1, 'y'),
(41, '80', '80 m', 35, 6, 0, 1, '01:00:00', '00:15:00', 80, 35, 1, 'y'),
(42, '100', '100 m', 40, 86, 0, 1, '01:00:00', '00:15:00', 100, 40, 1, 'y'),
(43, '150', '150 m', 48, 6, 0, 1, '01:00:00', '00:15:00', 150, 48, 1, 'y'),
(44, '200', '200 m', 50, 6, 0, 1, '01:00:00', '00:15:00', 200, 50, 1, 'y'),
(45, '300', '300 m', 60, 6, 0, 2, '01:00:00', '00:15:00', 300, 60, 1, 'y'),
(46, '400', '400 m', 70, 6, 0, 2, '01:00:00', '00:15:00', 400, 70, 1, 'y'),
(47, '600', '600 m', 80, 12, 0, 7, '01:00:00', '00:15:00', 600, 80, 1, 'y'),
(48, '800', '800 m', 90, 6, 0, 7, '01:00:00', '00:15:00', 800, 90, 1, 'y'),
(49, '1000', '1000 m', 100, 15, 0, 7, '01:00:00', '00:15:00', 1000, 100, 1, 'y'),
(50, '1500', '1500 m', 110, 12, 0, 7, '01:00:00', '00:15:00', 1500, 110, 1, 'y'),
(51, '1MILE', '1 mile', 120, 15, 0, 7, '01:00:00', '00:15:00', 1609, 120, 1, 'y'),
(52, '2000', '2000 m', 130, 15, 0, 7, '01:00:00', '00:15:00', 2000, 130, 1, 'y'),
(53, '3000', '3000 m', 140, 15, 0, 7, '01:00:00', '00:15:00', 3000, 140, 1, 'y'),
(54, '5000', '5000 m', 160, 15, 0, 7, '01:00:00', '00:15:00', 5000, 160, 1, 'y'),
(55, '10000', '10 000 m', 170, 20, 0, 7, '01:00:00', '00:15:00', 10000, 170, 1, 'y'),
(56, '20000', '20 000 m', 180, 20, 0, 7, '01:00:00', '00:15:00', 20000, 180, 1, 'y'),
(57, '1HEURE', '1 heure', 171, 620, 0, 7, '01:00:00', '00:15:00', 1, 182, 1, 'y'),
(58, '25000', '25 000 m', 181, 20, 0, 7, '01:00:00', '00:15:00', 25000, 181, 1, 'y'),
(59, '30000', '30 000 m', 182, 20, 0, 7, '01:00:00', '00:15:00', 30000, 195, 1, 'y'),
(61, 'DEMIMARATHON', 'Demimarathon', 183, 20, 0, 7, '01:00:00', '00:15:00', 0, 190, 1, 'y'),
(62, 'MARATHON', 'Marathon', 184, 20, 0, 7, '01:00:00', '00:15:00', 0, 200, 1, 'y'),
(64, '50H106.7', '50 m haies 106.7', 232, 6, 0, 1, '01:00:00', '00:15:00', 50, 232, 4, 'y'),
(65, '50H99.1', '50 m haies 99.1', 233, 6, 0, 2, '01:00:00', '00:15:00', 50, 233, 4, 'y'),
(66, '50H91.4', '50 m haies 91.4', 234, 6, 0, 2, '01:00:00', '00:15:00', 50, 234, 4, 'y'),
(67, '50H84.0', '50 m haies 84.0', 235, 6, 0, 2, '01:00:00', '00:15:00', 50, 235, 4, 'y'),
(68, '50H76.2', '50 m haies 76.2  U18 W', 236, 6, 0, 2, '01:00:00', '00:15:00', 50, 236, 4, 'y'),
(69, '60H106.7', '60 m haies 106.7', 241, 6, 0, 2, '01:00:00', '00:15:00', 60, 252, 4, 'y'),
(70, '60H99.1', '60 m haies 99.1', 242, 6, 0, 2, '01:00:00', '00:15:00', 60, 253, 4, 'y'),
(71, '60H91.4', '60 m haies 91.4', 243, 6, 0, 2, '01:00:00', '00:15:00', 60, 254, 4, 'y'),
(72, '60H84.0', '60 m haies 84.0', 244, 6, 0, 2, '01:00:00', '00:15:00', 60, 255, 4, 'y'),
(73, '60H76.2', '60 m haies 76.2  U18 W', 245, 6, 0, 2, '01:00:00', '00:15:00', 60, 256, 4, 'y'),
(74, '80H76.2', '80 m haies 76.2', 264, 6, 0, 1, '01:00:00', '00:15:00', 80, 258, 4, 'y'),
(75, '100H84.0', '100 m haies 84.0', 266, 6, 0, 1, '01:00:00', '00:15:00', 100, 261, 4, 'y'),
(76, '100H76.2', '100 m haies 76.2', 267, 6, 0, 1, '01:00:00', '00:15:00', 100, 259, 4, 'y'),
(77, '110H106.7', '110 m haies 106.7', 268, 6, 0, 1, '01:00:00', '00:15:00', 110, 271, 4, 'y'),
(78, '110H99.1', '110 m haies 99.1', 269, 6, 0, 1, '01:00:00', '00:15:00', 110, 269, 4, 'y'),
(79, '110H91.4', '110 m haies 91.4', 270, 6, 0, 1, '01:00:00', '00:15:00', 110, 268, 4, 'y'),
(80, '200H', '200 m haies', 280, 6, 0, 1, '01:00:00', '00:15:00', 200, 280, 4, 'y'),
(81, '300H84.0', '300 m haies 84.0', 290, 6, 0, 2, '01:00:00', '00:15:00', 300, 290, 4, 'y'),
(82, '300H76.2', '300 m haies 76.2', 291, 6, 0, 2, '01:00:00', '00:15:00', 300, 291, 4, 'y'),
(83, '400H91.4', '400 m haies 91.4', 298, 6, 0, 2, '01:00:00', '00:15:00', 400, 301, 4, 'y'),
(84, '400H76.2', '400 m haies 76.2', 301, 6, 0, 2, '01:00:00', '00:15:00', 400, 298, 4, 'y'),
(85, '1500ST', '1500 m Steeple', 302, 6, 0, 7, '01:00:00', '00:15:00', 1500, 209, 6, 'y'),
(86, '2000ST', '2000 m Steeple', 303, 6, 0, 7, '01:00:00', '00:15:00', 2000, 210, 6, 'y'),
(87, '3000ST', '3000 m Steeple', 304, 6, 0, 7, '01:00:00', '00:15:00', 3000, 220, 6, 'y'),
(88, '5XLIBRE', '5x libre', 395, 6, 5, 3, '01:00:00', '00:15:00', 5, 497, 1, 'y'),
(89, '5X80', '5x80 m', 396, 6, 5, 3, '01:00:00', '00:15:00', 400, 498, 1, 'y'),
(90, '6XLIBRE', '6x libre', 394, 6, 6, 3, '01:00:00', '00:15:00', 6, 499, 1, 'y'),
(91, '4X100', '4x100 m', 397, 6, 4, 3, '01:00:00', '00:15:00', 400, 560, 1, 'y'),
(92, '4X200', '4x200 m', 398, 6, 4, 3, '01:00:00', '00:15:00', 800, 570, 1, 'y'),
(93, '4X400', '4x400 m', 399, 6, 4, 3, '01:00:00', '00:15:00', 1600, 580, 1, 'y'),
(94, '3X800', '3x800 m', 400, 6, 3, 3, '01:00:00', '00:15:00', 2400, 589, 1, 'y'),
(95, '4X800', '4x800 m', 401, 6, 4, 3, '01:00:00', '00:15:00', 3200, 590, 1, 'y'),
(96, '3X1000', '3x1000 m', 402, 6, 3, 3, '01:00:00', '00:15:00', 3000, 595, 1, 'y'),
(97, '4X1500', '4x1500 m', 403, 6, 4, 3, '01:00:00', '00:15:00', 6000, 600, 1, 'y'),
(98, 'OLYMPISCHE', 'Olympische', 404, 12, 4, 3, '01:00:00', '00:15:00', 0, 601, 1, 'y'),
(99, 'AMERICAINE', 'Américaine', 405, 12, 3, 3, '01:00:00', '00:15:00', 0, 602, 1, 'y'),
(100, 'HAUTEUR', 'Hauteur', 310, 15, 0, 6, '01:00:00', '00:20:00', 0, 310, 1, 'y'),
(101, 'PERCHE', 'Perche', 320, 15, 0, 6, '01:30:00', '00:40:00', 0, 320, 1, 'y'),
(102, 'LONGUEUR', 'Longueur', 330, 15, 0, 4, '01:00:00', '00:20:00', 0, 330, 1, 'y'),
(103, 'TRIPLE', 'Triple', 340, 15, 0, 4, '01:00:00', '00:20:00', 0, 340, 1, 'y'),
(104, 'POIDS7.26', 'Poids 7.26 kg', 347, 15, 0, 8, '01:00:00', '00:20:00', 0, 351, 1, 'y'),
(105, 'POIDS6.00', 'Poids 6.00 kg', 348, 15, 0, 8, '01:00:00', '00:20:00', 0, 348, 1, 'y'),
(106, 'POIDS5.00', 'Poids 5.00 kg', 349, 15, 0, 8, '01:00:00', '00:20:00', 0, 347, 1, 'y'),
(107, 'POIDS4.00', 'Poids 4.00 kg', 350, 15, 0, 8, '01:00:00', '00:20:00', 0, 349, 1, 'y'),
(108, 'POIDS3.00', 'Poids 3.00 kg', 352, 15, 0, 8, '01:00:00', '00:20:00', 0, 352, 1, 'y'),
(109, 'POIDS2.50', 'Poids 2.50 kg', 353, 15, 0, 8, '01:00:00', '00:20:00', 0, 353, 1, 'y'),
(110, 'DISQUE2.00', 'Disque 2.00 kg', 356, 615, 0, 8, '01:00:00', '00:20:00', 0, 361, 1, 'y'),
(111, 'DISQUE1.75', 'Disque 1.75 kg', 357, 15, 0, 8, '01:00:00', '00:20:00', 0, 359, 1, 'y'),
(112, 'DISQUE1.50', 'Disque 1.50 kg', 358, 15, 0, 8, '01:00:00', '00:20:00', 0, 358, 1, 'y'),
(113, 'DISQUE1.00', 'Disque 1.00 kg', 359, 15, 0, 8, '01:00:00', '00:20:00', 0, 357, 1, 'y'),
(114, 'DISQUE0.75', 'Disque 0.75 kg', 361, 15, 0, 8, '01:00:00', '00:20:00', 0, 356, 1, 'y'),
(115, ' MARTEAU7.26', 'Marteau 7.26 kg', 375, 15, 0, 8, '01:00:00', '00:20:00', 0, 381, 1, 'y'),
(116, ' MARTEAU6.00', 'Marteau 6.00 kg', 376, 15, 0, 8, '01:00:00', '00:20:00', 0, 378, 1, 'y'),
(117, ' MARTEAU5.00', 'Marteau 5.00 kg', 377, 15, 0, 8, '01:00:00', '00:20:00', 0, 377, 1, 'y'),
(118, ' MARTEAU4.00', 'Marteau 4.00 kg', 378, 15, 0, 8, '01:00:00', '00:20:00', 0, 376, 1, 'y'),
(119, ' MARTEAU3.00', 'Marteau 3.00 kg', 381, 15, 0, 8, '01:00:00', '00:20:00', 0, 375, 1, 'y'),
(120, 'JAVELOT800', 'Javelot 800 gr', 387, 15, 0, 8, '01:00:00', '00:20:00', 0, 391, 1, 'y'),
(121, 'JAVELOT700', 'Javelot 700 gr', 388, 15, 0, 8, '01:00:00', '00:20:00', 0, 389, 1, 'y'),
(122, 'JAVELOT600', 'Javelot 600 gr', 389, 15, 0, 8, '01:00:00', '00:20:00', 0, 388, 1, 'y'),
(123, 'JAVELOT400', 'Javelot 400 gr', 391, 15, 0, 8, '01:00:00', '00:20:00', 0, 387, 1, 'y'),
(124, 'BALLE200', 'Balle 200 gr', 392, 15, 0, 8, '01:00:00', '00:20:00', 0, 386, 1, 'y'),
(125, '5ATHLON_W_U20WI', 'Pentathlon W / U20 W Indoor', 408, 6, 0, 9, '01:00:00', '00:15:00', 5, 394, 1, 'y'),
(126, '5ATHLON_U18W_I', 'Pentathlon U18 W Indoor', 409, 6, 0, 9, '01:00:00', '00:15:00', 5, 395, 1, 'y'),
(127, '7ATHLON_M_I', 'Heptathlon M Indoor', 413, 6, 0, 9, '01:00:00', '00:15:00', 7, 396, 1, 'y'),
(128, '7ATHLON_U20M_I', 'Heptathlon U20 M Indoor', 414, 6, 0, 9, '01:00:00', '00:15:00', 7, 397, 1, 'y'),
(129, '7ATHLON_U18M_I', 'Heptathlon U18 M Indoor', 415, 6, 0, 9, '01:00:00', '00:15:00', 7, 398, 1, 'y'),
(130, '10ATHLON_M', 'Décathlon M', 434, 6, 0, 9, '01:00:00', '00:15:00', 10, 410, 1, 'y'),
(131, '10ATHLON_U20M', 'Décathlon U20 M', 435, 6, 0, 9, '01:00:00', '00:15:00', 10, 411, 1, 'y'),
(132, '10ATHLON_U18M', 'Décathlon U18 M', 436, 6, 0, 9, '01:00:00', '00:15:00', 10, 412, 1, 'y'),
(133, '10ATHLON_W', 'Décathlon W', 437, 6, 0, 9, '01:00:00', '00:15:00', 10, 413, 1, 'y'),
(134, '7ATHLON', 'Heptathlon', 430, 6, 0, 9, '01:00:00', '00:15:00', 7, 400, 1, 'y'),
(135, '7ATHLON_U18W', 'Heptathlon U18 W', 431, 6, 0, 9, '01:00:00', '00:15:00', 7, 401, 1, 'y'),
(136, '6ATHLON_U16M', 'Hexathlon U16 M', 429, 6, 0, 9, '01:00:00', '00:15:00', 6, 402, 1, 'y'),
(137, '5ATHLON_U16W', 'Pentathlon U16 W', 426, 6, 0, 9, '01:00:00', '00:15:00', 5, 399, 1, 'y'),
(138, 'UKC', 'UBS Kids Cup', 439, 6, 0, 9, '01:00:00', '00:15:00', 3, 408, 1, 'y'),
(139, 'MILEWALK', 'Mile walk', 450, 50, 0, 7, '01:00:00', '00:15:00', 1609, 415, 5, 'y'),
(140, '3000WALK', '3000 m walk', 452, 50, 0, 7, '01:00:00', '00:15:00', 3000, 420, 5, 'y'),
(141, '5000WALK', '5000 m walk', 453, 50, 0, 7, '01:00:00', '00:15:00', 5000, 430, 5, 'y'),
(142, '10000WALK', '10000 m walk', 454, 50, 0, 7, '01:00:00', '00:15:00', 10000, 440, 5, 'y'),
(143, '20000WALK', '20000 m walk', 455, 50, 0, 7, '01:00:00', '00:15:00', 20000, 450, 5, 'y'),
(144, '50000WALK', '50000 m walk', 456, 50, 0, 7, '01:00:00', '00:15:00', 50000, 460, 5, 'y'),
(145, '3KMWALK', '3 km walk', 470, 50, 0, 7, '01:00:00', '00:15:00', 3000, 470, 5, 'y'),
(146, '5KMWALK', '5 km walk', 480, 50, 0, 7, '01:00:00', '00:15:00', 5000, 480, 5, 'y'),
(147, '10KMWALK', '10 km walk', 490, 50, 0, 7, '01:00:00', '00:15:00', 10000, 490, 5, 'y'),
(150, '20KMWALK', '20 km walk', 500, 50, 0, 7, '01:00:00', '00:15:00', 20000, 500, 5, 'y'),
(152, '35KMWALK', '35 km walk', 530, 50, 0, 7, '01:00:00', '00:15:00', 35000, 530, 5, 'y'),
(154, '50KMWALK', '50 km walk', 550, 50, 0, 7, '01:00:00', '00:15:00', 50000, 550, 5, 'y'),
(156, '10KM', '10 km', 440, 650, 0, 7, '01:00:00', '00:15:00', 10000, 491, 1, 'y'),
(157, '15KM', '15 km', 441, 50, 0, 7, '01:00:00', '00:15:00', 15000, 494, 1, 'y'),
(158, '20KM', '20 km', 442, 50, 0, 7, '01:00:00', '00:15:00', 20000, 501, 1, 'y'),
(159, '25KM', '25 km', 443, 50, 0, 7, '01:00:00', '00:15:00', 25000, 505, 1, 'y'),
(160, '30KM', '30 km', 444, 50, 0, 7, '01:00:00', '00:15:00', 30000, 511, 1, 'y'),
(162, '1HWALK', '1 h  walk', 555, 50, 0, 7, '01:00:00', '00:15:00', 1, 555, 5, 'y'),
(163, '2HWALK', '2 h  walk', 556, 50, 0, 7, '01:00:00', '00:15:00', 2, 556, 5, 'y'),
(164, '100KMWALK', '100 km walk', 457, 50, 0, 7, '01:00:00', '00:15:00', 100000, 559, 5, 'y'),
(165, 'BALLE80', 'Balle 80 gr', 393, 15, 0, 8, '01:00:00', '00:20:00', 0, 385, 1, 'y'),
(166, '300H91.4', '300 m haies 91.4', 289, 6, 0, 2, '01:00:00', '00:15:00', 300, 289, 4, 'y'),
(167, '...ATHLON', '...athlon', 799, 6, 0, 9, '01:00:00', '00:15:00', 4, 799, 1, 'y'),
(168, '75', '75 m', 31, 6, 0, 1, '01:00:00', '00:15:00', 75, 31, 1, 'y'),
(169, '50H68.6', '50 m haies 68.6', 240, 6, 0, 2, '01:00:00', '00:15:00', 50, 237, 1, 'y'),
(170, '60H68.6', '60 m haies 68.6', 252, 6, 0, 2, '01:00:00', '00:15:00', 60, 257, 1, 'y'),
(171, '80H84.0', '80 m haies 84.0', 263, 6, 0, 1, '01:00:00', '00:15:00', 80, 260, 1, 'y'),
(172, '80H68.6', '80 m haies 68.6', 265, 6, 0, 1, '01:00:00', '00:15:00', 80, 262, 1, 'y'),
(173, '300H68.6', '300 m haies 68.6', 292, 6, 0, 2, '01:00:00', '00:15:00', 300, 295, 1, 'y'),
(174, 'JAVELOT500', 'Javelot 500 gr', 390, 15, 0, 8, '01:00:00', '00:20:00', 0, 390, 1, 'y'),
(175, '5ATHLON_M', 'Pentathlon M', 418, 6, 0, 9, '01:00:00', '00:15:00', 5, 392, 1, 'y'),
(176, '5ATHLON_U20M', 'Pentathlon U20 M', 420, 6, 0, 9, '01:00:00', '00:15:00', 5, 393, 1, 'y'),
(177, '5ATHLON_U18M', 'Pentathlon U18 M', 421, 6, 0, 9, '01:00:00', '00:15:00', 5, 405, 1, 'y'),
(178, '5ATHLON_F', 'Pentathlon F', 423, 6, 0, 9, '01:00:00', '00:15:00', 5, 416, 1, 'y'),
(180, '5ATHLON_U18F', 'Pentathlon U18 F', 425, 6, 0, 9, '01:00:00', '00:15:00', 5, 418, 1, 'y'),
(181, '10ATHLON_MASTER', 'Décathlon Master', 438, 6, 0, 9, '01:00:00', '00:15:00', 10, 414, 1, 'y'),
(182, '2000WALK', '2000 m walk', 451, 50, 0, 7, '01:00:00', '00:15:00', 2000, 419, 1, 'y'),
(183, '...COURS', '...cours', 796, 6, 0, 9, '01:00:00', '00:15:00', 4, 796, 1, 'y'),
(184, '...LONGUEUR', '...longueur', 797, 6, 0, 9, '01:00:00', '00:20:00', 4, 797, 1, 'y'),
(185, '...LANCER', '...lancer', 798, 6, 0, 9, '01:00:00', '00:20:00', 4, 798, 1, 'y'),
(186, 'LONGUEUR Z', 'Longueur (zone)', 331, 15, 0, 5, '01:00:00', '00:20:00', 0, 331, 1, 'y'),
(187, '50H76.2U16', '50 m haies 76.2  U16W/U14M', 237, 6, 0, 2, '01:00:00', '00:15:00', 50, 246, 4, 'y'),
(188, '50H76.2U14', '50 m haies 76.2  U14 W (In)', 238, 6, 0, 2, '01:00:00', '00:15:00', 50, 247, 4, 'y'),
(189, '50H60-76.2', '50 m haies 60-76.2 U12 (In)', 239, 6, 0, 2, '01:00:00', '00:15:00', 50, 248, 4, 'y'),
(190, '60H76.2U16', '60 m haies 76.2  U16W/U14M', 247, 6, 0, 2, '01:00:00', '00:15:00', 60, 275, 4, 'y'),
(191, '60H76.2U14I', '60 m haies 76.2  U14W (In)', 248, 6, 0, 2, '01:00:00', '00:15:00', 60, 276, 4, 'y'),
(192, '60H60-76.2', '60 m haies 60-76.2  U12 (In)', 250, 6, 0, 2, '01:00:00', '00:15:00', 60, 277, 4, 'y'),
(193, '60H76.2U14O', '60 m haies 76.2  U14 W (Out)', 251, 6, 0, 2, '01:00:00', '00:15:00', 60, 278, 4, 'y'),
(194, '60H60-76.2U12', '60 m haies 60-76.2 U12', 254, 6, 0, 2, '01:00:00', '00:15:00', 60, 279, 4, 'y'),
(195, '5ATHLON_U16M', 'Athlon U16 M', 422, 6, 0, 9, '01:00:00', '00:15:00', 5, 406, 1, 'y'),
(196, '5ATHLON_U18M_I', 'Pentathlon U18 M Indoor', 406, 6, 0, 9, '01:00:00', '00:15:00', 5, 424, 1, 'y'),
(197, '5ATHLON_U23M', 'Pentathlon U23 M', 419, 6, 0, 9, '01:00:00', '00:15:00', 5, 407, 1, 'y'),
(198, '5ATHLON_U20W', 'Pentathlon U20 W', 424, 6, 0, 9, '01:00:00', '00:15:00', 5, 417, 1, 'y'),
(199, '5ATHLON_U16M_I', 'Pentathlon U16 M Indoor', 407, 6, 0, 9, '01:00:00', '00:15:00', 5, 425, 1, 'y'),
(200, '5ATHLON_U16W_I', 'Pentathlon U16 w Indoor', 410, 6, 0, 9, '01:00:00', '00:15:00', 5, 426, 1, 'y'),
(201, '8ATHLON_U18M', 'Octathlon U18 M', 433, 6, 0, 9, '01:00:00', '00:15:00', 5, 427, 1, 'y'),
(202, 'Relais suédois', 'Relais suédois', 404, 12, 4, 3, '01:00:00', '00:15:00', 0, 603, 1, 'y');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `disziplin_it`
--

DROP TABLE IF EXISTS `disziplin_it`;
CREATE TABLE IF NOT EXISTS `disziplin_it` (
  `xDisziplin` int(11) NOT NULL AUTO_INCREMENT,
  `Kurzname` varchar(15) NOT NULL DEFAULT '',
  `Name` varchar(40) NOT NULL DEFAULT '',
  `Anzeige` int(11) NOT NULL DEFAULT '1',
  `Seriegroesse` int(4) NOT NULL DEFAULT '0',
  `Staffellaeufer` int(11) DEFAULT NULL,
  `Typ` int(11) NOT NULL DEFAULT '0',
  `Appellzeit` time NOT NULL DEFAULT '00:00:00',
  `Stellzeit` time NOT NULL DEFAULT '00:00:00',
  `Strecke` float NOT NULL DEFAULT '0',
  `Code` int(11) NOT NULL DEFAULT '0',
  `xOMEGA_Typ` int(11) NOT NULL DEFAULT '0',
  `aktiv` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`xDisziplin`),
  UNIQUE KEY `Kurzname` (`Kurzname`),
  KEY `Anzeige` (`Anzeige`),
  KEY `Staffel` (`Staffellaeufer`),
  KEY `Code` (`Code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=203 ;

--
-- Daten für Tabelle `disziplin_it`
--

INSERT INTO `disziplin_it` (`xDisziplin`, `Kurzname`, `Name`, `Anzeige`, `Seriegroesse`, `Staffellaeufer`, `Typ`, `Appellzeit`, `Stellzeit`, `Strecke`, `Code`, `xOMEGA_Typ`, `aktiv`) VALUES
(38, '50', '50 m', 10, 6, 0, 2, '01:00:00', '00:15:00', 50, 10, 1, 'y'),
(39, '55', '55 m', 20, 6, 0, 2, '01:00:00', '00:15:00', 55, 20, 1, 'y'),
(40, '60', '60 m', 30, 6, 0, 2, '01:00:00', '00:15:00', 60, 30, 1, 'y'),
(41, '80', '80 m', 35, 6, 0, 1, '01:00:00', '00:15:00', 80, 35, 1, 'y'),
(42, '100', '100 m', 40, 6, 0, 1, '01:00:00', '00:15:00', 100, 40, 1, 'y'),
(43, '150', '150 m', 48, 6, 0, 1, '01:00:00', '00:15:00', 150, 48, 1, 'y'),
(44, '200', '200 m', 50, 6, 0, 1, '01:00:00', '00:15:00', 200, 50, 1, 'y'),
(45, '300', '300 m', 60, 6, 0, 2, '01:00:00', '00:15:00', 300, 60, 1, 'y'),
(46, '400', '400 m', 70, 6, 0, 2, '01:00:00', '00:15:00', 400, 70, 1, 'y'),
(47, '600', '600 m', 80, 12, 0, 7, '01:00:00', '00:15:00', 600, 80, 1, 'y'),
(48, '800', '800 m', 90, 6, 0, 7, '01:00:00', '00:15:00', 800, 90, 1, 'y'),
(49, '1000', '1000 m', 100, 15, 0, 7, '01:00:00', '00:15:00', 1000, 100, 1, 'y'),
(50, '1500', '1500 m', 110, 12, 0, 7, '01:00:00', '00:15:00', 1500, 110, 1, 'y'),
(51, '1MILE', '1 mile', 120, 15, 0, 7, '01:00:00', '00:15:00', 1609, 120, 1, 'y'),
(52, '2000', '2000 m', 130, 15, 0, 7, '01:00:00', '00:15:00', 2000, 130, 1, 'y'),
(53, '3000', '3000 m', 140, 15, 0, 7, '01:00:00', '00:15:00', 3000, 140, 1, 'y'),
(54, '5000', '5000 m', 160, 15, 0, 7, '01:00:00', '00:15:00', 5000, 160, 1, 'y'),
(55, '10000', '10 000 m', 170, 20, 0, 7, '01:00:00', '00:15:00', 10000, 170, 1, 'y'),
(56, '20000', '20 000 m', 180, 20, 0, 7, '01:00:00', '00:15:00', 20000, 180, 1, 'y'),
(57, '1ORA', '1 ora', 171, 20, 0, 7, '01:00:00', '00:15:00', 1, 182, 1, 'y'),
(58, '25000', '25 000 m', 181, 20, 0, 7, '01:00:00', '00:15:00', 25000, 181, 1, 'y'),
(59, '30000', '30 000 m', 182, 20, 0, 7, '01:00:00', '00:15:00', 30000, 195, 1, 'y'),
(61, 'MEZZA MARA', 'Mezza maratona', 183, 20, 0, 7, '01:00:00', '00:15:00', 0, 190, 1, 'y'),
(62, 'MARATONA', 'Maratona', 184, 20, 0, 7, '01:00:00', '00:15:00', 0, 200, 1, 'y'),
(64, '50H106.7', '50 m ostacoli 106.7', 232, 6, 0, 1, '01:00:00', '00:15:00', 50, 232, 4, 'y'),
(65, '50H99.1', '50 m ostacoli 99.1', 233, 6, 0, 2, '01:00:00', '00:15:00', 50, 233, 4, 'y'),
(66, '50H91.4', '50 m ostacoli 91.4', 234, 6, 0, 2, '01:00:00', '00:15:00', 50, 234, 4, 'y'),
(67, '50H84.0', '50 m ostacoli 84.0', 235, 6, 0, 2, '01:00:00', '00:15:00', 50, 235, 4, 'y'),
(68, '50H76.2', '50 m ostacoli 76.2  U18 W', 236, 6, 0, 2, '01:00:00', '00:15:00', 50, 236, 4, 'y'),
(69, '60H106.7', '60 m ostacoli 106.7', 241, 6, 0, 2, '01:00:00', '00:15:00', 60, 252, 4, 'y'),
(70, '60H99.1', '60 m ostacoli 99.1', 242, 6, 0, 2, '01:00:00', '00:15:00', 60, 253, 4, 'y'),
(71, '60H91.4', '60 m ostacoli 91.4', 243, 6, 0, 2, '01:00:00', '00:15:00', 60, 254, 4, 'y'),
(72, '60H84.0', '60 m ostacoli 84.0', 244, 6, 0, 2, '01:00:00', '00:15:00', 60, 255, 4, 'y'),
(73, '60H76.2', '60 m ostacoli 76.2  U18 W', 245, 6, 0, 2, '01:00:00', '00:15:00', 60, 256, 4, 'y'),
(74, '80H76.2', '80 m ostacoli 76.2', 264, 6, 0, 1, '01:00:00', '00:15:00', 80, 258, 4, 'y'),
(75, '100H84.0', '100 m ostacoli 84.0', 266, 6, 0, 1, '01:00:00', '00:15:00', 100, 261, 4, 'y'),
(76, '100H76.2', '100 m ostacoli 76.2', 267, 6, 0, 1, '01:00:00', '00:15:00', 100, 259, 4, 'y'),
(77, '110H106.7', '110 m ostacoli 106.7', 268, 6, 0, 1, '01:00:00', '00:15:00', 110, 271, 4, 'y'),
(78, '110H99.1', '110 m ostacoli 99.1', 269, 6, 0, 1, '01:00:00', '00:15:00', 110, 269, 4, 'y'),
(79, '110H91.4', '110 m ostacoli 91.4', 270, 6, 0, 1, '01:00:00', '00:15:00', 110, 268, 4, 'y'),
(80, '200H', '200 m ostacoli', 280, 6, 0, 1, '01:00:00', '00:15:00', 200, 280, 4, 'y'),
(81, '300H84.0', '300 m ostacoli 84.0', 290, 6, 0, 2, '01:00:00', '00:15:00', 300, 290, 4, 'y'),
(82, '300H76.2', '300 m ostacoli 76.2', 291, 6, 0, 2, '01:00:00', '00:15:00', 300, 291, 4, 'y'),
(83, '400H91.4', '400 m ostacoli 91.4', 298, 6, 0, 2, '01:00:00', '00:15:00', 400, 301, 4, 'y'),
(84, '400H76.2', '400 m ostacoli 76.2', 301, 6, 0, 2, '01:00:00', '00:15:00', 400, 298, 4, 'y'),
(85, '1500ST', '1500 m Steeple', 302, 6, 0, 7, '01:00:00', '00:15:00', 1500, 209, 6, 'y'),
(86, '2000ST', '2000 m Steeple', 303, 6, 0, 7, '01:00:00', '00:15:00', 2000, 210, 6, 'y'),
(87, '3000ST', '3000 m Steeple', 304, 6, 0, 7, '01:00:00', '00:15:00', 3000, 220, 6, 'y'),
(88, '5XLIBERO', '5x libero', 395, 6, 5, 3, '01:00:00', '00:15:00', 5, 497, 1, 'y'),
(89, '5X80', '5x80 m', 396, 6, 5, 3, '01:00:00', '00:15:00', 400, 498, 1, 'y'),
(90, '6XLIBERO', '6x libero', 394, 6, 6, 3, '01:00:00', '00:15:00', 6, 499, 1, 'y'),
(91, '4X100', '4x100 m', 397, 6, 4, 3, '01:00:00', '00:15:00', 400, 560, 1, 'y'),
(92, '4X200', '4x200 m', 398, 6, 4, 3, '01:00:00', '00:15:00', 800, 570, 1, 'y'),
(93, '4X400', '4x400 m', 399, 6, 4, 3, '01:00:00', '00:15:00', 1600, 580, 1, 'y'),
(94, '3X800', '3x800 m', 400, 6, 3, 3, '01:00:00', '00:15:00', 2400, 589, 1, 'y'),
(95, '4X800', '4x800 m', 401, 6, 4, 3, '01:00:00', '00:15:00', 3200, 590, 1, 'y'),
(96, '3X1000', '3x1000 m', 402, 6, 3, 3, '01:00:00', '00:15:00', 3000, 595, 1, 'y'),
(97, '4X1500', '4x1500 m', 403, 6, 4, 3, '01:00:00', '00:15:00', 6000, 600, 1, 'y'),
(98, 'OLYMPISCHE', 'Olympische', 404, 12, 4, 3, '01:00:00', '00:15:00', 0, 601, 1, 'y'),
(99, 'AMERICAINE', 'Américaine', 405, 12, 3, 3, '01:00:00', '00:15:00', 0, 602, 1, 'y'),
(100, 'ALTO', 'Alto', 310, 15, 0, 6, '01:00:00', '00:20:00', 0, 310, 1, 'y'),
(101, 'ASTA', 'Asta', 320, 15, 0, 6, '01:30:00', '00:40:00', 0, 320, 1, 'y'),
(102, 'LUNGO', 'Lungo', 330, 15, 0, 4, '01:00:00', '00:20:00', 0, 330, 1, 'y'),
(103, 'TRIPLO', 'Triplo', 340, 15, 0, 4, '01:00:00', '00:20:00', 0, 340, 1, 'y'),
(104, 'PESO7.26', 'Peso 7.26 kg', 347, 615, 0, 8, '01:00:00', '00:20:00', 0, 351, 1, 'y'),
(105, 'PESO6.00', 'Peso 6.00 kg', 348, 15, 0, 8, '01:00:00', '00:20:00', 0, 348, 1, 'y'),
(106, 'PESO5.00', 'Peso 5.00 kg', 349, 15, 0, 8, '01:00:00', '00:20:00', 0, 347, 1, 'y'),
(107, 'PESO4.00', 'Peso 4.00 kg', 350, 15, 0, 8, '01:00:00', '00:20:00', 0, 349, 1, 'y'),
(108, 'PESO3.00', 'Peso 3.00 kg', 352, 15, 0, 8, '01:00:00', '00:20:00', 0, 352, 1, 'y'),
(109, 'PESO2.50', 'Peso 2.50 kg', 353, 15, 0, 8, '01:00:00', '00:20:00', 0, 353, 1, 'y'),
(110, 'DISCO2.00', 'Disco 2.00 kg', 356, 15, 0, 8, '01:00:00', '00:20:00', 0, 361, 1, 'y'),
(111, 'DISCO1.75', 'Disco 1.75 kg', 357, 15, 0, 8, '01:00:00', '00:20:00', 0, 359, 1, 'y'),
(112, 'DISCO1.50', 'Disco 1.50 kg', 358, 15, 0, 8, '01:00:00', '00:20:00', 0, 358, 1, 'y'),
(113, 'DISCO1.00', 'Disco 1.00 kg', 359, 15, 0, 8, '01:00:00', '00:20:00', 0, 357, 1, 'y'),
(114, 'DISCO0.75', 'Disco 0.75 kg', 361, 15, 0, 8, '01:00:00', '00:20:00', 0, 356, 1, 'y'),
(115, 'MARTELLO7.26', 'Martello 7.26 kg', 375, 15, 0, 8, '01:00:00', '00:20:00', 0, 381, 1, 'y'),
(116, 'MARTELLO6.00', 'Martello 6.00 kg', 376, 15, 0, 8, '01:00:00', '00:20:00', 0, 378, 1, 'y'),
(117, 'MARTELLO5.00', 'Martello 5.00 kg', 377, 15, 0, 8, '01:00:00', '00:20:00', 0, 377, 1, 'y'),
(118, 'MARTELLO4.00', 'Martello 4.00 kg', 378, 15, 0, 8, '01:00:00', '00:20:00', 0, 376, 1, 'y'),
(119, 'MARTELLO3.00', 'Martello 3.00 kg', 381, 15, 0, 8, '01:00:00', '00:20:00', 0, 375, 1, 'y'),
(120, 'GIAVELLOTTO800', 'Giavellotto 800 gr', 387, 15, 0, 8, '01:00:00', '00:20:00', 0, 391, 1, 'y'),
(121, 'GIAVELLOTTO700', 'Giavellotto 700 gr', 388, 15, 0, 8, '01:00:00', '00:20:00', 0, 389, 1, 'y'),
(122, 'GIAVELLOTTO600', 'Giavellotto 600 gr', 389, 15, 0, 8, '01:00:00', '00:20:00', 0, 388, 1, 'y'),
(123, 'GIAVELLOTTO400', 'Giavellotto 400 gr', 391, 15, 0, 8, '01:00:00', '00:20:00', 0, 387, 1, 'y'),
(124, 'PALLINO200', 'Pallina 200 gr', 392, 15, 0, 8, '01:00:00', '00:20:00', 0, 386, 1, 'y'),
(125, '5ATHLON_W_U20WI', 'Pentathlon W / U20 W Indoor', 408, 6, 0, 9, '01:00:00', '00:15:00', 5, 394, 1, 'y'),
(126, '5ATHLON_U18W_I', 'Pentathlon U18 W Indoor', 409, 6, 0, 9, '01:00:00', '00:15:00', 5, 395, 1, 'y'),
(127, '7ATHLON_M_I', 'Heptathlon M Indoor', 413, 6, 0, 9, '01:00:00', '00:15:00', 7, 396, 1, 'y'),
(128, '7ATHLON_U20M_I', 'Heptathlon U20 M Indoor', 414, 6, 0, 9, '01:00:00', '00:15:00', 7, 397, 1, 'y'),
(129, '7ATHLON_U18M_I', 'Heptathlon U18 M Indoor', 415, 6, 0, 9, '01:00:00', '00:15:00', 7, 398, 1, 'y'),
(130, '10ATHLON_M', 'Decathlon M', 434, 6, 0, 9, '01:00:00', '00:15:00', 10, 410, 1, 'y'),
(131, '10ATHLON_U20M', 'Decathlon U20 M', 435, 6, 0, 9, '01:00:00', '00:15:00', 10, 411, 1, 'y'),
(132, '10ATHLON_U18M', 'Decathlon U18 M', 436, 6, 0, 9, '01:00:00', '00:15:00', 10, 412, 1, 'y'),
(133, '10ATHLON_W', 'Decathlon W', 437, 6, 0, 9, '01:00:00', '00:15:00', 10, 413, 1, 'y'),
(134, '7ATHLON', 'Heptathlon', 430, 6, 0, 9, '01:00:00', '00:15:00', 7, 400, 1, 'y'),
(135, '7ATHLON_U18W', 'Heptathlon U18 W', 431, 6, 0, 9, '01:00:00', '00:15:00', 7, 401, 1, 'y'),
(136, '6ATHLON_U16M', 'Hexathlon U16 M', 429, 6, 0, 9, '01:00:00', '00:15:00', 6, 402, 1, 'y'),
(137, '5ATHLON_U16W', 'Pentathlon U16 W', 426, 6, 0, 9, '01:00:00', '00:15:00', 5, 399, 1, 'y'),
(138, 'UKC', 'UBS Kids Cup', 439, 6, 0, 9, '01:00:00', '00:15:00', 3, 408, 1, 'y'),
(139, 'MILEWALK', 'Mile walk', 450, 20, 0, 7, '01:00:00', '00:15:00', 1609, 415, 5, 'y'),
(140, '3000WALK', '3000 m walk', 452, 20, 0, 7, '01:00:00', '00:15:00', 3000, 420, 5, 'y'),
(141, '5000WALK', '5000 m walk', 453, 20, 0, 7, '01:00:00', '00:15:00', 5000, 430, 5, 'y'),
(142, '10000WALK', '10000 m walk', 454, 20, 0, 7, '01:00:00', '00:15:00', 10000, 440, 5, 'y'),
(143, '20000WALK', '20000 m walk', 455, 20, 0, 7, '01:00:00', '00:15:00', 20000, 450, 5, 'y'),
(144, '50000WALK', '50000 m walk', 456, 20, 0, 7, '01:00:00', '00:15:00', 50000, 460, 5, 'y'),
(145, '3KMWALK', '3 km walk', 470, 20, 0, 7, '01:00:00', '00:15:00', 3000, 470, 5, 'y'),
(146, '5KMWALK', '5 km walk', 480, 20, 0, 7, '01:00:00', '00:15:00', 5000, 480, 5, 'y'),
(147, '10KMWALK', '10 km walk', 490, 20, 0, 7, '01:00:00', '00:15:00', 10000, 490, 5, 'y'),
(150, '20KMWALK', '20 km walk', 500, 20, 0, 7, '01:00:00', '00:15:00', 20000, 500, 5, 'y'),
(152, '35KMWALK', '35 km walk', 530, 20, 0, 7, '01:00:00', '00:15:00', 35000, 530, 5, 'y'),
(154, '50KMWALK', '50 km walk', 550, 20, 0, 7, '01:00:00', '00:15:00', 50000, 550, 5, 'y'),
(156, '10KM', '10 km', 440, 20, 0, 7, '01:00:00', '00:15:00', 10000, 491, 1, 'y'),
(157, '15KM', '15 km', 441, 20, 0, 7, '01:00:00', '00:15:00', 15000, 494, 1, 'y'),
(158, '20KM', '20 km', 442, 20, 0, 7, '01:00:00', '00:15:00', 20000, 501, 1, 'y'),
(159, '25KM', '25 km', 443, 20, 0, 7, '01:00:00', '00:15:00', 25000, 505, 1, 'y'),
(160, '30KM', '30 km', 444, 20, 0, 7, '01:00:00', '00:15:00', 30000, 511, 1, 'y'),
(162, '1HWALK', '1 h  walk', 555, 20, 0, 7, '01:00:00', '00:15:00', 1, 555, 5, 'y'),
(163, '2HWALK', '2 h  walk', 556, 20, 0, 7, '01:00:00', '00:15:00', 2, 556, 5, 'y'),
(164, '100KMWALK', '100 km walk', 457, 20, 0, 7, '01:00:00', '00:15:00', 100000, 559, 5, 'y'),
(165, 'PALLINO80', 'Pallina 80 gr', 393, 15, 0, 8, '01:00:00', '00:20:00', 0, 385, 1, 'y'),
(166, '300H91.4', '300 m ostacoli 91.4', 289, 6, 0, 2, '01:00:00', '00:15:00', 300, 289, 4, 'y'),
(167, '...ATHLON', '...athlon', 799, 6, 0, 9, '01:00:00', '00:15:00', 4, 799, 1, 'y'),
(168, '75', '75 m', 31, 6, 0, 1, '01:00:00', '00:15:00', 75, 31, 1, 'y'),
(169, '50H68.6', '50 m ostacoli 68.6', 240, 6, 0, 2, '01:00:00', '00:15:00', 50, 237, 1, 'y'),
(170, '60H68.6', '60 m ostacoli 68.6', 252, 6, 0, 2, '01:00:00', '00:15:00', 60, 257, 1, 'y'),
(171, '80H84.0', '80 m ostacoli 84.0', 263, 6, 0, 1, '01:00:00', '00:15:00', 80, 260, 1, 'y'),
(172, '80H68.6', '80 m ostacoli 68.6', 265, 6, 0, 1, '01:00:00', '00:15:00', 80, 262, 1, 'y'),
(173, '300H68.6', '300 m ostacoli 68.6', 292, 6, 0, 2, '01:00:00', '00:15:00', 300, 295, 1, 'y'),
(174, 'GIAVELLOTTO500', 'Giavellotto 500 gr', 390, 15, 0, 8, '01:00:00', '00:20:00', 0, 390, 1, 'y'),
(175, '5ATHLON_M', 'Pentathlon M', 418, 6, 0, 9, '01:00:00', '00:15:00', 5, 392, 1, 'y'),
(176, '5ATHLON_U20M', 'Pentathlon U20 M', 420, 6, 0, 9, '01:00:00', '00:15:00', 5, 393, 1, 'y'),
(177, '5ATHLON_U18M', 'Pentathlon U18 M', 421, 6, 0, 9, '01:00:00', '00:15:00', 5, 405, 1, 'y'),
(178, '5ATHLON_F', 'Pentathlon F', 423, 6, 0, 9, '01:00:00', '00:15:00', 5, 416, 1, 'y'),
(180, '5ATHLON_U18F', 'Pentathlon U18 F', 425, 6, 0, 9, '01:00:00', '00:15:00', 5, 418, 1, 'y'),
(181, '10ATHLON_MASTER', 'Decathlon Master', 438, 6, 0, 9, '01:00:00', '00:15:00', 10, 414, 1, 'y'),
(182, '2000WALK', '2000 m walk', 451, 50, 0, 7, '01:00:00', '00:15:00', 2000, 419, 1, 'y'),
(183, '...COURS', '...cours', 796, 6, 0, 9, '01:00:00', '00:15:00', 4, 796, 1, 'y'),
(184, '...LUNGO', '...lungo', 797, 6, 0, 9, '01:00:00', '00:20:00', 4, 797, 1, 'y'),
(185, '...LANCER', '...lancer', 798, 6, 0, 9, '01:00:00', '00:00:00', 4, 798, 1, 'y'),
(186, 'LUNGO Z', 'Lungo (zone)', 331, 15, 0, 5, '01:00:00', '00:20:00', 0, 331, 1, 'y'),
(187, '50H76.2U16', '50 m ostacoli 76.2  U16W/U14M', 237, 6, 0, 2, '01:00:00', '00:15:00', 50, 246, 4, 'y'),
(188, '50H76.2U14', '50 m ostacoli 76.2  U14 W (In)', 238, 6, 0, 2, '01:00:00', '00:15:00', 50, 247, 4, 'y'),
(189, '50H60-76.2', '50 m ostacoli 60-76.2 U12 (In)', 239, 6, 0, 2, '01:00:00', '00:15:00', 50, 248, 4, 'y'),
(190, '60H76.2U16', '60 m ostacoli 76.2  U16W/U14M', 247, 6, 0, 2, '01:00:00', '00:15:00', 60, 275, 4, 'y'),
(191, '60H76.2U14I', '60 m ostacoli 76.2  U14W (In)', 248, 6, 0, 2, '01:00:00', '00:15:00', 60, 276, 4, 'y'),
(192, '60H60-76.2', '60 m ostacoli 60-76.2  U12 (In)', 250, 6, 0, 2, '01:00:00', '00:15:00', 60, 277, 4, 'y'),
(193, '60H76.2U14O', '60 m ostacoli 76.2  U14 W (Out)', 251, 6, 0, 2, '01:00:00', '00:15:00', 60, 278, 4, 'y'),
(194, '60H60-76.2U12', '60 m ostacoli 60-76.2 U12', 254, 6, 0, 2, '01:00:00', '00:15:00', 60, 279, 4, 'y'),
(195, '5ATHLON_U16M', 'Pentathlon U16 M', 422, 6, 0, 9, '01:00:00', '00:15:00', 5, 406, 1, 'y'),
(196, '5ATHLON_U18M_I', 'Pentathlon U18 M Indoor', 406, 6, 0, 9, '01:00:00', '00:15:00', 5, 424, 1, 'y'),
(197, '5ATHLON_U23M', 'Pentathlon U23 M', 419, 6, 0, 9, '01:00:00', '00:15:00', 5, 407, 1, 'y'),
(198, '5ATHLON_U20W', 'Pentathlon U20 W', 424, 6, 0, 9, '01:00:00', '00:15:00', 5, 417, 1, 'y'),
(199, '55ATHLON_U16M_I', 'Pentathlon U16 M Indoor', 407, 6, 0, 9, '01:00:00', '00:15:00', 5, 425, 1, 'y'),
(200, '5ATHLON_U16W_I', 'Pentathlon U16 w Indoor', 410, 6, 0, 9, '01:00:00', '00:15:00', 5, 426, 1, 'y'),
(201, '8ATHLON_U18M', 'Octathlon U18 M', 433, 6, 0, 9, '01:00:00', '00:15:00', 5, 427, 1, 'y'),
(202, 'staffetta sved.', 'staffetta svedese', 404, 12, 4, 3, '01:00:00', '00:15:00', 0, 603, 1, 'y');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `faq`
--

DROP TABLE IF EXISTS `faq`;
CREATE TABLE IF NOT EXISTS `faq` (
  `xFaq` int(11) NOT NULL AUTO_INCREMENT,
  `Frage` varchar(255) NOT NULL DEFAULT '',
  `Antwort` text NOT NULL,
  `Zeigen` enum('y','n') NOT NULL DEFAULT 'y',
  `PosTop` int(11) NOT NULL DEFAULT '0',
  `PosLeft` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `Seite` varchar(255) NOT NULL DEFAULT '',
  `Sprache` char(2) NOT NULL DEFAULT '',
  `FarbeTitel` varchar(6) NOT NULL DEFAULT 'FFAA00',
  `FarbeHG` varchar(6) NOT NULL DEFAULT 'FFCC00',
  PRIMARY KEY (`xFaq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `faq`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hoehe`
--

DROP TABLE IF EXISTS `hoehe`;
CREATE TABLE IF NOT EXISTS `hoehe` (
  `xHoehe` int(11) NOT NULL AUTO_INCREMENT,
  `Hoehe` int(9) NOT NULL DEFAULT '0',
  `xRunde` int(11) NOT NULL DEFAULT '0',
  `xSerie` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xHoehe`),
  KEY `xRunde` (`xRunde`),
  KEY `xSerie` (`xSerie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `hoehe`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kategorie`
--

DROP TABLE IF EXISTS `kategorie`;
CREATE TABLE IF NOT EXISTS `kategorie` (
  `xKategorie` int(11) NOT NULL AUTO_INCREMENT,
  `Kurzname` varchar(4) NOT NULL DEFAULT '',
  `Name` varchar(30) NOT NULL DEFAULT '',
  `Anzeige` int(11) NOT NULL DEFAULT '1',
  `Alterslimite` tinyint(4) NOT NULL DEFAULT '99',
  `Code` varchar(4) NOT NULL DEFAULT '',
  `Geschlecht` enum('m','w') NOT NULL DEFAULT 'm',
  `aktiv` enum('y','n') NOT NULL DEFAULT 'y',
  `UKC` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`xKategorie`),
  UNIQUE KEY `Kurzname` (`Kurzname`),
  KEY `Anzeige` (`Anzeige`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Daten für Tabelle `kategorie`
--

INSERT INTO `kategorie` (`xKategorie`, `Kurzname`, `Name`, `Anzeige`, `Alterslimite`, `Code`, `Geschlecht`, `aktiv`, `UKC`) VALUES
(1, 'MAN_', 'MAN', 1, 99, 'MAN_', 'm', 'y', 'n'),
(2, 'U20M', 'U20 M', 4, 19, 'U20M', 'm', 'y', 'n'),
(3, 'U18M', 'U18 M', 5, 17, 'U18M', 'm', 'y', 'n'),
(4, 'U16M', 'U16 M', 6, 15, 'U16M', 'm', 'y', 'n'),
(5, 'U14M', 'U14 M', 7, 13, 'U14M', 'm', 'y', 'n'),
(6, 'U12M', 'U12 M', 8, 11, 'U12M', 'm', 'y', 'n'),
(7, 'WOM_', 'WOM', 10, 99, 'WOM_', 'w', 'y', 'n'),
(8, 'U20W', 'U20 W', 13, 19, 'U20W', 'w', 'y', 'n'),
(9, 'U18W', 'U18 W', 14, 17, 'U18W', 'w', 'y', 'n'),
(10, 'U16W', 'U16 W', 15, 15, 'U16W', 'w', 'y', 'n'),
(11, 'U14W', 'U14 W', 16, 13, 'U14W', 'w', 'y', 'n'),
(12, 'U12W', 'U12 W', 17, 11, 'U12W', 'w', 'y', 'n'),
(13, 'U23M', 'U23 M', 3, 22, 'U23M', 'm', 'y', 'n'),
(14, 'U23W', 'U23 W', 12, 22, 'U23W', 'w', 'y', 'n'),
(16, 'U10M', 'U10 M', 9, 9, 'U10M', 'm', 'y', 'n'),
(17, 'U10W', 'U10 W', 18, 9, 'U10W', 'w', 'y', 'n'),
(18, 'MASM', 'MASTERS M', 2, 99, 'MASM', 'm', 'y', 'n'),
(19, 'MASW', 'MASTERS W', 11, 99, 'MASW', 'w', 'y', 'n'),
(20, 'M15', 'U16 M15', 21, 15, 'M15', 'm', 'y', 'y'),
(21, 'M14', 'U16 M14', 22, 14, 'M14', 'm', 'y', 'y'),
(22, 'M13', 'U14 M13', 23, 13, 'M13', 'm', 'y', 'y'),
(23, 'M12', 'U14 M12', 24, 12, 'M12', 'm', 'y', 'y'),
(24, 'M11', 'U12 M11', 25, 11, 'M11', 'm', 'y', 'y'),
(25, 'M10', 'U12 M10', 26, 10, 'M10', 'm', 'y', 'y'),
(26, 'M09', 'U10 M09', 27, 9, 'M09', 'm', 'y', 'y'),
(27, 'M08', 'U10 M08', 28, 8, 'M08', 'm', 'y', 'y'),
(28, 'M07', 'U10 M07', 29, 7, 'M07', 'm', 'y', 'y'),
(29, 'W15', 'U16 W15', 31, 15, 'W15', 'w', 'y', 'y'),
(30, 'W14', 'U16 W14', 32, 14, 'W14', 'w', 'y', 'y'),
(31, 'W13', 'U14 W13', 33, 13, 'W13', 'w', 'y', 'y'),
(32, 'W12', 'U14 W12', 34, 12, 'W12', 'w', 'y', 'y'),
(33, 'W11', 'U12 W11', 35, 11, 'W11', 'w', 'y', 'y'),
(34, 'W10', 'U12 W10', 36, 10, 'W10', 'w', 'y', 'y'),
(35, 'W09', 'U10 W09', 37, 9, 'W09', 'w', 'y', 'y'),
(36, 'W08', 'U10 W08', 38, 8, 'W08', 'w', 'y', 'y'),
(37, 'W07', 'U10 W07', 39, 7, 'W07', 'w', 'y', 'y'),
(38, 'U12X', 'U12 MIX', 19, 11, 'U12X', 'm', 'y', 'n');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kategorie_svm`
--

DROP TABLE IF EXISTS `kategorie_svm`;
CREATE TABLE IF NOT EXISTS `kategorie_svm` (
  `xKategorie_svm` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Code` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`xKategorie_svm`),
  KEY `Code` (`Code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

--
-- Daten für Tabelle `kategorie_svm`
--

INSERT INTO `kategorie_svm` (`xKategorie_svm`, `Name`, `Code`) VALUES
(1, '29.01 Nationalliga A Männer', '29_01'),
(2, '29.02 Nationalliga A Frauen', '29_02'),
(3, '30.01 Nationalliga B Männer', '30_01'),
(4, '30.02 Nationalliga B Frauen', '30_02'),
(5, '31.01 Nationalliga C Männer', '31_01'),
(6, '31.02 Nationalliga C Frauen', '31_02'),
(7, '32.01 Regionalliga Ost Männer', '32_01'),
(8, '32.02 Regionalliga West Männer', '32_02'),
(9, '32.03 Regionalliga Ost Frauen', '32_03'),
(10, '32.04 Regionalliga West Frauen', '32_04'),
(11, '33.01 Junior Liga A Männer', '33_01'),
(12, '33.02 Junior Liga B Männer', '33_02'),
(13, '33.03 Junior Liga A Frauen', '33_03'),
(14, '33.04 Junior Liga B Frauen', '33_04'),
(15, '35.01 M30 und älter Männer', '35_01'),
(16, '35.02 U18 M', '35_02'),
(17, '35.03 U18 M Mehrkampf', '35_03'),
(18, '35.04 U16 M', '35_04'),
(19, '35.05 U16 M Mehrkampf', '35_05'),
(20, '35.06 U14 M', '35_06'),
(21, '35.07 U14 M Mannschaftswettkampf', '35_07'),
(22, '35.08 U12 M Mannschaftswettkampf', '35_08'),
(23, '36.01 W30 und älter Frauen', '36_01'),
(24, '36.02 U18 W', '36_02'),
(25, '36.03 U18 W Mehrkampf', '36_03'),
(26, '36.04 U16 W', '36_04'),
(27, '36.05 U16 W Mehrkampf', '36_05'),
(28, '36.06 U14 W', '36_06'),
(29, '36.07 U14 W Mannschaftswettkampf', '36_07'),
(30, '36.08 U12 W Mannschaftswettkampf', '36_08'),
(31, '36.09 Mixed Team U12 M und U12 W', '36_09'),
(32, '32.05 Regionalliga Mitte Männer', '32_05'),
(33, '32.06 Regionalliga Mitte Frauen', '32_06'),
(34, '33.05 Junior Liga C Männer', '33_05'),
(35, '33.06 Junior Liga C Frauen', '33_06'),
(36, '32.07 Regionalliga B Männer', '32_07'),
(37, '32.08 Regionalliga B Frauen', '32_08');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `land`
--

DROP TABLE IF EXISTS `land`;
CREATE TABLE IF NOT EXISTS `land` (
  `xCode` char(3) NOT NULL DEFAULT '',
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Sortierwert` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `land`
--

INSERT INTO `land` (`xCode`, `Name`, `Sortierwert`) VALUES
('SUI', 'Switzerland', 1),
('AFG', 'Afghanistan', 2),
('ALB', 'Albania', 3),
('ALG', 'Algeria', 4),
('ASA', 'American Samoa', 5),
('AND', 'Andorra', 6),
('ANG', 'Angola', 7),
('AIA', 'Anguilla', 8),
('ANT', 'Antigua & Barbuda', 9),
('ARG', 'Argentina', 10),
('ARM', 'Armenia', 11),
('ARU', 'Aruba', 12),
('AUS', 'Australia', 13),
('AUT', 'Austria', 14),
('AZE', 'Azerbaijan', 15),
('BAH', 'Bahamas', 16),
('BRN', 'Bahrain', 17),
('BAN', 'Bangladesh', 18),
('BAR', 'Barbados', 19),
('BLR', 'Belarus', 20),
('BEL', 'Belgium', 21),
('BIZ', 'Belize', 22),
('BEN', 'Benin', 23),
('BER', 'Bermuda', 24),
('BHU', 'Bhutan', 25),
('BOL', 'Bolivia', 26),
('BIH', 'Bosnia Herzegovina', 27),
('BOT', 'Botswana', 28),
('BRA', 'Brazil', 29),
('BRU', 'Brunei', 30),
('BUL', 'Bulgaria', 31),
('BRK', 'Burkina Faso', 32),
('BDI', 'Burundi', 33),
('CAM', 'Cambodia', 34),
('CMR', 'Cameroon', 35),
('CAN', 'Canada', 36),
('CPV', 'Cape Verde Islands', 37),
('CAY', 'Cayman Islands', 38),
('CAF', 'Central African Republic', 39),
('CHA', 'Chad', 40),
('CHI', 'Chile', 41),
('CHN', 'China', 42),
('COL', 'Colombia', 43),
('COM', 'Comoros', 44),
('CGO', 'Congo', 45),
('COD', 'Congo [Zaire]', 46),
('COK', 'Cook Islands', 47),
('CRC', 'Costa Rica', 48),
('CIV', 'Ivory Coast', 49),
('CRO', 'Croatia', 50),
('CUB', 'Cuba', 51),
('CYP', 'Cyprus', 52),
('CZE', 'Czech Republic', 53),
('DEN', 'Denmark', 54),
('DJI', 'Djibouti', 55),
('DMA', 'Dominica', 56),
('DOM', 'Dominican Republic', 57),
('TLS', 'East Timor', 58),
('ECU', 'Ecuador', 59),
('EGY', 'Egypt', 60),
('ESA', 'El Salvador', 61),
('GEQ', 'Equatorial Guinea', 62),
('ERI', 'Eritrea', 63),
('EST', 'Estonia', 64),
('ETH', 'Ethiopia', 65),
('FIJ', 'Fiji', 66),
('FIN', 'Finland', 67),
('FRA', 'France', 68),
('GAB', 'Gabon', 69),
('GAM', 'Gambia', 70),
('GEO', 'Georgia', 71),
('GER', 'Germany', 72),
('GHA', 'Ghana', 73),
('GIB', 'Gibraltar', 74),
('GBR', 'Great Britain & NI', 75),
('GRE', 'Greece', 76),
('GRN', 'Grenada', 77),
('GUM', 'Guam', 78),
('GUA', 'Guatemala', 79),
('GUI', 'Guinea', 80),
('GBS', 'Guinea-Bissau', 81),
('GUY', 'Guyana', 82),
('HAI', 'Haiti', 83),
('HON', 'Honduras', 84),
('HKG', 'Hong Kong', 85),
('HUN', 'Hungary', 86),
('ISL', 'Iceland', 87),
('IND', 'India', 88),
('INA', 'Indonesia', 89),
('IRI', 'Iran', 90),
('IRQ', 'Iraq', 91),
('IRL', 'Ireland', 92),
('ISR', 'Israel', 93),
('ITA', 'Italy', 94),
('JAM', 'Jamaica', 95),
('JPN', 'Japan', 96),
('JOR', 'Jordan', 97),
('KAZ', 'Kazakhstan', 98),
('KEN', 'Kenya', 99),
('KIR', 'Kiribati', 100),
('KOR', 'Korea', 101),
('KUW', 'Kuwait', 102),
('KGZ', 'Kirgizstan', 103),
('LAO', 'Laos', 104),
('LAT', 'Latvia', 105),
('LIB', 'Lebanon', 106),
('LES', 'Lesotho', 107),
('LBR', 'Liberia', 108),
('LIE', 'Liechtenstein', 109),
('LTU', 'Lithuania', 110),
('LUX', 'Luxembourg', 111),
('LBA', 'Libya', 112),
('MAC', 'Macao', 113),
('MKD', 'Macedonia', 114),
('MAD', 'Madagascar', 115),
('MAW', 'Malawi', 116),
('MAS', 'Malaysia', 117),
('MDV', 'Maldives', 118),
('MLI', 'Mali', 119),
('MLT', 'Malta', 120),
('MSH', 'Marshall Islands', 121),
('MTN', 'Mauritania', 122),
('MRI', 'Mauritius', 123),
('MEX', 'Mexico', 124),
('FSM', 'Micronesia', 125),
('MDA', 'Moldova', 126),
('MON', 'Monaco', 127),
('MGL', 'Mongolia', 128),
('MNE', 'Montenegro', 129),
('MNT', 'Montserrat', 130),
('MAR', 'Morocco', 131),
('MOZ', 'Mozambique', 132),
('MYA', 'Myanmar [Burma]', 133),
('NAM', 'Namibia', 134),
('NRU', 'Nauru', 135),
('NEP', 'Nepal', 136),
('NED', 'Netherlands', 137),
('AHO', 'Netherlands Antilles', 138),
('NZL', 'New Zealand', 139),
('NCA', 'Nicaragua', 140),
('NIG', 'Niger', 141),
('NGR', 'Nigeria', 142),
('NFI', 'Norfolk Islands', 143),
('PRK', 'North Korea', 144),
('NOR', 'Norway', 145),
('OMN', 'Oman', 146),
('PAK', 'Pakistan', 147),
('PLW', 'Palau', 148),
('PLE', 'Palestine', 149),
('PAN', 'Panama', 150),
('NGU', 'Papua New Guinea', 151),
('PAR', 'Paraguay', 152),
('PER', 'Peru', 153),
('PHI', 'Philippines', 154),
('POL', 'Poland', 155),
('POR', 'Portugal', 156),
('PUR', 'Puerto Rico', 157),
('QAT', 'Qatar', 158),
('ROM', 'Romania', 159),
('RUS', 'Russia', 160),
('RWA', 'Rwanda', 161),
('SMR', 'San Marino', 162),
('STP', 'São Tome & Principé', 163),
('KSA', 'Saudi Arabia', 164),
('SEN', 'Senegal', 165),
('SRB', 'Serbia', 166),
('SEY', 'Seychelles', 167),
('SLE', 'Sierra Leone', 168),
('SIN', 'Singapore', 169),
('SVK', 'Slovakia', 170),
('SLO', 'Slovenia', 171),
('SOL', 'Solomon Islands', 172),
('SOM', 'Somalia', 173),
('RSA', 'South Africa', 174),
('ESP', 'Spain', 175),
('SKN', 'St. Kitts & Nevis', 176),
('SRI', 'Sri Lanka', 177),
('LCA', 'St. Lucia', 178),
('VIN', 'St. Vincent & the Grenadines', 179),
('SUD', 'Sudan', 180),
('SUR', 'Surinam', 181),
('SWZ', 'Swaziland', 182),
('SWE', 'Sweden', 183),
('SYR', 'Syria', 185),
('TAH', 'Tahiti', 186),
('TPE', 'Taiwan', 187),
('TAD', 'Tadjikistan', 188),
('TAN', 'Tanzania', 189),
('THA', 'Thailand', 190),
('TOG', 'Togo', 191),
('TGA', 'Tonga', 192),
('TRI', 'Trinidad & Tobago', 193),
('TUN', 'Tunisia', 194),
('TUR', 'Turkey', 195),
('TKM', 'Turkmenistan', 196),
('TKS', 'Turks & Caicos Islands', 197),
('UGA', 'Uganda', 198),
('UKR', 'Ukraine', 199),
('UAE', 'United Arab Emirates', 200),
('USA', 'United States', 201),
('URU', 'Uruguay', 202),
('UZB', 'Uzbekistan', 203),
('VAN', 'Vanuatu', 204),
('VEN', 'Venezuela', 205),
('VIE', 'Vietnam', 206),
('ISV', 'Virgin Islands', 207),
('SAM', 'Western Samoa', 208),
('YEM', 'Yemen', 209),
('ZAM', 'Zambia', 210),
('ZIM', 'Zimbabwe', 211);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `layout`
--

DROP TABLE IF EXISTS `layout`;
CREATE TABLE IF NOT EXISTS `layout` (
  `xLayout` int(11) NOT NULL AUTO_INCREMENT,
  `TypTL` int(11) NOT NULL DEFAULT '0',
  `TextTL` varchar(255) NOT NULL DEFAULT '',
  `BildTL` varchar(255) NOT NULL DEFAULT '',
  `TypTC` int(11) NOT NULL DEFAULT '0',
  `TextTC` varchar(255) NOT NULL DEFAULT '',
  `BildTC` varchar(255) NOT NULL DEFAULT '',
  `TypTR` int(11) NOT NULL DEFAULT '0',
  `TextTR` varchar(255) NOT NULL DEFAULT '',
  `BildTR` varchar(255) NOT NULL DEFAULT '',
  `TypBL` int(11) NOT NULL DEFAULT '0',
  `TextBL` varchar(255) NOT NULL DEFAULT '',
  `BildBL` varchar(255) NOT NULL DEFAULT '',
  `TypBC` int(11) NOT NULL DEFAULT '0',
  `TextBC` varchar(255) NOT NULL DEFAULT '',
  `BildBC` varchar(255) NOT NULL DEFAULT '',
  `TypBR` int(11) NOT NULL DEFAULT '0',
  `TextBR` varchar(255) NOT NULL DEFAULT '',
  `BildBR` varchar(255) NOT NULL DEFAULT '',
  `xMeeting` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xLayout`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `layout`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `meeting`
--

DROP TABLE IF EXISTS `meeting`;
CREATE TABLE IF NOT EXISTS `meeting` (
  `xMeeting` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(60) NOT NULL DEFAULT '',
  `Ort` varchar(20) NOT NULL DEFAULT '',
  `DatumVon` date NOT NULL DEFAULT '0000-00-00',
  `DatumBis` date DEFAULT NULL,
  `Nummer` varchar(20) NOT NULL DEFAULT '',
  `ProgrammModus` int(1) NOT NULL DEFAULT '0',
  `Online` enum('y','n') NOT NULL DEFAULT 'y',
  `Organisator` varchar(200) NOT NULL DEFAULT '',
  `Zeitmessung` enum('no','omega','alge') NOT NULL DEFAULT 'no',
  `Passwort` varchar(50) NOT NULL DEFAULT '',
  `xStadion` int(11) NOT NULL DEFAULT '0',
  `xControl` int(11) NOT NULL DEFAULT '0',
  `Startgeld` float NOT NULL DEFAULT '0',
  `StartgeldReduktion` float NOT NULL DEFAULT '0',
  `Haftgeld` float NOT NULL DEFAULT '0',
  `Saison` enum('','I','O') NOT NULL DEFAULT '',
  `AutoRangieren` enum('n','y') NOT NULL DEFAULT 'n',
  `UKC` enum('y','n') DEFAULT 'n',
  `StatusChanged` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`xMeeting`),
  KEY `Name` (`Name`),
  KEY `xStadion` (`xStadion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `meeting`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `omega_typ`
--

DROP TABLE IF EXISTS `omega_typ`;
CREATE TABLE IF NOT EXISTS `omega_typ` (
  `xOMEGA_Typ` int(11) NOT NULL DEFAULT '0',
  `OMEGA_Name` varchar(15) NOT NULL DEFAULT '',
  `OMEGA_Kurzname` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`xOMEGA_Typ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `omega_typ`
--

INSERT INTO `omega_typ` (`xOMEGA_Typ`, `OMEGA_Name`, `OMEGA_Kurzname`) VALUES
(1, '', '0001'),
(2, 'Handstoppung', 'Hnd'),
(3, 'ohne Limite', 'o.Li'),
(4, 'Hürden', 'Hü'),
(5, 'Gehen', 'Geh'),
(6, 'Steeple', 'Stpl');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `region`
--

DROP TABLE IF EXISTS `region`;
CREATE TABLE IF NOT EXISTS `region` (
  `xRegion` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL DEFAULT '',
  `Anzeige` varchar(6) NOT NULL DEFAULT '',
  `Sortierwert` int(11) NOT NULL DEFAULT '0',
  `UKC` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`xRegion`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Daten für Tabelle `region`
--

INSERT INTO `region` (`xRegion`, `Name`, `Anzeige`, `Sortierwert`, `UKC`) VALUES
(1, 'Aargau', 'AG', 100, 'n'),
(2, 'Appenzell Ausserrhoden', 'AR', 101, 'n'),
(3, 'Appenzell Innerrhoden', 'AI', 102, 'n'),
(4, 'Basel-Landschaft', 'BL', 103, 'n'),
(5, 'Basel-Stadt', 'BS', 104, 'n'),
(6, 'Bern', 'BE', 105, 'n'),
(7, 'Freiburg', 'FR', 106, 'n'),
(8, 'Genf', 'GE', 107, 'n'),
(9, 'Glarus', 'GL', 108, 'n'),
(10, 'Graubünden', 'GR', 109, 'n'),
(11, 'Jura', 'JU', 110, 'n'),
(12, 'Luzern', 'LU', 111, 'n'),
(13, 'Neuenburg', 'NE', 112, 'n'),
(14, 'Nidwalden', 'NW', 113, 'n'),
(15, 'Obwalden', 'OW', 114, 'n'),
(16, 'Sankt Gallen', 'SG', 115, 'n'),
(17, 'Schaffhausen', 'SH', 116, 'n'),
(18, 'Schwyz', 'SZ', 117, 'n'),
(19, 'Solothurn', 'SO', 118, 'n'),
(20, 'Thurgau', 'TG', 119, 'n'),
(21, 'Tessin', 'TI', 120, 'n'),
(22, 'Uri', 'UR', 121, 'n'),
(23, 'Wallis', 'VS', 122, 'n'),
(24, 'Waadt', 'VD', 123, 'n'),
(25, 'Zug', 'ZG', 124, 'n'),
(26, 'Zürich', 'ZH', 125, 'n'),
(27, 'Liechtenstein', 'FL', 126, 'y');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `resultat`
--

DROP TABLE IF EXISTS `resultat`;
CREATE TABLE IF NOT EXISTS `resultat` (
  `xResultat` int(11) NOT NULL AUTO_INCREMENT,
  `Leistung` int(9) NOT NULL DEFAULT '0',
  `Info` char(5) NOT NULL DEFAULT '-',
  `Punkte` float NOT NULL DEFAULT '0',
  `xSerienstart` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xResultat`),
  KEY `Leistung` (`Leistung`),
  KEY `Serienstart` (`xSerienstart`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `resultat`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runde`
--

DROP TABLE IF EXISTS `runde`;
CREATE TABLE IF NOT EXISTS `runde` (
  `xRunde` int(11) NOT NULL AUTO_INCREMENT,
  `Datum` date NOT NULL DEFAULT '0000-00-00',
  `Startzeit` time NOT NULL DEFAULT '00:00:00',
  `Appellzeit` time NOT NULL DEFAULT '00:00:00',
  `Stellzeit` time NOT NULL DEFAULT '00:00:00',
  `Status` int(11) NOT NULL DEFAULT '0',
  `Speakerstatus` int(11) NOT NULL DEFAULT '0',
  `StatusZeitmessung` tinyint(4) NOT NULL DEFAULT '0',
  `StatusUpload` tinyint(4) NOT NULL DEFAULT '0',
  `QualifikationSieger` tinyint(4) NOT NULL DEFAULT '0',
  `QualifikationLeistung` tinyint(4) NOT NULL DEFAULT '0',
  `Bahnen` tinyint(4) NOT NULL DEFAULT '0',
  `Versuche` tinyint(4) NOT NULL DEFAULT '0',
  `Gruppe` char(2) NOT NULL DEFAULT '',
  `xRundentyp` int(11) DEFAULT NULL,
  `xWettkampf` int(11) NOT NULL DEFAULT '0',
  `nurBestesResultat` enum('y','n') NOT NULL DEFAULT 'n',
  `StatusChanged` enum('y','n') NOT NULL DEFAULT 'y',
  `Endkampf` enum('0','1') NOT NULL DEFAULT '0',
  `Finalisten` tinyint(4) DEFAULT '8',
  `FinalNach` tinyint(4) DEFAULT '3',
  `Drehen` varchar(20) DEFAULT '3',
  `StatusUploadUKC` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`xRunde`),
  KEY `xWettkampf` (`xWettkampf`),
  KEY `Zeit` (`Datum`,`Startzeit`),
  KEY `Status` (`Status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `runde`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rundenlog`
--

DROP TABLE IF EXISTS `rundenlog`;
CREATE TABLE IF NOT EXISTS `rundenlog` (
  `xRundenlog` int(11) NOT NULL AUTO_INCREMENT,
  `Zeit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Ereignis` varchar(255) NOT NULL DEFAULT '',
  `xRunde` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xRundenlog`),
  KEY `Zeit` (`Zeit`),
  KEY `Runde` (`xRunde`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `rundenlog`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rundenset`
--

DROP TABLE IF EXISTS `rundenset`;
CREATE TABLE IF NOT EXISTS `rundenset` (
  `xRundenset` int(11) NOT NULL DEFAULT '0',
  `xMeeting` int(11) NOT NULL DEFAULT '0',
  `xRunde` int(11) NOT NULL DEFAULT '0',
  `Hauptrunde` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xRundenset`,`xMeeting`,`xRunde`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `rundenset`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rundentyp_de`
--

DROP TABLE IF EXISTS `rundentyp_de`;
CREATE TABLE IF NOT EXISTS `rundentyp_de` (
  `xRundentyp` int(11) NOT NULL AUTO_INCREMENT,
  `Typ` char(2) NOT NULL DEFAULT '',
  `Name` varchar(20) NOT NULL DEFAULT '',
  `Wertung` tinyint(4) DEFAULT '0',
  `Code` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`xRundentyp`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `Typ` (`Typ`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `rundentyp_de`
--

INSERT INTO `rundentyp_de` (`xRundentyp`, `Typ`, `Name`, `Wertung`, `Code`) VALUES
(1, 'V', 'Vorlauf', 0, 'V'),
(2, 'F', 'Final', 0, 'F'),
(3, 'Z', 'Zwischenlauf', 0, 'Z'),
(5, 'Q', 'Qualifikation', 1, 'Q'),
(6, 'S', 'Serie', 0, 'S'),
(7, 'X', 'Halbfinal', 0, 'X'),
(8, 'D', 'Mehrkampf', 1, 'D'),
(9, '0', '(ohne)', 2, '0'),
(10, 'FZ', 'Zeitläufe', 1, 'FZ');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rundentyp_fr`
--

DROP TABLE IF EXISTS `rundentyp_fr`;
CREATE TABLE IF NOT EXISTS `rundentyp_fr` (
  `xRundentyp` int(11) NOT NULL AUTO_INCREMENT,
  `Typ` char(2) NOT NULL DEFAULT '',
  `Name` varchar(20) NOT NULL DEFAULT '',
  `Wertung` tinyint(4) DEFAULT '0',
  `Code` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`xRundentyp`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `Typ` (`Typ`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `rundentyp_fr`
--

INSERT INTO `rundentyp_fr` (`xRundentyp`, `Typ`, `Name`, `Wertung`, `Code`) VALUES
(1, 'V', 'Eliminatoire', 0, 'V'),
(2, 'F', 'Finale', 0, 'F'),
(3, 'Z', 'Second Tour', 0, 'Z'),
(5, 'Q', 'Qualification', 1, 'Q'),
(6, 'S', 'Série', 0, 'S'),
(7, 'X', 'Demi-finale', 0, 'X'),
(8, 'D', 'Concour multiple', 1, 'D'),
(9, '0', '(sans)', 2, '0'),
(10, 'FZ', 'Courses au temps', 1, 'FZ');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rundentyp_it`
--

DROP TABLE IF EXISTS `rundentyp_it`;
CREATE TABLE IF NOT EXISTS `rundentyp_it` (
  `xRundentyp` int(11) NOT NULL AUTO_INCREMENT,
  `Typ` char(2) NOT NULL DEFAULT '',
  `Name` varchar(20) NOT NULL DEFAULT '',
  `Wertung` tinyint(4) DEFAULT '0',
  `Code` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`xRundentyp`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `Typ` (`Typ`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `rundentyp_it`
--

INSERT INTO `rundentyp_it` (`xRundentyp`, `Typ`, `Name`, `Wertung`, `Code`) VALUES
(1, 'V', 'Eliminatoria', 0, 'V'),
(2, 'F', 'Finale', 0, 'F'),
(3, 'Z', 'Secondo tour', 0, 'Z'),
(5, 'Q', 'Qualificazione', 1, 'Q'),
(6, 'S', 'Serie', 0, 'S'),
(7, 'X', 'Semifinale', 0, 'X'),
(8, 'D', 'Gara multipla', 1, 'D'),
(9, '0', '(senza)', 2, '0'),
(10, 'FZ', 'corsa a tempo', 1, 'FZ');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `serie`
--

DROP TABLE IF EXISTS `serie`;
CREATE TABLE IF NOT EXISTS `serie` (
  `xSerie` int(11) NOT NULL AUTO_INCREMENT,
  `Bezeichnung` char(2) NOT NULL DEFAULT '',
  `Wind` varchar(5) DEFAULT '',
  `Film` int(11) DEFAULT '0',
  `Status` int(11) NOT NULL DEFAULT '0',
  `Handgestoppt` tinyint(4) NOT NULL DEFAULT '0',
  `xRunde` int(11) NOT NULL DEFAULT '0',
  `xAnlage` int(11) DEFAULT NULL,
  `TVName` varchar(70) DEFAULT NULL,
  `MaxAthlet` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xSerie`),
  UNIQUE KEY `Bezeichnung` (`xRunde`,`Bezeichnung`),
  KEY `Runde` (`xRunde`),
  KEY `Anlage` (`xAnlage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `serie`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `serienstart`
--

DROP TABLE IF EXISTS `serienstart`;
CREATE TABLE IF NOT EXISTS `serienstart` (
  `xSerienstart` int(11) NOT NULL AUTO_INCREMENT,
  `Position` int(11) NOT NULL DEFAULT '0',
  `Bahn` int(11) NOT NULL DEFAULT '0',
  `Rang` int(11) NOT NULL DEFAULT '0',
  `Qualifikation` tinyint(4) NOT NULL DEFAULT '0',
  `xSerie` int(11) NOT NULL DEFAULT '0',
  `xStart` int(11) NOT NULL DEFAULT '0',
  `RundeZusammen` int(11) NOT NULL DEFAULT '0',
  `Bemerkung` char(5) NOT NULL DEFAULT '',
  `Position2` int(11) NOT NULL DEFAULT '0',
  `Position3` int(11) NOT NULL DEFAULT '0',
  `AktivAthlet` enum('y','n') NOT NULL DEFAULT 'n',
  `Starthoehe` int(11) DEFAULT '0',
  PRIMARY KEY (`xSerienstart`),
  UNIQUE KEY `Serienstart` (`xSerie`,`xStart`),
  KEY `Rang` (`Rang`),
  KEY `Qualifikation` (`Qualifikation`),
  KEY `xSerie` (`xSerie`),
  KEY `xStart` (`xStart`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `serienstart`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stadion`
--

DROP TABLE IF EXISTS `stadion`;
CREATE TABLE IF NOT EXISTS `stadion` (
  `xStadion` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL DEFAULT '',
  `Bahnen` tinyint(4) NOT NULL DEFAULT '6',
  `BahnenGerade` tinyint(4) NOT NULL DEFAULT '8',
  `Ueber1000m` enum('y','n') NOT NULL DEFAULT 'n',
  `Halle` enum('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`xStadion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `stadion`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `staffel`
--

DROP TABLE IF EXISTS `staffel`;
CREATE TABLE IF NOT EXISTS `staffel` (
  `xStaffel` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(40) NOT NULL DEFAULT '',
  `xVerein` int(11) NOT NULL DEFAULT '0',
  `xMeeting` int(11) NOT NULL DEFAULT '0',
  `xKategorie` int(11) NOT NULL DEFAULT '0',
  `xTeam` int(11) NOT NULL DEFAULT '0',
  `Athleticagen` enum('y','n') NOT NULL DEFAULT 'n',
  `Startnummer` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xStaffel`),
  KEY `xMeeting` (`xMeeting`),
  KEY `xVerein` (`xVerein`),
  KEY `Name` (`Name`(10)),
  KEY `xTeam` (`xTeam`),
  KEY `Startnummer` (`Startnummer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `staffel`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `staffelathlet`
--

DROP TABLE IF EXISTS `staffelathlet`;
CREATE TABLE IF NOT EXISTS `staffelathlet` (
  `xStaffelstart` int(11) NOT NULL DEFAULT '0',
  `xAthletenstart` int(11) NOT NULL DEFAULT '0',
  `xRunde` int(11) NOT NULL DEFAULT '0',
  `Position` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xStaffelstart`,`xAthletenstart`,`xRunde`),
  UNIQUE KEY `Reihenfolge` (`xStaffelstart`,`Position`,`xRunde`),
  KEY `Position` (`Position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `staffelathlet`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `start`
--

DROP TABLE IF EXISTS `start`;
CREATE TABLE IF NOT EXISTS `start` (
  `xStart` int(11) NOT NULL AUTO_INCREMENT,
  `Anwesend` smallint(1) NOT NULL DEFAULT '0',
  `Bestleistung` int(11) NOT NULL DEFAULT '0',
  `Bezahlt` enum('y','n') NOT NULL DEFAULT 'n',
  `Erstserie` enum('y','n') NOT NULL DEFAULT 'n',
  `xWettkampf` int(11) NOT NULL DEFAULT '0',
  `xAnmeldung` int(11) NOT NULL DEFAULT '0',
  `xStaffel` int(11) NOT NULL DEFAULT '0',
  `BaseEffort` enum('y','n') NOT NULL DEFAULT 'y',
  `VorjahrLeistung` int(11) DEFAULT '0',
  PRIMARY KEY (`xStart`),
  UNIQUE KEY `start` (`xWettkampf`,`xAnmeldung`,`xStaffel`),
  KEY `Staffel` (`xStaffel`),
  KEY `Anmeldung` (`xAnmeldung`),
  KEY `Wettkampf` (`xWettkampf`),
  KEY `WettkampfAnmeldung` (`xAnmeldung`,`xWettkampf`),
  KEY `WettkampfStaffel` (`xStaffel`,`xWettkampf`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `start`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sys_backuptabellen`
--

DROP TABLE IF EXISTS `sys_backuptabellen`;
CREATE TABLE IF NOT EXISTS `sys_backuptabellen` (
  `xBackup` int(11) NOT NULL AUTO_INCREMENT,
  `Tabelle` varchar(50) DEFAULT NULL,
  `SelectSQL` text,
  PRIMARY KEY (`xBackup`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Daten für Tabelle `sys_backuptabellen`
--

INSERT INTO `sys_backuptabellen` (`xBackup`, `Tabelle`, `SelectSQL`) VALUES
(1, 'anlage', 'SELECT * FROM anlage'),
(2, 'anmeldung', 'SELECT * FROM anmeldung WHERE xMeeting = ''%d'''),
(3, 'athlet', 'SELECT * FROM athlet'),
(5, 'base_account', 'SELECT * FROM base_account'),
(6, 'base_athlete', 'SELECT * FROM base_athlete'),
(7, 'base_log', 'SELECT * FROM base_log'),
(8, 'base_performance', 'SELECT * FROM base_performance'),
(9, 'base_relay', 'SELECT * FROM base_relay'),
(10, 'base_svm', 'SELECT * FROM base_svm'),
(11, 'disziplin', 'SELECT * FROM disziplin'),
(13, 'kategorie', 'SELECT * FROM kategorie'),
(16, 'layout', 'SELECT * FROM layout WHERE xMeeting = ''%d'''),
(17, 'meeting', 'SELECT * FROM meeting WHERE xMeeting=''%d'''),
(18, 'omega_typ', 'SELECT * FROM omega_typ'),
(19, 'region', 'SELECT * FROM region'),
(20, 'resultat', 'SELECT\r\n    resultat.*\r\nFROM\r\n    athletica.resultat\r\n    LEFT JOIN athletica.serienstart \r\n        ON (resultat.xSerienstart = serienstart.xSerienstart)\r\n    LEFT JOIN athletica.start \r\n        ON (serienstart.xStart = start.xStart)\r\n    LEFT JOIN athletica.wettkampf \r\n        ON (start.xWettkampf = wettkampf.xWettkampf)\r\nWHERE (wettkampf.xMeeting =''%d'') \r\nAND xResultat IS NOT NULL;'),
(21, 'runde', 'SELECT\r\n    runde.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\nWHERE (wettkampf.xMeeting =''%d'') \r\nAND xRunde IS NOT NULL;'),
(22, 'rundenlog', 'SELECT\r\n    rundenlog.*\r\nFROM\r\n    athletica.runde\r\n    JOIN athletica.rundenlog \r\n        ON (runde.xRunde = rundenlog.xRunde)\r\n    JOIN athletica.wettkampf \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\nWHERE (wettkampf.xMeeting =''%d'') \r\nAND xRundenlog IS NOT NULL;'),
(23, 'rundenset', 'SELECT * FROM rundenset WHERE xMeeting = ''%d'''),
(24, 'rundentyp', 'SELECT * FROM rundentyp'),
(25, 'serie', 'SELECT\r\n    serie.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\n    LEFT JOIN athletica.serie \r\n        ON (runde.xRunde = serie.xRunde)\r\nWHERE (wettkampf.xMeeting =''%d'') \r\nAND xSerie IS NOT NULL;'),
(26, 'serienstart', 'SELECT\r\n    serienstart.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\n    LEFT JOIN athletica.serie \r\n        ON (runde.xRunde = serie.xRunde)\r\n    LEFT JOIN athletica.serienstart \r\n        ON (serie.xSerie = serienstart.xSerie)\r\nWHERE (wettkampf.xMeeting =''%d'') \r\nAND xSerienstart IS NOT NULL;'),
(27, 'stadion', 'SELECT * FROM stadion'),
(28, 'staffel', 'SELECT * FROM staffel WHERE xMeeting = ''%d'''),
(29, 'staffelathlet', 'SELECT\r\n    staffelathlet.*\r\nFROM\r\n    athletica.staffelathlet\r\n    INNER JOIN athletica.runde \r\n        ON (staffelathlet.xRunde = runde.xRunde)\r\n    INNER JOIN athletica.wettkampf \r\n        ON (runde.xWettkampf = wettkampf.xWettkampf)\r\nWHERE (wettkampf.xMeeting =''%d'') \r\nAND xStaffelstart IS NOT NULL;'),
(30, 'start', 'SELECT\r\n    start.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.start \r\n        ON (wettkampf.xWettkampf = start.xWettkampf)\r\nWHERE (wettkampf.xMeeting =''%d'') \r\nAND xStart IS NOT NULL;'),
(31, 'team', 'SELECT * FROM team WHERE xMeeting = ''%d'''),
(32, 'teamsm', 'SELECT * FROM teamsm WHERE xMeeting = ''%d'''),
(33, 'teamsmathlet', 'SELECT\r\n    teamsmathlet.*\r\nFROM\r\n    athletica.teamsmathlet\r\n    LEFT JOIN athletica.anmeldung \r\n        ON (teamsmathlet.xAnmeldung = anmeldung.xAnmeldung)\r\nWHERE (anmeldung.xMeeting =''%d'') \r\nAND xTeamsm IS NOT NULL;'),
(34, 'verein', 'SELECT * FROM verein'),
(35, 'wertungstabelle', 'SELECT * FROM wertungstabelle'),
(36, 'wertungstabelle_punkte', 'SELECT * FROM wertungstabelle_punkte'),
(37, 'wettkampf', 'SELECT * FROM wettkampf WHERE xMeeting = ''%d'''),
(38, 'zeitmessung', 'SELECT * FROM zeitmessung WHERE xMeeting = ''%d''');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `xTeam` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) NOT NULL DEFAULT '',
  `Athleticagen` enum('y','n') NOT NULL DEFAULT 'n',
  `xKategorie` int(11) NOT NULL DEFAULT '0',
  `xMeeting` int(11) NOT NULL DEFAULT '0',
  `xVerein` int(11) NOT NULL DEFAULT '0',
  `xKategorie_svm` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xTeam`),
  UNIQUE KEY `MeetingKatName` (`xMeeting`,`xKategorie`,`Name`,`xKategorie_svm`),
  KEY `Name` (`Name`),
  KEY `xKategorie` (`xKategorie`),
  KEY `xVerein` (`xVerein`),
  KEY `xMeeting` (`xMeeting`),
  KEY `xKategorie_svm` (`xKategorie_svm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `team`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `teamsm`
--

DROP TABLE IF EXISTS `teamsm`;
CREATE TABLE IF NOT EXISTS `teamsm` (
  `xTeamsm` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `xKategorie` int(11) NOT NULL DEFAULT '0',
  `xVerein` int(11) NOT NULL DEFAULT '0',
  `xWettkampf` int(11) NOT NULL DEFAULT '0',
  `xMeeting` int(11) NOT NULL DEFAULT '0',
  `Startnummer` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xTeamsm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `teamsm`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `teamsmathlet`
--

DROP TABLE IF EXISTS `teamsmathlet`;
CREATE TABLE IF NOT EXISTS `teamsmathlet` (
  `xTeamsm` int(11) NOT NULL DEFAULT '0',
  `xAnmeldung` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xTeamsm`,`xAnmeldung`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `teamsmathlet`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `verein`
--

DROP TABLE IF EXISTS `verein`;
CREATE TABLE IF NOT EXISTS `verein` (
  `xVerein` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Sortierwert` varchar(100) NOT NULL DEFAULT '0',
  `xCode` varchar(30) NOT NULL DEFAULT '',
  `Geloescht` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xVerein`),
  UNIQUE KEY `Name` (`Name`),
  KEY `Sortierwert` (`Sortierwert`),
  KEY `xCode` (`xCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000000 ;

--
-- Daten für Tabelle `verein`
--

INSERT INTO `verein` (`xVerein`, `Name`, `Sortierwert`, `xCode`, `Geloescht`) VALUES
(999999, '', '', 'UKC', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `videowand`
--

DROP TABLE IF EXISTS `videowand`;
CREATE TABLE IF NOT EXISTS `videowand` (
  `xVideowand` int(11) NOT NULL AUTO_INCREMENT,
  `xMeeting` int(11) NOT NULL DEFAULT '0',
  `X` int(11) NOT NULL DEFAULT '0',
  `Y` int(11) NOT NULL DEFAULT '0',
  `InhaltArt` enum('dyn','stat') NOT NULL DEFAULT 'dyn',
  `InhaltStatisch` text NOT NULL,
  `InhaltDynamisch` text NOT NULL,
  `Aktualisierung` int(11) NOT NULL DEFAULT '0',
  `Status` enum('black','white','active') NOT NULL DEFAULT 'active',
  `Hintergrund` varchar(6) NOT NULL DEFAULT '',
  `Fordergrund` varchar(6) NOT NULL DEFAULT '',
  `Bildnr` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xVideowand`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `videowand`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wertungstabelle`
--

DROP TABLE IF EXISTS `wertungstabelle`;
CREATE TABLE IF NOT EXISTS `wertungstabelle` (
  `xWertungstabelle` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`xWertungstabelle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `wertungstabelle`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wertungstabelle_punkte`
--

DROP TABLE IF EXISTS `wertungstabelle_punkte`;
CREATE TABLE IF NOT EXISTS `wertungstabelle_punkte` (
  `xWertungstabelle_Punkte` int(11) NOT NULL AUTO_INCREMENT,
  `xWertungstabelle` int(11) NOT NULL DEFAULT '0',
  `xDisziplin` int(11) NOT NULL DEFAULT '0',
  `Geschlecht` enum('W','M') NOT NULL DEFAULT 'M',
  `Leistung` varchar(50) NOT NULL DEFAULT '',
  `Punkte` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`xWertungstabelle_Punkte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `wertungstabelle_punkte`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wettkampf`
--

DROP TABLE IF EXISTS `wettkampf`;
CREATE TABLE IF NOT EXISTS `wettkampf` (
  `xWettkampf` int(11) NOT NULL AUTO_INCREMENT,
  `Typ` tinyint(4) NOT NULL DEFAULT '0',
  `Haftgeld` float unsigned NOT NULL DEFAULT '0',
  `Startgeld` float unsigned NOT NULL DEFAULT '0',
  `Punktetabelle` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Punkteformel` varchar(20) NOT NULL DEFAULT '0',
  `Windmessung` tinyint(4) NOT NULL DEFAULT '0',
  `Info` varchar(50) DEFAULT NULL,
  `Zeitmessung` tinyint(4) NOT NULL DEFAULT '0',
  `ZeitmessungAuto` tinyint(4) NOT NULL DEFAULT '0',
  `xKategorie` int(11) NOT NULL DEFAULT '1',
  `xDisziplin` int(11) NOT NULL DEFAULT '1',
  `xMeeting` int(11) NOT NULL DEFAULT '1',
  `Mehrkampfcode` int(11) NOT NULL DEFAULT '0',
  `Mehrkampfende` tinyint(4) NOT NULL DEFAULT '0',
  `Mehrkampfreihenfolge` tinyint(4) NOT NULL DEFAULT '0',
  `xKategorie_svm` int(11) NOT NULL DEFAULT '0',
  `OnlineId` int(11) NOT NULL DEFAULT '0',
  `TypAenderung` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`xWettkampf`),
  KEY `xKategorie` (`xKategorie`),
  KEY `xDisziplin` (`xDisziplin`),
  KEY `xMeeting` (`xMeeting`),
  KEY `OnlineId` (`OnlineId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `wettkampf`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zeitmessung`
--

DROP TABLE IF EXISTS `zeitmessung`;
CREATE TABLE IF NOT EXISTS `zeitmessung` (
  `xZeitmessung` int(11) NOT NULL AUTO_INCREMENT,
  `OMEGA_Verbindung` enum('local','ftp') NOT NULL DEFAULT 'local',
  `OMEGA_Pfad` varchar(255) NOT NULL DEFAULT '',
  `OMEGA_Server` varchar(255) NOT NULL DEFAULT '',
  `OMEGA_Benutzer` varchar(50) NOT NULL DEFAULT '',
  `OMEGA_Passwort` varchar(50) NOT NULL DEFAULT '',
  `OMEGA_Ftppfad` varchar(255) NOT NULL DEFAULT '',
  `OMEGA_Sponsor` varchar(255) NOT NULL DEFAULT '',
  `ALGE_Typ` varchar(20) NOT NULL DEFAULT '',
  `ALGE_Ftppfad` varchar(255) NOT NULL DEFAULT '',
  `ALGE_Passwort` varchar(50) NOT NULL DEFAULT '',
  `ALGE_Benutzer` varchar(50) NOT NULL DEFAULT '',
  `ALGE_Server` varchar(255) NOT NULL DEFAULT '',
  `ALGE_Pfad` varchar(255) NOT NULL DEFAULT '',
  `ALGE_Verbindung` enum('local','ftp') NOT NULL DEFAULT 'local',
  `xMeeting` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xZeitmessung`),
  KEY `xMeeting` (`xMeeting`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `zeitmessung`
--

