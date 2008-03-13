<?php

/**********
 *
 *	result maintenance functions
 *	
 */

if (!defined('AA_RESULTS_LIB_INCLUDED'))
{
	define('AA_RESULTS_LIB_INCLUDED', 1);


  /**
   * Configuration file
	 */
	include('./config.inc.php');


//
// get presets (common GET/POST variables)
//
function AA_results_getPresets($round)
{
	require('./lib/common.lib.php');

	$presets = array('category' => 0
						, 'event' => 0
						, 'focus' => '');

	if(!empty($_POST['category'])) {
		$presets['category'] = $_POST['category'];
	}

	if(!empty($_POST['event'])) {
		$presets['event'] = $_POST['event'];

		// only event preselected -> get category
		if($presets['category'] == 0)
		{
			$res = mysql_query("SELECT w.xKategorie"
								. " FROM wettkampf AS w"
								. " WHERE w.xWettkampf = " . $presets['event']);

			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$row = mysql_fetch_row($res);
				$presets['category'] = $row[0];		// category ID
				mysql_free_result($res);
			}
		}
	}

	if(!empty($_GET['focus'])){
		$presets['focus'] = $_GET['focus'];
	}
	else if(!empty($_POST['focus'])) {
		$presets['focus'] = $_POST['focus'];
	}

	// only round preselected -> get event and category
	if(($round > 0)
		&& (($presets['category'] == 0) || ($presets['event'] == 0)))
	{
		$res = mysql_query("SELECT r.xWettkampf"
								. ", w.xKategorie"
								. " FROM runde AS r"
								. ", wettkampf AS w"
								. " WHERE r.xRunde = " . $round
								. " AND r.xWettkampf = w.xWettkampf");

		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else {
			$row = mysql_fetch_row($res);
			$presets['event'] = $row[0];				// event ID
			$presets['category'] = $row[1];		// category ID
			mysql_free_result($res);
		}
	}
	return $presets;
}

