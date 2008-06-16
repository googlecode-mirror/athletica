ALTER IGNORE TABLE serienstart ADD KEY xSerie (xSerie);
ALTER IGNORE TABLE serienstart ADD KEY xStart (xStart);

ALTER TABLE wettkampf CHANGE Punkteformel Punkteformel varchar(20) DEFAULT 0 NOT NULL;

UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'MAN_';
UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'MASM';
UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U23M';
UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U20M';
UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U18M';
UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U16M';
UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U14M';
UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U12M';
UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U10M';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'WOM_';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'MASW';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'U23W';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'U20W';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'U18W';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'U16W';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'U14W';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'U12W';
UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'U10W';