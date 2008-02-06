# 
# Update Athletica 2.1.2 -> 2.1.3
# 

#
# Tabelle `meeting`
#
ALTER TABLE `meeting` ADD `Nummer` VARCHAR(20) NOT NULL AFTER `DatumBis`;

#
# Tabelle `teamwettkampf`
#
DROP TABLE `teamwettkampf`;

#
# Tabelle `wettkampf`
#
ALTER TABLE `wettkampf` DROP `Wertung`;


