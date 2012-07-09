<?php

/**********
 *
 *	high jump, pole vault results
 *	
 */

if (!defined('AA_RESULTS_HIGH_LIB_INCLUDED'))
{
	define('AA_RESULTS_HIGH_LIB_INCLUDED', 1);

function AA_results_High($round, $layout, $singleRound, $content_navi, $cat, $disc, $rtyp)  
{  
    
require('./config.inc.php');
require('./config.inc.end.php');   

require('./lib/common.lib.php');
require('./lib/heats.lib.php');    
require('./lib/utils.lib.php');        

$p = "./tmp";
$fp = @fopen($p."/live".$round.".html",'w');
if(!$fp){
    AA_printErrorMsg($GLOBALS['strErrFileOpenFailed']);  
    return;
}    

$svm = AA_checkSVM(0, $round); // decide whether to show club or team name

if ($singleRound > 0){    
    $single_svm = AA_checkSVM(0, $singleRound); // decide whether to show club or team name      
}     

$mergedMain=AA_checkMainRound($round);
if ($mergedMain == 1) {
    $round = AA_getMainRound($round); 
}  

// get url
   $url = '';
   $result = mysql_query("
            SELECT
                url
            FROM
                athletica_liveResultate.config");
        if(mysql_errno() > 0) {
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
            
        }
        else {
            $row = mysql_fetch_row($result);            
            $url = $row[0]; 
        } 

// read round data
if($round > 0)
{
	$status = AA_getRoundStatus($round);
    
     $content = $cfgHtmlStart1;   
     if  (empty($GLOBALS['cfgDir']) ){
        $content .= "<meta http-equiv='refresh' content='" . $GLOBALS['cfgMonitorReload'] . "; url=http://" . $url  ."/live".$round .".html'>"; 
     }
     else {
          $content .= "<meta http-equiv='refresh' content='" . $GLOBALS['cfgMonitorReload'] . "; url=http://" . $url . "/" . $GLOBALS['cfgDir'] ."/live".$round .".html'>"; 
         
     }         
     $content .= $cfgHtmlStart2;   
     $content .= $content_navi; 
     $content .= "</div ><div id='content_pc'><div id='content_pda'>";   
     
     $content .= "<h1>$strStartlist " . $_COOKIE['meeting'] ."</h1>";              // title          
     if (!empty($rtyp)){
            $content .= "<h2>$cat $disc, $rtyp</h2>";    
     } 
     else {
            $content .= "<h2>$cat $disc</h2>";    
     }  
     $content .= "<table class='dialog'>";      

	// display all athletes
	$result = mysql_query("
			SELECT rt.Name
				, rt.Typ
				, s.xSerie
				, s.Bezeichnung
				, ss.xSerienstart
				, ss.Position
				, ss.Rang
				, a.Startnummer
				, at.Name
				, at.Vorname
				, at.Jahrgang
				, if('".$svm."', t.Name, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo))   
				, LPAD(s.Bezeichnung,5,'0') as heatid
				, rs.xResultat
				, rs.Leistung
				, rs.Info
				, at.Land
                , ss.Bemerkung
                , at.xAthlet
                , r.xRunde 
                , ss.RundeZusammen  
			FROM
				athletica.runde AS r
				LEFT JOIN athletica.serie AS s ON (s.xRunde = r.xRunde )
				LEFT JOIN athletica.serienstart AS ss ON (ss.xSerie = s.xSerie)
				LEFT JOIN athletica.start AS st ON (st.xStart = ss.xStart)
				LEFT JOIN athletica.anmeldung AS a ON (a.xAnmeldung = st.xAnmeldung)
				LEFT JOIN athletica.athlet AS at ON (at.xAthlet = a.xAthlet)
				LEFT JOIN athletica.verein AS v ON (v.xVerein = at.xVerein)
            LEFT JOIN athletica.team AS t ON(a.xTeam = t.xTeam) 
			LEFT JOIN athletica.rundentyp_" . $_COOKIE['language'] . " AS rt
				ON rt.xRundentyp = r.xRundentyp
			LEFT JOIN athletica.resultat AS rs
				ON rs.xSerienstart = ss.xSerienstart
			WHERE r.xRunde = $round  
			ORDER BY
				heatid
				, ss.Position
				, rs.xResultat DESC
		");
       
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{       			
			// initialize variables
			$a = 0;
			$h = 0;
			$c_results = 0;
			$rowclass = 'odd';     
		
			while($row = mysql_fetch_row($result))
			{   if ($row[20] > 0){
                    $singleRound = $row[20];
                }
                else {
                      $singleRound = $row[19];  
                }   				
/*
 *  Heat headerline
 */   
				if($h != $row[2])		// new heat
				{
					$h = $row[2];				// keep heat ID
					if(is_null($row[0])) {		// only one round

						$title = "$strFinalround";
					}
					 else {        // more than one round
                        if ($row[1] == '0') {
                            $title = ""; 
                        }
                        else {
                            $title = "$row[0]"; 
                        }                          
                    }   					
                    $content .="</table><table class='dialog'>"; 
                    
                    if ($title != '') {   
	                    $content .= "<tr>";  
		                $content .= "<th class='dialog' colspan='6' />" . $title . " " .$row[3]  ."</th> ";  
	                    $content .= "</tr>";  
                    }  
	                $content .= "<tr>";    
		            $content .= "<th class='dialog'>" . $strPositionShort ."</th>";
                    $content .= "<th class='dialog'>" . $strStartnumber ."</th>"; 
		            $content .= "<th class='dialog'>" . $strAthlete ."</th>";
		            $content .= "<th class='dialog_pc'>" . $strYearShort ."</th>";
		            $content .= "<th class='dialog_pc'>" . $strCountry ."</th>";
		            $content .= "<th class='dialog_pc'>";
                    if($svm){
                        $content .= $strTeam; 
                    }else{ 
                        $content .= $strClub;
                    } 
                    $content .= "</th>";                         
                    $content .= "<th class='dialog'>" . $strPerformance ."</th>";    
	                $content .= "</tr>";    

				}		// ET new heat

/*
 * Athlete data lines
 */  
				if($a != $row[4])		// new athlete
				{   $c_results = 0;
					$a = $row[4];		// keep athlete ID
					
					$l = 0;				// reset result counter
					
					if($row[5] % 2 == 0) {		// even row numer
						$rowclass='even';
					}
					else {							// odd row number
						$rowclass='odd';
					}   

	               $content .= "<tr class='" . $rowclass . "' >";    
		           $content .= "<td class='forms_right'>" . $row[5] ."</td>";
		           $content .= "<td class='forms_right'>" . $row[7] ."</td>";                            /* start nbr */
		           $content .= "<td nowrap>" . $row[8] . " " . $row[9] ."</td>";                        /* name */
		           $content .= "<td class='forms_ctr_pc'>" . AA_formatYearOfBirth($row[10]) ."</td>";
		           $content .= "<td class='forms_pc'>";
                   if ($row[16]!='' && $row[16]!='-') {
                        $content .= $row[16];
                    }
                    else {
                        $content .= " "; 
                    }
                    $content .= "</td>";
		            $content .= "<td class='forms_pc' nowrap>" . $row[11] ."</td>";                                         /* club */  
                  
                    
				}		// ET new athlete
                
                  $perf = '';  
                  if (is_null($row[14]) == false) // result entered
                        {$c_results++;
                         if ($c_results > 4) {
                             $content .= "<tr><td colspan='6'></td>"; 
                             $c_results = 0;                          
                         }
                         $perf = $row[14];
                         if ($perf== $cfgMissedAttempt['dbx']){
                             $perf = $cfgMissedAttempt['codeX'];
                         }
                        $content .= "<td colspan='2' nowrap>" . AA_formatResultMeter($perf) . "($row[15])</td>";
                   }
                                                                 
			}    
			
			mysql_free_result($result);   
               
            $content .= "</table>"; 
            $content .= $cfgHtmlFooter;  
            $content .= "</div ></div>";   
            $content .= $cfgHtmlEnd;  

            if (!fwrite($fp, $content)) {
                AA_printErrorMsg($GLOBALS['strErrFileWriteFailed']);    
                return;
            }  

            fclose($fp);    
            
		}		// ET DB error
	
}		// ET round selected

         AA_UpdateStatusChanged($round);
         
?>    

</body>
</html>

<?php      

}	// end function AA_results_High         


}	// AA_RESULTS_HIGH_LIB_INCLUDED
?>
