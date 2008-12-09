<?php

/**********
 *
 *	rankinglist combined events
 *	
 */
   
if (!defined('AA_RANKINGLIST_COMBINED_LIB_INCLUDED'))
{
	define('AA_RANKINGLIST_COMBINED_LIB_INCLUDED', 1);

function AA_rankinglist_Combined($category, $formaction, $break, $cover, $sepu23, $cover_timing=false, $date = '%',$disc_nr,$catFrom,$catTo)
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
  
$contestcat = " ";
if (!empty($category)){         // show every category
    $contestcat = " AND w.xKategorie = $category";
} 

if($catFrom > 0) { 
     $getSortCat=AA_getSortCat($catFrom,$catTo);
	 if ($getSortCat[0]) {
	 	if ($catTo > 0){
			$contestcat = " AND k.Anzeige >=" . $getSortCat[$catFrom] . " AND k.Anzeige <=" . $getSortCat[$catTo] ." "; 
		}	 
		else {
			$contestcat = " AND k.Anzeige =" . $getSortCat[$catFrom] ." ";
		}
	 }
} 
                                    

// get athlete info per contest category

$results = mysql_query("
	SELECT  
		a.xAnmeldung
		, at.Name
		, at.Vorname
		, at.Jahrgang
		, k.Name
		, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo)
		, IF(at.xRegion = 0, at.Land, re.Anzeige)
		, w.Mehrkampfcode
		, d.Name
		, w.xKategorie
		, ka.Code
		, ka.Name
		, ka.Alterslimite  		
	FROM
		anmeldung AS a
		, athlet AS at
		, verein AS v
		, kategorie AS k
		, kategorie AS ka
		, start as st
		, wettkampf as w
		, disziplin as d
		LEFT JOIN region as re ON at.xRegion = re.xRegion
	WHERE a.xMeeting = " . $_COOKIE['meeting_id'] ."
	" . $contestcat . "
	AND at.xAthlet = a.xAthlet
	AND v.xVerein = at.xVerein
	AND k.xKategorie = w.xKategorie
	AND st.xAnmeldung = a.xAnmeldung
	AND w.xWettkampf = st.xWettkampf  
	AND w.Mehrkampfcode = d.Code  
	AND w.Mehrkampfcode > 0
	AND ka.xKategorie = a.xKategorie 
    AND st.anwesend = 0 
	GROUP BY
		a.xAnmeldung
	ORDER BY    	 
		k.Anzeige
		, w.Mehrkampfcode
		, ka.Alterslimite DESC
"); 
                 
  /*           
   $results= mysql_query("SELECT  
        a.xAnmeldung
        , at.Name
        , at.Vorname
        , at.Jahrgang
        , k.Name
        , IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo)
        , IF(at.xRegion = 0, at.Land, re.Anzeige)
        , w.Mehrkampfcode
        , d.Name
        , w.xKategorie
        , ka.Code
        , ka.Name
        , ka.Alterslimite
    FROM
        anmeldung AS a
        LEFT JOIN athlet AS at USING (xAthlet) 
        LEFT JOIN verein AS v USING (xVerein)
        LEFT JOIN kategorie AS k ON (w.xKategorie = k.xKategorie)
        LEFT JOIN kategorie AS ka ON (ka.xKategorie = a.xKategorie) 
        LEFT JOIN start as st  ON (st.xAnmeldung = a.xAnmeldung)
        LEFT JOIN wettkampf as w On (w.xWettkampf = st.xWettkampf)
        LEFT JOIN disziplin as d ON (w.Mehrkampfcode = d.Code) 
        LEFT JOIN region as re ON at.xRegion = re.xRegion
    WHERE a.xMeeting = " . $_COOKIE['meeting_id'] ." 
     " . $contestcat . "   
    AND w.Mehrkampfcode > 0 
    GROUP BY
        a.xAnmeldung
    ORDER BY
        w.xKategorie
        , w.Mehrkampfcode
        , ka.Alterslimite DESC
        ");     
*/               
  
if(mysql_errno() > 0) {		// DB error
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else
{     
	$cat = '';
	$catEntry = '';
	$catEntryLimit = "";
	$u23name = "";
	$comb = 0; // hold combined type
	$combName = "";
	$lastTime = ""; // hold start time of last event for print list
	$a = 0;
	$info = '';
	$points = 0;
	$sep = '';
	
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
	if($formaction == 'view') {	// display page for speaker 
		$list = new GUI_CombinedRankingList($_COOKIE['meeting']);
		$list->printPageTitle("$strRankingLists " . $_COOKIE['meeting']);
	}
	// start a new HTML print page
	elseif($formaction == "print") {
		$list = new PRINT_CombinedRankingList($_COOKIE['meeting']);
		if($cover == true) {		// print cover page 
			$list->printCover($GLOBALS['strResults'], $cover_timing);
		}
	}
	// export ranking
	elseif($formaction == "exportpress"){
		$list = new EXPORT_CombinedRankingListPress($_COOKIE['meeting'], 'txt');
	}elseif($formaction == "exportdiplom"){
		$list = new EXPORT_CombinedRankingListDiplom($_COOKIE['meeting'], 'csv');
	}
	
	while($row = mysql_fetch_row($results))
	{  
		// store previous before processing new athlete
		if(($a != $row[0])		// new athlete
			&& ($a > 0))			// first athlete processed
		{              		
			$points_arr[] = $points;
			$name_arr[] = $name;   		
			$year_arr[] = $year;
			$club_arr[] = $club;
			$info_arr[] = $info;
			$ioc_arr[] = $ioc;
			$x_arr[] = $a;
            $rank_arr[] = $rank;   
			$info = '';
			$points = 0;
			$sep = '';
		}
       
		// print previous before processing new category
		if(!empty($cat)				// not first result row
			&& 	(($row[4] != $cat || $row[7] != $comb) 	// not the same category, or not the same combined contest
				|| (($comb == 410 || $comb == 400) && $catEntry != $row[10] && $row[12] < 23 && !$bU23 && $sepu23)
					// extract the u23 categories from MAN or WOM combined when:
			)		// if last event was combined ten/seven and the athletes category has changed
					// and if the next athletes are < 23 and they are not yet separated ($bU23)
					// AND the user has choosen to separate ($sepu23)
		)
		{
			$bU23 = false; // set the separate flag! else it will be separated by each category
			if(($comb == 410 || $comb == 400) && $catEntry != $row[10] && $row[12] < 23){
				$bU23 = true;
			}
			$u23name = ''; // set the addition for the title if this is the separated cat
			if(($comb == 410 || $comb == 400) && $catEntryLimit < 23 && $sepu23){
				$u23name = " (U 23)";
			}
			
			$list->endList();                     
			$list->printSubTitle($cat."$u23name, ".$combName, "", "");     

			$list->startList();
			$list->printHeaderLine($lastTime);

		    arsort($points_arr, SORT_NUMERIC);	// sort descending by points
			$rank = 1;									// initialize rank
			$r = 0;										// start value for ranking
			$p = 0;
            
           
             $no_rank=999999;
             $max_rank=$no_rank;
            
		     foreach($points_arr as $key => $val) {
                $r++;
                
                if($limitRank && ($r < $rFrom || $r > $rTo)){ // limit ranks if set (export)
                    continue;
                }
                
                if($p != $val) {    // not same points as previous team
                    $rank = $r;        // next rank
                }
                                       
                // not set rank for invalid results 
                if (preg_match("@\(-[1-4]{0,1}@", $info_arr[$key])){    
                    $rank=$max_rank; 
                    $max_rank+=1;      
                    $r--;  
                }               
                
                $rank_arr[$key] = $rank;
                $p = $val;            // keep current points    
            
            } 
            
    		asort($rank_arr, SORT_NUMERIC);    // sort descending by rank     
            
            foreach($rank_arr as $key => $v)
            {   
                $val=$points_arr[$key];
                $rank=$v;
                
                if($rank>=$no_rank){ 
                    $rank='';
                }   
               
                $list->printLine($rank, $name_arr[$key], $year_arr[$key], $club_arr[$key], $val, $ioc_arr[$key]);
                $list->printInfo($info_arr[$key]);
               
                // insert points into combined top performance of entry
                mysql_query("UPDATE anmeldung SET BestleistungMK = $val WHERE xAnmeldung = ".$x_arr[$key]);            
            }
                         
			unset($points_arr);
			unset($name_arr);
			unset($year_arr);
			unset($club_arr);
			unset($info_arr);
			unset($ioc_arr);
			unset($x_arr);
            unset($rank_arr);   

			if(is_a($list, "PRINT_CombinedRankingList")	// page for printing
				&& ($break == 'category')) {		// page break after category
				$list->insertPageBreak();
			}
		}
		$cat = $row[4];		// keep current category
		$catEntry = $row[10];
		$catEntryLimit = $row[12];
		$comb = $row[7];
		$combName = $row[8];
		
		// events      
        $res = mysql_query("
            SELECT
                d.Kurzname
                , d.Typ
              , MAX(IF ((r.Info='-') && (d.Typ = 6) ,0,r.Leistung)) 
                , r.Info
                , MAX(IF ((r.Info='-') && (d.Typ = 6),0,r.Punkte)) AS pts    
                , s.Wind
                , w.Windmessung
                , st.xStart
                , CONCAT(DATE_FORMAT(ru.Datum,'$cfgDBdateFormat'), ' ', TIME_FORMAT(ru.Startzeit, '$cfgDBtimeFormat'))
                , w.Mehrkampfreihenfolge 
                , ss.Bemerkung
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
            AND d.xDisziplin = w.xDisziplin
            AND ( (r.Info = '" . $cfgResultsHighOut . "' && d.Typ = 6 && r.Leistung < 0)  OR  (r.Info !=  '" . $cfgResultsHighOut . "') )                                                                                
            AND w.xKategorie = $row[9]
            AND w.Mehrkampfcode = $row[7]
            GROUP BY
                st.xStart
            ORDER BY
                w.Mehrkampfreihenfolge ASC
                , ru.Datum
                , ru.Startzeit
        ");     
     
      /*  
		$res = mysql_query("
			SELECT
				d.Kurzname
				, d.Typ
				, MAX(r.Leistung)
				, r.Info
				, MAX(r.Punkte) AS pts
				, s.Wind
				, w.Windmessung
				, st.xStart
				, CONCAT(DATE_FORMAT(ru.Datum,'$cfgDBdateFormat'), ' ', TIME_FORMAT(ru.Startzeit, '$cfgDBtimeFormat'))
			FROM
				start as st USE INDEX (Anmeldung)
				, wettkampf as w
				, runde as ru
				, disziplin as d
				LEFT JOIN serie AS s ON s.xRunde = ru.xRunde
				LEFT JOIN serienstart AS ss ON ss.xSerie = s.xSerie AND ss.xStart = st.xStart
				LEFT JOIN resultat AS r ON (r.xSerienstart = ss.xSerienstart AND r.Info != '" . $cfgResultsHighOut . "')
				
			WHERE st.xAnmeldung = $row[0]
			AND w.xKategorie = $row[9]
			AND w.Mehrkampfcode = $row[7]   
			AND w.xWettkampf = st.xWettkampf   
            AND d.xDisziplin = w.xDisziplin  
			AND ru.xWettkampf = w.xWettkampf
			AND ru.Datum LIKE '".$date."'
			GROUP BY
				st.xStart
			ORDER BY
				w.Mehrkampfreihenfolge ASC
				, ru.Datum
				, ru.Startzeit
		");   
      */  
       
           
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{   $count_disc=0;
            $remark='';
			while($pt_row = mysql_fetch_row($res))
			{   $remark=$pt_row[10];  
				$lastTime = $pt_row[8];
				
				if($pt_row[1] == $cfgDisciplineType[$strDiscTypeJump]){
					$res2 = mysql_query("SELECT r.Info FROM 
								resultat as r
								LEFT JOIN serienstart as ss USING(xSerienstart)
							WHERE
								ss.xStart = $pt_row[7]
							AND	r.Punkte = $pt_row[4]");
					$row2 = mysql_fetch_array($res2);
					$pt_row[3] = $row2[0];
				}
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
					$perf = AA_formatResultTime($pt_row[2], true);
				}
                 
				 // show only points for number of choosed disciplines if the diszipline is done	  
                $count_disc++;    
                   if ($count_disc<=$disc_nr)  {
                       if($pt_row[4] > 0) {       // any points for this event 
                           $points = $points + $pt_row[4];      // calculate points   
					        $info = $info . $sep . $pt_row[0] . "&nbsp;(" . $perf . $wind . ", $pt_row[4])";                      
					        $sep = ", ";     
                       }
                        elseif ($pt_row[4] == 0 && $pt_row[2] >= 0){          //  athlete with 0 points                                   
                                $info = $info . $sep . $pt_row[0] . "&nbsp;(" . $perf . $wind . ", $pt_row[4])";                      
                                $sep = ", ";       
                        }  
                       else{ 
                         $count_disc--;   
                         $pointTxt="" ;   
                         foreach($cfgInvalidResult as $value)    // translate value
                                {
                                 if($value['code'] == $perf) {
                                    $pointTxt = $value['short'];
                                 }
                         }  
                         $info = $info . $sep . $pt_row[0] . "&nbsp;(" . $perf . $wind . ", $pointTxt)";                      
                         $sep = ", ";  
                         
                         //if($perf == null){ $perf = '0'; }
                            //$info = $info . $sep . $pt_row[0] . "&nbsp;(" . "0, 0)";
                            //$sep = ", ";  
                       } 
                   }           
			}	// END WHILE combined events
			mysql_free_result($res);
		}     
       
		$a = $row[0];
		$name = $row[1] . " " . $row[2];
		$year = AA_formatYearOfBirth($row[3]);
		$club = $row[5];
		$ioc = $row[6];   	
        $remark_arr[] = $remark;  	
	}	// END WHILE athlete per category
  
	if(!empty($a))		// add last athlete if any
	{
		$points_arr[] = $points;
		$name_arr[] = $name;
		$year_arr[] = $year;
		$club_arr[] = $club;
		$info_arr[] = $info;
		$ioc_arr[] = $ioc;
		$x_arr[] = $a;
        $remark_arr[] = $remark;
        $rank_arr[] = $rank;
	}       

	if(!empty($cat))		//	add last category if any
	{
		$u23name = '';
		if(($comb == 410 || $comb == 400) && $catEntryLimit < 23 && $sepu23){
			$u23name = " (U 23)";
		}
		$list->endList();     
		$list->printSubTitle($cat."$u23name, ".$combName, "", ""); 

		$list->startList();
		$list->printHeaderLine($lastTime);

		arsort($points_arr, SORT_NUMERIC);	// sort descending by points
		$rank = 1;									// initialize rank
		$r = 0;										// start value for ranking
		$p = 0;  
        
        $no_rank=999999;
        $max_rank=$no_rank;       
		 	   
		foreach($points_arr as $key => $val)
		{    
			$r++;                           
			
			if($limitRank && ($r < $rFrom || $r > $rTo)){ // limit ranks if set (export)
				continue;
			}
			
			if($p != $val) {	// not same points as previous athlete
				$rank = $r;		// next rank
			}  
		   	    		 	 
		    // not set rank for invalid results 
		   if (preg_match("@\(-[1-4]{0,1}@", $info_arr[$key])){  
                $rank=$max_rank; 
                $max_rank+=1;      
				$r--;  
		 	}     		  
			
			$p = $val;			// keep current points
            $rank_arr[$key]  = $rank;   
        }     
              
        asort($rank_arr, SORT_NUMERIC);    // sort descending by rank       
                       
        foreach($rank_arr as $key => $v)
        {   
            $val=$points_arr[$key];  
            $rank=$v;    
           
            if ($rank>=$no_rank) {
                $rank='';
            }
                         
            $list->printLine($rank, $name_arr[$key], $year_arr[$key], $club_arr[$key], $val, $ioc_arr[$key]);  
            $list->printInfo($info_arr[$key]);   

			// insert points into combined top performance of entry
			mysql_query("UPDATE anmeldung SET BestleistungMK = $val WHERE xAnmeldung = ".$x_arr[$key]);		
		}   		
	}

	mysql_free_result($results);
	$list->endList();

	$list->endPage();	// end HTML page for printing
}	// ET DB error all teams

}	// end function AA_rankinglist_Combined

}	// AA_RANKINGLIST_COMBINED_LIB_INCLUDED
?>
