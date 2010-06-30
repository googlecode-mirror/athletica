 
 
DROP TABLE IF EXISTS kategorie_svm;
CREATE TABLE kategorie_svm (
  xKategorie_svm int(11) NOT NULL auto_increment,
  Name varchar(100) NOT NULL default '',
  Code varchar(5) NOT NULL default '',
  PRIMARY KEY  (xKategorie_svm),
  KEY Code (Code)
) TYPE=MyISAM AUTO_INCREMENT=0;

INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
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
 
 
 ALTER TABLE staffel ADD COLUMN staffelID int(11) NULL default '' AFTER Startnummer;      
 