<?php

/********************
 *
 *	admin_backup.php
 *	----------------
 *	
 *******************/

require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	{		// invalid DB connection
	return;
}

/*
	Before restoring, the backup file will be verified according to the
	following attributes:
	1.) the following ID-String must be identical to guarantee the same
		DB version.
	2.) the number of TRUNCATE statements don't have to be equal because
		of the empty tables delivered by the backup
*/

set_time_limit(3600); // the script will break if this is not set

$idstring = "# $cfgApplicationName $cfgApplicationVersion\n";

if($_GET['arg'] == 'backup')
{
	if ($_GET['xMeeting']=="-"){
		$result = mysql_list_tables($cfgDBname);
		$filename = 'athletica_'. date('Y-m-d H.i') .'.sql';
	} else {
		$sql_backuptables = "TRUNCATE TABLE sys_backuptabellen;";
		$query_backuptables = mysql_query($sql_backuptables);
		
		$sql_backuptables = "INSERT INTO `sys_backuptabellen` (`xBackup`, `Tabelle`, `SelectSQL`) VALUES 
									  (1, 'anlage', 'SELECT * FROM anlage'),
									  (2, 'anmeldung', 'SELECT * FROM anmeldung WHERE xMeeting = \'%d\''),
									  (3, 'athlet', 'SELECT * FROM athlet'),
									  (5, 'base_account', 'SELECT * FROM base_account'),
									  (6, 'base_athlete', 'SELECT * FROM base_athlete'),
									  (7, 'base_log', 'SELECT * FROM base_log'),
									  (8, 'base_performance', 'SELECT * FROM base_performance'),
									  (9, 'base_relay', 'SELECT * FROM base_relay'),
									  (10, 'base_svm', 'SELECT * FROM base_svm'),
									  (11, 'disziplin', 'SELECT * FROM disziplin'),
									  (13, 'kategorie', 'SELECT * FROM kategorie'),
									  (16, 'layout', 'SELECT * FROM layout WHERE xMeeting = \'%d\''),
									  (17, 'meeting', 'SELECT * FROM meeting WHERE xMeeting=\'%d\''),
									  (18, 'omega_typ', 'SELECT * FROM omega_typ'),
									  (19, 'region', 'SELECT * FROM region'),
									  (20, 'resultat', 'SELECT\r\n    resultat.*\r\nFROM\r\n    athletica.resultat\r\n    LEFT JOIN athletica.serienstart \r\n        ON (resultat.xSerienstart = serienstart.xSerienstart)\r\n    LEFT JOIN athletica.start \r\n        ON (serienstart.xStart = start.xStart)\r\n    LEFT JOIN athletica.wettkampf \r\n        ON (start.xWettkampf = wettkampf.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\') \r\nAND xResultat IS NOT NULL;'),
									  (21, 'runde', 'SELECT\r\n    runde.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\') \r\nAND xRunde IS NOT NULL;'),
									  (22, 'rundenlog', 'SELECT\r\n    rundenlog.*\r\nFROM\r\n    athletica.runde\r\n    JOIN athletica.rundenlog \r\n        ON (runde.xRunde = rundenlog.xRunde)\r\n    JOIN athletica.wettkampf \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\') \r\nAND xRundenlog IS NOT NULL;'),
									  (23, 'rundenset', 'SELECT * FROM rundenset WHERE xMeeting = \'%d\''),
									  (24, 'rundentyp', 'SELECT * FROM rundentyp'),
									  (25, 'serie', 'SELECT\r\n    serie.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\n    LEFT JOIN athletica.serie \r\n        ON (runde.xRunde = serie.xRunde)\r\nWHERE (wettkampf.xMeeting =\'%d\') \r\nAND xSerie IS NOT NULL;'),
									  (26, 'serienstart', 'SELECT\r\n    serienstart.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.runde \r\n        ON (wettkampf.xWettkampf = runde.xWettkampf)\r\n    LEFT JOIN athletica.serie \r\n        ON (runde.xRunde = serie.xRunde)\r\n    LEFT JOIN athletica.serienstart \r\n        ON (serie.xSerie = serienstart.xSerie)\r\nWHERE (wettkampf.xMeeting =\'%d\') \r\nAND xSerienstart IS NOT NULL;'),
									  (27, 'stadion', 'SELECT * FROM stadion'),
									  (28, 'staffel', 'SELECT * FROM staffel WHERE xMeeting = \'%d\''),
									  (29, 'staffelathlet', 'SELECT\r\n    staffelathlet.*\r\nFROM\r\n    athletica.staffelathlet\r\n    INNER JOIN athletica.runde \r\n        ON (staffelathlet.xRunde = runde.xRunde)\r\n    INNER JOIN athletica.wettkampf \r\n        ON (runde.xWettkampf = wettkampf.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\') \r\nAND xStaffelstart IS NOT NULL;'),
									  (30, 'start', 'SELECT\r\n    start.*\r\nFROM\r\n    athletica.wettkampf\r\n    LEFT JOIN athletica.start \r\n        ON (wettkampf.xWettkampf = start.xWettkampf)\r\nWHERE (wettkampf.xMeeting =\'%d\') \r\nAND xStart IS NOT NULL;'),
									  (31, 'team', 'SELECT * FROM team WHERE xMeeting = \'%d\''),
									  (32, 'teamsm', 'SELECT * FROM teamsm WHERE xMeeting = \'%d\''),
									  (33, 'teamsmathlet', 'SELECT\r\n    teamsmathlet.*\r\nFROM\r\n    athletica.teamsmathlet\r\n    LEFT JOIN athletica.anmeldung \r\n        ON (teamsmathlet.xAnmeldung = anmeldung.xAnmeldung)\r\nWHERE (anmeldung.xMeeting =\'%d\') \r\nAND xTeamsm IS NOT NULL;'),
									  (34, 'verein', 'SELECT * FROM verein'),
									  (35, 'wertungstabelle', 'SELECT * FROM wertungstabelle'),
									  (36, 'wertungstabelle_punkte', 'SELECT * FROM wertungstabelle_punkte'),
									  (37, 'wettkampf', 'SELECT * FROM wettkampf WHERE xMeeting = \'%d\''),
									  (38, 'zeitmessung', 'SELECT * FROM zeitmessung WHERE xMeeting = \'%d\'');";
		$query_backuptables = mysql_query($sql_backuptables);
		
		$result = mysql_query('SELECT Tabelle, SelectSQL FROM sys_backuptabellen');
		$xMeeting = $_GET['xMeeting'];
		
		$res = mysql_query("SELECT Name FROM meeting WHERE xMeeting = $xMeeting");
		$row = mysql_fetch_array($res);
		
		$filename = 'athletica_'. date('Y-m-d H.i')  .' ' . strToNTFSFilename($row['Name']) .'.sql';
	}
	
	if(mysql_errno() > 0)
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		if(mysql_num_rows($result) > 0)	// any table
		{
			// print http header
			header('Content-type: application/octetstream');
			header('Content-Disposition: inline; filename="'. $filename . '"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');

			echo "$idstring";
			echo "# Database Dump:\n";
			echo "# Date/time: " . date("d.M.Y, H:i:s") . "\n";
			echo "# ----------------------------------------------------------\n";
		}
	while ($row = mysql_fetch_row($result))
		{
			//ignore base-tables, sys-tables and other tables with non user customizing possibilities
			if (!isset($_GET['base'])){ 
				if (substr($row[0],0,5)== "base_" ||
					substr($row[0],0,4)== "sys_" ||
					$row[0] == "kategorie_svm" ||
					$row[0] == "faq" ||
					$row[0] == "land") 
				{
					continue;
				}
			}

	
			if ($_GET['xMeeting']=="-"){
				$res = mysql_query("SELECT * FROM $row[0]");
			} else {
				$res = mysql_query(sprintf($row[1], $xMeeting));
			}
			
			// truncate in each case!
			echo "\n#\n";
			echo "# Table '$row[0]'\n";
			echo "#\n\n";
			echo "TRUNCATE TABLE $row[0];\n";
			
			
			$fieldArray = array();
			if(mysql_num_rows($res) > 0)	// any content
			{
				$sqlInsert = "INSERT INTO $row[0] \n";
				
				$fields = mysql_query("SHOW COLUMNS FROM $row[0]");
				$tmpf = "(";
				while($f = mysql_fetch_assoc($fields)){
					$tmpf .= "`".$f['Field']."`, ";
					$fieldArray[] = $f;
				}
				$sqlInsert .= substr($tmpf,0,-2).") VALUES\n";
				echo $sqlInsert;
				
			}

			unset($values);
			$n = 0;
			while($tabrow = mysql_fetch_assoc($res))
			{
				if(!empty($values) && !$skip_nextline) {	// print previous row
					echo "$values),\n";
				}
				
				// dds
				// skip row if all vales are empty
				$allEmpty = true;
				foreach($fieldArray as $f){
					if($tabrow[$f['Field']]!=''){
						$allEmpty = false;
						break;
					}
				}
				
				if(!$allEmpty){
					$n++;
					
					$values = "(";
					$cma = "";
					
					foreach($fieldArray as $f){
						if(substr($f['Type'],0,3) == 'int') {	
							$values = $values . $cma . $tabrow[$f['Field']];
						} else {
							$values = $values . $cma . "'" . addslashes($tabrow[$f['Field']]) . "'";
						}
						$cma = ", ";
					}
					
					if ($n==1000){
						$n=0;
						echo "$values);#*\n $sqlInsert";
						$skip_nextline = true;
					} else {
						$skip_nextline = false;
					}
				}
				
			}		// End while every table row

			if(mysql_num_rows($res) > 0)	// any content
			{
				echo "$values);#*\n";		// print last row
								// the '#*' is needed for finding the end of the insert statement
								// (if there are semicolons in a field value)
			}
			
			mysql_free_result($res);

			echo "\n# ----------------------------------------------------------\n";
		}		// End while every table

		if(mysql_num_rows($result) > 0) {	// any table
			echo "\n#*ENDLINE"; // termination for validating
						// has to be on the last 9 characters
			flush();
		}

		mysql_free_result($result);
	}
}
else if ($_POST['arg'] == 'restore')
{
	$page = new GUI_Page('admin_backup');
	$page->startPage();
	$page->printPageTitle($strRestore);
	
	?>
<table class="dialog">
	<?php
	
	$timing_errors = 0;
	
	// get uploaded SQL file and read its content
	$fd = fopen($_FILES['bkupfile']['tmp_name'], 'rb');
	$content = fread($fd, filesize($_FILES['bkupfile']['tmp_name']));
	//fclose($fd);
	
	// since version 1.4 the include statements contain the table fields,
	// so they can by restored in later versions
	
	$error_msg = '';
	$error_type = 0;
	$ini_done = false;
	$name_ini = 'php.ini';
	$name_ini2 = 'php2.ini';
	$name_bak = 'php.bak_'.date('YmdHis', time());
	
	if($content == false){
		$error_type = $_FILES['bkupfile']['error'];
		switch($_FILES['bkupfile']['error']){
			case 1:
				if($cfgInstallDir!='[ATHLETICA]'){
					$ini_inhalt = @file_get_contents($cfgInstallDir.'\php\\'.$name_ini);
					
					$search = 'upload_max_filesize = ([0-9]{1,2}M)';
					$replace = 'upload_max_filesize = 50M';
					$ini_inhalt2 = eregi_replace($search, $replace, $ini_inhalt);
					
					if($ini_inhalt2!='' && $ini_inhalt2!=$ini_inhalt){
						$ini_neu = @fopen($cfgInstallDir.'\php\\'.$name_ini2, 'w+');
						
						if($ini_neu){
							$write_neu = @fwrite($ini_neu, $ini_inhalt2);
							
							if($write_neu){
								@fclose($ini_neu);
								
								$ini_rename = rename($cfgInstallDir.'\php\\'.$name_ini, $cfgInstallDir.'\php\\'.$name_bak);
									
								if($ini_rename){
									$ini_rename2 = rename($cfgInstallDir.'\php\\'.$name_ini2, $cfgInstallDir.'\php\\'.$name_ini);
									
									if($ini_rename2){
										$ini_done = true;
									} else {
										$ini_rename = rename($cfgInstallDir.'\php\\'.$name_bak, $cfgInstallDir.'\php\\'.$name_ini);
										@unlink($cfgInstallDir.'\php\\'.$name_ini2, 'w+');
									}
								} else {
									@unlink($cfgInstallDir.'\php\\'.$name_ini2, 'w+');
								}
							} else {
								@fclose($ini_neu);
							}
						}
					}
				}
			
				$error_msg = str_replace('%SIZE%', ini_get('upload_max_filesize'), $strUploadMaxFilesize);
				break;
			case 2:
				$error_msg = $strUploadFormFilesize;
				break;
			case 3:
				$error_msg = $strUploadPartial;
				break;
			case 4:
				$error_msg = $strNoFile;
				break;
		}
	}
	
	$validBackup = false;
	
	if($error_msg==''){
		$backupVersion = "";
		foreach($cfgBackupCompatibles as $v){
			$idstring = "# $cfgApplicationName $v\n";
			$idstring2 = "# $cfgApplicationName $v\r";
			if((strncmp($content, $idstring, strlen($idstring)) == 0) || (strncmp($content, $idstring2, strlen($idstring2)) == 0)){
				$validBackup = true;
				$backupVersion = $v;
				break;
			}
		}
		
		// cut SLV_ from version
		$shortVersion = ""; // version without SLV_         
		if(substr($backupVersion,0,4) == "SLV_"){
			$shortVersion = substr($backupVersion, 4, 3);
		}else{
			$shortVersion = substr($backupVersion, 0, 3);            
		}
		
		// since version 1.9 the backup contains a termination line
		if($shortVersion >= 1.9){
			$term = substr($content, -9);
			if($term != "#*ENDLINE"){
				$validBackup = false;
			}else{
				echo "<tr><th class='secure'>-- $strBackupStatus2 --</th></tr>";
			}
			
		}else{
			
			echo "<tr><th class='insecure'>-- $strBackupStatus1 --</th></tr>";
			
		}
	}
	
	if(!$validBackup)	// invalid backup ID
	{
		if($error_msg!=''){
			?>
			<tr>
				<th class='bestlistupdate'><?=$strError?></th>
			</tr>
			<tr class="odd">
				<td><?=$error_msg?></td>
			</tr>
			<?php
			if($error_type==1){
				if($ini_done){
					$strMaxFileSize8 = str_replace('%NAME%', $name_bak, $strMaxFileSize8);
					?>
					<tr class="odd">
						<td>
							<br/><?=$strMaxFileSizeOK?><br/>
							<ol>
								<li><?=$strMaxFileSize1?><br/><br/></li>
								<li><?=$strMaxFileSize2?><br/><br/></li>
								<li><?=$strMaxFileSize7?><br/><br/></li>
								<li><?=$strMaxFileSize8?><br/><br/></li>
								<li><?=$strMaxFileSize6?></li>
							</ol>
						</td>
					</tr>
					<?php
				} else {
					$upload_max_filesize = ini_get('upload_max_filesize');
					$strMaxFileSize5 = str_replace('%SIZE%', $upload_max_filesize, $strMaxFileSize5);
					?>
					<tr class="odd">
						<td>
							<br/><?=$strMaxFileSizeCorrect?><br/>
							<ol>
								<li><?=$strMaxFileSize1?><br/><br/></li>
								<li><?=$strMaxFileSize2?><br/><br/></li>
								<li><?=$strMaxFileSize3?><br/><br/></li>
								<li>
									<?=$strMaxFileSize4?><br/><br/>
									<div class="code">
										; Maximum allowed size for uploaded files.<br/>
										upload_max_filesize = <?=$upload_max_filesize?>
									</div><br/>
								</li>
								<li><?=$strMaxFileSize5?><br/><br/></li>
								<li><?=$strMaxFileSize6?></li>
							</ol>
						</td>
					</tr>
					<?php
				}
			}
			?>
			<tr class="even">
				<td>
					<input type="button" name="btnBack" value="<?=$strBack?>" class="uploadbutton" onclick="document.location.href = 'admin.php';"/>
				</td>
			</tr>
			<?php
		} else {
			AA_printErrorMsg($strErrInvalidBackupFile);
		}
	}
	else
	{
		$error = false;			// backup error
		$sqlTruncate = array();		// array to hold TRUNCATE statements;	
		$sqlInsert = array();		// array to hold INSERT statements;	
		
		// as of 1.8 the table omega_konfiguration is named zeitmessung
		$content = str_replace("omega_konfiguration", "zeitmessung", $content);
		
		// dds
		$search = array(
			"VALUES\n(, '', '', '', '', '', '', , , , ),", // anmeldung
			",\n(, '', '', '', '', '', '', , , , )", // anmeldung
			"VALUES\n(, '', '', '', , , , '', '', '', '', '', , ''),", // athlet
			",\n(, '', '', '', , , , '', '', '', '', '', , '')", // athlet
			"VALUES\n(, '', '', , , , , '', '', '', , ),", // disziplin
			",\n(, '', '', , , , , '', '', '', , )", // disziplin
			"VALUES\n(, '', '', , '', '', ''),", // kategorie
			",\n(, '', '', , '', '', '')", // kategorie
			"VALUES\n(, , '', '', , '', '', , '', '', , '', '', , '', '', , '', '', ),", // layout
			",\n(, , '', '', , '', '', , '', '', , '', '', , '', '', , '', '', )", // layout
			"VALUES\n(, '', '', '', '', '', , '', '', '', '', , , '', '', '', ''),", // meeting
			",\n(, '', '', '', '', '', , '', '', '', '', , , '', '', '', '')", // meeting
			"VALUES\n(, '', ''),", // omega_typ
			",\n(, '', '')", // omega_typ
			"VALUES\n(, '', '', ),", // region
			",\n(, '', '', )", // region
			"VALUES\n(, , '', , ),", // resultat
			",\n(, , '', , )", // resultat
			"INSERT INTO resultat \n(`xResultat`, `Leistung`, `Info`, `Punkte`, `xSerienstart`) VALUES\n(, , '', , );", // resultat
			"INSERT INTO resultat \n(`xResultat`, `Leistung`, `Info`, `Punkte`, `xSerienstart`) VALUES\n);", // resultat
			"VALUES\n(, '', '', '', '', , , '', '', '', '', '', '', '', , ),", // runde
			",\n(, '', '', '', '', , , '', '', '', '', '', '', '', , )", // runde
			"INSERT INTO runde \n(`xRunde`, `Datum`, `Startzeit`, `Appellzeit`, `Stellzeit`, `Status`, `Speakerstatus`, `StatusZeitmessung`, `StatusUpload`, `QualifikationSieger`, `QualifikationLeistung`, `Bahnen`, `Versuche`, `Gruppe`, `xRundentyp`, `xWettkampf`) VALUES\n(, '', '', '', '', , , '', '', '', '', '', '', '', , );", // runde
			"INSERT INTO runde \n(`xRunde`, `Datum`, `Startzeit`, `Appellzeit`, `Stellzeit`, `Status`, `Speakerstatus`, `StatusZeitmessung`, `StatusUpload`, `QualifikationSieger`, `QualifikationLeistung`, `Bahnen`, `Versuche`, `Gruppe`, `xRundentyp`, `xWettkampf`) VALUES\n);", // runde
			"VALUES\n(, '', '', ),", // rundenlog
			",\n(, '', '', )", // rundenlog
			"INSERT INTO rundenlog \n(`xRundenlog`, `Zeit`, `Ereignis`, `xRunde`) VALUES\n(, '', '', );", // rundenlog
			"INSERT INTO rundenlog \n(`xRundenlog`, `Zeit`, `Ereignis`, `xRunde`) VALUES\n);", // rundenlog
			"VALUES\n(, , , ''),", // rundenset
			",\n(, , , '')", // rundenset
			"VALUES\n(, '', '', '', ''),", // rundentyp
			",\n(, '', '', '', '')", // rundentyp
			"VALUES\n(, '', '', , , '', , ),", // serie
			",\n(, '', '', , , '', , )", // serie
			"INSERT INTO serie \n(`xSerie`, `Bezeichnung`, `Wind`, `Film`, `Status`, `Handgestoppt`, `xRunde`, `xAnlage`) VALUES\n(, '', '', , , '', , );", // serie
			"INSERT INTO serie \n(`xSerie`, `Bezeichnung`, `Wind`, `Film`, `Status`, `Handgestoppt`, `xRunde`, `xAnlage`) VALUES\n);", // serie
			"VALUES\n(, , , , '', , ),", // serienstart
			",\n(, , , , '', , )", // serienstart
			"INSERT INTO serienstart \n(`xSerienstart`, `Position`, `Bahn`, `Rang`, `Qualifikation`, `xSerie`, `xStart`, `RundeZusammen`) VALUES\n(, , , , '', , );", // serienstart
			"INSERT INTO serienstart \n(`xSerienstart`, `Position`, `Bahn`, `Rang`, `Qualifikation`, `xSerie`, `xStart`, `RundeZusammen`) VALUES\n);", // serienstart
			"INSERT INTO serienstart \n(`xSerienstart`, `Position`, `Bahn`, `Rang`, `Qualifikation`, `xSerie`, `xStart`) VALUES\n(, , , , '', , );", // serienstart
			"INSERT INTO serienstart \n(`xSerienstart`, `Position`, `Bahn`, `Rang`, `Qualifikation`, `xSerie`, `xStart`) VALUES\n);", // serienstart
			"VALUES\n(, '', , , , , '', ),", // staffel
			",\n(, '', , , , , '', )", // staffel
			"VALUES\n(, , , ),", // staffelathlet
			",\n(, , , )", // staffelathlet
			"INSERT INTO staffelathlet \n(`xStaffelstart`, `xAthletenstart`, `xRunde`, `Position`) VALUES\n(, , , )", // staffelathlet
			"VALUES\n(, '', , '', '', , , ),", // start
			",\n(, '', , '', '', , , )", // start
			"INSERT INTO start \n(`xStart`, `Anwesend`, `Bestleistung`, `Bezahlt`, `Erstserie`, `xWettkampf`, `xAnmeldung`, `xStaffel`, `BaseEffort`) VALUES\n(, '', , '', '', , , );", // start
			"INSERT INTO start \n(`xStart`, `Anwesend`, `Bestleistung`, `Bezahlt`, `Erstserie`, `xWettkampf`, `xAnmeldung`, `xStaffel`, `BaseEffort`) VALUES\n);", // start
			"INSERT INTO start \n(`xStart`, `Anwesend`, `Bestleistung`, `Bezahlt`, `Erstserie`, `xWettkampf`, `xAnmeldung`, `xStaffel`) VALUES\n(, '', , '', '', , , );", // start
			"INSERT INTO start \n(`xStart`, `Anwesend`, `Bestleistung`, `Bezahlt`, `Erstserie`, `xWettkampf`, `xAnmeldung`, `xStaffel`) VALUES\n);", // start
			"VALUES\n(, '', '', '', '', ''),", // stadion
			",\n(, '', '', '', '', '')", // stadion
			"VALUES\n(, '', '', , , , ),", // team
			",\n(, '', '', , , , )", // team
			"VALUES\n(, '', , , , ),", // teamsm
			",\n(, '', , , , )", // teamsm
			"VALUES\n(, ),", // teamsmathlet
			",\n(, )", // teamsmathlet
			"INSERT INTO teamsmathlet \n(`xTeamsm`, `xAnmeldung`) VALUES\n(, )", // teamsmathlet
			"INSERT INTO teamsmathlet \n(`xTeamsm`, `xAnmeldung`) VALUES\n)", // teamsmathlet
			"VALUES\n(, '', '', '', ''),", // verein
			",\n(, '', '', '', '')", // verein
			"VALUES\n(, ''),", // wertungstabelle
			",\n(, '')", // wertungstabelle
			"VALUES\n(, , , '', '', ),", // wertungstabelle_punkte
			",\n(, , , '', '', )", // wertungstabelle_punkte
			"VALUES\n(, '', '', '', '', '', '', '', '', '', , , , , '', '', , ),", // wettkampf
			",\n(, '', '', '', '', '', '', '', '', '', , , , , '', '', , )", // wettkampf
			"VALUES\n(, '', '', '', '', '', '', '', '', '', '', '', '', '', '', ),", // zeitmessung
			",\n(, '', '', '', '', '', '', '', '', '', '', '', '', '', '', )", // zeitmessung
		);
		
		foreach($search as $s){
			$replace = (eregi('^VALUES', $s)) ? 'VALUES' : '';
			$content = str_replace($s, $replace, $content);
		}
		
		$glb_content = $content;
		
		while(strlen($content) > 0)
		{
			$content = strstr($content, "TRUNCATE");
			if($content == false) {
				break;
			}
			$length = strpos($content, ";");
			if($length == false) {
				break;
			}
			$sqlTruncate[]	= substr($content, 0, $length+1);
			$content = substr($content, $length+1);
		}
		
		rewind($fd);
		//$content = fread($fd, filesize($_FILES['bkupfile']['tmp_name']));
		$content = $glb_content;
		
		if($shortVersion < 1.9){ // replace certain things in older backups
			// as of 1.7 the field xMehrkampfcode is named as Mehrkampfcode
			$content = str_replace("xMehrkampfcode", "Mehrkampfcode", $content);
			// as of 1.7.1 the field RegionSpezial is named as xRegion
			$content = str_replace("RegionSpezial", "xRegion", $content);
			// as of 1.8 the table omega_konfiguration is named zeitmessung
			$content = str_replace("omega_konfiguration", "zeitmessung", $content);
			// --> the fields are the same but xOMEGA_Konfiguration
			$content = str_replace("xOMEGA_Konfiguration", "xZeitmessung", $content);
		}
		
		while(strlen($content) > 0)
		{
			$content = strstr($content, "INSERT");
			if($content == false) {
				break;
			}
			$length = strpos($content, ";#*");
			//$length = strpos($content, ";");
			if($length == false) {
				break;
			}
			$sqlInsert[]	= substr($content, 0, $length+1);
			$content = substr($content, $length+1);
		}
		
		
		// to less tables to truncate -> not a valid backup
		// this isn't relevant for version 1.9 and above ( because of the termination line)
		if($shortVersion < 1.9 && count($sqlTruncate) < 30){
			AA_printErrorMsg($strBackupDamaged);
			$error = true;
		}else{
			
			// set max_allowed_packet for inserting very big queries
			mysql_pconnect( $GLOBALS['cfgDBhost'].':'.$GLOBALS['cfgDBport'], "root", "");
			mysql_select_db($GLOBALS['cfgDBname']);
			mysql_query("SET @@global.max_allowed_packet=16777216"); //16 MB
			if(mysql_errno() > 0){
				$error = true;
				AA_printErrorMsg(mysql_errno().": ".mysql_error());
			}
			
			// check if equal amount of truncate and insert statements
			/*if(count($sqlTruncate) != count($sqlInsert))
			{
				AA_printErrorMsg($strErrInvalidBackupFile);
			}
			else
			{*/
				// process every SQL statement
				for($i=0; $i < count($sqlTruncate); $i++)
				{
					
					//skip tables
					if(substr($sqlTruncate[$i], 0, strlen("TRUNCATE TABLE kategorie_svm")) == "TRUNCATE TABLE kategorie_svm" ||
					   substr($sqlTruncate[$i], 0, strlen("TRUNCATE TABLE faq")) == "TRUNCATE TABLE faq"){ 
						continue;
					}
					
					//echo "$sqlTruncate[$i] ";
					mysql_query($sqlTruncate[$i]);
					if(mysql_errno() > 0)
					{
						$error = true;
						echo mysql_errno() . ": " . mysql_error() . "<br>";
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						break;
					}
					//echo "OK<br>";
				}
				
				for($i=0; $i < count($sqlInsert); $i++)
				{
					// restoring of base tables fails in older versions then 3.3 (new unique indexes in 3.3)
					if($shortVersion < 3.3 && substr($sqlInsert[$i],0, strlen("INSERT INTO base_")) == "INSERT INTO base_"){ 
						$skipped_basetables = true;
						continue;
					}
					
					//skip tables
					if(substr($sqlInsert[$i], 0, strlen("INSERT INTO kategorie_svm")) == "INSERT INTO kategorie_svm" || 
					   substr($sqlInsert[$i], 0, strlen("INSERT INTO faq")) == "INSERT INTO faq"){ 
						continue;
					}
					
					
					//echo substr($sqlInsert[$i], 0, strpos($sqlInsert[$i], " VALUES")) . " ... ";
					mysql_query($sqlInsert[$i]);
					if(mysql_errno() > 0)
					{
						$error = true;
						echo mysql_errno() . ": " . mysql_error() . "<br>";
						echo '<pre>'. $sqlInsert[$i].'</pre>';
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						break;
					}
					//echo "OK<br><br>";
				}
			//}	// ET invalid content
			
			// since 1.8 the roundtypes have a code field, update if backup is older
			if($shortVersion < 1.8){
				mysql_query("UPDATE `rundentyp` SET `Code` = 'V' WHERE `xRundentyp` =1 LIMIT 1 ");
				mysql_query("UPDATE `rundentyp` SET `Code` = 'F' WHERE `xRundentyp` =2 LIMIT 1 ");
				mysql_query("UPDATE `rundentyp` SET `Code` = 'Z' WHERE `xRundentyp` =3 LIMIT 1 ");
				mysql_query("UPDATE `rundentyp` SET `Code` = 'Q' WHERE `xRundentyp` =5 LIMIT 1 ");
				mysql_query("UPDATE `rundentyp` SET `Code` = 'S' WHERE `xRundentyp` =6 LIMIT 1 ");
				mysql_query("UPDATE `rundentyp` SET `Code` = 'X' WHERE `xRundentyp` =7 LIMIT 1 ");
				mysql_query("UPDATE `rundentyp` SET `Code` = 'D' WHERE `xRundentyp` =8 LIMIT 1 ");
				mysql_query("UPDATE `rundentyp` SET `Code` = '0' WHERE `xRundentyp` =9 LIMIT 1 ");
			}
			
			// since 1.9 the categories hava a gender
			if($shortVersion < 1.9){
				mysql_query("UPDATE kategorie SET
						Geschlecht = 'w' 
					WHERE Code = 'WOM_' 
						OR Code = 'U23W' 
						OR Code = 'U20W' 
						OR Code = 'U18W' 
						OR Code = 'U16W' 
						OR Code = 'U14W' 
						OR Code = 'U12W'");
			}
                                    			
			// new categories U10M and U10W and disciplines BALL80 and 300H91.4 since 3.0
			if($shortVersion < 3.1){
				// categories
				mysql_query("INSERT IGNORE INTO kategorie 
											   (xKategorie
												, Kurzname
												, Name
												, Anzeige
												, Alterslimite
												, Code
												, Geschlecht)
										VALUES (''
												, 'U10M'
												, 'U10 M'
												, '7'
												, '9'
												, 'U10M'
												, 'm');");
				mysql_query("INSERT IGNORE INTO kategorie 
											   (xKategorie
												, Kurzname
												, Name
												, Anzeige
												, Alterslimite
												, Code
												, Geschlecht)
										VALUES (''
												, 'U10W'
												, 'U10 W'
												, '15'
												, '18'
												, 'U10W'
												, 'w');");
												
				// disciplines
				mysql_query("INSERT IGNORE INTO disziplin 
											   (xDisziplin
												, Kurzname
												, Name
												, Anzeige
												, Seriegroesse
												, Staffellaeufer
												, Typ
												, Appellzeit
												, Stellzeit
												, Strecke
												, Code
												, xOMEGA_Typ)
										VALUES ('', 
												'BALL80'
												, 'Ball 80 g'
												, '385'
												, '6'
												, '0' 
												, '8'
												, '01:00:00'
												, '00:20:00'
												, '0'
												, '385'
												, '1');");
				mysql_query("UPDATE disziplin 
								SET Code = 385 
							  WHERE Anzeige = 385 
								AND Kurzname = 'BALL80';");
								
				mysql_query("INSERT IGNORE INTO disziplin 
											   (xDisziplin
												, Kurzname
												, Name
												, Anzeige
												, Seriegroesse
												, Staffellaeufer
												, Typ
												, Appellzeit
												, Stellzeit
												, Strecke
												, Code
												, xOMEGA_Typ)
										VALUES ('', 
												'300H91.4'
												, '300 m H�rden 91.4'
												, '289'
												, '6'
												, '0' 
												, '2'
												, '01:00:00'
												, '00:15:00'
												, '300'
												, '289'
												, '4');");
			}
			
			// new categories SENM and SENW
			if($shortVersion < 3.2){
				// categories
				mysql_query("INSERT IGNORE INTO kategorie 
											   (xKategorie
												, Kurzname
												, Name
												, Anzeige
												, Alterslimite
												, Code
												, Geschlecht)
										VALUES (''
												, 'MASM'
												, 'MASTERS M'
												, '2'
												, '99'
												, 'MASM'
												, 'm');");
				mysql_query("INSERT IGNORE INTO kategorie 
											   (xKategorie
												, Kurzname
												, Name
												, Anzeige
												, Alterslimite
												, Code
												, Geschlecht)
										VALUES (''
												, 'MASW'
												, 'MASTERS W'
												, '11'
												, '99'
												, 'MASW'
												, 'w');");
			} 
           
            if($shortVersion < 3.4){ 
                 // correct categories without gender
                 mysql_query("UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'MASW'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'w' WHERE Code = 'U10W'");  
                 
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'MAN_'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'MASM'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U23M'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U20M'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U18M'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U16M'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U14M'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U12M'");    
                 mysql_query("UPDATE kategorie SET Geschlecht = 'm' WHERE Code = 'U10M'");      
                 // special categories athletic cup
                 mysql_query("INSERT IGNORE INTO kategorie (Kurzname, Name, Anzeige, Alterslimite , Code, Geschlecht, aktiv) VALUES 
                            ( 'M15', 'U16 M15', 21, 15, '' , 'm', 'n'),
                            ( 'M14', 'U16 M14', 22, 14, '' , 'm', 'n'),
                            ( 'M13', 'U14 M13', 23, 13, '' , 'm', 'n'),
                            ( 'M12', 'U14 M12', 24, 12, '' , 'm', 'n'),
                            ( 'M11', 'U12 M11', 25, 11, '' , 'm', 'n'),
                            ( 'M10', 'U12 M10', 26, 10, '' , 'm', 'n'),
                            ( 'M09', 'U10 M09', 27, 9, '' , 'm', 'n'), 
                            ( 'M08', 'U10 M08', 28, 8, '' , 'm', 'n'), 
                            ( 'M07', 'U08 M07', 29, 7, '' , 'm', 'n'), 
                            ( 'W15', 'U16 W15', 31, 15, '' , 'w', 'n'),
                            ( 'W14', 'U16 W14', 32, 14, '' , 'w', 'n'),
                            ( 'W13', 'U14 W13', 33, 13, '' , 'w', 'n'),
                            ( 'W12', 'U14 W12', 34, 12, '' , 'w', 'n'),
                            ( 'W11', 'U12 W11', 35, 11, '' , 'w', 'n'),
                            ( 'W10', 'U12 W10', 36, 10, '' , 'w', 'n'),
                            ( 'W09', 'U10 W09', 37, 9, '' , 'w', 'n'), 
                            ( 'W08', 'U10 W08', 38, 8, '' , 'w', 'n'), 
                            ( 'W07', 'U08 W07', 39, 7, '' , 'w', 'n')");   
                 
                  mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('...KAMPF', '...kampf', 393, 6, 0, 9, '01:00:00','00:15:00', 4, 799, 1)");        
            }  
            
           if($shortVersion < 3.5){
                // disciplines
                mysql_query("UPDATE `disziplin` SET Code = 505 ,Anzeige = 505 WHERE xDisziplin = 159");  
                
                mysql_query("UPDATE `disziplin` SET Name = 'Erdgas Athletic Cup', Kurzname = 'AC' WHERE Code = 403");   
              
                // new discipline code and new sort order
                mysql_query("UPDATE `disziplin` SET Name = 'Athletic Cup', Kurzname = 'AC' WHERE Code = 403");   
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('75', '75 m', 31, 6, 0, 1, '01:00:00','00:15:00', 75, 31, 1)"); 
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('50H68.6', '50 m H�rden 68.6', 237, 6, 0, 2, '01:00:00','00:15:00', 50, 237, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('60H68.6', '60 m H�rden 68.6', 257, 6, 0, 2, '01:00:00','00:15:00', 60, 257, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('80H84.0', '80 m H�rden 84.0', 260, 6, 0, 1, '01:00:00','00:15:00', 80, 260, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('80H68.6', '80 m H�rden 68.6', 262, 6, 0, 1, '01:00:00','00:15:00', 80, 262, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('300H68.6', '300 m H�rden 68.6', 292, 6, 0, 2, '01:00:00','00:15:00', 300, 292, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('SPEER500', 'Speer 500 gr', 390, 6, 0, 8, '01:00:00','00:20:00', 0, 390, 1)");  
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('5KAMPF_M', 'F�nfkampf M', 415, 6, 0, 9, '01:00:00','00:15:00', 5, 392, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('5KAMPF_U20M', 'F�nfkampf U20 M', 416, 6, 0, 9, '01:00:00','00:15:00', 5, 393, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('5KAMPF_U18M', 'F�nfkampf U18 M', 417, 6, 0, 9, '01:00:00','00:15:00', 5, 405, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('5KAMPF_W', 'F�nfkampf W', 420, 6, 0, 9, '01:00:00','00:15:00', 5, 416, 1)");   
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('5KAMPF_U20W', 'F�nfkampf U20 W', 421, 6, 0, 9, '01:00:00','00:15:00', 5, 417, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('5KAMPF_U18W', 'F�nfkampf U18 W', 422, 6, 0, 9, '01:00:00','00:15:00', 5, 418, 1)");   
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('10KAMPF_MM', 'Zehnkampf MM', 414, 6, 0, 9, '01:00:00','00:15:00', 10, 414, 1)");  
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('2000WALK', '2000 m walk', 419, 6, 0, 7, '01:00:00','00:15:00', 2000, 419, 1)");    
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('...LAUF', '...lauf', 796, 6, 0, 9, '01:00:00','00:15:00', 4, 796, 1)");   
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('...SPRUNG', '...sprung', 797, 6, 0, 9, '01:00:00','00:15:00', 4, 797, 1)");   
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('...WURF', '...wurf', 798, 6, 0, 9, '01:00:00','00:15:00', 4, 798, 1)"); 
                mysql_query("INSERT INTO `disziplin` (Kurzname,Name,Anzeige,Seriegroesse,Staffellaeufer,Typ,Appellzeit,Stellzeit,Strecke,Code, xOMEGA_Typ) VALUES 
                       ('WEIT Z', 'Weit (Zone)', 331, 6, 0, 4, '01:00:00','00:40:00', 0, 331, 1)");   
                
                mysql_query("DELETE FROM `disziplin` WHERE Code = 201");   
                mysql_query("DELETE FROM `disziplin` WHERE Code = 481");   
                mysql_query("DELETE FROM `disziplin` WHERE Code = 495");   
                mysql_query("DELETE FROM `disziplin` WHERE Code = 496");   
                mysql_query("DELETE FROM `disziplin` WHERE Code = 510");   
                mysql_query("DELETE FROM `disziplin` WHERE Code = 540");   
                mysql_query("DELETE FROM `disziplin` WHERE Code = 558");    
                
                       
                mysql_query("UPDATE `disziplin` SET Anzeige = 171 WHERE Code = 182");    
                mysql_query("UPDATE `disziplin` SET Anzeige = 183 WHERE Code = 190");      
                mysql_query("UPDATE `disziplin` SET Anzeige = 182 WHERE Code = 195");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 184 WHERE Code = 200");    
                mysql_query("UPDATE `disziplin` SET Anzeige = 302 WHERE Code = 209");      
                mysql_query("UPDATE `disziplin` SET Anzeige = 303 WHERE Code = 210");      
                mysql_query("UPDATE `disziplin` SET Anzeige = 304 WHERE Code = 220");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 259 WHERE Code = 258");      
                mysql_query("UPDATE `disziplin` SET Anzeige = 262 WHERE Code = 259");      
                mysql_query("UPDATE `disziplin` SET Anzeige = 184 WHERE Code = 200");  
                mysql_query("UPDATE `disziplin` SET Anzeige = 267 WHERE Code = 271");  
                mysql_query("UPDATE `disziplin` SET Anzeige = 258 WHERE Code = 260");     
                mysql_query("UPDATE `disziplin` SET Anzeige = 260 WHERE Code = 262");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 269 WHERE Code = 268");        
                mysql_query("UPDATE `disziplin` SET Anzeige = 268 WHERE Code = 269");        
                mysql_query("UPDATE `disziplin` SET Anzeige = 301 WHERE Code = 298");        
                mysql_query("UPDATE `disziplin` SET Anzeige = 298 WHERE Code = 301");        
                mysql_query("UPDATE `disziplin` SET Anzeige = 349 WHERE Code = 347");        
                mysql_query("UPDATE `disziplin` SET Anzeige = 350 WHERE Code = 349");    
                mysql_query("UPDATE `disziplin` SET Anzeige = 347 WHERE Code = 351"); 
                mysql_query("UPDATE `disziplin` SET Anzeige = 361 WHERE Code = 356");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 359 WHERE Code = 357");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 357 WHERE Code = 359");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 356 WHERE Code = 361");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 381 WHERE Code = 375");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 378 WHERE Code = 376");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 376 WHERE Code = 378");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 375 WHERE Code = 381");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 393 WHERE Code = 385");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 392 WHERE Code = 386");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 391 WHERE Code = 387");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 389 WHERE Code = 388");                  
                mysql_query("UPDATE `disziplin` SET Anzeige = 388 WHERE Code = 389");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 387 WHERE Code = 391");    
                mysql_query("UPDATE `disziplin` SET Anzeige = 410 WHERE Code = 394");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 411 WHERE Code = 395");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 412 WHERE Code = 396");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 413 WHERE Code = 397");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 414 WHERE Code = 398");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 423 WHERE Code = 399");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 425 WHERE Code = 400");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 426 WHERE Code = 401");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 424 WHERE Code = 402");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 435 WHERE Code = 403");    
                mysql_query("UPDATE `disziplin` SET Anzeige = 430 WHERE Code = 410");                 
                mysql_query("UPDATE `disziplin` SET Anzeige = 431 WHERE Code = 411");                 
                mysql_query("UPDATE `disziplin` SET Anzeige = 432 WHERE Code = 412");                 
                mysql_query("UPDATE `disziplin` SET Anzeige = 433 WHERE Code = 413");                 
                mysql_query("UPDATE `disziplin` SET Anzeige = 434 WHERE Code = 414");    
                mysql_query("UPDATE `disziplin` SET Anzeige = 395 WHERE Code = 497");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 396 WHERE Code = 498");                 
                mysql_query("UPDATE `disziplin` SET Anzeige = 394 WHERE Code = 499");                 
                mysql_query("UPDATE `disziplin` SET Anzeige = 397 WHERE Code = 560");                 
                mysql_query("UPDATE `disziplin` SET Anzeige = 398 WHERE Code = 570");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 399 WHERE Code = 580");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 400 WHERE Code = 589");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 401 WHERE Code = 590");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 402 WHERE Code = 595");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 403 WHERE Code = 600");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 404 WHERE Code = 601");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 405 WHERE Code = 602");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 440, Kurzname = '10KM' WHERE Code = 491");                
                mysql_query("UPDATE `disziplin` SET Anzeige = 441 WHERE Code = 494");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 442 WHERE Code = 501");                
                mysql_query("UPDATE `disziplin` SET Anzeige = 443 WHERE Code = 505");                
                mysql_query("UPDATE `disziplin` SET Anzeige = 444 WHERE Code = 511");                
                mysql_query("UPDATE `disziplin` SET Anzeige = 451 WHERE Code = 419");                
                mysql_query("UPDATE `disziplin` SET Anzeige = 452 WHERE Code = 420");                
                mysql_query("UPDATE `disziplin` SET Anzeige = 453 WHERE Code = 430");                
                mysql_query("UPDATE `disziplin` SET Anzeige = 454 WHERE Code = 440");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 455 WHERE Code = 450");                
                mysql_query("UPDATE `disziplin` SET Anzeige = 456 WHERE Code = 460");   
                mysql_query("UPDATE `disziplin` SET Anzeige = 450 WHERE Code = 415");  
                mysql_query("UPDATE `disziplin` SET Anzeige = 457 WHERE Code = 559"); 
                mysql_query("UPDATE `disziplin` SET Anzeige = 799 WHERE Code = 799"); 
                
               // new svm categories 2010 
               mysql_query("TRUNCATE TABLE kategorie_svm;");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (1, '29.01 Nationalliga A M�nner', '29_01')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (2, '29.02 Nationalliga A Frauen', '29_02')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (3, '30.01 Nationalliga B M�nner', '30_01')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (4, '30.02 Nationalliga B Frauen', '30_02')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (5, '31.01 Nationalliga C M�nner', '31_01')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (6, '31.02 Nationalliga C Frauen', '31_02')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (7, '32.01 Regionalliga Ost M�nner', '32_01')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (8, '32.02 Regionalliga West M�nner', '32_02')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (9, '32.03 Regionalliga Ost Frauen', '32_03')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (10, '32.04 Regionalliga West Frauen', '32_04')");    
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (11, '33.01 Junior Liga A M�nner', '33_01')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (12, '33.02 Junior Liga B M�nner', '33_02')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (13, '33.03 Junior Liga A Frauen', '33_03')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (14, '33.04 Junior Liga B Frauen', '33_04')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (15, '35.01 M30 und �lter M�nner', '35_01')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (16, '35.02 U18 M', '35_02')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (17, '35.03 U18 M Mehrkampf', '35_03')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (18, '35.04 U16 M', '35_04')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (19, '35.05 U16 M Mehrkampf', '35_05')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (20, '35.06 U14 M', '35_06')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (21, '35.07 U14 M Mannschaftswettkampf', '35_07')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (22, '35.08 U12 M Mannschaftswettkampf', '35_08')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (23, '36.01 W30 und �lter Frauen', '36_01')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (24, '36.02 U18 W', '36_02')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (25, '36.03 U18 W Mehrkampf', '36_03')");    
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (26, '36.04 U16 W', '36_04')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (27, '36.05 U16 W Mehrkampf', '36_05')"); 
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (28, '36.06 U14 W', '36_06')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (29, '36.07 U14 W Mannschaftswettkampf', '36_07')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (30, '36.08 U12 W Mannschaftswettkampf', '36_08')");  
               mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (31, '36.09 Mixed Team U12 M und U12 W', '36_09')");                                          
                                  
            }
            if($shortVersion < 4.0){  
                    mysql_query("UPDATE `disziplin` SET Typ = 5 WHERE Code = 331"); 
                    
                    mysql_query("TRUNCATE TABLE kategorie_svm;");  
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (1, '29.01 Nationalliga A M�nner', '29_01')"); 
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (2, '29.02 Nationalliga A Frauen', '29_02')");   
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (3, '30.01 Nationalliga B M�nner', '30_01')");    
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (4, '30.02 Nationalliga B Frauen', '30_02')");   
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (5, '31.01 Nationalliga C M�nner', '31_01')");   
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (6, '31.02 Nationalliga C Frauen', '31_02')");  
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (7, '32.01 Regionalliga Ost M�nner', '32_01')");  
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (8, '32.02 Regionalliga West M�nner', '32_02')");    
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (9, '32.03 Regionalliga Ost Frauen', '32_03')");   
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (10, '32.04 Regionalliga West Frauen', '32_04')");   
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (11, '32.05 Regionalliga Mitte M�nner', '32_05')");   
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (12, '32.06 Regionalliga Mitte Frauen', '32_06')");     
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (13, '33.01 Junior Liga A M�nner', '33_01')");     
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (14, '33.02 Junior Liga B M�nner', '33_02')");    
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (15, '33.03 Junior Liga A Frauen', '33_03')");    
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (16, '33.04 Junior Liga B Frauen', '33_04')");     
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (17, '33.05 Junior Liga C M�nner', '33_05')"); 
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                                (18, '33.06 Junior Liga C Frauen', '33_06')"); 
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (19, '35.01 M30 und �lter M�nner', '35_01')");       
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (20, '35.02 U18 M', '35_02')");             
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (21, '35.03 U18 M Mehrkampf', '35_03')");       
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (22, '35.04 U16 M', '35_04')");    
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (23, '35.05 U16 M Mehrkampf', '35_05')");      
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (24, '35.06 U14 M', '35_06')");     
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (25, '35.07 U14 M Mannschaftswettkampf', '35_07')");    
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (26, '35.08 U12 M Mannschaftswettkampf', '35_08')");    
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (27, '36.01 W30 und �lter Frauen', '36_01')");       
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (28, '36.02 U18 W', '36_02')");    
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (29, '36.03 U18 W Mehrkampf', '36_03')");     
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (30, '36.04 U16 W', '36_04')");     
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (31, '36.05 U16 W Mehrkampf', '36_05')");
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (32, '36.06 U14 W', '36_06')");     
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (33, '36.07 U14 W Mannschaftswettkampf', '36_07')");       
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (34, '36.08 U12 W Mannschaftswettkampf', '36_08')");      
                    mysql_query("INSERT INTO kategorie_svm (xKategorie_svm, Name, Code) VALUES 
                               (35, '36.09 Mixed Team U12 M und U12 W', '36_09')");
             
			}
			// security updates
			mysql_query("UPDATE kategorie 
							SET Code = 'U10M' 
						  WHERE Kurzname = 'U10M';");
			mysql_query("UPDATE kategorie 
							SET Code = 'U10W' 
						  WHERE Kurzname = 'U10W';");
						  
			mysql_query("UPDATE kategorie 
							SET Kurzname = 'MASM', 
								Name = 'MASTERS M', 
								Code = 'MASM' 
						  WHERE Kurzname = 'SENM';");
			mysql_query("UPDATE kategorie 
							SET Kurzname = 'MASW', 
								Name = 'MASTERS W', 
								Code = 'MASW' 
						  WHERE Kurzname = 'SENW';");
			
			// update nations for all backups
			mysql_query("TRUNCATE TABLE land;");
			mysql_query("INSERT INTO land(xCode, Name, Sortierwert) VALUES 
									 ('SUI', 'Switzerland', 1),
									 ('AFG', 'Afghanistan', 2),
									 ('ALB', 'Albania', 3),
									 ('ALG', 'Algeria', 4),
									 ('ASA', 'American Samoa', 5),
									 ('AND', 'Andorra', 6),
									 ('ANG', 'Angola', 7),
									 ('AIA', 'Anguilla', 8),
									 ('ANT', 'Antigua & Barbuda', 9),
									 ('ARG', 'Argentina', 10),
									 ('ARM', 'Armenia', 11),
									 ('ARU', 'Aruba', 12),
									 ('AUS', 'Australia', 13),
									 ('AUT', 'Austria', 14),
									 ('AZE', 'Azerbaijan', 15),
									 ('BAH', 'Bahamas', 16),
									 ('BRN', 'Bahrain', 17),
									 ('BAN', 'Bangladesh', 18),
									 ('BAR', 'Barbados', 19),
									 ('BLR', 'Belarus', 20),
									 ('BEL', 'Belgium', 21),
									 ('BIZ', 'Belize', 22),
									 ('BEN', 'Benin', 23),
									 ('BER', 'Bermuda', 24),
									 ('BHU', 'Bhutan', 25),
									 ('BOL', 'Bolivia', 26),
									 ('BIH', 'Bosnia Herzegovina', 27),
									 ('BOT', 'Botswana', 28),
									 ('BRA', 'Brazil', 29),
									 ('BRU', 'Brunei', 30),
									 ('BUL', 'Bulgaria', 31),
									 ('BRK', 'Burkina Faso', 32),
									 ('BDI', 'Burundi', 33),
									 ('CAM', 'Cambodia', 34),
									 ('CMR', 'Cameroon', 35),
									 ('CAN', 'Canada', 36),
									 ('CPV', 'Cape Verde Islands', 37),
									 ('CAY', 'Cayman Islands', 38),
									 ('CAF', 'Central African Republic', 39),
									 ('CHA', 'Chad', 40),
									 ('CHI', 'Chile', 41),
									 ('CHN', 'China', 42),
									 ('COL', 'Colombia', 43),
									 ('COM', 'Comoros', 44),
									 ('CGO', 'Congo', 45),
									 ('COD', 'Congo [Zaire]', 46),
									 ('COK', 'Cook Islands', 47),
									 ('CRC', 'Costa Rica', 48),
									 ('CIV', 'Ivory Coast', 49),
									 ('CRO', 'Croatia', 50),
									 ('CUB', 'Cuba', 51),
									 ('CYP', 'Cyprus', 52),
									 ('CZE', 'Czech Republic', 53),
									 ('DEN', 'Denmark', 54),
									 ('DJI', 'Djibouti', 55),
									 ('DMA', 'Dominica', 56),
									 ('DOM', 'Dominican Republic', 57),
									 ('TLS', 'East Timor', 58),
									 ('ECU', 'Ecuador', 59),
									 ('EGY', 'Egypt', 60),
									 ('ESA', 'El Salvador', 61),
									 ('GEQ', 'Equatorial Guinea', 62),
									 ('ERI', 'Eritrea', 63),
									 ('EST', 'Estonia', 64),
									 ('ETH', 'Ethiopia', 65),
									 ('FIJ', 'Fiji', 66),
									 ('FIN', 'Finland', 67),
									 ('FRA', 'France', 68),
									 ('GAB', 'Gabon', 69),
									 ('GAM', 'Gambia', 70),
									 ('GEO', 'Georgia', 71),
									 ('GER', 'Germany', 72),
									 ('GHA', 'Ghana', 73),
									 ('GIB', 'Gibraltar', 74),
									 ('GBR', 'Great Britain & NI', 75),
									 ('GRE', 'Greece', 76),
									 ('GRN', 'Grenada', 77),
									 ('GUM', 'Guam', 78),
									 ('GUA', 'Guatemala', 79),
									 ('GUI', 'Guinea', 80),
									 ('GBS', 'Guinea-Bissau', 81),
									 ('GUY', 'Guyana', 82),
									 ('HAI', 'Haiti', 83),
									 ('HON', 'Honduras', 84),
									 ('HKG', 'Hong Kong', 85),
									 ('HUN', 'Hungary', 86),
									 ('ISL', 'Iceland', 87),
									 ('IND', 'India', 88),
									 ('INA', 'Indonesia', 89),
									 ('IRI', 'Iran', 90),
									 ('IRQ', 'Iraq', 91),
									 ('IRL', 'Ireland', 92),
									 ('ISR', 'Israel', 93),
									 ('ITA', 'Italy', 94),
									 ('JAM', 'Jamaica', 95),
									 ('JPN', 'Japan', 96),
									 ('JOR', 'Jordan', 97),
									 ('KAZ', 'Kazakhstan', 98),
									 ('KEN', 'Kenya', 99),
									 ('KIR', 'Kiribati', 100),
									 ('KOR', 'Korea', 101),
									 ('KUW', 'Kuwait', 102),
									 ('KGZ', 'Kirgizstan', 103),
									 ('LAO', 'Laos', 104),
									 ('LAT', 'Latvia', 105),
									 ('LIB', 'Lebanon', 106),
									 ('LES', 'Lesotho', 107),
									 ('LBR', 'Liberia', 108),
									 ('LIE', 'Liechtenstein', 109),
									 ('LTU', 'Lithuania', 110),
									 ('LUX', 'Luxembourg', 111),
									 ('LBA', 'Libya', 112),
									 ('MAC', 'Macao', 113),
									 ('MKD', 'Macedonia', 114),
									 ('MAD', 'Madagascar', 115),
									 ('MAW', 'Malawi', 116),
									 ('MAS', 'Malaysia', 117),
									 ('MDV', 'Maldives', 118),
									 ('MLI', 'Mali', 119),
									 ('MLT', 'Malta', 120),
									 ('MSH', 'Marshall Islands', 121),
									 ('MTN', 'Mauritania', 122),
									 ('MRI', 'Mauritius', 123),
									 ('MEX', 'Mexico', 124),
									 ('FSM', 'Micronesia', 125),
									 ('MDA', 'Moldova', 126),
									 ('MON', 'Monaco', 127),
									 ('MGL', 'Mongolia', 128),
									 ('MNE', 'Montenegro', 129),
									 ('MNT', 'Montserrat', 130),
									 ('MAR', 'Morocco', 131),
									 ('MOZ', 'Mozambique', 132),
									 ('MYA', 'Myanmar [Burma]', 133),
									 ('NAM', 'Namibia', 134),
									 ('NRU', 'Nauru', 135),
									 ('NEP', 'Nepal', 136),
									 ('NED', 'Netherlands', 137),
									 ('AHO', 'Netherlands Antilles', 138),
									 ('NZL', 'New Zealand', 139),
									 ('NCA', 'Nicaragua', 140),
									 ('NIG', 'Niger', 141),
									 ('NGR', 'Nigeria', 142),
									 ('NFI', 'Norfolk Islands', 143),
									 ('PRK', 'North Korea', 144),
									 ('NOR', 'Norway', 145),
									 ('OMN', 'Oman', 146),
									 ('PAK', 'Pakistan', 147),
									 ('PLW', 'Palau', 148),
									 ('PLE', 'Palestine', 149),
									 ('PAN', 'Panama', 150),
									 ('NGU', 'Papua New Guinea', 151),
									 ('PAR', 'Paraguay', 152),
									 ('PER', 'Peru', 153),
									 ('PHI', 'Philippines', 154),
									 ('POL', 'Poland', 155),
									 ('POR', 'Portugal', 156),
									 ('PUR', 'Puerto Rico', 157),
									 ('QAT', 'Qatar', 158),
									 ('ROM', 'Romania', 159),
									 ('RUS', 'Russia', 160),
									 ('RWA', 'Rwanda', 161),
									 ('SMR', 'San Marino', 162),
									 ('STP', 'S�o Tome & Princip�', 163),
									 ('KSA', 'Saudi Arabia', 164),
									 ('SEN', 'Senegal', 165),
									 ('SRB', 'Serbia', 166),
									 ('SEY', 'Seychelles', 167),
									 ('SLE', 'Sierra Leone', 168),
									 ('SIN', 'Singapore', 169),
									 ('SVK', 'Slovakia', 170),
									 ('SLO', 'Slovenia', 171),
									 ('SOL', 'Solomon Islands', 172),
									 ('SOM', 'Somalia', 173),
									 ('RSA', 'South Africa', 174),
									 ('ESP', 'Spain', 175),
									 ('SKN', 'St. Kitts & Nevis', 176),
									 ('SRI', 'Sri Lanka', 177),
									 ('LCA', 'St. Lucia', 178),
									 ('VIN', 'St. Vincent & the Grenadines', 179),
									 ('SUD', 'Sudan', 180),
									 ('SUR', 'Surinam', 181),
									 ('SWZ', 'Swaziland', 182),
									 ('SWE', 'Sweden', 183),
									 ('SYR', 'Syria', 185),
									 ('TAH', 'Tahiti', 186),
									 ('TPE', 'Taiwan', 187),
									 ('TAD', 'Tadjikistan', 188),
									 ('TAN', 'Tanzania', 189),
									 ('THA', 'Thailand', 190),
									 ('TOG', 'Togo', 191),
									 ('TGA', 'Tonga', 192),
									 ('TRI', 'Trinidad & Tobago', 193),
									 ('TUN', 'Tunisia', 194),
									 ('TUR', 'Turkey', 195),
									 ('TKM', 'Turkmenistan', 196),
									 ('TKS', 'Turks & Caicos Islands', 197),
									 ('UGA', 'Uganda', 198),
									 ('UKR', 'Ukraine', 199),
									 ('UAE', 'United Arab Emirates', 200),
									 ('USA', 'United States', 201),
									 ('URU', 'Uruguay', 202),
									 ('UZB', 'Uzbekistan', 203),
									 ('VAN', 'Vanuatu', 204),
									 ('VEN', 'Venezuela', 205),
									 ('VIE', 'Vietnam', 206),
									 ('ISV', 'Virgin Islands', 207),
									 ('SAM', 'Western Samoa', 208),
									 ('YEM', 'Yemen', 209),
									 ('ZAM', 'Zambia', 210),
									 ('ZIM', 'Zimbabwe', 211);");
			
			// check AUTO_INCREMENT (min. 100) of Wertungstabelle
			$sql_wt = "SELECT xWertungstabelle 
						 FROM wertungstabelle 
						WHERE xWertungstabelle < 100;";
			$query_wt = mysql_query($sql_wt);
			
			while($row_wt = mysql_fetch_assoc($query_wt)){
				$sql_max = "SELECT MAX(xWertungstabelle) AS max_id 
							  FROM wertungstabelle;";
				$query_max = mysql_query($sql_max);
				$max_id = (mysql_result($query_max, 0, 'max_id')>=100) ? mysql_result($query_max, 0, 'max_id') : 99;
				$new_id = ($max_id + 1);
				
				$sql_up = "UPDATE wertungstabelle 
							  SET xWertungstabelle = ".$new_id." 
							WHERE xWertungstabelle = ".$row_wt['xWertungstabelle'].";";
				$query_up = mysql_query($sql_up);
				
				if($query_up){
					$sql_up2 = "UPDATE wertungstabelle_punkte 
								   SET xWertungstabelle = ".$new_id." 
								 WHERE xWertungstabelle = ".$row_wt['xWertungstabelle'].";";
					$query_up2 = mysql_query($sql_up2);
				}
			}
			
			$sql_max = "SELECT MAX(xWertungstabelle) AS max_id 
						  FROM wertungstabelle;";
			$query_max = mysql_query($sql_max);
			$max_id = (mysql_num_rows($query_max)==1 && mysql_result($query_max, 0, 'max_id')>0) ? mysql_result($query_max, 0, 'max_id') : 99;
			$new_id = ($max_id + 1);
			
			$sql_ai = "ALTER TABLE wertungstabelle 
								   AUTO_INCREMENT = ".$new_id.";";
			$query_ai = mysql_query($sql_ai);
			
			
			// ACHTUNG:
			// Tempor�re �nderung (Fredy Mollet): Hallen-Flag bei den Stadien auf "n" setzen (damit bei den Punkten kein i erscheint)
			$sql_st = "UPDATE stadion 
						  SET Halle = 'n';";
			$query_st = mysql_query($sql_st);
			
			
			// Zeitmessungspfade pr�fen
			$sql_zd = "DELETE zeitmessung.* 
						 FROM zeitmessung 
					LEFT JOIN meeting USING(xMeeting) 
						WHERE Name = '' 
						   OR Name IS NULL;";
			$query_zd = mysql_query($sql_zd);
			
			$sql_z = "SELECT xZeitmessung, 
							 OMEGA_Pfad, 
							 ALGE_Pfad 
						FROM zeitmessung;";
			$query_z = mysql_query($sql_z);
			
			while($zeitmessung = mysql_fetch_assoc($query_z)){
				$err_this = false;
				$omega = $zeitmessung['OMEGA_Pfad'];
				$alge = $zeitmessung['ALGE_Pfad'];
				
				if($zeitmessung['OMEGA_Pfad']!=''){
					$path = stripslashes($zeitmessung['OMEGA_Pfad']);
					
					$fp = @fopen($p."/test.txt",'w');
					if(!$fp){
						$error = true;
						$err_this = true;
						$timing_errors++;
						$omega = '';
						
						AA_printErrorMsg($strOmegaNoPathBackup);
					}
				}
				if($zeitmessung['ALGE_Pfad']!=''){
					$path = stripslashes($zeitmessung['ALGE_Pfad']);
					
					$fp = @fopen($p."/test.txt",'w');
					if(!$fp){
						$error = true;
						$err_this = true;
						$timing_errors++;
						$alge = '';
						
						AA_printErrorMsg($strAlgeNoPathBackup);
					}
				}
				
				if($err_this){
					$sql_zu = "UPDATE zeitmessung 
								  SET OMEGA_Pfad = '".$omega."', 
									  ALGE_Pfad = '".$alge."' 
								WHERE xZeitmessung = ".$zeitmessung['xZeitmessung'].";";
					$query_zu = mysql_query($sql_zu);
				}
			}
			
		}
		
		
		// output information about number of truncate and insert statements
		echo "<tr><td class='dialog'>";
		if ($skipped_basetables == true){
			echo "<br><br><br>".$strBackupBaseTablesSkipped." ";?><input type="button" value="<?=$strBaseUpdate?>" class="baseupdatebutton" onclick="javascript:document.location.href='admin_base.php'"><?php
		}
		if ($timing_errors>0){
			echo '<br><br><br><b style="color: #FF0000">'.$strBackupTimingReset.'</b>';
		}
		echo "</td></tr>";

		
		if(!$error){
			echo "<tr><th class='dialog'>$strBackupSucceeded</th></tr>";
			
			setcookie("meeting_id", "", time()-3600);
			setcookie("meeting", "", time()-3600);
			if(isset($_SESSION['meeting_infos'])){
				unset($_SESSION['meeting_infos']);
			}
			
			$sql = "SELECT * 
					  FROM meeting;";
			$query = mysql_query($sql);
			
			if($query && mysql_num_rows($query)==1){
				$row = mysql_fetch_assoc($query);
				
				// store cookies on browser
				setcookie("meeting_id", $row['xMeeting'], time()+$cfgCookieExpires);
				setcookie("meeting", stripslashes($row['Name']), time()+$cfgCookieExpires);
				// update current cookies
				$_COOKIE['meeting_id'] = $row['xMeeting'];
				$_COOKIE['meeting'] = stripslashes($row['Name']);
				
				$_SESSION['meeting_infos'] = $row;
			}
		}
		
	}	// ET invalid backup ID
	
	fclose($fd);
	
	?>
</table>
	<?php
	
	$page->endPage();

}

function strToNTFSFilename($string)
{
  $reserved = preg_quote('\/:*?"<>', '/');
  return preg_replace("/([\\x00-\\x1f{$forbidden}])/e", "_", $string);
}
?>
