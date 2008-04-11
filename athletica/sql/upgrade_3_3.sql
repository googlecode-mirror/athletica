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

ALTER TABLE meeting ADD COLUMN Saison enum('','I','O') NOT NULL default '' AFTER Haftgeld;


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
(1, '20.01 M�nner Nationalliga A', '20_01'),
(2, '21.01 M�nner Nationalliga B', '21_01'),
(3, '22.01 M�nner Nationalliga C', '22_01'),
(4, '23.01 M�nner 1.Liga', '23_01'),
(5, '26.01 M�nner 2.Liga', '26_01'),
(6, '26.02 M�nner 3.Liga', '26_02'),
(7, '26.03 M�nner 4.Liga', '26_03'),
(8, '26.04 M30 und �lter M�nner', '26_04'),
(9, '24.01 U20 M 1', '24_01'),
(10, '26.05 U20 M 2', '26_05'),
(11, '26.06 U18 M', '26_06'),
(12, '26.07 U18 M MK', '26_07'),
(13, '26.08 U16 M', '26_08'),
(14, '26.09 U14 M MK', '26_09'),
(15, '26.10 U14 M', '26_10'),
(16, '26.11 U14 M MK', '26_11'),
(17, '26.12 U12 M MK', '26_12'),
(18, '20.02 Frauen Nationalliga A', '20_02'),
(19, '21.02 Frauen Nationalliga B', '21_02'),
(20, '23.02 Frauen 1.Liga', '23_02'),
(21, '27.01 Frauen 2. Liga', '27_01'),
(22, '27.02 Seniorinnen', '27_02'),
(23, '24.02 U20 W', '24_02'),
(24, '27.03 U18 W', '27_03'),
(25, '27.04 U18 W MK', '27_04'),
(26, '27.05 U16 W', '27_05'),
(27, '27.06 U16 W MK', '27_06'),
(28, '27.07 U14 W', '27_07'),
(29, '27.08 U14 W MK', '27_08'),
(30, '27.09 U12 W MK', '27_09'),
(31, '27.10 Mixed Team', '27_10'),
(32, '28.01 Schulen M�nner', '28_01'),
(33, '28.02 Schulen Frauen', '28_02'),
(34, '29.01 M�nner offene Klasse', '29_01'),
(35, '29.02 Frauen offene Klasse', '29_02');

DROP TABLE faq;
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
) TYPE=MyISAM;

