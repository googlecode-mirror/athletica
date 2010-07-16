<?php

/**********
 *
 *	track results (speaker)
 *	
 */

if (!defined('AA_SPEAKER_RESULTS_TRACK_LIB_INCLUDED'))
{
	define('AA_SPEAKER_RESULTS_TRACK_LIB_INCLUDED', 1);

function AA_speaker_Track($event, $round, $layout)
{
	require('./lib/cl_gui_menulist.lib.php');
	require('./lib/cl_gui_resulttable.lib.php');
    require('./lib/cl_performance.lib.php');  
	require('./config.inc.php');
	require('./lib/common.lib.php');

	$relay = AA_checkRelay($event);	// check, if this is a relay event
	$status = AA_getRoundStatus($round);

	// No action yet
	if(($status == $cfgRoundStatus['open'])
		|| ($status == $cfgRoundStatus['enrolement_done'])
		|| ($status == $cfgRoundStatus['heats_in_progress']))
	{
		AA_printWarningMsg($strHeatsNotDone);
	}
	// Enrolement pending
	else if($status == $cfgRoundStatus['enrolement_pending'])
	{
		AA_printWarningMsg($strEnrolementNotDone);
	}
	// Heat seeding completed
	else if($status >= $cfgRoundStatus['heats_done'])
	{
		// show link to rankinglist if results done
		if($status == $cfgRoundStatus['results_done'])
		{
			$menu = new GUI_Menulist();
			$menu->addButton("print_rankinglist.php?round=$round&type=single&formaction=speaker&show_efforts=none", $GLOBALS['strRankingList']);
			$menu->addButton("print_rankinglist.php?round=$round&type=single&formaction=speaker&show_efforts=sb_pb", $GLOBALS['strRankingListEfforts']);
			$menu->printMenu();
			echo "<p/>";
		}

		// show qualification info if another round follows
		$nextRound = AA_getNextRound($event, $round);

		if($nextRound > 0)
		{
			$result = mysql_query("
				SELECT
					QualifikationSieger
					, QualifikationLeistung
				FROM
					runde
				WHERE xRunde = $round
			");

			if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else
			{
				if(($row = mysql_fetch_row($result)) == TRUE);
				{
					echo "$strQualification: $row[0] $strQualifyTop, $row[1] $strQualifyPerformance";
					echo "<p/>";
				}
			}	// ET DB error
			mysql_free_result($result);
		}	// ET next round


		// display all athletes
		if($relay == FALSE) {		// single event
			$query = ("
				SELECT
					r.Bahnen
					, rt.Name
					, rt.Typ
					, s.xSerie
					, s.Bezeichnung
					, s.Wind
					, s.Film
					, s.Status
					, ss.xSerienstart
					, ss.Position
					, ss.Rang
					, ss.Qualifikation
					, a.Startnummer
					, at.Name
					, at.Vorname
					, at.Jahrgang
					, v.Name
					, LPAD(s.Bezeichnung,5,'0') as heatid
					, at.Land
					, st.Bestleistung
					, at.xAthlet
				FROM
					runde AS r
					, serie AS s
					, serienstart AS ss
					, start AS st
					, anmeldung AS a
					, athlet AS at
					, verein AS v
				LEFT JOIN rundentyp AS rt
					ON rt.xRundentyp = r.xRundentyp
				LEFT JOIN anlage AS an
					ON an.xAnlage = s.xAnlage
				WHERE r.xRunde = $round
				AND s.xRunde = r.xRunde
				AND ss.xSerie = s.xSerie
				AND st.xStart = ss.xStart
				AND a.xAnmeldung = st.xAnmeldung
				AND at.xAthlet = a.xAthlet
				AND v.xVerein = at.xVerein
				ORDER BY
					heatid
					, ss.Position
			");
		}
		else {								// relay event
			$query = ("
				SELECT
					r.Bahnen
					, rt.Name
					, rt.Typ
					, s.xSerie
					, s.Bezeichnung
					, s.Wind
					, s.Film
					, s.Status
					, ss.xSerienstart
					, ss.Position
					, ss.Rang
					, ss.Qualifikation
					, sf.Name
					, v.Name
					, LPAD(s.Bezeichnung,5,'0') as heatid
					, r.xRunde
					, st.xStart
				FROM
					runde AS r
					, serie AS s
					, serienstart AS ss
					, start AS st
					, staffel AS sf
					, verein AS v
				LEFT JOIN rundentyp AS rt
					ON rt.xRundentyp = r.xRundentyp
				LEFT JOIN anlage AS an
					ON an.xAnlage = s.xAnlage
				WHERE r.xRunde = $round
				AND s.xRunde = r.xRunde
				AND ss.xSerie = s.xSerie
				AND st.xStart = ss.xStart
				AND sf.xStaffel = st.xStaffel
				AND v.xVerein = sf.xVerein
				ORDER BY
					heatid
					, ss.Position
			");
		}

		$result = mysql_query($query);

		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else {
			// initialize variables
			$h = 0;		// heat counter
			$p = 0;		// position counter (to evaluate empty heats
			$tracks = 0;

			$resTable = new GUI_TrackResultTable($round, $layout, $status, $nextRound);

			while($row = mysql_fetch_row($result))
			{
				$p++;			// increment position counter
/*
 *  Heat headerline
 */
				if($h != $row[3])		// new heat
				{
					$tracks = $row[0];	// keep nbr of planned tracks

					// fill previous heat with empty tracks
					if($p > 1) {
						$resTable->printEmptyTracks($p, $tracks, 5+$c);
					}
	
					$h = $row[3];				// keep heat ID
					$p = 1;						// start with track one

					if(is_null($row[1])) {		// only one round
						$title = "$strFinalround $row[4]";
					}
					else {		// more than one round
						$title = "$row[1]: $row[2]$row[4]";
					}

					// increment colspan to include ranking and qualification
					$c = 0;
					if($status == $cfgRoundStatus['results_done']) {
						$c++;
						if($nextRound > 0) {
							$c++;
						}
					}

					$resTable->printHeatTitle($row[3], $row[4], $title, $row[7], $row[6], $row[5]);

					if($relay == FALSE) {	// athlete display
						$resTable->printAthleteHeader();
					}
					else {		// relay display
						$resTable->printRelayHeader();
					}
				}		// ET new heat

/*
 * Empty tracks
 */
				if(($layout == $cfgDisciplineType[$strDiscTypeTrack])
					|| ($layout == $cfgDisciplineType[$strDiscTypeTrackNoWind])
					|| ($layout == $cfgDisciplineType[$strDiscTypeRelay]))
				{
					// current track and athlete's position not identical
					if($p < $row[9]) {
						$p = $resTable->printEmptyTracks($p, ($row[9]-1), 6+$c);
					}
				}	// ET empty tracks

/*
 * Athlete/Relay data lines
 */
				// get performance
				$perf = '';
				$res = mysql_query("
					SELECT
						rs.xResultat
						, rs.Leistung
						, rs.Info
					FROM
						resultat AS rs
					WHERE rs.xSerienstart = $row[8]
					ORDER BY
						rs.Leistung ASC
				");

				if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else
				{
					$resrow = mysql_fetch_row($res);
					if($resrow != NULL) {		// result found
						$perf = AA_formatResultTime($resrow[1]);                         
                        $perfRounded = AA_formatResultTime($resrow[1], true); 
					}
					mysql_free_result($res);
				}	// ET DB error

				// print lines
				if($relay == FALSE) {
					$resTable->printAthleteLine($row[9], $row[12]
							, "$row[13] $row[14]", AA_formatYearOfBirth($row[15])
							, $row[16], AA_formatResultTime($row[19], true), $perfRounded, $row[10], $row[11], $row[18], $row[20]);
				}
				else {	// relay
					
					// get Athletes
					$arrAthletes = array();
					$sql = "SELECT at.Vorname, at.Name, at.Jahrgang FROM
								staffelathlet as sfat
								LEFT JOIN start as st ON sfat.xAthletenstart = st.xStart
								LEFT JOIN anmeldung as a USING(xAnmeldung)
								LEFT JOIN athlet as at USING(xAthlet)
							WHERE
								sfat.xStaffelstart = $row[16]
							AND	sfat.xRunde = $row[15]
							ORDER BY
								sfat.Position";
					$res_at = mysql_query($sql);
					if(mysql_errno() > 0) {		// DB error
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}else{
						while($row_at = mysql_fetch_array($res_at)){
							$arrAthletes[] = array($row_at[1], $row_at[0], AA_formatYearOfBirth($row_at[2]));
						}
					}
					
					$arrAthletes = (count($arrAthletes)>0) ? $arrAthletes : 0;
					
					$resTable->printRelayLine($row[9], $row[12], $row[13] 
							, $perfRounded, $row[10], $row[11], $arrAthletes);

				}
			}

			// Fill last heat with empty tracks for disciplines run in
			// individual tracks
			if(($layout == $cfgDisciplineType[$strDiscTypeTrack])
				|| ($layout == $cfgDisciplineType[$strDiscTypeTrackNoWind])
				|| ($layout == $cfgDisciplineType[$strDiscTypeRelay]))
			{
				if($p > 0) {	// heats set up
					$p++;
					$resTable->printEmptyTracks($p, $tracks, 6+$c);
				}
			}	// ET track disciplines

			$resTable->endTable();
			mysql_free_result($result);
		}		// ET DB error
	}		// ET heat seeding done

}	// End function AA_speaker_Track


}	// AA_SPEAKER_RESULTS_TRACK_LIB_INCLUDED
?>
