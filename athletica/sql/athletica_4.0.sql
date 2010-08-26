-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 21. August 2010 um 09:55
-- Server Version: 4.0.12
-- PHP-Version: 4.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Datenbank: `athletica`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `anlage`
-- 

DROP TABLE IF EXISTS `anlage`;
CREATE TABLE `anlage` (
  `xAnlage` int(11) NOT NULL auto_increment,
  `Bezeichnung` varchar(20) NOT NULL default '',
  `Homologiert` enum('y','n') NOT NULL default 'y',
  `xStadion` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xAnlage`),
  KEY `xStadion` (`xStadion`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `anlage`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `anmeldung`
-- 

DROP TABLE IF EXISTS `anmeldung`;
CREATE TABLE `anmeldung` (
  `xAnmeldung` int(11) NOT NULL auto_increment,
  `Startnummer` smallint(5) unsigned NOT NULL default '0',
  `Erstserie` enum('y','n') NOT NULL default 'n',
  `Bezahlt` enum('y','n') NOT NULL default 'y',
  `Gruppe` char(2) NOT NULL default '',
  `BestleistungMK` float NOT NULL default '0',
  `Vereinsinfo` varchar(150) NOT NULL default '',
  `xAthlet` int(11) NOT NULL default '0',
  `xMeeting` int(11) NOT NULL default '0',
  `xKategorie` int(11) default NULL,
  `xTeam` int(11) NOT NULL default '0',
  `BaseEffortMK` enum('y','n') NOT NULL default 'n',
  `Anmeldenr_ZLV` int(11) default '0',
  PRIMARY KEY  (`xAnmeldung`),
  UNIQUE KEY `AthleteMeetingKat` (`xAthlet`,`xMeeting`,`xKategorie`),
  KEY `xAthlet` (`xAthlet`),
  KEY `xMeeting` (`xMeeting`),
  KEY `xKategorie` (`xKategorie`),
  KEY `Startnummer` (`Startnummer`),
  KEY `xTeam` (`xTeam`),
  KEY `Vereinsinfo` (`Vereinsinfo`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `anmeldung`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `athlet`
-- 

DROP TABLE IF EXISTS `athlet`;
CREATE TABLE `athlet` (
  `xAthlet` int(11) NOT NULL auto_increment,
  `Name` varchar(25) NOT NULL default '',
  `Vorname` varchar(25) NOT NULL default '',
  `Jahrgang` year(4) default NULL,
  `xVerein` int(11) NOT NULL default '0',
  `xVerein2` int(11) NOT NULL default '0',
  `Lizenznummer` int(11) NOT NULL default '0',
  `Geschlecht` enum('m','w') NOT NULL default 'm',
  `Land` char(3) NOT NULL default '',
  `Geburtstag` date NOT NULL default '0000-00-00',
  `Athleticagen` enum('y','n') NOT NULL default 'n',
  `Bezahlt` enum('y','n') NOT NULL default 'n',
  `xRegion` int(11) NOT NULL default '0',
  `Lizenztyp` tinyint(2) NOT NULL default '0',
  `Manuell` int(1) NOT NULL default '0',
  PRIMARY KEY  (`xAthlet`),
  UNIQUE KEY `Athlet` (`Name`,`Vorname`,`Jahrgang`,`xVerein`),
  KEY `Name` (`Name`),
  KEY `xVerein` (`xVerein`),
  KEY `Lizenznummer` (`Lizenznummer`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `athlet`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `base_account`
-- 

DROP TABLE IF EXISTS `base_account`;
CREATE TABLE `base_account` (
  `account_code` varchar(30) NOT NULL default '',
  `account_name` varchar(255) NOT NULL default '',
  `account_short` varchar(255) NOT NULL default '',
  `account_type` varchar(100) NOT NULL default '',
  `lg` varchar(100) NOT NULL default '',
  KEY `account_code` (`account_code`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `base_account`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `base_athlete`
-- 

DROP TABLE IF EXISTS `base_athlete`;
CREATE TABLE `base_athlete` (
  `id_athlete` int(11) NOT NULL auto_increment,
  `license` int(11) NOT NULL default '0',
  `license_paid` enum('y','n') NOT NULL default 'y',
  `license_cat` varchar(4) NOT NULL default '',
  `lastname` varchar(100) NOT NULL default '',
  `firstname` varchar(100) NOT NULL default '',
  `sex` enum('m','w') NOT NULL default 'm',
  `nationality` char(3) NOT NULL default '',
  `account_code` varchar(30) NOT NULL default '',
  `second_account_code` varchar(30) NOT NULL default '',
  `birth_date` date NOT NULL default '0000-00-00',
  `account_info` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`id_athlete`),
  KEY `account_code` (`account_code`),
  KEY `second_account_code` (`second_account_code`),
  KEY `license` (`license`),
  KEY `lastname` (`lastname`),
  KEY `firstname` (`firstname`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `base_athlete`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `base_log`
-- 

DROP TABLE IF EXISTS `base_log`;
CREATE TABLE `base_log` (
  `id_log` int(11) NOT NULL auto_increment,
  `type` varchar(50) NOT NULL default '',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `global_last_change` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id_log`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `base_log`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur fÃ¼r Tabelle `base_performance`
-- 

DROP TABLE IF EXISTS `base_performance`;
CREATE TABLE `base_performance` (
  `id_performance` int(11) NOT NULL auto_increment,
  `id_athlete` int(11) NOT NULL default '0',
  `discipline` smallint(6) NOT NULL default '0',
  `category` varchar(10) NOT NULL default '',
  `best_effort` varchar(15) NOT NULL default '',
  `best_effort_date` date NOT NULL default '0000-00-00',
  `best_effort_event` varchar(100) NOT NULL default '',
  `season_effort` varchar(15) NOT NULL default '',
  `season_effort_date` date NOT NULL default '0000-00-00',
  `season_effort_event` varchar(100) NOT NULL default '',
  `notification_effort` varchar(15) NOT NULL default '',
  `notification_effort_date` date NOT NULL default '0000-00-00',
  `notification_effort_event` varchar(100) NOT NULL default '',
  `season` enum('I','O') NOT NULL default 'O',
  PRIMARY KEY  (`id_performance`),
  UNIQUE KEY `id_athlete_discipline_season` (`id_athlete`,`discipline`,`season`),
  KEY `id_athlete` (`id_athlete`),
  KEY `discipline` (`discipline`),
  KEY `season` (`season`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `base_performance`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `base_relay`
-- 

DROP TABLE IF EXISTS `base_relay`;
CREATE TABLE `base_relay` (
  `id_relay` int(11) NOT NULL default '0',
  `is_athletica_gen` enum('y','n') NOT NULL default 'y',
  `relay_name` varchar(255) NOT NULL default '',
  `category` varchar(10) NOT NULL default '',
  `discipline` varchar(10) NOT NULL default '',
  `account_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_relay`),
  KEY `account_code` (`account_code`),
  KEY `discipline` (`discipline`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `base_relay`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `base_svm`
-- 

DROP TABLE IF EXISTS `base_svm`;
CREATE TABLE `base_svm` (
  `id_svm` int(11) NOT NULL default '0',
  `is_athletica_gen` enum('y','n') NOT NULL default 'y',
  `svm_name` varchar(255) NOT NULL default '',
  `svm_category` varchar(10) NOT NULL default '',
  `account_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_svm`),
  KEY `account_code` (`account_code`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `base_svm`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `disziplin`
-- 

DROP TABLE IF EXISTS `disziplin`;
CREATE TABLE `disziplin` (
  `xDisziplin` int(11) NOT NULL auto_increment,
  `Kurzname` varchar(15) NOT NULL default '',
  `Name` varchar(40) NOT NULL default '',
  `Anzeige` int(11) NOT NULL default '1',
  `Seriegroesse` int(4) NOT NULL default '0',
  `Staffellaeufer` int(11) default NULL,
  `Typ` int(11) NOT NULL default '0',
  `Appellzeit` time NOT NULL default '00:00:00',
  `Stellzeit` time NOT NULL default '00:00:00',
  `Strecke` float NOT NULL default '0',
  `Code` int(11) NOT NULL default '0',
  `xOMEGA_Typ` int(11) NOT NULL default '0',
  `aktiv` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`xDisziplin`),
  UNIQUE KEY `Kurzname` (`Kurzname`),
  KEY `Anzeige` (`Anzeige`),
  KEY `Staffel` (`Staffellaeufer`),
  KEY `Code` (`Code`)
) TYPE=MyISAM AUTO_INCREMENT=190 ;

-- 
-- Daten für Tabelle `disziplin`
-- 

INSERT INTO `disziplin` (`xDisziplin`, `Kurzname`, `Name`, `Anzeige`, `Seriegroesse`, `Staffellaeufer`, `Typ`, `Appellzeit`, `Stellzeit`, `Strecke`, `Code`, `xOMEGA_Typ`, `aktiv`) VALUES 
(38, '50', '50 m', 10, 6, 0, 2, '01:00:00', '00:15:00', 50, 10, 1, 'y'),
(39, '55', '55 m', 20, 6, 0, 2, '01:00:00', '00:15:00', 55, 20, 1, 'y'),
(40, '60', '60 m', 30, 6, 0, 2, '01:00:00', '00:15:00', 60, 30, 1, 'y'),
(41, '80', '80 m', 35, 6, 0, 1, '01:00:00', '00:15:00', 80, 35, 1, 'y'),
(42, '100', '100 m', 40, 6, 0, 1, '01:00:00', '00:15:00', 100, 40, 1, 'y'),
(43, '150', '150 m', 48, 6, 0, 1, '01:00:00', '00:15:00', 150, 48, 1, 'y'),
(44, '200', '200 m', 50, 6, 0, 1, '01:00:00', '00:15:00', 200, 50, 1, 'y'),
(45, '300', '300 m', 60, 6, 0, 2, '01:00:00', '00:15:00', 300, 60, 1, 'y'),
(46, '400', '400 m', 70, 6, 0, 2, '01:00:00', '00:15:00', 400, 70, 1, 'y'),
(47, '600', '600 m', 80, 6, 0, 7, '01:00:00', '00:15:00', 600, 80, 1, 'y'),
(48, '800', '800 m', 90, 6, 0, 7, '01:00:00', '00:15:00', 800, 90, 1, 'y'),
(49, '1000', '1000 m', 100, 6, 0, 7, '01:00:00', '00:15:00', 1000, 100, 1, 'y'),
(50, '1500', '1500 m', 110, 6, 0, 7, '01:00:00', '00:15:00', 1500, 110, 1, 'y'),
(51, '1MEILE', '1 Meile', 120, 6, 0, 7, '01:00:00', '00:15:00', 1609, 120, 1, 'y'),
(52, '2000', '2000 m', 130, 6, 0, 7, '01:00:00', '00:15:00', 2000, 130, 1, 'y'),
(53, '3000', '3000 m', 140, 6, 0, 7, '01:00:00', '00:15:00', 3000, 140, 1, 'y'),
(54, '5000', '5000 m', 160, 6, 0, 7, '01:00:00', '00:15:00', 5000, 160, 1, 'y'),
(55, '10000', '10 000 m', 170, 6, 0, 7, '01:00:00', '00:15:00', 10000, 170, 1, 'y'),
(56, '20000', '20 000 m', 180, 6, 0, 7, '01:00:00', '00:15:00', 20000, 180, 1, 'y'),
(57, '1STUNDE', '1 Stunde', 171, 6, 0, 7, '01:00:00', '00:15:00', 1, 182, 1, 'y'),
(58, '25000', '25 000 m', 181, 6, 0, 7, '01:00:00', '00:15:00', 25000, 181, 1, 'y'),
(59, '30000', '30 000 m', 182, 6, 0, 7, '01:00:00', '00:15:00', 30000, 195, 1, 'y'),
(60, '10KM', '10 km', 186, 6, 0, 7, '01:00:00', '00:15:00', 10000, 186, 1, 'y'),
(61, 'HALBMARATH', 'Halbmarathon', 183, 6, 0, 7, '01:00:00', '00:15:00', 0, 190, 1, 'y'),
(62, 'MARATHON', 'Marathon', 184, 6, 0, 7, '01:00:00', '00:15:00', 0, 200, 1, 'y'),
(64, '50H106.7', '50 m Hürden 106.7', 232, 6, 0, 1, '01:00:00', '00:15:00', 50, 232, 4, 'y'),
(65, '50H99.1', '50 m Hürden 99.1', 233, 6, 0, 2, '01:00:00', '00:15:00', 50, 233, 4, 'y'),
(66, '50H91.4', '50 m Hürden 91.4', 234, 6, 0, 2, '01:00:00', '00:15:00', 50, 234, 4, 'y'),
(67, '50H84.0', '50 m Hürden 84.0', 235, 6, 0, 2, '01:00:00', '00:15:00', 50, 235, 4, 'y'),
(68, '50H76.2', '50 m Hürden 76.2', 236, 6, 0, 2, '01:00:00', '00:15:00', 50, 236, 4, 'y'),
(69, '60H106.7', '60 m Hürden 106.7', 252, 6, 0, 2, '01:00:00', '00:15:00', 60, 252, 4, 'y'),
(70, '60H99.1', '60 m Hürden 99.1', 253, 6, 0, 2, '01:00:00', '00:15:00', 60, 253, 4, 'y'),
(71, '60H91.4', '60 m Hürden 91.4', 254, 6, 0, 2, '01:00:00', '00:15:00', 60, 254, 4, 'y'),
(72, '60H84.0', '60 m Hürden 84.0', 255, 6, 0, 2, '01:00:00', '00:15:00', 60, 255, 4, 'y'),
(73, '60H76.2', '60 m Hürden 76.2', 256, 6, 0, 2, '01:00:00', '00:15:00', 60, 256, 4, 'y'),
(74, '80H76.2', '80 m Hürden 76.2', 259, 6, 0, 1, '01:00:00', '00:15:00', 80, 258, 4, 'y'),
(75, '100H84.0', '100 m Hurdles', 261, 6, 0, 1, '01:00:00', '00:15:00', 100, 261, 4, 'y'),
(76, '100H76.2', '100 m Hürden 76.2', 262, 6, 0, 1, '01:00:00', '00:15:00', 100, 259, 4, 'y'),
(77, '110H106.7', '110 m Hurdles', 267, 6, 0, 1, '01:00:00', '00:15:00', 110, 271, 4, 'y'),
(78, '110H99.1', '110 m Hürden 99.1', 268, 6, 0, 1, '01:00:00', '00:15:00', 110, 269, 4, 'y'),
(79, '110H91.4', '110 m Hürden 91.4', 269, 6, 0, 1, '01:00:00', '00:15:00', 110, 268, 4, 'y'),
(80, '200H', '200 m Hürden', 280, 6, 0, 1, '01:00:00', '00:15:00', 200, 280, 4, 'y'),
(81, '300H84.0', '300 m Hürden 84.0', 290, 6, 0, 2, '01:00:00', '00:15:00', 300, 290, 4, 'y'),
(82, '300H76.2', '300 m Hürden 76.2', 291, 6, 0, 2, '01:00:00', '00:15:00', 300, 291, 4, 'y'),
(83, '400H91.4', '400 m Hurdles', 298, 6, 0, 2, '01:00:00', '00:15:00', 400, 301, 4, 'y'),
(84, '400H76.2', '400 m Hurdles', 301, 6, 0, 2, '01:00:00', '00:15:00', 400, 298, 4, 'y'),
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
(98, 'OLYMPISCHE', 'Olympische', 404, 6, 4, 3, '01:00:00', '00:15:00', 0, 601, 1, 'y'),
(99, 'AMÉRICAINE', 'Américaine', 405, 6, 4, 3, '01:00:00', '00:15:00', 0, 602, 1, 'y'),
(100, 'HOCH', 'Hoch', 310, 6, 0, 6, '01:00:00', '00:20:00', 0, 310, 1, 'y'),
(101, 'STAB', 'Pole Vault', 320, 6, 0, 6, '01:00:00', '00:20:00', 0, 320, 1, 'y'),
(102, 'WEIT', 'Long Jump', 330, 6, 0, 4, '01:00:00', '00:20:00', 0, 330, 1, 'y'),
(103, 'DREI', 'Triple Jump', 340, 6, 0, 4, '01:00:00', '00:20:00', 0, 340, 1, 'y'),
(104, 'KUGEL7.26', 'Kugel 7.26 kg', 347, 6, 0, 8, '01:00:00', '00:20:00', 0, 351, 1, 'y'),
(105, 'KUGEL6.00', 'Kugel 6.00 kg', 348, 6, 0, 8, '01:00:00', '00:20:00', 0, 348, 1, 'y'),
(106, 'KUGEL5.00', 'Kugel 5.00 kg', 349, 6, 0, 8, '01:00:00', '00:20:00', 0, 347, 1, 'y'),
(107, 'KUGEL4.00', 'Kugel 4.00 kg', 350, 6, 0, 8, '01:00:00', '00:20:00', 0, 349, 1, 'y'),
(108, 'KUGEL3.00', 'Kugel 3.00 kg', 352, 6, 0, 8, '01:00:00', '00:20:00', 0, 352, 1, 'y'),
(109, 'KUGEL2.50', 'Kugel 2.50 kg', 353, 6, 0, 8, '01:00:00', '00:20:00', 0, 353, 1, 'y'),
(110, 'DISKUS2.00', 'Discus', 356, 6, 0, 8, '01:00:00', '00:20:00', 0, 361, 1, 'y'),
(111, 'DISKUS1.75', 'Diskus 1.75 kg', 357, 6, 0, 8, '01:00:00', '00:20:00', 0, 359, 1, 'y'),
(112, 'DISKUS1.50', 'Diskus 1.50 kg', 358, 6, 0, 8, '01:00:00', '00:20:00', 0, 358, 1, 'y'),
(113, 'DISKUS1.00', 'Discus', 359, 6, 0, 8, '01:00:00', '00:20:00', 0, 357, 1, 'y'),
(114, 'DISKUS0.75', 'Diskus 0.75 kg', 361, 6, 0, 8, '01:00:00', '00:20:00', 0, 356, 1, 'y'),
(115, 'HAMMER7.26', 'Hammer 7.26 kg', 375, 6, 0, 8, '01:00:00', '00:20:00', 0, 381, 1, 'y'),
(116, 'HAMMER6.00', 'Hammer 6.00 kg', 376, 6, 0, 8, '01:00:00', '00:20:00', 0, 378, 1, 'y'),
(117, 'HAMMER5.00', 'Hammer 5.00 kg', 377, 6, 0, 8, '01:00:00', '00:20:00', 0, 377, 1, 'y'),
(118, 'HAMMER4.00', 'Hammer', 378, 6, 0, 8, '01:00:00', '00:20:00', 0, 376, 1, 'y'),
(119, 'HAMMER3.00', 'Hammer 3.00 kg', 381, 6, 0, 8, '01:00:00', '00:20:00', 0, 375, 1, 'y'),
(120, 'SPEER800', 'Javelin', 387, 6, 0, 8, '01:00:00', '00:20:00', 0, 391, 1, 'y'),
(121, 'SPEER700', 'Speer 700 gr', 388, 6, 0, 8, '01:00:00', '00:20:00', 0, 389, 1, 'y'),
(122, 'SPEER600', 'Javelin', 389, 6, 0, 8, '01:00:00', '00:20:00', 0, 388, 1, 'y'),
(123, 'SPEER400', 'Speer 400 gr', 391, 6, 0, 8, '01:00:00', '00:20:00', 0, 387, 1, 'y'),
(124, 'BALL200', 'Ball 200 g', 392, 6, 0, 8, '01:00:00', '00:20:00', 0, 386, 1, 'y'),
(125, '5KAMPF_H', 'Fünfkampf Halle', 410, 6, 0, 9, '01:00:00', '00:15:00', 5, 394, 1, 'y'),
(126, '5KAMPF_H_U18W', 'Fünfkampf Halle  U18 W', 411, 6, 0, 9, '01:00:00', '00:15:00', 5, 395, 1, 'y'),
(127, '7KAMPF_H', 'Siebenkampf Halle', 412, 6, 0, 9, '01:00:00', '00:15:00', 7, 396, 1, 'y'),
(128, '7KAMPF_H_U20M', 'Siebenkampf Halle  U20 M', 413, 6, 0, 9, '01:00:00', '00:15:00', 7, 397, 1, 'y'),
(129, '7KAMPF_H_U18M', 'Siebenkampf Halle  U18 M', 414, 6, 0, 9, '01:00:00', '00:15:00', 7, 398, 1, 'y'),
(130, '10KAMPF', 'Zehnkampf', 430, 6, 0, 9, '01:00:00', '00:15:00', 10, 410, 1, 'y'),
(131, '10KAMPF_U20M', 'Zehnkampf  U20 M', 431, 6, 0, 9, '01:00:00', '00:15:00', 10, 411, 1, 'y'),
(132, '10KAMPF_U18M', 'Zehnkampf   U18 M', 432, 6, 0, 9, '01:00:00', '00:15:00', 10, 412, 1, 'y'),
(133, '10KAMPF_W', 'Zehnkampf Frauen', 433, 6, 0, 9, '01:00:00', '00:15:00', 10, 413, 1, 'y'),
(134, '7KAMPF', 'Siebenkampf', 425, 6, 0, 9, '01:00:00', '00:15:00', 7, 400, 1, 'y'),
(135, '7KAMPF_U18W', 'Siebenkampf   U18 W', 426, 6, 0, 9, '01:00:00', '00:15:00', 7, 401, 1, 'y'),
(136, '6KAMPF_U16M', 'Sechskampf  U16 M', 424, 6, 0, 9, '01:00:00', '00:15:00', 6, 402, 1, 'y'),
(137, '5KAMPF_U16W', 'Fünfkampf  U16 W', 423, 6, 0, 9, '01:00:00', '00:15:00', 5, 399, 1, 'y'),
(138, 'AC', 'Athletic Cup', 435, 6, 0, 9, '01:00:00', '00:15:00', 3, 403, 1, 'y'),
(139, 'MILEWALK', 'Mile walk', 450, 6, 0, 7, '01:00:00', '00:15:00', 1609, 415, 5, 'y'),
(140, '3000WALK', '3000 m walk', 452, 6, 0, 7, '01:00:00', '00:15:00', 3000, 420, 5, 'y'),
(141, '5000WALK', '5000 m walk', 453, 6, 0, 7, '01:00:00', '00:15:00', 5000, 430, 5, 'y'),
(142, '10000WALK', '10000 m walk', 454, 6, 0, 7, '01:00:00', '00:15:00', 10000, 440, 5, 'y'),
(143, '20000WALK', '20000 m walk', 455, 6, 0, 7, '01:00:00', '00:15:00', 20000, 450, 5, 'y'),
(144, '50000WALK', '50000 m walk', 456, 6, 0, 7, '01:00:00', '00:15:00', 50000, 460, 5, 'y'),
(145, '3KMWALK', '3 km walk', 470, 6, 0, 7, '01:00:00', '00:15:00', 3000, 470, 5, 'y'),
(146, '5KMWALK', '5 km walk', 480, 6, 0, 7, '01:00:00', '00:15:00', 5000, 480, 5, 'y'),
(147, '10KMWALK', '10 km walk', 490, 6, 0, 7, '01:00:00', '00:15:00', 10000, 490, 5, 'y'),
(150, '20KMWALK', '20 km walk', 500, 6, 0, 7, '01:00:00', '00:15:00', 20000, 500, 5, 'y'),
(152, '35KMWALK', '35 km walk', 530, 6, 0, 7, '01:00:00', '00:15:00', 35000, 530, 5, 'y'),
(154, '50KMWALK', '50 km walk', 550, 6, 0, 7, '01:00:00', '00:15:00', 50000, 550, 5, 'y'),
(156, '10KM_', '10 km', 491, 6, 0, 7, '01:00:00', '00:15:00', 10000, 491, 1, 'y'),
(157, '15KM', '15 km', 441, 6, 0, 7, '01:00:00', '00:15:00', 15000, 494, 1, 'y'),
(158, '20KM', '20 km', 442, 6, 0, 7, '01:00:00', '00:15:00', 20000, 501, 1, 'y'),
(159, '25KM', '25 km', 443, 6, 0, 7, '01:00:00', '00:15:00', 25000, 505, 1, 'y'),
(160, '30KM', '30 km', 444, 6, 0, 7, '01:00:00', '00:15:00', 30000, 511, 1, 'y'),
(162, '1HWALK', '1 h  walk', 555, 6, 0, 7, '01:00:00', '00:15:00', 1, 555, 5, 'y'),
(163, '2HWALK', '2 h  walk', 556, 6, 0, 7, '01:00:00', '00:15:00', 2, 556, 5, 'y'),
(164, '100KMWALK', '100 km walk', 457, 6, 0, 7, '01:00:00', '00:15:00', 100000, 559, 5, 'y'),
(165, 'BALL80', 'Ball 80 g', 393, 6, 0, 8, '01:00:00', '00:20:00', 0, 385, 1, 'y'),
(166, '300H91.4', '300 m Hürden 91.4', 289, 6, 0, 2, '01:00:00', '00:15:00', 300, 289, 4, 'y'),
(167, '4KAMPF', 'Vierkampf', 404, 6, 0, 9, '01:00:00', '00:20:00', 0, 404, 1, 'y'),
(168, '...KAMPF', '...kampf', 799, 6, 0, 9, '01:00:00', '00:15:00', 4, 799, 1, 'y'),
(169, '1500W', '1500m Wheelchair', 701, 8, 0, 7, '01:00:00', '00:15:00', 0, 0, 0, 'y'),
(170, '800W', '800m Wheelchair', 700, 8, 0, 7, '01:00:00', '00:15:00', 0, 0, 0, 'y'),
(171, '75', '75 m', 31, 6, 0, 1, '01:00:00', '00:15:00', 75, 31, 1, 'y'),
(172, '50H68.6', '50 m Hürden 68.6', 237, 6, 0, 2, '01:00:00', '00:15:00', 50, 237, 1, 'y'),
(173, '60H68.6', '60 m Hürden 68.6', 257, 6, 0, 2, '01:00:00', '00:15:00', 60, 257, 1, 'y'),
(174, '80H84.0', '80 m Hürden 84.0', 258, 6, 0, 1, '01:00:00', '00:15:00', 80, 260, 1, 'y'),
(175, '80H68.6', '80 m Hürden 68.6', 260, 6, 0, 1, '01:00:00', '00:15:00', 80, 262, 1, 'y'),
(176, '300H68.6', '300 m Hürden 68.6', 292, 6, 0, 2, '01:00:00', '00:15:00', 300, 292, 1, 'y'),
(177, 'SPEER500', 'Speer 500 gr', 390, 6, 0, 8, '01:00:00', '00:20:00', 0, 390, 1, 'y'),
(178, '5KAMPF_M', 'Fünfkampf M', 415, 6, 0, 9, '01:00:00', '00:15:00', 5, 392, 1, 'y'),
(179, '5KAMPF_U20M', 'Fünfkampf U20 M', 416, 6, 0, 9, '01:00:00', '00:15:00', 5, 393, 1, 'y'),
(180, '5KAMPF_U18M', 'Fünfkampf U18 M', 417, 6, 0, 9, '01:00:00', '00:15:00', 5, 405, 1, 'y'),
(181, '5KAMPF_W', 'Fünfkampf W', 420, 6, 0, 9, '01:00:00', '00:15:00', 5, 416, 1, 'y'),
(182, '5KAMPF_U20W', 'Fünfkampf U20 W', 421, 6, 0, 9, '01:00:00', '00:15:00', 5, 417, 1, 'y'),
(183, '5KAMPF_U18W', 'Fünfkampf U18 W', 422, 6, 0, 9, '01:00:00', '00:15:00', 5, 418, 1, 'y'),
(184, '10KAMPF_MM', 'Zehnkampf MM', 434, 6, 0, 9, '01:00:00', '00:15:00', 10, 414, 1, 'y'),
(185, '2000WALK', '2000 m walk', 451, 6, 0, 7, '01:00:00', '00:15:00', 2000, 419, 1, 'y'),
(186, '...LAUF', '...lauf', 796, 6, 0, 9, '01:00:00', '00:15:00', 4, 796, 1, 'y'),
(187, '...SPRUNG', '...sprung', 797, 6, 0, 9, '01:00:00', '00:15:00', 4, 797, 1, 'y'),
(188, '...WURF', '...wurf', 798, 6, 0, 9, '01:00:00', '00:15:00', 4, 798, 1, 'y'),
(189, 'WEIT Z', 'Weit (Zone)', 331, 6, 0, 5, '01:00:00', '00:40:00', 0, 331, 1, 'y');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `faq`
-- 

DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `xFaq` int(11) NOT NULL auto_increment,
  `Frage` varchar(255) NOT NULL default '',
  `Antwort` text NOT NULL,
  `Zeigen` enum('y','n') NOT NULL default 'y',
  `PosTop` int(11) NOT NULL default '0',
  `PosLeft` int(11) NOT NULL default '0',
  `height` int(11) NOT NULL default '0',
  `width` int(11) NOT NULL default '0',
  `Seite` varchar(255) NOT NULL default '',
  `Sprache` char(2) NOT NULL default '',
  `FarbeTitel` varchar(6) NOT NULL default 'FFAA00',
  `FarbeHG` varchar(6) NOT NULL default 'FFCC00',
  PRIMARY KEY  (`xFaq`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Daten für Tabelle `faq`
-- 

INSERT INTO `faq` (`xFaq`, `Frage`, `Antwort`, `Zeigen`, `PosTop`, `PosLeft`, `height`, `width`, `Seite`, `Sprache`, `FarbeTitel`, `FarbeHG`) VALUES 
(1, 'Regie', '<b>Ansicht für den Speaker:</b><br/>Es werden nur Wettkämpfe in Bearbeitung angezeigt, deren Startzeit kleiner als die aktuelle Zeit ist. ', 'y', 10, 900, 0, 250, 'regie', 'de', 'FFAA00', 'FFCC00'),
(2, 'Regie', '<b>Affichage pour le speaker:</b><br/>seuls les épreuves en traitement et dont l''heure de début est antérieure l''heure actuelle, sont affichés. ', 'y', 10, 900, 0, 250, 'regie', 'fr', 'FFAA00', 'FFCC00');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `hoehe`
-- 

DROP TABLE IF EXISTS `hoehe`;
CREATE TABLE `hoehe` (
  `xHoehe` int(11) NOT NULL auto_increment,
  `Hoehe` int(9) NOT NULL default '0',
  `xRunde` int(11) NOT NULL default '0',
  `xSerie` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xHoehe`),
  KEY `xRunde` (`xRunde`),
  KEY `xSerie` (`xSerie`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `hoehe`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `kategorie`
-- 

DROP TABLE IF EXISTS `kategorie`;
CREATE TABLE `kategorie` (
  `xKategorie` int(11) NOT NULL auto_increment,
  `Kurzname` varchar(4) NOT NULL default '',
  `Name` varchar(30) NOT NULL default '',
  `Anzeige` int(11) NOT NULL default '1',
  `Alterslimite` tinyint(4) NOT NULL default '99',
  `Code` varchar(4) NOT NULL default '',
  `Geschlecht` enum('m','w') NOT NULL default 'm',
  `aktiv` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`xKategorie`),
  UNIQUE KEY `Kurzname` (`Kurzname`),
  KEY `Anzeige` (`Anzeige`)
) TYPE=MyISAM AUTO_INCREMENT=20 ;

-- 
-- Daten für Tabelle `kategorie`
-- 

INSERT INTO `kategorie` (`xKategorie`, `Kurzname`, `Name`, `Anzeige`, `Alterslimite`, `Code`, `Geschlecht`, `aktiv`) VALUES 
(1, 'MAN_', 'MAN', 1, 99, 'MAN_', 'm', 'y'),
(2, 'U20M', 'U20 M', 4, 19, 'U20M', 'm', 'y'),
(3, 'U18M', 'U18 M', 5, 17, 'U18M', 'm', 'y'),
(4, 'U16M', 'U16 M', 6, 15, 'U16M', 'm', 'y'),
(5, 'U14M', 'U14 M', 7, 13, 'U14M', 'm', 'y'),
(6, 'U12M', 'U12 M', 8, 11, 'U12M', 'm', 'y'),
(7, 'WOM_', 'WOM', 10, 99, 'WOM_', 'w', 'y'),
(8, 'U20W', 'U20 W', 13, 19, 'U20W', 'w', 'y'),
(9, 'U18W', 'U18 W', 14, 17, 'U18W', 'w', 'y'),
(10, 'U16W', 'U16 W', 15, 15, 'U16W', 'w', 'y'),
(11, 'U14W', 'U14 W', 16, 13, 'U14W', 'w', 'y'),
(12, 'U12W', 'U12 W', 17, 11, 'U12W', 'w', 'y'),
(13, 'U23M', 'U23 M', 3, 22, 'U23M', 'm', 'y'),
(14, 'U23W', 'U23 W', 12, 22, 'U23W', 'w', 'y'),
(16, 'U10M', 'U10 M', 9, 9, 'U10M', 'm', 'y'),
(17, 'U10W', 'U10 W', 18, 9, 'U10W', 'w', 'y'),
(18, 'MASM', 'MASTERS M', 2, 99, 'MASM', 'm', 'y'),
(19, 'MASW', 'MASTERS W', 11, 99, 'MASW', 'w', 'y');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `kategorie_svm`
-- 

DROP TABLE IF EXISTS `kategorie_svm`;
CREATE TABLE `kategorie_svm` (
  `xKategorie_svm` int(11) NOT NULL auto_increment,
  `Name` varchar(100) NOT NULL default '',
  `Code` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`xKategorie_svm`),
  KEY `Code` (`Code`)
) TYPE=MyISAM AUTO_INCREMENT=36 ;

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
(11, '32.05 Regionalliga Mitte Männer', '32_05'),
(12, '32.06 Regionalliga Mitte Frauen', '32_06'),
(13, '33.01 Junior Liga A Männer', '33_01'),
(14, '33.02 Junior Liga B Männer', '33_02'),
(15, '33.03 Junior Liga A Frauen', '33_03'),
(16, '33.04 Junior Liga B Frauen', '33_04'),
(17, '33.05 Junior Liga C Männer', '33_05'),
(18, '33.06 Junior Liga C Frauen', '33_06'),
(19, '35.01 M30 und älter Männer', '35_01'),
(20, '35.02 U18 M', '35_02'),
(21, '35.03 U18 M Mehrkampf', '35_03'),
(22, '35.04 U16 M', '35_04'),
(23, '35.05 U16 M Mehrkampf', '35_05'),
(24, '35.06 U14 M', '35_06'),
(25, '35.07 U14 M Mannschaftswettkampf', '35_07'),
(26, '35.08 U12 M Mannschaftswettkampf', '35_08'),
(27, '36.01 W30 und älter Frauen', '36_01'),
(28, '36.02 U18 W', '36_02'),
(29, '36.03 U18 W Mehrkampf', '36_03'),
(30, '36.04 U16 W', '36_04'),
(31, '36.05 U16 W Mehrkampf', '36_05'),
(32, '36.06 U14 W', '36_06'),
(33, '36.07 U14 W Mannschaftswettkampf', '36_07'),
(34, '36.08 U12 W Mannschaftswettkampf', '36_08'),
(35, '36.09 Mixed Team U12 M und U12 W', '36_09');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `land`
-- 

DROP TABLE IF EXISTS `land`;
CREATE TABLE `land` (
  `xCode` char(3) NOT NULL default '',
  `Name` varchar(100) NOT NULL default '',
  `Sortierwert` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xCode`)
) TYPE=MyISAM;

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
CREATE TABLE `layout` (
  `xLayout` int(11) NOT NULL auto_increment,
  `TypTL` int(11) NOT NULL default '0',
  `TextTL` varchar(255) NOT NULL default '',
  `BildTL` varchar(255) NOT NULL default '',
  `TypTC` int(11) NOT NULL default '0',
  `TextTC` varchar(255) NOT NULL default '',
  `BildTC` varchar(255) NOT NULL default '',
  `TypTR` int(11) NOT NULL default '0',
  `TextTR` varchar(255) NOT NULL default '',
  `BildTR` varchar(255) NOT NULL default '',
  `TypBL` int(11) NOT NULL default '0',
  `TextBL` varchar(255) NOT NULL default '',
  `BildBL` varchar(255) NOT NULL default '',
  `TypBC` int(11) NOT NULL default '0',
  `TextBC` varchar(255) NOT NULL default '',
  `BildBC` varchar(255) NOT NULL default '',
  `TypBR` int(11) NOT NULL default '0',
  `TextBR` varchar(255) NOT NULL default '',
  `BildBR` varchar(255) NOT NULL default '',
  `xMeeting` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xLayout`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;


-- 
-- Daten für Tabelle `layout`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `meeting`
-- 

DROP TABLE IF EXISTS `meeting`;
CREATE TABLE `meeting` (
  `xMeeting` int(11) NOT NULL auto_increment,
  `Name` varchar(60) NOT NULL default '',
  `Ort` varchar(20) NOT NULL default '',
  `DatumVon` date NOT NULL default '0000-00-00',
  `DatumBis` date default NULL,
  `Nummer` varchar(20) NOT NULL default '',
  `ProgrammModus` int(1) NOT NULL default '0',
  `Online` enum('y','n') NOT NULL default 'y',
  `Organisator` varchar(200) NOT NULL default '',
  `Zeitmessung` enum('no','omega','alge') NOT NULL default 'no',
  `Passwort` varchar(50) NOT NULL default '',
  `xStadion` int(11) NOT NULL default '0',
  `xControl` int(11) NOT NULL default '0',
  `Startgeld` float NOT NULL default '0',
  `StartgeldReduktion` float NOT NULL default '0',
  `Haftgeld` float NOT NULL default '0',
  `Saison` enum('','I','O') NOT NULL default '',
  `AutoRangieren` enum('n','y') NOT NULL default 'n',
  PRIMARY KEY  (`xMeeting`),
  KEY `Name` (`Name`),
  KEY `xStadion` (`xStadion`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `meeting`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `omega_typ`
-- 

DROP TABLE IF EXISTS `omega_typ`;
CREATE TABLE `omega_typ` (
  `xOMEGA_Typ` int(11) NOT NULL default '0',
  `OMEGA_Name` varchar(15) NOT NULL default '',
  `OMEGA_Kurzname` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`xOMEGA_Typ`)
) TYPE=MyISAM;

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
CREATE TABLE `region` (
  `xRegion` int(11) NOT NULL auto_increment,
  `Name` varchar(50) NOT NULL default '',
  `Anzeige` varchar(6) NOT NULL default '',
  `Sortierwert` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xRegion`)
) TYPE=MyISAM AUTO_INCREMENT=27 ;

-- 
-- Daten für Tabelle `region`
-- 

INSERT INTO `region` (`xRegion`, `Name`, `Anzeige`, `Sortierwert`) VALUES 
(1, 'Aargau', 'AG', 100),
(2, 'Appenzell Ausserrhoden', 'AR', 101),
(3, 'Appenzell Innerrhoden', 'AI', 102),
(4, 'Basel-Landschaft', 'BL', 103),
(5, 'Basel-Stadt', 'BS', 104),
(6, 'Bern', 'BE', 105),
(7, 'Freiburg', 'FR', 106),
(8, 'Genf', 'GE', 107),
(9, 'Glarus', 'GL', 108),
(10, 'Graub&uuml;nden', 'GR', 109),
(11, 'Jura', 'JU', 110),
(12, 'Luzern', 'LU', 111),
(13, 'Neuenburg', 'NE', 112),
(14, 'Nidwalden', 'NW', 113),
(15, 'Obwalden', 'OW', 114),
(16, 'Sankt Gallen', 'SG', 115),
(17, 'Schaffhausen', 'SH', 116),
(18, 'Schwyz', 'SZ', 117),
(19, 'Solothurn', 'SO', 118),
(20, 'Thurgau', 'TG', 119),
(21, 'Tessin', 'TI', 120),
(22, 'Uri', 'UR', 121),
(23, 'Wallis', 'VS', 122),
(24, 'Waadt', 'VD', 123),
(25, 'Zug', 'ZG', 124),
(26, 'Z&uuml;rich', 'ZH', 125);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `resultat`
-- 

DROP TABLE IF EXISTS `resultat`;
CREATE TABLE `resultat` (
  `xResultat` int(11) NOT NULL auto_increment,
  `Leistung` int(9) NOT NULL default '0',
  `Info` char(5) NOT NULL default '-',
  `Punkte` float NOT NULL default '0',
  `xSerienstart` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xResultat`),
  KEY `Leistung` (`Leistung`),
  KEY `Serienstart` (`xSerienstart`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `resultat`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `runde`
-- 

DROP TABLE IF EXISTS `runde`;
CREATE TABLE `runde` (
  `xRunde` int(11) NOT NULL auto_increment,
  `Datum` date NOT NULL default '0000-00-00',
  `Startzeit` time NOT NULL default '00:00:00',
  `Appellzeit` time NOT NULL default '00:00:00',
  `Stellzeit` time NOT NULL default '00:00:00',
  `Status` int(11) NOT NULL default '0',
  `Speakerstatus` int(11) NOT NULL default '0',
  `StatusZeitmessung` tinyint(4) NOT NULL default '0',
  `StatusUpload` tinyint(4) NOT NULL default '0',
  `QualifikationSieger` tinyint(4) NOT NULL default '0',
  `QualifikationLeistung` tinyint(4) NOT NULL default '0',
  `Bahnen` tinyint(4) NOT NULL default '0',
  `Versuche` tinyint(4) NOT NULL default '0',
  `Gruppe` char(2) NOT NULL default '',
  `xRundentyp` int(11) default NULL,
  `xWettkampf` int(11) NOT NULL default '0',
  `nurBestesResultat` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`xRunde`),
  KEY `xWettkampf` (`xWettkampf`),
  KEY `Zeit` (`Datum`,`Startzeit`),
  KEY `Status` (`Status`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `runde`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rundenlog`
-- 

DROP TABLE IF EXISTS `rundenlog`;
CREATE TABLE `rundenlog` (
  `xRundenlog` int(11) NOT NULL auto_increment,
  `Zeit` datetime NOT NULL default '0000-00-00 00:00:00',
  `Ereignis` varchar(255) NOT NULL default '',
  `xRunde` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xRundenlog`),
  KEY `Zeit` (`Zeit`),
  KEY `Runde` (`xRunde`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rundenlog`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rundenset`
-- 

DROP TABLE IF EXISTS `rundenset`;
CREATE TABLE `rundenset` (
  `xRundenset` int(11) NOT NULL default '0',
  `xMeeting` int(11) NOT NULL default '0',
  `xRunde` int(11) NOT NULL default '0',
  `Hauptrunde` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`xRundenset`,`xMeeting`,`xRunde`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `rundenset`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rundentyp`
-- 

DROP TABLE IF EXISTS `rundentyp`;
CREATE TABLE `rundentyp` (
  `xRundentyp` int(11) NOT NULL auto_increment,
  `Typ` char(2) NOT NULL default '',
  `Name` varchar(20) NOT NULL default '',
  `Wertung` tinyint(4) default '0',
  `Code` char(2) NOT NULL default '',
  PRIMARY KEY  (`xRundentyp`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `Typ` (`Typ`)
) TYPE=MyISAM AUTO_INCREMENT=10 ;

-- 
-- Daten für Tabelle `rundentyp`
-- 

INSERT INTO `rundentyp` (`xRundentyp`, `Typ`, `Name`, `Wertung`, `Code`) VALUES 
(1, 'V', 'Vorlauf', 0, 'V'),
(2, 'F', 'Final', 0, 'F'),
(3, 'Z', 'Zwischenlauf', 0, 'Z'),
(5, 'Q', 'Qualifikation', 1, 'Q'),
(6, 'S', 'Heat', 0, 'S'),
(7, 'X', 'Halbfinal', 0, 'X'),
(8, 'D', 'Mehrkampf', 1, 'D'),
(9, '0', '(ohne)', 2, '0');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `serie`
-- 

DROP TABLE IF EXISTS `serie`;
CREATE TABLE `serie` (
  `xSerie` int(11) NOT NULL auto_increment,
  `Bezeichnung` char(2) NOT NULL default '',
  `Wind` varchar(5) default '',
  `Film` int(11) default '0',
  `Status` int(11) NOT NULL default '0',
  `Handgestoppt` tinyint(4) NOT NULL default '0',
  `xRunde` int(11) NOT NULL default '0',
  `xAnlage` int(11) default NULL,
  `TVName` varchar(70) default NULL,
  `MaxAthlet` int(3) NOT NULL default '0',
  PRIMARY KEY  (`xSerie`),
  UNIQUE KEY `Bezeichnung` (`xRunde`,`Bezeichnung`),
  KEY `Runde` (`xRunde`),
  KEY `Anlage` (`xAnlage`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `serie`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `serienstart`
-- 

DROP TABLE IF EXISTS `serienstart`;
CREATE TABLE `serienstart` (
  `xSerienstart` int(11) NOT NULL auto_increment,
  `Position` int(11) NOT NULL default '0',
  `Bahn` int(11) NOT NULL default '0',
  `Rang` int(11) NOT NULL default '0',
  `Qualifikation` tinyint(4) NOT NULL default '0',
  `xSerie` int(11) NOT NULL default '0',
  `xStart` int(11) NOT NULL default '0',
  `RundeZusammen` int(11) NOT NULL default '0',
  `Bemerkung` char(5) NOT NULL default '',
  `Position2` int(11) NOT NULL default '0',
  `Position3` int(11) NOT NULL default '0',
  `AktivAthlet` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`xSerienstart`),
  UNIQUE KEY `Serienstart` (`xSerie`,`xStart`),
  KEY `Rang` (`Rang`),
  KEY `Qualifikation` (`Qualifikation`),
  KEY `xSerie` (`xSerie`),
  KEY `xStart` (`xStart`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `serienstart`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `stadion`
-- 

DROP TABLE IF EXISTS `stadion`;
CREATE TABLE `stadion` (
  `xStadion` int(11) NOT NULL auto_increment,
  `Name` varchar(50) NOT NULL default '',
  `Bahnen` tinyint(4) NOT NULL default '6',
  `BahnenGerade` tinyint(4) NOT NULL default '8',
  `Ueber1000m` enum('y','n') NOT NULL default 'n',
  `Halle` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`xStadion`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `stadion`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `staffel`
-- 

DROP TABLE IF EXISTS `staffel`;
CREATE TABLE `staffel` (
  `xStaffel` int(11) NOT NULL auto_increment,
  `Name` varchar(40) NOT NULL default '',
  `xVerein` int(11) NOT NULL default '0',
  `xMeeting` int(11) NOT NULL default '0',
  `xKategorie` int(11) NOT NULL default '0',
  `xTeam` int(11) NOT NULL default '0',
  `Athleticagen` enum('y','n') NOT NULL default 'n',
  `Startnummer` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xStaffel`),
  KEY `xMeeting` (`xMeeting`),
  KEY `xVerein` (`xVerein`),
  KEY `Name` (`Name`(10)),
  KEY `xTeam` (`xTeam`),
  KEY `Startnummer` (`Startnummer`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `staffel`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `staffelathlet`
-- 

DROP TABLE IF EXISTS `staffelathlet`;
CREATE TABLE `staffelathlet` (
  `xStaffelstart` int(11) NOT NULL default '0',
  `xAthletenstart` int(11) NOT NULL default '0',
  `xRunde` int(11) NOT NULL default '0',
  `Position` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`xStaffelstart`,`xAthletenstart`,`xRunde`),
  UNIQUE KEY `Reihenfolge` (`xStaffelstart`,`Position`,`xRunde`),
  KEY `Position` (`Position`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `staffelathlet`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `start`
-- 

DROP TABLE IF EXISTS `start`;
CREATE TABLE `start` (
  `xStart` int(11) NOT NULL auto_increment,
  `Anwesend` smallint(1) NOT NULL default '0',
  `Bestleistung` int(11) NOT NULL default '0',
  `Bezahlt` enum('y','n') NOT NULL default 'n',
  `Erstserie` enum('y','n') NOT NULL default 'n',
  `xWettkampf` int(11) NOT NULL default '0',
  `xAnmeldung` int(11) NOT NULL default '0',
  `xStaffel` int(11) NOT NULL default '0',
  `BaseEffort` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`xStart`),
  UNIQUE KEY `start` (`xWettkampf`,`xAnmeldung`,`xStaffel`),
  KEY `Staffel` (`xStaffel`),
  KEY `Anmeldung` (`xAnmeldung`),
  KEY `Wettkampf` (`xWettkampf`),
  KEY `WettkampfAnmeldung` (`xAnmeldung`,`xWettkampf`),
  KEY `WettkampfStaffel` (`xStaffel`,`xWettkampf`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `start`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `sys_backuptabellen`
-- 

DROP TABLE IF EXISTS `sys_backuptabellen`;
CREATE TABLE `sys_backuptabellen` (
  `xBackup` int(11) NOT NULL auto_increment,
  `Tabelle` varchar(50) default NULL,
  `SelectSQL` text,
  PRIMARY KEY  (`xBackup`)
) TYPE=MyISAM AUTO_INCREMENT=39 ;

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
CREATE TABLE `team` (
  `xTeam` int(11) NOT NULL auto_increment,
  `Name` varchar(30) NOT NULL default '',
  `Athleticagen` enum('y','n') NOT NULL default 'n',
  `xKategorie` int(11) NOT NULL default '0',
  `xMeeting` int(11) NOT NULL default '0',
  `xVerein` int(11) NOT NULL default '0',
  `xKategorie_svm` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xTeam`),
  UNIQUE KEY `MeetingKatName` (`xMeeting`,`xKategorie`,`Name`),
  KEY `Name` (`Name`),
  KEY `xKategorie` (`xKategorie`),
  KEY `xVerein` (`xVerein`),
  KEY `xMeeting` (`xMeeting`),
  KEY `xKategorie_svm` (`xKategorie_svm`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `team`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `teamsm`
-- 

DROP TABLE IF EXISTS `teamsm`;
CREATE TABLE `teamsm` (
  `xTeamsm` int(11) NOT NULL auto_increment,
  `Name` varchar(100) NOT NULL default '',
  `xKategorie` int(11) NOT NULL default '0',
  `xVerein` int(11) NOT NULL default '0',
  `xWettkampf` int(11) NOT NULL default '0',
  `xMeeting` int(11) NOT NULL default '0',
  `Startnummer` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xTeamsm`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `teamsm`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `teamsmathlet`
-- 

DROP TABLE IF EXISTS `teamsmathlet`;
CREATE TABLE `teamsmathlet` (
  `xTeamsm` int(11) NOT NULL default '0',
  `xAnmeldung` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xTeamsm`,`xAnmeldung`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `teamsmathlet`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `verein`
-- 

DROP TABLE IF EXISTS `verein`;
CREATE TABLE `verein` (
  `xVerein` int(11) NOT NULL auto_increment,
  `Name` varchar(30) NOT NULL default '',
  `Sortierwert` varchar(30) NOT NULL default '0',
  `xCode` varchar(30) NOT NULL default '',
  `Geloescht` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`xVerein`),
  UNIQUE KEY `Name` (`Name`),
  KEY `Sortierwert` (`Sortierwert`),
  KEY `xCode` (`xCode`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `verein`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `videowand`
-- 

DROP TABLE IF EXISTS `videowand`;
CREATE TABLE `videowand` (
  `xVideowand` int(11) NOT NULL auto_increment,
  `xMeeting` int(11) NOT NULL default '0',
  `X` int(11) NOT NULL default '0',
  `Y` int(11) NOT NULL default '0',
  `InhaltArt` enum('dyn','stat') NOT NULL default 'dyn',
  `InhaltStatisch` text NOT NULL,
  `InhaltDynamisch` text NOT NULL,
  `Aktualisierung` int(11) NOT NULL default '0',
  `Status` enum('black','white','active') NOT NULL default 'active',
  `Hintergrund` varchar(6) NOT NULL default '',
  `Fordergrund` varchar(6) NOT NULL default '',
  `Bildnr` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`xVideowand`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `videowand`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `wertungstabelle`
-- 

DROP TABLE IF EXISTS `wertungstabelle`;
CREATE TABLE `wertungstabelle` (
  `xWertungstabelle` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`xWertungstabelle`)
) TYPE=MyISAM AUTO_INCREMENT=100 ;

-- 
-- Daten für Tabelle `wertungstabelle`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `wertungstabelle_punkte`
-- 

DROP TABLE IF EXISTS `wertungstabelle_punkte`;
CREATE TABLE `wertungstabelle_punkte` (
  `xWertungstabelle_Punkte` int(11) NOT NULL auto_increment,
  `xWertungstabelle` int(11) NOT NULL default '0',
  `xDisziplin` int(11) NOT NULL default '0',
  `Geschlecht` enum('W','M') NOT NULL default 'M',
  `Leistung` varchar(50) NOT NULL default '',
  `Punkte` float NOT NULL default '0',
  PRIMARY KEY  (`xWertungstabelle_Punkte`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `wertungstabelle_punkte`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `wettkampf`
-- 

DROP TABLE IF EXISTS `wettkampf`;
CREATE TABLE `wettkampf` (
  `xWettkampf` int(11) NOT NULL auto_increment,
  `Typ` tinyint(4) NOT NULL default '0',
  `Haftgeld` float unsigned NOT NULL default '0',
  `Startgeld` float unsigned NOT NULL default '0',
  `Punktetabelle` tinyint(3) unsigned NOT NULL default '0',
  `Punkteformel` varchar(20) NOT NULL default '0',
  `Windmessung` tinyint(4) NOT NULL default '0',
  `Info` varchar(15) default NULL,
  `Zeitmessung` tinyint(4) NOT NULL default '0',
  `ZeitmessungAuto` tinyint(4) NOT NULL default '0',
  `xKategorie` int(11) NOT NULL default '1',
  `xDisziplin` int(11) NOT NULL default '1',
  `xMeeting` int(11) NOT NULL default '1',
  `Mehrkampfcode` int(11) NOT NULL default '0',
  `Mehrkampfende` tinyint(4) NOT NULL default '0',
  `Mehrkampfreihenfolge` tinyint(4) NOT NULL default '0',
  `xKategorie_svm` int(11) NOT NULL default '0',
  `OnlineId` int(11) NOT NULL default '0',
  `TypAenderung` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`xWettkampf`),
  KEY `xKategorie` (`xKategorie`),
  KEY `xDisziplin` (`xDisziplin`),
  KEY `xMeeting` (`xMeeting`),
  KEY `OnlineId` (`OnlineId`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `wettkampf`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `zeitmessung`
-- 

DROP TABLE IF EXISTS `zeitmessung`;
CREATE TABLE `zeitmessung` (
  `xZeitmessung` int(11) NOT NULL auto_increment,
  `OMEGA_Verbindung` enum('local','ftp') NOT NULL default 'local',
  `OMEGA_Pfad` varchar(255) NOT NULL default '',
  `OMEGA_Server` varchar(255) NOT NULL default '',
  `OMEGA_Benutzer` varchar(50) NOT NULL default '',
  `OMEGA_Passwort` varchar(50) NOT NULL default '',
  `OMEGA_Ftppfad` varchar(255) NOT NULL default '',
  `OMEGA_Sponsor` varchar(255) NOT NULL default '',
  `ALGE_Typ` varchar(20) NOT NULL default '',
  `ALGE_Ftppfad` varchar(255) NOT NULL default '',
  `ALGE_Passwort` varchar(50) NOT NULL default '',
  `ALGE_Benutzer` varchar(50) NOT NULL default '',
  `ALGE_Server` varchar(255) NOT NULL default '',
  `ALGE_Pfad` varchar(255) NOT NULL default '',
  `ALGE_Verbindung` enum('local','ftp') NOT NULL default 'local',
  `xMeeting` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xZeitmessung`),
  KEY `xMeeting` (`xMeeting`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `zeitmessung`
-- 