INSERT INTO `faq` (`xFaq`, `Frage`, `Antwort`, `Zeigen`, `PosTop`, `PosLeft`, `height`, `width`, `Seite`, `Sprache`, `FarbeTitel`, `FarbeHG`) VALUES 
(1, 'Dynamische Navigation', '<b>[alle Browser]</b><br/>\r\ndie Liste der Anmeldungen kann durch Doppelklick auf den grauen Balken ein- oder ausgeblendet werden.<br/><br/>\r\n\r\n<b>[Internet Explorer]</b><br/>\r\ndie Liste der Anmeldungen kann durch klicken und ziehen des grauen Balkens dynamisch angepasst werden.<br/>', 'y', 10, 10, 0, 300, 'meeting_entrylist', 'de', 'FFAA00', 'FFCC00'),
(2, 'Datensicherung', 'Neu k�nnen auch einzelne Meetings gesichert werden.<br/>\r\n<b>ACHTUNG</b>: Beim zur�ckspielen einer Sicherung mit einelnem Meeting werden trotzdem alle vorherigen Daten gel�scht.<br/><br/>\r\n\r\n<b>Stammdaten sichern</b><br/>\r\nDie Stammdaten sollten nur gesichert werden, wenn das Meeting zu Hause erfasst, und sp�ter am Meeting mit fehlender Internetverbindung geladen wird.<br/>\r\nDie Dateigr�sse der Sicherung nimmt bei der Sicherung <b>mit</b> Stammdaten wesentlich zu.', 'y', 200, 50, 0, 0, 'admin', 'de', 'FFAA00', 'FFCC00'),
(3, 'Wo sind Zeitmessung und Drucklayout?', 'Da die Zeitmessung und das Drucklayout Meetingspezifisch sind, wurden diese Men�punkte in das �bergeordnete Men� <b>Meeting</b> verschoben', 'y', 20, 535, 0, 0, 'admin', 'de', 'FFAA00', 'FFCC00'),
(4, 'Versionspr�fung', 'Die Versionspr�fung wird wegen Leistungsproblemen bei fehlender Internetverbindung nicht mehr direkt auf der Startseite der Administration ausgef�hrt.<br/><br/>\r\n\r\nKlicken Sie auf die Schaltfl�che <b>Version pr�fen</b> im Abschnitt <b>Konfiguration</b> um zu pr�fen, ob die Athletica-Version aktuell ist.', 'y', 20, 50, 0, 0, 'admin', 'de', 'FFAA00', 'FFCC00'),
(5, 'Falsche Pfade', '<b>ACHTUNG:</b> Wenn Athletica den eingetragenen Pfad nicht finden kann, werden die Einstellungen nicht gespeichert.<br/>\r\nDies gilt auch f�r Backups: Wird eine Sicherung geladen, deren Zeitmessungspfade auf dem aktuellen Computer nicht existieren, werden diese geleert.<br/><br/>\r\n\r\nAndernfalls k�nnte es zu Problemen mit der Geschwindigkeit von Athletica-Funktionen f�hren.', 'y', 50, 300, 0, 0, 'meeting_timing', 'de', 'FFAA00', 'FFCC00'),
(6, 'Rangliste mit Bestleistungen', 'Neu werden auch die Bestleistungen in den Ranglisten angezeigt.\r\nNeben dem eigentlichen Resultat sind die aktuellen Bestleistungen (Datum, Meeting und Leistung f�r Saisonbestleistung und pers�nliche Bestleistung) f�r jeden Athleten ersichtlich.<br/><br/>\r\n\r\nDer Aufbau der Rangliste <b>mit Bestleitungen</b> kann etwas l�nger dauern.', 'y', 150, 50, 0, 0, 'event_rankinglists,speaker_rankinglists', 'de', 'FFAA00', 'FFCC00'),
(7, 'Navigation dynamique', '<b>[tous les navigateurs]</b><br/>\r\nla liste des inscriptions peut �tre affich�e ou cach�e par un double click sur la barre grise.<br/><br/>\r\n\r\n<b>[Internet Explorer]</b><br/>\r\nla liste des inscriptions peut �tre adapt�e de mani�re dynamique en cliquant et tirant sur la barre grise.', 'y', 10, 10, 0, 300, 'meeting_entrylist', 'fr', 'FFAA00', 'FFCC00'),
(8, 'Contr�le de la version', 'Le contr�le de la version ne sera plus ex�cut� directement sur la page d''accueil de l''administration en raison de probl�mes en cas d''absence de liaison Internet.<br/><br/>\r\n\r\nCliquez sur le bouton <b>contr�ler la version</b> dans la partie <b>configuration</b> pour contr�ler si la version Athletica est actuelle.', 'y', 20, 50, 0, 0, 'admin', 'fr', 'FFAA00', 'FFCC00'),
(9, 'Sauvegarde des donn�es', 'Il est nouvellement aussi possible de sauvegarder diff�rents meetings.<br/>\r\n<b>ATTENTION:</b> en renvoyant une sauvegarde avec un meeting, toutes les donn�es ant�rieures sont tout de m�me effac�es.<br/><br/>\r\n\r\n<b>Sauvegarder les donn�es de base </b><br/>\r\nLes donn�es de base ne devraient �tre sauvegard�es que si le meeting est enregistr� � la maison et charg� plus tard lors du meeting en l''absence de liaison Internet.\r\nLa grandeur du fichier de sauvegarde augmente sensiblement lors de la sauvegarde <b>avec</b> des donn�es de base.', 'y', 200, 50, 0, 0, 'admin', 'fr', 'FFAA00', 'FFCC00'),
(10, 'O� sont le chronom�trage et la mise en page pour l''impression?', 'Comme le chronom�trage et la mise en page pour l''impression sont sp�cifiques au meeting, ces points du menu ont �t� d�plac�s dans le menu subordonn� <b>Meeting</b>.', 'y', 20, 535, 0, 0, 'admin', 'fr', 'FFAA00', 'FFCC00'),
(11, 'Chemins faux', '<b>ATTENTION:</b> si Athletica ne peut pas trouver le chemin enregistr�, les r�glages ne seront pas enregistr�s.<br/>\r\nCeci est �galement valable pour les Backups: si une sauvegarde est charg�e, dont les chemins de chronom�trage n''existent pas sur l''ordinateur actuel, ceux-ci sont vid�s.<br/><br/>\r\n\r\nAutrement on pourrait conna�tre des probl�mes avec la vitesse des fonctions Athletica.', 'y', 50, 300, 0, 0, 'meeting_timing', 'fr', 'FFAA00', 'FFCC00'),
(12, 'Liste de r�sultats avec meilleures performances', 'Les meilleures performances sont nouvellement aussi affich�es sur les listes de r�sultats. Outre le r�sultat � proprement parl�, les meilleures performances actuelles (date, meeting, meilleure performance de la saison et meilleure performance personnelle) de chaque athl�te sont visibles.<br/><br/>f\r\n\r\nL''�laboration de la liste de r�sultats <b>avec meilleures performances</b> peut durer un peu plus longtemps.', 'y', 150, 50, 0, 0, 'event_rankinglists,speaker_rankinglists', 'fr', 'FFAA00', 'FFCC00'),
(13, 'Quittung drucken', 'Neu k�nnen Quittungen pro Athlet oder f�r alle Athleten eines Vereins gedruckt werden.<br/>\r\nDie Druckseite f�r die Quittungen finden Sie unter <b>Meeting > Anmeldungen > Quittung...</b>', 'y', 50, 50, 0, 300, 'meeting_entries_start', 'de', 'FFAA00', 'FFCC00'),
(14, 'Imprimer la quittance', 'Avec la nouvelle version on peut imprimer des quittances par athl�te ou par club.<br/>\r\nLe lien pour imprimer les quittances se trouve sous le menu <b>Meeting > Inscriptions > Quittance...</b>', 'y', 50, 50, 0, 300, 'meeting_entries_start', 'fr', 'FFAA00', 'FFCC00');