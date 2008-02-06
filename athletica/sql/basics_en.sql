# phpMyAdmin MySQL-Dump
# version 2.4.0
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Erstellungszeit: 01. Mai 2003 um 11:28
# Server Version: 3.23.38
# PHP-Version: 4.2.0
# Datenbank: `athletica`

#
# Daten für Tabelle `disziplin`
#

INSERT IGNORE INTO disziplin (Kurzname, Name, Anzeige, Seriegroesse, Staffellaeufer, Typ, Appellzeit) VALUES
('50', '50m', 10, 6, 0, 2, '01:00:00'),
('60', '60m', 11, 6, 0, 1, '01:00:00'),
('80', '80m', 12, 6, 0, 1, '01:00:00'),
('100', '100m', 13, 6, 0, 1, '01:00:00'),
('200', '200m', 14, 6, 0, 1, '01:00:00'),
('300', '300m', 15, 6, 0, 2, '01:00:00'),
('400', '400m', 16, 6, 0, 2, '01:00:00'),
('800', '800m', 17, 12, 0, 7, '01:00:00'),
('1500', '1500m', 19, 16, 0, 7, '01:00:00'),
('1000', '1000m', 18, 16, 0, 7, '01:00:00'),
('3000', '3000m', 20, 16, 0, 7, '01:00:00'),
('5000', '5000m', 21, 16, 0, 7, '01:00:00'),
('10000', '10000m', 22, 16, 0, 7, '01:00:00'),
('50H', '50m Hurdles', 30, 6, 0, 2, '01:00:00'),
('60H', '60m Hurdles', 31, 6, 0, 1, '01:00:00'),
('80H', '80m Hurdles', 32, 6, 0, 1, '01:00:00'),
('100H', '100m Hurdles', 33, 6, 0, 1, '01:00:00'),
('110H', '110m Hurdles', 34, 6, 0, 1, '01:00:00'),
('300H', '300m Hurdles', 35, 6, 0, 2, '01:00:00'),
('400H', '400m Hurdles', 36, 6, 0, 2, '01:00:00'),
('3000ST', '3000m Steeple', 37, 12, 0, 7, '01:00:00'),
('LONG', 'Long jump', 50, 999, 0, 4, '00:00:00'),
('TRIPLE', 'Triple jump', 51, 999, 0, 4, '00:00:00'),
('HIGH', 'High jump', 52, 999, 0, 6, '00:00:00'),
('POLE', 'Pole vault', 53, 999, 0, 6, '00:00:00'),
('SHOT', 'Shot put', 60, 999, 0, 8, '00:00:00'),
('DISCUS', 'Discus', 61, 999, 0, 8, '00:00:00'),
('HAMMER', 'Hammer', 62, 999, 0, 8, '00:00:00'),
('JAVELIN', 'Javelin', 63, 999, 0, 8, '00:00:00'),
('4X100', '4x100m', 1, 6, 4, 3, '01:00:00'),
('4X400', '4x400m', 2, 6, 4, 3, '01:00:00'),
('5X80', '5x80m', 3, 6, 5, 3, '01:00:00');

#
# Daten für Tabelle `kategorie`
#

INSERT IGNORE INTO kategorie (Kurzname, Name, Anzeige, Alterslimite) VALUES
('M', 'Men', 1, 99),
('F', 'Women', 2, 99);

#
# Daten für Tabelle `rundentyp`
#

INSERT INTO rundentyp (xRundentyp, Typ, Name, Wertung) VALUES
(1, 'P', 'Preliminary', 0),
(2, 'F', 'Final', 0),
(3, 'SF', 'Semi final', 0),
(5, 'Q', 'Qualification', 1);

