<?php

/**********
 *
 *	high jump, pole vault results regie
 *	
 */

if (!defined('AA_SPEAKER_RESULTS_HIGH_LIB_INCLUDED'))
{
	define('AA_SPEAKER_RESULTS_HIGH_LIB_INCLUDED', 1);

function AA_regie_High($event, $round, $layout, $cat, $disc)
{

	require('./lib/cl_gui_resulttable.lib.php');
	require('./config.inc.php');
	require('./lib/common.lib.php');
	require('./lib/results.lib.php');

	$status = AA_getRoundStatus($round);  
    
     mysql_query("
                LOCK TABLES
                    resultat READ
                    , serie READ
                    , start READ                    
                    , serienstart READ
                    , serienstart READ                   
                    , serie AS S READ
                    , start AS ST READ                      
                    , serienstart AS SS READ
                    , anmeldung AS A READ 
                    , athlet as at READ 
                    , verein as vREAD 
                    , rundentyp as rt READ   
                    , tempHigh WRITE
            ");   
    
    mysql_query("TRUNCATE TABLE tempHigh");                                 
            
         
                // if this is a combined event, rank all rounds togheter
                $roundSQL = "";
                if($combined){
                    $roundSQL = "AND serie.xRunde IN (";
                    $res_c = mysql_query("SELECT xRunde FROM runde WHERE xWettkampf = ".$presets['event']);
                    while($row_c = mysql_fetch_array($res_c)){
                        $roundSQL .= $row_c[0].",";
                    }
                    $roundSQL = substr($roundSQL,0,-1).")";
                }else{
                    $roundSQL = "AND serie.xRunde = $round";
                }
                
                // read all valid results (per athlet)
                $result = mysql_query("
                    SELECT
                        resultat.Leistung
                        , resultat.Info
                        , serienstart.xSerienstart
                        , serienstart.xSerie
                    FROM
                        resultat
                        , serienstart
                        , serie
                    WHERE resultat.xSerienstart = serienstart.xSerienstart
                    AND serienstart.xSerie = serie.xSerie
                    $roundSQL
                    AND resultat.Leistung != 0
                    ORDER BY
                        serienstart.xSerienstart
                        ,resultat.Leistung DESC
                ");
                
                if(mysql_errno() > 0)        // DB error
                {
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                else
                {
                    // initialize variables
                    $leistung = 0;        
                    $serienstart = 0;
                    $serie = 0;
                    $topX = 0;
                    $totX = 0;

                    $ss = 0;        // athlete's ID
                    $tt = FALSE;    // top result check

                    // process every result
                    while($row = mysql_fetch_row($result))
                    {  
                        // new athlete: save last athlete's data
                        if(($ss != $row[2]) && ($ss != 0))
                        {

                            if($leistung != 0)
                            {
                                // add one row per athlete to temp table
                                mysql_query("
                                    INSERT INTO tempHigh
                                    VALUES(
                                        $serienstart
                                        , $serie
                                        , $leistung
                                        , $topX
                                        , $totX
                                        , 0)
                                ");

                                if(mysql_errno() > 0) {        // DB error
                                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                                }
                            }
                            // initialize variables
                            $leistung = 0;        
                            $serienstart = 0;
                            $serie = 0;
                            $totX = 0;
                            $topX = 0;

                            $tt = FALSE;
                        }

                        // save data of current athlete's top result
                        if(($tt == FALSE) && (strstr($row[1], 'O')))
                        {
                            $leistung = $row[0];        
                            $serienstart = $row[2];
                            $serie = $row[3];
                            $topX = substr_count($row[1], 'X');                         
                            $tt = TRUE;
                        }

                        // count total invalid attempts
                        $totX = $totX + substr_count($row[1], 'X');                     
                        $ss = $row[2];                // keep athlete's ID
                    }
                    mysql_free_result($result);

                    // insert last pending data in temp table
                    if(($ss != 0) && ($leistung != 0)) {
                        mysql_query("
                            INSERT INTO tempHigh
                            VALUES(
                                $serienstart
                                , $serie
                                , $leistung
                                , $topX
                                , $totX
                                , 0)
                        ");
                          
                        if(mysql_errno() > 0) {        // DB error
                            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                        }
                    }
                }

                if($eval == $cfgEvalType[$strEvalTypeHeat]) {    // eval per heat
                    $order = "xSerie ,";
                }
                else {    // default: rank results from all heats together
                    $order = "";
                }

                // Read rows from temporary table ordered by performance,
                // nbr of invalid attempts for top performance and
                // total nbr of invalid attempts to determine ranking.
                $result = mysql_query("
                    SELECT
                        xSerienstart
                        , xSerie
                        , Leistung
                        , TopX
                        , TotalX
                    FROM
                        tempHigh
                    ORDER BY
                        $order
                        Leistung DESC
                        ,TopX ASC
                        ,TotalX ASC
                ");

                if(mysql_errno() > 0) {        // DB error
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                else {
                    // initialize variables
                    $heat = 0;
                    $perf = 0;
                    $topX = 0;
                    $totalX = 0;
                    $i = 0;
                    $rank = 0;
                    // set rank for every athlete
                    while($row = mysql_fetch_row($result))
                    {
                        if(($eval == $cfgEvalType[$strEvalTypeHeat])    // new heat
                            &&($heat != $row[1]))
                        {
                            $i = 0;        // restart ranking
                            $perf = 0;
                            $topX = 0;
                            $totalX = 0;
                        }

                        $j++;                                // increment ranking
                        if($perf != $row[2] || $topX != $row[3] || $totalX != $row[4])
                        {
                            $rank = $j;    // next rank (only if not same performance)
                        }

                        mysql_query("
                            UPDATE tempHigh SET
                                rang = $rank
                            WHERE xSerienstart = $row[0]
                        ");

                        if(mysql_errno() > 0) {
                            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                        }
                        $heat = $row[1];        // keep current heat ID
                        $perf = $row[2];
                        $topX = $row[3];
                        $totalX = $row[4];
                    }
                    mysql_free_result($result);
                }

               // mysql_query("DROP TABLE IF EXISTS temp");

                if(mysql_errno() > 0) {        // DB error
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
           

		// display all athletes
		$result = mysql_query("
			SELECT
				rt.Name
				, rt.Typ
				, s.xSerie
				, s.Bezeichnung
				, s.Status
				, ss.xSerienstart
				, ss.Position
				, ss.Rang
				, a.Startnummer
				, at.Name
				, at.Vorname
				, at.Jahrgang
				, v.Name
				, LPAD(s.Bezeichnung,5,'0') as heatid
				, st.Bestleistung
				, at.xAthlet
				, at.Land
                , t.rang   
			FROM
				runde AS r
				LEFT JOIN serie AS s ON (s.xRunde = r.xRunde )
				LEFT JOIN serienstart AS ss ON (ss.xSerie = s.xSerie)
				LEFT JOIN start AS st ON (st.xStart = ss.xStart)
				LEFT JOIN anmeldung AS a ON (a.xAnmeldung = st.xAnmeldung)
				LEFT JOIN athlet AS at ON (at.xAthlet = a.xAthlet)
				LEFT JOIN verein AS v ON (v.xVerein = at.xVerein)
                LEFT JOIN tempHigh AS t ON (t.xSerienstart = ss.xSerienstart)    
			    LEFT JOIN rundentyp AS rt ON (rt.xRundentyp = r.xRundentyp)
			WHERE r.xRunde = $round   			
			ORDER BY                     
				heatid
				, ss.Position
		");          
                
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			// initialize variables
			$h = 0;
			$i = 0;
            $current_athlete = false;
            $curr_class = '';

			$resTable = new GUI_HighResultTable($round, $layout, $status);
            $resTable->printHeatTitleRegie($cat, $disc);

			while($row = mysql_fetch_row($result))
			{
/*
 *  Heat headerline
 */
				if($h != $row[2])		// new heat
				{
					$h = $row[2];				// keep heat ID
					if(is_null($row[0])) {		// only one round

						$title = "$strFinalround  $row[3]";
					}
					else {		// more than one round
						$title = "$row[0]: $row[1]$row[3]";
					}

					$c = 0;
					if($status == $cfgRoundStatus['results_done']) {
						$c = 1;		// increment colspan to include ranking
					}
                      
					$resTable->printHeatTitle($row[2], $row[3], $title, $row[4], 'regie');
					$resTable->printAthleteHeader('regie');
				}		// ET new heat

/*
 * Athlete data lines
 */
				$rank = '';
				$perfs = array();

				$res = mysql_query("
					SELECT
						r.Leistung
						, r.Info
					FROM
						resultat as r
					WHERE r.xSerienstart = $row[5]
					ORDER BY
						r.xResultat DESC
				");

				if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else
				{
					if($status == $cfgRoundStatus['results_done']) {
						$rank = $row[7];
					}
                    else {
                         $rank = $row[17];
                    }

					while($resrow = mysql_fetch_row($res))
					{
						$perf = AA_formatResultMeter($resrow[0]);
						$info = $resrow[1];
						$perfs[] = "$perf ( $info )";
					}	// end loop every tech result acc. programm mode

					mysql_free_result($res);
				}
                if (empty($perfs) && !$current_athlete){
                    $current_athlete = true;
                    $curr_class = "active";
                }
				$resTable->printAthleteLine($row[6], $row[8], "$row[9] $row[10]"
					, '', '', AA_formatResultMeter($row[14]), $perfs, $fett, $rank, '', $row[15], $curr_class, 'regie' );
                $curr_class = "";
			}
			$resTable->endTable();
			mysql_free_result($result);
		}		// ET DB error      	
       
         mysql_query("UNLOCK TABLES");  
       
    
}	// End function AA_regie_High


}	// AA_SPEAKER_RESULTS_HIGH_LIB_INCLUDED
?>
