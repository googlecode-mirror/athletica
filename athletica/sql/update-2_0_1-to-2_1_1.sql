# 
# Update Athletica 2.0.1 -> 2.1.1
# 

#
# Tabelle `resultat`
#
ALTER TABLE `resultat` DROP INDEX `Serienstart`,
	ADD INDEX `xSerienstart` (`xSerienstart`);

#
# Tabelle `serie`
#
ALTER TABLE `serie` DROP INDEX `xAnlage`,
	ADD INDEX `Anlage` (`xAnlage`);

ALTER TABLE `serie` DROP INDEX `xRunde`,
	ADD INDEX `Runde` (`xRunde`);

#
# Tabelle `wettkampf`
#
ALTER TABLE `wettkampf` DROP `Gewichtung`;

ALTER TABLE `wettkampf` DROP INDEX `xKatxDisz`,
ADD UNIQUE `Wettkampf` (`xKategorie`,`xDisziplin`,`xMeeting`);

