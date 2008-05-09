INSERT IGNORE INTO disziplin(Kurzname, Name, Anzeige, Seriegroesse, Staffellaeufer, Typ, Appellzeit, Stellzeit, Strecke, Code, xOMEGA_Typ) VALUES
  ('4KAMPF', 'Vierkampf', '404', '6', '0', '9', '01:00:00', '00:20:00', '0', '404', '1'),
  ('100KMWALK', '100 km walk', '459', '6', '0' , '7', '01:00:00', '00:20:00', '0', '459', '1'),
  ('25KM', '25 km', '505', '6', '0' , '7', '01:00:00', '00:20:00', '0', '505', '1');

ALTER TABLE start ADD COLUMN BaseEffort ENUM ('y','n') NOT NULL DEFAULT 'n' AFTER xStaffel;
ALTER TABLE serienstart ADD COLUMN RundeZusammen int(11) NOT NULL default 0 AFTER xStart;
ALTER TABLE anmeldung ADD COLUMN BaseEffortMK ENUM('y','n') DEFAULT 'n' NOT NULL AFTER xTeam;


INSERT INTO faq(Frage, Antwort, Zeigen, PosTop, PosLeft, height, width, Seite, Sprache, FarbeTitel, FarbeHG) VALUES 
  ('Actualiser les meilleures performances', 'Neu können die Bestleistungen jederzeit aktualisiert werden. Damit ist gewährleistet, dass das Meeting mit den neusten Bestleistungen durchgeführt wird.<br/><br/>\r\n\r\nKlicken Sie auf die Schaltfläche \"Bestleistungen aktualisieren\" um diese Funktion aufzurufen.<br/>\r\nBevor die Funktion ausgeführt wird werden Sie gefragt, was mit den von Hand eingetragenen Bestleistungen geschehen soll.<br/><br/>\r\n\r\n<b>ACHTUNG: Damit die Bestleistungen aktualisiert werden können muss zuerst ein Update der Stammdaten durchgeführt werden.</b>', 'y', 50, 50, 120, 350, 'meeting_entries_start', 'fr', 'FFAA00', 'FFCC00'),
  ('Bestleistungen aktualisieren', 'Neu können die Bestleistungen jederzeit aktualisiert werden. Damit ist gewährleistet, dass das Meeting mit den neusten Bestleistungen durchgeführt wird.<br/><br/>\r\n\r\nKlicken Sie auf die Schaltfläche \"Bestleistungen aktualisieren\" um diese Funktion aufzurufen.<br/>\r\nBevor die Funktion ausgeführt wird werden Sie gefragt, was mit den von Hand eingetragenen Bestleistungen geschehen soll.<br/><br/>\r\n\r\n<b>ACHTUNG: Damit die Bestleistungen aktualisiert werden können muss zuerst ein Update der Stammdaten durchgeführt werden.</b>', 'y', 50, 50, 120, 350, 'meeting_entries_start', 'de', 'FFAA00', 'FFCC00');