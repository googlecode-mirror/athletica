-- phpMyAdmin SQL Dump
-- version 3.2.2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Erstellungszeit: 25. Juni 2012 um 13:51
-- Server Version: 5.1.26
-- PHP-Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `athletica_liveresultate`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `xConfig` int(11) NOT NULL DEFAULT '0',
  `ftpHost` varchar(50) NOT NULL DEFAULT '',
  `ftpUser` varchar(30) NOT NULL DEFAULT '',
  `ftpPwd` varchar(30) NOT NULL DEFAULT '',
  `url` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`xConfig`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `config`
--

INSERT INTO `config` (`xConfig`, `ftpHost`, `ftpUser`, `ftpPwd`, `url`) VALUES
(0, 'ftp.xxxx.ch', '', '', 'www.xxxx.ch/live');
