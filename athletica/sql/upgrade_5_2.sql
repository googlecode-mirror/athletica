

ALTER TABLE runde ADD StatusChanged enum('y','n') NOT NULL DEFAULT 'y';

ALTER TABLE meeting ADD StatusChanged enum('y','n') NOT NULL DEFAULT 'y';

ALTER TABLE start ADD VorjahrLeistung int(11) DEFAULT 0;

ALTER TABLE anmeldung ADD VorjahrLeistungMK int(11) DEFAULT 0;

INSERT INTO `disziplin_de` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES  ('5KAMPF_U16M_I', 'Fünfkampf U16 M Indoor', 407, 6, 0, 9, '01:00:00', '00:15:00', 5, 425, 1, 'y');
INSERT INTO `disziplin_de` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES  ('5KAMPF_U16W_I', 'Fünfkampf U16 W Indoor', 410, 6, 0, 9, '01:00:00', '00:15:00', 5, 426, 1, 'y');
INSERT INTO `disziplin_de` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES  ('8KAMPF_U18M', 'Achtkampf U18 M', 433, 6, 0, 9, '01:00:00', '00:15:00', 5, 427, 1, 'y');
INSERT INTO `disziplin_fr` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES ('5ATHLON_U16M_I', 'Pentathlon U16 M Indoor', 407, 6, 0, 9, '01:00:00', '00:15:00', 5, 425, 1, 'y');
INSERT INTO `disziplin_fr` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES  ('5ATHLON_U16W_I', 'Pentathlon U16 w Indoor', 410, 6, 0, 9, '01:00:00', '00:15:00', 5, 426, 1, 'y');
INSERT INTO `disziplin_fr` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES  ('8ATHLON_U18M', 'Octathlon U18 M', 433, 6, 0, 9, '01:00:00', '00:15:00', 5, 427, 1, 'y');

INSERT INTO `disziplin_it` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES ('55ATHLON_U16M_I', 'Pentathlon U16 M Indoor', 407, 6, 0, 9, '01:00:00', '00:15:00', 5, 425, 1, 'y');
INSERT INTO `disziplin_it` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES  ('5ATHLON_U16W_I', 'Pentathlon U16 w Indoor', 410, 6, 0, 9, '01:00:00', '00:15:00', 5, 426, 1, 'y');
INSERT INTO `disziplin_it` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ,aktiv) VALUES  ('8ATHLON_U18M', 'Octathlon U18 M', 433, 6, 0, 9, '01:00:00', '00:15:00', 5, 427, 1, 'y');

UPDATE disziplin_de SET Kurzname = '5KAMPF_U18M_I', Name = 'Fünfkampf U18 M Indoor' , Anzeige= 406 WHERE Code = 424;  
UPDATE disziplin_de SET Kurzname = '5KAMPF_W_U20W_I', Name = 'Fünfkampf W / U20 W Indoor' , Anzeige= 408 WHERE Code = 394; 
UPDATE disziplin_de SET Kurzname = '5KAMPF_U18W_I', Name = 'Fünfkampf U18 W Indoor' , Anzeige= 409 WHERE Code = 395; 
                
UPDATE disziplin_de SET Kurzname = '7KAMPF_M_I', Name = 'Siebenkampf M Indoor' , Anzeige= 413 WHERE Code = 396; 
UPDATE disziplin_de SET Kurzname = '7KAMPF_U20M_I', Name = 'Siebenkampf U20 M Indoor' , Anzeige= 414 WHERE Code = 397;  
UPDATE disziplin_de SET Kurzname = '7KAMPF_U18M_I', Name = 'Siebenkampf U18 M Indoor' , Anzeige= 415 WHERE Code = 398;  
             
             
UPDATE disziplin_fr SET Kurzname = '5ATHLON_U18M_I', Name = 'Pentathlon U18 M Indoor' , Anzeige= 406 WHERE Code = 424;  
UPDATE disziplin_fr SET Kurzname = '5ATHLON_W_U20W_I', Name = 'Pentathlon W / U20 W Indoor' , Anzeige= 408 WHERE Code = 394;  
UPDATE disziplin_fr SET Kurzname = '5ATHLON_U18W_I', Name = 'Pentathlon U18 W Indoor' , Anzeige= 409 WHERE Code = 395; 
                
