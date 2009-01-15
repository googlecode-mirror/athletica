<?php

/**********
 *
 *	rankinglist team events
 *	
 */

if (!defined('AA_RANKINGLIST_TEAM_LIB_INCLUDED'))
{
	define('AA_RANKINGLIST_TEAM_LIB_INCLUDED', 1);

 
function AA_rankinglist_Team($category, $formaction, $break, $cover, &$parser, $event, $heatSeparate, $type, $catFrom, $catTo)  
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

global $rFrom, $rTo, $limitRank; // limits rank if limitRank set to true
$rFrom = 0; $rTo = 0;
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
	$GLOBALS[$list] = new GUI_TeamRankingList($_COOKIE['meeting']);
	$GLOBALS[$list]->printPageTitle("$strRankingLists " . $_COOKIE['meeting']);
}
// catch output and do nothing exept for theam rankings
// these will be added to the xml result file
elseif($formaction == "xml"){
	$GLOBALS['xmladdon'] = true;
	$GLOBALS[$list] = new XML_TeamRankingList($parser);
}
// start a new HTML print page
elseif($formaction == "print") {                  
	$GLOBALS[$list] = new PRINT_TeamRankingList($_COOKIE['meeting']);
	if($cover == true) {		// print cover page 
		$GLOBALS[$list]->printCover($GLOBALS['strResults']);
	}
}
// export ranking
elseif($formaction == "exportpress"){
	$GLOBALS[$list] = new EXPORT_TeamRankingListPress($_COOKIE['meeting'], 'txt');
}elseif($formaction == "exportdiplom"){
	$GLOBALS[$list] = new EXPORT_TeamRankingListDiplom($_COOKIE['meeting'], 'csv');
}

$selection = ''; 
if($formaction != "xml"){
	if ($event!='')  {
    	$mergedCat=AA_mergedCatEvent($category, $event);  
		}
	else {
    	$mergedCat=AA_mergedCat($category); 
	}
}
  
if(!empty($category)) {        // show every category  
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
   // show category from .... to
if($catFrom > 0) {        
     $getSortCat=AA_getSortCat($catFrom,$catTo);
	 if ($getSortCat[0]) {
	 	if ($catTo > 0){
			$selection = " AND k.Anzeige >=" . $getSortCat[$catFrom] . " AND k.Anzeige <=" . $getSortCat[$catTo] . " "; 
		}	 
		else {
			$selection = "AND k.Anzeige =" . $getSortCat[$catFrom] . " ";
		}
	 }
}  

// evaluation per category
global $cfgEventType, $strEventTypeSingleCombined, $strEventTypeClubMA, 
	$strEventTypeClubMB, $strEventTypeClubMC, $strEventTypeClubFA, 
	$strEventTypeClubFB, $strEventTypeClubBasic, $strEventTypeClubAdvanced, 
	$strEventTypeClubTeam, $strEventTypeClubCombined, $strEventTypeTeamSM;
	 
$results = mysql_query("
	SELECT
	  	k.xKategorie
	  	, k.Name
		, w.Typ
  	FROM
	  	wettkampf AS w
	  	, kategorie AS k
  	WHERE w.xMeeting = " . $_COOKIE['meeting_id'] ."
	" . $selection . "
  	AND k.xKategorie = w.xKategorie "
  	// AND w.Typ >=  " . $cfgEventType[$strEventTypeClubMA] ."           // old svm
    ." AND w.Typ >=  " . $cfgEventType[$strEventTypeClubBasic] ."   
	AND w.Typ <  " . $cfgEventType[$strEventTypeTeamSM] ."
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
	// process all teams per category
	while($row = mysql_fetch_row($results))
	{
		// Club rankinglist:Combined 
		if ($row[2] == $cfgEventType[$strEventTypeClubCombined])
		{  
			processCombined($row[0], $row[1], $type);
		}
		// Club rankinglist: Single
		else
		{    
			processSingle($row[0], $row[1]);
		}
	}

	mysql_free_result($results);
}	// ET DB error categories 

$GLOBALS[$list]->endPage();	// end HTML page for printing

}	// end function AA_rankinglist_Team

