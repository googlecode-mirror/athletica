-- 
-- create language tables discipline
--    
                
 CREATE TABLE `disziplin_fr` SELECT * FROM disziplin;    
 CREATE TABLE `disziplin_it` SELECT * FROM disziplin;  
 RENAME TABLE `disziplin` TO `disziplin_de`; 
             
 DROP TABLE IF EXISTS `disziplin`;    
 UPDATE `disziplin_de` SET Name = 'UBS Kids Cup', Kurzname = 'UKC' WHERE Code = 403;    
 UPDATE disziplin_de SET Staffellaeufer = '3' WHERE Code = '602'; 
 DELETE FROM `disziplin_de` WHERE Code = 404;      
 UPDATE `disziplin_de` SET Name = 'Zehnkampf W' WHERE Code = 413;   
 DELETE FROM `disziplin_de` WHERE Code = 186;   
 UPDATE `disziplin_de` SET Anzeige = 440, Kurzname = '10KM' WHERE Code = 491;     
                   
-- 
-- disciplines fr
-- 

UPDATE `disziplin_fr` SET Name = 'UBS Kids Cup', Kurzname = 'UKC' WHERE Code = 403;    
UPDATE disziplin_fr SET Staffellaeufer = '3' WHERE Code = '602'; 
DELETE FROM `disziplin_fr` WHERE Code = 404;      
UPDATE `disziplin_fr` SET Name = 'Zehnkampf W' WHERE Code = 413;   
DELETE FROM `disziplin_fr` WHERE Code = 186;   
UPDATE `disziplin_fr` SET Anzeige = 440, Kurzname = '10KM' WHERE Code = 491;                           
                     
UPDATE `disziplin_fr` SET Name = '1 mile', Kurzname = '1MILE' WHERE Code = 120;  
UPDATE `disziplin_fr` SET Name = '1 heure', Kurzname = '1HEURE' WHERE Code = 182;   
UPDATE `disziplin_fr` SET Name = 'Demimarathon', Kurzname = 'DEMIMARATHON' WHERE Code = 190; 
UPDATE `disziplin_fr` SET Name = 'Marathon', Kurzname = 'MARATHON' WHERE Code = 200;      
                               
UPDATE `disziplin_fr` SET Name = '50 m haies 106.7' WHERE Code = 232; 
                             
UPDATE `disziplin_fr` SET Name = '50 m haies 99.1' WHERE Code = 233;  
                            
UPDATE `disziplin_fr` SET Name = '50 m haies 91.4' WHERE Code = 234;  
UPDATE `disziplin_fr` SET Name = '50 m haies 84.0' WHERE Code = 235;  
UPDATE `disziplin_fr` SET Name = '50 m haies 76.2' WHERE Code = 236;  
UPDATE `disziplin_fr` SET Name = '60 m haies 106.7' WHERE Code = 252;  
UPDATE `disziplin_fr` SET Name = '60 m haies 99.1' WHERE Code = 253; 
UPDATE `disziplin_fr` SET Name = '60 m haies 91.4' WHERE Code = 254;   
UPDATE `disziplin_fr` SET Name = '60 m haies 84.0' WHERE Code = 255; 
UPDATE `disziplin_fr` SET Name = '60 m haies 76.2' WHERE Code = 256;   
UPDATE `disziplin_fr` SET Name = '80 m haies 76.2' WHERE Code = 258;   
UPDATE `disziplin_fr` SET Name = '100 m haies 84.0' WHERE Code = 261; 
UPDATE `disziplin_fr` SET Name = '100 m haies 76.2' WHERE Code = 259; 
UPDATE `disziplin_fr` SET Name = '110 m haies 106.7' WHERE Code = 271;
UPDATE `disziplin_fr` SET Name = '110 m haies 99.1' WHERE Code = 269;                             
UPDATE `disziplin_fr` SET Name = '110 m haies 91.4' WHERE Code = 268;      
UPDATE `disziplin_fr` SET Name = '200 m haies' WHERE Code = 280; 
UPDATE `disziplin_fr` SET Name = '300 m haies 91.4' WHERE Code = 289;  
                           
