<?php

/**********
 *
 *	dlg_heat_seeding.php
 *	-------------------
 *	
 */
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');
require('./lib/cl_gui_dropdown.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	// invalid DB connection
{
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

// get presets
$round = $_GET['round'];
if(!empty($_POST['round'])) {
	$round = $_POST['round'];
}

/*$res = mysql_query("SELECT r.xWettkampf"
								. ", rt.Name"
								. ", d.Name"
								. ", k.Name"
								. ", rt.Typ"
								. ", r.Gruppe"
								. ", w.Typ"
								. ", w.Mehrkampfende"
								. " FROM runde AS r"
								. ", wettkampf AS w"
								. ", disziplin AS d"
								. ", kategorie AS k"
								. " LEFT JOIN rundentyp AS rt"
								. " ON r.xRundentyp = rt.xRundentyp"
								. " WHERE r.xRunde = " . $round
								. " AND r.xWettkampf = w.xWettkampf"
								. " AND w.xDisziplin = d.xDisziplin"
								. " AND w.xKategorie = k.xKategorie");*/

$sql = "SELECT 
			  r.xWettkampf
			, rt.Name
			, d.Name
			, k.Name
			, rt.Typ
			, r.Gruppe
			, w.Typ
			, w.Mehrkampfende 
		FROM
			runde AS r 
		LEFT JOIN 
			rundentyp AS rt USING(xRundentyp) 
		LEFT JOIN 
			wettkampf AS w ON(r.xWettkampf = w.xWettkampf) 
		LEFT JOIN 
			disziplin AS d USING(xDisziplin) 
		LEFT JOIN 
			kategorie AS k ON(w.xKategorie = k.xKategorie) 
		WHERE 
			r.xRunde = ".$round.";";
$res = mysql_query($sql);
								
$combined = false;
$cGroup = "";
$cLast = 0;
$teamsm = false;

if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else
{
	$row = mysql_fetch_row($res);
	$event = $row[0];				// event ID
	$title = $row[2] . ", " . $row[3];
	if(!is_null($row[1])) {
		$roundname = $row[1];
	}
	if($row[6] == $cfgEventType[$strEventTypeSingleCombined]){
		$combined = true;
		$cGroup = $row[5];
		$cLast = $row[7];
	}
	if($row[6] == $cfgEventType[$strEventTypeTeamSM]){
		$teamsm = true;
		$cGroup = $row[5];
	}
	mysql_free_result($res);
}

// read round data
if($round > 0)
{
	$relay = AA_checkRelay($event);
	if($relay == FALSE) {		// single event
		$XXXPresent = $strAthletesPresent;
		$XXXPerHeat = $strAthletesPerHeat;
		$XXXWithResult = $strAthletesWithResult;
	}
	else {							// relay event
		$XXXPresent = $strRelaysPresent;
		$XXXPerHeat = $strRelaysPerHeat;
		$XXXWithResult = $strRelaysWithResult;
	}

	// read all rounds per event, sorted by time
	$count == 0;
	$prev_rnd = 0;
	$prev_rnd_name = "";
	/*$res = mysql_query("SELECT r.xRunde"
				. ", rt.Name"
				. " FROM runde AS r"
				. " LEFT JOIN rundentyp AS rt"
				. " ON r.xRundentyp = rt.xRundentyp"
				. " WHERE r.xWettkampf = $event"
				. " ORDER BY r.Datum, r.Startzeit");*/
	$sql = "SELECT 
				  r.xRunde
				, rt.Name 
			FROM 
				runde AS r 
			LEFT JOIN 
				rundentyp AS rt USING(xRundentyp) 
			WHERE r.xWettkampf = ".$event." 
			ORDER BY 
				r.Datum ASC, 
				r.Startzeit ASC;";
	$res = mysql_query($sql);

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		$tot_rounds = mysql_num_rows($res);		// keep total nbr of rounds
		while ($row = mysql_fetch_row($res))
		{
			$count++;
			if($row[0] == $round)	{		// actual round found
				break;	// terminate loop
			}
			$prev_rnd = $row[0];			// keep round ID for further processing
			$prev_rnd_name = $row[1];	// keep round Name for further processing
		}
		mysql_free_result($res);
		$final = FALSE;
		if($tot_rounds == $count) {	// final round)
			$final = TRUE;
		}
	}		// ET DB error

	// get default heat size and discipline type
	$size = 0;
	/*$res = mysql_query("SELECT d.Seriegroesse"
							. ", d.Typ"
							. " FROM disziplin AS d"
							. ", wettkampf AS w"
							. " WHERE d.xDisziplin = w.xDisziplin"
							. " AND w.xWettkampf = $event");*/
	$sql = "SELECT 
				  d.Seriegroesse
				, d.Typ 
			FROM 
				disziplin AS d 
			LEFT JOIN 
				wettkampf AS w USING(xDisziplin) 
			WHERE
				w.xWettkampf = ".$event.";";
	$res = mysql_query($sql);

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		$row = mysql_fetch_row($res);
		$size = $row[0];
		$type = $row[1];
		mysql_free_result($res);
	}		// ET DB error
	

	// get nbr of tracks in stadium for disciplines run in tracks
	// (see cfgDisciplineType in config.inc.php)
	$tracks = 0;
	if(($type == $cfgDisciplineType[$strDiscTypeTrack])
		|| ($type == $cfgDisciplineType[$strDiscTypeTrackNoWind])
		|| ($type == $cfgDisciplineType[$strDiscTypeRelay]))
	{
		/*$res = mysql_query("SELECT s.Bahnen, s.BahnenGerade"
								. " FROM meeting AS m"
								. ", stadion AS s"
								. " WHERE m.xMeeting = " . $_COOKIE['meeting_id']
								. " AND s.xStadion = m.xStadion");*/
		$sql = "SELECT 
					  s.Bahnen
					, s.BahnenGerade
				FROM 
					meeting AS m
				LEFT JOIN 
					stadion AS s USING(xStadion)
				WHERE m.xMeeting = ".$_COOKIE['meeting_id'].";";
		$res = mysql_query($sql);

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			$row = mysql_fetch_row($res);
			$tracks = $row[0];
			mysql_free_result($res);
		}		// ET DB error
	}

	//
	// Display seeding dialog 
	//
	$page = new GUI_Page('dlg_heat_seeding');
	$page->startPage();
	
	
	//
	// read merged rounds an select all events
	//
	$sqlEvents = "";
	$eventMerged = false;
	$result = mysql_query("SELECT xRundenset FROM rundenset
				WHERE	xRunde = $round
				AND	xMeeting = ".$_COOKIE['meeting_id']);
	if(mysql_errno() > 0){
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}else{
		$rsrow = mysql_fetch_array($result); // get round set id
		mysql_free_result($result);
	}
	
	if($rsrow[0] > 0){
		/*$result = mysql_query("	SELECT r.xWettkampf FROM
						rundenset as s
						, runde as r
					WHERE
						s.xMeeting = ".$_COOKIE['meeting_id']."
					AND	s.xRundenset = $rsrow[0]
					AND	r.xRunde = s.xRunde");*/
		$sql = "SELECT
					r.xWettkampf 
				FROM
					rundenset AS s
				LEFT JOIN 
					runde AS r USING(xRunde)
				WHERE
					s.xMeeting = ".$_COOKIE['meeting_id']." 
				AND
					s.xRundenset = ".$rsrow[0].";";
		$result = mysql_query($sql);
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			$sqlEvents .= " s.xWettkampf = ".$event." ";
		}else{
			if(mysql_num_rows($result) == 0){ // no merged rounds
				$sqlEvents .= " s.xWettkampf = ".$event." ";
			}else{
				$eventMerged = true;
				$sqlEvents .= "( s.xWettkampf = ".$event." ";
				while($row = mysql_fetch_array($result)){
					if($row[0] != $event){ // if there are additional events (merged rounds) add them as sql statement
						$sqlEvents .= " OR s.xWettkampf = ".$row[0]." ";
					}
				}
				$sqlEvents .= ") ";
			}
			mysql_free_result($result);
		}
	}else{
		$sqlEvents .= " s.xWettkampf = ".$event." ";
	}
	
	//
	// first round -> basic seeding
	// if this is a combined event -> basic seeding
	// if this is a team sm event -> basic seeding
	//
	if($count == 1 || $combined || $teamsm)
	{
		// get number of athletes/relays present
		$present = 0;
		if($relay == FALSE) {		// single event
			$query = "SELECT COUNT(*)"
					. " FROM start AS s"
					. " WHERE " //s.xWettkampf = $event"
					. $sqlEvents
					. " AND s.Anwesend=0";
		}
		else {							// relay event
			/*$query = "SELECT COUNT(*)"
					. " FROM start AS s"
					. ", staffel AS st"
					. " WHERE " //s.xWettkampf = $event"
					. $sqlEvents
					. " AND s.Anwesend = 0"
					. " AND st.xStaffel = s.xStaffel";*/
			$query = "SELECT
						  COUNT(*) 
					  FROM
						  start AS s
					  LEFT JOIN
						  staffel AS st USING(xStaffel) 
					  WHERE 
						  ".$sqlEvents."
					  AND
						  s.Anwesend = 0;";
		}
		if($combined && $cLast == 0 && !empty($cGroup)){	// combined event and not last discipline, count athletes of correct group
			/*$query = "SELECT COUNT(*)"
					. " FROM start AS s, anmeldung as a"
					. " WHERE s.xWettkampf = $event"
					. " AND a.xAnmeldung = s.xAnmeldung"
					. " AND a.Gruppe = $cGroup"
					. " AND s.Anwesend=0";*/
			$query = "SELECT
						  COUNT(*)
					  FROM
						  start AS s
					  LEFT JOIN 
						  anmeldung AS a USING(xAnmeldung)
					  WHERE 
						  s.xWettkampf = ".$event."
					  AND
						  a.Gruppe = ".$cGroup." 
					  AND 
						  s.Anwesend = 0;";
		}elseif(($combined && $cLast == 1) 
			|| ($combined && empty($cGroup))){	// combined last event or combined without groups, take all starts
			$query = "SELECT COUNT(*)"
					. " FROM start AS s"
					. " WHERE s.xWettkampf = $event"
					. " AND s.Anwesend=0";
		}
		if($teamsm && !empty($cGroup)){ // team sm event with group (tech)
			/*$query = "SELECT COUNT(*)"
					. " FROM start AS s, anmeldung as a"
					. " WHERE s.xWettkampf = $event"
					. " AND a.xAnmeldung = s.xAnmeldung"
					. " AND a.Gruppe = $cGroup"
					. " AND s.Anwesend=0";*/
			$query = "SELECT
						  COUNT(*)
					  FROM
						  start AS s
					  LEFT JOIN
						  anmeldung AS a USING(xAnmeldung)
					  WHERE 
						  s.xWettkampf = ".$event."
					  AND
						  a.Gruppe = ".$cGroup."
					  AND 
						  s.Anwesend = 0;";
		}
		
		$res = mysql_query($query);
		
		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			$row = mysql_fetch_row($res);
			$present = $row[0];
			mysql_free_result($res);
		}
		
		if(($type == $cfgDisciplineType[$strDiscTypeJump])
			|| ($type == $cfgDisciplineType[$strDiscTypeJumpNoWind])
			|| ($type == $cfgDisciplineType[$strDiscTypeHigh])
			|| ($type == $cfgDisciplineType[$strDiscTypeThrow]))
		{
			
			$size = $present;
			
		}elseif(($type == $cfgDisciplineType[$strDiscTypeTrack])
			|| ($type == $cfgDisciplineType[$strDiscTypeTrackNoWind])
			|| ($type == $cfgDisciplineType[$strDiscTypeRelay]))
		{
			
			// calculate balanced number of athletes/relays per heat
			$bf = ceil($present / $tracks);
			if($bf > 1){
				$size = ceil($present / $bf);
			}else{
				$size = $present;
			}
			
		}

		$page->printPageTitle($strHeatSeeding);

		$menu = new GUI_Menulist();
		$menu->addButton($cfgURLDocumentation . 'help/event/seeding.html', $strHelp, '_blank');
		$menu->printMenu();

		$page->printSubTitle($title);

