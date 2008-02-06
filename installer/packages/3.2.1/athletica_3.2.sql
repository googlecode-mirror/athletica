-- 
-- Datenbank: 'athletica'
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'anlage'
-- 

DROP TABLE IF EXISTS anlage;
CREATE TABLE anlage (
  xAnlage int(11) NOT NULL auto_increment,
  Bezeichnung varchar(20) NOT NULL default '',
  Homologiert enum('y','n') NOT NULL default 'y',
  xStadion int(11) NOT NULL default '0',
  PRIMARY KEY  (xAnlage),
  KEY xStadion (xStadion)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'anlage'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'anmeldung'
-- 

DROP TABLE IF EXISTS anmeldung;
CREATE TABLE anmeldung (
  xAnmeldung int(11) NOT NULL auto_increment,
  Startnummer smallint(5) unsigned NOT NULL default '0',
  Erstserie enum('y','n') NOT NULL default 'n',
  Bezahlt enum('y','n') NOT NULL default 'y',
  Gruppe char(2) NOT NULL default '',
  BestleistungMK float NOT NULL default '0',
  Vereinsinfo varchar(150) NOT NULL default '',
  xAthlet int(11) NOT NULL default '0',
  xMeeting int(11) NOT NULL default '0',
  xKategorie int(11) default NULL,
  xTeam int(11) NOT NULL default '0',
  PRIMARY KEY  (xAnmeldung),
  UNIQUE KEY AthleteMeetingKat (xAthlet,xMeeting,xKategorie),
  KEY xAthlet (xAthlet),
  KEY xMeeting (xMeeting),
  KEY xKategorie (xKategorie),
  KEY Startnummer (Startnummer),
  KEY xTeam (xTeam),
  KEY Vereinsinfo (Vereinsinfo)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'anmeldung'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'athlet'
-- 

DROP TABLE IF EXISTS athlet;
CREATE TABLE athlet (
  xAthlet int(11) NOT NULL auto_increment,
  Name varchar(25) NOT NULL default '',
  Vorname varchar(25) NOT NULL default '',
  Jahrgang year(4) default NULL,
  xVerein int(11) NOT NULL default '0',
  xVerein2 int(11) NOT NULL default '0',
  Lizenznummer int(11) NOT NULL default '0',
  Geschlecht enum('m','w') NOT NULL default 'm',
  Land char(3) NOT NULL default '',
  Geburtstag date NOT NULL default '0000-00-00',
  Athleticagen enum('y','n') NOT NULL default 'n',
  Bezahlt enum('y','n') NOT NULL default 'n',
  xRegion int(11) NOT NULL default '0',
  Lizenztyp tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (xAthlet),
  UNIQUE KEY Athlet (Name,Vorname,Jahrgang,xVerein),
  KEY Name (Name),
  KEY xVerein (xVerein)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'athlet'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'base_account'
-- 

DROP TABLE IF EXISTS base_account;
CREATE TABLE base_account (
  account_code varchar(30) NOT NULL default '',
  account_name varchar(255) NOT NULL default '',
  account_short varchar(255) NOT NULL default '',
  account_type varchar(100) NOT NULL default '',
  lg varchar(100) NOT NULL default ''
) TYPE=MyISAM;

-- 
-- Daten für Tabelle 'base_account'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'base_athlete'
-- 

DROP TABLE IF EXISTS base_athlete;
CREATE TABLE base_athlete (
  id_athlete int(11) NOT NULL auto_increment,
  license int(11) NOT NULL default '0',
  license_paid enum('y','n') NOT NULL default 'y',
  license_cat varchar(4) NOT NULL default '',
  lastname varchar(100) NOT NULL default '',
  firstname varchar(100) NOT NULL default '',
  sex enum('m','w') NOT NULL default 'm',
  nationality char(3) NOT NULL default '',
  account_code varchar(30) NOT NULL default '',
  second_account_code varchar(30) NOT NULL default '',
  birth_date date NOT NULL default '0000-00-00',
  account_info varchar(150) NOT NULL default '',
  PRIMARY KEY  (id_athlete),
  KEY account_code (account_code),
  KEY second_account_code (second_account_code),
  KEY license (license),
  KEY lastname (lastname),
  KEY firstname (firstname)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'base_athlete'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'base_log'
-- 

DROP TABLE IF EXISTS base_log;
CREATE TABLE base_log (
  id_log int(11) NOT NULL auto_increment,
  type varchar(50) NOT NULL default '',
  update_time datetime NOT NULL default '0000-00-00 00:00:00',
  global_last_change date NOT NULL default '0000-00-00',
  PRIMARY KEY  (id_log)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'base_log'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'base_performance'
-- 

DROP TABLE IF EXISTS base_performance;
CREATE TABLE base_performance (
  id_performance int(11) NOT NULL auto_increment,
  id_athlete int(11) NOT NULL default '0',
  discipline varchar(10) NOT NULL default '',
  category varchar(10) NOT NULL default '',
  best_effort varchar(15) NOT NULL default '',
  season_effort varchar(15) NOT NULL default '',
  notification_effort varchar(15) NOT NULL default '',
  PRIMARY KEY  (id_performance),
  KEY id_athlete (id_athlete)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'base_performance'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'base_relay'
-- 

DROP TABLE IF EXISTS base_relay;
CREATE TABLE base_relay (
  id_relay int(11) NOT NULL default '0',
  is_athletica_gen enum('y','n') NOT NULL default 'y',
  relay_name varchar(255) NOT NULL default '',
  category varchar(10) NOT NULL default '',
  discipline varchar(10) NOT NULL default '',
  account_code int(11) NOT NULL default '0',
  PRIMARY KEY  (id_relay),
  KEY account_code (account_code)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle 'base_relay'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'base_svm'
-- 

DROP TABLE IF EXISTS base_svm;
CREATE TABLE base_svm (
  id_svm int(11) NOT NULL default '0',
  is_athletica_gen enum('y','n') NOT NULL default 'y',
  svm_name varchar(255) NOT NULL default '',
  svm_category varchar(10) NOT NULL default '',
  account_code int(11) NOT NULL default '0',
  PRIMARY KEY  (id_svm),
  KEY account_code (account_code)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle 'base_svm'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'disziplin'
-- 

DROP TABLE IF EXISTS disziplin;
CREATE TABLE disziplin (
  xDisziplin int(11) NOT NULL auto_increment,
  Kurzname varchar(15) NOT NULL default '',
  Name varchar(40) NOT NULL default '',
  Anzeige int(11) NOT NULL default '1',
  Seriegroesse int(4) NOT NULL default '0',
  Staffellaeufer int(11) default NULL,
  Typ int(11) NOT NULL default '0',
  Appellzeit time NOT NULL default '00:00:00',
  Stellzeit time NOT NULL default '00:00:00',
  Strecke float NOT NULL default '0',
  Code int(11) NOT NULL default '0',
  xOMEGA_Typ int(11) NOT NULL default '0',
  PRIMARY KEY  (xDisziplin),
  UNIQUE KEY Kurzname (Kurzname),
  KEY Anzeige (Anzeige),
  KEY Staffel (Staffellaeufer)
) TYPE=MyISAM AUTO_INCREMENT=167 ;

-- 
-- Daten für Tabelle 'disziplin'
-- 

INSERT INTO disziplin (xDisziplin, Kurzname, Name, Anzeige, Seriegroesse, Staffellaeufer, Typ, Appellzeit, Stellzeit, Strecke, Code, xOMEGA_Typ) VALUES 
(38, '50', '50 m', 10, 6, 0, 2, '01:00:00', '00:15:00', 50, 10, 1),
(39, '55', '55 m', 20, 6, 0, 2, '01:00:00', '00:15:00', 55, 20, 1),
(40, '60', '60 m', 30, 6, 0, 2, '01:00:00', '00:15:00', 60, 30, 1),
(41, '80', '80 m', 35, 6, 0, 1, '01:00:00', '00:15:00', 80, 35, 1),
(42, '100', '100 m', 40, 6, 0, 1, '01:00:00', '00:15:00', 100, 40, 1),
(43, '150', '150 m', 48, 6, 0, 1, '01:00:00', '00:15:00', 150, 48, 1),
(44, '200', '200 m', 50, 6, 0, 1, '01:00:00', '00:15:00', 200, 50, 1),
(45, '300', '300 m', 60, 6, 0, 2, '01:00:00', '00:15:00', 300, 60, 1),
(46, '400', '400 m', 70, 6, 0, 2, '01:00:00', '00:15:00', 400, 70, 1),
(47, '600', '600 m', 80, 6, 0, 7, '01:00:00', '00:15:00', 600, 80, 1),
(48, '800', '800 m', 90, 6, 0, 7, '01:00:00', '00:15:00', 800, 90, 1),
(49, '1000', '1000 m', 100, 6, 0, 7, '01:00:00', '00:15:00', 1000, 100, 1),
(50, '1500', '1500 m', 110, 6, 0, 7, '01:00:00', '00:15:00', 1500, 110, 1),
(51, '1MEILE', '1 Meile', 120, 6, 0, 7, '01:00:00', '00:15:00', 1609, 120, 1),
(52, '2000', '2000 m', 130, 6, 0, 7, '01:00:00', '00:15:00', 2000, 130, 1),
(53, '3000', '3000 m', 140, 6, 0, 7, '01:00:00', '00:15:00', 3000, 140, 1),
(54, '5000', '5000 m', 160, 6, 0, 7, '01:00:00', '00:15:00', 5000, 160, 1),
(55, '10000', '10 000 m', 170, 6, 0, 7, '01:00:00', '00:15:00', 10000, 170, 1),
(56, '20000', '20 000 m', 180, 6, 0, 7, '01:00:00', '00:15:00', 20000, 180, 1),
(57, '1STUNDE', '1 Stunde', 182, 6, 0, 7, '01:00:00', '00:15:00', 1, 182, 1),
(58, '25000', '25 000 m', 181, 6, 0, 7, '01:00:00', '00:15:00', 25000, 181, 1),
(59, '30000', '30 000 m', 195, 6, 0, 7, '01:00:00', '00:15:00', 30000, 195, 1),
(60, '10KM', '10 km', 186, 6, 0, 7, '01:00:00', '00:15:00', 10000, 186, 1),
(61, 'HALBMARATH', 'Halbmarathon', 190, 6, 0, 7, '01:00:00', '00:15:00', 0, 190, 1),
(62, 'MARATHON', 'Marathon', 200, 6, 0, 7, '01:00:00', '00:15:00', 0, 200, 1),
(63, '24H', '24 h Lauf', 201, 6, 0, 7, '01:00:00', '00:15:00', 24, 201, 1),
(64, '50H106.7', '50 m Hürden 106.7', 232, 6, 0, 1, '01:00:00', '00:15:00', 50, 232, 4),
(65, '50H99.1', '50 m Hürden 99.1', 233, 6, 0, 2, '01:00:00', '00:15:00', 50, 233, 4),
(66, '50H91.4', '50 m Hürden 91.4', 234, 6, 0, 2, '01:00:00', '00:15:00', 50, 234, 4),
(67, '50H84.0', '50 m Hürden 84.0', 235, 6, 0, 2, '01:00:00', '00:15:00', 50, 235, 4),
(68, '50H76.2', '50 m Hürden 76.2', 236, 6, 0, 2, '01:00:00', '00:15:00', 50, 236, 4),
(69, '60H106.7', '60 m Hürden 106.7', 252, 6, 0, 2, '01:00:00', '00:15:00', 60, 252, 4),
(70, '60H99.1', '60 m Hürden 99.1', 253, 6, 0, 2, '01:00:00', '00:15:00', 60, 253, 4),
(71, '60H91.4', '60 m Hürden 91.4', 254, 6, 0, 2, '01:00:00', '00:15:00', 60, 254, 4),
(72, '60H84.0', '60 m Hürden 84.0', 255, 6, 0, 2, '01:00:00', '00:15:00', 60, 255, 4),
(73, '60H76.2', '60 m Hürden 76.2', 256, 6, 0, 2, '01:00:00', '00:15:00', 60, 256, 4),
(74, '80H76.2', '80 m Hürden 76.2', 258, 6, 0, 1, '01:00:00', '00:15:00', 80, 258, 4),
(75, '100H84.0', '100 m Hürden 84.0', 261, 6, 0, 1, '01:00:00', '00:15:00', 100, 261, 4),
(76, '100H76.2', '100 m Hürden 76.2', 259, 6, 0, 1, '01:00:00', '00:15:00', 100, 259, 4),
(77, '110H106.7', '110 m Hürden 106.7', 271, 6, 0, 1, '01:00:00', '00:15:00', 110, 271, 4),
(78, '110H99.1', '110 m Hürden 99.1', 269, 6, 0, 1, '01:00:00', '00:15:00', 110, 269, 4),
(79, '110H91.4', '110 m Hürden 91.4', 268, 6, 0, 1, '01:00:00', '00:15:00', 110, 268, 4),
(80, '200H', '200 m Hürden', 280, 6, 0, 1, '01:00:00', '00:15:00', 200, 280, 4),
(81, '300H84.0', '300 m Hürden 84.0', 290, 6, 0, 2, '01:00:00', '00:15:00', 300, 290, 4),
(82, '300H76.2', '300 m Hürden 76.2', 291, 6, 0, 2, '01:00:00', '00:15:00', 300, 291, 4),
(83, '400H91.4', '400 m Hürden 91.4', 301, 6, 0, 2, '01:00:00', '00:15:00', 400, 301, 4),
(84, '400H76.2', '400 m Hürden 76.2', 298, 6, 0, 2, '01:00:00', '00:15:00', 400, 298, 4),
(85, '1500ST', '1500 m Steeple', 209, 6, 0, 7, '01:00:00', '00:15:00', 1500, 209, 6),
(86, '2000ST', '2000 m Steeple', 210, 6, 0, 7, '01:00:00', '00:15:00', 2000, 210, 6),
(87, '3000ST', '3000 m Steeple', 220, 6, 0, 7, '01:00:00', '00:15:00', 3000, 220, 6),
(88, '5XFREI', '5x frei', 497, 6, 5, 3, '01:00:00', '00:15:00', 5, 497, 1),
(89, '5X80', '5x80 m', 498, 6, 5, 3, '01:00:00', '00:15:00', 400, 498, 1),
(90, '6XFREI', '6x frei', 499, 6, 6, 3, '01:00:00', '00:15:00', 6, 499, 1),
(91, '4X100', '4x100 m', 560, 6, 4, 3, '01:00:00', '00:15:00', 400, 560, 1),
(92, '4X200', '4x200 m', 570, 6, 4, 3, '01:00:00', '00:15:00', 800, 570, 1),
(93, '4X400', '4x400 m', 580, 6, 4, 3, '01:00:00', '00:15:00', 1600, 580, 1),
(94, '3X800', '3x800 m', 589, 6, 3, 3, '01:00:00', '00:15:00', 2400, 589, 1),
(95, '4X800', '4x800 m', 590, 6, 4, 3, '01:00:00', '00:15:00', 3200, 590, 1),
(96, '3X1000', '3x1000 m', 595, 6, 3, 3, '01:00:00', '00:15:00', 3000, 595, 1),
(97, '4X1500', '4x1500 m', 600, 6, 4, 3, '01:00:00', '00:15:00', 6000, 600, 1),
(98, 'OLYMPISCHE', 'Olympische', 601, 6, 4, 3, '01:00:00', '00:15:00', 0, 601, 1),
(99, 'AMÉRICAINE', 'Américaine', 602, 6, 4, 3, '01:00:00', '00:15:00', 0, 602, 1),
(100, 'HOCH', 'Hoch', 310, 6, 0, 6, '01:00:00', '00:20:00', 0, 310, 1),
(101, 'STAB', 'Stab', 320, 6, 0, 6, '01:00:00', '00:20:00', 0, 320, 1),
(102, 'WEIT', 'Weit', 330, 6, 0, 4, '01:00:00', '00:20:00', 0, 330, 1),
(103, 'DREI', 'Drei', 340, 6, 0, 4, '01:00:00', '00:20:00', 0, 340, 1),
(104, 'KUGEL7.26', 'Kugel 7.26 kg', 351, 6, 0, 8, '01:00:00', '00:20:00', 0, 351, 1),
(105, 'KUGEL6.00', 'Kugel 6.00 kg', 348, 6, 0, 8, '01:00:00', '00:20:00', 0, 348, 1),
(106, 'KUGEL5.00', 'Kugel 5.00 kg', 347, 6, 0, 8, '01:00:00', '00:20:00', 0, 347, 1),
(107, 'KUGEL4.00', 'Kugel 4.00 kg', 349, 6, 0, 8, '01:00:00', '00:20:00', 0, 349, 1),
(108, 'KUGEL3.00', 'Kugel 3.00 kg', 352, 6, 0, 8, '01:00:00', '00:20:00', 0, 352, 1),
(109, 'KUGEL2.50', 'Kugel 2.50 kg', 353, 6, 0, 8, '01:00:00', '00:20:00', 0, 353, 1),
(110, 'DISKUS2.00', 'Diskus 2.00 kg', 361, 6, 0, 8, '01:00:00', '00:20:00', 0, 361, 1),
(111, 'DISKUS1.75', 'Diskus 1.75 kg', 359, 6, 0, 8, '01:00:00', '00:20:00', 0, 359, 1),
(112, 'DISKUS1.50', 'Diskus 1.50 kg', 358, 6, 0, 8, '01:00:00', '00:20:00', 0, 358, 1),
(113, 'DISKUS1.00', 'Diskus 1.00 kg', 357, 6, 0, 8, '01:00:00', '00:20:00', 0, 357, 1),
(114, 'DISKUS0.75', 'Diskus 0.75 kg', 356, 6, 0, 8, '01:00:00', '00:20:00', 0, 356, 1),
(115, 'HAMMER7.26', 'Hammer 7.26 kg', 381, 6, 0, 8, '01:00:00', '00:20:00', 0, 381, 1),
(116, 'HAMMER6.00', 'Hammer 6.00 kg', 378, 6, 0, 8, '01:00:00', '00:20:00', 0, 378, 1),
(117, 'HAMMER5.00', 'Hammer 5.00 kg', 377, 6, 0, 8, '01:00:00', '00:20:00', 0, 377, 1),
(118, 'HAMMER4.00', 'Hammer 4.00 kg', 376, 6, 0, 8, '01:00:00', '00:20:00', 0, 376, 1),
(119, 'HAMMER3.00', 'Hammer 3.00 kg', 375, 6, 0, 8, '01:00:00', '00:20:00', 0, 375, 1),
(120, 'SPEER800', 'Speer 800 gr', 391, 6, 0, 8, '01:00:00', '00:20:00', 0, 391, 1),
(121, 'SPEER700', 'Speer 700 gr', 389, 6, 0, 8, '01:00:00', '00:20:00', 0, 389, 1),
(122, 'SPEER600', 'Speer 600 gr', 388, 6, 0, 8, '01:00:00', '00:20:00', 0, 388, 1),
(123, 'SPEER400', 'Speer 400 gr', 387, 6, 0, 8, '01:00:00', '00:20:00', 0, 387, 1),
(124, 'BALL200', 'Ball 200 g', 386, 6, 0, 8, '01:00:00', '00:20:00', 0, 386, 1),
(125, '5KAMPF_H', 'Fünfkampf Halle', 394, 6, 0, 9, '01:00:00', '00:15:00', 5, 394, 1),
(126, '5KAMPF_H_U18W', 'Fünfkampf Halle  U18 W', 395, 6, 0, 9, '01:00:00', '00:15:00', 5, 395, 1),
(127, '7KAMPF_H', 'Siebenkampf Halle', 396, 6, 0, 9, '01:00:00', '00:15:00', 7, 396, 1),
(128, '7KAMPF_H_U20M', 'Siebenkampf Halle  U20 M', 397, 6, 0, 9, '01:00:00', '00:15:00', 7, 397, 1),
(129, '7KAMPF_H_U18M', 'Siebenkampf Halle  U18 M', 398, 6, 0, 9, '01:00:00', '00:15:00', 7, 398, 1),
(130, '10KAMPF', 'Zehnkampf', 410, 6, 0, 9, '01:00:00', '00:15:00', 10, 410, 1),
(131, '10KAMPF_U20M', 'Zehnkampf  U20 M', 411, 6, 0, 9, '01:00:00', '00:15:00', 10, 411, 1),
(132, '10KAMPF_U18M', 'Zehnkampf   U18 M', 412, 6, 0, 9, '01:00:00', '00:15:00', 10, 412, 1),
(133, '10KAMPF_W', 'Zehnkampf Frauen', 413, 6, 0, 9, '01:00:00', '00:15:00', 10, 413, 1),
(134, '7KAMPF', 'Siebenkampf', 400, 6, 0, 9, '01:00:00', '00:15:00', 7, 400, 1),
(135, '7KAMPF_U18W', 'Siebenkampf   U18 W', 401, 6, 0, 9, '01:00:00', '00:15:00', 7, 401, 1),
(136, '6KAMPF_U16M', 'Sechskampf  U16 M', 402, 6, 0, 9, '01:00:00', '00:15:00', 6, 402, 1),
(137, '5KAMPF_U16W', 'Fünfkampf  U16 W', 399, 6, 0, 9, '01:00:00', '00:15:00', 5, 399, 1),
(138, '3/4KAMPF', 'Dreikampf/Vierkampf (Athletic-', 403, 6, 0, 9, '01:00:00', '00:15:00', 3, 403, 1),
(139, 'MILEWALK', 'Mile walk', 415, 6, 0, 7, '01:00:00', '00:15:00', 1609, 415, 5),
(140, '3000WALK', '3000 m walk', 420, 6, 0, 7, '01:00:00', '00:15:00', 3000, 420, 5),
(141, '5000WALK', '5000 m walk', 430, 6, 0, 7, '01:00:00', '00:15:00', 5000, 430, 5),
(142, '10000WALK', '10000 m walk', 440, 6, 0, 7, '01:00:00', '00:15:00', 10000, 440, 5),
(143, '20000WALK', '20000 m walk', 450, 6, 0, 7, '01:00:00', '00:15:00', 20000, 450, 5),
(144, '50000WALK', '50000 m walk', 460, 6, 0, 7, '01:00:00', '00:15:00', 50000, 460, 5),
(145, '3KMWALK', '3 km walk', 470, 6, 0, 7, '01:00:00', '00:15:00', 3000, 470, 5),
(146, '5KMWALK', '5 km walk', 480, 6, 0, 7, '01:00:00', '00:15:00', 5000, 480, 5),
(147, '10KMWALK', '10 km walk', 490, 6, 0, 7, '01:00:00', '00:15:00', 10000, 490, 5),
(148, '15KMWALK', '15 km walk', 495, 6, 0, 7, '01:00:00', '00:15:00', 15000, 495, 5),
(149, '25KMWALK', '25 km walk', 496, 6, 0, 7, '01:00:00', '00:15:00', 25000, 496, 5),
(150, '20KMWALK', '20 km walk', 500, 6, 0, 7, '01:00:00', '00:15:00', 20000, 500, 5),
(151, '30KMWALK', '30 km walk', 510, 6, 0, 7, '01:00:00', '00:15:00', 30000, 510, 5),
(152, '35KMWALK', '35 km walk', 530, 6, 0, 7, '01:00:00', '00:15:00', 35000, 530, 5),
(153, '40KMWALK', '40 km walk', 540, 6, 0, 7, '01:00:00', '00:15:00', 40000, 540, 5),
(154, '50KMWALK', '50 km walk', 550, 6, 0, 7, '01:00:00', '00:15:00', 50000, 550, 5),
(155, '5KM', '5 km', 481, 6, 0, 7, '01:00:00', '00:15:00', 5000, 481, 1),
(156, '10KM_', '10 km', 491, 6, 0, 7, '01:00:00', '00:15:00', 10000, 491, 1),
(157, '15KM', '15 km', 494, 6, 0, 7, '01:00:00', '00:15:00', 15000, 494, 1),
(158, '20KM', '20 km', 501, 6, 0, 7, '01:00:00', '00:15:00', 20000, 501, 1),
(159, '25KM', '25 km', 497, 6, 0, 7, '01:00:00', '00:15:00', 25000, 497, 1),
(160, '30KM', '30 km', 511, 6, 0, 7, '01:00:00', '00:15:00', 30000, 511, 1),
(161, '100KM', '100 km', 558, 6, 0, 7, '01:00:00', '00:15:00', 100000, 558, 1),
(162, '1HWALK', '1 h  walk', 555, 6, 0, 7, '01:00:00', '00:15:00', 1, 555, 5),
(163, '2HWALK', '2 h  walk', 556, 6, 0, 7, '01:00:00', '00:15:00', 2, 556, 5),
(164, '100KMWALK', '100 km walk', 559, 6, 0, 7, '01:00:00', '00:15:00', 100000, 559, 5),
(165, 'BALL80', 'Ball 80 g', 385, 6, 0, 8, '01:00:00', '00:20:00', 0, 385, 1),
(166, '300H91.4', '300 m Hürden 91.4', 289, 6, 0, 2, '01:00:00', '00:15:00', 300, 289, 4);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'faq'
-- 

DROP TABLE IF EXISTS faq;
CREATE TABLE faq (
  xFaq int(11) NOT NULL auto_increment,
  Frage varchar(255) NOT NULL default '',
  Antwort text NOT NULL,
  Zeigen enum('y','n') NOT NULL default 'y',
  PosTop int(11) NOT NULL default '0',
  PosLeft int(11) NOT NULL default '0',
  height int(11) NOT NULL default '0',
  width int(11) NOT NULL default '0',
  Seite varchar(255) NOT NULL default '',
  Sprache char(2) NOT NULL default '',
  PRIMARY KEY  (xFaq)
) TYPE=MyISAM AUTO_INCREMENT=54 ;

-- 
-- Daten für Tabelle 'faq'
-- 

INSERT INTO faq (xFaq, Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache) VALUES 
(4, 'Wie &auml;ndere ich die Position eines L&auml;ufers?', 'Klicken Sie dazu in das Feld "Position", &auml;ndern Sie die Zahl in die gew&uuml;nschte Position um, und best&auml;tigen Sie mit "Enter".', 'n', 50, 50, 0, 0, 'meeting_relay', 'de'),
(1, 'Wie gebe ich neue Athleten ein?', 'Mit dem Feld "Aus Stammdaten" bestimmen Sie ob Athletica nach vorhandenen Athleten sucht (Feld aktiviert) oder ob neue Eintr&auml;ge erstellt werden (Feld deaktiviert).', 'n', 0, 30, 0, 0, 'meeting_entry_add', 'de'),
(2, 'Wie f&uuml;ge ich einen Athlet mit neuem Verein hinzu?', 'W&auml;hlen Sie im Feld "Verein" nichts aus (-) und geben Sie den neuen Verein im Feld unterhalb ein. F&uuml;llen Sie den Rest wie gewohnt aus und klicken Sie auf Speichern.', 'n', 360, 100, 0, 0, 'meeting_entry_add', 'de'),
(38, 'Wie wird die Angabe "Vereinsinfo" gehandhabt?', 'Die Vereinsinformation dient als Ersatz für Verein und Region. Wurde sie bei einer Anmeldung eingetragen, wird diese Information auf der Rangliste angezeigt (anstatt Verein und Region).\r\n<br><br>\r\nBereits verwendete Vereinsinformationen (aus dem aktuellen Meeting) werden beim Tippen wie beim Verein angezeigt.<br>\r\nVerinsinformationen können auch in den Stammdaten vorhanden sein. Beim Anmelden über die Stammdaten wird die Info angezeigt, falls vorhanden. ', 'n', 370, 100, 0, 0, 'meeting_entry_add', 'de'),
(5, 'Wie suche Ich nach einer Person?', 'Geben Sie in das Suchen-Feld entweder eine Startnummer ein, oder einen Teil des Nachnamen oder des Vornamen. Dr&uuml;cken Sie [Enter] um die Suche zu starten.', 'n', 0, 67, 0, 0, 'meeting_entries_header', 'de'),
(6, 'Was bedeuten die Zeitmessungs-Einstellungen?', 'Kreuzen Sie das erste Feld an um diese Disziplin mit Zeitmessung durchzuf&uuml;hren. Bei der Serieneinteilung werden somit die n&ouml;tigen Informationen an die Zeitmessung &uuml;bermittelt. Das zweite Feld aktiviert die automatische Pr&uuml;fung auf neue Resultate. Dies passiert immer dann wenn Sie den Wettkampfmonitor &ouml;ffnen, und jede Minute wenn Sie den Monitor offen lassen (egal auf welchem Athletica-Computer).', 'n', 30, 270, 0, 0, 'meeting_definition_event_add', 'de'),
(7, 'Wie erstelle Ich einen Mehrkampf?', 'Klicken Sie auf "Neue Disziplin ...", w&auml;hlen Sie eine Kategorie aus und klicken Sie auf "Mehrkampf erstellen". Dort k&ouml;nnen Sie den Mehrkampftyp aussuchen und er wird erstellt. ', 'n', 10, 400, 0, 0, 'meeting_definition_header', 'de'),
(8, 'Was kann Ich alles auf der Mehrkampfansicht einstellen?', 'Hier werden die Disziplinen und Runden f&uuml;r den Mehrkampf definiert. Wurde ein Mehrkampf erstellt, sehen Sie hier die dazugeh&ouml;rigen Disziplinen. Sie k&ouml;nnen alle Einstellungen wie Wind oder Zeitmessung hier t&auml;tigen! Die letzte Spalte zeigt Ihnen die Disziplin welche gruppen&uuml;bergreifend als Letzte durchgef&uuml;hrt wird. Mit einem Klick auf den ausgew&auml;hlten Kreis wird dieser Modus ausgeschaltet. Es kann auch eine andere Disziplin als letzte ausgew&auml;hlt werden. Sind die Mehrk&auml;mpfe definiert, melden Sie zuerst die Athleten an und teilen ihnen eine Gruppe zu (Gruppen zuteilen unter Anmeldung). Danach k&ouml;nnen hier die Startzeiten für jede Disziplin und Gruppe (Runde) gesetzt werden.', 'n', 90, 370, 0, 0, 'meeting_definition_category', 'de'),
(9, 'Muss Ich immer auch Appell- und Stellzeit eingeben?', 'Nein. Wenn Sie nur eine Startzeit angeben, werden Appell- und Stellzeit automatisch anhand der Disziplinendefinition berechnet. Die voreingestellte Zeit kann unter "Administration / Disziplinen" ge&auml;ndert werden.', 'n', 310, 190, 0, 0, 'meeting_definition_event_add', 'de'),
(10, 'Wie gross erscheinen die Bilder?', 'Alle Bilder werden auf eine H&ouml;he von 30 Pixel skaliert (im richtigen Seitenverh&auml;ltnis). Ein zu grosses Logo kann also problemlos eingef&uuml;gt werden. M&ouml;chten Sie mehrere Logos oder Sponsoren hintereinander anzeigen, erstellen Sie ein 30 Pixel hohes Bild das maximal 709 Pixel breit ist und f&uuml;gen die einzelnen Teile darin zusammen.', 'n', 260, 10, 0, 0, 'admin_page_layout', 'de'),
(11, 'Weshalb gelingen meine Ausdrucke nicht?', 'Mit den Athletica eigenen Kopf- und Fusszeilen muss darauf geachtet werden dass die Einstellungen Ihres Webbrowsers korrekt sind. Es d&uuml;rfen keine Kopf- oder Fusszeilen eingerichtet sein und die Seitenr&auml;nder m&uuml;ssen m&ouml;glichst klein sein. Gehen Sie im Menu des Webbrowsers in "Datei" --> "Seite einrichten".<br><br> <a href=''./img/pagelayoutie.jpg'' target=''_blank''>Idealeinstellung f&uuml;r den Internet Explorer (hier klicken)</a><br><br> <a href=''./img/pagelayoutff.jpg'' target=''_blank''>Idealeinstellung f&uuml;r den Firefox (hier klicken)</a><br><br> So eingerichtet werden die Athletica-Listen richtig ausgedruckt.', 'n', 260, 420, 0, 0, 'admin_page_layout', 'de'),
(12, 'Comment changer la position d&#039;un coureur?', 'Cliquez sur la case &quot;Position&quot;, transformez le chiffre en la position souhait&eacute;e et confirmez par &quot;Enter&quot;.', 'n', 50, 50, 0, 0, 'meeting_relay', 'fr'),
(13, 'Comment introduire de nouveaux athl&egrave;tes?', 'Avec la case &quot;A partir des donn&eacute;es initiales &quot; vous d&eacute;finissez si Athletica recherche des athl&egrave;tes existant (case activ&eacute;e) ou si de nouvelles entr&eacute;es sont effectu&eacute;es (case d&eacute;sactiv&eacute;e).', 'n', 0, 30, 0, 0, 'meeting_entry_add', 'fr'),
(14, 'Comment ajouter un athl&egrave;te avec une nouvelle soci&eacute;t&eacute;?', 'Ne choisissez rien dans la case &quot;soci&eacute;t&eacute;&quot; (-) et introduisez la nouvelle soci&eacute;t&eacute; dans la case en dessous. Remplissez le reste comme d&#039;habitude et cliquez sur enregistrer.', 'n', 360, 100, 0, 0, 'meeting_entry_add', 'fr'),
(36, 'Benvenuti in Athletica 3.0', 'Per un lavoro senza errori con Athletica 3.0 effettuate come primo utente un aggiornamento della banca dati della vostra societ&agrave;. Questa funzione la potete trovare nel menu sotto "Amministrazione dei dati” -&gt; "Aggiornamento della banca dati&quot;.', 'n', 50, 50, 0, 0, 'meeting', 'it'),
(37, 'Der Lizenztyp', 'Es sind 3 Auswahlmöglichkeiten vorhanden:<br>\r\n- Normale Lizenz, zwingend mit Lizenznummer<br>\r\n- Tageslizenz<br>\r\n- Keine Lizenz, z.B. nich lizenzierte Schüler<br><br>\r\nDer Lizenztyp kann zu jedem Zeitpunkt verändert werden. Beachten Sie, dass eine vorhandene Lizenznummer gelöscht wird falls der Typ auf "Tageslizenz" oder "Keine Lizenz" umgestellt wird.<br>\r\nWird auf den Typ "Normale Lizenz" umgestellt, muss die Lizenznummer eingegeben werden. Die Umstellung erfolgt danach automatisch.', 'n', 300, 100, 0, 0, 'meeting_entry', 'de'),
(16, 'Comment rechercher une personne?', 'Entrez dans la case rechercher soit un dossard soit une partie du nom de famille ou du pr&eacute;nom. Pressez [Enter] pour commencer la recherche.', 'n', 0, 67, 0, 0, 'meeting_entries_header', 'fr'),
(17, 'Que signifient les r&eacute;glages de chronom&eacute;trage?', 'Cochez la premi&egrave;re case pour ex&eacute;cuter cette discipline avec chronom&eacute;trage. Lors de l&#039;attribution des s&eacute;ries, les informations n&eacute;cessaires sont ainsi transmises au chronom&eacute;trage. La deuxi&egrave;me case active le contr&ocirc;le automatique des nouveaux r&eacute;sultats. Cela arrive toujours quand vous ouvrez le moniteur de comp&eacute;tition et toutes les minutes si vous laissez le moniteur ouvert (sur n&#039;importe quel ordinateur Athletica).', 'n', 30, 270, 0, 0, 'meeting_definition_event_add', 'fr'),
(18, 'Comment cr&eacute;er un concours multiple?', 'Cliquez sur &quot;Nouvelle discipline ...&quot;, choisissez une cat&eacute;gorie et cliquez sur &quot;Cr&eacute;er un concours multiple &quot;. L&agrave; vous pouvez choisir le type de concours multiple et il est cr&eacute;&eacute;.', 'n', 10, 400, 0, 0, 'meeting_definition_header', 'fr'),
(19, 'Que puis-je tout installer sur affichage du concours multiple?', 'Les disciplines et les tours du concours multiple sont d&eacute;finis ici. Si un concours multiple a &eacute;t&eacute; cr&eacute;&eacute;, vous voyez ici les disciplines qui en font partie. Vous pouvez activer tous les r&eacute;glages comme an&eacute;mom&egrave;tre ou chronom&eacute;trage ici! La derni&egrave;re colonne vous montre la discipline qui est organis&eacute;e la derni&egrave;re tous groupes confondus. Un clic sur la croix d&eacute;sir&eacute;e, d&eacute;sactive ce mode. On peut aussi choisir une autre discipline comme derni&egrave;re discipline. Quand les concours multiples sont d&eacute;finis, vous annoncez en premier les athl&egrave;tes et leur attribuez un groupe (attribuer les groupes sous inscription). Ensuite vous pouvez mettre ici les heures de d&eacute;part de chaque discipline et groupe (tour).', 'n', 90, 370, 0, 0, 'meeting_definition_category', 'fr'),
(20, 'Est-ce que je dois toujours taper l&#039;heure d&#039;appel et l&#039;heure de rassemblement?', 'Non. Si vous n&#039;indiquez qu&#039;une heure de d&eacute;part, l&#039;heure d&#039;appel et l&#039;heure de rassemblement sont  automatiquement calcul&eacute;es &agrave; l&#039;aide de la d&eacute;finition de la discipline. L&#039;heure r&eacute;gl&eacute;e &agrave; l&#039;avance peut &ecirc;tre chang&eacute;e sur &quot;Administration / Disciplines&quot;.', 'n', 310, 190, 0, 0, 'meeting_definition_event_add', 'fr'),
(21, 'Come posso modificare la posizione di un corridore?', 'Ciccare dapprima sul campo posizione, modificate il numero alla posizione desiderata e confermate con Enter.', 'n', 50, 50, 0, 0, 'meeting_relay', 'it'),
(22, 'Come posso inserire nuovi atleti?', 'Con il campo &quot;da archivio&quot; definite se &quot;Athletica&quot; deve trattare nuovi atleti.', 'n', 0, 30, 0, 0, 'meeting_entry_add', 'it'),
(23, 'Come inserisco un atleta con una nuova societ&agrave; d''appartenenza?', 'Scegliete nel campo &quot;Societ&agrave;&quot; non da (-) e inserite il nome della nuova societ&agrave; nel campo. Completate il resto come convenuto ciccando al termine su memorizza.', 'n', 360, 100, 0, 0, 'meeting_entry_add', 'it'),
(34, 'Willkommen bei Athletica 3.0', 'Für das fehlerfreie Arbeiten mit Athletica 3.0 führen Sie bitte als erstes ein Stammdatenupdate durch. Diese Funktion befindet sich im Menu unter "Administration" -> "Update der Stammdaten".', 'n', 50, 50, 0, 0, 'meeting', 'de'),
(35, 'Bienvenue chez Athletica 3.0', 'Pour travailler sans faute avec Athletica 3.0, veuillez tout d''abord ex&eacute;cuter une mise &agrave; jour des donn&eacute;es de base. Cette fonction se trouve dans le menu sous "Administration" -&gt; "Mise &agrave; jour des donn&eacute;es de base".', 'n', 50, 50, 0, 0, 'meeting', 'fr'),
(25, 'Come posso cercare un/una atleta?', 'Inserite nel campo ricerca: il numero di partenza o il cognome o il nome (o una parte di essi). Premete poi Enter per avviare la ricerca', 'n', 0, 67, 0, 0, 'meeting_entries_header', 'it'),
(26, 'Cosa significano le configurazioni del cronometraggio?', 'Per attivare una disciplina con il cronometraggio dovete crociare il primo campo. Al momento della creazione delle batterie verranno cos&igrave; fornite le informazioni necessarie. Il secondo campo attiva automaticamente l''esame di nuovi risultati. Questo succeder&agrave; sempre fin quando lascerete attivo il vostro PC per la manifestazione che state elaborando.', 'n', 30, 270, 0, 0, 'meeting_definition_event_add', 'it'),
(27, 'Come posso impostare una gara multipla?', 'Dapprima cliccare su &quot;nuova disciplina&quot;, scegliete quindi una categoria poi inserite &quot;preparazione gara multipla&quot;. Qui potete infine programmare una gara multipla.', 'n', 10, 400, 0, 0, 'meeting_definition_header', 'it'),
(28, 'Cosa posso trovare nella visione completa di una gara multipla?', 'In questo campo vengono definite tutte le caratteristiche di ogni singola disciplina delle gare multiple che state elaborando: vento, cronometraggio,.! L''ultima colonna vi da la successione delle discipline per ogni gruppo. Ciccando sul rispettivo cerchio &egrave; possibile disinserire questa informazione. Si pu&ograve; anche definire un''altra discipline come ultima. Una volta definite le prove multiple &eacute; possibile suddividere in gruppi gli atleti annunciati (suddivisione die gruppi sotto iscrizioni). Al termine &egrave; pure possibile organizzare gli orari di partenza di ogni discipline e di ogni gruppo previsto.', 'n', 90, 370, 0, 0, 'meeting_definition_category', 'it'),
(29, 'Devo sempre inserire anche l''orario dall''appello preliminare e dell''appello di gara?', 'No. Se inserite un solo orario appello preliminare e di gara vengono calcolati automaticamente attraverso la definizione delle discipline gi&agrave; impostata. L''orario previsto pu&ograve; sempre essere modificato ciccando &quot;Amministrazione/discipline&quot;.', 'n', 310, 190, 0, 0, 'meeting_definition_event_add', 'it'),
(30, 'De quelle taille sont les  images?', 'Toutes les images sont gradu&eacute;es sur une hauteur de 30 Pixel (en rapport correct avec la page). Un logo trop grand peut donc &ecirc;tre ins&eacute;r&eacute; sans probl&egrave;me. D&eacute;sirez-vous afficher plusieurs logos ou sponsors &agrave; la suite, alors cr&eacute;ez une image de 30 Pixel, d''une largeur maximale de 709 Pixel et assemblez-y les diff&eacute;rentes parties.', 'n', 260, 10, 0, 0, 'admin_page_layout', 'fr'),
(31, 'Pourquoi je n''arrive pas &agrave; imprimer?', 'Avec les en-t&ecirc;tes et les pieds de page propres &agrave; Athletica, il faut faire attention &agrave; installer correctement  votre navigateur. Aucune en-t&ecirc;te, aucun pied de page ne doivent &ecirc;tre install&eacute;s et les bords doivent le plus petits possible. Dans le menu du navigateur, allez dans &quot;Fichier&quot; --&gt; &quot;Mise en page&quot;.<br><br><a href=''./img/pagelayoutie.jpg'' target=''_blank''>Installation id&eacute;ale pour l''Internet Explorer (cliquer ici)</a><br><br> <a href=''./img/pagelayoutff.jpg'' target=''_blank''>Installation id&eacute;ale pour le Firefox (cliquer ici)</a><br><br>\r\nAvec cette installation,  les listes Athletica seront imprim&eacute;es correctement.\r\n', 'n', 260, 420, 0, 0, 'admin_page_layout', 'fr'),
(32, 'In quale grandezza compaiono le fotografie?', 'Tutte le foto vengono &quot;scannerizzate&quot; con altezza 30 pixel e con le dimensioni proporzionali a quelle della pagina. Un logo di dimensioni esagerate pu&ograve; dunque essere inserito senza problemi. \r\nSe desiderate mostrare diversi logo o sponsor uno dopo l''altro preparate una foto di 30 pixel che al massimo &egrave; larga 709 pixel. Componete poi le varie parti. ', 'n', 260, 10, 0, 0, 'admin_page_layout', 'it'),
(33, 'Come mai non riesco a stampare i miei documenti?', 'Con le intestazioni ed i pie'' di pagina del programma Athletica &egrave; opportuno assicurarsi che le impostazione del vostro browser web siano corrette. Non &egrave; possibile inserire intestazioni o pie'' di pagina propri ed i bordi pagina devono essere possibilmente il pi&ugrave; ridotti possibile. Andate al menu browser web che si trova nell''archivio alla pagina impostazioni.\r\nCliccare su <br><br><a href=''./img/pagelayoutie.jpg'' target=''_blank''>impostazioni ottimali per Internet Explorer oppure,</a><br><br>a seconda dei casi, su <br><br><a href=''./img/pagelayoutff.jpg'' target=''_blank''>impostazioni ideali per Firefox.</a>\r\n<br><br>\r\nCon queste impostazioni le liste stampate da Athletica usciranno corrette.', 'n', 260, 420, 0, 0, 'admin_page_layout', 'it'),
(39, 'Le type de licence', 'Il y a 3 possibilit&eacute;s &agrave; choix:<br>\r\n- Licence normale, avec obligatoirement un num&eacute;ro de licence <br>\r\n- Licence journali&egrave;re <br>\r\n- Pas de licence, par ex. &eacute;coliers non licenci&eacute;s <br>\r\n<br>\r\nLe type de licence peut &ecirc;tre modifi&eacute; en tout temps. Remarquez qu''un num&eacute;ro de licence est effac&eacute; si on passe au type de &quot;licence journali&egrave;re&quot; ou &quot;pas de licence&quot;.<br>\r\nSi on passe au type de &quot;licence normale&quot;, il faut introduire le num&eacute;ro de la licence. Le changement se fait alors automatiquement.', 'n', 300, 100, 0, 0, 'meeting_entry', 'fr'),
(40, 'Comment manie-t-on l''indication &quot;Info de soci&eacute;t&eacute;&quot;?', 'L''information de soci&eacute;t&eacute; sert &agrave; remplacer la soci&eacute;t&eacute; et la r&eacute;gion. Si elle a &eacute;t&eacute; introduite lors d''une inscription, cette information appara&icirc;t sur la liste de r&eacute;sultats (au lieu de la soci&eacute;t&eacute; et de la r&eacute;gion). <br>\r\n<br>\r\nLes informations de soci&eacute;t&eacute;s d&eacute;j&agrave; utilis&eacute;es (du meeting actuel) sont affich&eacute;es en tapant, de m&ecirc;me qu''avec la soci&eacute;t&eacute;.<br>\r\nLes informations de soci&eacute;t&eacute; peuvent &eacute;galement se trouver dans les donn&eacute;es de base. En inscrivant par les donn&eacute;es de base, l''info est affich&eacute;e, si elle existe.', 'n', 370, 100, 0, 0, 'meeting_entry_add', 'fr'),
(41, 'Tipi di licenza', 'Esistono tre tipi di licenza:<br>\r\n- Licenza normale con il rispettivo numero<br>\r\n- Licenza giornaliera<br>\r\n- Nessuna licenza, per esempio gli scolari non licenziati<br>\r\n<br>\r\nIl tipo di licenza pu&ograve; essere modificato in ogni momento. Nel caso una licenza normale viene modificata in un''altra categoria (giornaliera, senza licenza) il rispettivo numero viene eliminato.<br>\r\nNel caso si volesse trasformare una licenza giornaliera o un non licenziato in licenza normale occorre inserire un numero di licenza. La mutazione avviene poi automaticamente.', 'n', 300, 100, 0, 0, 'meeting_entry', 'it'),
(42, 'Come viene utilizzato il dato "Informazione della societ&agrave;"?', 'L''informazione della societ&agrave; &eacute; utilizzata in sostituzione della societ&agrave; e della rispettiva regione. Nel caso venisse utilizzata la stessa comparirebbe nelle classifiche al posto della societ&agrave; o della regione.<br><br>\r\nLe informazioni di societ&agrave; gi&agrave; inserite appaiono come societ&agrave;.\r\nLe informazioni di societ&agrave; vengono trattate con i dati d''archivio. Per ogni annuncio attraverso l''archivio le stesse appariranno normalmente.', 'n', 370, 100, 0, 0, 'meeting_entry_add', 'it'),
(43, 'Was ist neu?', '<ul> <li>Kategorie mit Angabe des Geschlechtes</li> <li>Mehrkampfreihenfolge für Rangliste und Bestenliste nach WO</li> <li>Exportfunktionen: Startnummern und Ranglisten können nun in ein MS Excel kompatibles Format umgewandelt werden.</li> <li>Qualifikation: Athleten können Status „verzichtet“ erhalten</li> <li>Erweiterte Meeting-Statistik</li> <li>Integration U14: Anmeldung für Schüler erweitert, Lizenztyp und Lizenznummer können bearbeitet werden, Vereinsinfo</li> <li>2 neue Disziplinen: Ball 80g, 300m Hürden 91.4</li> <li>Zeitmessung: Funktion für das explizite Exportieren der Serieneinteilung an die Zeitmessung</li> <li>Omega: Eigene Kategorien werden korrekt übermittelt. Disziplinenliste wurde gekürzt <li>Alge: Fehler in Punkteberechnung behoben</li> <li>Athletica MAX kann nun in ein beliebiges Verzeichnis installiert werden</li> <li>Datensicherung mit Verifizierung</li> <li>Diverse Anpassungen</li> </ul>', 'n', 350, 50, 0, 0, 'meeting', 'de'),
(44, 'Cosa ü nuovo?', '<ul> <li>Categorie con aggiunta se M o F</li> <li>La successione nellegare multiple e nella lista delle migliori prestazioni ö conforme al WO</li> <li>I dati da esportare: numero di partenza o classifiche sono ora compatibili con un formato Microsoft Excel</li> <li>Per la qualificazione degli atleti può ora essere utilizzata la scelta “rinuncia”</li> <li>Le statistiche a disposizione per i meeting sono ora aumentate</li> <li>Integrazione nella categoria U14: gli annunci per gli scolari sono ora possibili, é possibile l’elaborazione delle licenze e die numeri di licenza, Informazioni per le società <li>2 nuove discipline: Pallina 80 g, 300 m ostacoli 91.4</li> <li>Cronometraggio: é ora prevista una funzione per l’esportazione delle batterie direttamente al cronometraggio</li> <li>Omega: le categoie compaiono ora in modo correto. L’elenco delle discipline è ora abbreviato</li> <li>Alge: si é ora ovviato agli errori nei punteggi</li> <li>Athletica MAX può ora venir installato a piacimento</li> <li>Esiste la verifica della sicurezza die dati</li> <li>Ulteriori piccoli dettagli sono stati modificati</li> </ul>', 'n', 350, 50, 0, 0, 'meeting', 'it'),
(45, 'Qu’y a-t-il de nouveau?', '<ul> <li>Catégorie avec indication du sexe </li> <li>Ordre du concours multiple pour les listes de résultats et les listes des meilleurs selon RO </li> <li>Fonctions d’exportation: Dossards et listes de résultats peuvent maintenant être transformés en un format MS Excel compatible.</li> <li>Qualification: Les athlètes peuvent obtenir le statut „renonce“ </li> <li>Statistiques élargies du meeting </li> <li>Intégration U14: Inscription élargie aux écoliers, type de licence et numéro de licence peuvent être remaniés, info de société </li> <li>2 nouvelles disciplines: Balle 80g, 300m haies 91.4</li> <li>Chronométrage: Fonction pour une exportation explicite de la répartition des séries au chronométrage </li> <li>Omega: Propres catégories sont transmises correctement. La liste des disciplines a été raccourcie </li> <li>Alge: Erreur dans le calcul des points est réparée</li> <li>Athletica MAX peut maintenant être installé dans une liste à choix </li> <li>Sauvegarde des données avec vérification </li> <li>Diverses adaptations</li> </ul>', 'n', 350, 50, 0, 0, 'meeting', 'fr'),
(46, 'Was ist neu?', '<ul> \r\n<li>Meeting Passwort Schutz</li>\r\n<li>SVM Modus 2007</li>\r\n<li>Team SM Modus 2007</li>\r\n<li>Rangpunktewertung für Mannschaftskämpfe</li>\r\n<li>Startnummern nach Mannschaften</li>\r\n<li>Anmeldemaske über Lizenznummer erweitert</li>\r\n<li>Telefonbuchsortierung (äöü usw.)</li>\r\n<li>Runden zusammenfassen</li>\r\n<li>Anzeige von „Wettkampf-Info“ ausgedehnt</li>\r\n<li>Listen merken sich die letzte Sortierung</li>\r\n<li>neuer Zeitplan Wettkampfbüro</li>\r\n<li>Diverse Anpassungen</li>\r\n</ul>', 'n', 350, 50, 0, 0, 'meeting', 'de'),
(47, 'Wie kann ich Runden (Kategorien) zusammenfassen?', 'Runden der gleichen Disziplinen aus unterschiedlichen Kategorien können zusammengefasst werden. Hier werden alle Runden angezeigt, die Sie zusammenfassen können.<br>\r\nDie Runde der aktuell ausgewählten Disziplin wird beim Zusammenfassen zur „Hauptrunde“. Nur über die Hauptrunde können Sie nachher diese Disziplin durchführen.<br>\r\nWählen Sie mit den Kästchen alle Runden aus, welche mit der aktuellen zusammengehören sollen.', 'n', 300, 10, 0, 0, 'meeting_definitions', 'de'),
(48, 'Qu’y a-t-il de nouveau?', '<ul> <li>Mot de passe protection du meeting </li>\r\n<li>Formule CSI 2007</li>\r\n<li>Formule CS Team 2007</li>\r\n<li>Classements aux points pour les concours par équipe</li>\r\n<li>Dossards selon les équipes</li>\r\n<li>Elargir la grille d’inscription avec les numéros de licence</li>\r\n<li>Tri annuaire téléphonique (äöü etc.)</li>\r\n<li>Regrouper des tours</li>\r\n<li>Affichage élargi des "Infos-concours"</li>\r\n<li>Les listes remarquent le dernier tri</li>\r\n<li>Nouvel horaire bureau des calculs</li>\r\n</ul>', 'n', 350, 50, 0, 0, 'meeting', 'fr'),
(49, 'Wie schütze ich die Bearbeitungsfunktionen?', 'Unter dem Menupunkt „Administration“ kann ein Passwort für das aktuelle Meeting gesetzt werden.<br>\r\nUm Änderungen an einem geschützten Meeting vorzunehmen, muss der Benutzer das Passwort eingeben. Er bleibt so lange angemeldet, bis er Athletica (den Browser) schliesst. Der Speaker-Modus kann weiterhin ohne Passwort erreicht werden.<br>\r\nDiese Funktion ist dazu gedacht, unbeabsichtigte Änderungen durch z.B. den Speaker zu verhindern. Sie ist kein effektiver Schutz gegen bösartige Angriffe!', 'n', 350, 450, 0, 0, 'meeting', 'de'),
(50, 'Comment regrouper des tours (cat&eacute;gories)?', 'Il est possible de regrouper des tours de la m&ecirc;me discipline de cat&eacute;gories diff&eacute;rentes. Tous les tours que vous pouvez regrouper sont affich&eacute;s ici.&lt;br&gt;\r\nAu regroupement, le tour de la discipline actuellement s&eacute;lectionn&eacute;e devient le „tour principal“. Vous ne pouvez ensuite organiser cette discipline que par le tour principal.&lt;br&gt;\r\nS&eacute;lectionnez tous les tours qui doivent faire partie de l’actuel.', 'n', 300, 10, 0, 0, 'meeting_definitions', 'fr'),
(51, 'Comment protéger les fonctions de remaniement?', 'Il est possible d’insérer un mot de passe au point „Administration“ du menu.<br>\r\nPour apporter des changements à un meeting protégé, l’utilisateur doit introduire le mot de passe.  Il reste annoncé ainsi jusqu’à ce qu’il ferme Athletica (le navigateur). Le mode speaker peut toujours être atteint sans mot de passe.<br>\r\nCette fonction est prévue pour empêcher des changements involontaires par ex. par le speaker. Elle ne constitue pas de protection effective contre des agressions méchantes!', 'n', 350, 450, 0, 0, 'meeting', 'fr'),
(52, 'Was ist neu?', '<b>Software</b>\r\n<ul>\r\n	<li>Microsoft Windows Vista Kompatibilität (Installer)</li>\r\n	<li>Aktuellere Apache-Webserver Version 2.0.59 (Installer)</li>\r\n</ul>\r\n\r\n<b>Bugfixes</b>\r\n<ul>\r\n	<li>\r\n		Anmeldungsfehler "Doppelter Eintrag":<br/>\r\n		Wenn ein Athlet bereits einmal ohne Lizenznummer angemeldet wurde, und zu einem späteren Zeitpunkt mit Lizenznummer angemeldet werden soll, wird eine Fehlermeldung ausgegeben.<br/>\r\n		In einem solchen Fall kann nun in der Administration die Lizenznummer der Athleten angepasst werden.\r\n	</li>\r\n	<li>Staffeln können nun in die OMEGA-Zeitmessung eingelesen werden</li>\r\n	<li>Die Punkte für SVM-Nationalliga 2007 werden nun korrekt zusammengezählt</li>\r\n	<li>Bei schlechteren Leistungen werden keine negativen Punkte mehr vergeben</li>\r\n	<li>Wenn mehrere Meetings in Athletica eingetragen sind, wird eine gelöschte Anmeldung nur für das aktuelle Meeting, nicht für alle entfernt</li>\r\n	<li>Problem beim Eintragen in die Bestenliste von Meetings mit "&" im Namen behoben</li>\r\n	<li>Der Ladebalken beim Stammdaten-Update wird nun richtig angezeigt</li>\r\n	<li>In der Mehrkampf-Rangliste wird die Leistung auch bei 0 Punkten angezeigt</li>\r\n	<li>In der Versuchs-Ansicht der Rangliste wird bei Wurf-Disziplinen der Wind nicht mehr angezeigt</li>\r\n	<li>Die Versuche in der Rangliste werden nach Versuchreihenfolge, bei Hoch und Stab nach Leistung aufsteigend sortiert</li>\r\n	<li>Übersichtlichere Administrations-Startseite</li>\r\n</ul>\r\n\r\n<b>Erweiterungen</b>\r\n<ul>\r\n	<li>Die Rangpunkteformel kann nun frei definiert werden</li>\r\n	<li>Die neuen Kategorien Senioren M und Senioren W wurden hinzugefügt</li>\r\n	<li>Unterstützung für die ALGE OPTIc2 Zeitmessung (Wahl zwischen OPTIc und OPTIc2 unter Zeitmessung)</li>\r\n</ul>', 'y', 50, 50, 400, 650, 'meeting', 'de'),
(53, 'Qu’y a-t-il de nouveau?', '<b>Logiciel</b>\r\n<ul>\r\n	<li>Compatibilité Microsoft Windows Vista (Installer)</li>\r\n	<li>Version serveur web Apache assez récent 2.0.59 (Installer)</li>\r\n</ul>\r\n\r\n<b>Bugfixes</b>\r\n<ul>\r\n	<li>\r\n		Annonce d’erreur "Double enregistrement":<br/>\r\n		Si un athlète a une fois été inscrit sans numéro de licence et que plus tard on veut l’inscrire avec un numéro de licence, une annonce d’erreur est délivrée.<br/>\r\n		Dans un tel cas, il est possible d’adapter le numéro de licence des athlètes dans l’administration.\r\n	</li>\r\n	<li>Les relais peuvent maintenant être lus dans le chronométrage OMEGA</li>\r\n	<li>Les points pour la ligue nationale A 2007 sont maintenant calculés correctement</li>\r\n	<li>Lors de performances moins bonnes, il n’y a plus attribution de points négatifs</li>\r\n	<li>Lorsque plusieurs meetings sont enregistrés dans Athletica, une inscription supprimée n’est pas effacée dans tous les meetings, seulement dans le meeting actuel</li>\r\n	<li>Problème au cours de l’enregistrement dans la liste des meilleurs des meetings écarté avec "&" dans le nom</li>\r\n	<li>La barre de chargement pour la mise à jour des données de base est maintenant affichée correctement</li>\r\n	<li>Dans la liste des résultats des concours multiples, la performance est affichée même en cas de 0 point</li>\r\n	<li>Le vent n’est plus indiqué sur l’affichage des listes de résultats pour les essais des disciplines de lancer</li>\r\n	<li>Dans la liste de résultats, les essais sont indiqués dans l’ordre des passages, pour le saut en hauteur et à la perche, ils sont triés progressivement selon la performance</li>\r\n	<li>Page de démarrage Administration plus claire \r\n</ul> \r\n\r\n<b>Extensions</b>\r\n<ul>\r\n	<li>La formule des points par rang peut maintenant être définie librement</li>\r\n	<li>Les nouvelles catégories vétérans M et vétérans W ont été ajoutées</li>\r\n	<li>Soutien pour le chronométrage ALGE OPTIc2 (choix entre  OPTIc et OPTIc2 sous chronométrage)</li>\r\n</ul>', 'n', 350, 50, 0, 0, 'meeting', 'fr');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'kategorie'
-- 

DROP TABLE IF EXISTS kategorie;
CREATE TABLE kategorie (
  xKategorie int(11) NOT NULL auto_increment,
  Kurzname varchar(4) NOT NULL default '',
  Name varchar(30) NOT NULL default '',
  Anzeige int(11) NOT NULL default '1',
  Alterslimite tinyint(4) NOT NULL default '99',
  Code varchar(4) NOT NULL default '',
  Geschlecht enum('m','w') NOT NULL default 'm',
  PRIMARY KEY  (xKategorie),
  UNIQUE KEY Kurzname (Kurzname),
  KEY Anzeige (Anzeige)
) TYPE=MyISAM AUTO_INCREMENT=20 ;

-- 
-- Daten für Tabelle 'kategorie'
-- 

INSERT INTO kategorie (xKategorie, Kurzname, Name, Anzeige, Alterslimite, Code, Geschlecht) VALUES 
(1, 'MAN_', 'MAN', 1, 99, 'MAN_', 'm'),
(2, 'U20M', 'U20 M', 4, 19, 'U20M', 'm'),
(3, 'U18M', 'U18 M', 5, 17, 'U18M', 'm'),
(4, 'U16M', 'U16 M', 6, 15, 'U16M', 'm'),
(5, 'U14M', 'U14 M', 7, 13, 'U14M', 'm'),
(6, 'U12M', 'U12 M', 8, 11, 'U12M', 'm'),
(7, 'WOM_', 'WOM', 10, 99, 'WOM_', 'w'),
(8, 'U20W', 'U20 W', 13, 19, 'U20W', 'w'),
(9, 'U18W', 'U18 W', 14, 17, 'U18W', 'w'),
(10, 'U16W', 'U16 W', 15, 15, 'U16W', 'w'),
(11, 'U14W', 'U14 W', 16, 13, 'U14W', 'w'),
(12, 'U12W', 'U12 W', 17, 11, 'U12W', 'w'),
(13, 'U23M', 'U23 M', 3, 22, 'U23M', 'm'),
(14, 'U23W', 'U23 W', 12, 22, 'U23W', 'w'),
(16, 'U10M', 'U10 M', 9, 9, 'U10M', 'm'),
(17, 'U10W', 'U10 W', 18, 9, 'U10W', 'w'),
(18, 'SENM', 'SEN M', 2, 99, 'SENM', 'm'),
(19, 'SENW', 'SEN W', 11, 99, 'SENW', 'w');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'kategorie_svm'
-- 

DROP TABLE IF EXISTS kategorie_svm;
CREATE TABLE kategorie_svm (
  xKategorie_svm int(11) NOT NULL auto_increment,
  Name varchar(100) NOT NULL default '',
  Code varchar(5) NOT NULL default '',
  PRIMARY KEY  (xKategorie_svm),
  KEY Code (Code)
) TYPE=MyISAM AUTO_INCREMENT=36 ;

-- 
-- Daten für Tabelle 'kategorie_svm'
-- 

INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
(1, '26.1 Nationalliga A Männer', '26_01'),
(2, '26.2 Nationalliga B Männer', '26_02'),
(3, '26.3 Nationalliga C Männer', '26_03'),
(4, '26.4 1. Liga Männer', '26_04'),
(5, '26.5 2. Liga Männer', '26_05'),
(6, '26.6 3. Liga Männer', '26_06'),
(7, '26.7 4. Liga Männer', '26_07'),
(8, '26.8 Senioren', '26_08'),
(9, '26.9 Junioren I', '26_09'),
(10, '26.1 Junioren 2', '26_10'),
(11, '26.11 Männliche Jugend A', '26_11'),
(12, '26.12 Männliche Jugend A Mehrkampf', '26_12'),
(13, '26.13 Männliche Jugend B', '26_13'),
(14, '26.14 Männliche Jugend B Mehrkampf', '26_14'),
(15, '26.15 Schüler A', '26_15'),
(16, '26.16 Schüler A Mannschaftswettkampf', '26_16'),
(17, '26.17 Schüler B Mannschaftswettkampf', '26_17'),
(18, '27.1 Nationalliga A Frauen', '27_01'),
(19, '27.2 Nationalliga B Frauen', '27_02'),
(20, '27.3 1. Liga Frauen', '27_03'),
(21, '27.4 2. Liga Frauen', '27_04'),
(22, '27.5 Seniorinnen', '27_05'),
(23, '27.6 Juniorinnen', '27_06'),
(24, '27.7 Weibliche Jugend A', '27_07'),
(25, '27.8 Weibliche Jugend A Mehrkampf', '27_08'),
(26, '27.9 Weibliche Jugend B', '27_09'),
(27, '27.1 Weibliche Jugend B Mehrkampf', '27_10'),
(28, '27.11 Schülerinnen A', '27_11'),
(29, '27.12 Schülerinnen A Mannschaftswettkampf', '27_12'),
(30, '27.13 Schülerinnen B Mannschaftswettkampf', '27_13'),
(31, '27.14 Mixed-Team Schülerinnen B /Schüler B', '27_14'),
(32, '28.1 Männer', '28_01'),
(33, '28.2 Frauen', '28_02'),
(34, '29.1 Männer ohne Lizenz', '29_01'),
(35, '29.2 Frauen ohne Lizenz', '29_02');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'land'
-- 

DROP TABLE IF EXISTS land;
CREATE TABLE land (
  xCode char(3) NOT NULL default '',
  Name varchar(100) NOT NULL default '',
  Sortierwert int(11) NOT NULL default '0',
  PRIMARY KEY  (xCode)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle 'land'
-- 

INSERT INTO land (xCode, Name, Sortierwert) VALUES 
('ALB', 'Albania', 1),
('ALG', 'Algeria', 2),
('ANG', 'Anguilla', 3),
('ANO', 'Angola', 4),
('ANT', 'Antigua', 5),
('ARG', 'Argentina', 6),
('ARM', 'Armenia', 7),
('AUS', 'Australia', 8),
('AUT', 'Austria', 9),
('AZE', 'Azerbaijan', 10),
('BAH', 'Bahamas', 11),
('BAR', 'Barbados', 12),
('BEL', 'Belgium', 13),
('BER', 'Bermuda', 14),
('BHR', 'Bahrain', 15),
('BIH', 'Bosnia Herzegovina', 16),
('BLR', 'Belarus', 17),
('BRA', 'Brazil', 18),
('BUL', 'Bulgaria', 19),
('BUR', 'Burundi', 20),
('CAF', 'Central African Republic', 21),
('CAN', 'Canada', 22),
('CAY', 'Cayman Islands', 23),
('CGO', 'Congo', 24),
('CHI', 'Chile', 25),
('CHN', 'People''s Republic China', 26),
('CIV', 'Ivory Coast', 27),
('CMR', 'Cameroon', 28),
('COL', 'Colombia', 29),
('CRC', 'Costa Rica', 30),
('CRO', 'Croatia', 31),
('CUB', 'Cuba', 32),
('CYP', 'Cyprus', 33),
('DEN', 'Denmark', 34),
('DJI', 'Djibouti', 35),
('DMN', 'Dominica', 36),
('DOM', 'Dominican Republic', 37),
('ECU', 'Ecuador', 38),
('EGY', 'Egypt', 39),
('ENG', 'England', 40),
('ESA', 'El Salvador', 41),
('ESP', 'Spain', 42),
('EST', 'Estonia', 43),
('ETH', 'Ethiopia', 44),
('FIJ', 'Fiji', 45),
('FIN', 'Finland', 46),
('FRA', 'France', 47),
('GAB', 'Gabon', 48),
('GAM', 'The Gambia', 49),
('GBR', 'United Kingdom', 50),
('GEO', 'Georgia', 51),
('GER', 'Germany', 52),
('GHA', 'Ghana', 53),
('GRE', 'Greece', 54),
('GRN', 'Grenada', 55),
('GUA', 'Guatemala', 56),
('GUY', 'Guyana', 57),
('HAI', 'Haiti', 58),
('HKG', 'Hong Kong', 59),
('HON', 'Honduras', 60),
('HUN', 'Hungary', 61),
('INA', 'Indonesia', 62),
('IND', 'India', 63),
('IRL', 'Ireland', 64),
('IRN', 'Iran', 65),
('IRQ', 'Iraq', 66),
('ISL', 'Iceland', 67),
('ISR', 'Israel', 68),
('ISV', 'US Virgin Islands', 69),
('ITA', 'Italy', 70),
('JAM', 'Jamaica', 71),
('JOR', 'Jordan', 72),
('JPN', 'Japan', 73),
('KEN', 'Kenya', 74),
('KGZ', 'Kyrgyzstan', 75),
('KOR', 'Korea', 76),
('KUW', 'Kuwait', 77),
('KZK', 'Kazakhstan', 78),
('LAT', 'Latvia', 79),
('LES', 'Lesotho', 80),
('LIE', 'Liechtenstein', 81),
('LTU', 'Lithuania', 82),
('LUX', 'Luxembourg', 83),
('MAD', 'Madagascar', 84),
('MAR', 'Morocco', 85),
('MAS', 'Malaysia', 86),
('MDA', 'Moldova', 87),
('MEX', 'Mexico', 88),
('MNT', 'Montserrat', 89),
('MON', 'Monaco', 90),
('MOZ', 'Mozambique', 91),
('MRI', 'Mauritius', 92),
('MYA', 'Myanmar', 93),
('NAM', 'Namibia', 94),
('NCA', 'Nicaragua', 95),
('NED', 'Holland', 96),
('NGR', 'Nigeria', 97),
('NGU', 'Papua New Guinea', 98),
('NI\r', 'Northern Ireland', 99),
('NOR', 'Norway', 100),
('NZL', 'New Zealand', 101),
('OMA', 'Oman', 102),
('PAK', 'Pakistan', 103),
('PAN', 'Panama', 104),
('PAR', 'Paraguay', 105),
('PER', 'Peru', 106),
('PHI', 'Philippines', 107),
('POL', 'Poland', 108),
('POR', 'Portugal', 109),
('PRK', 'North Korea', 110),
('PUR', 'Puerto Rico', 111),
('QAT', 'Qatar', 112),
('ROM', 'Romania', 113),
('RSA', 'South Africa', 114),
('RUS', 'Russia', 115),
('RWA', 'Rwanda', 116),
('SAU', 'Saudi Arabia', 117),
('SCO', 'Scotland', 118),
('SEN', 'Sénégal', 119),
('SEY', 'Seychelles', 120),
('SIN', 'Singapore', 121),
('SLE', 'Sierra Leone', 122),
('SLO', 'Slovenia', 123),
('SOM', 'Somalia', 124),
('SRI', 'Sri Lanka', 125),
('STL', 'St Lucia', 126),
('SUD', 'Sudan', 127),
('SUI', 'Schweiz', 0),
('SUR', 'Surinam', 129),
('SVK', 'Slovakia', 130),
('SWE', 'Sweden', 131),
('SYR', 'Syria', 132),
('TAN', 'Tanzania', 133),
('TCH', 'Czech Republic', 134),
('THA', 'Thailand', 135),
('TJK', 'Tadjikistand', 136),
('TKM', 'Turkmenistan', 137),
('TPE', 'Taiwan', 138),
('TRI', 'Trinidad & Tobago', 139),
('TUN', 'Tunisia', 140),
('TUR', 'Turkey', 141),
('UAE', 'United Arab Emirates', 142),
('UGA', 'Uganda', 143),
('UKR', 'Ukraine', 144),
('URU', 'Uruguay', 145),
('USA', 'Amerika', 146),
('UZB', 'Uzbekistan', 147),
('VEN', 'Venezuela', 148),
('WAL', 'Wales', 149),
('YUG', 'Yugoslavia', 150),
('ZAM', 'Zambia', 151),
('ZIM', 'Zimbabwe', 152),
('CZE', 'Tschechien', 33),
('SER', 'Serbien', 119);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'layout'
-- 

DROP TABLE IF EXISTS layout;
CREATE TABLE layout (
  xLayout int(11) NOT NULL auto_increment,
  TypTL int(11) NOT NULL default '0',
  TextTL varchar(255) NOT NULL default '',
  BildTL varchar(255) NOT NULL default '',
  TypTC int(11) NOT NULL default '0',
  TextTC varchar(255) NOT NULL default '',
  BildTC varchar(255) NOT NULL default '',
  TypTR int(11) NOT NULL default '0',
  TextTR varchar(255) NOT NULL default '',
  BildTR varchar(255) NOT NULL default '',
  TypBL int(11) NOT NULL default '0',
  TextBL varchar(255) NOT NULL default '',
  BildBL varchar(255) NOT NULL default '',
  TypBC int(11) NOT NULL default '0',
  TextBC varchar(255) NOT NULL default '',
  BildBC varchar(255) NOT NULL default '',
  TypBR int(11) NOT NULL default '0',
  TextBR varchar(255) NOT NULL default '',
  BildBR varchar(255) NOT NULL default '',
  xMeeting int(11) NOT NULL default '0',
  PRIMARY KEY  (xLayout)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'layout'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'meeting'
-- 

DROP TABLE IF EXISTS meeting;
CREATE TABLE meeting (
  xMeeting int(11) NOT NULL auto_increment,
  Name varchar(60) NOT NULL default '',
  Ort varchar(20) NOT NULL default '',
  DatumVon date NOT NULL default '0000-00-00',
  DatumBis date default NULL,
  Nummer varchar(20) NOT NULL default '',
  ProgrammModus int(1) NOT NULL default '0',
  Online enum('y','n') NOT NULL default 'y',
  Organisator varchar(200) NOT NULL default '',
  Zeitmessung enum('no','omega','alge') NOT NULL default 'no',
  Passwort varchar(50) NOT NULL default '',
  xStadion int(11) NOT NULL default '0',
  xControl int(11) NOT NULL default '0',
  PRIMARY KEY  (xMeeting),
  KEY Name (Name),
  KEY xStadion (xStadion)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'meeting'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'omega_typ'
-- 

DROP TABLE IF EXISTS omega_typ;
CREATE TABLE omega_typ (
  xOMEGA_Typ int(11) NOT NULL default '0',
  OMEGA_Name varchar(15) NOT NULL default '',
  OMEGA_Kurzname varchar(4) NOT NULL default '',
  PRIMARY KEY  (xOMEGA_Typ)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle 'omega_typ'
-- 

INSERT INTO omega_typ (xOMEGA_Typ, OMEGA_Name, OMEGA_Kurzname) VALUES 
(1, '', '0001'),
(2, 'Handstoppung', 'Hnd'),
(3, 'ohne Limite', 'o.Li'),
(4, 'Hürden', 'Hü'),
(5, 'Gehen', 'Geh'),
(6, 'Steeple', 'Stpl');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'region'
-- 

DROP TABLE IF EXISTS region;
CREATE TABLE region (
  xRegion int(11) NOT NULL auto_increment,
  Name varchar(50) NOT NULL default '',
  Anzeige varchar(4) NOT NULL default '',
  Sortierwert int(11) NOT NULL default '0',
  PRIMARY KEY  (xRegion)
) TYPE=MyISAM AUTO_INCREMENT=27 ;

-- 
-- Daten für Tabelle 'region'
-- 

INSERT INTO region (xRegion, Name, Anzeige, Sortierwert) VALUES 
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
-- Tabellenstruktur für Tabelle 'resultat'
-- 

DROP TABLE IF EXISTS resultat;
CREATE TABLE resultat (
  xResultat int(11) NOT NULL auto_increment,
  Leistung int(9) NOT NULL default '0',
  Info char(5) NOT NULL default '-',
  Punkte float NOT NULL default '0',
  xSerienstart int(11) NOT NULL default '0',
  PRIMARY KEY  (xResultat),
  KEY Leistung (Leistung),
  KEY Serienstart (xSerienstart)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'resultat'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'runde'
-- 

DROP TABLE IF EXISTS runde;
CREATE TABLE runde (
  xRunde int(11) NOT NULL auto_increment,
  Datum date NOT NULL default '0000-00-00',
  Startzeit time NOT NULL default '00:00:00',
  Appellzeit time NOT NULL default '00:00:00',
  Stellzeit time NOT NULL default '00:00:00',
  Status int(11) NOT NULL default '0',
  Speakerstatus int(11) NOT NULL default '0',
  StatusZeitmessung tinyint(4) NOT NULL default '0',
  StatusUpload tinyint(4) NOT NULL default '0',
  QualifikationSieger tinyint(4) NOT NULL default '0',
  QualifikationLeistung tinyint(4) NOT NULL default '0',
  Bahnen tinyint(4) NOT NULL default '0',
  Versuche tinyint(4) NOT NULL default '0',
  Gruppe char(2) NOT NULL default '',
  xRundentyp int(11) default NULL,
  xWettkampf int(11) NOT NULL default '0',
  PRIMARY KEY  (xRunde),
  KEY xWettkampf (xWettkampf),
  KEY Zeit (Datum,Startzeit),
  KEY Status (Status)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'runde'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'rundenlog'
-- 

DROP TABLE IF EXISTS rundenlog;
CREATE TABLE rundenlog (
  xRundenlog int(11) NOT NULL auto_increment,
  Zeit datetime NOT NULL default '0000-00-00 00:00:00',
  Ereignis varchar(255) NOT NULL default '',
  xRunde int(11) NOT NULL default '0',
  PRIMARY KEY  (xRundenlog),
  KEY Zeit (Zeit),
  KEY Runde (xRunde)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'rundenlog'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'rundenset'
-- 

DROP TABLE IF EXISTS rundenset;
CREATE TABLE rundenset (
  xRundenset int(11) NOT NULL default '0',
  xMeeting int(11) NOT NULL default '0',
  xRunde int(11) NOT NULL default '0',
  Hauptrunde tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (xRundenset,xMeeting,xRunde)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle 'rundenset'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'rundentyp'
-- 

DROP TABLE IF EXISTS rundentyp;
CREATE TABLE rundentyp (
  xRundentyp int(11) NOT NULL auto_increment,
  Typ char(2) NOT NULL default '',
  Name varchar(20) NOT NULL default '',
  Wertung tinyint(4) default '0',
  Code char(2) NOT NULL default '',
  PRIMARY KEY  (xRundentyp),
  UNIQUE KEY Name (Name),
  UNIQUE KEY Typ (Typ)
) TYPE=MyISAM AUTO_INCREMENT=10 ;

-- 
-- Daten für Tabelle 'rundentyp'
-- 

INSERT INTO rundentyp (xRundentyp, Typ, Name, Wertung, Code) VALUES 
(1, 'V', 'Vorlauf', 0, 'V'),
(2, 'F', 'Final', 0, 'F'),
(3, 'Z', 'Zwischenlauf', 0, 'Z'),
(5, 'Q', 'Qualifikation', 1, 'Q'),
(6, 'S', 'Serie', 0, 'S'),
(7, 'X', 'Halbfinal', 0, 'X'),
(8, 'D', 'Mehrkampf', 1, 'D'),
(9, '0', '(ohne)', 2, '0');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'serie'
-- 

DROP TABLE IF EXISTS serie;
CREATE TABLE serie (
  xSerie int(11) NOT NULL auto_increment,
  Bezeichnung char(2) NOT NULL default '',
  Wind char(5) default '',
  Film int(11) default '0',
  Status int(11) NOT NULL default '0',
  Handgestoppt tinyint(4) NOT NULL default '0',
  xRunde int(11) NOT NULL default '0',
  xAnlage int(11) default NULL,
  PRIMARY KEY  (xSerie),
  UNIQUE KEY Bezeichnung (xRunde,Bezeichnung),
  KEY Runde (xRunde),
  KEY Anlage (xAnlage)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'serie'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'serienstart'
-- 

DROP TABLE IF EXISTS serienstart;
CREATE TABLE serienstart (
  xSerienstart int(11) NOT NULL auto_increment,
  Position int(11) NOT NULL default '0',
  Bahn int(11) NOT NULL default '0',
  Rang int(11) NOT NULL default '0',
  Qualifikation tinyint(4) NOT NULL default '0',
  xSerie int(11) NOT NULL default '0',
  xStart int(11) NOT NULL default '0',
  PRIMARY KEY  (xSerienstart),
  UNIQUE KEY Serienstart (xSerie,xStart),
  KEY Rang (Rang),
  KEY Qualifikation (Qualifikation)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'serienstart'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'stadion'
-- 

DROP TABLE IF EXISTS stadion;
CREATE TABLE stadion (
  xStadion int(11) NOT NULL auto_increment,
  Name varchar(50) NOT NULL default '',
  Bahnen tinyint(4) NOT NULL default '6',
  BahnenGerade tinyint(4) NOT NULL default '8',
  Ueber1000m enum('y','n') NOT NULL default 'n',
  Halle enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (xStadion)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'stadion'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'staffel'
-- 

DROP TABLE IF EXISTS staffel;
CREATE TABLE staffel (
  xStaffel int(11) NOT NULL auto_increment,
  Name varchar(40) NOT NULL default '',
  xVerein int(11) NOT NULL default '0',
  xMeeting int(11) NOT NULL default '0',
  xKategorie int(11) NOT NULL default '0',
  xTeam int(11) NOT NULL default '0',
  Athleticagen enum('y','n') NOT NULL default 'n',
  Startnummer int(11) NOT NULL default '0',
  PRIMARY KEY  (xStaffel),
  KEY xMeeting (xMeeting),
  KEY xVerein (xVerein),
  KEY Name (Name(10)),
  KEY xTeam (xTeam),
  KEY Startnummer (Startnummer)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'staffel'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'staffelathlet'
-- 

DROP TABLE IF EXISTS staffelathlet;
CREATE TABLE staffelathlet (
  xStaffelstart int(11) NOT NULL default '0',
  xAthletenstart int(11) NOT NULL default '0',
  xRunde int(11) NOT NULL default '0',
  Position smallint(1) NOT NULL default '0',
  PRIMARY KEY  (xStaffelstart,xAthletenstart,xRunde),
  UNIQUE KEY Reihenfolge (xStaffelstart,Position,xRunde),
  KEY Position (Position)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle 'staffelathlet'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'start'
-- 

DROP TABLE IF EXISTS start;
CREATE TABLE start (
  xStart int(11) NOT NULL auto_increment,
  Anwesend smallint(1) NOT NULL default '0',
  Bestleistung int(11) NOT NULL default '0',
  Bezahlt enum('y','n') NOT NULL default 'n',
  Erstserie enum('y','n') NOT NULL default 'n',
  xWettkampf int(11) NOT NULL default '0',
  xAnmeldung int(11) NOT NULL default '0',
  xStaffel int(11) NOT NULL default '0',
  PRIMARY KEY  (xStart),
  UNIQUE KEY start (xWettkampf,xAnmeldung,xStaffel),
  KEY Staffel (xStaffel),
  KEY Anmeldung (xAnmeldung),
  KEY Wettkampf (xWettkampf),
  KEY WettkampfAnmeldung (xAnmeldung,xWettkampf),
  KEY WettkampfStaffel (xStaffel,xWettkampf)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'start'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'team'
-- 

DROP TABLE IF EXISTS team;
CREATE TABLE team (
  xTeam int(11) NOT NULL auto_increment,
  Name varchar(30) NOT NULL default '',
  Athleticagen enum('y','n') NOT NULL default 'n',
  xKategorie int(11) NOT NULL default '0',
  xMeeting int(11) NOT NULL default '0',
  xVerein int(11) NOT NULL default '0',
  xKategorie_svm int(11) NOT NULL default '0',
  PRIMARY KEY  (xTeam),
  UNIQUE KEY MeetingKatName (xMeeting,xKategorie,Name),
  KEY Name (Name),
  KEY xKategorie (xKategorie),
  KEY xVerein (xVerein),
  KEY xMeeting (xMeeting),
  KEY xKategorie_svm (xKategorie_svm)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'team'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'teamsm'
-- 

DROP TABLE IF EXISTS teamsm;
CREATE TABLE teamsm (
  xTeamsm int(11) NOT NULL auto_increment,
  Name varchar(100) NOT NULL default '',
  xKategorie int(11) NOT NULL default '0',
  xVerein int(11) NOT NULL default '0',
  xWettkampf int(11) NOT NULL default '0',
  xMeeting int(11) NOT NULL default '0',
  PRIMARY KEY  (xTeamsm)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'teamsm'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'teamsmathlet'
-- 

DROP TABLE IF EXISTS teamsmathlet;
CREATE TABLE teamsmathlet (
  xTeamsm int(11) NOT NULL default '0',
  xAnmeldung int(11) NOT NULL default '0',
  PRIMARY KEY  (xTeamsm,xAnmeldung)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle 'teamsmathlet'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'verein'
-- 

DROP TABLE IF EXISTS verein;
CREATE TABLE verein (
  xVerein int(11) NOT NULL auto_increment,
  Name varchar(30) NOT NULL default '',
  Sortierwert varchar(30) NOT NULL default '0',
  xCode varchar(30) NOT NULL default '',
  Geloescht tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (xVerein),
  UNIQUE KEY Name (Name),
  KEY Sortierwert (Sortierwert),
  KEY xCode (xCode)
) TYPE=MyISAM AUTO_INCREMENT=560 ;

-- 
-- Daten für Tabelle 'verein'
-- 

INSERT INTO verein (xVerein, Name, Sortierwert, xCode, Geloescht) VALUES 
(1, 'Swiss Athletics', 'Swiss Athletics', '1', 0),
(2, 'Aargauischer Leichtathletikver', 'ALV', '1.KLV.AG', 0),
(3, 'STV Wegenstetten', 'Wegenstetten STV', '1.AG.0001', 0),
(4, 'TV Stein (AG)', 'Stein (AG) TV', '1.AG.0002', 0),
(5, 'TV Zofingen LA', 'Zofingen LA TV', '1.AG.0003', 0),
(6, 'SATUS Rothrist LA', 'Rothrist SATUS LA', '1.AG.0004', 0),
(7, 'TV Rothrist', 'Rothrist TV', '1.AG.0005', 0),
(8, 'BTV Aarau LA', 'Aarau BTV', '1.AG.0006', 0),
(9, 'TV Buchs AG', 'Buchs AG TV', '1.AG.0007', 0),
(10, 'LAR SATUS Oberentfelden', 'Oberentfelden SATUS', '1.AG.0008', 0),
(11, 'KTVerb.-LV Fricktal', 'Fricktal KTV-LV', '1.AG.0009', 0),
(12, 'Velo Club Glückauf', 'Glückauf Velo Club', '1.AG.0010', 0),
(13, 'LAR TV Windisch', 'Windisch LAR-TV', '1.AG.0012', 0),
(14, 'Laufsportgruppe Brugg', 'Brugg LSG', '1.AG.0013', 0),
(15, 'Vom Stein Baden', 'Baden vom Stein', '1.AG.0014', 0),
(16, 'Läufergruppe Horn', 'Horn LG', '1.AG.0015', 0),
(17, 'LVWB/SV Tägerig', 'LVWB/SVT', '1.AG.0016', 0),
(18, 'LVWB/LC Aue Baden', 'LVWB/LCA', '1.AG.0017', 0),
(19, 'LVWB/STV Baden', 'LVWB/STVB', '1.AG.0018', 0),
(20, 'LVWB/STV Untersiggenthal', 'LVWB/STVU', '1.AG.0021', 0),
(21, 'LVWB/STV Würenlos', 'LVWB/STVWü', '1.AG.0022', 0),
(22, 'LVWB/SV Lägern-Wettingen', 'LVWB/SVLW', '1.AG.0023', 0),
(23, 'LV Wettingen-Baden', 'Wettingen-Baden LV', '1.LG.0001', 0),
(24, 'Sri Chinmoy Marathon Team', 'Sri Chinmoy Marathon Team', '1.ZH.1864', 0),
(25, 'STV Lenzburg', 'Lenzburg STV', '1.AG.0028', 0),
(26, 'LR Wohlen', 'Wohlen LR', '1.AG.0029', 0),
(27, 'TV Wohlen AG', 'Wohlen (AG) TV', '1.AG.0030', 0),
(28, 'LA Villmergen', 'Villmergen LA', '1.AG.0031', 0),
(29, 'STV Büttikon', 'Büttikon STV', '1.AG.0032', 0),
(30, 'STV Beinwil/Freiamt', 'Beinwil/Freiamt STV', '1.AG.0033', 0),
(31, 'STV Auw', 'Auw LAG', '1.AG.0034', 0),
(32, 'Schweiz. Leichtathletikver. de', 'SLVB', '1.SLV.0005', 0),
(33, 'Schulsport Seengen', 'Seengen Schulsport', '1.AG.0036', 0),
(34, 'SATUS Gränichen', 'Gränichen SATUS', '1.AG.0037', 0),
(35, 'STV Gränichen', 'Gränichen STV', '1.AG.0038', 0),
(36, 'Läufergruppe Homberg', 'Homberg LG', '1.AG.0039', 0),
(37, 'TSV Berikon', 'Berikon TSV', '1.AG.0041', 0),
(38, 'LG Zofingen', 'Zofingen LG', '1.LG.0002', 0),
(39, 'STV Mühlau', 'Mühlau STV', '1.AG.0044', 0),
(40, 'Berner Leichtathletik-Verband', 'BLV', '1.KLV.BE', 0),
(41, 'SATUS Zollikofen', 'Zollikofen SATUS', '1.BE.0101', 0),
(42, 'Sport- und Schützenverein Gals', 'Gals SSV', '1.BE.0102', 0),
(43, 'LAC Biel', 'Biel LAC', '1.BE.0103', 0),
(44, 'LSV Biel', 'Biel LSV', '1.BE.0104', 0),
(45, 'STV Biel', 'Biel STV', '1.BE.0105', 0),
(46, 'FSG Bienne-Romande', 'Bienne-Romande FSG', '1.BE.0106', 0),
(47, 'FSG La Neuveville', 'Neuveville FSG', '1.BE.0107', 0),
(48, 'CA Courtelary', 'Courtelary CA', '1.BE.0109', 0),
(49, 'FSG St-Imier', 'St-Imier FSG', '1.BE.0110', 0),
(50, 'GS Malleray-Bévilard', 'Malleray-Bévilard GS', '1.BE.0111', 0),
(51, 'CA Moutier', 'Moutier CA', '1.BE.0112', 0),
(52, 'FSSS, athlétisme', 'FSSS, athlétisme', '1.BE.0113', 0),
(53, 'SFG "Le Cornet" Crémines', 'Crémines SFG "Le Cornet"', '1.BE.0114', 0),
(54, 'GG Bern', 'Bern GGB', '1.BE.0115', 0),
(55, 'STBern Leichtathletik', 'Bern STB', '1.BE.0116', 0),
(56, 'TV Länggasse', 'Bern TVL', '1.BE.0117', 0),
(57, 'Freunde der Leichtathletik', 'Freunde der Leichtathletik', '1.BE.0118', 0),
(58, 'swiss masters athletics', 'swiss masters athletics', '1.SLV.0002', 0),
(59, 'LG Bern Nord', 'Bern Nord LG', '1.LG.0003', 0),
(60, 'LAG TV Zollikofen', 'Zollikofen TV-LAG', '1.BE.0121', 0),
(61, 'TV Schüpfen', 'Schüpfen TV', '1.BE.0122', 0),
(62, 'TV Bolligen', 'Bolligen TV', '1.BE.0123', 0),
(63, 'TV Ostermundigen', 'Ostermundigen TV', '1.BE.0125', 0),
(64, 'TV Köniz', 'Köniz TV', '1.BE.0126', 0),
(65, 'TV Münsingen', 'Münsingen TV', '1.BE.0127', 0),
(66, 'TV Schwarzenburg', 'Schwarzenburg TV', '1.BE.0128', 0),
(67, 'TV Oberwangen', 'Oberwangen TV', '1.BE.0129', 0),
(68, 'TSV Frauenkappelen', 'Frauenkappelen TSV', '1.BE.0130', 0),
(69, 'TV Erlach', 'Erlach TV', '1.BE.0131', 0),
(70, 'LC Wohlen/BE', 'Wohlen/BE LC', '1.BE.0132', 0),
(71, 'TV Lyss', 'Lyss TV', '1.BE.0133', 0),
(72, 'TV Worben', 'Worben TV', '1.BE.0134', 0),
(73, 'TV Busswil', 'Busswil TV', '1.BE.0135', 0),
(74, 'TV Fraubrunnen', 'Fraubrunnen TV', '1.BE.0136', 0),
(75, 'TV Herzogenbuchsee', 'Herzogenbuchsee TV', '1.BE.0137', 0),
(76, 'TV Rüegsauschachen', 'Rüegsauschachen Tv', '1.BE.0138', 0),
(77, 'TV Oberburg', 'Oberburg TV', '1.BE.0139', 0),
(78, 'LG Rüegsausch.-Lützelflüh', 'Rüegsausch.- Lützelflüh LG', '1.LG.0004', 0),
(79, 'Lauf-und Marschverein Emmental', 'Emmental LMV', '1.BE.0141', 0),
(80, 'LC Kirchberg', 'Kirchberg LC', '1.BE.0142', 0),
(81, 'TV Lützelflüh-Goldbach', 'Lützelflüh-B''ach TV', '1.BE.0143', 0),
(82, 'TV Sumiswald', 'Sumiswald TV', '1.BE.0144', 0),
(83, 'TV Grosshöchstetten', 'Grosshöchstetten TV', '1.BE.0145', 0),
(84, 'TV Konolfingen Athletics', 'Konolfingen TV', '1.BE.0146', 0),
(85, 'SK Langnau', 'Langnau SK', '1.BE.0147', 0),
(86, 'TV Trubschachen', 'Trubschachen TV', '1.BE.0148', 0),
(87, 'LG Berner Oberland', 'Berner Oberland LG', '1.LG.0005', 0),
(88, 'Laufteam Niesen', 'Niesen Laufteam', '1.BE.0150', 0),
(89, 'TV Steffisburg', 'Steffisburg TV', '1.BE.0151', 0),
(90, 'All Blacks Thun', 'Thun All-Blacks', '1.BE.0152', 0),
(91, 'TV Kiesen', 'Kiesen TV', '1.BE.0153', 0),
(92, 'LV Thun', 'Thun LV', '1.BE.0154', 0),
(93, 'LAG TV Boltigen', 'Boltigen  TV-LAG', '1.BE.0155', 0),
(94, 'TV Spiez', 'Spiez TV', '1.BE.0156', 0),
(95, 'LC Scharnachtal', 'Scharnachtal LC', '1.BE.0157', 0),
(96, 'TV Saanen-Gstaad', 'Saanen-Gstaad TV', '1.BE.0158', 0),
(97, 'TV Unterseen', 'Unterseen TV', '1.BE.0159', 0),
(98, 'TV Meiringen', 'Meiringen TV', '1.BE.0160', 0),
(99, 'LV Langenthal', 'Langenthal LV', '1.BE.0161', 0),
(100, 'TV Aeschi', 'Aeschi TV', '1.BE.0162', 0),
(101, 'LV Huttwil', 'Huttwil LV', '1.BE.0163', 0),
(102, 'TV Eriswil', 'Eriswil TV', '1.BE.0164', 0),
(103, 'STV Attiswil', 'Attiswil LA-STV', '1.BE.0166', 0),
(104, 'UOV Burgdorf-LGr', 'Burgdorf UOV', '1.BE.0167', 0),
(105, 'SATUS Biel-Stadt', 'Biel-Stadt SATUS', '1.BE.0168', 0),
(106, 'TV Vinelz', 'Vinelz TV', '1.BE.0169', 0),
(107, 'LAV beider Basel', 'LABB', '1.KLV.BSBL', 0),
(108, 'LC Fortuna Oberbaselbiet', 'Oberbaselbiet LC Fortuna', '1.BSBL.0201', 0),
(109, 'Old-Boys Basel', 'Basel OB', '1.BSBL.0202', 0),
(110, 'LC Basel', 'Basel LC', '1.BSBL.0203', 0),
(111, 'LAR Binningen', 'Binningen LAR', '1.BSBL.0204', 0),
(112, 'TV Bottmingen', 'Bottmingen TV', '1.BSBL.0205', 0),
(113, 'Sportclub Biel-Benken', 'Biel-Benken SC', '1.BSBL.0206', 0),
(114, 'LC Therwil', 'Therwil LC', '1.BSBL.0207', 0),
(115, 'TV Riehen', 'Riehen TV', '1.BSBL.0209', 0),
(116, 'LV Birsfelden', 'Birsfelden LV', '1.BSBL.0210', 0),
(117, 'TV Muttenz athletics', 'Muttenz TV', '1.BSBL.0211', 0),
(118, 'TV Pratteln AS', 'Pratteln AS TV', '1.BSBL.0212', 0),
(119, 'TV Arlesheim', 'Arlesheim TV', '1.BSBL.0213', 0),
(120, 'TV Zwingen', 'Zwingen TV', '1.BSBL.0214', 0),
(121, 'SC Liestal', 'Liestal SC', '1.BSBL.0216', 0),
(122, 'TV Bubendorf', 'Bubendorf TV', '1.BSBL.0217', 0),
(123, 'LV Frenke', 'Frenke LV', '1.BSBL.0218', 0),
(124, 'TV Läufelfingen', 'Läufelfingen TV', '1.BSBL.0219', 0),
(125, 'TV Sissach', 'Sissach TV', '1.BSBL.0220', 0),
(126, 'TV Zunzgen', 'Zunzgen TV', '1.BSBL.0221', 0),
(127, 'TV Ormalingen', 'Ormalingen TV', '1.BSBL.0224', 0),
(128, 'TV Aesch', 'Aesch TV', '1.BSBL.0225', 0),
(129, 'TSV Anwil', 'Anwil TSV', '1.BSBL.0226', 0),
(130, 'TV Zeglingen', 'Zeglingen TV', '1.BSBL.0227', 0),
(131, 'TV Rothenfluh', 'Rothenfluh TV', '1.BSBL.0228', 0),
(132, 'LG Oberbaselbiet', 'Oberbaselbiet LG', '1.LG.0006', 0),
(133, 'TV Rickenbach', 'Rickenbach TV', '1.BSBL.0231', 0),
(134, 'TV Thürnen', 'Thürnen TV', '1.BSBL.0232', 0),
(135, 'TV Wintersingen', 'Wintersingen TV', '1.BSBL.0233', 0),
(136, 'TV Gelterkinden', 'Gelterkinden TV', '1.BSBL.0235', 0),
(137, 'DTV Thürnen', 'Thürnen DTV', '1.BSBL.0236', 0),
(138, 'Fédération Fribourgeoise d`Ath', 'FFA', '1.KLV.FR', 0),
(139, 'FSG Estavayer-Lully', 'Estavayer-Lully FSG', '1.FR.0301', 0),
(140, 'UA Châtel-St-Denis', 'Châtel-St-Denis UA', '1.FR.0302', 0),
(141, 'SA Bulle', 'Bulle SA', '1.FR.0303', 0),
(142, 'CS Marsens', 'Marsens CS', '1.FR.0304', 0),
(143, 'FSG Broc', 'Broc FSG', '1.FR.0305', 0),
(144, 'CS Neirivue', 'Neirivue CS', '1.FR.0306', 0),
(145, 'CA Romont-Condémina', 'Romont-Condémina CA', '1.FR.0307', 0),
(146, 'CA Fribourg', 'Fribourg CA', '1.FR.0308', 0),
(147, 'LAT Sense (Lauf& Athletikteam)', 'Sense LAT', '1.FR.0309', 0),
(148, 'TSV Rechthalten', 'Rechthalten TSV', '1.FR.0310', 0),
(149, 'TSV Heitenried', 'Heitenried TSV', '1.FR.0311', 0),
(150, 'TV Alterswil', 'Alterswil TV', '1.FR.0312', 0),
(151, 'CA Marly', 'Marly CA', '1.FR.0313', 0),
(152, 'CS Le Mouret', 'Mouret, Le CS', '1.FR.0314', 0),
(153, 'CA Gibloux Farvagny', 'Farvagny CA Gibloux', '1.FR.0315', 0),
(154, 'CA Belfaux', 'Belfaux CA', '1.FR.0316', 0),
(155, 'CA Rosé', 'Rosé CA', '1.FR.0317', 0),
(156, 'CM Fribourg', 'Fribourg CM', '1.FR.0318', 0),
(157, 'TV Bösingen', 'Bösingen TV', '1.FR.0319', 0),
(158, 'TV Wünnewil', 'Wünnewil TV', '1.FR.0320', 0),
(159, 'TSV Düdingen', 'Düdingen TSV', '1.FR.0321', 0),
(160, 'TSV Gurmels', 'Gurmels TSV', '1.FR.0322', 0),
(161, 'AC Murten', 'Murten AC', '1.FR.0323', 0),
(162, 'TV Murten', 'Murten TV', '1.FR.0324', 0),
(163, 'TSV Plaffeien', 'Plaffeien TSV', '1.FR.0325', 0),
(164, 'COA Sarine', 'Sarine COA', '1.LG.0009', 0),
(165, 'LG Sense', 'Sense LG', '1.LG.0010', 0),
(166, 'COA La Gruyère', 'Gruyère, La COA', '1.LG.0011', 0),
(167, 'TSV Kerzers', 'Kerzers TSV', '1.FR.0329', 0),
(168, 'Association Genevoise d`Athlét', 'AGA', '1.KLV.GE', 0),
(169, 'AVG-Athlétisme Viseu-Genève', 'Genève Athlétisme Viseu', '1.GE.0401', 0),
(170, 'CA Genève', 'Genève CA', '1.GE.0402', 0),
(171, 'C.H. de Plainpalais', 'Plainpalais C.H.', '1.GE.0403', 0),
(172, 'Felege Guihon International', 'Felege Guihon International', '1.GE.0404', 0),
(173, 'Stade Genève', 'Genève Stade', '1.GE.0405', 0),
(174, 'UGS-Athlétisme', 'UGS-Athlétisme', '1.GE.0406', 0),
(175, 'CGA Onex', 'Onex CGA', '1.GE.0407', 0),
(176, 'FSG Meyrin', 'Meyrin FSG', '1.GE.0408', 0),
(177, 'Foulées Athlé. Saconnesiennes', 'Saconnesiennes FA', '1.GE.0409', 0),
(178, 'FSG Grand-Saconnex', 'Grand-Saconnex FSG', '1.GE.0410', 0),
(179, 'C.H. Châtelaine', 'Châtelaine C.H.', '1.GE.0411', 0),
(180, 'SATUS Athl. Genève', 'Genève SATUS Athl.', '1.GE.0412', 0),
(181, 'FSG Bernex-Confignon', 'Bernex-Confignon FSG', '1.GE.0414', 0),
(182, 'FSG Jussy', 'Jussy FSG', '1.GE.0415', 0),
(183, 'FSG Versoix', 'Versoix FSG', '1.GE.0416', 0),
(184, 'COA Petit-Léman', 'Petit-Léman COA', '1.LG.0012', 0),
(185, 'Glarner LAV', 'Glarner LAV', '1.KLV.GL', 0),
(186, 'LAV Glarus', 'Glarus LAV', '1.GL.0501', 0),
(187, 'KLV Graubünden', 'Graubünden KLV', '1.KLV.GR', 0),
(188, 'TV Rhäzüns', 'Rhäzüns TV', '1.GR.0601', 0),
(189, 'BTV Chur', 'Chur BTV', '1.GR.0602', 0),
(190, 'TV Tamins', 'Tamins TV', '1.GR.0603', 0),
(191, 'TV Zizers', 'Zizers TV', '1.GR.0604', 0),
(192, 'TV Seewis', 'Seewis TV', '1.GR.0605', 0),
(193, 'Track Club Davos', 'Davos Track-Club', '1.GR.0606', 0),
(194, 'AJ Landquart', 'Landquart AJ', '1.GR.0607', 0),
(195, 'Laufteam St. Moritz', 'St. Moritz Laufteam', '1.GR.0608', 0),
(196, 'LA Innerschweiz', 'LAIS', '1.KLV.LAIS', 0),
(197, 'LC Luzern', 'Luzern LC', '1.LAIS.0701', 0),
(198, 'STV Kriens', 'Kriens STV', '1.LAIS.0702', 0),
(199, 'TV Reussbühl LA', 'Reussbühl LA TV', '1.LAIS.0703', 0),
(200, 'STV-Jugend Ruswil', 'Ruswil STV-Jugend', '1.LAIS.0704', 0),
(201, 'LC Emmenstrand', 'Emmenstrand LC', '1.LAIS.0705', 0),
(202, 'KTV Grosswangen', 'Grosswangen KTV', '1.LAIS.0706', 0),
(203, 'TSV Rothenburg', 'Rothenburg TSV', '1.LAIS.0707', 0),
(204, 'TV Inwil', 'Inwil TV', '1.LAIS.0708', 0),
(205, 'LR Ebikon', 'Ebikon LR', '1.LAIS.0709', 0),
(206, 'LV Horw', 'Horw LV', '1.LAIS.0710', 0),
(207, 'STV Alpnach, LAGr', 'Alpnach STV LAGr', '1.LAIS.0712', 0),
(208, 'TV Sarnen LA', 'Sarnen TV', '1.LAIS.0713', 0),
(209, 'STV Malters', 'Malters STV', '1.LAIS.0714', 0),
(210, 'TV Wolhusen', 'Wolhusen TV', '1.LAIS.0715', 0),
(211, 'STV Willisau', 'Willisau STV', '1.LAIS.0716', 0),
(212, 'LR Gettnau', 'Gettnau LR', '1.LAIS.0717', 0),
(213, 'STV Altbüron', 'Altbüron STV', '1.LAIS.0718', 0),
(214, 'LAR TV Schüpfheim', 'Schüpfheim LAR-TV', '1.LAIS.0719', 0),
(215, 'KTV Neuenkirch LR', 'Neuenkirch KTV', '1.LAIS.0720', 0),
(216, 'TSV Oberkirch', 'Oberkirch TSV', '1.LAIS.0721', 0),
(217, 'TV Sursee', 'Sursee TV', '1.LAIS.0722', 0),
(218, 'STV Ettiswil', 'Ettiswil STV', '1.LAIS.0723', 0),
(219, 'STV Beromünster', 'Beromünster STV', '1.LAIS.0724', 0),
(220, 'STV Triengen', 'Triengen STV', '1.LAIS.0725', 0),
(221, 'TV Uffikon', 'Uffikon TV', '1.LAIS.0727', 0),
(222, 'STV-LA Roggliswil', 'Roggliswil STV-LA', '1.LAIS.0728', 0),
(223, 'STV Ballwil', 'Ballwil STV', '1.LAIS.0730', 0),
(224, 'AUDACIA Hochdorf', 'Hochdorf AUDACIA', '1.LAIS.0731', 0),
(225, 'LAR STV Hitzkirch', 'Hitzkirch STV', '1.LAIS.0732', 0),
(226, 'LK Zug', 'Zug LK', '1.LAIS.0733', 0),
(227, 'Hochwacht Zug', 'Zug Hochwacht', '1.LAIS.0734', 0),
(228, 'STV Allenwinden Jugendriege', 'Allenwinden STV JR', '1.LAIS.0735', 0),
(229, 'TV Cham 1884', 'Cham 1884 TV', '1.LAIS.0737', 0),
(230, 'STV Oberägeri', 'Oberägeri STV', '1.LAIS.0738', 0),
(231, 'TSV 2001 Rotkreuz', 'Rotkreuz TSV-2001', '1.LAIS.0740', 0),
(232, 'LA Nidwalden', 'Nidwalden LA', '1.LAIS.0741', 0),
(233, 'LC Altdorf', 'Altdorf LC', '1.LAIS.0743', 0),
(234, 'LA TV Erstfeld', 'Erstfeld LA-TV', '1.LAIS.0745', 0),
(235, 'LG Obwalden', 'Obwalden LG', '1.LG.0016', 0),
(236, 'LG Uri', 'Uri LG', '1.LG.0017', 0),
(237, 'LG Luzern Nord', 'Luzern Nord LG', '1.LG.0018', 0),
(238, 'STV Nebikon', 'Nebikon STV', '1.LAIS.0750', 0),
(239, 'LG Pilatus', 'Pilatus LG', '1.LG.0019', 0),
(240, 'Leichtathletik Kerns', 'Kerns LA', '1.LAIS.0753', 0),
(241, 'Association Jurassienne d`Athl', 'AJA', '1.KLV.JU', 0),
(242, 'GS Franches-Montagnes', 'Franches-Montagnes GS', '1.JU.0801', 0),
(243, 'FSG Les Breuleux', 'Breuleux FSG', '1.JU.0802', 0),
(244, 'FSG Saignelégier', 'Saignelégier FSG', '1.JU.0803', 0),
(245, 'La Neuveville Sport', 'Neuveville Sport', '1.JU.0804', 0),
(246, 'FSG Reconvilier', 'Reconvilier FSG', '1.JU.0805', 0),
(247, 'GAP Prévôtois', 'Prévôtois GAP', '1.JU.0806', 0),
(248, 'CA Delémont', 'Delémont CA', '1.JU.0807', 0),
(249, 'FSG Delémont', 'Delémont FSG', '1.JU.0808', 0),
(250, 'FSG Courroux', 'Courroux FSG', '1.JU.0809', 0),
(251, 'Femina Vicques', 'Vicques Femina', '1.JU.0810', 0),
(252, 'FSG Vicques', 'Vicques FSG', '1.JU.0811', 0),
(253, 'FSG Châtillon', 'Châtillon FSG', '1.JU.0812', 0),
(254, 'FSG Courtételle', 'Courtételle FSG', '1.JU.0813', 0),
(255, 'FSG Bassecourt', 'Bassecourt FSG', '1.JU.0814', 0),
(256, 'GS Tabeillon', 'Tabeillon GS', '1.JU.0815', 0),
(257, 'Montfaucon Gym Sport', 'Montfaucon FS', '1.JU.0816', 0),
(258, 'FSG Alle', 'Alle FSG', '1.JU.0817', 0),
(259, 'FSG Avenir-Porrentruy', 'Avenir-Porrentruy FSG', '1.JU.0818', 0),
(260, 'GS Ajoie', 'Ajoie GS', '1.JU.0819', 0),
(261, 'CA Fontenais', 'Fontenais CA', '1.JU.0820', 0),
(262, 'Femina Sport Boncourt', 'Boncourt Femina Sport', '1.JU.0821', 0),
(263, 'FSG Courgenay', 'Courgenay FSG', '1.JU.0822', 0),
(264, 'COA Ajoie', 'Ajoie COA', '1.LG.0013', 0),
(265, 'COA Delémont', 'Delémont COA', '1.LG.0014', 0),
(266, 'FSG Les Bois', 'Bois FSG', '1.JU.0825', 0),
(267, 'CA des Franches-Montagnes', 'CA des Franches-Montagnes', '1.JU.0830', 0),
(268, 'COA Franches-Montagnes', 'Franches-Montagnes COA', '1.LG.0015', 0),
(269, 'Fémina Sport Saignelégier', 'Saignelégier Fémina Sport', '1.JU.0828', 0),
(270, 'FSG Malleray-Bévilard', 'Malleray-Bévilard FSG', '1.JU.0829', 0),
(271, 'Association neuchâteloise d`at', 'ANA', '1.KLV.NE', 0),
(272, 'CEP Cortaillod', 'Cortaillod CEP', '1.NE.0901', 0),
(273, 'FSG Bevaix', 'Bevaix FSG', '1.NE.0902', 0),
(274, 'FSG Corcelles-Cormondrèche', 'Corcelles FSG', '1.NE.0903', 0),
(275, 'FSG Couvet', 'Couvet FSG', '1.NE.0904', 0),
(276, 'FSG Môtiers', 'Môtiers FSG', '1.NE.0905', 0),
(277, 'FSG Geneveys & Coffrane', 'Geneveys&Coffrane FSG', '1.NE.0906', 0),
(278, 'SEP Olympic La Chaux-de-Fonds', 'SEP Olympic ChdF', '1.NE.0907', 0),
(279, 'FSG Le Locle', 'Locle, Le FSG', '1.NE.0908', 0),
(280, 'GA Neuchâtelois', 'Neuchâtelois GA', '1.LG.0020', 0),
(281, 'Cressier-Chaumont', 'Cressier-Chaumont', '1.NE.0910', 0),
(282, 'TV Bad Ragaz', 'Bad Ragaz TV', '1.SGALV.1001', 0),
(283, 'SC Diemberg', 'Diemberg SC', '1.SGALV.1002', 0),
(284, 'LAG Gossau', 'Gossau LAG', '1.SGALV.1003', 0),
(285, 'LC Rapperswil-Jona', 'Rapperswil-Jona LC', '1.SGALV.1004', 0),
(286, 'STV Altstätten', 'Altstätten STV', '1.SGALV.1005', 0),
(287, 'STV Eschenbach SG', 'Eschenbach SG STV', '1.SGALV.1006', 0),
(288, 'Läuferriege Walenstadt', 'Walenstadt LR', '1.SGALV.1007', 0),
(289, 'TV Mels', 'Mels TV', '1.SGALV.1008', 0),
(290, 'LA Speicher', 'Speicher LA', '1.SGALV.1009', 0),
(291, 'TV Appenzell', 'Appenzell TV', '1.SGALV.1010', 0),
(292, 'TV Teufen', 'Teufen TV', '1.SGALV.1011', 0),
(293, 'TV Stein (AR)', 'Stein (AR) TV', '1.SGALV.1012', 0),
(294, 'TV Herisau', 'Herisau TV', '1.SGALV.1013', 0),
(295, 'JR TV Flawil', 'Flawil TV Jugi', '1.SGALV.1014', 0),
(296, 'STV Lütisburg', 'Lütisburg STV', '1.SGALV.1015', 0),
(297, 'LC Uzwil', 'Uzwil LC', '1.SGALV.1016', 0),
(298, 'TV St. Gallen-Ost', 'St.Gallen-Ost TV', '1.SGALV.1017', 0),
(299, 'LG Bodensee', 'Bodensee LGB', '1.SGALV.1018', 0),
(300, 'TV Thal', 'Thal TV', '1.SGALV.1019', 0),
(301, 'STV Au SG', 'Au SG  STV', '1.SGALV.1020', 0),
(302, 'STV Widnau', 'Widnau STV', '1.SGALV.1021', 0),
(303, 'TV Rebstein', 'Rebstein TV', '1.SGALV.1022', 0),
(304, 'Athleticteam KTV Altstätten', 'Athleticteam KTV Altstätten', '1.SGALV.1023', 0),
(305, 'STV Balgach', 'Balgach STV', '1.SGALV.1024', 0),
(306, 'KTV Oberriet', 'Oberriet KTV', '1.SGALV.1025', 0),
(307, 'STV Oberriet-Eichenwies', 'Oberriet-Eichenwies STV', '1.SGALV.1026', 0),
(308, 'STV Rüthi', 'Rüthi STV', '1.SGALV.1027', 0),
(309, 'TV Buchs SG', 'Buchs SG TV', '1.SGALV.1028', 0),
(310, 'STV Gams', 'Gams STV', '1.SGALV.1029', 0),
(311, 'Ski- Bergclub Gauschla', 'Gauschla SBC', '1.SGALV.1030', 0),
(312, 'LC Vaduz', 'Vaduz LC', '1.SGALV.1031', 0),
(313, 'TV Eschen-Mauren', 'Eschen-Mauren TV', '1.SGALV.1032', 0),
(314, 'LC Schaan', 'Schaan LC', '1.SGALV.1033', 0),
(315, 'TV Schaan / Leichtathletik', 'Schaan TV', '1.SGALV.1034', 0),
(316, 'TV Triesen', 'Triesen TV', '1.SGALV.1035', 0),
(317, 'KTV Wil LA', 'Wil KTV', '1.SGALV.1036', 0),
(318, 'STV Schwarzenbach', 'Schwarzenbach STV', '1.SGALV.1039', 0),
(319, 'KTV Bütschwil', 'Bütschwil KTV', '1.SGALV.1040', 0),
(320, 'LC Brühl', 'St.Gallen LC Brühl', '1.SGALV.1041', 0),
(321, 'LG Fürstenland', 'Fürstenland LG', '1.LG.0021', 0),
(322, 'LG Liechtenstein', 'Liechtenstein LG', '1.LG.0022', 0),
(323, 'LG Obersee', 'Obersee LG', '1.LG.0023', 0),
(324, 'TV Sevelen', 'Sevelen TV', '1.SGALV.1046', 0),
(325, 'LGB Benken', 'Benken LGB', '1.SGALV.1047', 0),
(326, 'LG Rheintal', 'Rheintal LG', '1.LG.0024', 0),
(327, 'TV St. Peterzell', 'St. Peterzell TV', '1.SGALV.1050', 0),
(328, 'Schaffhauser KLV', 'KLVS', '1.KLV.SH', 0),
(329, 'LC Schaffhausen', 'Schaffhausen LC', '1.SH.1101', 0),
(330, 'TV Löhningen', 'Löhningen TV', '1.SH.1102', 0),
(331, 'TV Dörflingen', 'Dörflingen TV', '1.SH.1103', 0),
(332, 'OK Staaner Stadtlauf', 'OK Staaner Stadtlauf', '1.SH.1104', 0),
(333, 'TV Stein am Rhein', 'Stein am Rhein TV', '1.SH.1105', 0),
(334, 'TV Buchberg/Rüdlingen', 'Buchberg/Rüdlingen TV', '1.SH.1106', 0),
(335, 'Turne Schlaate', 'Schlaate Turne', '1.SH.1108', 0),
(336, 'KLV Solothurn', 'Solothurn KLV', '1.KLV.SO', 0),
(337, 'TV Grenchen', 'Grenchen TV', '1.SO.1201', 0),
(338, 'Biberist aktiv! LA', 'Biberist aktiv!', '1.SO.1202', 0),
(339, 'STV Bettlach', 'Bettlach STV', '1.SO.1203', 0),
(340, 'STV Selzach', 'Selzach STV', '1.SO.1204', 0),
(341, 'LAZ Thierstein', 'Thierstein LAZ', '1.SO.1205', 0),
(342, 'STV Riedholz', 'Riedholz TV', '1.SO.1206', 0),
(343, 'TV Luterbach', 'Luterbach TV', '1.SO.1207', 0),
(344, 'TV Biezwil', 'Biezwil TV', '1.SO.1208', 0),
(345, 'TV Olten', 'Olten TV', '1.SO.1209', 0),
(346, 'STV Gunzgen', 'Gunzgen STV', '1.SO.1210', 0),
(347, 'LA TV Wolfwil', 'Wolfwil LA-TV', '1.SO.1212', 0),
(348, 'LZ Lostorf', 'Lostorf LZ', '1.SO.1213', 0),
(349, 'TV Däniken', 'Däniken TV', '1.SO.1214', 0),
(350, 'TSV Kestenholz', 'Kestenholz TSV', '1.SO.1215', 0),
(351, 'TV Balsthal', 'Balsthal TV', '1.SO.1216', 0),
(352, 'STV Welschenrohr', 'Welschenrohr STV', '1.SO.1217', 0),
(353, 'TV Gretzenbach', 'Gretzenbach TV', '1.SO.1218', 0),
(354, 'TV Dulliken', 'Dulliken TV', '1.SO.1219', 0),
(355, 'LG Solothurn-West', 'Solothurn-West LG', '1.LG.0025', 0),
(356, 'LVS Schwyz', 'Schwyz LVS', '1.KLV.SZ', 0),
(357, 'KTV Muotathal', 'Muotathal KTV', '1.SZ.1301', 0),
(358, 'STV Küssnacht', 'Küssnacht STV', '1.SZ.1302', 0),
(359, 'TSV Steinen', 'Steinen TSV', '1.SZ.1303', 0),
(360, 'TV Ibach', 'Ibach TV', '1.SZ.1305', 0),
(361, 'TV Brunnen', 'Brunnen TV', '1.SZ.1306', 0),
(362, 'STV Gersau', 'Gersau STV', '1.SZ.1307', 0),
(363, 'KTV Freienbach', 'Freienbach KTV', '1.SZ.1308', 0),
(364, 'STV Pfäffikon-Freienbach', 'Pfäffikon-Freienbach STV', '1.SZ.1309', 0),
(365, 'STV Wollerau-Bäch', 'Wollerau-Bäch STV', '1.SZ.1310', 0),
(366, 'ETV Schindellegi', 'Schindellegi ETV', '1.SZ.1311', 0),
(367, 'STV Einsiedeln', 'Einsiedeln STV', '1.SZ.1312', 0),
(368, 'KTV Altendorf', 'Altendorf KTV', '1.SZ.1313', 0),
(369, 'STV Siebnen', 'Siebnen STV', '1.SZ.1314', 0),
(370, 'TSV Galgenen', 'Galgenen TSV', '1.SZ.1315', 0),
(371, 'STV Wangen SZ', 'Wangen SZ STV', '1.SZ.1316', 0),
(372, 'STV Lachen', 'Lachen STV', '1.SZ.1317', 0),
(373, 'STV Tuggen', 'Tuggen STV', '1.SZ.1318', 0),
(374, 'STV Wägital', 'Wägital STV', '1.SZ.1319', 0),
(375, 'TV Buttikon-Schübelbach', 'Buttikon-Sch''bach TV', '1.SZ.1321', 0),
(376, 'STV Reichenburg', 'Reichenburg STV', '1.SZ.1322', 0),
(377, 'LG Innerschwyz', 'Innerschwyz LG', '1.LG.0026', 0),
(378, 'Thurgauer Leichtathletik-Verba', 'TLAV', '1.KLV.TG', 0),
(379, 'LAR Tägerwilen', 'Tägerwilen LAR', '1.TG.1401', 0),
(380, 'TV Aadorf', 'Aadorf TV', '1.TG.1403', 0),
(381, 'TSV Guntershausen', 'Guntershausen TSV', '1.TG.1404', 0),
(382, 'KTV Frauenfeld', 'Frauenfeld KTV', '1.TG.1405', 0),
(383, 'LA Gachnang-Islikon', 'Gachnang-Islikon LA', '1.TG.1406', 0),
(384, 'TV Gachnang-Islikon', 'Gachnang-Islikon TV', '1.TG.1407', 0),
(385, 'LAR TV Weinfelden', 'Weinfelden LAR-TV', '1.TG.1409', 0),
(386, 'STV Illhart-Sonterswil', 'Illhart-Sonterswil STV', '1.TG.1410', 0),
(387, 'TV Amriswil', 'Amriswil TV', '1.TG.1411', 0),
(388, 'STV Berg', 'Berg STV', '1.TG.1412', 0),
(389, 'LG erdgas Oberthurgau', 'Oberthurgau LG', '1.LG.0027', 0),
(390, 'TV Zihlschlacht', 'Zihlschlacht TV', '1.TG.1414', 0),
(391, 'LAR Bischofszell', 'Bischofszell LAR', '1.TG.1415', 0),
(392, 'STV Salmsach', 'Salmsach STV', '1.TG.1416', 0),
(393, 'STV Güttingen', 'Güttingen STV', '1.TG.1417', 0),
(394, 'LC Bottighofen', 'Bottighofen LC', '1.TG.1418', 0),
(395, 'STV Neukirch-Egnach', 'Neukirch-Egnach STV', '1.TG.1419', 0),
(396, 'LC Frauenfeld', 'Frauenfeld LC', '1.TG.1420', 0),
(397, 'LAR Matzingen', 'Matzingen LAR', '1.TG.1421', 0),
(398, 'STV Neuwilen', 'Neuwilen STV', '1.TG.1422', 0),
(399, 'Federazione Ticinese di Atleti', 'FTAL', '1.KLV.TI', 0),
(400, 'GAB Bellinzona', 'Bellinzona GAB', '1.TI.1501', 0),
(401, 'SA Bellinzona', 'Bellinzona SA', '1.TI.1502', 0),
(402, 'Atletica Tenero 90', 'Tenero 90 Atletica', '1.TI.1503', 0),
(403, 'Comunità Atletica 97', 'Comunità Atletica 97', '1.LG.0028', 0),
(404, 'Vis Nova Agarone', 'Agarone Vis-Nova', '1.TI.1506', 0),
(405, 'SAG Gordola', 'Gordola SAG', '1.TI.1507', 0),
(406, 'VIRTUS Locarno', 'Locarno VIRTUS', '1.TI.1508', 0),
(407, 'US Ascona', 'Ascona US', '1.TI.1509', 0),
(408, 'SFG Brissago', 'Brissago FSG', '1.TI.1510', 0),
(409, 'SFG Biasca', 'Biasca SFG', '1.TI.1511', 0),
(410, 'SFG Airolo Sezione Atletica', 'Airolo SA SFG', '1.TI.1512', 0),
(411, 'SAR Rivera', 'Rivera SAR', '1.TI.1513', 0),
(412, 'ASSPO Riva San Vitale', 'Riva S. Vitale', '1.TI.1514', 0),
(413, 'Atletica Mendrisiotto', 'Mendrisiotto Atletica', '1.TI.1515', 0),
(414, 'SFG Chiasso', 'Chiasso SFG', '1.TI.1516', 0),
(415, 'SAV Vacallo', 'Vacallo SAV', '1.TI.1517', 0),
(416, 'SFG Morbio Inferiore', 'Morbio Inferiore SFG', '1.TI.1518', 0),
(417, 'Società Sportiva Valle di Mugg', 'Società Sportiva Valle di Mugg', '1.TI.1519', 0),
(418, 'SFG Mendrisio', 'Mendrisio SFG', '1.TI.1520', 0),
(419, 'VIGOR Ligornetto', 'Ligornetto VIGOR', '1.TI.1521', 0),
(420, 'SFG Stabio', 'Stabio SFG', '1.TI.1522', 0),
(421, 'SA Massagno', 'Massagno SA', '1.TI.1524', 0),
(422, 'SAL Lugano', 'Lugano SA', '1.TI.1526', 0),
(423, 'Fédération Suisse de Marche', 'Fédération Suisse de Marche', '1.SLV.0004', 0),
(424, 'USC Capriaschese-Atletica', 'Capriachese USC', '1.TI.1529', 0),
(425, 'SAL Lugano  sezione Marcia', 'Lugano Marcia SA', '1.TI.1530', 0),
(426, 'GAD Dongio', 'Dongio GAD', '1.TI.1532', 0),
(427, 'Senioren Laufverein Schweiz', 'SLVS', '1.SLV.0001', 0),
(428, 'Supportervereinigung', 'Supportervereinigung', '1.SLV.0003', 0),
(429, 'Association Cantonale Vaudoise', 'ACVA', '1.KLV.VD', 0),
(430, 'Stade Lausanne', 'Lausanne Stade', '1.VD.1601', 0),
(431, 'Lausanne-Sports', 'Lausanne-Sports', '1.VD.1602', 0),
(432, 'CM Cour Lausanne', 'Lausanne CM-Cour', '1.VD.1603', 0),
(433, 'FSG Renens', 'Renens FSG', '1.VD.1604', 0),
(434, 'FSG St-Cierges', 'St-Cierges FSG', '1.VD.1605', 0),
(435, 'FSG Epalinges', 'Epalinges FSG', '1.VD.1606', 0),
(436, 'Footing-Club Lausanne', 'Lausanne FC', '1.VD.1607', 0),
(437, 'FSG Mézières', 'Mézières FSG', '1.VD.1608', 0),
(438, 'FSG Tolochenaz', 'Tolochenaz FSG', '1.VD.1609', 0),
(439, 'Lausanne-Marathon', 'Lausanne-Marathon', '1.VD.1610', 0),
(440, 'COVA Nyon', 'Nyon COVA', '1.VD.1611', 0),
(441, 'US Yverdon', 'Yverdon US', '1.VD.1612', 0),
(442, 'CM Athlétique Yverdon CMA Yver', 'Yverdon CMA', '1.VD.1613', 0),
(443, 'CA Broyard', 'Broyard CA', '1.VD.1614', 0),
(444, 'FSG Avenches', 'Avenches FSG', '1.VD.1615', 0),
(445, 'CARE Vevey', 'Vevey CARE', '1.VD.1616', 0),
(446, 'FSG Chailly-Montreux', 'Chailly-Montreux F.S.G.', '1.VD.1617', 0),
(447, 'CA Aiglon', 'Aiglon CA', '1.VD.1618', 0),
(448, 'COA Jorat', 'Jorat COA', '1.LG.0029', 0),
(449, 'COA Lausanne-Riviera', 'Lausanne-Riviera COA', '1.LG.0030', 0),
(450, 'COA Broye-Nord Vaudois', 'Broye-Nord Vaudois COA', '1.LG.0031', 0),
(451, 'FSG Morges', 'Morges FSG', '1.VD.1622', 0),
(452, 'CM Ecureuils TdP', 'Tour-de-Peilz CM', '1.VD.1623', 0),
(453, 'FSG Oron JP', 'Oron "JP" FSG', '1.VD.1624', 0),
(454, 'Ecole nouvelle de la Suisse Ro', 'ENSR', '1.VD.1625', 0),
(455, 'Fédération Valaisanne d`Athlét', 'FVA', '1.KLV.VS', 0),
(456, 'Club de Marche Monthey', 'Monthey CM', '1.VS.1701', 0),
(457, 'SG St-Maurice', 'St-Maurice SG', '1.VS.1702', 0),
(458, 'FSG Collombey-Muraz', 'Collombey-Muraz SFG', '1.VS.1703', 0),
(459, 'CA Vouvry', 'Vouvry CA', '1.VS.1704', 0),
(460, 'RC des Deux Rives', 'Deux Rives RC', '1.VS.1705', 0),
(461, 'CABV Martigny', 'Martigny CABV', '1.VS.1706', 0),
(462, 'Amis-Gym Fully', 'Fully FSG', '1.VS.1707', 0),
(463, 'CS 13 Etoiles', 'Sion 13 Etoiles', '1.VS.1708', 0),
(464, 'SFG Conthey', 'Conthey SFG', '1.VS.1709', 0),
(465, 'CA Sion', 'Sion CA', '1.VS.1710', 0),
(466, 'SFG Ardon La Lizernoise', 'Ardon Lizernoise FSG', '1.VS.1711', 0),
(467, 'Uvrier - Sports', 'Uvrier - Sports', '1.VS.1712', 0),
(468, 'CA Vétroz', 'Vétroz CA', '1.VS.1713', 0),
(469, 'ES Ayent Anzère', 'Ayent Anzère ES', '1.VS.1714', 0),
(470, 'LF-Team Oberwallis', 'Oberwallis LF-Team', '1.VS.1716', 0),
(471, 'TV Naters', 'Naters TV', '1.VS.1717', 0),
(472, 'LC Matterhorn', 'Matterhorn LC', '1.VS.1718', 0),
(473, 'LV Visp', 'Visp LV', '1.VS.1719', 0),
(474, 'STV Gampel', 'Gampel STV', '1.VS.1720', 0),
(475, 'CA Sierre DSG', 'Sierre DSG CA', '1.VS.1721', 0),
(476, 'COA Valais Romand', 'Valais Romand COA', '1.LG.0032', 0),
(477, 'Laufsportverband Oberwallis', 'Oberwallis LSV', '1.VS.1723', 0),
(478, 'Verein Gondo Event', 'Gondo Event Verein', '1.VS.1724', 0),
(479, 'Zürcher Leichtathletik-Verband', 'ZLV', '1.KLV.ZH', 0),
(480, 'TV Hausen a. A.', 'Hausen a. A. TV', '1.ZH.1801', 0),
(481, 'TV Unterstrass Zürich', 'Zürich TVU', '1.ZH.1802', 0),
(482, 'STV Wiedikon', 'Wiedikon STV', '1.ZH.1803', 0),
(483, 'LC Zürich', 'Zürich LC', '1.ZH.1804', 0),
(484, 'TV Samstagern', 'Samstagern TV', '1.ZH.1805', 0),
(485, 'TV Altstetten-Zürich', 'Altstetten-ZH  TV', '1.ZH.1806', 0),
(486, 'LG Oerlikon', 'Oerlikon-Glattal LG', '1.LG.0033', 0),
(487, 'SATUS Zürich-Oerlikon', 'Oerlikon SATUS', '1.ZH.1808', 0),
(488, 'Akad. Sportverband Zürich', 'Zürich Akad. Sportverband', '1.ZH.1809', 0),
(489, 'TV Weiningen', 'Weiningen TV', '1.ZH.1810', 0),
(490, 'LC Regensdorf', 'Regensdorf LC', '1.ZH.1811', 0),
(491, 'TV Regensdorf', 'Regensdorf TV', '1.ZH.1812', 0),
(492, 'TV Egg', 'Egg TV', '1.ZH.1813', 0),
(493, 'Adliswil Track Team', 'Adliswil Track Team', '1.ZH.1814', 0),
(494, 'LC Turicum', 'Turicum LC', '1.ZH.1815', 0),
(495, 'LVWB/LC Opfikon', 'LVWB/Opfikon LC', '1.ZH.1816', 0),
(496, 'STV Dietikon', 'Dietikon STV', '1.ZH.1817', 0),
(497, 'TV Kloten LA', 'Kloten TV LA', '1.ZH.1819', 0),
(498, 'TV Dietlikon', 'Dietlikon TV', '1.ZH.1820', 0),
(499, 'Winterthur Marathon', 'Winterthur Marathon', '1.ZH.1821', 0),
(500, 'LV Winterthur', 'Winterthur LV', '1.ZH.1822', 0),
(501, 'TVNS Winterthur', 'Winterthur TVNS', '1.ZH.1823', 0),
(502, 'TV Kilchberg', 'Kilchberg TV', '1.ZH.1824', 0),
(503, 'SC Seebel Pfungen', 'Pfungen SC Seebel', '1.ZH.1825', 0),
(504, 'LSG Bauma', 'Bauma LSG', '1.ZH.1826', 0),
(505, 'LC Dübendorf', 'Dübendorf LC', '1.ZH.1827', 0),
(506, 'LC Uster', 'Uster LC', '1.ZH.1828', 0),
(507, 'TV Uster', 'Uster TV', '1.ZH.1830', 0),
(508, 'TV Oerlikon', 'Oerlikon TV', '1.ZH.1831', 0),
(509, 'LV Zürcher Oberland', 'Zürcher Oberland LV', '1.ZH.1832', 0),
(510, 'LAR TV Rüti', 'Rüti TV-LAR', '1.ZH.1833', 0),
(511, 'TV Hombrechtikon', 'Hombrechtikon TV', '1.ZH.1834', 0),
(512, 'LC Küsnacht/LGKE', 'Küsnacht/LGKE', '1.ZH.1835', 0),
(513, 'TV Erlenbach/LGKE', 'Erlenbach TV', '1.ZH.1836', 0),
(514, 'LC Meilen', 'Meilen LC', '1.ZH.1837', 0),
(515, 'TV Stäfa', 'Stäfa TV', '1.ZH.1839', 0),
(516, 'TV Thalwil', 'Thalwil TV', '1.ZH.1840', 0),
(517, 'TV Bülach', 'Bülach TV', '1.ZH.1841', 0),
(518, 'TV Horgen', 'Horgen TV', '1.ZH.1842', 0),
(519, 'SATUS Jugendriege Wädenswil', 'Wädenswil SATUS JR', '1.ZH.1843', 0),
(520, 'STV Wädenswil', 'Wädenswil STV', '1.ZH.1844', 0),
(521, 'LV  Albis', 'Albis LV', '1.ZH.1845', 0),
(522, 'TV Obfelden', 'Obfelden TV', '1.ZH.1846', 0),
(523, 'STV Ottenbach', 'Ottenbach STV', '1.ZH.1847', 0),
(524, 'STV Schlieren', 'Schlieren STV', '1.ZH.1849', 0),
(525, 'LG Küsnacht-Erlenbach', 'Küsnacht-Erlenbach LG', '1.LG.0034', 0),
(526, 'LG ZH Oberland Athletics', 'ZH Oberland Athletics LG', '1.LG.0035', 0),
(527, 'TV Maur', 'Maur TV', '1.ZH.1858', 0),
(528, 'LG Goldküste', 'Goldküste LG', '1.LG.0036', 0),
(529, 'TV Mettmenstetten', 'Mettmenstetten TV', '1.ZH.1861', 0),
(530, 'Kinder-Leichtathletik Wyland', 'Wyland KL', '1.ZH.1862', 0),
(531, 'Ausland', 'Ausland', '999999', 0),
(532, 'Club atletica Müstair', 'Müstair Club atletica', '1.GR.0610', 0),
(533, 'GK-Racing', 'GK-Racing', '1.AG.0045', 0),
(534, 'KTV Einsiedeln', 'Einsiedeln KTV', '1.SZ.1323', 0),
(535, 'STV Grabs', 'Grabs STV', '1.SGALV.1051', 0),
(536, 'Läuferriege TV Mauritius Emmen', 'Emmen TV Mauritius LR', '1.LAIS.0754', 0),
(537, 'Verein Lucerne Marathon', 'Verein Lucerne Marathon', '1.LAIS.0755', 0),
(538, 'Laufsportgruppe Olten', 'Olten LSG', '1.SO.1222', 0),
(539, 'Liechtensteiner Turn- und Leic', 'LTLV', '1.SLV.0006', 0),
(540, 'Jeune Chambre Economique de la', 'Gruyère JCE', '100780', 0),
(541, 'FSG Le Noirmont', 'FSG Le Noirmont', '1.JU.0826', 0),
(542, 'TV Brüttisellen', 'Brüttisellen TV', '1.ZH.1863', 0),
(543, 'TV Lüterkofen', 'Lüterkofen TV', '100653', 0),
(544, 'athletcs.BL', 'athletcs.BL', '1.LG.0007', 0),
(545, 'TV Eschlikon', 'Eschlikon TV', '1.TG.1423', 0),
(546, 'TV Oberdiessbach', 'Oberdiessbach TV', '1.BE.0170', 0),
(547, 'Lichtensteinischer Leichathlet', 'LLV', '100727', 0),
(548, 'St.Gallisch.-Appenzel. Leichta', 'SGALV', '1.KLV.SGALV', 0),
(549, 'TV Grüsch', 'Grüsch TV', '1.GR.0611', 0),
(550, 'Athlétissima Lausanne', 'Athlétissima Lausanne', '1.VD.1626', 0),
(551, 'Weltklasse Zürich', 'VfG / LCZ', '1.ZH.1865', 0),
(552, 'LA Bern', 'Bern LA', '1.BE.0171', 0),
(553, 'Regionales Trainingszentrum Th', 'Thun RTZ', '1.BE.0172', 0),
(554, 'SC Diegten', 'Diegten SC', '1.BSBL.0237', 0),
(555, 'perü timing', 'perü timing', '1.BE.0173', 0),
(556, 'LG Oberbaselbiet / BTV Sissach', 'LG Oberbaselbiet / BTV Sissach', '1.BSBL.0238', 0),
(557, 'Free Runners', 'Grenchen Free Runners', '1.SO.1220', 0),
(558, 'Schüler', 'Schüler', '888888', 0),
(559, 'Association Genève Marathon', 'Association Gnève Marathon', '1.GE.0417', 0);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'videowand'
-- 

DROP TABLE IF EXISTS videowand;
CREATE TABLE videowand (
  xVideowand int(11) NOT NULL auto_increment,
  xMeeting int(11) NOT NULL default '0',
  X int(11) NOT NULL default '0',
  Y int(11) NOT NULL default '0',
  InhaltArt enum('dyn','stat') NOT NULL default 'dyn',
  InhaltStatisch text NOT NULL,
  InhaltDynamisch text NOT NULL,
  Aktualisierung int(11) NOT NULL default '0',
  Status enum('black','white','active') NOT NULL default 'active',
  Hintergrund varchar(6) NOT NULL default '',
  Fordergrund varchar(6) NOT NULL default '',
  Bildnr tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (xVideowand)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'videowand'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'wertungstabelle'
-- 

DROP TABLE IF EXISTS wertungstabelle;
CREATE TABLE wertungstabelle (
  xWertungstabelle int(11) NOT NULL auto_increment,
  Name varchar(255) NOT NULL default '',
  PRIMARY KEY  (xWertungstabelle)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'wertungstabelle'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'wertungstabelle_punkte'
-- 

DROP TABLE IF EXISTS wertungstabelle_punkte;
CREATE TABLE wertungstabelle_punkte (
  xWertungstabelle_Punkte int(11) NOT NULL auto_increment,
  xWertungstabelle int(11) NOT NULL default '0',
  xDisziplin int(11) NOT NULL default '0',
  Geschlecht enum('W','M') NOT NULL default 'M',
  Leistung varchar(50) NOT NULL default '',
  Punkte float NOT NULL default '0',
  PRIMARY KEY  (xWertungstabelle_Punkte)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'wertungstabelle_punkte'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'wettkampf'
-- 

DROP TABLE IF EXISTS wettkampf;
CREATE TABLE wettkampf (
  xWettkampf int(11) NOT NULL auto_increment,
  Typ tinyint(4) NOT NULL default '0',
  Haftgeld float unsigned NOT NULL default '0',
  Startgeld float unsigned NOT NULL default '0',
  Punktetabelle tinyint(3) unsigned NOT NULL default '0',
  Punkteformel varchar(10) NOT NULL default '0',
  Windmessung tinyint(4) NOT NULL default '0',
  Info varchar(15) default NULL,
  Zeitmessung tinyint(4) NOT NULL default '0',
  ZeitmessungAuto tinyint(4) NOT NULL default '0',
  xKategorie int(11) NOT NULL default '1',
  xDisziplin int(11) NOT NULL default '1',
  xMeeting int(11) NOT NULL default '1',
  Mehrkampfcode int(11) NOT NULL default '0',
  Mehrkampfende tinyint(4) NOT NULL default '0',
  Mehrkampfreihenfolge tinyint(4) NOT NULL default '0',
  xKategorie_svm int(11) NOT NULL default '0',
  OnlineId int(11) NOT NULL default '0',
  PRIMARY KEY  (xWettkampf),
  KEY xKategorie (xKategorie),
  KEY xDisziplin (xDisziplin),
  KEY xMeeting (xMeeting),
  KEY OnlineId (OnlineId)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle 'wettkampf'
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle 'zeitmessung'
-- 

DROP TABLE IF EXISTS zeitmessung;
CREATE TABLE zeitmessung (
  xZeitmessung int(11) NOT NULL auto_increment,
  OMEGA_Verbindung enum('local','ftp') NOT NULL default 'local',
  OMEGA_Pfad varchar(255) NOT NULL default '',
  OMEGA_Server varchar(255) NOT NULL default '',
  OMEGA_Benutzer varchar(50) NOT NULL default '',
  OMEGA_Passwort varchar(50) NOT NULL default '',
  OMEGA_Ftppfad varchar(255) NOT NULL default '',
  ALGE_Typ varchar(20) NOT NULL default '',
  ALGE_Ftppfad varchar(255) NOT NULL default '',
  ALGE_Passwort varchar(50) NOT NULL default '',
  ALGE_Benutzer varchar(50) NOT NULL default '',
  ALGE_Server varchar(255) NOT NULL default '',
  ALGE_Pfad varchar(255) NOT NULL default '',
  ALGE_Verbindung enum('local','ftp') NOT NULL default 'local',
  xMeeting int(11) NOT NULL default '0',
  PRIMARY KEY  (xZeitmessung),
  KEY xMeeting (xMeeting)
) TYPE=MyISAM AUTO_INCREMENT=1 ;