//
// get results from alge timing
//
// - round: wich round should be updated with results
// - arg: true -> if there are manually entered results, do not update with timing results
// - noerror: true -> supress errors (useful on auto fetching)
//
function AA_results_getTimingAlge($round, $arg=false, $noerror=false){
	global $cfgInvalidResult, $strErrTimingWrongRegid, $cfgRoundStatus;
	
	$alge = new alge($noerror);
	
	if($alge->is_configured() == false){
		return;
	}
	
	$relay = AA_checkRelay(0, $round);
	
	/*if($arg){
		$sqladd = "AND r.Status = ".$cfgRoundStatus['heats_done'];
	}else{
		$sqladd = "";
	}*/
	if(!$arg){
		// COMMENT ROH:
		// existing results should not be updated automatically. Either we require the user to manually 
		// delete existing results, or we have to ask for confirmation. 
		// for the moment, no results are imported if results exist.
	}
	
	mysql_query("
		LOCK TABLES 
			serie as s WRITE
			, resultat as res WRITE
			, resultat WRITE
			, runde as r WRITE
			, serienstart as sst READ
			, start as st READ
			, anmeldung as a READ
			, disziplin as d READ
			, wettkampf as w READ
			, rundentyp as rt READ
			, kategorie as k READ
			, staffel as sf READ
			, disziplin READ
			, wettkampf READ"
	);
	
	/*$res_film = mysql_query("
		SELECT s.Film, s.xSerie, r.xWettkampf FROM 
			serie as s
			LEFT JOIN runde as r USING(xRunde)
		WHERE s.xRunde = $round
		$sqladd"
	);*/
	// Fix during indoor SM: do not load if results exist for a serie
	$res_film = mysql_query("
		SELECT s.Film as film, s.xSerie as heat, r.xWettkampf as event, count(res.Leistung) as numResults FROM 
			runde as r
			LEFT JOIN serie AS s USING ( xRunde )
			LEFT JOIN serienstart AS sst USING ( xSerie ) 
			LEFT JOIN resultat AS res USING ( xserienstart )
		WHERE s.xRunde = $round
		GROUP BY sst.xSerie"
	);
	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}else{
		
		while($row_film = mysql_fetch_array($res_film)){
			if ($row_film[3] == 0) { // check if results exist
				$nr = $row_film[0];
				$event = $row_film[2];
				
				// COMMENT ROH:
				// commented out the line below because results should only be inported if official
				// the same line is inserted some lines below after the check
				// $results = $alge->import_heat_results($row_film[1]);
				$infos = $alge->import_heat_infos($row_film[1]);
				
				if($infos['Official']){
					// COMMENT ROH:
					// import results only if official
					$results = $alge->import_heat_results($row_film[1]);
					
					$wind = $infos['RaceInfo']['Wind'];
					if(empty($wind)){
						$wind = "";
					}elseif(substr($wind,0,1) == "+"){
						$wind = substr($wind, 1, 3);
					}elseif(substr($wind,0,1) == "-"){
						$wind = substr($wind, 0, 4);
					}
					
					mysql_query("UPDATE serie as s SET Wind = '".$wind."'
							WHERE xRunde = $round AND Film = $nr");
					
					foreach($results as $val){
						
						switch($val[11]){
							case 0:
							$perf = $cfgInvalidResult['DNS']['code'];
							$points = 0;
							break;
							case 1:
							$perf = $cfgInvalidResult['DNF']['code'];
							$points = 0;
							break;
							case 2:
							$perf = $cfgInvalidResult['DSQ']['code'];
							$points = 0;
							break;
							default:
							$perf = 0;
							
							if($alge->typ=='OPTIc2'){
								$perf = substr($val[9], 0, (strlen($val[9])-1));
								
								// COMMENT ROH:
								// cutting digits is not according to competition rules
								// $perf = substr($val[9], 0, (strlen($val[9])-1));
								// alternative: round correctly 
								// (ceil was not consistent with the official results when the 1/1000sec was xx0)
								$perf = (floor($val[9] / 10) + 1);
							} else {
								$perf = $val[9];
							}
							
							$sex = 'M';
							//if(!$relay){
								/*$sql = "SELECT Geschlecht
										  FROM serienstart AS sst
									 LEFT JOIN serie AS s USING ( xSerie )
									 LEFT JOIN START AS st ON sst.xStart = st.xStart
									 LEFT JOIN anmeldung AS a ON st.xAnmeldung = a.xAnmeldung
									 LEFT JOIN athlet USING ( xAthlet )
										 WHERE s.xRunde = ".$round."
										   AND s.Film = ".$nr."
										   AND a.Startnummer = ".$val[1].";";*/
								$sql_sex = "SELECT DISTINCT(Geschlecht) AS Geschlecht
											  FROM kategorie 
										 LEFT JOIN wettkampf USING(xKategorie) 
										 LEFT JOIN start AS st USING(xWettkampf)
										 LEFT JOIN serienstart AS sst  USING(xStart)
										 LEFT JOIN serie AS s USING(xSerie)
											 WHERE s.XRunde = ".$round." 
											   AND s.Film = ".$nr.";";
								$query = mysql_query($sql_sex);
								
								if($query && mysql_num_rows($query)==1){
									$sex = mysql_result($query, 0, 'Geschlecht');
								}
							//}
							
							$points = AA_utils_calcPoints($event, $perf, 0, $sex);
							//echo $GLOBALS['AA_ERROR'];
						}
						
						if($relay == false){ // no relay event
							
							$res = mysql_query("
								SELECT xResultat 
									   , Geschlecht
								  FROM resultat as res
							 LEFT JOIN serienstart as sst USING(xSerienstart)
							 LEFT JOIN serie as s USING (xSerie)
							 LEFT JOIN start as st ON sst.xStart = st.xStart
							 LEFT JOIN anmeldung as a ON st.xAnmeldung = a.xAnmeldung
							 LEFT JOIN athlet USING(xAthlet)
								 WHERE s.xRunde = $round
								   AND s.Film = $nr
								   AND a.Startnummer = ".$val[1]
							);
							
							
							// COMMENT ROH:
							// ok, here is the test to check if results already exist
							// however, something with this test did not work and results got imported multiple times
							// please verify!!! 
							// note: we check now before importing so that we never should get here if results already exist. 
							
							if(mysql_num_rows($res) == 0){
								// insert result
								$res = mysql_query("
									SELECT sst.xSerienstart FROM
										serie as s
										LEFT JOIN serienstart as sst USING(xSerie)
										LEFT JOIN start as st USING(xStart)
										LEFT JOIN anmeldung as a USING(xAnmeldung)
									WHERE	a.Startnummer = ".$val[1]."
									AND	s.Film = $nr
									AND	s.xRunde = $round"
								);
								
								if(mysql_num_rows($res) == 0){
									// no athlete with this registration id is started
									// if($noerror==false){ AA_printErrorMsg($strErrTimingWrongRegid); }
								}else{
									
									$row = mysql_fetch_array($res);
									mysql_query("
										INSERT INTO resultat
										SET 	Leistung = '$perf'
											, Punkte = '$points'
											, xSerienstart = ".$row[0]
									);
									
								}
							}else{
								// update
								$row = mysql_fetch_array($res);
								mysql_query("UPDATE resultat as res SET Leistung = '$perf'
										, Punkte = '$points'
									WHERE xResultat = ".$row[0]);
								
							}
									//
						}else{ 			// relay event
									//
							$res = mysql_query("
								SELECT xResultat FROM
									resultat as res
									LEFT JOIN serienstart as sst USING(xSerienstart)
									LEFT JOIN serie as s USING (xSerie)
									LEFT JOIN start as st ON sst.xStart = st.xStart
									LEFT JOIN staffel as sf ON st.xStaffel = sf.xStaffel
								WHERE s.xRunde = $round
								AND s.Film = $nr
								AND sf.Startnummer = ".$val[1]
							);
							
							if(mysql_num_rows($res) == 0){
								// insert result
								$res = mysql_query("
									SELECT sst.xSerienstart FROM
										serie as s
										LEFT JOIN serienstart as sst USING(xSerie)
										LEFT JOIN start as st USING(xStart)
										LEFT JOIN staffel as sf USING(xStaffel)
									WHERE	sf.Startnummer = ".$val[1]."
									AND	s.Film = $nr
									AND	s.xRunde = $round"
								);
								
								if(mysql_num_rows($res) == 0){
									// no athlete with this registration id is started
									//if($noerror==false){ AA_printErrorMsg($strErrTimingWrongRegid); }
								}else{
									
									$row = mysql_fetch_array($res);
									mysql_query("
										INSERT INTO resultat
										SET 	Leistung = '$perf'
											, Punkte = '$points'
											, xSerienstart = ".$row[0]
									);
									
								}
							}else{
								// update
								$row = mysql_fetch_array($res);
								mysql_query("UPDATE resultat as res SET Leistung = '$perf'
										, Punkte = '$points'
									WHERE xResultat = ".$row[0]);
								
							}
						}
					}
					
					// results updated, now set status for event time table
					
					mysql_query("UPDATE runde as r SET StatusZeitmessung = 1 WHERE xRunde = $round");
					if(mysql_errno() > 0) {
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
				}
			}  // check if results exist	
			
		}
	}
	
	mysql_query("UNLOCK TABLES");
}


//
// get results from omega timing
//
// - round: wich round should be updated with results
// - arg: true -> if there are manually entered results, do not update with timing results
// - noerror: true -> supress errors (useful on auto fetching)
//
function AA_results_getTimingOmega($round, $arg=false, $noerror=false){
	global $cfgInvalidResult, $strErrTimingWrongRegid, $cfgRoundStatus;
	
	$omega = new omega($noerror);
	
	if($omega->is_configured() == false){
		return;
	}
	
	$results = $omega->get_lstrslt();
	$status = $omega->get_lststatu();
	$infos = $omega->get_lstrrslt();
	//print_r($results);
	
	if(($results && $status && $infos) == false){
		return;
	}
	
	$relay = AA_checkRelay(0, $round);
	
	if($arg){
		$sqladd = "AND ru.Status = ".$cfgRoundStatus['heats_done'];
	}else{
		$sqladd = "";
	}
	
	mysql_query("
		LOCK TABLES 
			serie as s WRITE
			, resultat as r WRITE
			, resultat WRITE
			, runde as ru WRITE
			, serienstart as sst READ
			, start as st READ
			, anmeldung as a READ
			, disziplin READ
			, wettkampf READ
			, staffel as sf READ"
	);
	
	$res_film = mysql_query("
		SELECT s.Film, ru.xWettkampf FROM 
			serie as s
			LEFT JOIN runde as ru USING(xRunde)
		WHERE s.xRunde = $round
		$sqladd"
	);
	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}else{
		
		while($row_film = mysql_fetch_array($res_film)){
			
			$nr = $row_film[0];
			$event = $row_film[1];
			//echo $event;
			// get only the official results (end of judgement)
			if($infos[$nr][8] == 'Official'){
				
				// save infos like wind
				//$timingInf[$nr] = $infos[$nr];
				$wind = $infos[$nr][5];
				if($wind == 'N/A'){
					$wind = "";
				}else{
					$wind = substr($wind,0,5);
					if(substr($wind,0,1) == "-"){
						$wind = "-".trim(substr($wind,1,4));
					}else{
						$wind = trim(substr($wind,1,4));
					}
					// round fraction hundert up
					$wind = (ceil($wind*10)/10);
					$wind = sprintf("%01.1f", $wind);
				}
				mysql_query("UPDATE serie as s SET Wind = '".$wind."'
						WHERE xRunde = $round AND Film = $nr");
				
				foreach($results as $val){
					if($val[0] == $nr){
						
						// add results to timingRes (array key is the registration id of the athlete)
						//$timingRes[$val[4]] = $val;
						// get status text for id (ok, dns, dnf, dq)
						//$timingRes[$val[4]][6] = $status[$val[6]][2];
						
						switch($status[$val[6]][2]){
							case "DNS":
							$perf = $cfgInvalidResult['DNS']['code'];
							$points = 0;
							break;
							case "DNF":
							$perf = $cfgInvalidResult['DNF']['code'];
							$points = 0;
							break;
							case "DQ":
							$perf = $cfgInvalidResult['DSQ']['code'];
							$points = 0;
							break;
							default:
							$perf = ceil($val[7] / 10);
							
							$sex = 'M';
							//if(!$relay){
								/*$sql = "SELECT Geschlecht
										  FROM serienstart AS sst
									 LEFT JOIN serie AS s USING ( xSerie )
									 LEFT JOIN START AS st ON sst.xStart = st.xStart
									 LEFT JOIN anmeldung AS a ON st.xAnmeldung = a.xAnmeldung
									 LEFT JOIN athlet USING ( xAthlet )
										 WHERE s.xRunde = ".$round."
										   AND s.Film = ".$nr."
										   AND a.Startnummer = ".$val[4].";";*/
								$sql_sex = "SELECT DISTINCT(Geschlecht) AS Geschlecht
											  FROM kategorie 
										 LEFT JOIN wettkampf USING(xKategorie) 
										 LEFT JOIN start AS st USING(xWettkampf)
										 LEFT JOIN serienstart AS sst  USING(xStart)
										 LEFT JOIN serie AS s USING(xSerie)
											 WHERE s.XRunde = ".$round." 
											   AND s.Film = ".$nr.";";
								$query = mysql_query($sql_sex);
								
								if($query && mysql_num_rows($query)==1){
									$sex = mysql_result($query, 0, 'Geschlecht');
								}
							//}
							
							$points = AA_utils_calcPoints($event, $perf, 0, $sex);
							//echo $GLOBALS['AA_ERROR'];
						}
						
						if($relay == false){
						
							$res = mysql_query("
								SELECT xResultat FROM
									resultat as r
									LEFT JOIN serienstart as sst USING(xSerienstart)
									LEFT JOIN serie as s USING (xSerie)
									LEFT JOIN start as st ON sst.xStart = st.xStart
									LEFT JOIN anmeldung as a ON st.xAnmeldung = a.xAnmeldung
								WHERE s.xRunde = $round
								AND s.Film = $nr
								AND a.Startnummer = ".$val[4]
							);
							
							if(mysql_num_rows($res) == 0){
								// insert result
								$res = mysql_query("
									SELECT sst.xSerienstart FROM
										serie as s
										LEFT JOIN serienstart as sst USING(xSerie)
										LEFT JOIN start as st USING(xStart)
										LEFT JOIN anmeldung as a USING(xAnmeldung)
									WHERE	a.Startnummer = ".$val[4]."
									AND	s.Film = $nr
									AND	s.xRunde = $round"
								);
								
								if(mysql_num_rows($res) == 0){
									// no athlete with this registration id is started
									if($noerror==false){ AA_printErrorMsg($strErrTimingWrongRegid); }
								}else{
									$row = mysql_fetch_array($res);
									mysql_query("
										INSERT INTO resultat
										SET 	Leistung = '$perf'
											, Punkte = '$points'
											, xSerienstart = ".$row[0]
									);
									/*echo ("INSERT INTO resultat as r
										SET 	Leistung = '$perf'
											, xSerienstart = ".$row[0]);*/
								}
							}else{
								// update
								$row = mysql_fetch_array($res);
								mysql_query("UPDATE resultat as r SET Leistung = '$perf'
										, Punkte = '$points'
									WHERE xResultat = ".$row[0]);
								
							}
								//
						}else{		// relay event
								//
							
							// set startnumber - 999 because of this omega trick (nbr = 999XXX)
							//$val[4] = substr($val[4],3); <-- changed
							
							$res = mysql_query("
								SELECT xResultat FROM
									resultat as r
									LEFT JOIN serienstart as sst USING(xSerienstart)
									LEFT JOIN serie as s USING (xSerie)
									LEFT JOIN start as st ON sst.xStart = st.xStart
									LEFT JOIN staffel as sf ON st.xStaffel = a.xStaffel
								WHERE s.xRunde = $round
								AND s.Film = $nr
								AND sf.Startnummer = ".$val[4]
							);
							
							if(mysql_num_rows($res) == 0){
								// insert result
								$res = mysql_query("
									SELECT sst.xSerienstart FROM
										serie as s
										LEFT JOIN serienstart as sst USING(xSerie)
										LEFT JOIN start as st USING(xStart)
										LEFT JOIN staffel as sf USING(xStaffel)
									WHERE	sf.Startnummer = ".$val[4]."
									AND	s.Film = $nr
									AND	s.xRunde = $round"
								);
								
								if(mysql_num_rows($res) == 0){
									// no athlete with this registration id is started
									if($noerror==false){ AA_printErrorMsg($strErrTimingWrongRegid); }
								}else{
									$row = mysql_fetch_array($res);
									mysql_query("
										INSERT INTO resultat
										SET 	Leistung = '$perf'
											, Punkte = '$points'
											, xSerienstart = ".$row[0]
									);
									/*echo ("INSERT INTO resultat as r
										SET 	Leistung = '$perf'
											, xSerienstart = ".$row[0]);*/
								}
							}else{
								// update
								$row = mysql_fetch_array($res);
								mysql_query("UPDATE resultat as r SET Leistung = '$perf'
										, Punkte = '$points'
									WHERE xResultat = ".$row[0]);
								
							}
							
						}
					}
				}
				
				// results updated, now set status for event time table
				
				mysql_query("UPDATE runde as ru SET StatusZeitmessung = 1 WHERE xRunde = $round");
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
			}
		}
		
	}
	
	mysql_query("UNLOCK TABLES");
}

//
// print page header
//
function AA_results_printHeader($category, $event, $round)
{
	require('./lib/cl_gui_menulist.lib.php');
	require('./lib/cl_gui_page.lib.php');

	require('./lib/common.lib.php');

	$page = new GUI_Page('event_results');
	$page->startPage();
	$page->printPageTitle($GLOBALS['strResults'] . ": " . $_COOKIE['meeting']);

	$menu = new GUI_Menulist();
	$menu->addButton($cfgURLDocumentation . 'help/event/results.html', $GLOBALS['strHelp'], '_blank');
	$menu->printMenu();
?>

<script type="text/javascript">
<!--
	function submitResult(perfForm, focus)
	{
		perfForm.submit();

		if(focus) {
			focus.perf.focus();
			focus.perf.select();
		}
	}

	function submitForm(form, focus)
	{
		form.submit();

		if(document.getElementById(focus)) {
			document.getElementById(focus).focus();
			document.getElementById(focus).select();
		}
	}

	function checkSubmit(perfForm, focus)
	{
		if(perfForm.perf.value <= 0)
		{
			if(perfForm.wind) {
				perfForm.wind.value = '';
			}
			else if(perfForm.attempts) {
				perfForm.attempts.value = '';
			}
			submitResult(perfForm, focus);
		}else{
			if(perfForm.wind && perfForm.wind.value != ''){
				submitResult(perfForm, focus);
			}
		}
	}

	// remove these functions after high jump is switched to architecture!
	function selectResult(result)
	{
		document.selection.result.value=result;
	}

	function selectAthlete(athlete)
	{
		document.selection.athlete.value=athlete;
		document.selection.submit();
	}
	
//-->
</script>

<form action='event_results.php' method='post'
	name='selection'>
	<input type='hidden' name='round' value='<?php echo $round; ?>' />
	<input type='hidden' name='athlete' value='' />
	<input type='hidden' name='result' value='' />
</form>

<table><tr>
	<td class='forms'>
		<?php	AA_printCategorySelection("event_results.php", $category); ?>
	</td>
	<td class='forms'>
		<?php	AA_printEventSelection("event_results.php", $category, $event, "post"); ?>
	</td>

<?php
	if($event > 0)		// event selected
	{
		printf("<td class='forms'>\n");
		AA_printRoundSelection("event_results.php", $category, $event, $round);
		printf("</td>\n");
	}
	if($round > 0)		// round selected
	{
		printf("<td class='forms'>\n");
		AA_printHeatSelection($round);
		printf("</td>\n");
	}

?>
</tr>
</table>
<?php
}



//
// print menu buttons
//
function AA_results_printMenu($round, $status)
{
	require('./lib/cl_gui_menulist.lib.php');
	require('./lib/common.lib.php');

	$menu = new GUI_Menulist();

	if($status == $GLOBALS['cfgRoundStatus']['results_done'])
	{
		$menu->addButton("event_results.php?arg=change_results&round=$round", $GLOBALS['strChangeResults']);
		$menu->addButton("event_rankinglists.php?round=$round", $GLOBALS['strPrint'] . " ...");

		$menu->printMenu();

		// Info: signature for invalid results
		?>
		<p/>
		<table><tr>
		<?php
		foreach($GLOBALS['cfgInvalidResult'] as $value)
		{
			echo "<td>".$value['code']." = ".$value['long']."</td>";
		}
		?>
		</tr></table>
		<?php
	}
	else
	{
		$menu->addButton("event_results.php?arg=results_done&round=$round", $GLOBALS['strEvaluateResults']);
		
		$res = mysql_query("SELECT Zeitmessung FROM runde LEFT JOIN wettkampf USING(xWettkampf) WHERE xRunde = $round");
		$row = mysql_fetch_array($res);
		if($row[0] == 1){
			$menu->addButton("event_results.php?arg=time_measurement&round=$round", $GLOBALS['strGetResultsFromTM']);
		}
		if($status == $GLOBALS['cfgRoundStatus']['heats_done']){
			$menu->addButton("event_heats.php?arg=del_heats&round=$round", $GLOBALS['strDeleteHeats']);
		}else{
			$menu->addButton("event_results.php?arg=del_results&round=$round", $GLOBALS['strDeleteResults']);
		}
		$menu->printMenu();

		// Info: signature for invalid results
		?>
		<p/>
		<table><tr>
		<?php
		foreach($GLOBALS['cfgInvalidResult'] as $value)
		{
			echo "<td>".$value['long']." = ".$value['code']."</td>";
		}
		?>
		</tr></table>
		<?php
	}
}



//
// add/change result	(Hochsprung, Wurf, Weit)
//
function AA_results_update($performance, $info, $points = 0)
{
	require('./lib/common.lib.php');

	if(($performance != NULL) )
	{
		
		if(!empty($_POST['item']))	// result provided -> change it
		{
			if(AA_checkReference("resultat", "xResultat", $_POST['item']) == 0)
			{
				AA_printErrorMsg($GLOBALS['strErrAthleteNotInHeat']);
			}
			else
			{
				mysql_query("
					UPDATE resultat SET
						Leistung = $performance
						, Info = '$info'
						, Punkte = $points
					WHERE xResultat = " . $_POST['item']
				);

				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
			}
		}
		else // no result provided -> add result
		{
			mysql_query("
				INSERT INTO resultat SET
					Leistung = $performance
					, Info= '$info'
					, Punkte = $points
					, xSerienstart = " . $_POST['start']
			);

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
		}	// ET add or change
	}	// ET valid data provided
}



//
// delete result	(Hochsprung, Wurf, Weit!)
//
function AA_results_delete($round, $item)
{
	require('./lib/common.lib.php');
	require('./lib/utils.lib.php');

	AA_utils_changeRoundStatus($round, $GLOBALS['cfgRoundStatus']['results_in_progress']);
	if(!empty($GLOBALS['AA_ERROR'])) {
		AA_printErrorMsg($GLOBALS['AA_ERROR']);
	}

	mysql_query("LOCK TABLES resultat WRITE");

	mysql_query("DELETE FROM resultat"
				. " WHERE xResultat=" . $item);

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}

	mysql_query("UNLOCK TABLES");
}


//
// delete all results for one round
// (invoked if the user wants to delete the heats)
//
function AA_results_deleteResults($round){
	
	require('./lib/common.lib.php');
	require('./lib/utils.lib.php');
	
	// get each heat start and delete results for it
	mysql_query("LOCK TABLES serie as s READ, serienstart as sst READ, resultat WRITE");
	
	$res = mysql_query("	SELECT sst.xSerienstart FROM
					serienstart as sst
					, serie as s
				WHERE
					sst.xSerie = s.xSerie
				AND	s.xRunde = $round");
	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}else{
		
		while($row = mysql_fetch_array($res)){
			
			mysql_query("DELETE FROM resultat WHERE xSerienstart = ".$row[0]);
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			
		}
		
	}
	
	mysql_query("UNLOCK TABLES");
	
	// change status to heats_done
	AA_utils_changeRoundStatus($round, $GLOBALS['cfgRoundStatus']['heats_done']);
	if(!empty($GLOBALS['AA_ERROR'])) {
		AA_printErrorMsg($GLOBALS['AA_ERROR']);
	}
	
}

//
// get evaluation type
//
function AA_results_getEvaluationType($round)
{
	require('./lib/common.lib.php');

	$eval = $GLOBALS['cfgEvalType'][$GLOBALS['strEvalTypeDiscDefault']];

	if(!empty($round))
	{
		$result = mysql_query("SELECT rundentyp.Wertung"
									. " FROM rundentyp"
									. ", runde"
									. " WHERE rundentyp.xRundentyp = runde.xRundentyp"
									. " AND runde.xRunde = " . $round);

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			if(mysql_num_rows($result) > 0) {
				$row = mysql_fetch_row($result);
				$eval = $row[0];
			}
			mysql_free_result($result);
		}
	}
	return $eval;
}



//
// get program mode
//
function AA_results_getProgramMode()
{
	require('./lib/common.lib.php');

	$prog_mode = 0;
	$result = mysql_query("	SELECT
										ProgrammModus
									FROM
										meeting
									WHERE xMeeting = " . $_COOKIE['meeting_id']
								);

	if(mysql_errno() > 0) {		// DB error
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		$row = mysql_fetch_row($result);
		$prog_mode = $row[0];
		mysql_free_result($result);
	}
	return $prog_mode;
}




//
// reset qualification
//
function AA_results_resetQualification($round)
{
	global $cfgQualificationType;
	require('./lib/common.lib.php');

	if(!empty($round))
	{
		mysql_query("LOCK TABLES serie READ, serienstart WRITE");

		// get athletes by qualifying rank (random order if same rank)
		$result =	mysql_query("SELECT serienstart.xSerienstart"
							. " FROM serienstart"
							. ", serie"
							. " WHERE serienstart.Qualifikation > 0"
							// don't requalify athletes who waived to continue
							. " AND serienstart.Qualifikation != ".$cfgQualificationType['waived']['code']
							. " AND serienstart.xSerie = serie.xSerie"
							. " AND serie.xRunde = " . $round);

		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			while($row = mysql_fetch_row($result))
			{
				mysql_query("UPDATE serienstart SET"
							. " Qualifikation = 0"
							. " WHERE xSerienstart = " . $row[0]);

				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
			}
			mysql_free_result($result);
		}
		mysql_query("UNLOCK TABLES");
	}
}


//
// Set 'Did not start' for all athletes not having a result entered.
//
function AA_results_setNotStarted($round)
{
	require('./lib/common.lib.php');

	if(!empty($round))
	{
		mysql_query("LOCK TABLES serie READ"
					. ", resultat WRITE, serienstart WRITE");

		$result = mysql_query("SELECT serienstart.xSerienstart"
								. " FROM serienstart"
								. ", serie"
								. " LEFT JOIN resultat"
								. " ON serienstart.xSerienstart = resultat.xSerienstart"
								. " WHERE resultat.xSerienstart is NULL"
								. " AND serienstart.xSerie = serie.xSerie"
								. " AND serie.xRunde = " . $round);
	
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			// add result "Did Not Start" to every athlete
			$performance = $GLOBALS['cfgInvalidResult']['DNS']['code'];
			$info = $GLOBALS['cfgResultsInfoFill'];
			while($row = mysql_fetch_row($result))
			{
				mysql_query("INSERT INTO resultat SET"
							. " Leistung=" . $performance
							. ", Info='" . $info
							. "', xSerienstart=" . $row[0]);

				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}

				mysql_query("UPDATE serienstart SET"
							. " Rang = 0"
							. " WHERE xSerienstart=" . $row[0]);

				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
			}
			mysql_free_result($result);
		}
		mysql_query("UNLOCK TABLES");
	}	// ET valid round
	return;
}



}		// AA_RESULTS_LIB_INCLUDED
?>