?>
		<form action='event_heats.php' method='post'>
		<input name='arg' type='hidden' value='seed' />
		<input name='final' type='hidden' value='<?php echo $final; ?>' />
		<input name='event' type='hidden' value='<?php echo $event; ?>' />
		<input name='round' type='hidden' value='<?php echo $round; ?>' />
		<input name='cGroup' type='hidden' value='<?php echo $cGroup; ?>' />
			<table class='dialog'>
<?php
		if(!empty($roundname)) {		// round name set
?>
				<tr>
					<th class='dialog' colspan='2'><?php echo $roundname; ?></th>
				</tr>
<?php
		}
?>
				<tr>
					<th class='dialog'><?php echo $XXXPresent; ?></th>
					<td class='dialog'><?php echo $present; ?></td>
				</tr>
				<tr>
					<th class='dialog'><?php echo $XXXPerHeat; ?></th>
					<td class='forms'><input class='nbr' name='size' type='text' maxlength='5'
						value='<?php echo $size; ?>' /></td>
				</tr>
<?php
		if($tracks > 0) {		// discipline run in tracks
?>
				<tr>
					<th class='dialog'><?php echo $strNbrOfTracks; ?></th>
					<?php
						$dd = new GUI_ConfigDropDown('tracks', 'cfgTrackOrder', $tracks, '', true);
					?>
				</tr>
<?php
		}
