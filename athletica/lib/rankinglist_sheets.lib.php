<?php

/**********
 *
 *	rankinglist team sheets
 *	
 */

if (!defined('AA_RANKINGLIST_SHEET_LIB_INCLUDED'))
{
	define('AA_RANKINGLIST_SHEET_LIB_INCLUDED', 1);


function AA_rankinglist_Sheets($category, $event, $formaction, $cover, $cover_timing=false, $heatSeparate)
{  
require('./lib/cl_gui_page.lib.php');
require('./lib/cl_print_page.lib.php');

require('./lib/common.lib.php');
require('./lib/results.lib.php');

if(AA_connectToDB() == FALSE)	{ // invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

// start a new HTML display page
if($formaction == 'view') { 
	$list = new GUI_TeamSheet($_COOKIE['meeting']);
	$list->printPageTitle("$strClubSheets " . $_COOKIE['meeting']);
}
// start a new HTML print page
else {
	$list = new PRINT_TeamSheet($_COOKIE['meeting']);
	if($cover == true) {		// print cover page 
		$list->printCover($strClubSheets, $cover_timing);
	}
}
$selection = ''; 
if ($event!='')
    $mergedCat=AA_mergedCatEvent($category, $event);  
else
    $mergedCat=AA_mergedCat($category);   

if(!empty($category)) {		// show every category  
    if ($mergedCat=='') {
	    $selection = " AND k.xKategorie = $category";
    }
    else  {
        if ($heatSeparate){  
            $selection = " AND k.xKategorie = $category"; 
        }
        else {  
            $selection = " AND k.xKategorie IN $mergedCat"; 
        }
    }
}

// evaluation per category      

mysql_query("DROP TABLE IF EXISTS tempresult");
if(mysql_errno() > 0) {		// DB error
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}


$results = mysql_query("
	SELECT
	  	k.xKategorie
	  	, k.Name
		, w.Typ
  	FROM
	  	kategorie AS k
	  	, wettkampf AS w
  	WHERE w.xMeeting = " . $_COOKIE['meeting_id'] ."
  	AND k.xKategorie = w.xKategorie
	" . $selection . "    
    " // AND w.Typ >=  " . $cfgEventType[$strEventTypeClubMA] ."        // old svm
  	." AND w.Typ >=  " . $cfgEventType[$strEventTypeClubBasic] ."  
    GROUP BY
		k.xKategorie
	ORDER BY
		k.Anzeige
");  
 
if(mysql_errno() > 0) {		// DB error
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else
{
	$GLOBALS['AA_TC'] = 0;		// team counter

	// process all categories
	while($row = mysql_fetch_row($results))
	{
		// Team sheet: Combined 
		if ($row[2] == $cfgEventType[$strEventTypeClubCombined])
		{
			AA_sheets_processCombined($row[0], $row[1], $list);
		}
		// Team sheet: Single
		else
		{  
			AA_sheets_processSingle($row[0], $row[1], $list);
		}

	}

	mysql_free_result($results);
}	// ET DB error categories 

$list->endPage();	// end HTML page for printing

}	// end function AA_rankinglist_Team

//
//	process club single events
//

function AA_sheets_processSingle($xCategory, $category, $list)
{  
	require('./config.inc.php');

	mysql_query("
		LOCK TABLES
	  		anmeldung AS a 
  			, athlet READ
  			, disziplin READ
  			, resultat READ
  			, serie READ
  			, serienstart READ
  			, staffel READ
  			, start READ
			, team READ
			, verein READ
  			, wettkampf READ
  			, tempresult WRITE
			, kategorie READ
	");

	// get all teams
	$results = mysql_query("
		SELECT
			t.Name
			, t.xTeam
			, v.Name
		FROM
			team AS t
			, verein AS v
		WHERE t.xMeeting = " . $_COOKIE['meeting_id'] ."
		AND t.xKategorie = $xCategory
		AND v.xVerein = t.xVerein
	");   
    
	if(mysql_errno() > 0) {		// DB error
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		// keep list of all teams for this category
		while($row = mysql_fetch_row($results))
		{
			$teamList[] = array(
				"name"=>$row[0]
				, "xTeam"=>$row[1]
				, "club"=>$row[2]
			);
		}
		mysql_free_result($results);

		// process every team
		foreach($teamList as $team)
		{
			// page break after each team
			if(is_a($list, "PRINT_TeamSheet")	// page for printing
				&& ($GLOBALS['AA_TC'] > 0)) {		// not first result row
				$list->insertPageBreak();
			}
			$GLOBALS['AA_TC']++;		// team counter
			$total = 0;
			$temptable = false;

			if(is_a($list, "PRINT_TeamSheet")) {	// page for printing
				// set up list of other competitors
				$sep = '';
				$competitors = '';
				foreach($teamList as $comp)
				{
					if($comp['xTeam'] != $team['xTeam'])	// not current team
					{
						$competitors = $competitors . $sep . $comp['club'];	// club
						$sep = ', ';
					}
				}

				$list->printHeader($team['club']." (".$team['name'].")", $category, $competitors);
			}
			else {
				$list->printHeader($team['club']." (".$team['name'].")", $category);
			}

			// single events
			// -------------
			$results = mysql_query("
				SELECT
					d.Name
					, d.Typ
					, at.Name
					, at.Vorname
					, at.Jahrgang
					, MAX(r.Leistung)
					, r.Info
					, MAX(r.Punkte) AS pts
					, w.Typ
					, s.Wind
					, w.Windmessung
					, k.Code
					, at.Geschlecht
                    , ss.Bemerkung
				FROM
					anmeldung AS a
					, athlet AS at 
					, disziplin AS d 
					, resultat AS r 
					, serie AS s 
					, serienstart AS ss 
					, start AS st 
					, wettkampf AS w
					, kategorie AS k
  				WHERE w.xMeeting = " . $_COOKIE['meeting_id'] ."
				AND w.xKategorie = $xCategory
				AND d.xDisziplin = w.xDisziplin
				AND st.xWettkampf = w.xWettkampf
				AND ss.xStart = st.xStart
				AND r.xSerienstart = ss.xSerienstart
				AND s.xSerie = ss.xSerie
				AND a.xAnmeldung = st.xAnmeldung
				AND a.xTeam = " . $team['xTeam'] . "
				AND at.xAthlet = a.xAthlet
				AND r.Info != '$cfgResultsHighOut'
				AND a.xKategorie = k.xKategorie
				GROUP BY
					st.xStart
				ORDER BY
					d.Anzeige
					, pts DESC
			");   
           
			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else
			{
				$a = 0;
				$c = 0;
				$d = '';
				$r = 0;
				$mixedTeamCount = array('m'=>0,'w'=>0);
				
				while($pt_row = mysql_fetch_row($results))
				{
					if($pt_row[0] != $d) // new discipline
					{
						for(;$c < $a; $c++)
						{
							$points = '';
							if($c + 1 == $a)	// last line
							{
								if($r == $cfgEventType[$strEventTypeClubAdvanced]) {
									//$points = $p / 2;
									$points = $p;
								}elseif($r == $cfgEventType[$strEventTypeSVMNL]) {
									//$points = $p / 2;
									$points = $p;
								}/*elseif($r == $cfgEventType[$strEventTypeClubMixedTeam]){
									$points = $p / 6;
								}*/else {
									$points = $p;
								}

								$total = $total + $points;	// accumulate total points
								$points = round($points,$cfgResultsPointsPrecision);
							}                             
							$list->printLine('', $cfgResultsInfoFill, $cfgResultsInfoFill, '', '0', $points);	// empty line
						}

						// nbr of athletes to be included in total points
						switch($pt_row[8]) {
							case $cfgEventType[$strEventTypeSVMNL]: // new national league mode since 2007
												// simply 2 athletes per disc and 1 relay
								$a = 2;
								break;
							case $cfgEventType[$strEventTypeClubBasic]:
								$a = 1;
								break;
							case $cfgEventType[$strEventTypeClubAdvanced]:
								$a = 2;
								break;
							case $cfgEventType[$strEventTypeClubTeam]:
								$a = 5;
								break;
							case $cfgEventType[$strEventTypeClubMixedTeam]:
								$a = 6;
								break;
							case $cfgEventType[$strEventTypeClubMA]: // old NL modes, updated in 2006
							case $cfgEventType[$strEventTypeClubMB]: // kept for compatibility reasons
							case $cfgEventType[$strEventTypeClubMC]: // and to demonstrate respect for the guys who coded this ;)
							case $cfgEventType[$strEventTypeClubFA]:
							case $cfgEventType[$strEventTypeClubFB]:
								$a = 1;
								if($c == 0) {	// first time here: initialize temp. table
									mysql_query("
										CREATE TABLE tempresult(
											Disziplinengruppe tinyint(4)
											, Disziplin varchar(30)
											, Name varchar(25)
											, Vorname varchar(25)
											, `Jahrgang` year(4)
											, Leistung int(9)
											, Info char(5)
											, Punkte smallint(6)
											, Wettkampftyp tinyint(4)
											, Wind char(5)
											, Windmessung tinyint(4)
											, Disizplinentyp tinyint(4)
											, xStaffel int(11)
											)
										TYPE=HEAP
									");

									if(mysql_errno() > 0) {		// DB error
										AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
									}
									else {
										$temptable = true;
									}

									$list->printSubHeader($strTeamRankingSubtitle1);
								}
								break;

							default:
								$a = 1;
						}

						$c = 0;					// athlete counter
						$p = 0;					// point counter
						$mixedTeamCount = array('m'=>0,'w'=>0);	// mixedteam counter
					}

					if($c < $a)		// show only top ranking athletes
					{
						// special mixed-team schüler svm
						// get 3 men results and 3 women results
						if($pt_row[8] == $cfgEventType[$strEventTypeClubMixedTeam]){
							
							if(empty($pt_row[12])){
								if(substr($pt_row[11],0,1) == 'M' || substr($pt_row[11],3,1) == 'M'){
									$mixedTeamCount['m']++;
									if($mixedTeamCount['m'] > 3){
										continue;
									}
								}else{
									$mixedTeamCount['w']++;
									if($mixedTeamCount['w'] > 3){
										continue;
									}
								}
							}else{
								if($pt_row[12] == "m"){
									$mixedTeamCount['m']++;
									if($mixedTeamCount['m'] > 3){
										continue;
									}
								}else{
									$mixedTeamCount['w']++;
									if($mixedTeamCount['w'] > 3){
										continue;
									}
								}
							}
							
						}
						
						$windsep='';
						if(is_a($list, "PRINT_TeamSheet")) {	// page for printing
							$windsep="/ ";
						}

						// set wind, if required
						if($pt_row[10] == 1)
						{
							if($pt_row[1] == $cfgDisciplineType[$strDiscTypeTrack]) {
								$wind = $windsep . $pt_row[9];
							}
							else if($pt_row[1] == $cfgDisciplineType[$strDiscTypeJump]) {
								$wind = $windsep . $pt_row[6];
							}
						}
						else {
							$wind = '';
						}

						// format output
						if(($pt_row[1] == $cfgDisciplineType[$strDiscTypeJump])
							|| ($pt_row[1] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
							|| ($pt_row[1] == $cfgDisciplineType[$strDiscTypeThrow])
							|| ($pt_row[1] == $cfgDisciplineType[$strDiscTypeHigh])) {
							$perf = AA_formatResultMeter($pt_row[5], true);
						}
						else {
							$perf = AA_formatResultTime($pt_row[5], true);
						}
						$year = AA_formatYearOfBirth($pt_row[4]);

						// calculate points
						if($pt_row[8] == $cfgEventType[$strEventTypeClubMixedTeam]){
							$p = $p + $pt_row[7] / 6;
							$p = round($p,$cfgResultsPointsPrecision);
						}elseif($pt_row[8] == $cfgEventType[$strEventTypeClubTeam]){
							$p = $p + $pt_row[7] / 5;
							$p = round($p,$cfgResultsPointsPrecision);
						}else{
							$p = $p + $pt_row[7];	// accumulate points per discipline
						}

						if(($c + 1) == $a) {	// last athlete
							if($pt_row[8] == $cfgEventType[$strEventTypeClubAdvanced]) {
								//$p = $p / 2;
								$p = $p;
							}elseif($pt_row[8] == $cfgEventType[$strEventTypeSVMNL]) {
								//$p = $p / 2;
								$p = $p;
							}/*elseif($pt_row[8] == $cfgEventType[$strEventTypeClubMixedTeam]){
								$p = $p / 6;
							}*/

							$points = round($p,$cfgResultsPointsPrecision);
						}
						else {					// not last athlete
							$points = '';
						}

						$total = $total + $points;	// accumulate total points

						// print athlete line
						$ip = '';
						if($pt_row[8] == $cfgEventType[$strEventTypeClubAdvanced]
							|| $pt_row[8] == $cfgEventType[$strEventTypeClubMixedTeam]
							|| $pt_row[8] == $cfgEventType[$strEventTypeClubTeam]
							|| $pt_row[8] == $cfgEventType[$strEventTypeSVMNL])
						{
							$ip = $pt_row[7];
						}

						if($pt_row[0] != $d)		// new discipline
						{
							$list->printLine($pt_row[0],
								$pt_row[2] . " " . $pt_row[3] .", " . $year,
								$perf, $wind, $ip, $points, $pt_row[13]);
						}
						else {
							$list->printLine('',
								$pt_row[2] . " " . $pt_row[3] . ", " . $year,
								$perf, $wind, $ip, $points, $pt_row[13]);
						}
					}
					else if ($temptable == true)	// temp table created
					{
						switch($pt_row[1]) {
							// group 1 = run
							case $cfgDisciplineType[$strDiscTypeTrack]:
							case $cfgDisciplineType[$strDiscTypeTrackNoWind]:
							case $cfgDisciplineType[$strDiscTypeDistance]:
							case $cfgDisciplineType[$strDiscTypeRelay]:
								$g = 1;
								break;
							// group 2 = jump
							case $cfgDisciplineType[$strDiscTypeJump]:
							case $cfgDisciplineType[$strDiscTypeJumpNoWind]:
							case $cfgDisciplineType[$strDiscTypeHigh]:
								$g = 2;
								break;
							// group 3 = throw
							case $cfgDisciplineType[$strDiscTypeThrow]:
								$g = 3;
								break;
							default:
								$g = 4;
						}

						// add result to list for further processing
						mysql_query("
							INSERT INTO tempresult
							VALUES(
								$g
								, '$pt_row[0]'
								, '$pt_row[2]'
								, '$pt_row[3]'
								, $pt_row[4]
								, $pt_row[5]
								, '$pt_row[6]'
								, $pt_row[7]
								, $pt_row[8]
								, '$pt_row[9]'
								, $pt_row[10]
								, $pt_row[1]
								, 0)
						");

						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}

					}

					$c++;
					$d = $pt_row[0];	// keep discipline
					$r = $pt_row[8];	// keep rating type                      
				}	// END WHILE team events

				// print remaining empty lines for last disciplines (if any)
				for(;$c < $a; $c++)
				{
					$points = '';
					if($c + 1 == $a)	// last line
					{
						if($r == $cfgEventType[$strEventTypeClubAdvanced]) {
							$points = $p / 2;
						}elseif($r == $cfgEventType[$strEventTypeSVMNL]) {
							//$points = $p / 2;
							$points = $p;
						}/*elseif($r == $cfgEventType[$strEventTypeClubMixedTeam]){
							$points = $p / 6;
						}*/
						else {
							$points = $p;
						}
						$total = $total + $points;	// accumulate total points
					}
					$list->printLine('', $cfgResultsInfoFill, $cfgResultsInfoFill, '', '0', round($points,$cfgResultsPointsPrecision));	// empty line
				}

				mysql_free_result($results);
			}

			// relay events
			// -------------
			$results = mysql_query("
				SELECT
					d.Name
					, st.Name
					, r.Leistung
					, MAX(r.Punkte) AS pts
					, st.xStaffel
					, w.Typ
					, d.Typ
                    , ss.Bemerkung
				FROM
					disziplin AS d 
					, resultat AS r 
					, serienstart AS ss 
					, staffel AS st 
					, start AS s 
					, wettkampf AS w
  				WHERE w.xMeeting = " . $_COOKIE['meeting_id'] ."
				AND w.xKategorie = $xCategory
				AND d.xDisziplin = w.xDisziplin
				AND s.xWettkampf = w.xWettkampf
				AND ss.xStart = s.xStart
				AND r.xSerienstart = ss.xSerienstart
				AND st.xStaffel = s.xStaffel
				AND st.xTeam = " . $team['xTeam'] . "
				GROUP BY
					s.xStart
				ORDER BY
					d.Anzeige
					, pts DESC
			");
           
			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else
			{
				$a = 1;
				$c = 0;
				$d = '';
				$r = 0;
				$p = 0;

				while($pt_row = mysql_fetch_row($results))
				{
					if($pt_row[0] != $d) 	// new discipline
					{
						// accumulate points of previous relay event
						if($p > 0) {
							$points = $p;
							$total = $total + $points;	// accumulate total points
							$list->printLine('', $cfgResultsInfoFill, $cfgResultsInfoFill, '', '0', round($points,$cfgResultsPointsPrecision)); // empty line
						}

						$c = 0;					// athlete counter
						$p = 0;					// point counter
					}

					if($c < $a)		// show only top ranking relays
					{
						$perf = AA_formatResultTime($pt_row[2], true, true);

						// calculate points
						$p = $p + $pt_row[3];			// accumulate points per discipline
						if(($c + 1) == $a) {	// last athlete
							$points = $p;
						}
						else {					// not last athlete
							$points = '';
						}

						$total = $total + $points;	// accumulate total points

						// print line
						$ip = '';
						if($pt_row[8] == $cfgEventType[$strEventTypeClubAdvanced])
						{
							$ip = $pt_row[3];	// set individual points
						}

						if($pt_row[0] != $d)		// new discipline
						{
							$list->printLine($pt_row[0], $pt_row[1], $perf, '',
								$ip, round($points,$cfgResultsPointsPrecision), $pt_row[7]);

						}
						else {
							$list->printLine('', $pt_row[1], $perf, '', $ip, round($points,$cfgResultsPointsPrecision), $pt_row[7]);
						}

						AA_sheets_printRelayAthletes($list, $pt_row[4]);
					}	// ET top ranking relays
					else if ($temptable == true)
					{
						// add result to list for further processing (group 1 = run)
						mysql_query("
							INSERT INTO tempresult
							VALUES(
								1
								, '$pt_row[0]'
								, '$pt_row[1]'
								, '' 
								, 0
								, $pt_row[2]
								, ''
								, $pt_row[3]
								, $pt_row[5]
								, ''
								, 0
								, $pt_row[6]
								, $pt_row[4])
						");

						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
					}



					$c++;
					$d = $pt_row[0];	// keep discipline
					$r = $pt_row[5];	// keep rating type
				}	// END WHILE team events

				$list->printSubTotal(round($total,$cfgResultsPointsPrecision));

				mysql_free_result($results);

				// evaluate remaining results per discipline group
				//	(Event Type: ClubMA to ClubFB)
				if($temptable == true)
				{
					// get next results per discipline group
					$res = mysql_query("
						SELECT
							Disziplinengruppe
							, Disziplin
							, Name
							, Vorname
							, Jahrgang
							, Leistung
							, Info
							, Punkte
							, Wettkampftyp
							, Wind
							, Windmessung
							, Disizplinentyp
							, xStaffel
						FROM
							tempresult
						ORDER BY
							Disziplinengruppe ASC
							, Punkte DESC
					");

					if(mysql_errno() > 0) {		// DB error
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
					else {

						$list->printSubHeader($strTeamRankingSubtitle2);

						$c = 0;					// athlete counter
						$g = 0;					// group indicator
						$p = 0;					// point counter

						while($pt_row = mysql_fetch_row($res))
						{
							// nbr of athletes per disc. group to be included in total points
							switch($pt_row[8]) {
								case $cfgEventType[$strEventTypeClubMA]:
									$a = 3;
									break;
								case $cfgEventType[$strEventTypeClubMB]:
									$a = 2;
									break;
								case $cfgEventType[$strEventTypeClubMC]:
									$a = 1;
									break;
								case $cfgEventType[$strEventTypeClubFA]:
									$a = 2;
									break;
								case $cfgEventType[$strEventTypeClubFB]:
									$a = 1;
									break;
								default:
									$a = 0;
							}
					
							if($pt_row[0] != $g) 	// new discipline	group
							{
								// accumulate points of previous event
								if($p > 0) {
									$points = $points + $p;		// accumulate points
								}

								$c = 0;					// athlete counter
								$p = 0;					// point counter
							}

							if($c < $a)		// show only top ranking athletes
							{
								$windsep='';
								if(is_a($list, "PRINT_TeamSheet")) {	// page for printing
									$windsep="/ ";
								}

								// set wind, if required
								if($pt_row[10] == 1)
								{
									if($pt_row[11] == $cfgDisciplineType[$strDiscTypeTrack]) {
										$wind = $windsep . $pt_row[9];
									}
									else if($pt_row[11] == $cfgDisciplineType[$strDiscTypeJump]) {
										$wind = $windsep . $pt_row[6];
									}
								}
								else {
									$wind = '';
								}

								// format output
								if(($pt_row[11] == $cfgDisciplineType[$strDiscTypeJump])
									|| ($pt_row[11] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
									|| ($pt_row[11] == $cfgDisciplineType[$strDiscTypeThrow])
									|| ($pt_row[11] == $cfgDisciplineType[$strDiscTypeHigh])) {
									$perf = AA_formatResultMeter($pt_row[5], true);
								}
								else {
									$perf = AA_formatResultTime($pt_row[5], true);
								}

								$year = '';
								if($pt_row[11] != $cfgDisciplineType[$strDiscTypeRelay])
								{
									$year = ", " . AA_formatYearOfBirth($pt_row[4]);
								}
		
								$p = $pt_row[7];
								$points = round($p,$cfgResultsPointPrecision);
								$total = $total + $points;		// accumulate points

								$list->printLine($pt_row[1],
									$pt_row[2] . " " . $pt_row[3] . $year,
									$perf, $wind, "", $points);

								if ($pt_row[11] == $cfgDisciplineType[$strDiscTypeRelay])
								{
									AA_sheets_printRelayAthletes($list, $pt_row[12]);
								}

							}
							else if ($pt_row[11] != $cfgDisciplineType[$strDiscTypeRelay])
							{
								// add result to list for further processing (group 1 = run)
								mysql_query("
									INSERT INTO tempresult
									VALUES(
										0
										, '$pt_row[1]'
										, '$pt_row[2]'
										, '$pt_row[3]'
										, $pt_row[4]
										, $pt_row[5]
										, '$pt_row[6]'
										, $pt_row[7]
										, $pt_row[8]
										, '$pt_row[9]'
										, $pt_row[10]
										, $pt_row[11]
										, 0)
								");
							}

							if(mysql_errno() > 0) {		// DB error
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}


							$c++;
							$g = $pt_row[0];
						}	// END WHILE next results

						$list->printSubTotal(round($total,$cfgResultsPointsPrecision));
						mysql_free_result($res);
					}

					// get remaining results
					$res = mysql_query("
						SELECT
							Disziplinengruppe
							, Disziplin
							, Name
							, Vorname
							, Jahrgang
							, Leistung
							, Info
							, Punkte
							, Wettkampftyp
							, Wind
							, Windmessung
							, Disizplinentyp
						FROM
							tempresult
						WHERE Disziplinengruppe = 0
						ORDER BY
							Punkte DESC
					");

					if(mysql_errno() > 0) {		// DB error
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
					else {

						$list->printSubHeader($strTeamRankingSubtitle3);

						$c = 0;					// athlete counter
						$p = 0;					// point counter

						while($pt_row = mysql_fetch_row($res))
						{
							// nbr of remaining athletes to be included in total points
							switch($pt_row[8]) {
								case $cfgEventType[$strEventTypeClubMA]:
									$a = 6;
									break;
								case $cfgEventType[$strEventTypeClubMB]:
									$a = 3;
									break;
								case $cfgEventType[$strEventTypeClubMC]:
									$a = 2;
									break;
								case $cfgEventType[$strEventTypeClubFA]:
									$a = 4;
									break;
								case $cfgEventType[$strEventTypeClubFB]:
									$a = 4;
									break;
								default:
									$a = 0;
							}
					
							if($c < $a)		// add only top ranking athletes
							{
								$windsep='';
								if(is_a($list, "PRINT_TeamSheet")) {	// page for printing
									$windsep="/ ";
								}

								// set wind, if required
								if($pt_row[10] == 1)
								{
									if($pt_row[11] == $cfgDisciplineType[$strDiscTypeTrack]) {
										$wind = $windsep . $pt_row[9];
									}
									else if($pt_row[11] == $cfgDisciplineType[$strDiscTypeJump]) {
										$wind = $windsep . $pt_row[6];
									}
								}
								else {
									$wind = '';
								}

								// format output
								if(($pt_row[11] == $cfgDisciplineType[$strDiscTypeJump])
									|| ($pt_row[11] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
									|| ($pt_row[11] == $cfgDisciplineType[$strDiscTypeThrow])
									|| ($pt_row[11] == $cfgDisciplineType[$strDiscTypeHigh])) {
									$perf = AA_formatResultMeter($pt_row[5], true);
								}
								else {
									$perf = AA_formatResultTime($pt_row[5], true);
								}
								$year = AA_formatYearOfBirth($pt_row[4]);

								$p = $pt_row[7];
								$points = round($p,$cfgResultsPointPrecision);
								$total = $total + $points;		// accumulate points

								$list->printLine($pt_row[1],
									$pt_row[2] . " " . $pt_row[3] .", " . $year,
									$perf, $wind, "", $points);
							}

							$c++;
						}	// END WHILE remaining results

						mysql_free_result($res);
					}

				}

				$list->printTotal(round($total,$cfgResultsPointsPrecision));
			}

			if(is_a($list, "PRINT_TeamSheet")) {	// page for printing
				$list->printFooter();
			}

			mysql_query("DROP TABLE IF EXISTS tempresult");
			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			$temptable = false;	// reset temp table indicator

		}	// END FOREACH every team
	}	// ET DB error all teams

	mysql_query("UNLOCK_TABLES");

}	// end function processSingle()


//
//	process club combined events
//

function AA_sheets_processCombined($xCategory, $category, $list)
{
	require('./config.inc.php');

	// get athlete info per category and team
	$results = mysql_query("
		SELECT
			DISTINCT(a.xAnmeldung)
			, at.Name
			, at.Vorname
			, at.Jahrgang
			, t.xTeam
			, t.Name
			, v.Name
		FROM
			anmeldung AS a
			, athlet AS at
			, team AS t
			, verein AS v
			, start as st
			, wettkampf as w
		WHERE a.xMeeting = " . $_COOKIE['meeting_id'] ."
		AND at.xAthlet = a.xAthlet
		AND t.xTeam = a.xTeam
		AND v.xVerein = t.xVerein
		AND st.xAnmeldung = a.xAnmeldung
		AND w.xWettkampf = st.xWettkampf
		AND w.xKategorie = $xCategory
		ORDER BY
			t.xTeam
	");

	if(mysql_errno() > 0) {		// DB error
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		$evaluation = 5;		// nbr of athletes included in total result
		$a = 0;
		$club = '';
		$info = '';
		$name = '';
		$points = 0;
		$team = '';
		$sep = '';
		$tm = '';
		$year = '';
	
		while($row = mysql_fetch_row($results))
		{
			// store previous athlete before processing new athlete
			if(($a != $row[0])		// new athlete
				&& ($a > 0))			// first athlete processed
			{
				$athleteList[] = array(
					"points"=>$points
					, "name"=>$name
					, "year"=>$year
					, "info"=>$info
				);

				$points = 0;
				$info = '';
				$sep = '';
			}

			// store previous team before processing new team
			if(($tm != $row[4])		// new athlete
				&& ($tm > 0))			// first athlete processed
			{
				usort($athleteList, "AA_sheets_cmp");	// sort athletes by points

				// nbr of athletes to include in team result
				$total = 0;
				for($i=0; $i < $evaluation; $i++) {
					$total = $total + $athleteList[$i]['points'];
				}

				$teamList[] = array(
					"points"=>$total
					, "name"=>$team
					, "club"=>$club
					, "athletes"=>$athleteList
				);

				$team = '';
				$club = '';
				unset($athleteList);
				$sep = '';
			}

			$tm = $row[4];		// keep current team

			// events
			$res = mysql_query("
				SELECT
					d.Kurzname
					, d.Typ
					, MAX(r.Leistung)
					, r.Info
					, MAX(r.Punkte) AS pts
					, s.Wind
					, w.Windmessung
				FROM
					start AS st USE INDEX (Anmeldung)
					, serienstart AS ss 
					, resultat AS r 
					, serie AS s 
					, runde AS ru 
					, wettkampf AS w
					, disziplin AS d 
				WHERE st.xAnmeldung = $row[0]
				AND ss.xStart = st.xStart
				AND r.xSerienstart = ss.xSerienstart
				AND s.xSerie = ss.xSerie
				AND ru.xRunde = s.xRunde
				AND w.xWettkampf = st.xWettkampf
				AND w.Typ = " . $cfgEventType[$strEventTypeClubCombined] . "
				AND d.xDisziplin = w.xDisziplin
				AND r.Info != '" . $cfgResultsHighOut . "'
				GROUP BY
					st.xStart
				ORDER BY
					ru.Datum
					, ru.Startzeit
			");
		
			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else
			{
				while($pt_row = mysql_fetch_row($res))
				{
					// set wind, if required
					if($pt_row[6] == 1)
					{
						if($pt_row[1] == $cfgDisciplineType[$strDiscTypeTrack]) {
							$wind = " / " . $pt_row[5];
						}
						else if($pt_row[1] == $cfgDisciplineType[$strDiscTypeJump]) {
							$wind = " / " . $pt_row[3];
						}
					}
					else {
						$wind = '';
					}

					// format output
					if(($pt_row[1] == $cfgDisciplineType[$strDiscTypeJump])
						|| ($pt_row[1] == $cfgDisciplineType[$strDiscTypeJumpNoWind])
						|| ($pt_row[1] == $cfgDisciplineType[$strDiscTypeThrow])
						|| ($pt_row[1] == $cfgDisciplineType[$strDiscTypeHigh])) {
						$perf = AA_formatResultMeter($pt_row[2], true);
					}
					else {
						$perf = AA_formatResultTime($pt_row[2], true);
					}

					// calculate points
					$points = $points + $pt_row[4];	// accumulate points

					if($pt_row[4] > 0) {					// any points for this event
						$info = $info . $sep . $pt_row[0] . "&nbsp;(" . $perf . $wind . ")";
						$sep = ", ";
					}
				}	// END WHILE combined events
				mysql_free_result($res);
			}

			$a = $row[0];
			$name = $row[1] . " " . $row[2];
			$year = AA_formatYearOfBirth($row[3]);
			$team = $row[5];
			$club = $row[6];
		}	// END WHILE athlete per category

		mysql_free_result($results);

		if(!empty($tm))		// add last team if any
		{
			// last athlete
			$athleteList[] = array(
				"points"=>$points
				, "name"=>$name
				, "year"=>$year
				, "info"=>$info
			);

			// last team
			usort($athleteList, "AA_sheets_cmp");	// sort athletes by points

			$total = 0;
			for($i=0; $i < $evaluation; $i++) {
				$total = $total + $athleteList[$i]['points'];
			}

			$teamList[] = array(
				"points"=>$total
				, "name"=>$team
				, "club"=>$club
				, "athletes"=>$athleteList
			);
		}

		// print team sheets
		usort($teamList, "AA_sheets_cmp");

		foreach($teamList as $team)
		{
			if(is_a($list, "PRINT_TeamSheet")) {	// page for printing

				// page break after each team
				if($GLOBALS['AA_TC'] > 0) {				// not first team
					$list->insertPageBreak();
				}
				$GLOBALS['AA_TC']++;		// team counter

				// set up list of other competitors
				$sep = '';
				$competitors = '';
				foreach($teamList as $comp)
				{
					if($comp['name'] != $team['name'])	// not current team
					{
						$competitors = $competitors . $sep . $comp['club'];	// club
						$sep = ', ';
					}
				}

				$list->printHeader($team['club']." (".$team['name'].")", $category, $competitors);
			}
			else {
				$list->printHeaderCombined($team['club']." (".$team['name'].")", $category);
			}

			$i = 0;
			foreach($team['athletes'] as $athlete)
			{
				if($i >= $evaluation) {	// show only athletes included in end result
					break;
				}
				$i++;

				$list->printLineCombined($athlete['name'], $athlete['year'], $athlete['points']);
				$list->printDisciplinesCombined($athlete['info']);
			}


			if(is_a($list, "PRINT_TeamSheet")) {	// page for printing
				$list->printTotal($team['points']);
				$list->printFooter();
			}
			else {
				$list->printTotalCombined($team['points']);
			}
		}	// FOREACH team

	}	// ET DB error all teams

}	// end function processCombined()


//
// compare function to sort teamList
// 
function AA_sheets_cmp ($a, $b) {
    if ($a["points"]== $b["points"]) return 0;
    return ($a["points"] > $b["points"]) ? -1 : 1;
}



//
// print list of relay athletes
// 
function AA_sheets_printRelayAthletes($list, $relay)
{
	$at_res = mysql_query("
		SELECT
		  at.Name
		  , at.Vorname
		  , at.Jahrgang
		  , sta.Position
	  FROM
		  athlet AS at 
		  , anmeldung AS a 
		  , start AS s 
		  , staffelathlet AS sta 
		  , start AS st
	  WHERE s.xStaffel = $relay
	  AND sta.xStaffelstart = s.xStart
	  AND st.xStart = sta.xAthletenstart
	  AND a.xAnmeldung = st.xAnmeldung
	  AND at.xAthlet = a.xAthlet
	  ORDER BY
		  sta.Position
	");

	if(mysql_errno() > 0) {		// DB error
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		while($at_row = mysql_fetch_row($at_res))
		{
			$year = AA_formatYearOfBirth($at_row[2]);
			$list->printRelayAthlete("$at_row[3]. $at_row[0] $at_row[1], $year");
		}
		mysql_free_result($at_res);
	}
}




}	// AA_RANKINGLIST_SHEET_LIB_INCLUDED
?>
