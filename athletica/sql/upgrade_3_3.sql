DROP TABLE IF EXISTS base_performance;
CREATE TABLE `base_performance` (
	`id_performance` int(11) NOT NULL auto_increment,
	`id_athlete` int(11) NOT NULL default '0',
	`discipline` varchar(10) NOT NULL default '',
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
	PRIMARY KEY	(`id_performance`),
	UNIQUE KEY `id_athlete_discipline_season` (`id_athlete`,`discipline`,`season`),
	KEY `id_athlete` (`id_athlete`)
) TYPE=MyISAM;

ALTER IGNORE TABLE athlet ADD KEY Lizenznummer (Lizenznummer);
ALTER IGNORE TABLE base_account ADD KEY account_code (account_code);
ALTER IGNORE TABLE base_performance ADD KEY id_athlete (id_athlete);
ALTER IGNORE TABLE base_performance ADD KEY discipline (discipline);
ALTER IGNORE TABLE base_performance ADD KEY season (season);
ALTER IGNORE TABLE base_relay ADD KEY discipline (discipline);
ALTER IGNORE TABLE disziplin ADD KEY Code (Code);


CREATE TABLE `sys_backuptabellen` (
  `xBackup` int(11) NOT NULL auto_increment,
  `Tabelle` varchar(50) default NULL,
  `SelectSQL` text,
  PRIMARY KEY  (`xBackup`)
) TYPE=MyISAM;

INSERT INTO `sys_backuptabellen`(`xBackup`,`Tabelle`,`SelectSQL`) VALUES 
(1,'anlage','SELECT * FROM anlage'),
(2,'anmeldung','SELECT * FROM anmeldung WHERE xMeeting = \'%d\''),
(3,'athlet','SELECT * FROM athlet'),
(5,'base_account','SELECT * FROM base_account'),
(6,'base_athlete','SELECT * FROM base_athlete'),
(7,'base_log','SELECT * FROM base_log'),
(8,'base_performance','SELECT * FROM base_performance'),
(9,'base_relay','SELECT * FROM base_relay'),
(10,'base_svm','SELECT * FROM base_svm'),
(11,'disziplin','SELECT * FROM disziplin'),
(12,'faq','SELECT * FROM faq'),
(13,'kategorie','SELECT * FROM kategorie'),
(16,'layout','SELECT * FROM layout WHERE xMeeting = \'%d\''),
(17,'meeting','SELECT * FROM meeting WHERE xMeeting=\'%d\''),
(18,'omega_typ','SELECT * FROM omega_typ'),
(19,'region','SELECT * FROM region'),
(20,'resultat','SELECT\r\n    resultat.*\r\nFROM\r\n    athletica.resultat\r\n    LEFT JOIN athletica.serienstart \r\n        ON (resultat.xSerienstart = serienstart.xSerienstart)\r\n    LEFT JOIN athletica.start \r\n        ON (serienstart.xStart = start.xStart)\r\n    LEFT JOIN athletica.wettkampf \r\n        ON (start.xWettkampf = wettkampf.xWettkampf)\r\nWHERE (wettkampf.xMeeting =40);'),
(21,'runde','SELECT\r\n	runde.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\');'),
(22,'rundenlog','SELECT\r\n    rundenlog.*\r\nFROM\r\n    athletica.runde\r\n    JOIN athletica.rundenlog \r\n        ON (runde.xRunde = rundenlog.xRunde)\r\n    JOIN athletica.wettkampf \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\');'),
(23,'rundenset','SELECT * FROM rundenset WHERE xMeeting = \'%d\''),
(24,'rundentyp','SELECT * FROM rundentyp'),
(25,'serie','SELECT\r\n	serie.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\n    LEFT JOIN athletica.serie \r\n        ON (runde.xRunde = serie.xRunde)\r\nWHERE (wettkampf.xMeeting =\'%d\');'),
(26,'serienstart','SELECT\r\n	serienstart.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\n    LEFT JOIN athletica.serie \r\n        ON (runde.xRunde = serie.xRunde)\r\n    LEFT JOIN athletica.serienstart \r\n        ON (serie.xSerie = serienstart.xSerie)\r\nWHERE (wettkampf.xMeeting =\'%d\');'),
(27,'stadion','SELECT * FROM stadion'),
(28,'staffel','SELECT * FROM staffel WHERE xMeeting = \'%d\''),
(29,'staffelathlet','SELECT\r\n    staffelathlet.*\r\nFROM\r\n    athletica.staffelathlet\r\n    INNER JOIN athletica.runde \r\n        ON (staffelathlet.xRunde = runde.xRunde)\r\n    INNER JOIN athletica.wettkampf \r\n        ON (runde.xWettkampf = wettkampf.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\');'),
(30,'start','SELECT\r\n    start.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.start \r\n        ON (wettkampf.xWettkampf = start.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\');'),
(31,'team','SELECT * FROM team WHERE xMeeting = \'%d\''),
(32,'teamsm','SELECT * FROM teamsm WHERE xMeeting = \'%d\''),
(33,'teamsmathlet','SELECT\r\n    teamsmathlet.*\r\nFROM\r\n    athletica.teamsmathlet\r\n    LEFT JOIN athletica.anmeldung \r\n        ON (teamsmathlet.xAnmeldung = anmeldung.xAnmeldung)\r\nWHERE (anmeldung.xMeeting =\'%d\');'),
(34,'verein','SELECT * FROM verein'),
(35,'wertungstabelle','SELECT * FROM wertungstabelle'),
(36,'wertungstabelle_punkte','SELECT * FROM wertungstabelle_punkte'),
(37,'wettkampf','SELECT * FROM wettkampf WHERE xMeeting = \'%d\''),
(38,'zeitmessung','SELECT * FROM zeitmessung WHERE xMeeting = \'%d\'');