?>
				 <tr>
					 <th class='dialog'><?php echo $strMode; ?></th>
					 <td><input name='mode' type='radio' value='0' checked>
						 <?php echo $strModeOpen; ?></input></td>
				 </tr>
				 <tr>
					 <td />
					 <td><input name='mode' type='radio' value='1'>
						 <?php echo $strModeTopTogether; ?></input></td>
				 </tr>
				 <tr>
					 <td />
					 <td><input name='mode' type='radio' value='2'>
						 <?php echo $strModeTopSeparated; ?></input></td>
				 </tr>
<?php
	}	// ET 1st round
	
	//
	// qualification from previous round
	//
	else			
	{

		// get number of athletes/relays with valid result
		/*$res = mysql_query("SELECT COUNT(*)"
								. " FROM serienstart AS ss"
								. ", serie AS s"
								. " WHERE ss.Qualifikation > 0"
								. " AND s.xSerie = ss.xSerie"
								. " AND s.xRunde = " . $prev_rnd);*/
		$sql = "SELECT
					COUNT(*)
				FROM
					serienstart AS ss
				LEFT JOIN
					serie AS s USING(xSerie)
				WHERE
					ss.Qualifikation > 0 
				AND
					ss.Qualifikation != 9
				AND
					s.xRunde = ".$prev_rnd." ;";
		$res = mysql_query($sql);

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			$row = mysql_fetch_row($res);
			$present = $row[0];
			mysql_free_result($res);
		}

		$page->printPageTitle($strQualification);

		$menu = new GUI_Menulist();
		$menu->addButton($cfgURLDocumentation . 'help/event/qualification.html', $strHelp, '_blank');
		$menu->printMenu();

		$page->printSubTitle($title);
