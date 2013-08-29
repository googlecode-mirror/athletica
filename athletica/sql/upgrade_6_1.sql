
ALTER TABLE teamsm ADD Gruppe char(2) DEFAULT '';   

ALTER TABLE start ADD Gruppe char(2) DEFAULT '';  

ALTER TABLE teamsm ADD `Quali` int(11) NOT NULL DEFAULT '0'; 
ALTER TABLE teamsm ADD `Leistung` int(9) NOT NULL DEFAULT '0'; 


INSERT INTO `disziplin_de` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES ('Stab-Weit', 'Stab - Weit',325, 15, 0, 5, '01:00:00', '00:20:00', 0, 332, 1);   
INSERT INTO `disziplin_fr` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES ('perche-long', 'perche en longueur',325, 15, 0, 5, '01:00:00', '00:20:00', 0, 332, 1); 
INSERT INTO `disziplin_it` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES ('asta-lungo', 'salto con l\'asta et lungo',325, 15, 0, 5, '01:00:00', '00:20:00', 0, 332, 1);     
                  
INSERT INTO `disziplin_de` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES ('Drehwurf', 'Drehwerfen',365, 15, 0, 8, '01:00:00', '00:20:00', 0, 354, 1);    
INSERT INTO `disziplin_fr` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES ('lancer-rotation', 'lancer en rotation',365, 15, 0, 8, '01:00:00', '00:20:00', 0, 354, 1); 
INSERT INTO `disziplin_it` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES ('lancio-rotativo', 'lancio di rotativo',365, 15, 0, 8, '01:00:00', '00:20:00', 0, 354, 1);                                                                              