UPDATE disziplin_fr SET Kurzname = '7ATHLON_M_I', Name = 'Heptathlon M Indoor' , Anzeige= 413 WHERE Code = 396;  
UPDATE disziplin_fr SET Kurzname = '7ATHLON_U20M_I', Name = 'Heptathlon U20 M Indoor' , Anzeige= 414 WHERE Code = 397;  
UPDATE disziplin_fr SET Kurzname = '7ATHLON_U18M_I', Name = 'Heptathlon U18 M Indoor' , Anzeige= 415 WHERE Code = 398;  
             
UPDATE disziplin_it SET Kurzname = '5ATHLON_U18M_I', Name = 'Pentathlon U18 M Indoor' , Anzeige= 406 WHERE Code = 424;  
UPDATE disziplin_it SET Kurzname = '5ATHLON_W_U20W_I', Name = 'Pentathlon W / U20 W Indoor' , Anzeige= 408 WHERE Code = 394;  
UPDATE disziplin_it SET Kurzname = '5ATHLON_U18W_I', Name = 'Pentathlon U18 W Indoor' , Anzeige= 409 WHERE Code = 395; 
                
UPDATE disziplin_it SET Kurzname = '7ATHLON_M_I', Name = 'Heptathlon M Indoor' , Anzeige= 413 WHERE Code = 396;  
UPDATE disziplin_it SET Kurzname = '7ATHLON_U20M_I', Name = 'Heptathlon U20 M Indoor' , Anzeige= 414 WHERE Code = 397;
UPDATE disziplin_it SET Kurzname = '7ATHLON_U18M_I', Name = 'Heptathlon U18 M Indoor' , Anzeige= 415 WHERE Code = 398;   
             
                                                 
                  
UPDATE disziplin_de SET Anzeige= 418 WHERE Code = 392; 
UPDATE disziplin_de SET Anzeige= 419 WHERE Code = 407;  
UPDATE disziplin_de SET Anzeige= 420 WHERE Code = 393;  
UPDATE disziplin_de SET Anzeige= 421 WHERE Code = 405;  
UPDATE disziplin_de SET Anzeige= 422 WHERE Code = 406;  
UPDATE disziplin_de SET Name = 'Fünfkampf W' , Anzeige= 423 WHERE Code = 416;  
UPDATE disziplin_de SET Anzeige= 424 WHERE Code = 417;  
UPDATE disziplin_de SET Anzeige= 425 WHERE Code = 418;
UPDATE disziplin_de SET Anzeige= 426 WHERE Code = 399; 
                                        
