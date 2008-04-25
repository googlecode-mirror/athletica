<?php

/**********
 *
 *	print_event_enrolement.php
 *	-------------------------
 *	
 */

include('./config.inc.php');
require('./lib/common.lib.php');
require('./lib/cl_print_entrypage.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
	}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

$cCat = 0; // vars for combined event
$cCode = 0;

if(!empty($_GET['event'])) {
	$argument = "w.xWettkampf = " . $_GET['event'];
}
else if(!empty($_GET['category'])) {
	$argument = "w.xKategorie = " . $_GET['category']
				. " AND w.xMeeting = " . $_COOKIE['meeting_id']
				. " AND d.Appellzeit > 0";
}
elseif(!empty($_GET['comb'])){
	list($cCat, $cCode) = explode("_", $_GET['comb']);
	$argument = "w.xKategorie = $cCat
			AND w.Mehrkampfcode = $cCode
			AND w.xMeeting = ". $_COOKIE['meeting_id'];
}
else {
	$argument = "w.xMeeting = " . $_COOKIE['meeting_id'];
				//. " AND d.Appellzeit > 0";
}

$pagebreak = "no";
if(isset($_GET['pagebreak'])){
	$pagebreak = $_GET['pagebreak'];
}

// start a new HTML page for printing
$doc = new PRINT_EnrolementPage($_COOKIE['meeting']);

// get event title data
$result = mysql_query("SELECT d.Name"
					. ", k.Name"
					. ", DATE_FORMAT(r.Datum, '$cfgDBdateFormat')"
					. ", TIME_FORMAT(r.Appellzeit, '$cfgDBtimeFormat')"
					. ", w.xWettkampf"
					. ", r.xRunde"
					. ", r.Status"
					. ", w.xKategorie"
					. ", TIME_FORMAT(r.Startzeit, '$cfgDBtimeFormat')"
					. ", TIME_FORMAT(r.Stellzeit, '$cfgDBtimeFormat')"
					. ", w.Mehrkampfcode"
					. ", dm.Name"
					. " FROM disziplin AS d"
					. ", kategorie AS k"
					. ", wettkampf AS w"
					. ", runde AS r"
					. " LEFT JOIN disziplin as dm ON w.Mehrkampfcode = dm.Code"
					. " WHERE " . $argument
					. " AND r.xWettkampf = w.xWettkampf"
					. " AND d.xDisziplin = w.xDisziplin"
					. " AND k.xKategorie = w.xKategorie"
					. " ORDER BY w.xKategorie, w.Mehrkampfcode, r.xWettkampf, r.Datum, r.Startzeit");

