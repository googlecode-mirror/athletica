#
# Daten für Tabelle `db`
#

INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Grant_priv, References_priv, Index_priv, Alter_priv) VALUES
('localhost', 'athletica', 'athletica', 'Y', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'N', 'N');


#
# Daten für Tabelle `tables_priv`
#

INSERT INTO tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'athletica', 'athletica', 'tempresult', 'root', 20030122133620, 'Select,Insert,Update,Delete,Create,Drop', '');


#
# Daten für Tabelle `user`
#

INSERT INTO user (Host, User, Password, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv) VALUES
('localhost', 'athletica', '6b8717154bdc0df7', 'Y', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');