UPDATE disziplin_de SET Anzeige= 429 WHERE Code = 402; 
UPDATE disziplin_de SET Anzeige= 430 WHERE Code = 400;  
UPDATE disziplin_de SET Anzeige= 431 WHERE Code = 401;  
UPDATE disziplin_de SET Kurzname = '10KAMPF_M', Name = 'Zehnkampf M' , Anzeige= 434 WHERE Code = 410;  
UPDATE disziplin_de SET Anzeige= 435 WHERE Code = 411;  
UPDATE disziplin_de SET Anzeige= 436 WHERE Code = 412;   
UPDATE disziplin_de SET Anzeige= 437 WHERE Code = 413;  
UPDATE disziplin_de SET Anzeige= 438 WHERE Code = 414;  
UPDATE disziplin_de SET Anzeige= 439 WHERE Code = 408;  
             
 UPDATE disziplin_fr SET Anzeige= 418 WHERE Code = 392; 
 UPDATE disziplin_fr SET Anzeige= 419 WHERE Code = 407; 
 UPDATE disziplin_fr SET Anzeige= 420 WHERE Code = 393;  
 UPDATE disziplin_fr SET Anzeige= 421 WHERE Code = 405;  
 UPDATE disziplin_fr SET Anzeige= 422 WHERE Code = 406;  
 UPDATE disziplin_fr SET Name = 'Pentathlon F' , Anzeige= 423 WHERE Code = 416;  
 UPDATE disziplin_fr SET Anzeige= 424 WHERE Code = 417;   
 UPDATE disziplin_fr SET Anzeige= 425 WHERE Code = 418; 
 UPDATE disziplin_fr SET Anzeige= 426 WHERE Code = 399;  
                                        
 UPDATE disziplin_fr SET Anzeige= 429 WHERE Code = 402;  
 UPDATE disziplin_fr SET Anzeige= 430 WHERE Code = 400;  
 UPDATE disziplin_fr SET Anzeige= 431 WHERE Code = 401;  
 UPDATE disziplin_fr SET Kurzname = '10ATHLON_M', Name = 'Décathlon M' , Anzeige= 434 WHERE Code = 410;  
 UPDATE disziplin_fr SET Kurzname = '10ATHLON_U20M', Name = 'Décathlon U20 M' , Anzeige= 435 WHERE Code = 411;  
 UPDATE disziplin_fr SET Kurzname = '10ATHLON_U18M', Name = 'Décathlon U18 M' , Anzeige= 436 WHERE Code = 412;   
 UPDATE disziplin_fr SET Kurzname = '10ATHLON_W', Name = 'Décathlon W' , Anzeige= 437 WHERE Code = 413; 
 UPDATE disziplin_fr SET Anzeige= 438 WHERE Code = 414;  
 UPDATE disziplin_fr SET Anzeige= 439 WHERE Code = 408;  
             
 UPDATE disziplin_it SET Anzeige= 418 WHERE Code = 392; 
 UPDATE disziplin_it SET Anzeige= 419 WHERE Code = 407; 
 UPDATE disziplin_it SET Anzeige= 420 WHERE Code = 393; 
 UPDATE disziplin_it SET Anzeige= 421 WHERE Code = 405; 
 UPDATE disziplin_it SET Anzeige= 422 WHERE Code = 406;  
 UPDATE disziplin_it SET Name = 'Pentathlon F' , Anzeige= 423 WHERE Code = 416;  
 UPDATE disziplin_it SET Anzeige= 424 WHERE Code = 417;   
 UPDATE disziplin_it SET Anzeige= 425 WHERE Code = 418; 
 UPDATE disziplin_it SET Anzeige= 426 WHERE Code = 399;  
                                        
 UPDATE disziplin_it SET Anzeige= 429 WHERE Code = 402;  
 UPDATE disziplin_it SET Anzeige= 430 WHERE Code = 400;  
 UPDATE disziplin_it SET Anzeige= 431 WHERE Code = 401;  
 UPDATE disziplin_it SET Kurzname = '10ATHLON_M', Name = 'Decathlon M' , Anzeige= 434 WHERE Code = 410;  
 UPDATE disziplin_it SET Anzeige= 435 WHERE Code = 411;  
 UPDATE disziplin_it SET Anzeige= 436 WHERE Code = 412;   
 UPDATE disziplin_it SET  Anzeige= 437 WHERE Code = 413;  
 UPDATE disziplin_it SET Anzeige= 438 WHERE Code = 414;  
 UPDATE disziplin_it SET Anzeige= 439 WHERE Code = 408;                                  
            
 UPDATE disziplin_de SET Kurzname = '10KAMPF_MASTER', Name = 'Zehnkampf Master'  WHERE Code = 414;   
 UPDATE disziplin_fr SET Kurzname = '10ATHLON_MASTER', Name = 'Décathlon Master'  WHERE Code = 414;   
 UPDATE disziplin_it SET Kurzname = '10ATHLON_MASTER', Name = 'Decathlon Master'  WHERE Code = 414;   