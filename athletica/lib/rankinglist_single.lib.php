<?php

/**********
 *
 *	rankinglist single events
 *	
 */

if (!defined('AA_RANKINGLIST_SINGLE_LIB_INCLUDED'))
{
	define('AA_RANKINGLIST_SINGLE_LIB_INCLUDED', 1);

function AA_rankinglist_Single($category, $event, $round, $formaction, $break, $cover, $biglist = false, $cover_timing = false, $date = '%',  $show_efforts = 'none')
{

require('./lib/cl_gui_page.lib.php');
require('./lib/cl_print_page.lib.php');
require('./lib/cl_export_page.lib.php');

require('./lib/common.lib.php');
require('./lib/results.lib.php');

if(AA_connectToDB() == FALSE)	{ // invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}


// set up ranking list selection
$selection = '';

if($round > 0) {	// show a specific round
	$selection = "r.xRunde =" . $round;
}
else if($category == 0) {		// show all disciplines for every category
	$selection = "w.xMeeting =" . $_COOKIE['meeting_id'];
}
else if ($event == 0) {	// show all disciplines for a specific category
	$selection = "w.xMeeting = " . $_COOKIE['meeting_id']
					. " AND w.xKategorie = " . $category;
}
else if($round == 0) {	// show all rounds for a specific event
	$selection = "w.xWettkampf =" . $event;
}

// get event rounds from DB
$results = mysql_query("
	SELECT
		r.xRunde
		, k.Name
		, d.Name
		, d.Typ
		, w.xWettkampf
		, r.QualifikationSieger
		, r.QualifikationLeistung
		, w.Punkteformel
		, w.Windmessung
		, r.Speakerstatus
		, d.Staffellaeufer
		, CONCAT(DATE_FORMAT(r.Datum,'$cfgDBdateFormat'), ' ', TIME_FORMAT(r.Startzeit, '$cfgDBtimeFormat'))
		, w.xDisziplin
	FROM
		wettkampf AS w
		, kategorie AS k
		, disziplin as d
		, runde AS r
	WHERE " . $selection . "
	AND k.xKategorie = w.xKategorie
	AND d.xDisziplin = w.xDisziplin
	AND r.xWettkampf = w.xWettkampf
	AND r.Status = " . $cfgRoundStatus['results_done'] . " 
	AND r.Datum LIKE '".$date."'
	ORDER BY
		k.Anzeige
		, d.Anzeige
		, r.Datum
		, r.Startzeit
");

if(mysql_errno() > 0) {		// DB error
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else {
	
	$limitRankSQL = "";
	$limitRank = false;
	if($_GET['limitRank'] == "yes"){ // check if ranks are limited, but limitRankSQL will set only if export is pressed
		if(!empty($_GET['limitRankFrom']) && !empty($_GET['limitRankTo'])){
			$limitRank = true;
		}
	}
	
	// start a new HTML display page
	if(($formaction == 'view')
		||	($formaction == 'speaker')) {	// display page
		$list = new GUI_RankingList($_COOKIE['meeting']);
		$list->printPageTitle("$strRankingLists " . $_COOKIE['meeting']);
	}
	// start a new HTML print page
	elseif($formaction == "print") {
		$list = new PRINT_RankingList($_COOKIE['meeting']);
		if($cover == true) {		// print cover page 
			$list->printCover($GLOBALS['strResults'], $cover_timing);
		}
	}
	// export ranking
	elseif($formaction == "exportpress"){
		$list = new EXPORT_RankingListPress($_COOKIE['meeting'], 'txt');
		if($limitRank){
			$limitRankSQL = " AND ss.Rang <= ".$_GET['limitRankTo']." AND ss.Rang >= ".$_GET['limitRankFrom']." ";
		}
	}elseif($formaction == "exportdiplom"){
		$list = new EXPORT_RankingListDiplom($_COOKIE['meeting'], 'csv');
		if($limitRank){
			$limitRankSQL = " AND ss.Rang <= ".$_GET['limitRankTo']." AND ss.Rang >= ".$_GET['limitRankFrom']." ";
		}
	}
	
	// initialize variables
	$cat = '';
	$evnt = 0;
	
	while($row = mysql_fetch_row($results))
	{
		// for a combined event, the rounds are merged, so jump until the next event
		if($cRounds > 1){
			$cRounds--;
			continue;
		}
		$roundSQL = "s.xRunde = $row[0]";
		$cRounds = 0;
		
		// check page  break
		if(is_a($list, "PRINT_RankingList")	// page for printing
			&& ($cat != '')						// not first result row
			&& (($break == 'discipline')	// page break after each discipline
				|| (($break == 'category')	// or after new category
					&& ($row[1] != $cat))))
		{
			$list->insertPageBreak();
		}
		
		if(($row[3] == $cfgDisciplineType[$strDiscTypeTrack])
				|| ($row[3] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
				|| ($row[3] == $cfgDisciplineType[$strDiscTypeRelay]))
		{
			$eval = $cfgEvalType[$strEvalTypeHeat];
		}
		else
		{
			$eval = $cfgEvalType[$strEvalTypeAll];
		}

		$roundName = '';
		$type = '';
		$res = mysql_query("
			SELECT
				rundentyp.Name
				, rundentyp.Typ
				, rundentyp.Wertung
			FROM
				runde
				, rundentyp
			WHERE runde.xRunde = $row[0]
			AND rundentyp.xRundentyp = runde.xRundentyp
		");

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			if(mysql_num_rows($res) > 0) {
				$row_rt = mysql_fetch_row($res);
				
				if($row_rt[1] == '0'){
					$type = " ";
					$row_rt[0] = '';
				}else{
					$type = $row_rt[0]." ";
				}
				
				$eval = $row_rt[2];
				if($round != 0) {		// specific round selected
					$roundName = $row_rt[0];
				}
			}
			mysql_free_result($res);
		}

		if($evnt != $row[4])		// new event -> repeat title
		{
			// if this is a combined event, dont fragment list by rounds
			$combined = AA_checkCombined($row[4]);
			// not selectet a specific round
			if($round == 0 && $combined){
				$res_c = mysql_query("SELECT 
								r.xRunde
							FROM
								wettkampf as w
								, runde as r
							WHERE	w.xWettkampf = $row[4]
							AND	r.xWettkampf = w.xWettkampf");
				if(mysql_errno() > 0){
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}else{
					$cRounds = mysql_num_rows($res_c);
					$roundSQL = "s.xRunde IN (";
					while($row_c = mysql_Fetch_array($res_c)){
						$roundSQL .= $row_c[0].",";
					}
					$roundSQL = substr($roundSQL, 0, -1).")";
				}
			}
			
			// set up category and discipline title information
			$list->printSubTitle($row[1], $row[2], $roundName);
		
			if(($formaction == 'speaker') 	// speaker display page
				&& (AA_getNextRound($row[4], $row[0]) == 0))
			{
				// last round: show ceremony status
				$list->printCeremonyStatus($row[0], $row[9]);
			}

			// print qualification mode if round selected
			$info = '';
			if(($round > 0)
				&& (($row[5] > 0) || ($row[6] > 0)))
			{
				$info = "$strQualification: "
							. $row[5] . " $strQualifyTop, "
							. $row[6] . " $strQualifyPerformance";
				$list->printInfoLine($info);
				$qual_mode = TRUE;
			}

			// print qualification descriptions if required 
			$info = '';
			if(($row[5] > 0) || ($row[6] > 0))
			{
				foreach($cfgQualificationType as $qt)
				{
					$info = $info . $qt['token'] . " ="
							. $qt['text'] . "&nbsp;&nbsp;&nbsp;";
				}
				$list->printInfoLine($info);
				$qual_mode = TRUE;
			}
			$evnt = $row[4];	// keep event ID
		} // ET new event

		$relay = AA_checkRelay($row[4]);	// check, if this is a relay event
		$svm = AA_checkSVM($row[4]);

		// If round evaluated per heat, group results accordingly	
		$order_heat = "";
		if($eval == $cfgEvalType[$strEvalTypeHeat]) {	// eval per heat
			$order_heat = "heatid, ";
		}

		$valid_result ="";
		// Order performance depending on discipline type
		if(($row[3] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
			|| ($row[3] == $cfgDisciplineType[$strDiscTypeThrow]))
		{
			$order_perf = "DESC";
		}
		else if($row[3] == $cfgDisciplineType[$strDiscTypeJump])
		{
			if ($row[8] == 1) {			// with wind
				$order_perf = "DESC, r.Info ASC";
			}
			else {							// without wind
				$order_perf = "DESC";
			}
		}
		else if($row[3] == $cfgDisciplineType[$strDiscTypeHigh])
		{
			$order_perf = "DESC";
			$valid_result =	" AND (r.Info LIKE '%O%'"
										. " OR r.Leistung < 0)";
		}
		else
		{
			$order_perf = "ASC";
		}
		
		// get all results ordered by ranking; for invalid results (Rang=0), the
		// rank is set to max_rank to put them to the end of the list.
		$max_rank = 999999999;
		
		$sql_leistung = ($order_perf=='ASC') ? "r.Leistung" : "IF(r.Leistung<0, (If(r.Leistung = -99, -9, r.Leistung) * -1), r.Leistung)";
		if($relay == FALSE) {
			$query = "
				SELECT
					ss.xSerienstart
					, IF(ss.Rang=0, $max_rank,ss.Rang) AS rank
					, ss.Qualifikation
					, ".$sql_leistung." AS leistung_neu
					, r.Info
					, s.Bezeichnung
					, s.Wind
					, r.Punkte
					, IF('$svm', t.Name, 
						IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo)
						)
					, at.Name
					, at.Vorname
					, at.Jahrgang
					, LPAD(s.Bezeichnung,5,'0') as heatid
					, IF(at.xRegion = 0, at.Land, re.Anzeige) as Land
					, at.xAthlet
				FROM
					serie AS s USE INDEX (Runde)
					, serienstart AS ss
					, resultat AS r
					, start AS st
					, anmeldung AS a
					, athlet AS at
					, verein AS v
					LEFT JOIN region as re ON at.xRegion = re.xRegion
					LEFT JOIN team AS t ON a.xTeam = t.xTeam
				WHERE $roundSQL
				AND ss.xSerie = s.xSerie
				AND r.xSerienstart = ss.xSerienstart
				AND st.xStart = ss.xStart
				AND a.xAnmeldung = st.xAnmeldung
				AND at.xAthlet = a.xAthlet
				AND v.xVerein = at.xVerein
				$limitRankSQL
				$valid_result
				ORDER BY
					$order_heat
					rank
					, leistung_neu "
					. $order_perf ."
					, at.Name
					, at.Vorname";
		}
		else {						// relay event
			$query = "
				SELECT
					ss.xSerienstart
					, IF(ss.Rang=0, $max_rank,ss.Rang) AS rank
					, ss.Qualifikation
					, ".$sql_leistung." AS leistung_neu
					, r.Info
					, s.Bezeichnung
					, s.Wind
					, r.Punkte
					, if('$svm', t.Name, v.Name)
					, sf.Name
					, LPAD(s.Bezeichnung,5,'0') as heatid
					, st.xStart
				FROM
					serie AS s USE INDEX (Runde)
					, serienstart AS ss
					, resultat AS r
					, start AS st
					, staffel AS sf
					, verein AS v
					LEFT JOIN team AS t ON sf.xTeam = t.xTeam
				WHERE s.xRunde = $row[0]
				AND ss.xSerie = s.xSerie
				AND r.xSerienstart = ss.xSerienstart
				AND st.xStart = ss.xStart
				AND sf.xStaffel = st.xStaffel
				AND v.xVerein = sf.xVerein
				$limitRankSQL
				$valid_result
				GROUP BY
					r.xSerienstart
				ORDER BY
					$order_heat
					rank
					, r.Leistung "
					. $order_perf ."
					, sf.Name";
		}

		$res = mysql_query($query);
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else {

			// initialize variables
			$heat = '';
			$h = 0;
			$info = '';
			$id = '';
			$r = '';
			
			$list->startList();
			// process every result
			while($row_res = mysql_fetch_row($res))
			{
				$row_res[3] = ($row_res[3]==1 || $row_res[3]==2 || $row_res[3]==3 || $row_res[3]==4) ? ($row_res[3] * -1) : (($row_res[3]==9) ? -99 : $row_res[3]);
				
				if($row_res[0] != $id)	// athlete not processed yet
				{
					if(($h == 0)						// first header line or ...
						|| (($row_res[5] != $heat) // new header after each heat
							&& ($eval == $cfgEvalType[$strEvalTypeHeat])))
					{
						// heat name
						if($eval == $cfgEvalType[$strEvalTypeHeat]) {
							if(empty($type))	{			// no round type defined
								$type = $strFinalround . " ";
							}
							$title = $type . $row_res[5];	// heat name with nbr.
						}
						else {
							$title = $type;	// heat name withour nbr.
						}
						
						$title = trim($title);

						// wind per heat
						if(($row[3] == $cfgDisciplineType[$strDiscTypeTrack])
								&& ($row[8] == 1)
								&& ($eval == $cfgEvalType[$strEvalTypeHeat]))
						{
							$heatwind = $row_res[6];		// wind per heat
						}
						else {
							$heatwind = '';					// no wind 
						}

						$wind= FALSE;
						if(($row[8] == 1) 
							&& ($row[3] == $cfgDisciplineType[$strDiscTypeJump]) 
							|| (($row[3] == $cfgDisciplineType[$strDiscTypeTrack]) 
								&& ($eval == $cfgEvalType[$strEvalTypeAll])))
						{
							$wind= TRUE;
						}

						// add column header 'points' if required
						$points= FALSE;
						if($row[7] != '0') {
							$points= TRUE;
						}

						if ($show_efforts == 'sb_pb'){
							$base_perf = true;
						}
						
						$list->printHeaderLine($title, $relay, $points, $wind, $heatwind, $row[11], $svm, $base_perf, $qual_mode);

						$heat = $row_res[5];		// keep heat description
						$h++;						// increment if evaluation per heat
					}

					// rank
					if(($row_res[1]==$max_rank) 		// invalid result
						|| ($r == $row_res[1])) {		// same rank as previous
						$rank='';
					}
					else {
						$rank= $row_res[1];
					}
					$r= $row_res[1];		// keep rank

					// name
					$name = $row_res[9];
					if($relay == FALSE) {
						$name = $name . " " . $row_res[10];
					}

					// year of birth
					if($relay == FALSE) {
						$year = AA_formatYearOfBirth($row_res[11]);
					}
					else {
						$year = '';
					}
					
					// year of birth
					if($relay == FALSE) {
						$land = ($row_res[13]!='' && $row_res[13]!='-') ? $row_res[13] : '';
					}
					else {
						$year = '';
					}

					// performance
					if($row_res[3] < 0) {	// invalid result
						foreach($cfgInvalidResult as $value)	// translate value
						{
							if($value['code'] == $row_res[3]) {
								$perf = $value['short'];
							}
						}
					}
					else if(($row[3] == $cfgDisciplineType[$strDiscTypeJump])
						|| ($row[3] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
						|| ($row[3] == $cfgDisciplineType[$strDiscTypeThrow])
						|| ($row[3] == $cfgDisciplineType[$strDiscTypeHigh])) {
						$perf = AA_formatResultMeter($row_res[3]);
					}
					else {
						if(($row[3] == $cfgDisciplineType[$strDiscTypeTrack])
						|| ($row[3] == $cfgDisciplineType[$strDiscTypeTrackNoWind])){
							$perf = AA_formatResultTime($row_res[3], true, true);
						}else{
							$perf = AA_formatResultTime($row_res[3], true);
						}
					}

					$qual = '';
					if($row_res[2] > 0) {	// Athlete qualified
						foreach($cfgQualificationType as $qtype)
						{
							if($qtype['code'] == $row_res[2]) {
								$qual = $qtype['token'];
							}
						}
					}	// ET athlete qualified

					// points for performance
					$points = '';
					if($row[7] != '0') {
						$points = $row_res[7];
					}

					// wind info
					$wind = '';
					$secondResult = false;
					if($r != $max_rank) 	// valid result
					{
						if(($row[3] == $cfgDisciplineType[$strDiscTypeJump])
							&& ($row[8] == 1))
						{
							$wind = $row_res[4];
							
							//
							// if wind bigger than max wind (2.0) show the next best result without wind too
							//
							if($wind > 2){
								$res_wind = mysql_query("
										SELECT Info, Leistung FROM
											resultat
										WHERE
											xSerienstart = $row_res[0]
										ORDER BY
											Leistung ASC");
								if(mysql_errno() > 0) {		// DB error
									AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
								}else{
									while($row_wind = mysql_fetch_array($res_wind)){
										
										if($row_wind[0] <= 2){
											$secondResult = true;
											$wind2 = $row_wind[0].")";
											$perf2 = "(".AA_formatResultMeter($row_wind[1]);
										}
										
									}
								}
							}
						}
						else if(($row[3] == $cfgDisciplineType[$strDiscTypeTrack])
							&& ($row[8] == 1)
							&& ($eval == $cfgEvalType[$strEvalTypeAll])) 
						{
							$wind = $row_res[6];
						}
					}
					
					// ioc country code
					$ioc = '';
					if($relay == false){
						$ioc = $row_res[13];
					}
					
					//show performances from base
					if($show_efforts == 'sb_pb'){
						$sql = "SELECT 
									season_effort
									, DATE_FORMAT(season_effort_date, '%d.%m.%Y') AS sb_date
									, season_effort_event
									, best_effort
									, DATE_FORMAT(best_effort_date, '%d.%m.%Y') AS pb_date
									, best_effort_event
									, season
						FROM 
							base_performance
						LEFT JOIN 
							base_athlete USING (id_athlete)
						LEFT JOIN 
							disziplin ON (discipline = Code)
						LEFT JOIN 
							athlet ON (license = Lizenznummer)
						WHERE 
							xAthlet = $row_res[14]
							AND xDisziplin = $row[12]
							AND season = 'I'";
						$res_perf = mysql_query($sql);
						//echo $sql;
						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}else{
							if ($res_perf){
								$row_pref = mysql_fetch_array($res_perf);
								
								if(($row[3] == $cfgDisciplineType[$strDiscTypeJump])
									|| ($row[3] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
									|| ($row[3] == $cfgDisciplineType[$strDiscTypeThrow])
									|| ($row[3] == $cfgDisciplineType[$strDiscTypeHigh])) {
									$sb_perf = AA_formatResultMeter(str_replace(".", "", $row_pref['season_effort']));
									$pb_perf = AA_formatResultMeter(str_replace(".", "", $row_pref['best_effort']));
									//highlight sb or pb if new performance is better
									if ($perf>$sb_perf || $perf>$pb_perf){
										$perf = "<b>$perf</b>";
									}

								} else {
									if(($row[3] == $cfgDisciplineType[$strDiscTypeTrack])
									|| ($row[3] == $cfgDisciplineType[$strDiscTypeTrackNoWind])){
										$sb_perf = AA_formatResultTime(str_replace(":", "", $row_pref['season_effort']), true, true);
										$pb_perf = AA_formatResultTime(str_replace(":", "", $row_pref['best_effort']), true, true);
									}else{
										$sb_perf = AA_formatResultTime(str_replace(":", "", $row_pref['season_effort']), true);
										$pb_perf = AA_formatResultTime(str_replace(":", "", $row_pref['best_effort']), true);
									}										
									//highlight sb or pb if new performance is better
									if ($perf<$sb_perf || $perf<$pb_perf){
										$perf = "<b>$perf</b>";
									}
								}
								
								if (!empty($row_pref['season_effort'])){
									$sb = "<abbr class=\"info\">$sb_perf<span>$row_pref[sb_date]<br>$row_pref[season_effort_event]</span></abbr>";
								} else {
									$sb = "&nbsp;";
								}
								
								if (!empty($row_pref['best_effort'])){
									$pb = "<abbr class=\"info\">$pb_perf<span>$row_pref[pb_date]<br>$row_pref[best_effort_event]</span></abbr>";
								} else {
									$pb = "&nbsp;";
								}
							}		
						}		
					}
					
					
					$list->printLine($rank, $name, $year, $row_res[8], $perf
						, $wind, $points, $qual, $ioc, $sb, $pb,$qual_mode);
					if($secondResult){
						$list->printLine("","","","",$perf2,$wind2,"","","","","",$qual_mode);
					}
						
					// 
					// if relay, show started ahtletes in right order under the result
					//
					if($relay){
						
						$res_at = mysql_query("
								SELECT at.Vorname, at.Name, at.Jahrgang FROM
									staffelathlet as sfat
									LEFT JOIN start as st ON sfat.xAthletenstart = st.xStart
									LEFT JOIN anmeldung as a USING(xAnmeldung)
									LEFT JOIN athlet as at USING(xAthlet)
								WHERE
									sfat.xStaffelstart = $row_res[11]
								AND	sfat.xRunde = $row[0]
								ORDER BY
									sfat.Position
								LIMIT $row[10]
						");
						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}else{
							$text_at = "";
							while($row_at = mysql_fetch_array($res_at)){
								$text_at .= $row_at[1]." ".$row_at[0]." ".AA_formatYearOfBirth($row_at[2])." / ";
							}
							$text_at = substr($text_at, 0, (strlen($text_at)-2));
							
							$list->printAthletesLine("( ".$text_at.")");
						}
					}
					
					// 
					// if biglist, show all attempts
					//
					if($biglist){
						
						if(($row[3] == $cfgDisciplineType[$strDiscTypeJump])
							|| ($row[3] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
							|| ($row[3] == $cfgDisciplineType[$strDiscTypeThrow])
							|| ($row[3] == $cfgDisciplineType[$strDiscTypeHigh]))
						{
						
						$query_sort = ($row[3]==$cfgDisciplineType[$strDiscTypeHigh]) ? "ORDER BY Leistung ASC": "";
							
						$res_att = mysql_query("
								SELECT * FROM 
									resultat 
								WHERE xSerienstart = $row_res[0]
								".$query_sort."
								");
						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}else{
							$text_att = "";
							while($row_att = mysql_fetch_array($res_att)){
								if($row_att['Leistung'] < 0){
									foreach($cfgInvalidResult as $value)	// translate value
									{
										if($value['code'] == $row_att['Leistung']) {
											$text_att .= $value['short'];
										}
									}
									$text_att .= " / ";
								}else{
									$text_att .= ($row_att['Leistung']=='-') ? '-' : AA_formatResultMeter($row_att['Leistung']);
									if($row_att['Info'] != "-" && !empty($row_att['Info']) && $row[3] != $cfgDisciplineType[$strDiscTypeThrow]){
										$text_att .= " , ".$row_att['Info'];
									}
									$text_att .= " / ";
								}
							}
							$text_att = substr($text_att, 0, (strlen($text_att)-2));
							
							$list->printAthletesLine("$strAttempts: ( $text_att )");
						}
						
						}
					}
				}		// ET athlete processed

				$id = $row_res[0];				// keep current athletes ID
			}	// END WHILE result lines
			mysql_free_result($res);
			$list->endList();
		}	// ET DB error result rows

		$cat = $row[1];	// keep category
	}	// END WHILE event rounds
	mysql_free_result($results);

	$list->endPage();	// end HTML page for printing
} // ET DB error event rounds


}	// end function AA_rankinglist_Single

}	// AA_RANKINGLIST_SINGLE_LIB_INCLUDED
?>