UPDATE `disziplin_fr` SET Name = '300 m haies 84.0' WHERE Code = 290; 
UPDATE `disziplin_fr` SET Name = '300 m haies 76.2' WHERE Code = 291;      
UPDATE `disziplin_fr` SET Name = '400 m haies 91.4' WHERE Code = 301;
UPDATE `disziplin_fr` SET Name = '400 m haies 76.2' WHERE Code = 298;    
                           
UPDATE `disziplin_fr` SET Name = 'Décathlon W' WHERE Code = 413;                                                                                                                                                                                                                                    
                            
UPDATE `disziplin_fr` SET Name = '5x libre', Kurzname = '5XLIBRE' WHERE Code = 497;  
UPDATE `disziplin_fr` SET Name = '6x libre', Kurzname = '6XLIBRE' WHERE Code = 499;
                                   
UPDATE `disziplin_fr` SET Name = 'Hauteur', Kurzname =  'HAUTEUR' WHERE Code = 310;;   
UPDATE `disziplin_fr` SET Name = 'Perche', Kurzname =  'PERCHE' WHERE Code = 320;; 
UPDATE `disziplin_fr` SET Name = 'Longeur', Kurzname = 'LONGEUR' WHERE Code = 330;
UPDATE `disziplin_fr` SET Name = 'Triple', Kurzname = 'TRIPLE' WHERE Code = 340;       
                                                       
UPDATE `disziplin_fr` SET Name = 'Poids 7.26 kg', Kurzname = 'POIDS7.26' WHERE Code = 351;  
UPDATE `disziplin_fr` SET Name = 'Poids 6.00 kg', Kurzname = 'POIDS6.00' WHERE Code = 348;   
UPDATE `disziplin_fr` SET Name = 'Poids 5.00 kg', Kurzname = 'POIDS5.00' WHERE Code = 347;   
UPDATE `disziplin_fr` SET Name = 'Poids 4.00 kg', Kurzname = 'POIDS4.00' WHERE Code = 349;   
UPDATE `disziplin_fr` SET Name = 'Poids 3.00 kg', Kurzname = 'POIDS3.00' WHERE Code = 352;   
UPDATE `disziplin_fr` SET Name = 'Poids 2.50 kg', Kurzname = 'POIDS2.50' WHERE Code = 353;   
                                          
UPDATE `disziplin_fr` SET Name = 'Disque 2.00 kg', Kurzname = 'DISQUE2.00' WHERE Code = 361; 
UPDATE `disziplin_fr` SET Name = 'Disque 1.75 kg', Kurzname = 'DISQUE1.75' WHERE Code = 359; 
UPDATE `disziplin_fr` SET Name = 'Disque 1.50 kg', Kurzname = 'DISQUE1.50' WHERE Code = 358; 
UPDATE `disziplin_fr` SET Name = 'Disque 1.00 kg', Kurzname = 'DISQUE1.00' WHERE Code = 357; 
UPDATE `disziplin_fr` SET Name = 'Disque 0.75 kg', Kurzname = 'DISQUE0.75' WHERE Code = 356; 
                                         
UPDATE `disziplin_fr` SET Name = 'Marteau 7.26 kg', Kurzname = ' MARTEAU7.26' WHERE Code = 381;    
UPDATE `disziplin_fr` SET Name = 'Marteau 6.00 kg', Kurzname = ' MARTEAU6.00' WHERE Code = 378;
UPDATE `disziplin_fr` SET Name = 'Marteau 5.00 kg', Kurzname = ' MARTEAU5.00' WHERE Code = 377;
UPDATE `disziplin_fr` SET Name = 'Marteau 4.00 kg', Kurzname = ' MARTEAU4.00' WHERE Code = 376;
UPDATE `disziplin_fr` SET Name = 'Marteau 3.00 kg', Kurzname = ' MARTEAU3.00' WHERE Code = 375;
                           
UPDATE `disziplin_fr` SET Name = 'Javelot 800 gr', Kurzname = 'JAVELOT800' WHERE Code = 391;
UPDATE `disziplin_fr` SET Name = 'Javelot 700 gr', Kurzname = 'JAVELOT700' WHERE Code = 389; 
UPDATE `disziplin_fr` SET Name = 'Javelot 600 gr', Kurzname = 'JAVELOT600' WHERE Code = 388; 
UPDATE `disziplin_fr` SET Name = 'Javelot 400 gr', Kurzname = 'JAVELOT400' WHERE Code = 387; 
                              
