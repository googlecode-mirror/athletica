UPDATE kategorie 
   SET Kurzname = 'MASM', 
       Name = 'MASTERS M', 
       Code = 'MASM' 
 WHERE Kurzname = 'SENM';

UPDATE kategorie 
   SET Kurzname = 'MASW', 
       Name = 'MASTERS W', 
       Code = 'MASW' 
 WHERE Kurzname = 'SENW';

ALTER TABLE meeting 
 ADD COLUMN Startgeld float NOT NULL default '0', 
 ADD COLUMN StartgeldReduktion fload NOT NULL default '0',
 ADD COLUMN Haftgeld float NOT NULL default '0';