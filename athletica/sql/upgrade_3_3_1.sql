INSERT IGNORE INTO `disziplin` ( `xDisziplin` , `Kurzname` , `Name` , `Anzeige` , `Seriegroesse` , `Staffellaeufer` , `Typ` , `Appellzeit` , `Stellzeit` , `Strecke` , `Code` , `xOMEGA_Typ` )
VALUES (
'', '4KAMPF', 'Vierkampf', '404', '6', '0' , '9', '01:00:00', '00:20:00', '0', '404', '1'
);

INSERT IGNORE INTO `disziplin` ( `xDisziplin` , `Kurzname` , `Name` , `Anzeige` , `Seriegroesse` , `Staffellaeufer` , `Typ` , `Appellzeit` , `Stellzeit` , `Strecke` , `Code` , `xOMEGA_Typ` )
VALUES (
'', '100KMWALK', '100 km walk', '459', '6', '0' , '7', '01:00:00', '00:20:00', '0', '459', '1'
);

INSERT IGNORE INTO `disziplin` ( `xDisziplin` , `Kurzname` , `Name` , `Anzeige` , `Seriegroesse` , `Staffellaeufer` , `Typ` , `Appellzeit` , `Stellzeit` , `Strecke` , `Code` , `xOMEGA_Typ` )
VALUES (
'', '25KM', '25 km', '505', '6', '0' , '7', '01:00:00', '00:20:00', '0', '505', '1'
);

ALTER TABLE start 
 ADD COLUMN BaseEffort ENUM ('y','n') NOT NULL DEFAULT 'n' AFTER xStaffel;

ALTER TABLE Serienstart ADD COLUMN RundeZusammen int(11) NOT NULL default 0 AFTER xStart;

ALTER TABLE anmeldung ADD COLUMN BaseEffortMK ENUM('y','n') DEFAULT 'n' NOT NULL AFTER xTeam;
ALTER TABLE start ADD COLUMN BaseEffort ENUM('y','n') DEFAULT 'n' NOT NULL AFTER xStaffel;