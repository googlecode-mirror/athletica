##
## athletica sql upgrade to 3.1.1
## ------------------------------
##
##


INSERT IGNORE INTO `disziplin` ( `xDisziplin` , `Kurzname` , `Name` , `Anzeige` , `Seriegroesse` , `Staffellaeufer` , `Typ` , `Appellzeit` , `Stellzeit` , `Strecke` , `Code` , `xOMEGA_Typ` )
VALUES (
'', 'BALL80', 'Ball 80 g', '385', '6', '0' , '8', '01:00:00', '00:20:00', '0', '385', '1'
);'
UPDATE `disziplin` SET `Code` = '385' WHERE `Anzeige` = '385' AND `Kurzname` = 'BALL80';

INSERT IGNORE INTO `disziplin` ( `xDisziplin` , `Kurzname` , `Name` , `Anzeige` , `Seriegroesse` , `Staffellaeufer` , `Typ` , `Appellzeit` , `Stellzeit` , `Strecke` , `Code` , `xOMEGA_Typ` )
VALUES (
'', '300H91.4', '300 m Hürden 91.4', '289', '6', '0' , '2', '01:00:00', '00:15:00', '300', '289', '4'
);


INSERT IGNORE INTO `kategorie` ( `xKategorie` , `Kurzname` , `Name` , `Anzeige` , `Alterslimite` , `Code` , `Geschlecht` )
VALUES (
'', 'U10M', 'U10 M', '7', '9', 'U10M', 'm'
);
INSERT INTO `kategorie` ( `xKategorie` , `Kurzname` , `Name` , `Anzeige` , `Alterslimite` , `Code` , `Geschlecht` )
VALUES (
'', 'U10W', 'U10 W', '15', '9', 'U10W', 'w'
);