UPDATE `disziplin_fr` SET Name = 'Balle 200 gr', Kurzname = 'BALLE200' WHERE Code = 386; 
                          
UPDATE `disziplin_fr` SET Name = 'Pentathlon hall', Kurzname = '5ATHLON_H' WHERE Code = 394; 
UPDATE `disziplin_fr` SET Name = 'Pentathlon hall U18 W', Kurzname = '5ATHLON_H_U18w' WHERE Code = 395;                                                                             
UPDATE `disziplin_fr` SET Name = 'Heptathlon hall', Kurzname = '7ATHLON_H' WHERE Code = 396;                                                                             
UPDATE `disziplin_fr` SET Name = 'Heptathlon hall U20 M', Kurzname = '7ATHLON_H_U20M' WHERE Code = 397;                                                                             
UPDATE `disziplin_fr` SET Name = 'Heptathlon hall U18 M', Kurzname = '7ATHLON_H_U18M' WHERE Code = 398;                                                                             
UPDATE `disziplin_fr` SET Name = 'Décathlon', Kurzname = '10ATHLON' WHERE Code = 410;                                                                             
UPDATE `disziplin_fr` SET Name = 'Décathlon U20 M', Kurzname = '10ATHLON_U20M' WHERE Code = 411;                                                                             
UPDATE `disziplin_fr` SET Name = 'Décathlon U18 M', Kurzname = '10ATHLON_U18M' WHERE Code = 412;                                                                             
UPDATE `disziplin_fr` SET Name = 'Décathlon W', Kurzname = '10ATHLON_W' WHERE Code = 413;
UPDATE `disziplin_fr` SET Name = 'Heptathlon', Kurzname = '7ATHLON' WHERE Code = 400;                                                                             
UPDATE `disziplin_fr` SET Name = 'Heptathlon U18 W', Kurzname = '7ATHLON_U18W' WHERE Code = 401;                                                                             
UPDATE `disziplin_fr` SET Name = 'Hexathlon U16 M', Kurzname = '6ATHLON_U16M' WHERE Code = 402;                                                                             
UPDATE `disziplin_fr` SET Name = 'Pentathlon U16 W', Kurzname = '5ATHLON_U16W' WHERE Code = 399;  
                           
UPDATE `disziplin_fr` SET Name = 'Balle 80 gr', Kurzname = 'BALLE80' WHERE Code = 385; 
UPDATE `disziplin_fr` SET Name = '400 m haies 76.2' WHERE Code = 298; 
                                                                                                                                                                                                                                                                   
UPDATE `disziplin_fr` SET Name = '50 m haies 68.6' WHERE Code = 237;  
UPDATE `disziplin_fr` SET Name = '60 m haies 68.6' WHERE Code = 257;  
UPDATE `disziplin_fr` SET Name = '80 m haies 84.0' WHERE Code = 260;  
UPDATE `disziplin_fr` SET Name = '80 m haies 68.6' WHERE Code = 262;  
UPDATE `disziplin_fr` SET Name = '300 m haies 68.6' WHERE Code = 292;  
                                                                                                      
UPDATE `disziplin_fr` SET Name = 'Javelot 500 gr', Kurzname = 'JAVELOT500' WHERE Code = 390;                                                        
UPDATE `disziplin_fr` SET Name = 'Pentathlon M', Kurzname = '5ATHLON_M' WHERE Code = 392; 
UPDATE `disziplin_fr` SET Name = 'Pentathlon U20 M', Kurzname = '5ATHLON_U20M' WHERE Code = 393;             
UPDATE `disziplin_fr` SET Name = 'Pentathlon U18 M', Kurzname = '5ATHLON_U18M' WHERE Code = 405;             
UPDATE `disziplin_fr` SET Name = 'Pentathlon F', Kurzname = '5ATHLON_F' WHERE Code = 416;             
UPDATE `disziplin_fr` SET Name = 'Pentathlon U20 F', Kurzname = '5ATHLON_U20F' WHERE Code = 417;             
UPDATE `disziplin_fr` SET Name = 'Pentathlon U18 F', Kurzname = '5ATHLON_U18F' WHERE Code = 418;                  
                               
