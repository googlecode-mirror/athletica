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
('50H', '50m haies', 30, 6, 0, 2, '01:00:00'),
('60H', '60m haies', 31, 6, 0, 1, '01:00:00'),
('80H', '80m haies', 32, 6, 0, 1, '01:00:00'),
('100H', '100m haies', 33, 6, 0, 1, '01:00:00'),
('110H', '110m haies', 34, 6, 0, 1, '01:00:00'),
('300H', '300m haies', 35, 6, 0, 2, '01:00:00'),
('400H', '400m haies', 36, 6, 0, 2, '01:00:00'),
('3000ST', '3000m Steeple', 37, 12, 0, 7, '01:00:00'),
('LONGEUR', 'Longeur', 50, 999, 0, 4, '00:00:00'),
('TRIPLE', 'Triple saut', 51, 999, 0, 4, '00:00:00'),
('HAUTEUR', 'Hauteur', 52, 999, 0, 6, '00:00:00'),
('PERCHE', 'Perche', 53, 999, 0, 6, '00:00:00'),
('POIDS', 'Poids', 60, 999, 0, 8, '00:00:00'),
('DISQUES', 'Disques', 61, 999, 0, 8, '00:00:00'),
('MARTEAU', 'Marteu', 62, 999, 0, 8, '00:00:00'),
('JAVELOT', 'Javelot', 63, 999, 0, 8, '00:00:00'),
('4X100', 'Relais 4x100m', 1, 6, 4, 3, '01:00:00'),
('4X400', 'Relais 4x400m', 2, 6, 4, 3, '01:00:00'),
('5X80', 'Relais 5x80m', 3, 6, 5, 3, '01:00:00');

#
# Daten für Tabelle `kategorie`
#

INSERT IGNORE INTO kategorie (Kurzname, Name, Anzeige, Alterslimite) VALUES
('M', 'Hommes', 1, 99),
('J', 'Juniors', 2, 19),
('CA', 'Cadets A', 3, 17),
('CB', 'Cadets B', 3, 17),
('EA', 'Ecoliers A', 5, 13),
('EB', 'Ecoliers B', 6, 11),
('F', 'Femmes', 7, 99),
('FJ', 'Femmes juniores' 8, 19),
('FCA', 'Cadettes A', 9, 17),
('FCB', 'Cadettes B', 10, 15),
('FEA', 'Ecolières A', 11, 13),
('FEB', 'Ecolières B', 12, 11);

#
# Daten für Tabelle `rundentyp`
#

INSERT INTO rundentyp (xRundentyp, Typ, Name, Wertung) VALUES
(1, 'V', 'Vorlauf', 0),
(2, 'F', 'Finale', 0),
(3, 'ZW', 'Zwischenlauf', 0),
(4, 'ZE', 'Zeitendlauf', 1),
(5, 'Q', 'Qualification', 1);

