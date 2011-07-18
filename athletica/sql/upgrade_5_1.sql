

ALTER TABLE athlet CHANGE Adresse Adresse varchar(50); 
ALTER TABLE athlet CHANGE Ort Ort varchar(50); 
ALTER TABLE athlet CHANGE Email Email varchar(50); 

ALTER TABLE team DROP INDEX `MeetingKatName`;
ALTER TABLE team ADD UNIQUE KEY `MeetingKatName` (`xMeeting`,`xKategorie`,`Name`,`xKategorie_svm`);    