UPDATE `disziplin_fr` SET Name = 'Décathlon CM', Kurzname = '10ATHLON_CM' WHERE Code = 414;                                                                                                                          

UPDATE `disziplin_fr` SET Name = '...cours', Kurzname = '...COURS' WHERE Code = 796;                                                                                                                          
UPDATE `disziplin_fr` SET Name = '...longueur', Kurzname = '...LONGUEUR' WHERE Code = 797;
UPDATE `disziplin_fr` SET Name = '...lancer', Kurzname = '...LANCER' WHERE Code = 798;
UPDATE `disziplin_fr` SET Name = '...athlon', Kurzname = '...ATHLON' WHERE Code = 799;  

UPDATE `disziplin_fr` SET Name = 'Longueur (zone)', Kurzname = 'LONGUEUR Z' WHERE Code = 331;                       
                        
                    
                   
-- 
-- disciplines it
-- 
UPDATE `disziplin_it` SET Name = 'UBS Kids Cup', Kurzname = 'UKC' WHERE Code = 403;    
UPDATE disziplin_it SET Staffellaeufer = '3' WHERE Code = '602'; 
DELETE FROM `disziplin_it` WHERE Code = 404;      
UPDATE `disziplin_it` SET Name = 'Zehnkampf W' WHERE Code = 413;   
DELETE FROM `disziplin_it` WHERE Code = 186;   
UPDATE `disziplin_it` SET Anzeige = 440, Kurzname = '10KM' WHERE Code = 491;      
                        
UPDATE `disziplin_it` SET Name = '1 mile', Kurzname = '1MILE' WHERE Code = 120;  
UPDATE `disziplin_it` SET Name = '1 ora', Kurzname = '1ORA' WHERE Code = 182;   
UPDATE `disziplin_it` SET Name = 'Mezza maratona', Kurzname = 'MEZZA MARA' WHERE Code = 190;  
UPDATE `disziplin_it` SET Name = 'Maratona', Kurzname = 'MARATONA' WHERE Code = 200;         
                               
UPDATE `disziplin_it` SET Name = '50 m ostacoli 106.7' WHERE Code = 232; 
UPDATE `disziplin_it` SET Name = '50 m ostacoli 99.1' WHERE Code = 233;  
UPDATE `disziplin_it` SET Name = '50 m ostacoli 91.4' WHERE Code = 234;  
UPDATE `disziplin_it` SET Name = '50 m ostacoli 84.0' WHERE Code = 235;  
UPDATE `disziplin_it` SET Name = '50 m ostacoli 76.2' WHERE Code = 236;  
UPDATE `disziplin_it` SET Name = '60 m ostacoli 106.7' WHERE Code = 252;  
UPDATE `disziplin_it` SET Name = '60 m ostacoli 99.1' WHERE Code = 253; 
UPDATE `disziplin_it` SET Name = '60 m ostacoli 91.4' WHERE Code = 254; 
UPDATE `disziplin_it` SET Name = '60 m ostacoli 84.0' WHERE Code = 255;  
UPDATE `disziplin_it` SET Name = '60 m ostacoli 76.2' WHERE Code = 256;   
UPDATE `disziplin_it` SET Name = '80 m ostacoli 76.2' WHERE Code = 258;  
UPDATE `disziplin_it` SET Name = '100 m ostacoli 84.0' WHERE Code = 261; 
UPDATE `disziplin_it` SET Name = '100 m ostacoli 76.2' WHERE Code = 259; 
UPDATE `disziplin_it` SET Name = '110 m ostacoli 106.7' WHERE Code = 271;
UPDATE `disziplin_it` SET Name = '110 m ostacoli 99.1' WHERE Code = 269;                             
UPDATE `disziplin_it` SET Name = '110 m ostacoli 91.4' WHERE Code = 268;      
UPDATE `disziplin_it` SET Name = '200 m ostacoli' WHERE Code = 280; 
UPDATE `disziplin_it` SET Name = '300 m ostacoli 91.4' WHERE Code = 289;  
UPDATE `disziplin_it` SET Name = '300 m ostacoli 84.0' WHERE Code = 290; 
UPDATE `disziplin_it` SET Name = '300 m ostacoli 76.2' WHERE Code = 291;      
UPDATE `disziplin_it` SET Name = '400 m ostacoli 91.4' WHERE Code = 301;
UPDATE `disziplin_it` SET Name = '400 m ostacoli 76.2' WHERE Code = 298;  
                           
