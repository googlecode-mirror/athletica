<?php

/**********
 *
 *	meeting_entry.php
 *	-----------------
 *	
 */
		
require('./lib/cl_gui_button.lib.php');
require('./lib/cl_gui_dropdown.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');
require('./lib/cl_performance.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

//
// check if a heat is assigned
//
$heats_done = "false";
$res = mysql_query("
		SELECT xRunde FROM
			runde 
			LEFT JOIN wettkampf USING (xWettkampf)
		WHERE
			(Status = ".$cfgRoundStatus['heats_done']."
			OR Status = ".$cfgRoundStatus['results_in_progress']."
			OR Status = ".$cfgRoundStatus['results_done'].")
			AND xMeeting = ".$_COOKIE['meeting_id']
);

if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	if(mysql_num_rows($res) > 0){
		$heats_done = "true";
	}
}



// prepare search argument
if($_POST['arg']=='search') {
	$name = '';
	$nbr = '';
	if(is_numeric($_POST['searchfield'])) {
		$nbr = $_POST['searchfield'];
	}	
	else {
		$name = $_POST['searchfield'];
	}
}


//
// Process change_startnbr-request if required
//
if ($_POST['arg']=="change_startnbr")
{
	// Error: Empty fields
	if(empty($_POST['nbr']))
	{
		AA_printErrorMsg($strErrEmptyFields);
	}
	// OK: try to change it if different
	else if ($_POST['nbr'] != $_POST['old_nbr'])
	{
		$nbrOK = TRUE;

		// test if nbr already used
		if($_POST['nbr'] > 0)
		{
			
			$nReg = false;
			$nRelay = false;
			$result = mysql_query("SELECT xAnmeldung "
						. " FROM anmeldung"
						. " WHERE xMeeting=" . $_COOKIE['meeting_id']
						. " AND Startnummer=" . $_POST['nbr']);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				$nbrOK = FALSE;
			}else{
				if(mysql_num_rows($result) > 0) { $nReg = true; }
			}
			mysql_free_result($result);
			
			// check if this nbr is used for a relay
			$result = mysql_query("SELECT xStaffel "
						. " FROM staffel"
						. " WHERE xMeeting=" . $_COOKIE['meeting_id']
						. " AND Startnummer=" . $_POST['nbr']);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				$nbrOK = FALSE;
			}else{
				if(mysql_num_rows($result) > 0) { $nRelay = true; }
			}
			mysql_free_result($result);
			
			if($nReg || $nRelay){
				AA_printErrorMsg($strStartnumberLong . $strErrNotValid);
				$nbrOK = FALSE;
			}
			
			/*$result = mysql_query("
				SELECT
					xAnmeldung 
				FROM
					anmeldung
				WHERE xMeeting = " . $_COOKIE['meeting_id'] . "
				AND Startnummer = " . $_POST['nbr']
			);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				$nbrOK = FALSE;
			}
			else if(mysql_num_rows($result) > 0)
			{
				AA_printErrorMsg($strStartnumberLong . $strErrNotValid);
				$nbrOK = FALSE;
			}*/
		}

		// update startnbr if everything OK
		if($nbrOK == TRUE)
		{
			mysql_query("
				UPDATE anmeldung SET
					Startnummer = " . $_POST['nbr'] . "
				WHERE xAnmeldung = " . $_POST['item']
			);
			if(mysql_errno() > 0)
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
		}
	}
}

//
// Change license number
//
if($_POST['arg'] == "change_licensenr"){
	
	if(!is_numeric($_POST['licensenr'])){
		AA_printErrorMsg($strErrLicenseNotValid);
	}else{
		
		// a license number forces license type normal and athleitca gen 'no'
		mysql_query("UPDATE athlet SET
				Lizenznummer = ".$_POST['licensenr']."
				, Lizenztyp = 1
				, Athleticagen = 'n'
			WHERE
				xAthlet = ".$_POST['athlete']);
		
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}
	}
}

//
// Change license type
//
if($_POST['arg'] == "change_licensetype"){
	
	if($_POST['licensetype'] == 1){ // should not become true if javascript went right
		$sql = "SELECT Lizenznummer 
				  FROM athlet 
				 WHERE xAthlet = ".$_POST['athlete'].";";
		$query = mysql_query($sql);
		
		$row = mysql_fetch_assoc($query);
		
		if(intval($row['Lizenznummer'])>0){
			$sql2 = "UPDATE athlet 
						SET Lizenztyp = 1, 
							Athleticagen = 'n' 
					  WHERE xAthlet = ".$_POST['athlete'].";";
			$query2 = mysql_query($sql2);
		} else {
			AA_printErrorMsg($strErrLicenseNotEntered);
		}
	}else{
		
		// a license type of 2 or 3 forces athletica gen 'yes' and license numer '0'
		mysql_query("UPDATE athlet SET
				Lizenztyp = ".$_POST['licensetype']."
				, Athleticagen = 'y'
				, Lizenznummer = 0
			WHERE
				xAthlet = ".$_POST['athlete']);
		
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}
	}
	
}

