# phpMyAdmin SQL Dump
# version 2.5.5-pl1
# http://www.phpmyadmin.net
#
# Host: localhost
# Erstellungszeit: 12. März 2007 um 20:27
# Server Version: 4.0.12
# PHP-Version: 4.3.4
# 
# Datenbank: `athletica`
# 

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `anlage`
#

CREATE TABLE `anlage` (
  `xAnlage` int(11) NOT NULL auto_increment,
  `Bezeichnung` varchar(20) NOT NULL default '',
  `Homologiert` enum('y','n') NOT NULL default 'y',
  `xStadion` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xAnlage`),
  KEY `xStadion` (`xStadion`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `anmeldung`
#

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
  PRIMARY KEY  (`xAnmeldung`),
  UNIQUE KEY `AthleteMeetingKat` (`xAthlet`,`xMeeting`,`xKategorie`),
  KEY `xAthlet` (`xAthlet`),
  KEY `xMeeting` (`xMeeting`),
  KEY `xKategorie` (`xKategorie`),
  KEY `Startnummer` (`Startnummer`),
  KEY `xTeam` (`xTeam`),
  KEY `Vereinsinfo` (`Vereinsinfo`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `athlet`
#

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
  PRIMARY KEY  (`xAthlet`),
  UNIQUE KEY `Athlet` (`Name`,`Vorname`,`Jahrgang`,`xVerein`),
  KEY `Name` (`Name`),
  KEY `xVerein` (`xVerein`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `base_account`
#

CREATE TABLE `base_account` (
  `account_code` varchar(30) NOT NULL default '',
  `account_name` varchar(255) NOT NULL default '',
  `account_short` varchar(255) NOT NULL default '',
  `account_type` varchar(100) NOT NULL default '',
  `lg` varchar(100) NOT NULL default ''
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `base_athlete`
#

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
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `base_log`
#

CREATE TABLE `base_log` (
  `id_log` int(11) NOT NULL auto_increment,
  `type` varchar(50) NOT NULL default '',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `global_last_change` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id_log`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `base_performance`
#

CREATE TABLE `base_performance` (
  `id_performance` int(11) NOT NULL auto_increment,
  `id_athlete` int(11) NOT NULL default '0',
  `discipline` varchar(10) NOT NULL default '',
  `category` varchar(10) NOT NULL default '',
  `best_effort` varchar(15) NOT NULL default '',
  `season_effort` varchar(15) NOT NULL default '',
  `notification_effort` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id_performance`),
  KEY `id_athlete` (`id_athlete`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `base_relay`
#

CREATE TABLE `base_relay` (
  `id_relay` int(11) NOT NULL default '0',
  `is_athletica_gen` enum('y','n') NOT NULL default 'y',
  `relay_name` varchar(255) NOT NULL default '',
  `category` varchar(10) NOT NULL default '',
  `discipline` varchar(10) NOT NULL default '',
  `account_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_relay`),
  KEY `account_code` (`account_code`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `base_svm`
#

CREATE TABLE `base_svm` (
  `id_svm` int(11) NOT NULL default '0',
  `is_athletica_gen` enum('y','n') NOT NULL default 'y',
  `svm_name` varchar(255) NOT NULL default '',
  `svm_category` varchar(10) NOT NULL default '',
  `account_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_svm`),
  KEY `account_code` (`account_code`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `disziplin`
#

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
  PRIMARY KEY  (`xDisziplin`),
  UNIQUE KEY `Kurzname` (`Kurzname`),
  KEY `Anzeige` (`Anzeige`),
  KEY `Staffel` (`Staffellaeufer`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `faq`
#

CREATE TABLE `faq` (
  `xFaq` int(11) NOT NULL auto_increment,
  `Frage` varchar(255) NOT NULL default '',
  `Antwort` text NOT NULL,
  `Zeigen` enum('y','n') NOT NULL default 'y',
  `PosTop` int(11) NOT NULL default '0',
  `PosLeft` int(11) NOT NULL default '0',
  `Seite` varchar(255) NOT NULL default '',
  `Sprache` char(2) NOT NULL default '',
  PRIMARY KEY  (`xFaq`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `kategorie`
#

CREATE TABLE `kategorie` (
  `xKategorie` int(11) NOT NULL auto_increment,
  `Kurzname` varchar(4) NOT NULL default '',
  `Name` varchar(30) NOT NULL default '',
  `Anzeige` int(11) NOT NULL default '1',
  `Alterslimite` tinyint(4) NOT NULL default '99',
  `Code` varchar(4) NOT NULL default '',
  `Geschlecht` enum('m','w') NOT NULL default 'm',
  PRIMARY KEY  (`xKategorie`),
  UNIQUE KEY `Kurzname` (`Kurzname`),
  KEY `Anzeige` (`Anzeige`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `kategorie_svm`
#

CREATE TABLE `kategorie_svm` (
  `xKategorie_svm` int(11) NOT NULL auto_increment,
  `Name` varchar(100) NOT NULL default '',
  `Code` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`xKategorie_svm`),
  KEY `Code` (`Code`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `land`
#

CREATE TABLE `land` (
  `xCode` char(3) NOT NULL default '',
  `Name` varchar(100) NOT NULL default '',
  `Sortierwert` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xCode`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `layout`
#

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
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `meeting`
#

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
  `xStadion` int(11) NOT NULL default '0',
  `xControl` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xMeeting`),
  KEY `Name` (`Name`),
  KEY `xStadion` (`xStadion`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `omega_typ`
#

CREATE TABLE `omega_typ` (
  `xOMEGA_Typ` int(11) NOT NULL default '0',
  `OMEGA_Name` varchar(15) NOT NULL default '',
  `OMEGA_Kurzname` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`xOMEGA_Typ`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `region`
#

CREATE TABLE `region` (
  `xRegion` int(11) NOT NULL auto_increment,
  `Name` varchar(50) NOT NULL default '',
  `Anzeige` varchar(4) NOT NULL default '',
  `Sortierwert` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xRegion`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `resultat`
#

CREATE TABLE `resultat` (
  `xResultat` int(11) NOT NULL auto_increment,
  `Leistung` int(9) NOT NULL default '0',
  `Info` char(5) NOT NULL default '-',
  `Punkte` float NOT NULL default '0',
  `xSerienstart` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xResultat`),
  KEY `Leistung` (`Leistung`),
  KEY `Serienstart` (`xSerienstart`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `runde`
#

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
  PRIMARY KEY  (`xRunde`),
  KEY `xWettkampf` (`xWettkampf`),
  KEY `Zeit` (`Datum`,`Startzeit`),
  KEY `Status` (`Status`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `rundenlog`
#

CREATE TABLE `rundenlog` (
  `xRundenlog` int(11) NOT NULL auto_increment,
  `Zeit` datetime NOT NULL default '0000-00-00 00:00:00',
  `Ereignis` varchar(255) NOT NULL default '',
  `xRunde` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xRundenlog`),
  KEY `Zeit` (`Zeit`),
  KEY `Runde` (`xRunde`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `rundenset`
#

CREATE TABLE `rundenset` (
  `xRundenset` int(11) NOT NULL default '0',
  `xMeeting` int(11) NOT NULL default '0',
  `xRunde` int(11) NOT NULL default '0',
  `Hauptrunde` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`xRundenset`,`xMeeting`,`xRunde`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `rundentyp`
#

CREATE TABLE `rundentyp` (
  `xRundentyp` int(11) NOT NULL auto_increment,
  `Typ` char(2) NOT NULL default '',
  `Name` varchar(20) NOT NULL default '',
  `Wertung` tinyint(4) default '0',
  `Code` char(2) NOT NULL default '',
  PRIMARY KEY  (`xRundentyp`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `Typ` (`Typ`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `serie`
#

CREATE TABLE `serie` (
  `xSerie` int(11) NOT NULL auto_increment,
  `Bezeichnung` char(2) NOT NULL default '',
  `Wind` char(5) default '',
  `Film` int(11) default '0',
  `Status` int(11) NOT NULL default '0',
  `Handgestoppt` tinyint(4) NOT NULL default '0',
  `xRunde` int(11) NOT NULL default '0',
  `xAnlage` int(11) default NULL,
  PRIMARY KEY  (`xSerie`),
  UNIQUE KEY `Bezeichnung` (`xRunde`,`Bezeichnung`),
  KEY `Runde` (`xRunde`),
  KEY `Anlage` (`xAnlage`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `serienstart`
#

CREATE TABLE `serienstart` (
  `xSerienstart` int(11) NOT NULL auto_increment,
  `Position` int(11) NOT NULL default '0',
  `Bahn` int(11) NOT NULL default '0',
  `Rang` int(11) NOT NULL default '0',
  `Qualifikation` tinyint(4) NOT NULL default '0',
  `xSerie` int(11) NOT NULL default '0',
  `xStart` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xSerienstart`),
  UNIQUE KEY `Serienstart` (`xSerie`,`xStart`),
  KEY `Rang` (`Rang`),
  KEY `Qualifikation` (`Qualifikation`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `stadion`
#

CREATE TABLE `stadion` (
  `xStadion` int(11) NOT NULL auto_increment,
  `Name` varchar(50) NOT NULL default '',
  `Bahnen` tinyint(4) NOT NULL default '6',
  `BahnenGerade` tinyint(4) NOT NULL default '8',
  `Ueber1000m` enum('y','n') NOT NULL default 'n',
  `Halle` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`xStadion`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `staffel`
#

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
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `staffelathlet`
#

CREATE TABLE `staffelathlet` (
  `xStaffelstart` int(11) NOT NULL default '0',
  `xAthletenstart` int(11) NOT NULL default '0',
  `xRunde` int(11) NOT NULL default '0',
  `Position` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`xStaffelstart`,`xAthletenstart`,`xRunde`),
  UNIQUE KEY `Reihenfolge` (`xStaffelstart`,`Position`,`xRunde`),
  KEY `Position` (`Position`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `start`
#

CREATE TABLE `start` (
  `xStart` int(11) NOT NULL auto_increment,
  `Anwesend` smallint(1) NOT NULL default '0',
  `Bestleistung` int(11) NOT NULL default '0',
  `Bezahlt` enum('y','n') NOT NULL default 'n',
  `xWettkampf` int(11) NOT NULL default '0',
  `xAnmeldung` int(11) NOT NULL default '0',
  `xStaffel` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xStart`),
  UNIQUE KEY `start` (`xWettkampf`,`xAnmeldung`,`xStaffel`),
  KEY `Staffel` (`xStaffel`),
  KEY `Anmeldung` (`xAnmeldung`),
  KEY `Wettkampf` (`xWettkampf`),
  KEY `WettkampfAnmeldung` (`xAnmeldung`,`xWettkampf`),
  KEY `WettkampfStaffel` (`xStaffel`,`xWettkampf`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `team`
#

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
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `verein`
#

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
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `videowand`
#

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
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `wettkampf`
#

CREATE TABLE `wettkampf` (
  `xWettkampf` int(11) NOT NULL auto_increment,
  `Typ` tinyint(4) NOT NULL default '0',
  `Haftgeld` float unsigned NOT NULL default '0',
  `Startgeld` float unsigned NOT NULL default '0',
  `Punktetabelle` tinyint(3) unsigned NOT NULL default '0',
  `Punkteformel` varchar(10) NOT NULL default '0',
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
  PRIMARY KEY  (`xWettkampf`),
  KEY `xKategorie` (`xKategorie`),
  KEY `xDisziplin` (`xDisziplin`),
  KEY `xMeeting` (`xMeeting`),
  KEY `OnlineId` (`OnlineId`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `zeitmessung`
#

CREATE TABLE `zeitmessung` (
  `xZeitmessung` int(11) NOT NULL auto_increment,
  `OMEGA_Verbindung` enum('local','ftp') NOT NULL default 'local',
  `OMEGA_Pfad` varchar(255) NOT NULL default '',
  `OMEGA_Server` varchar(255) NOT NULL default '',
  `OMEGA_Benutzer` varchar(50) NOT NULL default '',
  `OMEGA_Passwort` varchar(50) NOT NULL default '',
  `OMEGA_Ftppfad` varchar(255) NOT NULL default '',
  `ALGE_Ftppfad` varchar(255) NOT NULL default '',
  `ALGE_Passwort` varchar(50) NOT NULL default '',
  `ALGE_Benutzer` varchar(50) NOT NULL default '',
  `ALGE_Server` varchar(255) NOT NULL default '',
  `ALGE_Pfad` varchar(255) NOT NULL default '',
  `ALGE_Verbindung` enum('local','ftp') NOT NULL default 'local',
  `xMeeting` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xZeitmessung`),
  KEY `xMeeting` (`xMeeting`)
) TYPE=MyISAM;
