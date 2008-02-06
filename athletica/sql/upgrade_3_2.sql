##
## athletica sql upgrade to 3.2
## ------------------------------
##
##


CREATE TABLE IF NOT EXISTS wertungstabelle (
  xWertungstabelle int(11) NOT NULL auto_increment,
  Name varchar(255) NOT NULL default '',
  PRIMARY KEY  (xWertungstabelle)
) AUTO_INCREMENT=100;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS wertungstabelle_punkte (
  xWertungstabelle_Punkte int(11) NOT NULL auto_increment,
  xWertungstabelle int(11) NOT NULL default '0',
  xDisziplin int(11) NOT NULL default '0',
  Geschlecht enum('W','M') NOT NULL default 'M',
  Leistung varchar(50) NOT NULL default '',
  Punkte float NOT NULL default '0',
  PRIMARY KEY  (xWertungstabelle_Punkte)
) AUTO_INCREMENT=1;



INSERT IGNORE INTO kategorie(
		   xKategorie
		 , Kurzname
		 , Name
		 , Anzeige
		 , Alterslimite
		 , Code
		 , Geschlecht) 
	    VALUES (''
		 , 'SENM'
		 , 'SEN M'
		 , '2'
		 , '99'
		 , 'SENM'
		 , 'm');

INSERT IGNORE INTO kategorie(
		   xKategorie
		 , Kurzname
		 , Name
		 , Anzeige
		 , Alterslimite
		 , Code
		 , Geschlecht) 
	    VALUES (''
		 , 'SENW'
		 , 'SEN W'
		 , '11'
		 , '99'
		 , 'SENW'
		 , 'w');