<?php

/**********
 *
 *	timetable extension
 *	-------------------
 * The timetable function is used to print an overview of all events
 * of a meeting.   
 */

if (!defined('AA_TIMETABLE_LIB_INCLUDED'))
{
	define('AA_TIMETABLE_LIB_INCLUDED', 1);



/**
 *	show timetable
 *	-------------------
 */
function AA_timetable_display()
{   
    
 require_once('./lib/cl_http_data.lib.php'); //include class      
 require_once('./lib/common.lib.php'); //include class  
 
 require('./config.inc.php');  
 require('./config.inc.end.php');    

 $p = "./tmp";
 $fp = @fopen($p."/index.html",'w');

 if(!$fp){     
    AA_printErrorMsg($GLOBALS['strErrFileOpenFailed']);  
    return;
 }    
     
    $content = $cfgHtmlStart1;      
     
    $content .= "<meta http-equiv='refresh' content='" . $GLOBALS['cfgMonitorReload'] . ";  url=http://" . $GLOBALS['cfgUrl'] . "/" . $GLOBALS['cfgDir'] ."/index.html'>";  
            
    
    $content .= $GLOBALS['cfgHtmlStart2'];   
    $content .= "<div id='navi'>\r\n";  
    $content_navi = "<div id='navi_pc'>\r\n";    
    
    $content .= "<div id='navi_left'>\r\n";  
    
   
    require('./lib/rankinglist_single.lib.php');   
    require('./lib/results_track.lib.php');  
    require('./lib/results_tech.lib.php');   
    require('./lib/results_high.lib.php');    
    require('./config.inc.php');   
    require("./lib/cl_ftp_data.lib.php");       
        
	$result = mysql_query("
		SELECT DISTINCT
			k.Name
		FROM
			athletica.wettkampf AS w
			LEFT JOIN athletica.kategorie AS k ON (w.xKategorie = k.xKategorie)
		WHERE w.xMeeting = " . $_COOKIE['meeting_id'] . "  		
		ORDER BY
			k.Anzeige,
            k.Kurzname
	");
    
	if(mysql_errno() > 0)	// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else			// no DB error
	{
		        
		// all rounds ordered by date/categorie/time
		// - count nbr of present athletes or relays (don't include
		//   athletes starting in relays)
		// - group by r.xRunde to show event-rounds entered more than once
		// - group by s.xWettkampf to count athletes per event
		// (the different date and time fields are required to properly set
		// up the table)
		
         $sql="SELECT
                r.xRunde
                , r.Status
                , rt.Typ
                , k.Name
                , d.Kurzname
                , IF(s.xWettkampf IS NULL,0,COUNT(*))
                , TIME_FORMAT(r.Startzeit, '$cfgDBtimeFormat')
                , TIME_FORMAT(r.Startzeit, '%H')
                , DATE_FORMAT(r.Datum, '$cfgDBdateFormat')
                , r.Datum
                , r.xWettkampf
                , k.xKategorie
                , r.Speakerstatus
                , w.Info
                , r.StatusZeitmessung
                , r.Gruppe
                , w.Typ
                , w.Mehrkampfende
                , w.Mehrkampfcode
                , rs.xRundenset
                , rs.Hauptrunde
                , d.Name
                , rt.Name
                , r.StatusChanged
                , m.StatusChanged
            FROM
                athletica.runde AS r
                LEFT JOIN athletica.wettkampf AS w ON (r.xWettkampf = w.xWettkampf)
                LEFT JOIN athletica.kategorie AS k ON (w.xKategorie = k.xKategorie)
                LEFT JOIN athletica.disziplin_" . $_COOKIE['language'] . " AS d ON (w.xDisziplin = d.xDisziplin)
            LEFT JOIN athletica.rundentyp_" . $_COOKIE['language'] . " AS rt
                ON r.xRundentyp = rt.xRundentyp
            LEFT JOIN athletica.start AS s
                ON w.xWettkampf = s.xWettkampf
                AND s.Anwesend = 0
                AND ((d.Staffellaeufer = 0
                    AND s.xAnmeldung > 0)
                    OR (d.Staffellaeufer > 0
                    AND s.xStaffel > 0))
            LEFT JOIN athletica.rundenset AS rs ON (rs.xRunde = r.xRunde AND rs.xMeeting = " . $_COOKIE['meeting_id'] .") 
            LEFT JOIN athletica.meeting AS m ON (w.xMeeting = m.xMeeting)
            WHERE w.xMeeting=" . $_COOKIE['meeting_id'] ."  
            AND w.Mehrkampfcode = 0
            GROUP BY
                r.xRunde
                , s.xWettkampf
             ORDER BY
                r.Datum
                , k.Anzeige 
                , r.Startzeit                
                , k.Kurzname
                , d.Anzeige";
       
        $res = mysql_query($sql);
        
        
		if(mysql_errno() > 0)	// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else			// no DB error
		{
			$date = 0;
			$hour = '';
			$time = 0;
			$i=0;
            $l=0;
            $s=0;
			$k='';
            $m_statusChanged = 'n';
			$events = array();	// array to hold last processed round per event
			
            $content .= "<table class='timetable' >\r\n ";
            $content_navi .= "<table class='timetable' >\r\n ";  
           
            $count_navi = mysql_num_rows($res);
            $count_navi_half = round($count_navi/2, 0);
            
           
			while ($row = mysql_fetch_row($res))
			{     /*
                if ($i == $count_navi_half){                  
                    $content .= "</table>\r\n ";
                    $content .= "</div>\r\n ";
                    $content .= "<div id='navi_right'>\r\n";                      
                    $content .= "<table class='timetable' >\r\n ";  
                }
               */
                $event = $row[10];            
				$combGroup = "";	// combined group if set
				$combined = false;	// is combined event
				$teamsm = false;	// is team sm event
				if($row[16] == $cfgEventType[$strEventTypeSingleCombined]){
					$combined = true;
				}
				if($row[16] == $cfgEventType[$strEventTypeTeamSM]){
					$teamsm = true;
				}
				$roundSet = $row[19];	    // set if round is merged
				$roundSetMain = $row[20];	// main round flag of round set
								            // if main round -> count athletes from all merged rounds                                                       
                
				// new date or time: start a new line
				//if(($date != $row[8]) || ($time != $row[6]) || ($time == $row[6]) && $k != $row[3] || ($i == $count_navi_half))	// new date or time
                if(($date != $row[8]) || ($time != $row[6]) || ($time == $row[6]) && $k != $row[3] )    // new date or time 
				{
					if($date != 0) {		// not first item    
                        $content .= "</td>\r\n</tr> \r\n "; 
                        $content_navi .= "</td>\r\n</tr> \r\n ";  
					}

					if($date != $row[8])	{	// new date -> headerline with date   
                        $content .= "<tr>\r\n<th class='date' id='".$row[9].$row[7]."'>". $row[8]. "</th>\r\n<th width='100%' class='timetable'>" .$row[3]."</th>\r\n</tr>\r\n";  
                        $content_navi .= "<tr>\r\n<th class='date' id='".$row[9].$row[7]."'>". $row[8]. "</th>\r\n<th width='100%' class='timetable'>" .$row[3]."</th>\r\n</tr>\r\n";  
					} 
                    else {  
                            if ($k != $row[3]){  
                                $content .="<tr>\r\n<th class='timetable_sub' id='".$row[9].$row[7]."' /></th>\r\n<th class='timetable'>" .$row[3]."</th>\r\n</tr>\r\n</tr>\r\n";
                                $content_navi .="<tr>\r\n<th class='timetable_sub' id='".$row[9].$row[7]."' /></th>\r\n<th class='timetable'>" .$row[3]."</th>\r\n</tr>\r\n</tr>\r\n";
                            }     
					}		// ET new date or new hour
                  

					if($i % 2 == 0 ) {		// even row number
						$class='even';
					}
					else {	// odd row number
						$class='odd';
					}
                   
					
					$k = '';   
                   
                    $content .= "<tr class='". $class."'>\r\n<th class='timetable_sub'>". $row[6] ."</th>\r\n"; 
                    $content_navi .= "<tr class='". $class."'>\r\n<th class='timetable_sub'>". $row[6] ."</th>\r\n"; 
                   
				}		// ET new date, time
                else {
                    if ($k != $row[3]){
                        $content .="<tr>\r\n<th class='timetable_sub' id='".$row[9].$row[7]."' /></th>\r\n<th class='timetable'>" .$row[3]."</th></tr>";
                        $content_navi .="<tr>\r\n<th class='timetable_test' id='".$row[9].$row[7]."' /></th>\r\n<th class='timetable'>" .$row[3]."</th></tr>";                            
                    } 
                }
                $i++;  
                
				$time = $row[6];
				$hour = $row[7];
				$date = $row[8];
                $href_class = '';     
				
				// check round status and set correct link   
                if ($row[23] == 'y' || $row[24] == 'y'){
                        // only upload the changes rounds
                        if ($row[24] == 'y'){
                            $m_statusChanged = 'y';
                        }
				        if ($row[1] == $cfgRoundStatus['results_done'] || $row[1] == $cfgRoundStatus['results_in_progress'] ) {  	
                                if ($row[1] == $cfgRoundStatus['results_done']){
                                      $href_class = "timetable_heat_done";   
                                }
                                else {
                                     $href_class = "timetable_heat_process";   
                                }			
						       
                                $arr_link[$l] = $row[0];  
                                $arr_link_evt[$l] = $row[10];    
                                $l++;    
					        } 
                        elseif  ($row[1] == $cfgRoundStatus['heats_done'] ) {  
                                $href_class = "timetable_sub";   
                                $arr_link_start[$s] = $row[0];                          
                                $arr_cat[$s] = $row[3]; 
                                $arr_disc[$s] = $row[21];
                               
                                if ($row[2] == '0'){                    // Typ: (ohne)
                                    $arr_rtyp[$s] = '';
                                }
                                else {
                                     $arr_rtyp[$s] = $row[22]; 
                                }
                                $arr_event[$s] = $row[10];  
                                $s++;    
                        }                             
                }                 
                
				// next event is in a different category: go to next cell
				if($k != $row[3])
				{  
					if($k != 0) {     // not first category   
                        $content .= "</td>\r\n";
                        $content_navi .= "</td>\r\n";  
					}  
					$k = $row[3];		// keep current category
                    
					if(array_key_exists($row[10], $events) == TRUE 		// not first round (count qualified athletes)
						&& $combined == false && $teamsm == false) 	// no combined event
					{    
						$starts = "-";
						// get number of athletes/relays with valid result
                        if ($row[2] == 'S' || $row[2] == 'O'){                // round typ: S = Serie ,  O = ohne
						    $sql = "
							    SELECT
								    COUNT(*)
							    FROM
								    athletica.serienstart AS ss
								    LEFT JOIN athletica.serie AS s ON (s.xSerie = ss.xSerie)
							    WHERE 
							         s.xRunde =" . $events[$row[10]];
						
                        }
                        else {
                            $sql="SELECT
                                COUNT(*)
                            FROM
                                athletica.serienstart AS ss
                                LEFT JOIN athletica.serie AS s ON (s.xSerie = ss.xSerie)
                            WHERE ss.Qualifikation > 0   
                                AND s.xRunde =" . $events[$row[10]];
                        }
                        $result=mysql_query($sql);
                           
						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
						else {
							$start_row = mysql_fetch_row($result);
							$starts = $start_row[0];
							mysql_free_result($result);
						}
					}elseif($combined || $teamsm){ // for combined rounds, count starts for correct group
						 
						if($row[17] == 1){ // if this is a combined last event, every athlete starts
							$starts = $row[5];
						}elseif(empty($row[15])){ // if no group is set
							$starts = $row[5];
						}else{      
							$result = mysql_query("SELECT COUNT(*) FROM
											athletica.start as st
											LEFT JOIN athletica.anmeldung as a ON (st.xAnmeldung = a.xAnmeldung)
										WHERE	
											st.xWettkampf = $row[10]
										    AND	a.Gruppe = '$row[15]'");  
									
							if(mysql_errno() > 0) {	 
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}else{
								$start_row = mysql_fetch_array($result);
								$starts = $start_row[0];
								mysql_free_result($result);
							}
							$combGroup = "&nbsp;g".$row[15];
						}
						
					}elseif($roundSet > 0){
						   
						if($roundSetMain == 0){
							$starts = "m";
						}else{
							  
							$result = mysql_query("SELECT COUNT(*) FROM
											athletica.rundenset AS rs
											LEFT JOIN athletica.runde AS r ON (r.xRunde = rs.xRunde)
											LEFT JOIN athletica.wettkampf AS w ON (w.xWettkampf = r.xWettkampf)
											LEFT JOIN athletica.disziplin_" . $_COOKIE['language'] . " AS d ON (d.xDisziplin = w.xDisziplin)
											LEFT JOIN athletica.start AS s
												ON w.xWettkampf = s.xWettkampf
												AND s.Anwesend = 0
												AND ((d.Staffellaeufer = 0
													AND s.xAnmeldung > 0)
													OR (d.Staffellaeufer > 0
													AND s.xStaffel > 0))
										WHERE
											rs.xRundenset = $roundSet 										
                                            AND s.xWettkampf > 0 
                                            AND rs.xMeeting = " . $_COOKIE['meeting_id'] ."  
                                            AND w.xMeeting = " . $_COOKIE['meeting_id'] ."                                         
										"); 
                                                                                                             
							if(mysql_errno() > 0) {		// DB error
                            
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}else{ 
								$start_row = mysql_fetch_array($result);
								$starts = $start_row[0];
								mysql_free_result($result);
							}
						} 
					}else
					{
						$starts = $row[5];
					}  
    
                    if (!empty($href_class)){   
                        $link = "live".$row[0].".html";  
                        $content .="<td ><a class='$href_class' href='". $link . "'>&nbsp;" .$row[4] . "&nbsp;" . $row[2].$combGroup ."&nbsp;" .$row[13] ."</a>\r\n";                         
                        $content_navi .="<td ><a class='$href_class' href='". $link . "'>&nbsp;" .$row[4] . "&nbsp;" . $row[2].$combGroup ."&nbsp;" .$row[13] ."</a>\r\n"; 
                       
                    }
                    else {           
                        $content .="<td>\r\n";   
                        $content_navi .="<td>\r\n";
                    }  
				}  
				// next event has same category: linebreak within cell
				else		// same category
				{                        
					if(array_key_exists($row[10], $events) == TRUE 		// not first round (count qualified athletes)
						&& $combined == false && $teamsm == false) 	// no combined event, no team sm event
					{
						$starts = "-";
						// get number of athletes/relays with valid result
						$result = mysql_query("
							SELECT
								COUNT(*)
							FROM
								athletica.serienstart AS ss
								LEFT JOIN athletica.serie AS s ON (s.xSerie = ss.xSerie  )
							WHERE ss.Qualifikation > 0  							
							    AND s.xRunde =" . $events[$row[10]]
						);

						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
						else {
							$start_row = mysql_fetch_row($result);
							$starts = $start_row[0];
							mysql_free_result($result);
						}
					}elseif($combined || $teamsm){ // for combined rounds, count starts for correct group
						
						if($row[17] == 1){ // if this is a combined last event, every athlete starts
							$starts = $row[5];
						}elseif(empty($row[15])){ // if no group is set
							$starts = $row[5];
						}else{
							$result = mysql_query("SELECT COUNT(*) FROM   
											athletica.start as st
											LEFT JOIN athletica.anmeldung as a ON (st.xAnmeldung = a.xAnmeldung)
										WHERE	
											st.xWettkampf = $row[10]
										    AND	a.Gruppe = '$row[15]'");
							if(mysql_errno() > 0) {		// DB error
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}else{
								$start_row = mysql_fetch_array($result);
								$starts = $start_row[0];
								mysql_free_result($result);
							}
							$combGroup = "&nbsp;g".$row[15];
						}
						
					}elseif($roundSet > 0){
						
						if($roundSetMain == 0){
							$starts = "m";   
						}else{
							
							$result =mysql_query("SELECT COUNT(*) FROM
                                            athletica.rundenset AS rs
                                            LEFT JOIN athletica.runde AS r ON (r.xRunde = rs.xRunde)
                                            LEFT JOIN athletica.wettkampf AS w ON ( w.xWettkampf = r.xWettkampf)
                                            LEFT JOIN athletica.disziplin_" . $_COOKIE['language'] . " AS d ON ( d.xDisziplin = w.xDisziplin )
                                            LEFT JOIN athletica.start AS s
                                                ON w.xWettkampf = s.xWettkampf
                                                AND s.Anwesend = 0
                                                AND ((d.Staffellaeufer = 0
                                                    AND s.xAnmeldung > 0)
                                                    OR (d.Staffellaeufer > 0
                                                    AND s.xStaffel > 0))
                                        WHERE
                                            rs.xRundenset = $roundSet                                         
                                            AND s.xWettkampf > 0 
                                            AND rs.xMeeting = " . $_COOKIE['meeting_id'] ."  
                                            AND w.xMeeting = " . $_COOKIE['meeting_id'] ."   
                                        "); 
							if(mysql_errno() > 0) {		// DB error
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}else{
								$start_row = mysql_fetch_array($result);  
								$starts = $start_row[0];
								mysql_free_result($result);
							}
						}  
					}
					else {   
						$starts = $row[5];
					}
					
                    if (!empty($href_class)){    
                       
                        $link = "live".$row[0].".html"; 
                        $content .= "<div class='" . $class ."'>\r\n<a  class='$href_class'  href='". $link ."'>&nbsp;". $row[4] ."&nbsp;" . $row[2].$combGroup."&nbsp;&nbsp;".$row[13] ."</a>\r\n";
                        $content_navi .= "<div class='" . $class ."'>\r\n<a  class='$href_class'  href='". $link ."'>&nbsp;". $row[4] ."&nbsp;" . $row[2].$combGroup."&nbsp;&nbsp;".$row[13] ."</a>\r\n";                                                                    
				    }
                    else {
                        if ($time != $row[6]){
                           $content .="<td>\r\n";   
                           $content_navi .="<td>\r\n";
                        } 
                    }
                 }
               
				$events[$row[10]] = $row[0]; // keep last processed round per event
			}	// END while every event round
			mysql_free_result($res);      
			
		}		// ET DB error event rounds    
		
	}		// ET DB timetable item error     
   
    
    $formaction = 'view';
    $break = 'none';
    $biglist = true;
    $sepu23 = false;
    $show_efforts = 'none';
    $disc_nr = 99;
    $athleteCat = false; 
    $heatSeparate = false; 
    $date = '%';
    $catFrom = 0;
    $catTo = 0;
    $discFrom = 0;
    $discto = 0;
    $heatFrom = 0;
    $heatTo = 0;  
    $cover = FALSE;
    $cover_timing = false;

    $content .= "</tr>\r\n</table>\r\n";  
    $content_navi .= "</tr>\r\n</table>\r\n";   
    
      
    // create rankinglist for every round from array link 
    foreach ($arr_link as $key => $round){
         $event = $arr_link_evt[$key];
        
        // Ranking list single event and all attempts  
        AA_rankinglist_Single(0, $event, $round, $formaction, $break, $cover, $biglist, $cover_timing, $date, $show_efforts,$heatSeparate,$catFrom,$catTo,$discFrom, $discTo,$heatFrom,$heatTo,$athleteCat, $content_navi);                                                                                                                                                                                                         
    }
    
     foreach ($arr_link_start as $key => $round){
        
            // start list   
            $layout = AA_getDisciplineType($round);    // type determines layout

            // track disciplines, with or without wind
            if(($layout == $cfgDisciplineType[$strDiscTypeNone])
                    || ($layout == $cfgDisciplineType[$strDiscTypeTrack])
                    || ($layout == $cfgDisciplineType[$strDiscTypeTrackNoWind])
                    || ($layout == $cfgDisciplineType[$strDiscTypeDistance])
                    || ($layout == $cfgDisciplineType[$strDiscTypeRelay]))
                    {  
                   
                    AA_results_Track($round, $layout, $content_navi, $arr_cat[$key], $arr_disc[$key] ,$arr_rtyp[$key], $arr_event[$key]);
            }
            // technical disciplines, with or withour wind
            else if(($layout == $cfgDisciplineType[$strDiscTypeJump])
                    || ($layout == $cfgDisciplineType[$strDiscTypeJumpNoWind])
                    || ($layout == $cfgDisciplineType[$strDiscTypeThrow]))
            {
                    AA_results_Tech($round, $layout, $content_navi, $arr_cat[$key], $arr_disc[$key] ,$arr_rtyp[$key]);      
                    
            }
            // high jump, pole vault
            else if($layout == $cfgDisciplineType[$strDiscTypeHigh])
                {
                AA_results_High($round, $layout, $singleRound, $content_navi, $arr_cat[$key], $arr_disc[$key] ,$arr_rtyp[$key]); 
            }
    }
    
    $content .= "</div></div>\r\n<div id='content'></div>\r\n";
    $content .= $GLOBALS['cfgHtmlEnd'];  
  
    
    if (!fwrite($fp, $content)) {
        AA_printErrorMsg($GLOBALS['strErrFileWriteFailed']);    
        return;
    }   
    fclose($fp);  
    
   
    // get ftp data
   $result = mysql_query("
            SELECT
                *
            FROM
                athletica_liveResultate.config");
        if(mysql_errno() > 0) {
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
            
        }
        else {
            $row = mysql_fetch_row($result);
            $host = $row[1];
            $user = $row[2];    
            $pwd = $row[3];    
            $url = $row[4];   
            mysql_free_result($result);
        } 
   
   
    
     $ftp = new FTP_data();
    // send files per ftp
    $local = dirname($_SERVER['SCRIPT_FILENAME'])."/tmp/index.html";
    if (empty($GLOBALS['cfgDir'] )){
         $remote = "index.html";       
    }
    else {
         $remote = $GLOBALS['cfgDir'] . "/index.html";       
    }
       
   
    // upload result file  
   
    $ftp->open_connection($host, $user, $pwd);
    if ($m_statusChanged == 'y') {
        $success = $ftp->put_file($local, $remote);
        if(!$success){
             AA_printErrorMsg($strErrFtpNoPut); 
        }
    }     
    
    foreach ($arr_link as $key => $round){ 
        $local = dirname($_SERVER['SCRIPT_FILENAME'])."/tmp/live".$round.".html";       
         if (empty($GLOBALS['cfgDir'] )){
             $remote =  "live".$round.".html";          
         }
         else {
             $remote =  $GLOBALS['cfgDir'] . "/live".$round.".html";          
         }            
         $success = $ftp->put_file($local, $remote); 
    } 
    
    foreach ($arr_link_start as $key => $round){ 
        $local = dirname($_SERVER['SCRIPT_FILENAME'])."/tmp/live".$round.".html";   
        if (empty($GLOBALS['cfgDir'] )){ 
            $remote =  "live".$round.".html";            
        }
        else {
            $remote =  $GLOBALS['cfgDir'] . "/live".$round.".html";           
        }         
         $success = $ftp->put_file($local, $remote);          
    } 
    $ftp->close_connection();          
                               
   
}        // End AA_timetable_display

}		// AA_TIMETABLE_LIB_INCLUDED
?>


 
