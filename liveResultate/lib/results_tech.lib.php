<?php

/**********
 *
 *    tech results
 *    
 */

if (!defined('AA_RESULTS_TECH_LIB_INCLUDED'))
{
    define('AA_RESULTS_TECH_LIB_INCLUDED', 1);

function AA_results_Tech($round, $layout, $content_navi, $cat, $disc, $rtyp)   
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
          $content .= "<meta http-equiv='refresh' content='" . $GLOBALS['cfgMonitorReload'] . "; url=http://" . $url ."/live".$round .".html'>";
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
         $sql="SELECT rt.Name"
                                . ", rt.Typ"
                                . ", s.xSerie"
                                . ", s.Bezeichnung"
                                . ", s.Wind"
                                . ", an.Bezeichnung"
                                . ", ss.xSerienstart"
                                . ", ss.Position"
                                . ", ss.Rang"
                                . ", a.Startnummer"
                                . ", at.Name"
                                . ", at.Vorname"
                                . ", at.Jahrgang"
                                 . ", if('".$svm."', t.Name, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo))"   
                                . ", LPAD(s.Bezeichnung,5,'0') as heatid"
                                . ", r.Versuche"
                                . ", ss.Qualifikation"
                                . ", at.Land"
                                . ", r.nurBestesResultat"
                                . ", ss.Bemerkung"
                                . ", at.xAthlet"
                                . " FROM athletica.runde AS r"
                                . " LEFT JOIN athletica.serie AS s ON (s.xRunde = r.xRunde)"
                                . " LEFT JOIN athletica.serienstart AS ss ON (ss.xSerie = s.xSerie)"
                                . " LEFT JOIN athletica.start AS st ON (st.xStart = ss.xStart)"
                                . " LEFT JOIN athletica.anmeldung AS a ON (a.xAnmeldung = st.xAnmeldung)"
                                . " LEFT JOIN athletica.athlet AS at ON (at.xAthlet = a.xAthlet)"
                                . " LEFT JOIN athletica.verein AS v ON (v.xVerein = at.xVerein)"                                   
                                . " LEFT JOIN athletica.team AS t ON(a.xTeam = t.xTeam)" 
                                . " LEFT JOIN athletica.rundentyp_" . $_COOKIE['language'] . " AS rt"
                                . " ON rt.xRundentyp = r.xRundentyp"
                                . " LEFT JOIN athletica.anlage AS an"
                                . " ON an.xAnlage = s.xAnlage"
                                . " WHERE r.xRunde = " . $round                               
                                . " ORDER BY heatid, ss.Position";
                               
        $result = mysql_query($sql);
        
        if(mysql_errno() > 0) {        // DB error            
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
        }
        else
        {
          
            // initialize variables
            $h = 0;
            $i = 0;
            $r = 0;
            $rowclass = 'odd';
            $r_rem = 0;                    
?>

<?php
         
            while($row = mysql_fetch_row($result))
            {    
               
/*               
 *  Heat headerline
 */
                if($h != $row[2])        // new heat
                {
                    $h = $row[2];                // keep heat ID

                    if(is_null($row[0])) {        // only one round
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
                    $c = 0;  
                    $content .="</table><table class='dialog'>";
                    if ($title != '') { 
                        $content .="<tr>";     
                        $content .="<th class='dialog' colspan='6'>" . $title ." " .$row[3] ."</th>"; 
                        $content .="</tr>";    
                    }   
                      
                    $content .="<tr>";    
                    $content .="<th class='dialog'>" . $strPositionShort ."</th>";
                    $content .="<th class='dialog'>" . $strStartnumber ."</th>";  
                    $content .=" <th class='dialog' >" . $strAthlete ."</th>";
                    $content .="<th class='dialog_pc'>" . $strYearShort ."</th>";
                    $content .="<th class='dialog_pc'>" . $strCountry ."</th>";
                    $content .="<th class='dialog_pc'>";
                    if($svm){ 
                        $content .= $strTeam; 
                    }else{ 
                        $content .= $strClub;
                    }
                    $content .="</th>"; 
                    
                    for($c=1; $c<=$row[15]; $c++)             // count of attempts
                       {        
                        $content .="<th class='dialog'>".$c."</th>"; 
                    }
                        
                    $content .="</tr>";    

                }        // ET new heat

/*
 * Athlete data lines
 */
                $i++;
                if($row[7] % 2 == 0) {        // even row numer
                    $rowclass='odd';
                }
                else {                            // odd row number
                    $rowclass='even';
                }

                $content .="<tr class='" . $rowclass ."'>";
                $content .="<td class='forms_right'>" . $row[7] ."</td>";                            /* position */ 
                $content .="<td class='forms_right'>" . $row[9] ."</td>";                            /* start nbr */
                $content .="<td nowrap>" . $row[10] . " " . $row[11] ."</td>";                          /* name */
                $content .="<td class='forms_ctr_pc'>" . AA_formatYearOfBirth($row[12]) ."</td>"; 
                $content .="<td class='forms_pc'>";
                if ($row[17]!='' && $row[17]!='-') {
                    $content .= $row[17];                                                                          /* club */   
                } else {
                    $content .=" ";
                }
                $content .="</td>";  
                $content .="<td class='forms_pc' nowrap>" . $row[13] ."</td>";  
                
                
                $res = mysql_query("SELECT rs.xResultat"
                    . ", rs.Leistung"
                    . ", rs.Info"
                    . " FROM athletica.resultat AS rs"
                    . " WHERE rs.xSerienstart = " . $row[6]. "
                    ORDER BY rs.xResultat");
                
                if(mysql_errno() > 0) {        // DB error
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
               
           
            
            for($c=1; $c<=$row[15]; $c++)             // count of attempts
                {        
                    $item = '';
                    $perf = '';
                    $info = '';
                    if($resrow = mysql_fetch_row($res)) {
                            $item = $resrow[0];
                            $perf = AA_formatResultMeter($resrow[1]);
                            $info = $resrow[2];
                    }
                    if ($perf < 0){
                         if ($perf == $cfgMissedAttempt['dbx']){                             
                            $perf = $cfgInvalidResult['NAA']['code'];                             
                        }
                        elseif (perf == $cfgMissedAttempt['db']){                           
                            $perf = $cfgInvalidResult['WAI']['short']; 
                       }
                        else {
                            foreach($cfgInvalidResult as $value)    // translate value
                                {   
                                if($value['code'] == $row_res[3]) {
                                    $perf = $value['short'];                                       
                                }
                            }
                        }
                    }
                    // technical disciplines with wind
                    if($layout == $cfgDisciplineType[$strDiscTypeJump])
                        {
                        $content .="<td>".$perf." (".$info .") </td>";          // info = wind
                        
                     }
                     // technical disciplines without wind
                     else
                        {
                         $content .="<td>".$perf."</td>";     
                     }

            }
                
                    
           }    // end while     
           $content .= "</table>"; 
           $content .= $cfgHtmlFooter;  
           $content .= "</div ></div>";   
           $content .= $cfgHtmlEnd;  

           if (!fwrite($fp, $content)) {
                AA_printErrorMsg($GLOBALS['strErrFileWriteFailed']);    
                return;
           }  

           fclose($fp);
           mysql_free_result($result);
        }        // ET DB error
   
}        // ET round selected    

        AA_UpdateStatusChanged($round);   

?>

</body>
</html>

<?php    

}    // End Function AA_results_Tech

}    // AA_RESULTS_TECH_LIB_INCLUDED
?>
