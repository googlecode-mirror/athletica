<?php

/**
 * Utilities
 * ---------
 *
 * This library contains utility functions that don't write any
 * output. Error messages are stored in the global field AA_ERROR
 * and must be evaluated by the client.
 */


if (!defined('AA_UTILS_LIB_INCLUDED'))
{
	define('AA_UTILS_LIB_INCLUDED', 1);

	require('./lib/common.lib.php');
	require('./convtables.inc.php');

/*
 * ------------------------------------------------------
 *
 *	Processing Functions
 *	--------------------
 *	various processing functions
 *
 * ------------------------------------------------------
 */

	/**
	 * Calculate points for a given performance
	 *
	 * @param	event			ID table 'wettkampf'
	 * @param	perf			performance (in cm or 1/100sec)	
	 * @return	int			points	
	 */

	function AA_utils_calcPoints($event, $perf, $fraction = 0, $sex = 'M', $startID)
	{  
        // check if this is a merged round   (important for calculate points for merged round with different sex)
        $sql="SELECT                                           
                    se.RundeZusammen                       
                FROM
                    serienstart as se
                   
                WHERE se.xSerienstart = " .$startID;
        $res=mysql_query($sql);    
        if(mysql_errno() > 0) {        // DB error
                $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
            }
        else {   
            $row = mysql_fetch_row($res);
            
            if ($row[0] > 0) {                  // merged round exist
            
                // get event to the merged round
                $sql="SELECT
                    ru.xWettkampf                      
                FROM
                    runde as ru
                   
                WHERE ru.xRunde = " .$row[0];
                $res=mysql_query($sql);  
                if(mysql_errno() > 0) {        // DB error
                    $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                } 
                else { 
                    if (mysql_num_rows($res) > 0){   
                        $row = mysql_fetch_row($res);                         
                        $event=$row[0];   
                    } 
                }     
            }
        }
             
		global $strConvtableRankingPoints;
		
		$GLOBALS['AA_ERROR'] = '';
		
		$points = 0;
		if($perf > 0)
		{
			// get formula to calculate points from performance
			$result = mysql_query("
				SELECT
					disziplin.Typ
					, wettkampf.Punktetabelle
					, wettkampf.Punkteformel
					, disziplin.xDisziplin
				FROM
					disziplin
					, wettkampf
				WHERE wettkampf.xWettkampf = $event
				AND disziplin.xDisziplin = wettkampf.xDisziplin 
				AND wettkampf.Punktetabelle > 0
				AND (wettkampf.Punkteformel != '0'
				 OR (wettkampf.Punkteformel = '0' 
				AND wettkampf.Punktetabelle >= 100))
			");
			
			if(mysql_errno() > 0) {		// DB error
				$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
			}
			else if (mysql_num_rows($result) > 0)	// event has formula assigned
			{
				
				$row = mysql_fetch_row($result);
				
				// if ranking points are set, return
				if($row[1] == $GLOBALS['cvtTable'][$strConvtableRankingPoints]){
					return 0;
				}

				// track disciplines: performance in 1/100 sec
				if($row[1]<100 && ($row[0] == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeTrack']]
								  || $row[0] == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeTrackNoWind']]
								  || $row[0] == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeRelay']]
								  || $row[0] == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeDistance']]))
				{
					$perf = ceil($perf/10);
				}
				
				// own score table
				$test = 0;
				if($row[1] >= 100){
					
					$operator = '>=';
					$sort = 'ASC';
					if(($row[0] == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeTrack']]
					  || $row[0] == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeTrackNoWind']]
					  || $row[0] == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeRelay']]
					  || $row[0] == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeDistance']]))
						{
						$operator = '>=';
						$sort = 'ASC';
					}
					else {
						$operator = '<=';
						$sort = 'DESC';
					}
					 
					$sqlpt = "SELECT Punkte 
								FROM wertungstabelle_punkte 
							   WHERE xWertungstabelle = ".$row[1]." 
								 AND xDisziplin = ".$row[3]." 
								 AND Leistung ".$operator." ".$perf." 
								 AND Geschlecht = '".$sex."'
							ORDER BY Leistung ".$sort." 
							   LIMIT 1;";
					$querypt = mysql_query($sqlpt);
					
					$datei = fopen('test.txt', 'w+');
					fwrite($datei, $sqlpt);
					fclose($datei);
					
					if($querypt && mysql_num_rows($querypt)){
						$points = mysql_result($querypt, 0, 'Punkte');
					}
					
					$testdatei = fopen('test.txt', 'w+');
					fwrite($testdatei, $sqlpt);
					fclose($testdatei);					
				} else {	
					// split formula into parameters
					$params = explode(" ", $GLOBALS['cvtFormulas'][$row[1]][$row[2]]);
					// formula types
					$A = $params[1];
					$B = $params[2];
					$C = $params[3];
					
					switch ($params[0]) {
						// points = A * ((B - perf) / 100) ^ C, fractions are rounded down
						case 1:
							$points = floor($A * (pow(($B-$perf)/100, (float)$C)));	
							break;
						// points = A * ((perf - B) / 100) ^ C, fractions are rounded down
						case 2:
							$points = floor($A * (pow(($perf-$B)/100, (float)$C)));	
							break;
						// points = A * (perf - B) ^ C, fractions are rounded down
						case 3:
							$points = floor($A * (pow($perf-$B, (float)$C)));
							break;
						// points = A * ((B - perf/100)^2) + C, fractions rounded down
						// (unused)
						case 4:
							$points = floor( $A * pow($B - ($perf/100), 2) - $C);
							break;
						// points = A * (perf/100 + B)^2 - C, fractions rounded down
						case 5:
							$points = floor( $A * pow(($perf/100)+$B, 2) - $C);
							break;
						default:
							$GLOBALS['AA_ERROR'] = "System Error: Invalid conversion formula (convtables.inc.php)";
					}		// end switch params[]
				}
				
				mysql_free_result($result);
			}		// ET event with formula
		}		// ET performance provided
		
		if(is_nan($points) || $points<0){ // prevent wrong or negative points for "to bad" performances
			$points = 0;
		}
		
		return $points;
	}
	
	/**
	 * Calculate ranking points for a given round
	 *
	 * @param	round			ID table 'runde'
	 */
	 
	function AA_utils_calcRankingPoints($round){    
		global $strConvtableRankingPoints, $cfgEventType;
		global $strEventTypeSVMNL, $strEventTypeSingleCombined, $strEventTypeClubAdvanced
			, $strEventTypeClubBasic, $strEventTypeClubTeam, $strEventTypeClubMixedTeam;
		
		$valid = false;
		$minus=true; 
		//
		// initialize parameters
		//
		$pStart = 0;
		$pStep = 0;
		$bSVM = false; // set if contest type has a result limitation for only best athletes
				// e.g.: for svm NL only the 2 best athletes of a team are counting -> distribute points on these athletes
		$countMaxRes = 0; // set to the maximum of countet results in case of an svm contest
		      
		$relay = AA_checkRelay('',$round);  
		
		$res = mysql_query("
			SELECT
				w.Punktetabelle
				, w.Punkteformel
				, w.Typ
			FROM
				runde as r
				, wettkampf as w
			WHERE
				r.xWettkampf = w.xWettkampf
			AND	r.xRunde = $round
		");
		
		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			
			$row = mysql_fetch_array($res);
			mysql_free_result($res);
			
			$rpt = $GLOBALS['cvtTable'][$strConvtableRankingPoints];  		
			if($row[0] == $rpt){
			   
				// if mode is team
				if($row[2] > $cfgEventType[$strEventTypeSingleCombined]){
					$bSVM = true;    				
					switch($row[2]){
						case $cfgEventType[$strEventTypeSVMNL]:   
							if ($relay)
								$countMaxRes = 1; 
							else
								$countMaxRes = 2;
							break;
						case $cfgEventType[$strEventTypeClubBasic]:
							$countMaxRes = 1;
							break;
						case $cfgEventType[$strEventTypeClubAdvanced]:
							$countMaxRes = 2;
							break;
						case $cfgEventType[$strEventTypeClubTeam]:
							$countMaxRes = 5;
							break;
						case $cfgEventType[$strEventTypeClubMixedTeam]:
							$countMaxRes = 6;
							break;
						default:
							$countMaxRes = 1;
					}
				}
			  
				//list($pStart, $pStep) = explode(" ", $GLOBALS['cvtFormulas'][$rpt][$row[1]]);
				list($pStart, $pStep) = explode(" ", $row[1]);
                if (strpos($row[1], '-') ){ 
				    $pStep = str_replace('-', '', $pStep);
                    $minus=true;
                }
                else {
                     $pStep = str_replace('+', '', $pStep);
                    $minus=false;
                }
				$valid = true;
				
			}
			
		}
		
		//
		// calculate points
		//
		if($valid){   
		   
			// if svm, the ranking points have only to be distributed on the results that count afterwards for team
			// so: only the best 2 athletes of the same team will get points    
			
			if(!$bSVM){ 				
				$res = mysql_query("
					SELECT
						serienstart.xSerienstart
						, serienstart.Rang
					FROM
						serienstart
						, serie
					WHERE serienstart.xSerie = serie.xSerie
					AND serie.xRunde = $round
					AND serienstart.Rang > 0
					ORDER BY serienstart.Rang ASC
				");
		 }else{     		  
				 	$res = mysql_query("
						SELECT 
							serienstart.xSerienstart
							, serienstart.Rang
							, IF(anmeldung.xTeam > 0, anmeldung.xTeam, staffel.xTeam)
						FROM
							serienstart
							, serie
							, start
							LEFT JOIN staffel ON start.xStaffel = staffel.xStaffel
							LEFT JOIN anmeldung ON start.xAnmeldung = anmeldung.xAnmeldung
						WHERE serienstart.xSerie = serie.xSerie
						AND serienstart.xStart = start.xStart
						AND serie.xRunde = $round
						AND serienstart.Rang > 0
						ORDER BY serienstart.Rang ASC
						");   				   
			}     
			 
			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}else{
				
				$pts = 0;	// points to share
				$rank = 1;	// current rank
				$update = array();	// holding serienstart[key] with points
				$tmp = array();	// holding temporary serienstarts
				$point = $pStart;	// current points to set
				$i = 0;	// share counter
				
				$cClubs = array(); // count athlete teams for svm mode
				
				while($row = mysql_fetch_array($res)){
					
					if($bSVM){
						// count athletes per club
						if(isset($cClubs[$row[2]])){
							$cClubs[$row[2]]++;
						}else{
							$cClubs[$row[2]] = 1;
						}  
					            
						// skip result if more than MaxRes athletes of a team are on top
						if(isset($cClubs[$row[2]]) && $cClubs[$row[2]] > $countMaxRes){   
							          						
							mysql_query("UPDATE resultat SET
									Punkte = 0
								WHERE
									xSerienstart = $row[0]");
							if(mysql_errno() > 0) {
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}
							continue; // skip
						}
					}       
					
						if($rank != $row[1] && $i > 0){
							
							$p = $pts / $i; // divide points for athletes with the same rank
							$p = round($p, 1);
							foreach($tmp as $x){
								$update[$x] = $p;
							}
							$i = 1;
							$pts = $point;
							$rank = $row[1];
							$tmp = array(); 
							    						
						}else{ 
						 							
							$i++;
							$pts += $point; 
							   							
						}
						
						$tmp[] = $row[0];
                        
						if ($minus){
						    $point -= $pStep;
                        }
                        else {
                            $point += $pStep; 
                        }    
				}
				
				// check on last entries
				if($i > 0){
					
					$p = $pts / $i; // divide points for athletes with the same rank
					$p = round($p, 1);
					foreach($tmp as $x){
						$update[$x] = $p;
					}
					
				}
				
				// update points
				foreach($update as $key => $p){     
					mysql_query("UPDATE resultat SET
							Punkte = $p
						WHERE
							xSerienstart = $key");
					if(mysql_errno() > 0) {
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
					
				}
				
			}
		} // endif $valid
	  	
	}
	
	/**
	 * Establish DB connection
	 *
	 */
	function AA_utils_connectToDB()
	{
		$GLOBALS['AA_ERROR'] = '';

		$db = mysql_pconnect( $GLOBALS['cfgDBhost'].':'.$GLOBALS['cfgDBport'], $GLOBALS['cfgDBuser'], $GLOBALS['cfgDBpass']);
		if ($db == FALSE)
		{
			$GLOBALS['AA_ERROR'] = $GLOBALS['strNoDBConnx'];
		}
		else
		{
			if(!mysql_select_db($GLOBALS['cfgDBname'], $db))
			{
				$GLOBALS['AA_ERROR'] = $GLOBALS['strDBnotfound'];
			}
		}
		return $db;
	}


/*
 * ------------------------------------------------------
 *
 *	Data functions
 *	--------------------
 *	various functions to retrieve or check data from DB
 *
 * ------------------------------------------------------
 */


	/**
	 * Check data reference
	 *
	 * @param		string	table		name of table to be checked
	 * @param		string	unique	name of unique key column
	 * @param		int		id			uniqe key of item to be checked (xTablename)
	 *
	 * @return		int		nbr of rows found
	 */
	function AA_utils_checkReference($table, $unique, $id)
	{  
		$GLOBALS['AA_ERROR'] = '';

		$rows = 0;
		$result = mysql_query("
			SELECT
				$unique
			FROM
				$table
			WHERE
				$unique = $id
		");
        
		if(mysql_errno() > 0) {		// DB error
			$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();  
		}
		else {
			$rows = mysql_num_rows($result);
			mysql_free_result($result);
		}
		return $rows;
	}


	/**
	 * change round status
	 * -------------------
	*/
	function AA_utils_changeRoundStatus($round, $status)
	{   
		require ('./lib/cl_round.lib.php');

		$GLOBALS['AA_ERROR'] = '';   
		
		$mergedRounds=AA_getMergedRounds($round);
  
		if ($mergedRounds!='') {           
			$sqlRounds="IN ". $mergedRounds;   
			$arrRound=split('[,]', substr($mergedRounds,1,-1));
			foreach ($arrRound as $round){ 
			  $rnd = new Round($round);
			  $rnd->setStatus($status); 
			} 
		}
		else {  
			  $rnd = new Round($round);
			  $rnd->setStatus($status);
		}  
	}


	/**
	 * Get first category ID from DB
	 *
	 * @return	Category ID (primary key)
	 */
	function AA_utils_getFirstCategoryID()
	{
		$GLOBALS['AA_ERROR'] = '';

		$result = mysql_query("
			SELECT
				DISTINCT w.xKategorie
			FROM
				wettkampf AS w
				, kategorie AS k
			WHERE
				w.xMeeting=" . $_COOKIE['meeting_id'] . "
				AND w.xKategorie = k.xKategorie
			ORDER BY
				k.Anzeige
		");

		if(mysql_errno() > 0) {
			$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
			$category = 0;		// set category to zero
		}
		else {
			$row = mysql_fetch_row($result);
			$category = $row[0];			// preselect default category
			mysql_free_result($result);
		}
		return $category;
	}



	/**
	 * get round status
	 * ----------------
	*/
	function AA_utils_getRoundStatus($round)
	{
		$status = 0;
		$res = mysql_query("
			SELECT
				Status
			FROM
				runde
			WHERE xRunde = $round
		");

		if(mysql_errno() > 0)		// DB error
		{
			$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
		}
		else
		{
			$row = mysql_fetch_row($res);
			$status = $row[0];
			mysql_free_result($res);
		}		// ET DB error    
		return $status;
	}





	/**
	 * log round event
	 * ---------------
	*/
	function AA_utils_logRoundEvent($round, $txt)
	{
		$GLOBALS['AA_ERROR'] = '';

		mysql_query("LOCK TABLES rundelog WRITE");

		mysql_query("
			INSERT INTO rundenlog SET
				Zeit = NOW()
				, Ereignis = '$txt'
				, xRunde = $round
		");

		if(mysql_errno() > 0) {
			$GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
		}
		mysql_query("UNLOCK TABLES");
	}
    
    
    
    
     
 /**
     * save remark
     * ---------------
    */
    function AA_utils_saveRemark($startID, $remark, $xAthlete)
    {
        $GLOBALS['AA_ERROR'] = '';
        $query = '';    

        mysql_query("
            LOCK TABLES rundenset READ, runde READ, runde as r READ , serie as s READ , start as st READ, 
            wettkampf as w READ , anmeldung as a READ , athlet as at READ, verein as v READ, 
            rundentyp as rt READ, serienstart as ss READ  , serienstart WRITE
        ");

        if(!empty($startID))    // result provided -> change it
        {
            if(AA_utils_checkReference("serienstart", "xSerienstart"
                                        , $startID) == 0)
            {
                $GLOBALS['AA_ERROR'] = $GLOBALS['strErrAthleteNotInHeat'];
            }
            else
            {
                $query="SELECT 
                        w.mehrkampfcode , ss.Bemerkung
                    FROM
                        serienstart as ss
                        LEFT JOIN start as st On (ss.xStart = st.xStart)
                        LEFT JOIN wettkampf as w On (w.xWettkampf = st.xWettkampf) 
                    WHERE
                        w.mehrkampfcode = 0
                        AND ss.xSerienstart = ".$startID;
                
                $result=mysql_query($query); 
                
                if(mysql_errno() > 0) {
                    $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                }
                else {
                    if (mysql_num_rows($result) > 0) {
                   
                         $sql = "UPDATE serienstart 
                                    SET Bemerkung = '".$remark."'                                     
                                         WHERE xSerienstart = ".$startID.";";
                            mysql_query($sql);
                           
                            if(mysql_errno() > 0) {
                                $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                            }   
                   }     
                   else {
                
                        // comnined event
                
                        $query_mk="SELECT 
                                        ss.xSerienstart , ss.Bemerkung   
                                   FROM 
                                        runde AS r 
                                        LEFT JOIN serie AS s ON (s.xRunde = r.xRunde) 
                                        LEFT JOIN serienstart AS ss ON (ss.xSerie = s.xSerie)
                                        LEFT JOIN START AS st ON (st.xStart = ss.xStart) 
                                        LEFT JOIN wettkampf as w ON (w.xWettkampf = st.xWettkampf)
                                        LEFT JOIN anmeldung AS a ON (a.xAnmeldung = st.xAnmeldung)
                                        LEFT JOIN athlet AS at ON (at.xAthlet = a.xAthlet)
                                        LEFT JOIN verein AS v ON (v.xVerein = at.xVerein   )
                                        LEFT JOIN rundentyp AS rt ON rt.xRundentyp = r.xRundentyp                              
                                   WHERE w.mehrkampfcode > 0
                                        AND at.xAthlet = ". $xAthlete;                           
                           
                        $result=mysql_query($query_mk); 
                
                        if(mysql_errno() > 0) {
                            $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                        }
                        else {
                            while ($row=mysql_fetch_row($result)){
                                    $sql = "UPDATE serienstart 
                                            SET Bemerkung = '".$remark."'                                     
                                            WHERE xSerienstart = ".$row[0].";";
                                    mysql_query($sql);
                           
                                    if(mysql_errno() > 0) {
                                        $GLOBALS['AA_ERROR'] = mysql_errno() . ": " . mysql_error();
                                    }  
                           }
                        } 
                    }
                }
            }
        }
        mysql_query("UNLOCK TABLES");

    }

} // end AA_UTILS_LIB_INCLUDED
?>