?>
		<form action='event_heats.php' method='post'>
		<input name='arg' type='hidden' value='seed_qual' />
		<input name='final' type='hidden' value='<?php echo $final; ?>' />
		<input name='event' type='hidden' value='<?php echo $event; ?>' />
		<input name='round' type='hidden' value='<?php echo $round; ?>' />
		<input name='prev_round' type='hidden' value='<?php echo $prev_rnd; ?>' />
			<table class='dialog'>
<?php
		if(!empty($roundname)) {		// round name set
?>
				<tr>
					<th class='dialog' colspan='2'><?php echo $roundname; ?></th>
				</tr>
<?php
		}
?>
				<tr>
					<th class='dialog'><?php echo $strQualifyFrom; ?></th>
					<td class='dialog'><?php echo $prev_rnd_name; ?></td>
				</tr>
				<tr>
					<th class='dialog'><?php echo $XXXWithResult; ?></th>
					<td class='dialog'><?php echo $present; ?></td>
				</tr>
				<tr>
					<th class='dialog'><?php echo $XXXPerHeat; ?></th>
					<td><input class='nbr' name='size' type='text' maxlength='4'
						value='<?php echo $size; ?>' /></td>
				</tr>
<?php
		if($tracks > 0) {		// discipline run in tracks
?>
				<tr>
					<th class='dialog'><?php echo $strNbrOfTracks; ?></th>
					<td><input class='nbr' name='tracks' type='text' maxlength='2'
						value='<?php echo $tracks; ?>' /></td>
				</tr>
<?php
		}
?>
				 <tr>
					 <th class='dialog'><?php echo $strMode; ?></th>
					 <td><input name='mode' type='radio' value='0' checked>
						 <?php echo $strTopPerformance; ?></input></td>
				 </tr>
				 <tr>
					 <td />
					 <td><input name='mode' type='radio' value='1'>
						 <?php echo $strIWB166; ?></input></td>
				 </tr>
<?php
	}	// ET first/later round
?>

			</table>
			<p />
			<table>
				<tr>
					<td>
						<button type='submit'>
							<?php echo $strSeed; ?>
						</button>
					</td>
				</tr>
			</table>
		</form>
<?php
	$page->endPage();
}		// ET round selected

?>