//
//	process club single events
//

function processSingle($xCategory, $category)
{   
	global $rFrom, $rTo, $limitRank;
	require('./config.inc.php');

	mysql_query("
		LOCK TABLES
	  		anmeldung AS a 
  			, disziplin READ
  			, resultat READ
  			, serienstart READ
  			, staffel READ
  			, start READ
			, team READ
			, verein READ
  			, wettkampf READ
  			, tempresult WRITE
			, kategorie READ
			, athlet as a READ
	");

	// get all teams for this category
	$results = mysql_query("
		SELECT
			t.xTeam
			, t.Name
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
		return;
	}

	$tm = 0;			// team
	$info = '';
	$points = 0;
	$sep = '';
	$temptable = false;

	// process all teams
	while($row = mysql_fetch_row($results))
	{
		// store previous before processing new team
		if(($tm != $row[0])		// new team
			&& ($tm > 0))			// first team processed
		{  
			$teamList[] = array(
				"points"=>$points
				, "team"=>$team
				, "club"=>$club
				, "info"=>$info
				, "id"=>$tm		// needed for result upload
			);

			$info = '';
			$points = 0;
			$sep = '';
		}

		// single events
		// -------------
		$res = mysql_query("
  			SELECT
	  			d.Kurzname
	  			, MAX(r.Punkte) AS pts
	  			, w.Typ
	  			, d.Typ
				, k.Code
				, at.Geschlecht                
  			FROM
	  			wettkampf AS w
	  			, disziplin AS d 
	  			, start AS st 
	  			, serienstart AS ss 
	  			, resultat AS r 
	  			, anmeldung AS a 
				, kategorie AS k
				, athlet AS at
  			WHERE w.xMeeting = " . $_COOKIE['meeting_id'] ."
  			AND w.xKategorie = $xCategory
  			AND d.xDisziplin = w.xDisziplin
  			AND st.xWettkampf = w.xWettkampf
  			AND ss.xStart = st.xStart
  			AND r.xSerienstart = ss.xSerienstart
  			AND a.xAnmeldung = st.xAnmeldung
			AND at.xAthlet = a.xAthlet
  			AND a.xTeam = $row[0]
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
			$c = 0;
			$d = '';
			$g = 0;
			$p = 0;
			$mixedTeamCount = array('m'=>0, 'w'=>0);
            
			while($pt_row = mysql_fetch_row($res))
			{   
				// nbr of athletes to be included in total points
				switch($pt_row[2]) {
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
					case $cfgEventType[$strEventTypeClubMB]:
					case $cfgEventType[$strEventTypeClubMC]:
					case $cfgEventType[$strEventTypeClubFA]:
					case $cfgEventType[$strEventTypeClubFB]:
						$a = 1;
						if($c == 0) {	// first time here: initialize temp. table
							mysql_query("
								CREATE TABLE tempresult(
									Disziplinengruppe tinyint(4)
									, Punkte smallint(6)
									, Wettkampftyp tinyint(4)
									, Disizplinentyp tinyint(4)
									)
								TYPE=HEAP
							");
						}
						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
						else {
							$temptable = true;
						}
						break;
					default:
						$a = 1;
				}
		
				if($pt_row[0] != $d) 	// new discipline
				{
					// accumulate points of previous event
					if($p > 0) {
						$info = $info . $sep . $d;
						$sep = ", ";
						$points = $points + $p;		// accumulate points
					}
                  
					$c = 0;					// athlete counter
					$p = 0;					// point counter
					$mixedTeamCount = array('m'=>0, 'w'=>0);// mixedteam counter
				}

				if($c < $a)		// add only top ranking athletes
				{
					// special mixed-team schüler svm
					// get 3 men results and 3 women results
					if($pt_row[2] == $cfgEventType[$strEventTypeClubMixedTeam]){
						
						if(empty($pt_row[5])){
							if(substr($pt_row[4],0,1) == 'M' || substr($pt_row[4],3,1) == 'M'){
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
							if($pt_row[5] == "m"){
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
					
					// average points
					if($pt_row[2] == $cfgEventType[$strEventTypeClubAdvanced]) {  
						$p = $p + $pt_row[1] / 2;      
					}elseif($pt_row[2] == $cfgEventType[$strEventTypeSVMNL]) {
						//$p = $p + $pt_row[1] / 2;   
						$p = $p + $pt_row[1];
					}elseif($pt_row[2] == $cfgEventType[$strEventTypeClubMixedTeam]) {                       
						$p = $p + $pt_row[1] / 6;
						$p = round($p, $cfgResultsPointsPrecision);
					}elseif($pt_row[2] == $cfgEventType[$strEventTypeClubTeam]) {                       
						$p = $p + $pt_row[1] / 5;
						$p = round($p, $cfgResultsPointsPrecision);
					}
					
					// total points
					else {
						$p = $p + $pt_row[1];
					}
					
					// last athlete
					if(($c + 1) == $a){
						/*if($pt_row[2] == $cfgEventType[$strEventTypeClubMixedTeam]) {
							$p = $p * ($cfgResultsPointsPrecision * 10);
							$p = floor($p / 6);
							$p = $p/($cfgResultsPointsPrecision * 10);
						}*/
					}
				}
				else if ($temptable == true)	// temp table created
				{
					switch($pt_row[3]) {
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
							, $pt_row[1]
							, $pt_row[2]
							, $pt_row[3])
					");

					if(mysql_errno() > 0) {		// DB error
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}

				}

				$c++;
				$d = $pt_row[0];				// keep discipline
                
			}	// END WHILE team events
           
			// accumulate points of last event
			if($p > 0) {
				$info = $info . $sep . $d;
				$sep = ", ";
				$points = $points + $p;		// accumulate points
			}

			mysql_free_result($res);
		}

		// relay events
		// ------------
		$res = mysql_query("
  			SELECT
	  			d.Kurzname
	  			, MAX(r.Punkte) AS pts
	  			, w.Typ
	  			, d.Typ
  			FROM
	  			wettkampf AS w
	  			, disziplin AS d 
	  			, start AS st 
	  			, serienstart AS ss 
	  			, resultat AS r 
	  			, staffel AS s 
  			WHERE w.xMeeting = " . $_COOKIE['meeting_id'] ."
  			AND w.xKategorie = $xCategory
  			AND d.xDisziplin = w.xDisziplin
  			AND st.xWettkampf = w.xWettkampf
  			AND ss.xStart = st.xStart
  			AND r.xSerienstart = ss.xSerienstart
  			AND s.xStaffel = st.xStaffel
  			AND s.xTeam = $row[0]
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
			$a = 1;
			$c = 0;
			$d = '';
			$p = 0;

			while($pt_row = mysql_fetch_row($res))
			{
				if($pt_row[0] != $d) 	// new discipline
				{
					// accumulate points of previous event
					if($p > 0) {
						$info = $info . $sep . $d;
						$sep = ", ";
						$points = $points + $p;		// accumulate points
					}

					$c = 0;					// ranking counter
					$p = 0;					// point counter
				}

				if($c < $a)		// count only top ranking results
				{
					// calculate points
					$p = $p + $pt_row[1];
				}
				else if ($temptable == true)
				{
					// add result to list for further processing (group 1 = run)
					mysql_query("
						INSERT INTO tempresult
						VALUES(
							1
							, $pt_row[1]
							, $pt_row[2]
							, $pt_row[3])
					");

					if(mysql_errno() > 0) {		// DB error
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
				}

				$c++;
				$d = $pt_row[0];				// keep discipline
			}	// END WHILE team events

			// accumulate points of last event
			if($p > 0) {
				$info = $info . $sep . $d;
				$sep = ", ";
				$points = $points + $p;		// accumulate points
			}

			mysql_free_result($res);
		}

		// evaluate remaining results (Event Type: ClubMA to ClubFB)
		if($temptable == true)
		{
			$res = mysql_query("
				SELECT
					Disziplinengruppe
					, Punkte
					, Wettkampftyp
					, Disizplinentyp
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

				$c = 0;					// athlete counter
				$g = 0;					// group indicator
				$p = 0;					// point counter
               
				while($pt_row = mysql_fetch_row($res))
				{
					// nbr of athletes per disc. group to be included in total points
					switch($pt_row[2]) {
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

					if($c < $a)		// add only top ranking athletes
					{
						$p = $p + $pt_row[1];
					}
					else if ($pt_row[3] != $cfgDisciplineType[$strDiscTypeRelay])
					{
						// add result to list for further processing (group 1 = run)
						mysql_query("
							INSERT INTO tempresult
							VALUES(
								0
								, $pt_row[1]
								, $pt_row[2]
								, 0)
						");
					}

					if(mysql_errno() > 0) {		// DB error
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}


					$c++;
					$g = $pt_row[0];
				}	// END WHILE remaining events

				// accumulate points of last event
				if($p > 0) {
					$points = $points + $p;		// accumulate
				}
				
				mysql_free_result($res);
			}

			$res = mysql_query("
				SELECT
					Disziplinengruppe
					, Punkte
					, Wettkampftyp
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

				$c = 0;					// athlete counter
				$p = 0;					// point counter
               
				while($pt_row = mysql_fetch_row($res))
				{
					// nbr of remaining athletes to be included in total points
					switch($pt_row[2]) {
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
			
					if($pt_row[0] != $g) 	// new discipline	group
					{
						// accumulate points of previous event
						if($p > 0) {
                            
							$points = $points + $p;		// accumulate points
						}
						
						$c = 0;					// athlete counter
						$p = 0;					// point counter
					}

					if($c < $a)		// add only top ranking athletes
					{
						$p = $p + $pt_row[1];
					}

					$c++;
					$g = $pt_row[0];
				}	// END WHILE remaining events

				// accumulate points of last event
				if($p > 0) {
                    
					$points = $points + $p;		// accumulate
				}

				mysql_free_result($res);
			}

		}

		$tm = $row[0];
		$team = $row[1];
		$club = $row[2];

		mysql_query("DROP TABLE IF EXISTS tempresult");
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		$temptable = false;	// reset temp table indicator

	}	// END WHILE teams	

	if(!empty($tm))		// add last team if any
	{    
		$teamList[] = array(
			"points"=>$points
			, "team"=>$team
			, "club"=>$club
			, "info"=>$info
			, "id"=>$tm
		);
	}

	$GLOBALS[$list]->printSubTitle($category, "", "");
	$GLOBALS[$list]->startList();
	$GLOBALS[$list]->printHeaderLine();
    
    
	usort($teamList, "cmp");
	$rank = 1;									// initialize rank
	$r = 0;										// start value for ranking
	$p = 0;
	foreach($teamList as $team)
	{
		$r++;
		
		if($limitRank && ($r < $rFrom || $r > $rTo)){ // limit ranks if set (export)
			continue;
		}
		
		if($p != $team['points']) {	// not same points as previous team
			$rank = $r;		// next rank
		}
		else {
			$rank = '';
		}
		
		if($GLOBALS['xmladdon']){ 
			$GLOBALS[$list]->printLine($rank, $team['team'], $team['club'], $team['points'], $team['id']);
		}else{
			$GLOBALS[$list]->printLine($rank, $team['team'], $team['club'], $team['points']);
		}
		$GLOBALS[$list]->printInfo($team['info']);
		$p = $team['info'];			// keep current points
	}
	$GLOBALS[$list]->endList();

	mysql_query("UNLOCK TABLES");

}	// end function processSingle()


//
//	process club combined events
//

function processCombined($xCategory, $category, $type)
{  
	global $rFrom, $rTo, $limitRank;
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
            , IF(at.xRegion = 0, at.Land, re.Anzeige) AS Land
		FROM
			anmeldung AS a
			, athlet AS at
			, team AS t
			, verein AS v
			, start as st
			, wettkampf as w
            LEFT JOIN region AS re ON (at.xRegion = re.xRegion) 
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
        $evaluationPt = 5;              //  nbr of athletes fot calcualte points  
		if ($type=='teamAll')   
           $evaluation = 99999999;      //  all athletes    
        else                                                                                                     
           $evaluation = 5;	            // nbr of athletes included in total result
       
		$a = 0;
		$club = '';
		$info = '';
		$name = '';
		$points = 0;
		$team = '';
		$sep = '';
		$tm = '';
		$year = '';
        $country = '';  
	
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
                    , "country"=>$country
				);

				$points = 0;
				$info = '';
				$sep = '';
                
			}

			// store previous team before processing new team
			if(($tm != $row[4])		// new athlete
				&& ($tm > 0))			// first athlete processed
			{
				usort($athleteList, "cmp");	// sort athletes by points

				// nbr of athletes to include in team result
				$total = 0;
				for($i=0; $i < $evaluationPt; $i++) {
					$total = $total + $athleteList[$i]['points'];
				}    
                
				$teamList[] = array(
					"points"=>$total
					, "team"=>$team
					, "club"=>$club
					, "athletes"=>$athleteList
					, "id"=>$tm
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
                    , st.xAnmeldung
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
						$perf = AA_formatResultMeter($pt_row[2]);
					}
					else {
						$perf = AA_formatResultTime($pt_row[2]);
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
            $country = $row[7];
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
                , "country"=>$country  
			);
           
			// last team
			usort($athleteList, "cmp");	// sort athletes by points

			$total = 0;    
			for($i=0; $i < $evaluationPt; $i++) {
				$total = $total + $athleteList[$i]['points'];
			}

			$teamList[] = array(
				"points"=>$total
				, "team"=>$team
				, "club"=>$club
				, "athletes"=>$athleteList
				, "id"=>$tm
			);
		}
       
		$GLOBALS[$list]->printSubTitle("$category", "", "");
		$GLOBALS[$list]->startList();
		$GLOBALS[$list]->printHeaderLine();
        
		usort($teamList, "cmp");
		$rank = 1;									// initialize rank
		$r = 0;										// start value for ranking
		$p = 0;
		$tp = 0;
		foreach($teamList as $team)
		{
			$r++;
			
			if($limitRank && ($r < $rFrom || $r > $rTo)){ // limit ranks if set (export)
				continue;
			}
			
			if($p != $team['points']) {	// not same points as previous team
				$rank = $r;		// next rank
			}
			if($GLOBALS['xmladdon']){
				$GLOBALS[$list]->printLine($rank, $team['team'], $team['club'], $team['points'], $team['id']);
			}else{
				$GLOBALS[$list]->printLine($rank, $team['team'], $team['club'], $team['points']);
			}
			$p = $team['points'];			// keep current points

			$i = 0;
			$xmlinfo = "";
			foreach($team['athletes'] as $athlete)
			{   
				if($i >= $evaluation) {	// show only athletes included in end result
					break;
				}
				$i++;
               
				$GLOBALS[$list]->printAthleteLine($athlete['name'], $athlete['year'], $athlete['points'], $athlete['country']);
				if($GLOBALS['xmladdon']){
					$xmlinfo .= $athlete['name']." (".$athlete['points'].") / ";
				}else{
					$GLOBALS[$list]->printInfo($athlete['info']);
				}
			}
			
			if($GLOBALS['xmladdon']){
				$GLOBALS[$list]->printInfo(substr($xmlinfo,0,strlen($xmlinfo)-2));
			}
		}

		$GLOBALS[$list]->endList();
	}	// ET DB error all teams

}	// end function processCombined()


//
// compare function to sort teamList
// 
function cmp ($a, $b) {
    if ($a["points"]== $b["points"]) return 0;
    return ($a["points"] > $b["points"]) ? -1 : 1;
}



}	// AA_RANKINGLIST_TEAM_LIB_INCLUDED
?>