DROP TABLE IF EXISTS kategorie_svm;
CREATE TABLE kategorie_svm (
  xKategorie_svm int(11) NOT NULL auto_increment,
  Name varchar(100) NOT NULL default '',
  Code varchar(5) NOT NULL default '',
  PRIMARY KEY  (xKategorie_svm),
  KEY Code (Code)
) TYPE=MyISAM AUTO_INCREMENT=36;

INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
(1, '20.11 Männer Nat. A', '20_11'),
(2, '21.11 Männer Nat. B', '21_11'),
(3, '22.10 Männer Nat. C', '22_10'),
(4, '23.11 Männer 1.Liga', '23_11'),
(5, '26.1 Männer 2.Liga', '26_01'),
(6, '26.2 Männer 3.Liga', '26_02'),
(7, '26.3 Männer 4.Liga', '26_03'),
(8, '26.4 M30 u. älter', '26_04'),
(9, '24.11 U20M 1, Einzel', '24_11'),
(10, '26.5 U20M 2, Einzel', '26_05'),
(11, '26.6 U18M Einzel', '26_06'),
(12, '26.7 U18M Mehrkampf', '26_07'),
(13, '26.8 U16M Einzel', '26_08'),
(14, '26.9 U16M Mehrkampf', '26_09'),
(15, '26.10 U14M Einzel', '26_10'),
(16, '26.11 U14M Mannschaftswettkampf', '26_11'),
(17, '26.12 U12M Mannschaftswettkampf', '26_12'),
(18, '20.11 Frauen Nat. A', '20_12'),
(19, '21.11 Frauen Nat. B', '21_12'),
(20, '23.11 Frauen 1.Liga', '23_12'),
(21, '27.1 2. Liga Frauen', '27_01'),
(22, '27.2 W30 u. älter', '27_02'),
(23, '24.11 U20W Einzel', '24_12'),
(24, '27.3 U18W Einzel', '27_03'),
(25, '27.4 U18W Mehrkampf', '27_04'),
(26, '27.5 U16W Einzel', '27_05'),
(27, '27.6 U16W Mehrkampf', '27_06'),
(28, '27.7 U14W Einzel', '27_07'),
(29, '27.8 U14W Mannschaftswettkampf', '27_08'),
(30, '27.9 U12W Mannschaftswettkampf', '27_09'),
(31, '27.10 U12M/U12W Mixed-Team', '27_10'),
(32, '28.1 Schulen U21M ohne Lizenz', '28_01'),
(33, '28.2 Schulen U21W ohne Lizenz', '28_02'),
(34, '29.1 Männer ohne Lizenz', '29_01'),
(35, '29.2 Frauen ohne Lizenz', '29_02');

TRUNCATE TABLE faq;