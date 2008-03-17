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
('50H', '50m ostacoli', 30, 6, 0, 2, '01:00:00'),
('60H', '60m ostacoli', 31, 6, 0, 1, '01:00:00'),
('80H', '80m ostacoli', 32, 6, 0, 1, '01:00:00'),
('100H', '100m ostacoli', 33, 6, 0, 1, '01:00:00'),
('110H', '110m ostacoli', 34, 6, 0, 1, '01:00:00'),
('300H', '300m ostacoli', 35, 6, 0, 2, '01:00:00'),
('400H', '400m ostacoli', 36, 6, 0, 2, '01:00:00'),
('3000ST', '3000m siepi', 37, 12, 0, 7, '01:00:00'),
('LUNGO', 'Salto in lungo', 50, 999, 0, 4, '00:00:00'),
('TRIPLO', 'Salto in triplo', 51, 999, 0, 4, '00:00:00'),
('ALTO', 'Salto in alto', 52, 999, 0, 6, '00:00:00'),
('ASTA', 'Salto in asta', 53, 999, 0, 6, '00:00:00'),
('PESO', 'Getto del peso', 60, 999, 0, 8, '00:00:00'),
('DISCO', 'Getto del disco', 61, 999, 0, 8, '00:00:00'),
('MARTELLO', 'Martello', 62, 999, 0, 8, '00:00:00'),
('GIAVELLOTTO', 'Lancio del giavellotto', 63, 999, 0, 8, '00:00:00'),
('4X100', '4x100m', 1, 6, 4, 3, '01:00:00'),
('4X400', '4x400m', 2, 6, 4, 3, '01:00:00'),
('5X80', '5x80m', 3, 6, 5, 3, '01:00:00');

#
# Daten für Tabelle `kategorie`
#

INSERT IGNORE INTO kategorie (Kurzname, Name, Anzeige, Alterslimite) VALUES
('M', 'Maschile', 1, 99),
('JM', 'Juniores maschile', 2, 19),
('GA', 'Giovani A', 3, 17),
('GB', 'Giovani B', 4, 15),
('SAM', 'Scolari A', 5, 13),
('SBM', 'Scolari B', 6, 11),
('F', 'Femminile', 7, 99),
('JF', 'Juniores femminile', 8, 19),
('RA', 'Ragazze A', 9, 17),
('RB', 'Ragazze B', 10, 15),
('SAF', 'Scolare A', 11, 13),
('SBF', 'Scolare B', 12, 11);

#
# Daten für Tabelle `rundentyp`
#

INSERT INTO rundentyp (xRundentyp, Typ, Name, Wertung) VALUES
(1, 'V', 'Vorlauf', 0),
(2, 'F', 'Finale', 0),
(3, 'SF', 'Semi finale', 0),
(4, 'ZE', 'Zeitendlauf', 1),
(5, 'Q', 'Qualificazione', 1);