UPDATE `disziplin_fr` SET Name = 'Decathlon W' WHERE Code = 413;                                                                                                                                                                                                                                    
                                
UPDATE `disziplin_it` SET Name = '5x libero', Kurzname = '5XLIBERO' WHERE Code = 497; 
UPDATE `disziplin_it` SET Name = '6x libero', Kurzname = '6XLIBERO' WHERE Code = 499;  
                           
UPDATE `disziplin_it` SET Name = 'Alto', Kurzname =  'ALTO' WHERE Code = 310;
UPDATE `disziplin_it` SET Name = 'Asta', Kurzname = 'ASTA' WHERE Code = 320; 
UPDATE `disziplin_it` SET Name = 'Lungo', Kurzname = 'LUNGO' WHERE Code = 330;
UPDATE `disziplin_it` SET Name = 'Triplo', Kurzname = 'TRIPLO' WHERE Code = 340;   
                                                       
UPDATE `disziplin_it` SET Name = 'Peso 7.26 kg', Kurzname = 'PESO7.26' WHERE Code = 351;  
UPDATE `disziplin_it` SET Name = 'Peso 6.00 kg', Kurzname = 'PESO6.00' WHERE Code = 348;   
UPDATE `disziplin_it` SET Name = 'Peso 5.00 kg', Kurzname = 'PESO5.00' WHERE Code = 347;   
UPDATE `disziplin_it` SET Name = 'Peso 4.00 kg', Kurzname = 'PESO4.00' WHERE Code = 349;   
UPDATE `disziplin_it` SET Name = 'Peso 3.00 kg', Kurzname = 'PESO3.00' WHERE Code = 352;   
UPDATE `disziplin_it` SET Name = 'Peso 2.50 kg', Kurzname = 'PESO2.50' WHERE Code = 353;   
                           
UPDATE `disziplin_it` SET Name = 'Disco 2.00 kg', Kurzname = 'DISCO2.00' WHERE Code = 361; 
UPDATE `disziplin_it` SET Name = 'Disco 1.75 kg', Kurzname = 'DISCO1.75' WHERE Code = 359; 
UPDATE `disziplin_it` SET Name = 'Disco 1.50 kg', Kurzname = 'DISCO1.50' WHERE Code = 358; 
UPDATE `disziplin_it` SET Name = 'Disco 1.00 kg', Kurzname = 'DISCO1.00' WHERE Code = 357; 
UPDATE `disziplin_it` SET Name = 'Disco 0.75 kg', Kurzname = 'DISCO0.75' WHERE Code = 356; 
                           
UPDATE `disziplin_it` SET Name = 'Martello 7.26 kg', Kurzname = 'MARTELLO7.26' WHERE Code = 381;    
UPDATE `disziplin_it` SET Name = 'Martello 6.00 kg', Kurzname = 'MARTELLO6.00' WHERE Code = 378;
UPDATE `disziplin_it` SET Name = 'Martello 5.00 kg', Kurzname = 'MARTELLO5.00' WHERE Code = 377;
UPDATE `disziplin_it` SET Name = 'Martello 4.00 kg', Kurzname = 'MARTELLO4.00' WHERE Code = 376;
UPDATE `disziplin_it` SET Name = 'Martello 3.00 kg', Kurzname = 'MARTELLO3.00' WHERE Code = 375;
                           
UPDATE `disziplin_it` SET Name = 'Giavellotto 800 gr', Kurzname = 'GIAVELLOTTO800' WHERE Code = 391;
UPDATE `disziplin_it` SET Name = 'Giavellotto 700 gr', Kurzname = 'GIAVELLOTTO700' WHERE Code = 389; 
UPDATE `disziplin_it` SET Name = 'Giavellotto 600 gr', Kurzname = 'GIAVELLOTTO600' WHERE Code = 388; 
UPDATE `disziplin_it` SET Name = 'Giavellotto 400 gr', Kurzname = 'GIAVELLOTTO400' WHERE Code = 387; 
                              
UPDATE `disziplin_it` SET Name = 'Pallina 200 gr', Kurzname = 'PALLINO200' WHERE Code = 386; 
                          
