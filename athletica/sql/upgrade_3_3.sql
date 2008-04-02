DROP TABLE base_performance;

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

insert  into `sys_backuptabellen`(`xBackup`,`Tabelle`,`SelectSQL`) values 
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
(14,'kategorie_svm','SELECT * FROM kategorie_svm'),
(15,'land','SELECT * FROM land'),
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