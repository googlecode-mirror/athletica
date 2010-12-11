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
                                    
$dCode = 0;
// get athlete info per contest category

$results = mysql_query("
	SELECT DISTINCT 
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
        , d.Code             
	FROM
		anmeldung AS a
		, athlet AS at
		, verein AS v
		, kategorie AS k
		, kategorie AS ka
		, start as st
		, wettkampf as w
		, disziplin_" . $_COOKIE['language'] . " as d
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
	ORDER BY    	 
		k.Anzeige
		, w.Mehrkampfcode
		, ka.Alterslimite DESC
"); 
            
  
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
	{  $dCode = $row[13];
     
		// store previous before processing new athlete
		if(($a != $row[0])		// new athlete
			&& ($a > 0))			// first athlete processed
		{              		
			$points_arr[] = $points;     
           if ($dCode_keep == 403){     // Athletic Cup
                 $points_arr_max_disc[$xKat][] = $points_disc;    
            }
            else {
                 $points_arr_max_disc[$xKat][] = AA_get_MaxPointDisc($points_disc); 
            }     
           
            $points_arr_more_disc[$xKat][] = AA_get_MoreBestPointDisc($points_disc);  
           
            $points_arr_more_disc_all[$row[9]][] = $points_disc; 
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
                if (preg_match("@\(-[1]{1}@", $info_arr[$key])){ 
                    $rank=$max_rank; 
                    $max_rank+=1;      
                    $r--;  
                }               
                
                $rank_arr[$key] = $rank;
                $p = $val;            // keep current points    
            
            } 
            
    		asort($rank_arr, SORT_NUMERIC);    // sort descending by rank    
           
            $rank_keep = 0; 
                      
            foreach($rank_arr as $key => $v) {
                  $val=$points_arr[$key];  
                  $rank=$v;  
                   
                  if ($rank == $rank_keep){ 
                   if ($dCode_keep == 403){          // Athletic Cup
                        $c=0;
                        $c_max_disc1 = 0;
                        $c_max_disc2 = 0; 
                        for ($i=0;$i<3;$i++){
                            if ($points_arr_max_disc[$xKat][$key_keep][$i] > $points_arr_max_disc[$xKat][$key][$i]){
                                $c_max_disc1++; 
                            }
                            if ($points_arr_max_disc[$xKat][$key_keep][$i] < $points_arr_max_disc[$xKat][$key][$i]){
                                $c_max_disc2++; 
                            }
                        }
                        
                        if ($c_max_disc1 == 2 && $c_max_disc2 == 1){    // two disciplines more points
                            $rank_arr[$key]++;      
                        }
                        elseif ($c_max_disc1 == 1 && $c_max_disc2 == 2){    // two disciplines more points
                            $rank_arr[$key_keep]++; 
                        }
                        elseif ($c_max_disc1 == 1 && $c_max_disc2 == 1){      // one discipline same points and total same points
                                $k=AA_get_BestPointDisc($points_arr_max_disc{$xKat}[$key_keep],$points_arr_max_disc[$xKat][$key],$key_keep,$key); 
                                $rank_arr[$k]++;   
                        }                    
                   }
                   else {        // other combined events
                        if  ($points_arr_more_disc[$xKat][$key_keep] < $points_arr_more_disc[$xKat][$key]){  
                            $rank_arr[$key_keep]++;  
                        }
                        elseif ($points_arr_more_disc[$xKat][$key_keep] > $points_arr_more_disc[$xKat][$key]){  
                                $rank_arr[$key]++;   
                        }
                        else {       // always same points --> check 
                            $max_key = AA_get_BestPointDisc($points_arr_more_disc_all[$xKat][$key_keep], $points_arr_more_disc_all[$xKat][$key], $key_keep, $key);
                            $rank_arr[$max_key]++;  
                        }   
                   } 
                }            
                $rank_keep = $rank;  
                $key_keep = $key;   
            
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
        $xKat = $row[9];
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
                , w.info
            FROM
                start AS st USE INDEX (Anmeldung)
                LEFT JOIN serienstart AS ss ON (ss.xStart = st.xStart )
                LEFT JOIN resultat AS r ON (r.xSerienstart = ss.xSerienstart) 
                LEFT JOIN serie AS s ON (s.xSerie = ss.xSerie)
                LEFT JOIN runde AS ru ON (ru.xRunde = s.xRunde)
                LEFT JOIN wettkampf AS w ON (w.xWettkampf = st.xWettkampf)
                LEFT JOIN disziplin_" . $_COOKIE['language'] . " AS d ON (d.xDisziplin = w.xDisziplin)
            WHERE st.xAnmeldung = $row[0]            
            AND ( (r.Info = '" . $cfgResultsHighOut . "' && d.Typ = 6 && r.Leistung < 0)  OR  (r.Info !=  '" . $cfgResultsHighOut . "') )                                                                                
            AND w.xKategorie = $row[9]
            AND w.Mehrkampfcode = $row[7]
            AND ru.Status = " . $cfgRoundStatus['results_done'] . "   
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
            $points_disc = array();
            
			while($pt_row = mysql_fetch_row($res))
			{    
                $remark=$pt_row[10];  
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
                       
                        if (!empty($pt_row[11])) {
                             $pt_row[11] = " ($pt_row[11])";
                        } 
                       if($pt_row[4] > 0) {       // any points for this event 
                     
                           $points = $points + $pt_row[4];      // calculate points 
                           if ($dCode == 403) {                // Athletic Cup
                               switch ($pt_row[1]){
                                   case 1:
                                   case 2: $c=0;          // track
                                           break;
                                   case 4:
                                   case 6: $c=1;          // jump and high
                                           break; 
                                   case 8: $c=2;          // throw
                                           break;  
                                   default: $c=0;
                                           break;
                               }
                                $points_disc[$c]=$pt_row[4];
                           } 
                           else {
                                $points_disc[$pt_row[9]]=$pt_row[4];
                               
                           }
                          
					       $info = $info . $sep . $pt_row[0] . $pt_row[11]. "&nbsp;(" . $perf . $wind . ", $pt_row[4])";                      
					       $sep = ", ";     
                       }
                        elseif ($pt_row[4] == 0 && $pt_row[2] >= 0){          //  athlete with 0 points                                   
                                $info = $info . $sep . $pt_row[0] . $pt_row[11] . "&nbsp;(" . $perf . $wind . ", $pt_row[4])";                      
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
                         
                         $info = $info . $sep . $pt_row[0] . $pt_row[11] . "&nbsp;(" . $perf . $wind . ", $pointTxt)";                      
                         $sep = ", ";     
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
        $xKat = $row[9]; 	
        $points_disc_keep = $points_disc;
        $dCode_keep = $dCode;
        
	}	// END WHILE athlete per category
  
	if(!empty($a))		// add last athlete if any
	{
		$points_arr[] = $points;  
        
         if ($dCode == 403){
                 $points_arr_max_disc[$xKat][] = $points_disc;    
            }
            else {
                 $points_arr_max_disc[$xKat][] = AA_get_MaxPointDisc($points_disc);  
            }   
        
        $points_arr_more_disc[$xKat][] = AA_get_MoreBestPointDisc($points_disc);
        $points_arr_more_disc_all[$xKat][] = $points_disc; 
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
        $k = 0;  
        
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
            else {
                if ($dCode == 403){         // Athletic Cup
                
                    if ($points_arr_max_disc[$xkat][$key] > $points_arr_max_disc[$xKat][$k]){
                        $rank_arr[$k]  = $r;
                    }   
                }
            }  
            
		   	    		 	 
		    // not set rank for invalid results 
		    if (preg_match("@\(-[1]{1}@", $info_arr[$key])){ 
                $rank=$max_rank; 
                $max_rank+=1;      
				$r--;  
		 	}     		  
			
			$p = $val;			// keep current points
            $k = $key;            // keep current key
            $rank_arr[$key]  = $rank;   
        }   
              
        asort($rank_arr, SORT_NUMERIC);    // sort descending by rank       
        
         $rank_keep = 0; 
                   
         foreach($rank_arr as $key => $v){
                $val=$points_arr[$key];  
                $rank=$v;   
               
                if ($rank == $rank_keep){ 
                   if ($dCode == 403){          // Athletic Cup
                        $c=0;
                        $c_max_disc1 = 0;
                        $c_max_disc2 = 0; 
                        for ($i=0;$i<3;$i++){
                            if ($points_arr_max_disc[$xKat][$key_keep][$i] > $points_arr_max_disc[$xKat][$key][$i]){
                                $c_max_disc1++; 
                            }
                            if ($points_arr_max_disc[$xKat][$key_keep][$i] < $points_arr_max_disc[$xKat][$key][$i]){
                                $c_max_disc2++; 
                            }
                        }
                        
                        if ($c_max_disc1 == 2 && $c_max_disc2 == 1){    // two disciplines more points
                            $rank_arr[$key]++;     
                        }
                        elseif ($c_max_disc1 == 1 && $c_max_disc2 == 2){    // two disciplines more points
                            $rank_arr[$key_keep]++;   
                        }
                        elseif ($c_max_disc1 == 1 && $c_max_disc2 == 1){      // one discipline same points and total same points
                                $k=AA_get_BestPointDisc($points_arr_max_disc{$xKat}[$key_keep],$points_arr_max_disc[$xKat][$key],$key_keep,$key);    
                                $rank_arr[$k]++;  
                        }                    
                   }
                   else {        // other combined events
                        if  ($points_arr_more_disc[$xKat][$key_keep] < $points_arr_more_disc[$xKat][$key]){  
                            $rank_arr[$key_keep]++; 
                        }
                        elseif ($points_arr_more_disc[$xKat][$key_keep] > $points_arr_more_disc[$xKat][$key]){  
                                $rank_arr[$key]++;    
                        }
                        else {       // always same points --> check 
                            $max_key = AA_get_BestPointDisc($points_arr_more_disc_all[$xKat][$key_keep], $points_arr_more_disc_all[$xKat][$key], $key_keep, $key);
                            $rank_arr[$max_key]++; 
                        }   
                   } 
                }            
                $rank_keep = $rank;  
                $key_keep = $key; 
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