//
// Process change-request if required
//
if ($_POST['arg']=="change")
{
	// Error: Empty fields
	if(empty($_POST['name']) || empty($_POST['first'])
		|| empty($_POST['category']))
	{
		AA_printErrorMsg($strErrEmptyFields);
	}
	// OK: try to add item
	else
	{
		$birthday = "0000-00-00";
		if(!empty($_POST['day']) && !empty($_POST['month']) && !empty($_POST['year'])){
			$_POST['year'] = AA_setYearOfBirth($_POST['year']);
			//$_POST['day'] = printf("[%02d]",  $_POST['day']);
			//$_POST['month'] = printf("[%02d]",  $_POST['month']);
			$birthday = $_POST['year']."-".$_POST['month']."-".$_POST['day'];
		}else
		// correct two-digit year
		if(!empty($_POST['year'])) {
			$_POST['year'] = AA_setYearOfBirth($_POST['year']);
		}

		mysql_query("
			LOCK TABLES
				wettkampf READ
				, kategorie READ
				, meeting READ
				, team READ
				, anmeldung WRITE
				, athlet WRITE
		");
		
		$sqlSex = "";
		if(!empty($_POST['sex'])){
			$sqlSex = ", Geschlecht = '".$_POST['sex']."'";
		}

		if(AA_checkReference("kategorie", "xKategorie", $_POST['category']) == 0)	// Category does not exist (anymore)
		{
			AA_printErrorMsg($strCategory . $strErrNotValid);
		}
		else
		{
			if(AA_checkReference("meeting", "xMeeting", $_COOKIE['meeting_id']) == 0)	// Meeting does not exist (anymore)
			{
				AA_printErrorMsg($strMeeting . $strErrNotValid);
			}
			else
			{
				// Basic athlet data
				mysql_query("
					UPDATE athlet SET 
						Name='" . ($_POST['name']) . "'
						, Vorname='" . $_POST['first'] . "'
						, Jahrgang='" . $_POST['year'] . "'
						, Geburtstag='" . $birthday . "'
						$sqlSex
					WHERE xAthlet='" . $_POST['athlete'] . "'
				");
			}		// ET Meeting valid
		}		// ET Category valid
		// Check if any error returned from DB
		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		mysql_query("UNLOCK TABLES");
	}
}


//
// Process add_event-request if required
//
else if ($_POST['arg']=="add_event" || $_POST['arg']=="add_combined")
{
	$item = $_POST['item'];
	$events = array();
	
	if($_POST['arg']=="add_event"){
		$events[] = $_POST['event'];
	}else{ // add a combined event, get each discipline
		list($cCat, $cCode) = split('_', $_POST['event']);
		$res_comb = mysql_query("SELECT xWettkampf FROM
						wettkampf as w
					WHERE	w.Mehrkampfcode = $cCode
					AND	w.xKategorie = $cCat
					AND	w.xMeeting = ".$_COOKIE['meeting_id']
					);
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			while($row_comb = mysql_Fetch_array($res_comb)){
				$events[] = $row_comb[0];
			}
		}
		
		// get combined top perf
		
		$saison = $_SESSION['meeting_infos']['Saison'];
		if ($saison == ''){
			$saison = "O"; //if no saison is set take outdoor
		}
		/*$res = mysql_query("
			SELECT
				base_performance.notification_effort
			FROM
				base_performance
				, base_athlete
			WHERE	base_athlete.license = ".$_POST['license']."
			AND	base_performance.id_athlete = base_athlete.id_athlete
			AND	base_performance.discipline = ".$cCode);*/
		$res = mysql_query("SELECT 
					notification_effort 
				FROM 
					base_performance 
				LEFT JOIN base_athlete USING(id_athlete) 
				WHERE base_athlete.license = ".$_POST['license']." 
				AND base_performance.discipline = ".$cCode." 
				AND season = '".$saison."';");
		
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_array($res);
			mysql_query("UPDATE 
							anmeldung 
						SET 
							BestleistungMK = '".$row[0]."' 
							, BaseEffortMK = 'y'
						WHERE 
							xAnmeldung = ".$_POST['item']);
		}else{
			mysql_query("UPDATE 
							anmeldung 
						SET 
							BestleistungMK = 0 
							, BaseEffortMK = 'y'
						WHERE 
							xAnmeldung = ".$_POST['item']);
		}
	}
	
	
	mysql_query("LOCK TABLES anmeldung READ, disziplin READ, runde READ,"
		. " kategorie READ, base_athlete READ, base_performance READ, wettkampf READ, start READ, start WRITE");
	
	
	foreach($events as $event){
		if(AA_checkReference("wettkampf", "xWettkampf", $event) == 0)	// Event does not exist (anymore)
		{
			AA_printErrorMsg($strEvent . $strErrNotValid);
		}
		else
		{
			if(AA_checkReference("anmeldung", "xAnmeldung", $_POST['item']) == 0)	// Meeting does not exist (anymore)
			{
				AA_printErrorMsg($strEntry . $strErrNotValid);
			}
			else                     
			{   
				// check if event already started
				$res = mysql_query("SELECT disziplin.Name"
										. " FROM disziplin"
										. ", runde"
										. ", wettkampf"
										. " WHERE runde.xWettkampf=" . $event
										. " AND runde.Status > 0"
										. " AND runde.Status != ". $cfgRoundStatus['enrolement_pending']
										. " AND wettkampf.xWettkampf = " . $event
										. " AND disziplin.xDisziplin = wettkampf.xDisziplin");
	
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				// OK to add
				else {
					if (mysql_num_rows($res) > 0) {
						$row = mysql_fetch_row($res);
						AA_printErrorMsg($strWarningEventInProgress . $row[0]);
					}
					
					//
					// get performance from base data if searched for an athlete
					//
					$perf = 0;
					if($_POST['license'] != ''){    
						// need codes of category and discipline        // meine 3 zugefügt
						/*$sql ="
							SELECT disziplin.Code as DiszCode, 
								kategorie.Code as KatCode, 
								disziplin.Typ as Typ, 
								disziplin.xDisziplin, 
								kategorie.xKategorie,
								wettkampf.xMeeting  
							FROM
								disziplin
								, kategorie
								, wettkampf
							WHERE	wettkampf.xWettkampf = ".$event."
							AND	wettkampf.xDisziplin = disziplin.xDisziplin
							AND	wettkampf.xKategorie = kategorie.xKategorie";*/
							
						$sql = "SELECT disziplin.Code AS DiszCode, 
									   kategorie.Code AS KatCode, 
									   disziplin.Typ AS Typ, 
									   disziplin.xDisziplin, 
									   kategorie.xKategorie, 
									   wettkampf.xMeeting 
								  FROM disziplin 
									LEFT JOIN wettkampf ON(wettkampf.xDisziplin = disziplin.xDisziplin) 
									LEFT JOIN kategorie USING(xKategorie) 
								 WHERE wettkampf.xWettkampf = ".$event.";";
						$res = mysql_query($sql);
						
						if($res){						
							$rowCodes = mysql_fetch_array($res);
							
							$saison = $_SESSION['meeting_infos']['Saison'];
							if ($saison == ''){
								$saison = "O"; //if no saison is set take outdoor
							}
							
							$rowMeeting = mysql_fetch_array($res);

							/*$sql = "
								SELECT
									notification_effort
								FROM
									base_performance
									, base_athlete
								WHERE	base_athlete.license = ".$_POST['license']."
								AND	base_performance.id_athlete = base_athlete.id_athlete
								AND	base_performance.discipline = ".$rowCodes['DiszCode'] ."
								AND season = '$saison'";*/
							$sql = "SELECT notification_effort 
									  FROM base_performance 
								 LEFT JOIN base_athlete USING(id_athlete) 
									 WHERE base_athlete.license = ".$_POST['license']." 
									   AND base_performance.discipline = ".$rowCodes['DiszCode'] ." 
									   AND season = '".$saison."';";
							$res = mysql_query($sql); 

						}
						//echo $sql;
						if(mysql_errno() > 0){
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error() . $sql);
						}else{ 
							$rowPerf = mysql_fetch_array($res); 
							$perf = $rowPerf['notification_effort']; 
							
							if(($rowCodes['Typ'] == $cfgDisciplineType[$strDiscTypeTrack])
								|| ($rowCodes['Typ'] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
								|| ($rowCodes['Typ'] == $cfgDisciplineType[$strDiscTypeRelay])
								|| ($rowCodes['Typ'] == $cfgDisciplineType[$strDiscTypeDistance]))
								{                                             // disciplines track
								$pt = new PerformanceTime(trim($perf));
								$perf = $pt->getPerformance();
								$order="ASC";
								$best=AA_getBestPrevious($rowCodes['xDisziplin'], $_POST['item'] ,$order);  
							   
								if ($best!=0) {         // previous best exist
									if ($perf==0){
										$perf=$best;
									}
									elseif ($best<$perf)
										$perf=$best;                                             
								}                                 
							}
							else {                                        // disciplines tech
								$order="DESC";    
								$best=AA_getBestPrevious($rowCodes['xDisziplin'], $_POST['item'],$order);
																		  
								$perf = (ltrim($perf,"0"))*100;                                  
								
								if ($best!=0) {       // previous best exist
									if ($best>$perf )                        
										$perf=$best; 
								} 
							}
							if($perf == NULL) {	// invalid performance
								$perf = 0;
							}  
							
						}
					}     
					
					mysql_query("INSERT INTO start SET "
									 . " xWettkampf='" . $event
									 . "', xAnmeldung='" . $_POST['item'] . "'
									 , Bestleistung = $perf
									 , BaseEffort = 'y'");
					if(mysql_errno() > 0)
					{
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
				}			// ET Event already started
			}			// ET Entry valid
		}			// ET Event valid
	}
	
	mysql_query("UNLOCK TABLES");
}

//
// Process delete-request if required
//
else if ($_POST['arg']=="del_event" || $_POST['arg']=="del_combined")
{
	
	$item = $_POST['item'];
	$events = array();
	
	if($_POST['arg']=="del_event"){
		$events[] = $_POST['event'];
	}else{ // delete a combined event, get starts for each discipline
		list($cCat, $cCode) = split('_', $_POST['event']);
		$res_comb = mysql_query("SELECT xStart FROM
						start as s
					LEFT JOIN wettkampf as w USING (xWettkampf)
					WHERE	w.Mehrkampfcode = $cCode
					AND	w.xKategorie = $cCat
					AND	s.xAnmeldung = $item
					AND	w.xMeeting = ".$_COOKIE['meeting_id']
					);
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			while($row_comb = mysql_Fetch_array($res_comb)){
				$events[] = $row_comb[0];
			}
		}
	}
	
	mysql_query("LOCK TABLES serienstart READ, staffelathlet READ, start WRITE");
	foreach($events as $start){
		// Meeting does not exist (anymore)
		//echo $start;
		if((AA_checkReference("serienstart", "xStart", $start) == 0)	
			&& (AA_checkReference("staffelathlet", "xAthletenstart", $start) == 0))	
		{
			// Delete starts
			mysql_query("DELETE FROM start WHERE xStart=" . $start);
			if(mysql_errno() > 0)
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
		}
		else {
			AA_printErrorMsg($strErrAthleteSeeded);
		}
	}
	mysql_query("UNLOCK TABLES");
}



//
// Process change_cat-request if required
//
else if ($_POST['arg']=="change_cat")
{
	$category = $_POST['category'];

	// check if any starts
	$res = mysql_query("SELECT xStart"
						. " FROM start"
						. " WHERE xAnmeldung = " . $_POST['item']);

	/*if(mysql_num_rows($res) != 0)		// starts found
	{
		AA_printErrorMsg($strCategory . " " . $strErrStillUsed . " "
							. $strDeleteDisciplines);
		$category = $_POST['old_cat'];	// reuse old category
	}
	else				// no DB error
	{*/
		mysql_query("LOCK TABLES wettkampf READ, anmeldung as an WRITE, athlet as at WRITE, kategorie READ");

		$result = mysql_query("	SELECT
											xKategorie
										FROM
											wettkampf
										WHERE xKategorie = $category
										AND xMeeting = " . $_COOKIE['meeting_id']
									);

		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			/*if(mysql_num_rows($result) < 1)	// Category does not exist (anymore)
			{
				AA_printErrorMsg($strCategory . $strErrNotValid);
				$category = $_POST['old_cat'];	// reuse old category
			}
			else
			{*/
				mysql_query("	UPDATE anmeldung as an SET
										xKategorie = $category
										"/*, xTeam = 0*/."
									WHERE xAnmeldung = " . $_POST['item']
								);
				
				// set gender after category change
				$res_sex = mysql_query("SELECT if(Code = 'MAN_' OR substring(Code,4,1) = 'M', 'm', 'w') as sex, Code
							FROM `kategorie` WHERE xKategorie = $category");
				$row = mysql_fetch_array($res_sex);
				if(mysql_num_rows($res_sex) != 0 && $row[1] != ""){
					$sex = $row[0];
					mysql_query("	UPDATE athlet as at, anmeldung as an SET 
								at.Geschlecht = '$sex' 
							WHERE at.xAthlet = an.xAthlet 
							AND an.xAnmeldung = ".$_POST['item']);
				
				}
				
				if(mysql_errno() > 0)
				{
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
			//}		// ET Category valid
			mysql_free_result($result);
		}	// ET DB error
		mysql_query("UNLOCK TABLES");
	//}	// ET athlete entry found
}


//
// Process change_team-request if required
//
else if ($_POST['arg']=="change_team")
{
	mysql_query("LOCK TABLES team READ, anmeldung WRITE");

	if((!empty($_POST['team']))
		&& (AA_checkReference("team", "xTeam", $_POST['team']) == 0))
	{
		AA_printErrorMsg($strTeam . $strErrNotValid);
	}
	else
	{
		mysql_query("	UPDATE anmeldung SET
								xTeam = " . $_POST['team'] . "
							WHERE xAnmeldung = " . $_POST['item']
						);
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
	}	// ET team found
	mysql_query("UNLOCK TABLES");
}

//
// Process change_club-request if required
//
else if ($_POST['arg']=="change_club" && $_POST['club']!='new')
{   

	  mysql_query("LOCK TABLES verein READ, verein WRITE, athlet WRITE");  
	
	if ($_POST['newClub']=='newClub') {    
		
		mysql_query("INSERT INTO verein SET Name = '".$_POST['clubNewText']."' 
					, Sortierwert = '".$_POST['clubNewText']."'");
					
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		} 
	  
	   //mysql_query("SELECT MAX(xVerein) FROM verein GROUP BY xVerein");
		$res_id=mysql_query($sql="SELECT MAX(xVerein) FROM verein");
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		$row_id = mysql_fetch_array($res_id);
		
		mysql_query("    UPDATE athlet SET
								xVerein = " . $row_id[0] . "
							WHERE xAthlet = " . $_POST['xathlete']
						);
						
		$_POST['xVerein']=$row_id[0];
		
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		} 
		 
	
	}
	else {
	
	
	if((!empty($_POST['team'])) 
		&& (AA_checkReference("verein", "xVerein", $_POST['club']) == 0))
	{
		AA_printErrorMsg($strClub . $strErrNotValid);
	}
	else
	{
		mysql_query("	UPDATE athlet SET
								xVerein = " . $_POST['club'] . "
							WHERE xAthlet = " . $_POST['xathlete']
						);
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
	}	// ET team found
	

	
	}
	mysql_query("UNLOCK TABLES");  
	
}    

//
// Process change_clubinfo-request if required
//
else if ($_POST['arg']=="change_clubinfo")
{
	
	mysql_query("LOCK TABLES anmeldung WRITE");
	
	$ci = trim($_POST['clubinfo']);
	
	mysql_query("UPDATE anmeldung SET
			Vereinsinfo = '$ci'
		WHERE
			xAnmeldung = ".$_POST['item']);
	
	if(mysql_errno() > 0)
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	
	mysql_query("UNLOCK TABLES");
	
}

//
// Process change_ccountry-request if required
//
else if ($_POST['arg']=="change_country")
{
	mysql_query("LOCK TABLES land READ, athlet WRITE");

	if((!empty($_POST['team']))
		&& (AA_checkReference("land", "xCode", $_POST['country']) == 0))
	{
		AA_printErrorMsg($strCountry . $strErrNotValid);
	}
	else
	{
		mysql_query("	UPDATE athlet SET
								Land = '" . $_POST['country'] . "'
							WHERE xAthlet = " . $_POST['xathlete']
						);
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
	}	// ET team found
	mysql_query("UNLOCK TABLES");
}

//
// Process change_top-request if required
//
else if ($_POST['arg']=="change_top")
{
	mysql_query("
		LOCK TABLES
			disziplin READ
			, wetkkampf READ
			, start WRITE
	");

	// check if any starts
	$result = mysql_query("
		SELECT
			d.Typ
		FROM
			disziplin AS d
			, start AS s
			, wettkampf AS w
		WHERE s.xStart = " . $_POST['event'] . "
		AND w.xWettkampf = s.xWettkampf
		AND d.xDisziplin = w.xDisziplin
	");

	if(mysql_errno() > 0)
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else				// no DB error
	{
		$row = mysql_fetch_row($result);

		// validate top performance (if any)
		$perf = 0;
		$p = 'topperf_' . $_POST['event'];

		if(!empty($_POST[$p]))
		{
			if(($row[0] == $cfgDisciplineType[$strDiscTypeTrack])
				|| ($row[0] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
				|| ($row[0] == $cfgDisciplineType[$strDiscTypeRelay])
				|| ($row[0] == $cfgDisciplineType[$strDiscTypeDistance]))
				{
				$secflag = false;
				if(substr($_POST[$p],0,2) >= 60){
					$secflag = true;
				}
				$pt = new PerformanceTime($_POST[$p], $secflag);
				$perf = $pt->getPerformance();

			}
			else {
				$pa = new PerformanceAttempt($_POST[$p]);
				$perf = $pa->getPerformance();
			}
			if($perf == NULL) {	// invalid performance
				$perf = 0;
			}
		}
		
		mysql_query("
			UPDATE
				start
			SET
				Bestleistung = $perf
				, BaseEffort = 'n'
			WHERE xStart = " . $_POST['event']
		);
			 
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}

		mysql_free_result($result);
	}	// ET DB error
}

//
// Process change_topcomb (combined top performance)
// not added per combined discipline because an athlete will perform only one combined contest
//
else if ($_POST['arg']=="change_topcomb")
{
	
	$item = $_POST['event'];
	$perf = $_POST['topcomb_'.$item];
	
	mysql_query("LOCK TABLES anmeldung WRITE");
	$sql = "UPDATE anmeldung SET
				BestleistungMK = '$perf'
				, BaseEffortMK = 'n'
			WHERE
				xAnmeldung = $item";
	mysql_query($sql);
	//echo $sql;
	if(mysql_errno() > 0)
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	
	mysql_query("UNLOCK TABLES");
	
}

//
// Process change_cgroup-request if required
//
else if ($_POST['arg']=="change_cgroup")
{
	
	mysql_query("UPDATE anmeldung SET
			Gruppe = '".strtoupper($_POST['combinedgroup'])."'
		WHERE xAnmeldung = ".$_POST['item']."");
	
	if(mysql_errno() > 0){
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	
}

//
// Process change_region-request if required
//
else if ($_POST['arg']=="change_region")
{
	
	mysql_query("UPDATE athlet SET
			xRegion = '".$_POST['region']."'
		WHERE xAthlet = ".$_POST['xathlete']."");
	
	if(mysql_errno() > 0){
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	
}

//
// Process del-request
//
if ($_GET['arg']=="del")
{
	mysql_query("LOCK TABLES serienstart READ, start WRITE, anmeldung WRITE, athlet WRITE");

	// check if start data still used
	$result = mysql_query("SELECT xStart FROM start"
								. " WHERE xAnmeldung=" . $_GET['item']);

	$rc=0;	// row counter
	while($row = mysql_fetch_row($result))
	{
		$rc = $rc + AA_checkReference("serienstart", "xStart", $row[0]);
	}
	mysql_free_result($result);

	// OK: not used anymore
	if($rc == 0)
	{
		// Delete starts
		mysql_query("DELETE FROM start WHERE xAnmeldung=" . $_GET['item']);
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		
		// get xAthlet
		$res = mysql_query("SELECT xAthlet FROM anmeldung WHERE xAnmeldung=" . $_GET['item']);
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			
			$row = mysql_fetch_array($res);
			
			// Delete entry
			mysql_query("DELETE FROM anmeldung WHERE xAnmeldung=" . $_GET['item']);
			if(mysql_errno() > 0)
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			
			/*mysql_query("DELETE FROM athlet WHERE xAthlet=" .$row[0]);
			if(mysql_errno() > 0)
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}*/
			
		}
	}
	// Error: still in use
	else
	{
		AA_printErrorMsg($strEntry . $strErrStillUsed);
	}
	mysql_query("UNLOCK TABLES");

	$_POST['item'] = $_GET['item'];	// show empty form after delete
}

//
//	Process change first heat runner
//
if ($_POST['arg']=="firstheat")
{
	mysql_query("LOCK TABLES start WRITE");
	
	// first set on all starts to 'no', then evaluate the entered values
	mysql_query("UPDATE start SET Erstserie = 'n' WHERE xAnmeldung = ".$_POST['item']);
	if(mysql_errno() > 0){
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}else{
		
		if($_POST['firstheat1']>0){
			mysql_query("UPDATE start SET
					Erstserie = 'y'
				WHERE
					xStart = ".$_POST['firstheat1']);
		}
		
		if($_POST['firstheat2']>0){
			mysql_query("UPDATE start SET
					Erstserie = 'y'
				WHERE
					xStart = ".$_POST['firstheat2']);
		}
		
	}
	
	if(mysql_errno() > 0)
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	mysql_query("UNLOCK TABLES");
}

//
//	Display data
// ------------

$page = new GUI_Page('meeting_entry',false,'stylesheet_small-fonts.css');
$page->startPage();
$page->printPageTitle($strEntry);

if($_GET['arg'] == 'del')	// refresh list
{
	?>
	<script type="text/javascript">
		window.open("meeting_entrylist.php", "list")
	</script>
	<?php
}
else 
{
	?>
	<script type="text/javascript">
		window.open("meeting_entrylist.php?arg=<?php echo $_POST['lsort'] ?>&item=<?php echo $_POST['item']; ?>#item<?php echo $_POST['item']; ?>", "list");
	</script>
	<?php
}
 
// read entry
$result = mysql_query("
	SELECT
		a.xAnmeldung
		, a.xKategorie
		, a.Startnummer
		, at.xAthlet
		, at.Name
		, at.Vorname
		, at.Jahrgang
		, k.Kurzname
		, v.Name
		, v.xVerein
		, t.Name
		, t.xTeam
		, a.Erstserie
		, at.Lizenznummer
		, k.Alterslimite
		, k.Code
		, substring(at.Geburtstag, 9,2)
		, substring(at.Geburtstag, 6,2)
		, at.Land
		, at.Geschlecht
		, a.Gruppe
		, a.BestleistungMK
		, at.xRegion
		, at.Lizenztyp
		, at.Athleticagen
		, a.Vereinsinfo
		, a.BaseEffortMK
	FROM
		anmeldung AS a
		, athlet AS at
		, kategorie AS k
		LEFT JOIN verein AS v ON (at.xVerein = v.xVerein)
	LEFT JOIN team AS t
	ON a.xTeam = t.xTeam
	WHERE a.xAnmeldung = " . $_POST['item'] . "
	AND a.xAthlet = at.xAthlet
	AND a.xKategorie = k.xKategorie
	
");

 if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else if(mysql_num_rows($result) > 0)  // data found
{   
	$row = mysql_fetch_row($result);

	//
	//	Show athlete's personal data
	//
	
	$agelimit = $row[14];
	$catcode = $row[15];
	$sex = '';
	/*if(substr($catcode,0,1) == 'M' || substr($catcode,3,1) == 'M'){
		$sex = "M";
	}else{
		$sex = "W";
	}*/
	$sex = trim($row[19]);
	
?>

<script type="text/javascript">
		 
function check_rounds(){
	
	// check always
	check = confirm("<?php echo $strStartNrConfirm ?>");
	
	if(check == true){
		
		document.startnbr.arg.value = 'change_startnbr';
		document.startnbr.submit();
		
	}else{
		
		document.startnbr.arg.value = '';
		
	}
	
}

// if normal license has been chosen, a license number must be present
function change_licensetype(){
	
	var oldvalue = <?php echo $row[23] ?>;
	var o = document.getElementById('licensetypeselectbox');
	
	if(o.value == 1){
		
		if(document.licensenr.licensenr.value == ''){
			alert("<?php echo $strErrLicenseNotEntered ?>");
			o.value = oldvalue;
			document.licensenr.licensenr.focus();
			return;
		}
	}
	
	document.licensetype.submit();
	
}

function change_licensenr(){
	
	var oldvalue = '<?php echo empty($row[13]) ? '' : $row[13]; ?>';
	//var lic = document.getElementById('licensenr');
	
	if(oldvalue == ''){ // empty license nr --> submit to change license type
		document.licensenr.submit();
	}else{
		if(confirm("<?php echo $strLicenseNrChange ?>")){
			document.licensenr.submit();
		}else{
			document.licensenr.licensenr.value = oldvalue;
		}
	}
	
}


	function setPrint()
	{     
		document.printdialog.formaction.value = 'print'        
		document.printdialog.target = '_blank';     
	}

	 

</script>
 
<table>
 <tr>
	<td > 
		 <?php   
		 $btn = new GUI_Button("meeting_entry.php?arg=del&item=$row[0]", $strDelete);
		 $btn->printButton();
		 ?> 
	</td>    
	<td>
		<form action='print_meeting_receipt.php' method='get' name='printdialog'>
			<input type='hidden' name='formaction' value=''>
			<input type='hidden' name='item' value='<?php echo $row[0]; ?>'>
			<button name='print' type='submit' onClick='setPrint()' valign="bottom">
				<?php echo $strReceipt; ?>
			</button> 
	</td>
	</form> 
   </tr>  
</table>

  

<table class='dialog'>
<tr>
	<form action='meeting_entry.php' method='post' name='startnbr'>
	<th class='dialog'><?php echo $strStartnumberLong; ?></th>
	<td class='forms'>
		<input name='arg' type='hidden' value='change_startnbr' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='old_nbr' type='hidden' value='<?php echo $row[2]; ?>' />
		<input class='nbr' name='nbr' type='text'
			maxlength='5' value='<?php echo $row[2]; ?>'
			onChange='check_rounds()' />
	</td>
	</form>
	<th class='dialog'><?php echo $strFirstHeat; ?></th>
	<form action='meeting_entry.php' method='post' name='firstheat'>
		<input name='arg' type='hidden' value='firstheat' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
	<td class='forms' colspan="3">
		<?php
		// check on firstheat
		// athlete can be a "first heat runner" on max 2 disciplines
		// flag is set on start for event
		$resFh = mysql_query("SELECT 
						s.xStart
						, s.Erstserie
						, d.Kurzname
						, w.Info
					FROM
						start as s
						, wettkampf as w
						, disziplin as d
					WHERE
						s.xAnmeldung = $row[0]
					AND	w.xWettkampf = s.xWettkampf
					AND	d.xDisziplin = w.xDisziplin");
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}else{
			
			$s1 = new GUI_Select("firstheat1", 1, "document.firstheat.submit()");
			$s2 = new GUI_Select("firstheat2", 1, "document.firstheat.submit()");
			$s1->addOption("-",0);
			$s2->addOption("-",0);
			
			// show max 2 select boxes with each discipline
			$c = 0;
			while($rowFh = mysql_fetch_array($resFh)){
				
				$s1->addOption("$rowFh[2] ($rowFh[3])",$rowFh[0]);
				$s2->addOption("$rowFh[2] ($rowFh[3])",$rowFh[0]);
				
				if($rowFh[1] == "y"){
					if($c == 0){
						$s1->selectOption($rowFh[0]);
						$c++;
					}elseif($c == 1){
						$s2->selectOption($rowFh[0]);
						$c++;
					}
				}
				
			}
			
			$s1->printList();
			if($c>0) $s2->printList();
			
		}
		?>
	</td>
	</form>
</tr>

<?php   
//$dis = ($row[13]!='') ? ' disabled="disabled"' : '';  
$dis = '';
$dis2 = false;
?>

<tr>
	<form action='meeting_entry.php' method='post' name='licensenr'>
	<th class='dialog'><?php echo $strLicenseNr; ?></th>
	<td class='forms'>
		<input name='arg' type='hidden' value='change_licensenr' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='athlete' type='hidden' value='<?php echo $row[3]; ?>' />
		<input name='licensenr' type='text' size='12' id='licensenr'
			value='<?php echo empty($row[13]) ? '' : $row[13]; ?>'
			onChange='change_licensenr()' disabled="disabled"/>
		
	</td>
	</form>
	<form action='meeting_entry.php' method='post' name='licensetype'>
		<input name='arg' type='hidden' value='change_licensetype' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='athlete' type='hidden' value='<?php echo $row[3]; ?>' />
	<th class='dialog'><?php echo $strLicenseType; ?></th>
	<?php
	$lt = 0;
	if($row[23] == 0){ // if lic type is null, there are 2 possibilities
		if($row[24] == 'y'){
			$lt = 2; // day license
		}else{
			$lt = 1; // normal license
		}
	}else{
		$lt = $row[23];
	}
	$dd = new GUI_ConfigDropDown('licensetype', 'cfgLicenseType', $lt, "change_licensetype()", false, $dis2);
	?>
	</form>
</tr>

<tr>
	<form action='meeting_entry.php' method='post' name='data'>
	<th class='dialog'><?php echo $strName; ?></th>
	<td class='forms' colspan='3'>
		<input name='arg' type='hidden' value='change' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='category' type='hidden' value='<?php echo $row[1]; ?>' />
		<input name='athlete' type='hidden' value='<?php echo $row[3]; ?>' />
		<input name='combinedgroup' type='hidden' value='' />
		<input class='text' name='name' type='text'
			maxlength='25' value='<?php echo $row[4]; ?>'
			onChange='document.data.submit()'<?=$dis?>/>
		<input class='text' name='first' type='text'
			maxlength='25' value='<?php echo $row[5]; ?>'
			onChange='document.data.submit()'<?=$dis?>/>
	</td>
</tr>

<tr>
	<th class='dialog'><?php echo $strBirthday; ?></th>
	<td class='forms'>
		<input class='nbr' name='day' type='text' maxlength='2'
			value='<?php echo $row[16]; ?>'
			onChange='document.data.submit()'<?=$dis?>>
		<input class='nbr' name='month' type='text' maxlength='2'
			value='<?php echo $row[17]; ?>'
			onChange='document.data.submit()'<?=$dis?>>
		<input name='year' type='text' maxlength='4'
			value='<?php echo $row[6]; ?>'
			onChange='document.data.submit()' size='4'<?=$dis?>>
		<?php
		if($row[19] == 'm'){
			$sexm = "checked";
		}elseif($row[19] == 'w'){
			$sexw = "checked";
		}
		?>
	</td>
	
	<th class='dialog'><?php echo $strSex ?></th>
	<td class='forms'>
		<input type="radio" name="sex" value="m" <?php echo $sexm ?> onChange='document.data.submit()'<?=$dis?>><?php echo $strSexMShort ?>
		<input type="radio" name="sex" value="w" <?php echo $sexw ?> onChange='document.data.submit()'<?=$dis?>><?php echo $strSexWShort ?>
	</td>
	</form>
</tr>

<tr>
	<th class='dialog'><?php echo $strCategory; ?></th>
	<form action='meeting_entry.php' method='post' name='data_cat'>
		<input name='arg' type='hidden' value='change_cat' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='club' type='hidden' value='<?php echo $club; ?>' />
		<input name='old_cat' type='hidden' value='<?php echo $row[1]; ?>' />
<?php
			$dd = new GUI_CategoryDropDown($row[1], 'document.data_cat.submit()', true, false, '' ,false);
?>
	</form>
	<td colspan='2'></td>
</tr>

<tr>
	<th class='dialog'><?php echo $strCountry; ?></th>
	<form action='meeting_entry.php' method='post' name='data_country'>
		<input name='arg' type='hidden' value='change_country' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='xathlete' type='hidden' value='<?php echo $row[3]; ?>' />
<?php
			
		  $dd = new GUI_CountryDropDown($row[18], 'document.data_country.submit()', $dis2); 
?>
	</form>
	
	<th class='dialog'><?php echo $strRegion; ?></th>
	<form action='meeting_entry.php' method='post' name='data_region'>
		<input name='arg' type='hidden' value='change_region' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='xathlete' type='hidden' value='<?php echo $row[3]; ?>' />
<?php
			$dd = new GUI_RegionDropDown($row[22], 'document.data_region.submit()');
?>
	</form>
</tr>

<tr>
	<th class='dialog'><?php echo $strClub; ?></th>
	<form action='meeting_entry.php' method='post' name='data_club'>
		<input name='arg' type='hidden' value='change_club' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='xathlete' type='hidden' value='<?php echo $row[3]; ?>' />
<?php
		if ($_POST['club']=="new") {
			?>
		   <td class='forms'> <input class='text' name='clubNewText' type='text'
			maxlength='25' value=''
			onChange='document.data_club.submit()'<?=$dis?>/> </td>
			<input name='newClub' type='hidden' value='newClub' />  
		<?php 
		}
		else
			{$clubSelected=$row[9];
			if (!empty($_POST['clubNewText'])) {      
				$clubSelected=$_POST['xVerein'];
			}  
			$dd = new GUI_ClubDropDown($clubSelected, true, 'document.data_club.submit()', $dis2, false);
		}
?>
	</form>
	
	<th class='dialog'><?php echo $strClubInfo; ?></th>
	<form action='meeting_entry.php' method='post' name='data_clubinfo'>
		<input name='arg' type='hidden' value='change_clubinfo' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='xathlete' type='hidden' value='<?php echo $row[3]; ?>' />
	<td class='forms'>
		<input type="text" name="clubinfo" value="<?php echo $row[25] ?>" size="25"
			onchange="document.data_clubinfo.submit()">
	</td>
	</form>
</tr>

<tr>
	<th class='dialog'><?php echo $strTeam; ?></th>
	<form action='meeting_entry.php' method='post' name='data_team'>
		<input name='arg' type='hidden' value='change_team' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='club' type='hidden' value='<?php echo $club; ?>' />
		<input name='category' type='hidden' value='<?php echo $row[1]; ?>' />
<?php
			$dd = new GUI_TeamDropDown($row[1], $row[9], $row[11] , 'document.data_team.submit()');
?>
	</form>
	
	<th class='dialog'><?php echo $strCombinedGroup; ?></th>
	<form action='meeting_entry.php' method='post' name='data_combined'>
		<input name='arg' type='hidden' value='change_cgroup' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
	<td class='forms'>
		<input type="text" size="2" maxlength="2" name="combinedgroup"
		onchange="document.data_combined.submit()" value="<?php echo $row[20] ?>">
	</td>
	</form>
</tr>


	<?php
	//
	//	Show disciplines
	//
	$res = mysql_query("
		SELECT
			w.xWettkampf
			, d.Kurzname
			, d.Typ
			, k.Kurzname
			, k.Name
			, k.Alterslimite
			, k.Code
			, w.Info
			, k.xKategorie
			, w.Mehrkampfcode
			, w.Typ
			, k.Geschlecht
			, d.xDisziplin
		FROM
			wettkampf as w
			, disziplin AS d
			, kategorie as k
		WHERE w.xMeeting = " . $_COOKIE['meeting_id'] . "
		"/*AND w.xKategorie = $row[1]*/
		."AND w.xDisziplin = d.xDisziplin
		AND w.xKategorie = k.xKategorie
		ORDER BY
			k.Kurzname, w.Mehrkampfcode, d.Anzeige
	");
								
	if(mysql_errno() > 0)			// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else if(mysql_num_rows($res) > 0)
	{
?>
<tr>
	<th class='dialog' colspan='4'><?php echo $strDisciplines; ?></th>
</tr>
<tr>
	<td class='forms' colspan='4'>
		<table>
		<form action='meeting_entry.php' method='post' name='change_event'>
		<script type="text/javascript">
		<!--
			function updateStarts(argument, event, aname) {
				document.change_event.arg.value = argument;
				document.change_event.event.value = event;
				document.change_event.action = "meeting_entry.php#"+aname;
				document.change_event.submit();
			}
		//-->
		</script>
		<input name='arg' type='hidden' value='' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='category' type='hidden' value='<?php echo $category; ?>' />
		<input name='event' type='hidden' value='' />
		<input name='license' type='hidden' value='<?php echo $row[13]; ?>' />
<?php
		$d=0;
		$last_cat = "";
		$comb = 0;
		$combClosed = 0;
		
		// fetch all rows and put the disciplines of the entry category on top
		$event_rows = array();
		$temp_rows = array();
		while ($event_row = mysql_fetch_row($res)){
			if($event_row[6] == $catcode){
				$event_rows[] = $event_row;
			}else{
				$temp_rows[] = $event_row;
			}
		}
		// if no category matchs, get the right gender on top
		if(count($event_rows) == 0){
			$top_rows = array();
			$event_rows = $temp_rows;
			$i = 0;
			foreach($temp_rows as $temp_row){
				if($temp_row[11] == $sex){
					$tmp = $temp_row;
					$top_rows[] = $temp_row;
					array_splice($event_rows,$i,1);
					//array_unshift($event_rows, $tmp);
					$i--;
				}
				$i++;
			}
			$event_rows = array_merge($top_rows, $event_rows);
		}else{
			$event_rows = array_merge($event_rows, $temp_rows);
		}
		
		// display list of events
		//while ($event_row = mysql_fetch_row($res))
		foreach($event_rows as $event_row)
		{   
			if($last_cat != $event_row[3]){	// new row with title for separating categories
				if($comb > 0){
					echo "</table></td>";
					$comb = 0;
				}
				if($combClosed > 0){
					$combClosed = 0;
				}
				if($last_cat != ""){
					printf("</tr>");
				}
				printf("<tr><td colspan=6 class='cat'>$event_row[4]</td></tr>");
				$last_cat = $event_row[3];
				$d=0;
			}
			
			if( $d % 3 == 0 ) {		// new row after three events
				if ( $d != 0 ) {
					printf("</tr>");	// terminate previous row
				}
				printf("<tr>");
			}

			// check if event already selected
			$r = mysql_query("
				SELECT
					xStart
					, Bestleistung
					, BaseEffort
				FROM
					start
				WHERE xWettkampf = $event_row[0]
				AND xAnmeldung = $row[0]
			");   
					 
			if(mysql_errno() > 0)		// DB error
			{
			  AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else				// no DB error
			{
				$start_row = mysql_fetch_array($r);         
			
				//check if performance from base or manually entered
				($start_row['BaseEffort']=='y' || $start_row['Bestleistung']=='0')?$manual='':$manual=" manual"; 
				 
				
				// check if this is a valid selection (age on category)
				//if($event_row[5] < $agelimit || (substr($event_row[6],0,1) != $sex && substr($event_row[6],3,1) != $sex)){
				
				//echo $event_row[5] ."<". $agelimit ."||". $event_row[11] ."!=". $sex ."<br>";
				
				if($event_row[5] < $agelimit || $event_row[11] != $sex){
					$span = "<span class='highlight_red'>";
					$span_end = "</span>";
				}else{
					$span = "";
					$span_end = "";
				}
				
				if(($event_row[2] == $cfgDisciplineType[$strDiscTypeTrack])
					|| ($event_row[2] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
					|| ($event_row[2] == $cfgDisciplineType[$strDiscTypeRelay])
					|| ($event_row[2] == $cfgDisciplineType[$strDiscTypeDistance]))
				{
					$class = 'time';  
					if($event_row[2] == $cfgDisciplineType[$strDiscTypeDistance]){
						$perf = AA_formatResultTime($start_row[1]);
					}else{
						$perf = AA_formatResultTime($start_row[1], false, true);
					}                    
				}
				else { 
					 $class = 'meter';                      
					 $perf = AA_formatResultMeter($start_row[1]);                            
				}
				
				//
				// merge the disciplines for a combined event
				//
				if($event_row[10] == $cfgEventType[$strEventTypeSingleCombined]){
					
					if (!$comb_start ) {
						echo "</tr>";
					}
					$comb_start = true;
					
					$d=1;
					
					// check if one of the combined events is selected
					$start_comb = false;
					$resStartComb = mysql_query("SELECT xStart FROM
									start as s
									, wettkampf as w
								WHERE
									w.xKategorie = $event_row[8]
								AND	w.Mehrkampfcode = $event_row[9]
								AND	s.xWettkampf = w.xWettkampf
								AND	s.xAnmeldung = $row[0]");
					if(mysql_num_rows($resStartComb) > 0){
						$start_comb = true;
					}
					mysql_free_result($resStartComb);
					 
					if($start_comb) {		// combined selected
						if($comb != $event_row[9]){
							if($comb > 0){
								echo "</table></td></tr><tr>";
							}
							$comb = $event_row[9];
							$comb_res = mysql_query("SELECT Name FROM disziplin WHERE Code = $comb");
							$comb_row = mysql_fetch_array($comb_res);
							
							//check if performance from base or manually entered
							($row[26]=='y' || $row[26]=='0')? $manualMK='':$manualMK=' manual'; 
							?>
							<td nowrap="nowrap" class='dialog-top' colspan='2'><?php echo $span ?>
								<input type="checkbox" value="<?php echo $event_row[8]."_".$comb ?>" name="combined[]"
									onclick="updateStarts('del_combined', '<?php echo $event_row[8]."_".$comb ?>','')" 
									checked>
								<?php echo $comb_row[0]; ?><?php echo $span_end ?>
							</td>
							<td class='dialog-top' colspan='2'>
								<input class="perfmeter<?=$manualMK;?>" type="text" name="topcomb_<?php echo $row[0] ?>" value="<?php echo $row[21] ?>" size="5"   									onchange="updateStarts('change_topcomb', <?php echo $row[0] ?>, <?php echo $event_row[0] ?>)">
							</td>
							<td class='dialog' colspan='2' id='td_<?php echo $event_row[8]."_".$comb ?>'>
								<table>
							<?php
						}
						
						if($start_row[0] != 0) { // start of each discipline, athlete can choose for optional disciplines                           
							printf("<tr><td class=\"dialog-top\" nowrap=\"nowrap\"><input name='events[]' type='checkbox' id='$event_row[0]'
								onclick='updateStarts(\"del_event\", $start_row[0], $event_row[0])'
								value='$start_row[0]' checked/>
								$event_row[1]</td><td>
								<input class='perf$class$manual' name='topperf_$start_row[0]'
								type='text' maxlength='12'
								onchange='updateStarts(\"change_top\", $start_row[0], $event_row[0])'
								value='$perf' /></td></tr>\n");
						}else{
							printf("<tr><td class=\"dialog-top\" nowrap=\"nowrap\"><input name='events[]' type='checkbox' id='$event_row[0]'
								onclick='updateStarts(\"add_event\", $event_row[0], $event_row[0])'
								value='$event_row[0]' />
								$event_row[1]</td><td>
								</td></tr>\n");
						}
					}else{
						if($combClosed != $event_row[9]){
							if($comb > 0){ // check on last selected combined event
								echo "</table></td></tr>";
								$comb = 0;
							}
							$combClosed = $event_row[9];
							$comb_res = mysql_query("SELECT Name FROM disziplin WHERE Code = $combClosed");
							$comb_row = mysql_fetch_array($comb_res);
							?>
							<tr>
								<td class='dialog-top' colspan='6'>
									<input type="checkbox" value="<?php echo $event_row[8]."_".$combClosed ?>" name="combined[]"
										onclick="updateStarts('add_combined', '<?php echo $event_row[8]."_".$combClosed ?>','')">
									<?php echo $comb_row[0]; ?>
								</td>
							</tr>
							<?php 
						}
					}
				}else{
					$info = (strlen($event_row[7])==0)?"":"(".$event_row[7].")";
					if($start_row[0] != 0) {		// event selected				   
						printf("<td class=\"dialog-top\" nowrap=\"nowrap\">$span<input name='events[]' type='checkbox' id='$event_row[0]'"
						. " onclick='updateStarts(\"del_event\", $start_row[0], $event_row[0])'"
						. " value='$start_row[0]' checked/>$event_row[1] $info $span_end</td>\n");
						
						
						printf("<td class=\"dialog-top\" nowrap=\"nowrap\">
							<input class='perf$class$manual' name='topperf_$start_row[0]'
							type='text' maxlength='12'
							onchange='updateStarts(\"change_top\", $start_row[0], $event_row[0])'
							value='$perf' /></td>\n");
					}else{
						printf("<td class=\"dialog-top\" nowrap=\"nowrap\"><input name='events[]' type='checkbox' id='$event_row[0]'"
						. " onclick='updateStarts(\"add_event\", $event_row[0], $event_row[0])'"
						. " value='$event_row[0]' />$event_row[1] $info</td><td></td>\n");
					}
				}
				
			}				// ET DB error	(start)
			mysql_free_result($r);
			$d++;
		}					// next event
		mysql_free_result($res);
		if($comb > 0){
			echo "</table></td>";
			$comb = 0;
		}
		?>
		</form>
		</table>
		<?php
	}	// ET DB error	/ disciplines present (disciplines)

	// show if athlete starts also in other category (e.g. relays)
	// (only delete possible!)
	/*$res = mysql_query("
		SELECT
			d.Kurzname
			, k.Kurzname
			, s.xStart
		FROM
			disziplin AS d
			, kategorie AS k
			, start AS s
			, wettkampf AS w
		WHERE s.xAnmeldung = $row[0]
		AND s.xWettkampf = w.xWettkampf
		AND w.xDisziplin = d.xDisziplin
		AND w.xKategorie = k.xKategorie
		AND w.xKategorie != $row[1]
		ORDER BY
			d.Anzeige
	");
							
	if(mysql_errno() > 0)			// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else if(mysql_num_rows($res) > 0)
	{*/
?>
		<br/>
		<table>
		<form action='meeting_entry.php' method='post' name='del_event'>
		<script type="text/javascript">
		<!--
			function delStarts(event) {
				document.del_event.event.value = event;
				document.del_event.submit();
			}
		//-->
		</script>
		<input name='arg' type='hidden' value='del_event' />
		<input name='category' type='hidden' value='<?php echo $category; ?>' />
		<input name='club' type='hidden' value='<?php echo $club; ?>' />
		<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
		<input name='event' type='hidden' value='' />

		<?php
		/*$d=0;

		// display list of events
		while ($event_row = mysql_fetch_row($res))
		{
			if( $d % 3 == 0 ) {		// new row after seven events
				if ( $d != 0 ) {
					printf("</tr>");	// terminate previous row
				}
				printf("<tr>");
			}

			printf("<td><input name='events[]' type='checkbox'"
			. " onclick='delStarts($event_row[2])'"
			. " value='$event_row[2]' checked/>$event_row[0] ($event_row[1])</td>\n");
			$d++;
		}					// next event
		mysql_free_result($res);
		if($d!=0) {				// any row -> terminate last one
			printf("</tr>\n");
		}*/
		?>
		</form>
		</table>
		<?php
	//}	// ET DB error	/ disciplines present (disciplines)
	?>
	</td>
</tr>
</table>
	<?php
	mysql_free_result($result);
}

?>
<p/> 
<?php
$btn = new GUI_Button("meeting_entry.php?arg=del&item=$row[0]", $strDelete);
$btn->printButton(); 
	
$page->endPage();
?>