if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else
{
	$i = 0;
	$event = 0;
	$xCat = 0;
	$xComb = 0;
	
	while($row = mysql_fetch_row($result))
	{
		if($row[4] != $event)	// only first round per event
		{
			// change round status only if nothing done yet
			if($row[6] == $cfgRoundStatus['open']) {	
				AA_utils_changeRoundStatus($row[5],
					$cfgRoundStatus['enrolement_pending']);
				if(!empty($GLOBALS['AA_ERROR'])) {
					AA_printErrorMsg($GLOBALS['AA_ERROR']);
				}
			}
			
			// handle page break
		  if($i > 0 && $pagebreak == "discipline") {	// not first event
			  $doc->insertPageBreak();
		  }
		  if($i > 0 && $xCat != $row[7] && $pagebreak == "category"){
			  $doc->insertPageBreak();
		  }
		  $xCat = $row[7];
		  $i++;

		  $event = $row[4];
		  $relay = AA_checkRelay($event);
		  $combined = AA_checkCombined($event);
		  $svm = AA_checkSVM($event);
		  if($svm){
			  $sortAddition = "t.Name, ";
		  }
		  
		  if($combined && $xComb == $row[10]){
			  continue;
		  }
		  $xComb = $row[10];
		  
		  if($combined){
			  $doc->event = $row[11];
		  }else{
			  $doc->event = $row[0];
		  }
		  $doc->cat = $row[1];
		  $et = "";
		  $ot = "";
		  if($row[3] != "00:00"){ // add enrolement time
			  $et = ", " . $row[3];
		  }
		  $ot .= " ($strStarttime $row[8]"; // add starttime
		  if($row[9] != "00:00"){ // add manipulation time
			  $ot .= ", $strManipulationTime $row[9]";
		  }
		  $ot .= ")";
		  $doc->time = $strEnrolement. ": " . $row[2] . $et;
		  $doc->timeinfo = $ot;

		  // read event entries
		  if($relay == FALSE) {		// single event
			  if($combined){
				  $query = "SELECT a.Startnummer"
						  . ", at.Name"
						  . ", at.Vorname"
						  . ", at.Jahrgang"
						  . ", IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo)"
						  . ", a.BestleistungMK"
						  . ", d.Typ"
						  . ", IF(at.xRegion = 0, at.Land, re.Anzeige)"
						  . " FROM anmeldung AS a"
						  . ", athlet AS at"
						  . ", start AS s"
						  . ", verein AS v"
						  . ", wettkampf AS w"
						  . "  LEFT JOIN region as re ON at.xRegion = re.xRegion"
						  . "  LEFT JOIN disziplin as d ON w.xDisziplin = d.xDisziplin"
						  . " WHERE s.xWettkampf = w.xWettkampf"
						  . " AND w.xKategorie = $xCat"
						  . " AND w.Mehrkampfcode = $xComb"
						  . " AND w.xMeeting = ". $_COOKIE['meeting_id']
						  . " AND s.xAnmeldung = a.xAnmeldung"
						  . " AND a.xAthlet = at.xAthlet"
						  . " AND at.xVerein = v.xVerein"
						  . " GROUP BY a.xAnmeldung"
						  . " ORDER BY at.Name, at.Vorname";
			  }else{
				  $query = "SELECT a.Startnummer"
						  . ", at.Name"
						  . ", at.Vorname"
						  . ", at.Jahrgang"
						  . ", if('$svm', t.Name, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo))"
						  . ", s.Bestleistung"
						  . ", d.Typ"
						  . ", IF(at.xRegion = 0, at.Land, re.Anzeige)"
						  . " FROM anmeldung AS a"
						  . ", athlet AS at"
						  . ", start AS s"
						  . ", verein AS v"
						  . "  LEFT JOIN region as re ON at.xRegion = re.xRegion"
						  . "  LEFT JOIN wettkampf as w ON s.xWettkampf = w.xWettkampf"
						  . "  LEFT JOIN disziplin as d ON w.xDisziplin = d.xDisziplin"
						  . "  LEFT JOIN team as t ON a.xTeam = t.xTeam"
						  . " WHERE s.xWettkampf = " . $event
						  . " AND s.xAnmeldung = a.xAnmeldung"
						  . " AND a.xAthlet = at.xAthlet"
						  . " AND at.xVerein = v.xVerein"
						  . " ORDER BY $sortAddition at.Name, at.Vorname";
			  }
		  }
		  else {							// relay event
			  $query = "SELECT st.Name"
					  . ", v.Name"
					  . " FROM staffel AS st"
					  . ", start AS s"
					  . ", verein AS v"
					  . " WHERE s.xWettkampf = " . $event
					  . " AND s.xStaffel = st.xStaffel"
					  . " AND st.xVerein = v.xVerein"
					  . " ORDER BY v.Sortierwert, st.Name";
			//
			// get each athlete from all registered relays
			//
			$query = "SELECT s2.xStart"
					. ", s2.Anwesend"
					. ", st.Name"
					. ", if('$svm', t.Name, v.Name)"
					. ", a.Startnummer"
					. ", at.Name"
					. ", at.Vorname"
					. ", at.Jahrgang"
					. ", at.Land"
					. " FROM staffel AS st"
					. ", start AS s"
					. ", verein AS v"
					. ", staffelathlet as stat"
					. ", start as s2"
					. ", anmeldung as a"
					. ", athlet as at"
					. "  LEFT JOIN team as t ON st.xTeam = t.xTeam"
					. " WHERE s.xWettkampf = " . $event
					. " AND s.xStaffel = st.xStaffel"
					. " AND st.xVerein = v.xVerein"
					. " AND stat.xStaffelstart = s.xStart"
					. " AND s2.xStart = stat.xAthletenstart"
					. " AND a.xAnmeldung = s2.xAnmeldung"
					. " AND at.xAthlet = a.xAthlet"
					. " GROUP BY stat.xAthletenstart"
					. " ORDER BY $sortAddition v.Sortierwert, st.Name, a.Startnummer";
		  }

		  $res = mysql_query($query);

		  if(mysql_errno() > 0)		// DB error
		  {
			  AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		  }
		  else if(mysql_num_rows($res) > 0)  // data found
		  {
			  $l = 0;		// line counter

			  // full list
			  while ($row = mysql_fetch_row($res))
			  {
				  if($l == 0) {					// new page, print header line
					  $doc->printTitle();
					  printf("<table>\n");
					  $doc->printHeaderLine($relay, $svm);
				  }

				  if($relay == FALSE)
				  {
						// show top performance of athletes
						if(($row[6] == $cfgDisciplineType[$strDiscTypeJump])
							|| ($row[6] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
							|| ($row[6] == $cfgDisciplineType[$strDiscTypeThrow])
							|| ($row[6] == $cfgDisciplineType[$strDiscTypeHigh])) {
							$perf = AA_formatResultMeter($row[5]);
						}else {
							if(($row[6] == $cfgDisciplineType[$strDiscTypeTrack])
							|| ($row[6] == $cfgDisciplineType[$strDiscTypeTrackNoWind])){
								$perf = AA_formatResultTime($row[5], true, true);
							}else{
								$perf = AA_formatResultTime($row[5], true);
							}
						}
						if($combined){
							$perf = $row[5]; // points
						}
						$doc->printLine($row[0], $row[1] . " " . $row[2],
							AA_formatYearOfBirth($row[3]), $row[4], $row[7], $perf);
				  }
				  else
				  {
						$doc->printLine($row[4], $row[5] . " " . $row[6],
							AA_formatYearOfBirth($row[7]), $row[2], $row[8], "", $row[3]);
						//$doc->printLine('', $row[0], '', $row[1]);
				  }
				  $l++;			// increment line count
			  }
			  printf("</table>\n");
			  mysql_free_result($res);
		  }		// ET DB error
		}		// END same round
	}		// END WHILE events
	mysql_free_result($result);
}	// ET DB error event data


$doc->endPage();		// end a HTML page for printing

?>
