<?php

/**********
 *
 *	heat maintenance functions
 *	
 */

if (!defined('AA_HEATS_LIB_INCLUDED'))
{
	define('AA_HEATS_LIB_INCLUDED', 1);



/**
 * seed entries
 * ------------
 */
function AA_heats_seedEntries($event)
{      
	require('./lib/cl_gui_dropdown.lib.php');
	require('./lib/cl_gui_select.lib.php');
	require('./lib/common.lib.php');
	require('./lib/utils.lib.php');
	include('./config.inc.php');
	
	$filmnumber = false;
	
	$relay = AA_checkRelay($event);
	$combined = AA_checkCombined($event); // combined event
	$teamsm = AA_checkTeamSM($event); // team sm event
	$cGroup = $_POST['cGroup']; // combined group to seed
    
    if (isset($_POST['round']))  
         $round = $_POST['round'];   
    else
        if (isset($_GET['round'])) 
	        $round = $_GET['round'];
            
	$size = $_POST['size'];
   
	if(!empty($_POST['tracks'])) {
		$tracks = $_POST['tracks'];
	}
	else {
		$tracks = $size;
	}

	$mode = 0;
	if(!empty($_POST['mode'])) {
		$mode = $_POST['mode'];
	}
	
	//
	//	read athletes/relays, ordered by mode type
	//
	// get type of contest
	// if this is an svm contest, sort with first heat runner
	$svmContest = AA_checkSVM($event);
	if($svmContest){   
		$orderFirst = "s.Erstserie ASC,"; // those with 'y' come first
	}else{
		$orderFirst = "";
	}
	
	// discipline type for top performance mode and for determining the need of a filmnumber
	$result = mysql_query("
		SELECT
			d.Typ
		FROM
			disziplin AS d
			, wettkampf AS w
		WHERE xWettkampf = $event
		AND d.xDisziplin = w.xDisziplin
	");

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		$order = "RAND()";
	}
	else
	{
		$row = mysql_fetch_row($result);
	}
	
	if($mode == 0 && !$svmContest) {	// open mode
		// random order
		$order = "RAND()";
		$badValue = "0";
		
		if(($row[0] == $cfgDisciplineType[$strDiscTypeTrack])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeRelay])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeDistance]))
			{
				$filmnumber = true;
			}
	} elseif($svmContest){	// SVM mode
		if(($row[0] == $cfgDisciplineType[$strDiscTypeTrack])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeRelay])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeDistance]))
			{
				$order = "best ASC, RAND()";	// track disciplines
				$badValue = "99999999";
				$filmnumber = true;
			}
			else {
				$order = "best ASC, RAND()";	// field disciplines
				$badValue = "0";
			}
	}
	else {				// top performance mode
		
		if(($row[0] == $cfgDisciplineType[$strDiscTypeTrack])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeRelay])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeDistance]))
			{
				$order = "best ASC, RAND()";	// track disciplines
				$badValue = "99999999";
				$filmnumber = true;
			}
			else {
				$order = "best DESC, RAND()";	// field disciplines
				$badValue = "0";
			}
			mysql_free_result($result);
	}
	 
	//
	// read merged rounds and select all events
	//    
    $eventMerged = false;    
    $sqlEvents=AA_getMergedEvents($round);
    if ($sqlEvents=='' )
        $sqlEvents = " s.xWettkampf = ".$event." "; 
    else {
        $sqlEvents = " s.xWettkampf IN ".$sqlEvents." ";   
         $eventMerged = true; 
    } 
    
    $mergedRounds=AA_getMergedRounds($round);  
    if ($mergedRounds=='')
        $sqlRounds="= ". $round;
    else
        $sqlRounds="IN ". $mergedRounds;  
   
	//	read entries either for athletes, relays or athletes in combined event
	//
	if(!$combined){
        if($relay == FALSE && !$svmContest) {    // single event
            $query = "SELECT xStart, if(Bestleistung = 0, $badValue, Bestleistung) as best, r.xRunde"
                    . " FROM start as s, anmeldung as a LEFT JOIN runde as r On (r.xWettkampf=s.xWettkampf)" 
                    . " WHERE " //xWettkampf = " . $event
                    . $sqlEvents
                    . " AND s.Anwesend = 0"
                    . " AND s.xAnmeldung > 0"
                    . " AND a.xAnmeldung = s.xAnmeldung"
                    . " AND r.xRunde ". $sqlRounds   
                    . " ORDER BY $order";   
        }
        elseif($relay == FALSE && $svmContest){ // single event but svm             
            $query = "SELECT s.xStart, if(Bestleistung = 0, $badValue, Bestleistung) as best, r.xRunde"
                    . " FROM start as s, anmeldung as a LEFT JOIN runde as r On (r.xWettkampf=s.xWettkampf)"
                    . " WHERE " //xWettkampf = " . $event
                    . $sqlEvents
                    . " AND s.Anwesend = 0"
                    . " AND s.xAnmeldung > 0"
                    . " AND a.xAnmeldung = s.xAnmeldung"
                    . " AND r.xRunde ". $sqlRounds
                    . " ORDER BY $orderFirst $order";    
        }
        else {                        // relay event
            $query = "SELECT xStart, if(Bestleistung = 0, $badValue, Bestleistung) as best, r.xRunde"
                    . " FROM start as s LEFT JOIN runde as r On (r.xWettkampf=s.xWettkampf)"
                    . " WHERE " // xWettkampf = " . $event
                    . $sqlEvents
                    . " AND s.Anwesend = 0"
                    . " AND s.xStaffel > 0"
                    . " AND r.xRunde ". $sqlRounds
                    . " ORDER BY $order";     
        }
    }else{ // combined 
        if(!empty($cGroup)){
            $query = "SELECT xStart, if(Bestleistung = 0, $badValue, Bestleistung) as best"
                    . " FROM start as s, anmeldung as a"
                    . " WHERE s.xWettkampf = " . $event
                    . " AND s.xAnmeldung = a.xAnmeldung"
                    . " AND a.Gruppe = $cGroup"
                    . " AND s.Anwesend = 0"
                    . " AND s.xAnmeldung > 0"
                    . " ORDER BY $order";
        }else{
            $query = "SELECT xStart, if(BestleistungMK = 0, 0, BestleistungMK) as best"
                    . " FROM start as s, anmeldung as a"
                    . " WHERE s.xWettkampf = " . $event
                    . " AND s.xAnmeldung = a.xAnmeldung"
                    . " AND s.Anwesend = 0"
                    . " AND s.xAnmeldung > 0"
                    . " ORDER BY best DESC, RAND()";
        }
    }
    if($teamsm && !empty($cGroup)){    // teamsm event with groups
        $query = "SELECT xStart, if(Bestleistung = 0, $badValue, Bestleistung) as best"
                . " FROM start as s, anmeldung as a"
                . " WHERE s.xWettkampf = " . $event
                . " AND s.xAnmeldung = a.xAnmeldung"
                . " AND a.Gruppe = $cGroup"
                . " AND s.Anwesend = 0"
                . " AND s.xAnmeldung > 0"
                . " ORDER BY $order";       
    }    
	$result = mysql_query($query);  
	$entries = mysql_num_rows($result);		// keep nbr of entries       
   
	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	// entries for this event found
	else if($entries > 0)
	{            
		mysql_query("LOCK TABLES resultat READ, rundenset READ, wettkampf READ , meeting READ, runde WRITE, serie WRITE"
							. ", serienstart WRITE");

		// check if round still exists
		if(AA_checkReference("runde", "xRunde", $round) == 0)
		{
			AA_printErrorMsg($strRound . $strErrNotValid);
		}
		else
		{
			// check if there are any results for this round
			$res = mysql_query("SELECT xResultat"
									. " FROM resultat"
									. ", serienstart"
									. ", serie"
									. " WHERE serie.xRunde  " . $sqlRounds
									. " AND serienstart.xSerie = serie.xSerie"
									. " AND resultat.xSerienstart = serienstart.xSerienstart");   
                                    
			if(mysql_errno() > 0)		// DB error
			{
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else if(mysql_num_rows($res) > 0)		// any results
			{
				mysql_free_result($res);
				AA_printErrorMsg($strErrResultsEntered);
			}
			else
			{
				mysql_free_result($res);
				$OK = TRUE;

				//
				// Delete current start per heat
				//
                
				$res = mysql_query("SELECT xSerie"
										. " FROM serie"
										. " WHERE xRunde  " . $sqlRounds);      

				if(mysql_errno() > 0)		// DB error
				{
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					$OK = FALSE;
				}
				else
				{  
					while($row = mysql_fetch_row($res))                                              
					{   
						mysql_query("DELETE FROM serienstart"
											. " WHERE xSerie = " . $row[0]);
						if(mysql_errno() > 0)		// DB error
						{
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							$OK = FALSE;
						}
					}
				}
				mysql_free_result($res);
              
				//
				// Delete heat
				//
               
				if($OK == TRUE)		// no errors while deleting
				{   
					// delete this round's heats
					mysql_query("DELETE FROM serie"
										. " WHERE xRunde  " . $sqlRounds);     
					if(mysql_errno() > 0)		// DB error
					{
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
					else
					{  
						// Update round data and seed entries
						AA_utils_changeRoundStatus($round, $cfgRoundStatus['heats_in_progress']);
						if(!empty($GLOBALS['AA_ERROR'])) {
							AA_printErrorMsg($GLOBALS['AA_ERROR']);
						}
                          
						mysql_query("
							UPDATE runde SET
								Bahnen = $tracks
							WHERE xRunde = $round
						");

						if(mysql_errno() > 0)		// DB error
						{
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
						else
						{  
							// create heats     
							$filmnr = 0;
							if($filmnumber){
								$filmnr = AA_heats_getNextFilm();
							}
							
							$h = ceil($entries/$size);	// calc. nbr of heats	
                            
							for($i = 1; $i <= $h; $i++)  
							{   
								mysql_query("INSERT INTO serie SET"
											. " xRunde = " . $round
											. ", xAnlage = 0"
											. ", Bezeichnung = " . $i
											. ", Film = ".$filmnr);  

								if(mysql_errno() > 0) {		// DB error
									AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
								}
								else {
									$heats[] = mysql_insert_id();
									if($filmnumber){
										$filmnr++;
									}
								}
							}   
                                  
							//
							// Mode: open or top performances together
							// ---------------------------------------
							if(($_POST['mode'] == 0)
								|| ($_POST['mode'] == 1))
							{
								// seed qualified athletes to heats
								// distribute athletes from center to outer tracks
								$i = 0;						// heat nbr
								$p = 1;						// first position
                                                               
								while ($row = mysql_fetch_row($result))
								{   
                                        if ($p > $size) {	// heat full -> start new heat
										    $i++;		// next heat
										    $p = 1;	// restart with first position   
									    }    
                               
									if(!empty($cfgTrackOrder[$tracks][$p])) {
										$pos = $cfgTrackOrder[$tracks][$p];
									}
									else {
										$pos = $p;
									}          
                                    if ($eventMerged){
									    mysql_query("INSERT INTO serienstart SET"
												. " Position = " . $pos
												. ", Bahn = " . $pos
												. ", xSerie = " . $heats[$i]
                                                . ", xStart = " . $row[0] 
												. ", RundeZusammen = " . $row[2]);  
                                    }
                                   else {
                                         mysql_query("INSERT INTO serienstart SET"
                                                . " Position = " . $pos
                                                . ", Bahn = " . $pos
                                                . ", xSerie = " . $heats[$i]
                                                . ", xStart = " . $row[0]);    
                                   }

									if(mysql_errno() > 0) {		// DB error
										AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
									}
									$p++;		// next position
                                  
								}    
							}
							//
							// Mode: top performances separated
							// --------------------------------
							else if($_POST['mode'] == 2)
							{
								// distribute entries to heats
								$i = 0;
								$p = 1;
								if(!empty($cfgTrackOrder[$tracks][$p])){
									$pos = $cfgTrackOrder[$tracks][$p];
								}else{
									$pos = $p;
								}
								while ($row = mysql_fetch_row($result))
								{
									if($i >= count($heats)) { 	// end of heat array
										$i=0;			// restart with first heat
										$p++;			// next position
										if(!empty($cfgTrackOrder[$tracks][$p])) {
											$pos = $cfgTrackOrder[$tracks][$p];
										}
										else {
											$pos = $p;
										}
									}
                                    if ($eventMerged){
                                        mysql_query("INSERT INTO serienstart SET"
                                                . " Position = " . $pos
                                                . ", Bahn = " . $pos
                                                . ", xSerie = " . $heats[$i]
                                                . ", xStart = " . $row[0] 
                                                . ", RundeZusammen = " . $row[2]);   
                                    }
                                    else {
									    $sql = "INSERT INTO serienstart SET"
												. " Position = " . $pos
												. ", Bahn = " . $pos
												. ", xSerie = " . $heats[$i]
												. ", xStart = " . $row[0];
									    mysql_query($sql);
                                    }
									
									if(mysql_errno() > 0) {		// DB error
										AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
									}
									$i++;			// next heat
								}
							}		// ET mode
						}		// ET DB error (status update)  
					}		// ET DB error (delete rounds)
				}		// ET DB error (delete starts)
			}		// ET results
		}		// ET round still active   
		mysql_query("UNLOCK TABLES");
	}		// ET DB error, entries found
}


/**
 * seed qualifided athletes
 * ------------------------
 */
function AA_heats_seedQualifiedAthletes($event)
{
	require('./lib/common.lib.php');
	include('./config.inc.php');

	$relay = AA_checkRelay($event);    
	$round = $_POST['round'];
	$prev_rnd = $_POST['prev_round'];

	$size = 0;
	if(!empty($_POST['size'])) {
		$size = $_POST['size'];
	}

	$tracks = 0;
	/*if(!empty($_POST['tracks'])) {
		$tracks = $_POST['tracks'];
	}*/
	if(!empty($_POST['tracks'])) {
		$tracks = $_POST['tracks'];
	}
	else {
		$tracks = $size;
	}

	$mode = 0;
	if(!empty($_POST['mode'])) {
		$mode = $_POST['mode'];
	}
	
	// check if film number is needed
	$filmnumber = false;
	$result = mysql_query("
		SELECT
			d.Typ
		FROM
			disziplin AS d
			, wettkampf AS w
		WHERE xWettkampf = $event
		AND d.xDisziplin = w.xDisziplin
	");

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		$row = mysql_fetch_row($result);
		if(($row[0] == $cfgDisciplineType[$strDiscTypeTrack])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeRelay])
			|| ($row[0] == $cfgDisciplineType[$strDiscTypeDistance]))
		{
			$filmnumber = true;
		}
	}

	//
	//	read qualified athletes/relays, ordered by mode type
	//
	if($mode == 0) {	// top performance mode
		// performance, rank in previous round, randomize
		$order = "r.Leistung ASC, ss.Rang ASC, RAND()";
	}
	else {				// according IWB rule 166
		// Rules:
		// - qualifiers by top position are ordered first
		// - qualifiers by top positions are ordered by rank, performance
		// - qualifiers by performance are ordered by performance only
		$order = "2 ASC, 3 ASC, r.Leistung ASC";
	}

    //
    // read merged rounds and select all events
    //   
     
    $eventMerged = false;       
    $mergedRounds=AA_getMergedRounds($prev_rnd);  
    if ($mergedRounds=='')
        $sqlRoundsPrev="= ". $round;                  
    else {
        $sqlRoundsPrev="IN ". $mergedRounds; 
        $eventMerged = true;  
    } 
    
   
    if ($eventMerged) {     
    
        mysql_query("DROP TABLE IF EXISTS qualified_tmp");    // temporary table    
  
        $query_t="CREATE TEMPORARY TABLE qualified_tmp SELECT ss.xStart"
                            . ", IF(ss.Qualifikation<=2, 0, 1) AS qualified1"
                            . ", IF(ss.Qualifikation<=2, ss.Rang, 0) AS qualified2"  
                            . " FROM serienstart AS ss"
                            . ", serie AS s"
                            . ", resultat AS r"
                            . " WHERE ss.Qualifikation > 0"
                            . " AND ss.Qualifikation != 9"
                            . " AND s.xSerie = ss.xSerie"
                            . " AND s.xRunde  " . $sqlRoundsPrev
                            . " AND r.xSerienstart = ss.xSerienstart"
                            . " ORDER BY " . $order;
                             
                             
        $res_tmp = mysql_query($query_t);   
 
        if(mysql_errno() > 0)        // DB error
            {
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
        }
        else  
        {            
            $mergedRounds=AA_getMergedRounds($round);  
            if ($mergedRounds=='')
                $sqlRounds="= ". $round;
             else 
                $sqlRounds="IN ". $mergedRounds;     
    
           $result = mysql_query("select s.xStart 
                    ,t.qualified1
                    , t.qualified2
                     ,   r.xRunde                        
                    FROM
                        start as s  
                        LEFT JOIN runde as r ON (r.xWettkampf = s.xWettkampf)
                        INNER JOIN qualified_tmp AS t ON (t.xStart = s.xStart)
                    WHERE r.xRunde " . $sqlRounds);        
                            // . " AND s.xRunde = " . $prev_rnd        
        }
    }
    else {  
	$result = mysql_query("SELECT ss.xStart"
							. ", IF(ss.Qualifikation<=2, 0, 1)"
							. ", IF(ss.Qualifikation<=2, ss.Rang, 0)"
                            . ", ss.RundeZusammen"
							. " FROM serienstart AS ss"
							. ", serie AS s"
							. ", resultat AS r"
							. " WHERE ss.Qualifikation > 0"
							. " AND ss.Qualifikation != 9"
							. " AND s.xSerie = ss.xSerie"
							. " AND s.xRunde  =" . $prev_rnd
							. " AND r.xSerienstart = ss.xSerienstart"
							. " ORDER BY " . $order);
                            
                            // . " AND s.xRunde = " . $prev_rnd    
    }
         
	if(mysql_errno() > 0)		// DB error
	{    
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	// athletes for this event found
	else if(mysql_num_rows($result) > 0)
	{         
		mysql_query("LOCK TABLES resultat READ, runde WRITE, rundenset READ,serie WRITE"
							. ", serienstart WRITE");

		// check if round still exists
		if(AA_checkReference("runde", "xRunde", $round) == 0)
		{
			AA_printErrorMsg($strRound . $strErrNotValid);
		}
		else
		{   
			// check if there are any results for this round
			$res = mysql_query("SELECT xResultat"
									. " FROM resultat"
									. ", serienstart"
									. ", serie"
									. " WHERE serie.xRunde = " . $round
									. " AND serienstart.xSerie = serie.xSerie"
									. " AND resultat.xSerienstart = serienstart.xSerienstart");

                                    
			if(mysql_errno() > 0)		// DB error
			{
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else if(mysql_num_rows($res) > 0)		// any results
			{
				mysql_free_result($res);
				AA_printErrorMsg($strErrResultsEntered);
			}
			else
			{
				mysql_free_result($res);
				$OK = TRUE;
                  
				//
				// Delete already seeded starts per heat
				//
				$res = mysql_query("SELECT xSerie"
										. " FROM serie"
										. " WHERE xRunde = " . $round);

				if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					$OK = FALSE;
				}
				else
				{
					while($row = mysql_fetch_row($res))
					{
						mysql_query("DELETE FROM serienstart"
											. " WHERE xSerie = " . $row[0]);
						if(mysql_errno() > 0)		// DB error
						{
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							$OK = FALSE;
						}
					}
				}
				mysql_free_result($res);

				//
				// Delete heat
				//
				if($OK == TRUE)		// no errors while deleting
				{
					// delete this round's heats
					mysql_query("DELETE FROM serie"
										. " WHERE xRunde = " . $round);

					if(mysql_errno() > 0) {		// DB error
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
					else
					{
						// Update round data and seed entries
						AA_utils_changeRoundStatus($round, $cfgRoundStatus['heats_in_progress']);
						if(!empty($GLOBALS['AA_ERROR'])) {
							AA_printErrorMsg($GLOBALS['AA_ERROR']);
						}

						mysql_query("
							UPDATE runde SET
								Bahnen = $tracks
							WHERE xRunde = $round
						");

						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
						else
						{
							// create necessary heats
							$filmnr = 0;
							if($filmnumber){
								$filmnr = AA_heats_getNextFilm();
							}
							
							$entries = mysql_num_rows($result);	// qualified athletes
							$h = ceil($entries/$size);				// calc. nbr of heats

							unset($heats);					// array to store heat ID's
							for($i=1; $i <= $h; $i++)
							{
								mysql_query("INSERT INTO serie SET"
											. " xRunde = " . $round
											. ", xAnlage = 0"
											. ", Bezeichnung = " . $i
											. ", Film = ".$filmnr);

								if(mysql_errno() > 0) {		// DB error
									AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
								}
								$heats[] = mysql_insert_id();
								if($filmnumber){
									$filmnr++;
								}
							}

							//
							// Mode: Seed by top performances
							// ------------------------------
							if($_POST['final'] == TRUE)
							{
								// seed qualified athletes to heats
								// distribute athletes from center to outer tracks
								$h = 0;						// heat nbr
								$p = 1;						// first position
								while ($row = mysql_fetch_row($result))
								{
									if($p > $size) {	// heat full -> start new heat
												// check for size, not tracks in case that there are more athletes than tracks
										$h++;		// next heat
										$p = 1;	// restart with first position
									}
									if(!empty($cfgTrackOrder[$tracks][$p])) {
										$pos = $cfgTrackOrder[$tracks][$p];
									}
									else {
										$pos = $p;
									}
									
									$t = ceil($pos/($size/$tracks)); // calculate track
													// relevant if there are more athletes than tracks
									if ($eventMerged){
									    mysql_query("INSERT INTO serienstart SET"
												. " Position = " . $pos
												. ", Bahn = " . $t
												. ", xSerie = " . $heats[$h]
												. ", xStart = " . $row[0]
                                                . ", RundeZusammen = " . $row[3]);
                                    }
                                    else {
                                          mysql_query("INSERT INTO serienstart SET"
                                                . " Position = " . $pos
                                                . ", Bahn = " . $t
                                                . ", xSerie = " . $heats[$h]
                                                . ", xStart = " . $row[0]); 
                                    }

									if(mysql_errno() > 0) {		// DB error
										AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
									}
									$p++;		// next position
								}
							}
							//
							// Mode: Seed according to IWB rule 166
							// ------------------------------------
							else
							{
								// seed qualified athletes to heats
								$pos = 1;
								srand ((float)microtime()*1000000);	// seed shuffle
								shuffle ($heats);		// randomly order all heats
								$h = 0;					// heat to start with
								$up = TRUE;				// step-through-heats direction
								while ($row = mysql_fetch_row($result))
								{
									// last heat reached
									if($up == TRUE)	// step up
									{
										if ($h >= count($heats)) {
											$up = FALSE;				// step down
											$h = count($heats)-1;	// start with last heat
											$pos++;						// next position
										}
									}
									else	// step down
									{
										if ($h < 0) {
											$up = TRUE;		// step down
											$h = 0;			// start with first heat
											$pos++;			// next position
										}
									}
                                    if ($eventMerged){
									    mysql_query("INSERT INTO serienstart SET"
												. " Position = " . $pos
												. ", Bahn = " . $pos
												. ", xSerie = " . $heats[$h]
												. ", xStart = " . $row[0]
                                                . ", RundeZusammen = " . $row[3]);
                                    }
                                    else {
                                           mysql_query("INSERT INTO serienstart SET"
                                                . " Position = " . $pos
                                                . ", Bahn = " . $pos
                                                . ", xSerie = " . $heats[$h]
                                                . ", xStart = " . $row[0]);
                                    
                                    
                                    }

									if(mysql_errno() > 0) {		// DB error
										AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
									}

									if($up == TRUE) {	// step up
										$h++;		// next heat
									}
									else {
										$h--;		// next heat
									}
								}
								// draw start positions per heat
								foreach($heats as $heat)
								{
									// Separate random order for top half and
									// bottom half positions
									$split = ceil($size/2);		// split position
									$res = mysql_query("SELECT xSerienstart"
												. ", IF(Position <= " . $split . ", 0, 1)"
												. " FROM serienstart"
												. " WHERE xSerie = " . $heat
												. " ORDER BY 2"
												. ", RAND()");

									if(mysql_errno() > 0) {		// DB error
										AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
									}
									else {
										$p = 1;
										while ($row = mysql_fetch_row($res))
										{
											if(!empty($cfgTrackOrder[$tracks][$p])) {
												$pos = $cfgTrackOrder[$tracks][$p];
											}
											else {
												$pos = $p;
											}
											mysql_query("UPDATE serienstart SET"
														. " Position = " . $pos
														. ", Bahn = " . $pos
														. " WHERE xSerienstart = $row[0]");

											if(mysql_errno() > 0) {		// DB error
												AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
											}
											$p++;
										}
										mysql_free_result($res);
									}		// ET DB error
								}		// next heat
							}		// ET mode
						}		// ET DB error (status update)
					}		// ET DB error (delete rounds)
				}		// ET DB error (delete starts)
			}		// ET results
		}		// ET round still active
		mysql_query("UNLOCK TABLES");
		mysql_free_result($result);
	}	// ET DB error, entries found
    
   mysql_query("DROP TABLE IF EXISTS qualified_tmp");   
}


/**
 * seed one heat randomly
 * ----------------------
 */
function AA_heats_seedHeat($heat){
	
	mysql_query("LOCK TABLES serienstart WRITE");
	
	$res = mysql_query("
			SELECT * FROM serienstart
			WHERE xSerie = $heat
			ORDER BY RAND()"
	);
	if(mysql_errno() > 0){
		AA_print_ErrorMsg(mysql_errno().": ".mysql_error());
	}else{
		$pos = array();
		$starts = array();
		
		while($row = mysql_fetch_assoc($res)){
			$pos[] = $row['Position'];
			$starts[] = $row['xSerienstart'];
		}
		sort($pos);
		
		for($i=0; $i<count($pos); $i++){
			mysql_query("
				UPDATE serienstart
				SET Position = ".$pos[$i]."
				, Bahn = ".$pos[$i]."
				WHERE xSerienstart = ".$starts[$i]."
			");
		}
	}
	
	mysql_query("UNLOCK TABLES");
}


/**
 * add new athlete/relay
 * ---------------------
 */
function AA_heats_addStart($round)
{  
	require('./lib/common.lib.php');
    
    $eventMerged=false; 
    $mround=0;
    $mergedRounds=AA_getMergedRounds($round);
    if ($mergedRounds != '') {
        $eventMerged=true;  
        $sql="select 
                r.xRunde 
              FROM 
                start as st
                LEFT JOIN runde AS r ON (r.xWettkampf=st.xWettkampf)
              WHERE 
                st.xStart= " .  $_POST['start'] . "
                AND r.xRunde IN " . $mergedRounds; 
        
        $result = mysql_query($sql);
        if(mysql_errno() > 0){
            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
            
        }else{
            $row = mysql_fetch_array($result); 
            if ($row[0] > 0) {
                $mround=$row[0];    
            }
        }   
    }     

	if(empty($_POST['heat']) || empty($_POST['start']) || empty($_POST['pos'])) {
		AA_printErrorMsg($GLOBALS['strErrEmptyFields']);
	}
	// OK: try to change
	else
	{  
		mysql_query("LOCK TABLES start WRITE, serie WRITE, serienstart WRITE, runde READ");

		// check if start exists
		if(AA_checkReference("start", "xStart", $_POST['start']) > 0)
		{
			$xSerie = 0;
			// heat does not yet exist
			if($_POST['heat'] == 'new')
			{  
				// check if round still valid
				if(AA_checkReference("runde", "xRunde", $round) != 0)
				{
					// get new heat name
					$newhn = '0';
					$res = mysql_query("SELECT MAX(Bezeichnung) FROM
								serie
							WHERE
								xRunde = $round");
					if(mysql_errno() > 0) {
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}else{
						$row = mysql_fetch_array($res);
						mysql_free_result($res);
						$newhn = chr((ord($row[0])+1));
					}
					
					mysql_query("
						INSERT INTO serie SET
							Bezeichnung = '$newhn'
							, xRunde = $round
							, xAnlage = 0
					");

					if(mysql_errno() > 0) {
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
					else {
						$xSerie = mysql_insert_id();	// get new heat ID
					}
				}
			}
			else if(AA_checkReference("serie", "xSerie", $_POST['heat']) > 0)
			{   
				$xSerie = $_POST['heat'];
			}

			if($xSerie > 0)	// valid heat
			{
				// update heat start with new heat / position
                if ($eventMerged){
                        mysql_query("INSERT serienstart SET
                                    xSerie = $xSerie
                                    , xStart = " . $_POST['start'] . "
                                    , Position = '" . $_POST['pos'] . "'
                                    , RundeZusammen = '" . $mround . "'    
                             ");         
                }
                else {
				        mysql_query("INSERT serienstart SET
						            xSerie = $xSerie
						            , xStart = " . $_POST['start'] . "
						            , Position = '" . $_POST['pos'] . "'
                                   
				");
                }
				
				if(mysql_errno() > 0){
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				
				// set start.Anwesend to 0
				mysql_query("UPDATE start SET Anwesend = 0 WHERE xStart = ".$_POST['start']);
				
				if(mysql_errno() > 0){
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				
			}	// ET heat valid
		}	// ET referential integrity OK
		mysql_query("UNLOCK TABLES");
	}	// ET valid data provided
}



/**
 * delete an athlete/relay from the startlist
 * ------------------------------------------
 */
function AA_heats_deleteStart()
{
	require('./lib/common.lib.php');

	if(empty($_GET['item'])) {
		AA_printErrorMsg($GLOBALS['strErrEmptyFields']);
	}
	// OK: try to change
	else
	{   
		mysql_query("LOCK TABLES resultat WRITE, serie WRITE, serienstart WRITE, runde WRITE");

		mysql_query("DELETE FROM resultat WHERE xSerienstart = "
						. $_GET['item']);

		if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			mysql_query("DELETE FROM serienstart WHERE xSerienstart = "
						. $_GET['item']);

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
		}

		AA_heats_deleteHeats();

		mysql_query("UNLOCK TABLES");
	}	// ET valid data provided
}



/**
 * delete heats if not referenced anymore
 * --------------------------------------
 */
function AA_heats_deleteHeats()
{
	require('./lib/common.lib.php');

	$round = 0;
	$res = mysql_query("SELECT serie.xSerie"
							. ", serie.xRunde"
							. " FROM serie"
							. " LEFT JOIN serienstart"
							. " ON serie.xSerie = serienstart.xSerie"
							. " WHERE serienstart.xSerie is NULL");

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else {
		while($row = mysql_fetch_row($res))
		{
			$round = $row[1];		// keep round ID
			mysql_query("DELETE FROM serie"
						. " WHERE xSerie = $row[0]");
			if(mysql_errno() > 0)
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
		}
		mysql_free_result($res);
	}

	// if last heat for this round, reset round status
	if($round > 0) {		// heat found
		$res = mysql_query("SELECT serie.xSerie"
							. " FROM serie"
							. " WHERE serie.xRunde = $round");
		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else {
			if(mysql_num_rows($res) == 0) {	// no more heats
				AA_utils_changeRoundStatus($round, $GLOBALS['cfgRoundStatus']['open']);
				if(!empty($GLOBALS['AA_ERROR'])) {
					AA_printErrorMsg($GLOBALS['AA_ERROR']);
				}
			}
			mysql_free_result($res);
		}
	}
}

/**
 * Change film number
 */

function AA_heats_changeFilm(){
	
	if(empty($_POST['film'])) {
		AA_printErrorMsg($GLOBALS['strErrEmptyFields']);
	}else{
		
		mysql_query("LOCK TABLES runde READ, wettkampf READ, meeting READ, serie WRITE");
		
		// check if filmnummer already exists in context of current meeting
		$res = mysql_query("
				SELECT * FROM 
					serie
					LEFT JOIN runde  USING(xRunde) 
					LEFT JOIN wettkampf USING(xWettkampf) 
					LEFT JOIN meeting USING(xMeeting)
				WHERE meeting.xMeeting = ".$_COOKIE['meeting_id']."
				AND serie.Film = ".$_POST['film']);
		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			if(mysql_num_rows($res) == 0){
				// no results --> update film nummer
				mysql_query("update 
						serie
					set Film = ".$_POST['film']."
					where xSerie = ".$_POST['item']);
				
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
			}else{
				// film already exists
				AA_printErrorMsg($strErrFilmExists);
			}
		}
		
		mysql_query("UNLOCK TABLES");
	}
	
}

/**
 * get next higher film number
 * ---------------------------------
 */
function AA_heats_getNextFilm(){
	
	$res = mysql_query("
		SELECT max(serie.Film) FROM
			serie
			LEFT JOIN runde  USING(xRunde) 
			LEFT JOIN wettkampf USING(xWettkampf) 
			LEFT JOIN meeting USING(xMeeting)
		WHERE meeting.xMeeting = ".$_COOKIE['meeting_id']);
	
	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}else{
		$row = mysql_fetch_array($res);
		if($row[0] == 0){
			return 1;
		}
		return ($row[0]+1);
	}
	
}

/**
 * set flag hand stopped time on heat
 * ---------------------------------
 */
function AA_heats_changeHandStopped($heat){
	
	if(isset($_POST['handstopped'])){ // set flag
		
		mysql_query("UPDATE serie SET
				Handgestoppt = 1
			WHERE
				serie.xSerie = $heat");
		
	}else{ // unset flag
		
		mysql_query("UPDATE serie SET
				Handgestoppt = 0
			WHERE
				serie.xSerie = $heat");
		
	}
	
	if(mysql_errno() > 0){
		$GLOBALS['ERROR'] = mysql_errno().": ".mysql_error();
	}
	
}

/**
 * change heat
 * ---------------------------------
 */
function AA_heats_changePosition($round)
{
	require('./lib/common.lib.php');
	include('./config.inc.php');

	if(empty($_POST['pos']) && empty($_POST['track'])) {
		AA_printErrorMsg($GLOBALS['strErrEmptyFields']);
	}
	// OK: try to change
	else
	{  
		mysql_query("LOCK TABLES runde READ, serie WRITE, serienstart WRITE, wettkampf READ, disziplin READ, meeting READ");
		
		// change request by text field
		if(empty($_POST['heat'])){
			$result = mysql_query("
				SELECT xSerie FROM 
					serie 
				WHERE	Bezeichnung = '".$_POST['heatname']."'
				AND	xRunde = $round"
			);
			if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				if(mysql_num_rows($result) > 0){
					$row = mysql_fetch_array($result);
					$_POST['heat'] = $row[0];
					mysql_free_result($result);
				}
			}
		}
		
		$xSerie = 0;
		// heat does not yet exist
		if(AA_checkReference("serie", "xSerie", $_POST['heat']) == 0)
		{
			// check if round still valid
			if(AA_checkReference("runde", "xRunde", $round) != 0)
			{
				// get discipline type for evaluating if a filmnumber is needed
				$filmnr = 0;
				$res_dist = mysql_query("SELECT disziplin.Typ FROM 
								runde
								LEFT JOIN wettkampf USING(xWettkampf)
								LEFT JOIN disziplin USING(xDisziplin)
							WHERE runde.xRunde = $round");
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}else{
					$row_dist = mysql_fetch_Array($res_dist);
					
					if(($row_dist[0] == $cfgDisciplineType[$strDiscTypeTrack])
						|| ($row_dist[0] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
						|| ($row_dist[0] == $cfgDisciplineType[$strDiscTypeRelay])
						|| ($row_dist[0] == $cfgDisciplineType[$strDiscTypeDistance]))
					{
						$filmnr = AA_heats_getNextFilm();
					}
				}
				
				mysql_query("INSERT INTO serie SET"
							/*. " Bezeichnung = '0'"*/
							. " Bezeichnung = '".$_POST['heatname']."'"
							. ", xRunde = $round"
							. ", xAnlage = 0
							   , Film = $filmnr");

				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else {
					$xSerie = mysql_insert_id();	// get new heat ID
				}
			}
		}
		else		// heat does exist -> keep heat ID
		{
			$xSerie = $_POST['heat'];
		}

		if($xSerie != 0)		// heat valid
		{
			if(!empty($_POST['track'])){
				mysql_query("UPDATE serienstart SET"
						//. " Position = '" . $_POST['pos']
						. " xSerie = " . $xSerie
						. ", Bahn = " . $_POST['track']
						. " WHERE xSerienstart = " . $_POST['item']);
			}else{
				// update heat start with new heat / position
				mysql_query("UPDATE serienstart SET"
						. " Position = '" . $_POST['pos']
						. "', xSerie = " . $xSerie
						. ", Bahn = " . $_POST['pos'] // initial track is the position
						. " WHERE xSerienstart = " . $_POST['item']);
			}
			if(mysql_errno() > 0)
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
		}

		AA_heats_deleteHeats();

		mysql_query("UNLOCK TABLES");

		// reset round status
		if(AA_getRoundStatus($round) >= $GLOBALS['cfgRoundStatus']['heats_done']) {
			AA_utils_changeRoundStatus($round, $GLOBALS['cfgRoundStatus']['heats_in_progress']);
			if(!empty($GLOBALS['AA_ERROR'])) {
				AA_printErrorMsg($GLOBALS['AA_ERROR']);
			}
		}
	}
}



/**
 * change heat id
 * --------------
 */
function AA_heats_changeHeatName($round)
{
	require('./lib/common.lib.php');

	if(empty($_POST['item'])) {
		AA_printErrorMsg($GLOBALS['strErrEmptyFields']);
	}
	// OK: try to change
	else
	{
		//if($_POST['id'] != '?')		// '?' is reserved for new heats
		if($_POST['id'] != '?' && eregi('^([A-Z]{1,3}|[0-9]{1,3})$', $_POST['id']))		// '?' is reserved for new heats
		{  
			mysql_query("LOCK TABLES serie WRITE");

			mysql_query("
				UPDATE serie SET
					Bezeichnung = '" . $_POST['id'] . "'
				WHERE xSerie=". $_POST['item']
			);

			if(mysql_errno() > 0)
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			mysql_query("UNLOCK TABLES");
		}
		else
		{
			AA_printErrorMsg("'".$_POST['id']."': " . $GLOBALS['strErrInvalidHeatName']);
		}
	}
}



/**
 * change installations
 * --------------------
 */
function AA_heats_changeInstallation($round)
{
	require('./lib/common.lib.php');

	if(($_POST['arg'] == 'change_inst') && (empty($_POST['item']))) {
		AA_printErrorMsg($GLOBALS['strErrEmptyFields']);
	}
	// OK: try to change
	else
	{   
		mysql_query("LOCK TABLES anlage READ, serie WRITE");

		$installation = $_POST['installation'];
		if(AA_checkReference("anlage", "xAnlage", $installation) == 0)
		{
			$installation = 0;
		}

		if($_POST['arg'] == 'change_inst') {	// only for one heat
			$clause = " WHERE xSerie=". $_POST['item'];
		}
		else {											// all heats
			$clause = " WHERE xRunde=". $round;
		}
		mysql_query("UPDATE serie SET"
					. " xAnlage = $installation"
					. $clause);

		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		mysql_query("UNLOCK TABLES");
	}
}


//
// delete heats
// ------------
function AA_heats_delete($round)
{
	require('./lib/common.lib.php');
      
	mysql_query("LOCK TABLES runde READ ,rundenset READ, serie WRITE, serienstart WRITE");

	// delete only possible if no results entered yet
	if(AA_getRoundStatus($round) > $GLOBALS['cfgRoundStatus']['heats_done'])
	{
		AA_printErrorMsg($GLOBALS['strErrResultsEntered']);
	}
	else
	{
		// get this round's heats
		$result = mysql_query("SELECT xSerie"
								. " FROM serie"
								. " WHERE xRunde = $round");

		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			// delete every start per heat
			$err = '';
			while($row = mysql_fetch_row($result))
			{
				mysql_query("DELETE FROM serienstart"
							. " WHERE xSerie = $row[0]");
				if(mysql_errno() > 0)
				{
					$err = mysql_errno() . ": " . mysql_error();
				}
			}
			if($err != '')
			{
				AA_printErrorMsg($err);
			}
			else
			{
				// delete heat
				mysql_query("DELETE FROM serie"
							. " WHERE xRunde = $round");
				if(mysql_errno() > 0)
				{
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else
				{
					// reset round status
					AA_utils_changeRoundStatus($round, $GLOBALS['cfgRoundStatus']['open']);
					if(!empty($GLOBALS['AA_ERROR'])) {
						AA_printErrorMsg($GLOBALS['AA_ERROR']);
					}
				}
			}		// ET DB Error while deleting starts
		}		// ET DB error while reading hests
		mysql_free_result($result);
	}		// ET results already entered
	
	mysql_query("UNLOCK TABLES");
}



/**
 * Form to add additional athlete/relay
 * ------------------------------------
 */
function AA_heats_printNewStart($event, $round, $action)
{  
	include('./config.inc.php');    
                                           
    $mergedRounds=AA_getMergedRounds($round);   
    if ($mergedRounds!='')
        $SqlRounds=" IN " .$mergedRounds;
    else
        $SqlRounds=" = " .$round;     
   
	// set up key list containing this round's starts
	$result = mysql_query("SELECT ss.xStart"
								. " FROM runde AS r"
								. ", serie AS s"
								. ", serienstart AS ss"
								. " WHERE r.xRunde " . $SqlRounds
								. " AND s.xRunde = r.xRunde"
								. " AND ss.xSerie = s.xSerie");
	if(mysql_errno() > 0) {		// DB error
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		$keys = "";
		$sep = "";
		while($row = mysql_fetch_row($result))
		{
			$keys = $keys . $sep . $row[0];
			$sep = ",";
		}
		mysql_free_result($result);
	}

	$relay = AA_checkRelay($event);
    
    $mergedEvents=AA_getMergedEventsFromEvent($event);   
    if ($mergedEvents!='')
        $SqlEvents=" IN " .$mergedEvents;
    else
        $SqlEvents=" = " .$event;   

	// get athletes entered for this event but not qualified for this round
	if($relay == FALSE) {		// single event
		$title = $GLOBALS['strAthlete'];
		$query = ("SELECT st.xStart"
					. ", CONCAT(a.Startnummer, ' ', at.Name , ' ', at.Vorname"
					. ", ', ',  at.Jahrgang, ', ', v.Name)"
					. " FROM start AS st"
					. ", anmeldung AS a"
					. ", athlet AS at"
					. ", verein AS v"
					. " WHERE st.xWettkampf " . $SqlEvents
					. " AND st.xStart NOT IN (" . $keys
					. ") AND a.xAnmeldung = st.xAnmeldung"
					. " AND at.xAthlet = a.xAthlet"
					. " AND v.xVerein = at.xVerein"
					. " ORDER BY at.Name, at.Vorname");
	}
	else {								// relay event
		$title = $GLOBALS['strRelay'];
		$query = ("SELECT st.xStart"
					. ", CONCAT(sf.Name, ', ', v.Name)"
					. " FROM start AS st"
					. ", staffel AS sf"
					. ", verein AS v"
					. " WHERE st.xWettkampf " . $SqlEvents
					. " AND st.xStart NOT IN (" . $keys
					. ") AND sf.xStaffel = st.xStaffel"
					. " AND v.xVerein = sf.xVerein"
					. " ORDER BY sf.Name");
	}
   
	$result = mysql_query($query);
	if(mysql_errno() > 0) {		// DB error
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		if(mysql_num_rows($result) > 0)	// any athletes found
		{  
?>
<form action='<?php echo $action; ?>' method='post'>
<table>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strNew']; ?></th>
		<td class='dialog'>
		<input name='arg' type='hidden' value='add_start' />
		<input type='hidden' name='round' value='<?php echo $round; ?>' />   
		<?php echo $title; ?>:</td>
		<td class='forms'>
<?php
			// print drop down list of athletes
			$dd = new GUI_Select('start', 1);
			$dd->addOptionNone();
			while ($row = mysql_fetch_row($result))
			{
				$dd->addOption($row[1], $row[0]);
			}
			$dd->printList();
?>
		</td>
		<td class='dialog'><?php echo $GLOBALS['strHeat']; ?>:</td>
<?php
			// print drop down list of heats
			$dd = new GUI_HeatDropDown($round);
?>
		<td class='dialog'><?php echo $GLOBALS['strPosition']; ?>:</td>
		<td class='forms'>
			<input class='nbr' name='pos' type='text' maxlength='4'
				value='0' />
		</td>
		<td class='forms'>
		<td class='forms'>
			<button type='submit'>
				<?php echo $GLOBALS['strAdd']; ?>
			</button>
		</td>
	</tr>
</table>
</form>
<?php
		}	// ET athletes found
		mysql_free_result($result);
	}	//	ET DB error
}


/**
 * print empty tracks
 * ------------------
 * arg 1 (int): heat position
 * arg 2 (int): up to this position
 * arg 3 (int): heat ID
 * arg 4 (char): heat name
 *
 * returns next position
 */
function AA_heats_printEmptyTracks($position, $last, $heatID, $heatName)
{
	while($position <= $last)
	{
		// switch row class again
		if($position % 2 == 0) {			// even row numer
			$rowclass='even';
		}
		else {						// odd row number
			$rowclass='odd';
		}	
?>
	<tr class='<?php echo $rowclass; ?>'
		onClick='clickTrack(this, <?php echo "0, ".$heatID.", \"".$heatName."\", ".$position;?>)'>
		<td>
			<?php echo $position; ?>
		</td>
		<td colspan='7'><?php echo $GLOBALS['strEmpty']; ?></td>
	</tr>
<?php
		$position++;
	}

	return $position;
}



}		// AA_HEATS_LIB_INCLUDED
?>
