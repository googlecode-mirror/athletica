<?php

/**********
 *
 *	rankinglist team sm events
 *	
 */

if (!defined('AA_RANKINGLIST_TEAMSM_LIB_INCLUDED'))
{
	define('AA_RANKINGLIST_TEAMSM_LIB_INCLUDED', 1);


function AA_rankinglist_TeamSM($category, $event, $formaction, $break, $cover, $cover_timing=false, $date = '%')
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
	
	global $rFrom, $rTo, $limitRank, $date;
	$rFrom = 0; $rTo = 0; // limits rank if limitRank set to true
	$limitRank = false;
	if($_GET['limitRank'] == "yes" && substr($formaction,0,6) == "export"){ // check if ranks are limited
		if(!empty($_GET['limitRankFrom']) && !empty($_GET['limitRankTo'])){
			$limitRank = true;
			$rFrom = $_GET['limitRankFrom'];
			$rTo = $_GET['limitRankTo'];
		}
	}
	
	// start a new HTML display page
	if($formaction == 'view') {
		$list = new GUI_TeamSMRankingList($_COOKIE['meeting']);
		$list->printPageTitle("$strRankingLists " . $_COOKIE['meeting']);
	}
	// start a new HTML print page
	elseif($formaction == "print") {
		$list = new PRINT_TeamSMRankingList($_COOKIE['meeting']);
		if($cover == true) {		// print cover page 
			$list->printCover($GLOBALS['strResults']);
		}
	}
	// export ranking
	elseif($formaction == "exportpress"){
		$list = new EXPORT_TeamSMRankingListPress($_COOKIE['meeting'], 'txt');
	}elseif($formaction == "exportdiplom"){
		$list = new EXPORT_TeamSMRankingListDiplom($_COOKIE['meeting'], 'csv');
	}
	
	$selection = '';
	if(!empty($event)) {		// show specific event
		$selection = " w.xWettkampf = $event";
	}
	elseif(!empty($category)) {	// show disciplines per specific category
		$selection = " w.xMeeting = ".$_COOKIE['meeting_id']." AND w.xKategorie = $category";
	}
	else{				// show events over all categories
		$selection = " w.xMeeting = ".$_COOKIE['meeting_id']." ";
	}
	
	//
	// get each discipline for selection and process
	//
	$result = mysql_query("
		SELECT
			w.xWettkampf
			, d.Typ
			, k.Name
			, d.Name
			, w.Windmessung
		FROM
			wettkampf AS w
			, kategorie AS k
			, disziplin AS d
		WHERE
			$selection
		AND	k.xKategorie = w.xKategorie
		AND	d.xDisziplin = w.xDisziplin
		AND	w.Typ = " . $cfgEventType[$strEventTypeTeamSM] ."
		ORDER BY
			k.Anzeige
			, d.Anzeige
	");
	
	if(mysql_errno() > 0){
		AA_printErrorMsg(mysql_errno().": ".mysql_error());
	}else{
		
		$cat = "";
		
		while($row = mysql_fetch_array($result)){
			
			if($cat != $row[2] && !empty($cat)){
				
			}
			
			processDiscipline($row[0], $row[1], $row[2], $row[3], $row[4], $list);
		}
		
	}
	
	$list->endPage();
	
} // EF function


function processDiscipline($event, $disctype, $catname, $discname, $windmeas, $list){
	
	global $rFrom, $rTo, $limitRank, $date;
	require('config.inc.php');
	
	$teams = array();	// team array
	$countRes = 3;		// results per team counting
	
	$order_perf = "";
	$valid_result = "";
	if(($disctype == $cfgDisciplineType[$strDiscTypeJumpNoWind])
		|| ($disctype == $cfgDisciplineType[$strDiscTypeThrow]))
	{
		$order_perf = "DESC";
	}
	else if($disctype == $cfgDisciplineType[$strDiscTypeJump])
	{
		if ($windmeas == 1) {			// with wind
			$order_perf = "DESC, r.Info ASC";
		}
		else {					// without wind
			$order_perf = "DESC";
		}
	}
	else if($disctype == $cfgDisciplineType[$strDiscTypeHigh])
	{
		$order_perf = "DESC";
		$valid_result =	" AND (r.Info LIKE '%O%' OR r.Leistung < 0)";
	}
	else
	{
		$order_perf = "ASC";
	}
	
	$sql_leistung = ($order_perf=='ASC') ? "r.Leistung" : "IF(r.Leistung<0, (If(r.Leistung = -99, -9, r.Leistung) * -1), r.Leistung)";
	$res = mysql_query("
		SELECT
			ts.xTeamsm
			, ts.Name
			, v.Name
			, at.Name
			, at.Vorname
			, a.Startnummer
			, ".$sql_leistung." AS leistung_neu
			, at.xAthlet
		FROM
			teamsm AS ts
			, verein AS v
			, teamsmathlet AS tsa
			, anmeldung AS a
			, athlet AS at
			, start AS st
			, serienstart AS ss
			, resultat AS r 
			, serie AS se
			, runde as ru
		WHERE
			ts.xWettkampf = $event
		AND	tsa.xTeamsm = ts.xTeamsm
		AND	v.xVerein = ts.xVerein
		AND	a.xAnmeldung = tsa.xAnmeldung
		AND	at.xAthlet = a.xAthlet
		AND	st.xAnmeldung = tsa.xAnmeldung
		AND	st.xWettkampf = $event
		AND	ss.xStart = st.xStart
		AND ss.xSerie = se.xSerie
		AND se.xRunde = ru.xRunde
		AND ru.Datum LIKE '".$date."'
		
		AND	r.xSerienstart = ss.xSerienstart
		$valid_result
		
		ORDER BY
			ts.xTeamsm
			, leistung_neu $order_perf
	");
	
	if(mysql_errno() > 0){
		AA_printErrorMsg(mysql_errno().": ".mysql_error());
	}else{
		
		$team = 0;		// current team
		$c = 0;			// count results
		$athletes = array();
		
		while($row = mysql_fetch_array($res)){
			
			$row_res[6] = ($row_res[6]==1 || $row_res[6]==2 || $row_res[6]==3 || $row_res[6]==4) ? ($row_res[6] * -1) : (($row_res[6]==9) ? -99 : $row_res[6]);
			
			if(isset($athletes[$row[7]])){
				continue;
			}else{
				$athletes[$row[7]] = 1;
			}
			
			if($team != $row[0]){
				
				if($team > 0){
					
					$teams[$team]['perf'] /= $countRes;
					
				}
				
				$team = $row[0];
				$teams[$team]['club'] = $row[2];
				$teams[$team]['name'] = $row[1];
				$c = 0;
			}
			
			$perf = 0;
			$perf_print = 0;
			if(($disctype == $cfgDisciplineType[$strDiscTypeJump])
				|| ($disctype == $cfgDisciplineType[$strDiscTypeJumpNoWind])
				|| ($disctype == $cfgDisciplineType[$strDiscTypeThrow])
				|| ($disctype == $cfgDisciplineType[$strDiscTypeHigh])) {
				$perf = $row[6];
				$perf_print = AA_formatResultMeter($row[6]);
			}
			else {
				$perf = (ceil($row[6]/10))*10; // round up 1000
				if(($disctype == $cfgDisciplineType[$strDiscTypeTrack])
				|| ($disctype == $cfgDisciplineType[$strDiscTypeTrackNoWind])){
					$perf_print = AA_formatResultTime($row[6], true, true);
				}else{
					$perf_print = AA_formatResultTime($row[6], true);
				}
			}
			
			if($c < $countRes){
				
				$teams[$team]['perf'] += $perf;
				$teams[$team]['athletes'][] = "$row[3] $row[4], $perf_print";
				
			}else{
				
				$teams[$team]['athletes'][] = "[$row[3] $row[4], $perf_print]";
				
			}
			
			$c++;
		}
		
		if($team > 0){ // calc last team
			
			$teams[$team]['perf'] /= $countRes;
			
		}
		
		
		//
		// print team ranking
		//
		if(count($teams)>0){
			
			$list->printSubTitle($catname, $discname, "");
			$list->startList();
			$list->printHeaderLine();
			
			usort($teams, "cmp_$order_perf");	// sort by performance
			$rank = 1;			// initialize rank
			$r = 0;				// start value for ranking
			$p = 0;
			
			foreach($teams as $team){
				
				$r++;
				
				if($limitRank && ($r < $rFrom || $r > $rTo)){ // limit ranks if set (export)
					continue;
				}
				
				if($p != $team['perf']) {	// not the same performance
					$rank = $r;		// next rank
				}
				else {
					$rank = '';
				}
				
				$perf = 0;
				if(($disctype == $cfgDisciplineType[$strDiscTypeJump])
					|| ($disctype == $cfgDisciplineType[$strDiscTypeJumpNoWind])
					|| ($disctype == $cfgDisciplineType[$strDiscTypeThrow])
					|| ($disctype == $cfgDisciplineType[$strDiscTypeHigh])) {
					$perf = AA_formatResultMeter($team['perf']);
				}
				else {
					if(($disctype == $cfgDisciplineType[$strDiscTypeTrack])
					|| ($disctype == $cfgDisciplineType[$strDiscTypeTrackNoWind])){
						$perf = AA_formatResultTime($team['perf'], true, true);
					}else{
						$perf = AA_formatResultTime($team['perf'], true);
					}
				}
				
				$list->printLine($rank, $team['name'], $team['club'], $perf);
				
				// print each athlete with result for team
				$tmp = "";
				foreach($team['athletes'] as $athlete){
					
					//$list->printInfo($athlete);
					$tmp .= $athlete." / ";
					
				}
				$list->printInfo(substr($tmp,0, -2));
				
				$p = $team['perf'];	// keep current performance
				
			}
			$list->endList();
			
		}
	}
	
}


//
// compare function to sort teams
// 
function cmp_DESC ($a, $b) {
	if ($a["perf"]== $b["perf"]) return 0;
	return ($a["perf"] > $b["perf"]) ? -1 : 1;
}

function cmp_ASC ($a, $b) {
	if ($a["perf"]== $b["perf"]) return 0;
	return ($a["perf"] > $b["perf"]) ? 1 : -1;
}


} // EF defined



?>
