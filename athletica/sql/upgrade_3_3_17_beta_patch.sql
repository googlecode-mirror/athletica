
ALTER TABLE athlet ADD COLUMN Manuell int(1) NOT NULL default 0 AFTER Lizenztyp;  



   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Changements manuels des données d’athlète ', 'Sur demande, les changements manuels des données (nom, prénom et société) ne seront plus reportés avec la mise à jour des données de base et l&lsquo;ajustement de meeting.', 'y', 290, 830, 120, 250, 'admin', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Manuelle Änderungen Athletendaten', 'Manuelle Änderungen der Athletendaten (Name, Vorname und Verein) werden mit dem Update der Stammdaten und mit dem Meetingabgleich auf Wunsch nicht mehr überschrieben.', 'y', 290, 830, 120, 250, 'admin', 'de', 'FFAA00', 'FFCC00');
  
 
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Attribution des dossards ', 'Les dossards peuvent être attribués en fonction de la catégorie de la compétition ou en fonction d&lsquo;une combinaison à choix des disciplines techniques, courses de moins de 400m et de plus de 400m ou en fonction de toutes les disciplines.', 'y', 10, 30, 120, 250, 'meeting_entries_start', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Startnummern Zuordnung', 'Startnummern können nach Wettkampfkategorie und  nach beliebiger Kombination von technischen Disziplinen, Läufe unter 400m und über 400m oder nach allen Disziplinen vergeben werden.', 'y', 10, 30, 120, 250, 'meeting_entries_start', 'de', 'FFAA00', 'FFCC00');
  