ALTER TABLE serienstart ADD COLUMN Bemerkung char(5) NOT NULL AFTER RundeZusammen;
ALTER TABLE runde ADD COLUMN nurBestesResultat ENUM('y','n') NOT NULL default 'n' AFTER xWettkampf;
ALTER TABLE meeting ADD COLUMN AutoRangieren ENUM('y','n') NOT NULL default 'n' AFTER Saison;
ALTER TABLE kategorie ADD COLUMN aktiv ENUM('y','n') DEFAULT 'y' NOT NULL AFTER Geschlecht;    
ALTER TABLE disziplin ADD COLUMN aktiv ENUM('y','n') DEFAULT 'y' NOT NULL AFTER xOMEGA_Typ; 
ALTER TABLE wettkampf ADD COLUMN TypAenderung varchar(50) NOT NULL AFTER OnlineId;  
ALTER TABLE teamsm ADD COLUMN Startnummer int(11) NOT NULL default 0 AFTER xMeeting;  
ALTER TABLE start CHANGE BaseEffort BaseEffort ENUM('y','n') DEFAULT 'y' NOT NULL;    

INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Courses de finale', 'Pour les courses de finale, les meilleurs athlètes sont automatiquement répartis dans la dernière série. Les séries sont dénommées A, B, C etc (A pour la série la plus rapide).', 'y', 110, 130, 120, 200, 'event_heats', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Finalläufe', 'Bei Finalläufen werden die besten Athleten automatisch in die letzte Serie eingeteilt. Die Serien werden mit A, B, C usw. bezeichnet (A für die schnellste Serie).', 'y', 110, 130, 120, 200, 'event_heats', 'de', 'FFAA00', 'FFCC00');
  
  
 INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Listes de sélection pour les relais ', 'La liste “autres athlètes de la société“ montre tous les autres athlètes de la même société ainsi que nouvellement les membres des CoA.<br>La nouvelle liste “selon équipe“ montre tous les membres de l&lsquo;équipe de relais.', 'y', 250, 310, 120, 250, 'meeting_relay', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Staffel Auswahllisten', 'Die Liste "andere Vereinsathleten" zeigt alle weiteren Athleten desselben Vereins sowie neu die Mitglieder der LG &lsquo;s.<br><br>Neu ist die Liste “nach Mannschaft“, die alle Mitglieder der Mannschaft dieser Staffel zeigt.'
   , 'y', 250, 310, 120, 250, 'meeting_relay', 'de', 'FFAA00', 'FFCC00');
  
  INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Liste de sélection CS Team', 'La liste “autres athlètes de la société“ montre tous les autres athlètes de la même société ainsi que nouvellement les membres des CoA.', 'y', 180, 200, 120, 250, 'meeting_teamsm', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Team SM Auswahlliste', 'Die Liste "andere Vereinsathleten" zeigt alle weiteren Athleten desselben Vereins sowie neu die Mitglieder der LG&lsquo;s.' , 'y', 180, 200, 120, 250, 'meeting_teamsm', 'de', 'FFAA00', 'FFCC00');
  
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Augmenter les points par rang ', 'Il est maintenant aussi possible d’augmenter les points par rang, par ex. en entrant 1+1. ', 'y', 90, 90, 120, 200, 'meeting_definition_event_add', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Rangpunkte erhöhen ', 'Rangpunkte können nun auch erhöht werden,  z.B. durch Eingabe von 1+1. ', 'y', 90, 90, 120, 200, 'meeting_definition_event_add', 'de', 'FFAA00', 'FFCC00');
  
  INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Seulement le meilleur résultat ', 'Pour n’enregistrer que le meilleur résultat dans les disciplines techniques, il est possible de cocher “Seulement le meilleur résultat“. Cela facilite l’enregistrement, car le curseur saute directement sur la première case du prochain athlète.', 'y', 210, 180, 120, 250, 'print_contest', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Nur bestes Resultat', 'Um bei den technischen Disziplinen nur den Bestversuch zu erfassen, kann das Häkchen “Nur Bestes Resultat“ gesetzt werden. Dies erleichtert die Eingabe, indem der Cursor direkt auf das erste Feld des nächsten Athleten springt.', 'y', 210, 180, 120, 250, 'print_contest', 'de', 'FFAA00', 'FFCC00');
  
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Doubles définitions des tours ', 'Il est nouvellement possible d’indiquer deux fois le même type de tour (pour „série“ et „sans“).', 'y', 210, 120, 120, 200, 'meeting_definition_event_add', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Doppelte Rundendefinitionen', 'Neu besteht die Möglichkeit zwei Mal den gleichen Rundentyp (für "Serie" und "ohne") anzugeben. ', 'y', 210, 120, 120, 200, 'meeting_definition_event_add', 'de', 'FFAA00', 'FFCC00'); 
  
  INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Créer CSI', 'Lors de la création d’un concours CSI les disciplines prescrites sont nouvellement automatiquement attribuées.<br/><br/>Cliquez sur le bouton "Créer CSI" pour appeler cette fonction.<br/>En sélectionnant une catégorie CSI, les horaires fixes sont directement insérés, s’ils existent.<br/><br/><b>ATTENTION: Pour les disciplines marquées en rouge, le temps zéro peut être entré, ce qui implique une calculation subséquente des autres heures de départ.</br>', 'y', 90, 90, 120, 350, 'meeting_definition_event_add', 'fr', 'FFAA00', 'FFCC00'),
  
  ('SVM erstellen', 'Neu werden beim Erstellen eines SVM Wettkampfes die vorgegebenen Disziplinen automatisch zugeordnet.<br/><br/>\r\n\r\nKlicken Sie auf die Schaltfläche \"SVM erstellen\" um diese Funktion aufzurufen.<br/><br/>\r\Durch die Auswahl einer SVM Kategorie werden die fixen Zeitpläne, falls vorhanden, direkt eingefügt.<br/><br/>\r\n\r\n<b>ACHTUNG: Bei den rot markierten Disziplinen kann die Nullzeit eingegeben werden, was eine Nachberechnung der restlichen Startzeiten zur Folge hat.</b>', 'y', 90, 90, 120, 350, 'meeting_definition_event_add', 'de', 'FFAA00', 'FFCC00');
  
  
 INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Classement automatique ', 'Une fois les résultats de tous les athlètes enregistrés, le classement est automatiquement terminé, dans la mesure où la coche „classement automatique“ est mise.', 'y', 100, 190, 120, 250, 'meeting_timing', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Automatische Rangierung ', 'Nachdem die Resultate aller Athleten eingelesen sind, wird die Rangierung automatisch abgeschlossen, sofern das Häkchen für “Automatisch rangieren“ gesetzt ist.', 'y', 100, 190, 120, 250, 'meeting_timing', 'de', 'FFAA00', 'FFCC00');
  
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Catégories', 'Catégories peuvent être actives resp. inactives. Dans la liste sélectionné n’apparaissent alors que celles qui sont actives.', 'y', 80, 610, 120, 240, 'admin_categories', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Kategorien ', 'Kategorien können aktiv bzw. inaktiv gesetzt werden. Somit erscheinen in den Auswahllisten nur noch die Aktiven.', 'y', 80, 610, 120, 240, 'admin_categories', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Disciplines', 'Disciplines peuvent être actives resp. inactives. Dans la liste sélectionné n’apparaissent alors que celles qui sont actives.', 'y', 80, 610, 120, 240, 'admin_disciplines', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Diszipline ', 'Diszipline können aktiv bzw. inaktiv gesetzt werden. Somit erscheinen in den Auswahllisten nur noch die Aktiven.', 'y', 80, 610, 120, 240, 'admin_disciplines', 'de', 'FFAA00', 'FFCC00');
  
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Seconde société ', 'Pour les athlètes avec licences doubles, la seconde société ou la CoA est indiquée.', 'y', 280, 190, 120, 250, 'meeting_entry_add', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Zweitverein', 'Bei Athleten mit Doppellizenzen wird der Zweitverein oder die LG angezeigt.', 'y', 280, 190, 120, 250, 'meeting_entry_add', 'de', 'FFAA00', 'FFCC00');
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Transmission des résultats ', 'Avant la transmission des résultats, il faut contrôler s&lsquo;il y a des résultats qui n&lsquo;ont pas été classés et font paraître un message d&lsquo;erreur correspondant.', 'y', 490, 790, 120, 250, 'admin', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Resultate Übermittlung', 'Vor der Resultate Übermittlung wird überprüft, ob Resultate vorhanden sind, die nicht rangiert wurden und eine entsprechende Fehlermeldung herausgegeben.', 'y', 490, 790, 120, 250, 'admin', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Équipes CS Team ', 'Des numéros peuvent maintenant être attribués aux équipes CS Team. De plus les fonctions d’impression ont été développées.', 'y', 40, 90, 120, 250, 'meeting_teamsms', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Team SM Mannschaften', 'Den Team SM Mannschaften können nun Nummern zugeteilt werden. Zusätzlich wurden die Druckfunktionen erweitert.', 'y', 40, 90, 120, 250, 'meeting_teamsms', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Statistique', 'Dans la statistique aperçu finances d&lsquo;inscription / finances de garantie par société, la taxe à la fédération est en plus notée par catégorie.', 'y', 90, 210, 120, 250, 'Statistics', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Statistik', 'Den Team SM Mannschaften können nun Nummern zugeteilt werden. Zusätzlich wurden die Druckfunktionen erweitert.', 'y', 90, 210, 120, 250, 'Statistics', 'de', 'FFAA00', 'FFCC00');
   
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Groupes de concours multiple avec lettres ', 'La répartition des groupes en concours multiple fonctionne maintenant aussi avec des lettres.', 'y', 90, 180, 120, 250, 'meeting_definition_category', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Mehrkampf Gruppen mit Buchstaben', 'Die Gruppenzuteilungen im Mehrkampf funktionieren nun auch mit Buchstaben.', 'y', 90, 180, 120, 250, 'meeting_definition_category', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Chronométrage', 'Si on sélectionne „chronométrage automatique“, le chronométrage automatique est mis pour toutes les courses.', 'y', 90, 220, 120, 250, 'meeting_definitions', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Zeitmessung', 'Wird „Zeitmessung automatisch“ gewählt, wird bei allen Läufen die Zeitmessung automatisch gesetzt.', 'y', 90, 220, 120, 250, 'meeting_definitions', 'de', 'FFAA00', 'FFCC00');
  
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Concours multiple  - Concours simple ', 'En concours multiple il est nouvellement possible de définir ultérieurement une certaine discipline comme concours simple. Celui-ci ne compte ensuite plus pour le concours multiple et est noté sur la liste de résultat comme concours simple.', 'y', 50, 230, 120, 250, 'meeting_definition_category', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Mehrkampf - Einzelkampf', 'Neu kann bei einem Mehrkampf eine einzelne Disziplin nachträglich als Einzelwettkampf definiert werden. Diese zählt danach nicht mehr zum Mehrkampf und wird auf der Rangliste als Einzelwettkampf ausgegeben.', 'y', 50, 230, 120, 250, 'meeting_definition_category', 'de', 'FFAA00', 'FFCC00');
  
   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Athletic Cup gaz naturel', 'Si un concours est définit comme „Athletic Cup gaz naturel“, les disciplines fixées sont automatiquement attribuées à chaque catégorie.', 'y', 130, 200, 120, 250, 'meeting_definition_category', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Erdgas Athletic Cup', 'Wird ein Wettkampf als „Erdgas Athletic Cup“ definiert, werden die vorgegebenen Disziplinen der jeweiligen Kategorien automatisch zugeordnet.', 'y', 130, 200, 120, 250, 'meeting_definition_category', 'de', 'FFAA00', 'FFCC00');