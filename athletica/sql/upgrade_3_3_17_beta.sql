ALTER TABLE serienstart ADD COLUMN Bemerkung char(5) NOT NULL AFTER RundeZusammen;
ALTER TABLE runde ADD COLUMN nurBestesResultat ENUM('y','n') NOT NULL default 'n' AFTER xWettkampf;
ALTER TABLE meeting ADD COLUMN AutoRangieren ENUM('y','n') NOT NULL default 'n' AFTER Saison;
ALTER TABLE kategorie ADD COLUMN aktiv ENUM('y','n') DEFAULT 'y' NOT NULL AFTER Geschlecht;    
ALTER TABLE disziplin ADD COLUMN aktiv ENUM('y','n') DEFAULT 'y' NOT NULL AFTER xOMEGA_Typ; 
ALTER TABLE wettkampf ADD COLUMN TypAenderung varchar(50) NOT NULL AFTER OnlineId;  
ALTER TABLE teamsm ADD COLUMN Startnummer int(11) NOT NULL default 0 AFTER xMeeting;  
ALTER TABLE start CHANGE BaseEffort BaseEffort ENUM('y','n') DEFAULT 'y' NOT NULL;    

INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Courses de finale', 'Pour les courses de finale, les meilleurs athl�tes sont automatiquement r�partis dans la derni�re s�rie. Les s�ries sont d�nomm�es A, B, C etc (A pour la s�rie la plus rapide).', 'y', 110, 130, 120, 200, 'event_heats', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Finall�ufe', 'Bei Finall�ufen werden die besten Athleten automatisch in die letzte Serie eingeteilt. Die Serien werden mit A, B, C usw. bezeichnet (A f�r die schnellste Serie).', 'y', 110, 130, 120, 200, 'event_heats', 'de', 'FFAA00', 'FFCC00');
  
  
 INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Listes de s�lection pour les relais ', 'La liste �autres athl�tes de la soci�t� montre tous les autres athl�tes de la m�me soci�t� ainsi que nouvellement les membres des CoA.<br>La nouvelle liste �selon �quipe� montre tous les membres de l&lsquo;�quipe de relais.', 'y', 250, 310, 120, 250, 'meeting_relay', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Staffel Auswahllisten', 'Die Liste "andere Vereinsathleten" zeigt alle weiteren Athleten desselben Vereins sowie neu die Mitglieder der LG &lsquo;s.<br><br>Neu ist die Liste �nach Mannschaft�, die alle Mitglieder der Mannschaft dieser Staffel zeigt.'
   , 'y', 250, 310, 120, 250, 'meeting_relay', 'de', 'FFAA00', 'FFCC00');
  
  INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Liste de s�lection CS Team', 'La liste �autres athl�tes de la soci�t� montre tous les autres athl�tes de la m�me soci�t� ainsi que nouvellement les membres des CoA.', 'y', 180, 200, 120, 250, 'meeting_teamsm', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Team SM Auswahlliste', 'Die Liste "andere Vereinsathleten" zeigt alle weiteren Athleten desselben Vereins sowie neu die Mitglieder der LG&lsquo;s.' , 'y', 180, 200, 120, 250, 'meeting_teamsm', 'de', 'FFAA00', 'FFCC00');
  
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Augmenter les points par rang ', 'Il est maintenant aussi possible d�augmenter les points par rang, par ex. en entrant 1+1. ', 'y', 90, 90, 120, 200, 'meeting_definition_event_add', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Rangpunkte erh�hen ', 'Rangpunkte k�nnen nun auch erh�ht werden,  z.B. durch Eingabe von 1+1. ', 'y', 90, 90, 120, 200, 'meeting_definition_event_add', 'de', 'FFAA00', 'FFCC00');
  
  INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Seulement le meilleur r�sultat ', 'Pour n�enregistrer que le meilleur r�sultat dans les disciplines techniques, il est possible de cocher �Seulement le meilleur r�sultat�. Cela facilite l�enregistrement, car le curseur saute directement sur la premi�re case du prochain athl�te.', 'y', 210, 180, 120, 250, 'print_contest', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Nur bestes Resultat', 'Um bei den technischen Disziplinen nur den Bestversuch zu erfassen, kann das H�kchen �Nur Bestes Resultat� gesetzt werden. Dies erleichtert die Eingabe, indem der Cursor direkt auf das erste Feld des n�chsten Athleten springt.', 'y', 210, 180, 120, 250, 'print_contest', 'de', 'FFAA00', 'FFCC00');
  
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Doubles d�finitions des tours ', 'Il est nouvellement possible d�indiquer deux fois le m�me type de tour (pour �s�rie� et �sans�).', 'y', 210, 120, 120, 200, 'meeting_definition_event_add', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Doppelte Rundendefinitionen', 'Neu besteht die M�glichkeit zwei Mal den gleichen Rundentyp (f�r "Serie" und "ohne") anzugeben. ', 'y', 210, 120, 120, 200, 'meeting_definition_event_add', 'de', 'FFAA00', 'FFCC00'); 
  
  INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Cr�er CSI', 'Lors de la cr�ation d�un concours CSI les disciplines prescrites sont nouvellement automatiquement attribu�es.<br/><br/>Cliquez sur le bouton "Cr�er CSI" pour appeler cette fonction.<br/>En s�lectionnant une cat�gorie CSI, les horaires fixes sont directement ins�r�s, s�ils existent.<br/><br/><b>ATTENTION: Pour les disciplines marqu�es en rouge, le temps z�ro peut �tre entr�, ce qui implique une calculation subs�quente des autres heures de d�part.</br>', 'y', 90, 90, 120, 350, 'meeting_definition_event_add', 'fr', 'FFAA00', 'FFCC00'),
  
  ('SVM erstellen', 'Neu werden beim Erstellen eines SVM Wettkampfes die vorgegebenen Disziplinen automatisch zugeordnet.<br/><br/>\r\n\r\nKlicken Sie auf die Schaltfl�che \"SVM erstellen\" um diese Funktion aufzurufen.<br/><br/>\r\Durch die Auswahl einer SVM Kategorie werden die fixen Zeitpl�ne, falls vorhanden, direkt eingef�gt.<br/><br/>\r\n\r\n<b>ACHTUNG: Bei den rot markierten Disziplinen kann die Nullzeit eingegeben werden, was eine Nachberechnung der restlichen Startzeiten zur Folge hat.</b>', 'y', 90, 90, 120, 350, 'meeting_definition_event_add', 'de', 'FFAA00', 'FFCC00');
  
  
 INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Classement automatique ', 'Une fois les r�sultats de tous les athl�tes enregistr�s, le classement est automatiquement termin�, dans la mesure o� la coche �classement automatique� est mise.', 'y', 100, 190, 120, 250, 'meeting_timing', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Automatische Rangierung ', 'Nachdem die Resultate aller Athleten eingelesen sind, wird die Rangierung automatisch abgeschlossen, sofern das H�kchen f�r �Automatisch rangieren� gesetzt ist.', 'y', 100, 190, 120, 250, 'meeting_timing', 'de', 'FFAA00', 'FFCC00');
  
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Cat�gories', 'Cat�gories peuvent �tre actives resp. inactives. Dans la liste s�lectionn� n�apparaissent alors que celles qui sont actives.', 'y', 80, 610, 120, 240, 'admin_categories', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Kategorien ', 'Kategorien k�nnen aktiv bzw. inaktiv gesetzt werden. Somit erscheinen in den Auswahllisten nur noch die Aktiven.', 'y', 80, 610, 120, 240, 'admin_categories', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Disciplines', 'Disciplines peuvent �tre actives resp. inactives. Dans la liste s�lectionn� n�apparaissent alors que celles qui sont actives.', 'y', 80, 610, 120, 240, 'admin_disciplines', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Diszipline ', 'Diszipline k�nnen aktiv bzw. inaktiv gesetzt werden. Somit erscheinen in den Auswahllisten nur noch die Aktiven.', 'y', 80, 610, 120, 240, 'admin_disciplines', 'de', 'FFAA00', 'FFCC00');
  
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Seconde soci�t� ', 'Pour les athl�tes avec licences doubles, la seconde soci�t� ou la CoA est indiqu�e.', 'y', 280, 190, 120, 250, 'meeting_entry_add', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Zweitverein', 'Bei Athleten mit Doppellizenzen wird der Zweitverein oder die LG angezeigt.', 'y', 280, 190, 120, 250, 'meeting_entry_add', 'de', 'FFAA00', 'FFCC00');
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Transmission des r�sultats ', 'Avant la transmission des r�sultats, il faut contr�ler s&lsquo;il y a des r�sultats qui n&lsquo;ont pas �t� class�s et font para�tre un message d&lsquo;erreur correspondant.', 'y', 490, 790, 120, 250, 'admin', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Resultate �bermittlung', 'Vor der Resultate �bermittlung wird �berpr�ft, ob Resultate vorhanden sind, die nicht rangiert wurden und eine entsprechende Fehlermeldung herausgegeben.', 'y', 490, 790, 120, 250, 'admin', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('�quipes CS Team ', 'Des num�ros peuvent maintenant �tre attribu�s aux �quipes CS Team. De plus les fonctions d�impression ont �t� d�velopp�es.', 'y', 40, 90, 120, 250, 'meeting_teamsms', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Team SM Mannschaften', 'Den Team SM Mannschaften k�nnen nun Nummern zugeteilt werden. Zus�tzlich wurden die Druckfunktionen erweitert.', 'y', 40, 90, 120, 250, 'meeting_teamsms', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Statistique', 'Dans la statistique aper�u finances d&lsquo;inscription / finances de garantie par soci�t�, la taxe � la f�d�ration est en plus not�e par cat�gorie.', 'y', 90, 210, 120, 250, 'Statistics', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Statistik', 'Den Team SM Mannschaften k�nnen nun Nummern zugeteilt werden. Zus�tzlich wurden die Druckfunktionen erweitert.', 'y', 90, 210, 120, 250, 'Statistics', 'de', 'FFAA00', 'FFCC00');
   
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Groupes de concours multiple avec lettres ', 'La r�partition des groupes en concours multiple fonctionne maintenant aussi avec des lettres.', 'y', 90, 180, 120, 250, 'meeting_definition_category', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Mehrkampf Gruppen mit Buchstaben', 'Die Gruppenzuteilungen im Mehrkampf funktionieren nun auch mit Buchstaben.', 'y', 90, 180, 120, 250, 'meeting_definition_category', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Chronom�trage', 'Si on s�lectionne �chronom�trage automatique�, le chronom�trage automatique est mis pour toutes les courses.', 'y', 90, 220, 120, 250, 'meeting_definitions', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Zeitmessung', 'Wird �Zeitmessung automatisch� gew�hlt, wird bei allen L�ufen die Zeitmessung automatisch gesetzt.', 'y', 90, 220, 120, 250, 'meeting_definitions', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Concours multiple  - Concours simple ', 'En concours multiple il est nouvellement possible de d�finir ult�rieurement une certaine discipline comme concours simple. Celui-ci ne compte ensuite plus pour le concours multiple et est not� sur la liste de r�sultat comme concours simple.', 'y', 50, 230, 120, 250, 'meeting_definition_category', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Mehrkampf - Einzelkampf', 'Neu kann bei einem Mehrkampf eine einzelne Disziplin nachtr�glich als Einzelwettkampf definiert werden. Diese z�hlt danach nicht mehr zum Mehrkampf und wird auf der Rangliste als Einzelwettkampf ausgegeben.', 'y', 50, 230, 120, 250, 'meeting_definition_category', 'de', 'FFAA00', 'FFCC00');
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Athletic Cup gaz naturel', 'Si un concours est d�finit comme �Athletic Cup gaz naturel�, les disciplines fix�es sont automatiquement attribu�es � chaque cat�gorie.', 'y', 130, 200, 120, 250, 'meeting_definition_category', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Erdgas Athletic Cup', 'Wird ein Wettkampf als �Erdgas Athletic Cup� definiert, werden die vorgegebenen Disziplinen der jeweiligen Kategorien automatisch zugeordnet.', 'y', 130, 200, 120, 250, 'meeting_definition_category', 'de', 'FFAA00', 'FFCC00');