UPDATE `disziplin_it` SET Name = 'Pentathlon hall', Kurzname = '5ATHLON_H' WHERE Code = 394; 
UPDATE `disziplin_it` SET Name = 'Pentathlon hall U18 W', Kurzname = '5ATHLON_H_U18w' WHERE Code = 395;                                                                             
UPDATE `disziplin_it` SET Name = 'Heptathlon hall', Kurzname = '7ATHLON_H' WHERE Code = 396;                                                                             
UPDATE `disziplin_it` SET Name = 'Heptathlon hall U20 M', Kurzname = '7ATHLON_H_U20M' WHERE Code = 397;                                                                             
UPDATE `disziplin_it` SET Name = 'Heptathlon hall U18 M', Kurzname = '7ATHLON_H_U18M' WHERE Code = 398;                                                                             
UPDATE `disziplin_it` SET Name = 'Decathlon', Kurzname = '10ATHLON' WHERE Code = 410;                                                                             
UPDATE `disziplin_it` SET Name = 'Decathlon U20 M', Kurzname = '10ATHLON_U20M' WHERE Code = 411;                                                                             
UPDATE `disziplin_it` SET Name = 'Decathlon U18 M', Kurzname = '10ATHLON_U18M' WHERE Code = 412;                                                                             
UPDATE `disziplin_it` SET Name = 'Decathlon W', Kurzname = '10ATHLON_W' WHERE Code = 413;
UPDATE `disziplin_it` SET Name = 'Heptathlon', Kurzname = '7ATHLON' WHERE Code = 400;                                                                             
UPDATE `disziplin_it` SET Name = 'Heptathlon U18 W', Kurzname = '7ATHLON_U18W' WHERE Code = 401;                                                                             
UPDATE `disziplin_it` SET Name = 'Hexathlon U16 M', Kurzname = '6ATHLON_U16M' WHERE Code = 402;                                                                             
UPDATE `disziplin_it` SET Name = 'Pentathlon U16 W', Kurzname = '5ATHLON_U16W' WHERE Code = 399;  
                           
UPDATE `disziplin_it` SET Name = 'Pallina 80 gr', Kurzname = 'PALLINO80' WHERE Code = 385; 
UPDATE `disziplin_it` SET Name = '400 m ostacoli 76.2' WHERE Code = 298; 
                           
UPDATE `disziplin_it` SET Name = '50 m ostacoli 68.6' WHERE Code = 237;  
UPDATE `disziplin_it` SET Name = '60 m ostacoli 68.6' WHERE Code = 257;  
UPDATE `disziplin_it` SET Name = '80 m ostacoli 84.0' WHERE Code = 260;  
UPDATE `disziplin_it` SET Name = '80 m ostacoli 68.6' WHERE Code = 262;  
UPDATE `disziplin_it` SET Name = '300 m ostacoli 68.6' WHERE Code = 292;  
                                                                                                      
UPDATE `disziplin_it` SET Name = 'Giavellotto 500 gr', Kurzname = 'GIAVELLOTTO500' WHERE Code = 390;                                                        
UPDATE `disziplin_it` SET Name = 'Pentathlon M', Kurzname = '5ATHLON_M' WHERE Code = 392; 
UPDATE `disziplin_it` SET Name = 'Pentathlon U20 M', Kurzname = '5ATHLON_U20M' WHERE Code = 393;             
UPDATE `disziplin_it` SET Name = 'Pentathlon U18 M', Kurzname = '5ATHLON_U18M' WHERE Code = 405;             
UPDATE `disziplin_it` SET Name = 'Pentathlon F', Kurzname = '5ATHLON_F' WHERE Code = 416;             
UPDATE `disziplin_it` SET Name = 'Pentathlon U20 F', Kurzname = '5ATHLON_U20F' WHERE Code = 417;             
UPDATE `disziplin_it` SET Name = 'Pentathlon U18 F', Kurzname = '5ATHLON_U18F' WHERE Code = 418;                  
                               
UPDATE `disziplin_it` SET Name = 'Decathlon CM', Kurzname = '10ATHLON_CM' WHERE Code = 414;                                                                                                                          

