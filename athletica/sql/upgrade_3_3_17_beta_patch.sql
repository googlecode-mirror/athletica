
ALTER TABLE athlet ADD COLUMN Manuell int(1) NOT NULL default 0 AFTER Lizenztyp;  



   INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Changements manuels des donn�es d�athl�te ', 'Sur demande, les changements manuels des donn�es (nom, pr�nom et soci�t�) ne seront plus report�s avec la mise � jour des donn�es de base et l&lsquo;ajustement de meeting.', 'y', 290, 830, 120, 250, 'admin', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Manuelle �nderungen Athletendaten', 'Manuelle �nderungen der Athletendaten (Name, Vorname und Verein) werden mit dem Update der Stammdaten und mit dem Meetingabgleich auf Wunsch nicht mehr �berschrieben.', 'y', 290, 830, 120, 250, 'admin', 'de', 'FFAA00', 'FFCC00');
  
 
    INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Attribution des dossards ', 'Les dossards peuvent �tre attribu�s en fonction de la cat�gorie de la comp�tition ou en fonction d&lsquo;une combinaison � choix des disciplines techniques, courses de moins de 400m et de plus de 400m ou en fonction de toutes les disciplines.', 'y', 10, 30, 120, 250, 'meeting_entries_start', 'fr', 'FFAA00', 'FFCC00'),
  
  ('Startnummern Zuordnung', 'Startnummern k�nnen nach Wettkampfkategorie und  nach beliebiger Kombination von technischen Disziplinen, L�ufe unter 400m und �ber 400m oder nach allen Disziplinen vergeben werden.', 'y', 10, 30, 120, 250, 'meeting_entries_start', 'de', 'FFAA00', 'FFCC00');
  