UPDATE `disziplin_it` SET Name = '...cours', Kurzname = '...COURS' WHERE Code = 796;                                                                                                                          
UPDATE `disziplin_it` SET Name = '...lungo', Kurzname = '...LUNGO' WHERE Code = 797;
UPDATE `disziplin_it` SET Name = '...lancer', Kurzname = '...LANCER' WHERE Code = 798;
UPDATE `disziplin_it` SET Name = '...athlon', Kurzname = '...ATHLON' WHERE Code = 799;       

UPDATE `disziplin_it` SET Name = 'Lungo (zone)', Kurzname = 'LUNGO Z' WHERE Code = 331;     
 
 
 
-- 
-- create language tables rundentyp
--         
 CREATE TABLE `rundentyp_fr` SELECT * FROM rundentyp;    
 CREATE TABLE `rundentyp_it` SELECT * FROM rundentyp;  
 RENAME TABLE `rundentyp` TO `rundentyp_de`; 
             
 DROP TABLE IF EXISTS `rundentyp`;       
 
-- 
-- rundentyp  fr  
--  
UPDATE `rundentyp_fr` SET Name = 'Eliminatoire' WHERE Typ = 'V'; 
UPDATE `rundentyp_fr` SET Name = 'Finale' WHERE Typ = 'F';
UPDATE `rundentyp_fr` SET Name = 'Second Tour' WHERE Typ = 'Z';
UPDATE `rundentyp_fr` SET Name = 'Qualification' WHERE Typ = 'Q';
UPDATE `rundentyp_fr` SET Name = 'Série' WHERE Typ = 'S';
UPDATE `rundentyp_fr` SET Name = 'Demi-finale' WHERE Typ = 'X';
UPDATE `rundentyp_fr` SET Name = 'Concour multiple' WHERE Typ = 'D';
UPDATE `rundentyp_fr` SET Name = '(sans)' WHERE Typ = '0';     
                   
 
-- 
-- rundentyp  it
--  
         
UPDATE `rundentyp_it` SET Name = 'Eliminatoria' WHERE Typ = 'V'; 
UPDATE `rundentyp_it` SET Name = 'Finale' WHERE Typ = 'F';
UPDATE `rundentyp_it` SET Name = 'Secondo tour' WHERE Typ = 'Z';
UPDATE `rundentyp_it` SET Name = 'Qualificazione' WHERE Typ = 'Q';
UPDATE `rundentyp_it` SET Name = 'Serie' WHERE Typ = 'S';
UPDATE `rundentyp_it` SET Name = 'Semifinale' WHERE Typ = 'X';
UPDATE `rundentyp_it` SET Name = 'Gara multipla' WHERE Typ = 'D';
UPDATE `rundentyp_it` SET Name = '(senza)' WHERE Typ = '0';     
                                                                
 


INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
 ("Attribuer les dossards", "Il est nouvellement possible d’indiquer des dossards pour les athlètes restant. Les dossards peuvent en plus être attribués par sexe.", "y", 0, 50, 200, 250, "meeting_entrylist", "fr", "FFAA00", "FFCC00"),                                                                     
  ('Startnummern zuordnen', 'Neu kann für restliche Athleten Nummern vergeben werden. Zusätzlich besteht die Möglichkeit, die Nummern nach Geschlecht zuzuordnen.', 'y', 0, 50, 200, 250, 'meeting_entrylist', 'de', 'FFAA00', 'FFCC00'); 
 
INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
 ("Administration des disciplines", "En même temps, modification globale d’un type.", "y", 30, 330, 200, 250, "admin_disciplines", "fr", "FFAA00", "FFCC00"), 
  ('Administration Disziplinen', 'Pauschale Änderung für einen Typ gleichzeitig.', 'y', 30, 330, 200, 250, 'admin_disciplines', 'de', 'FFAA00', 'FFCC00'); 
 
INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Liste de résultats complète', 'Avec la liste de résultats il est en plus possible de sélectionner une liste de résultats complète, attachée à la fin.', 'y', 230, 220, 200, 250, 'event_rankinglists', 'fr', 'FFAA00', 'FFCC00'),                                                     
  ('Rangliste über alle Serien', 'Zusätzlich kann zur Rangliste eine Gesamtrangliste gewählt werden, die hinten angehängt wird.', 'y', 230, 220, 200, 250, 'event_rankinglists', 'de', 'FFAA00', 'FFCC00'); 
 
