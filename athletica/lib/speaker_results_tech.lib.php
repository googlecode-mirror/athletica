<?php

/**********
 *
 *	tech results speaker
 *	
 */

if (!defined('AA_SPEAKER_RESULTS_TECH_LIB_INCLUDED'))
{
	define('AA_SPEAKER_RESULTS_TECH_LIB_INCLUDED', 1);

function AA_speaker_Tech($event, $round, $layout)
{
	require('./lib/cl_gui_resulttable.lib.php');
	require('./config.inc.php');
	require('./lib/common.lib.php');
	require('./lib/results.lib.php');

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
	// Heat seeding completed, ready to enter results
	else if($status >= $cfgRoundStatus['heats_done'])
	{
		// show link to rankinglist if results done
		if($status == $cfgRoundStatus['results_done'])
		{
			$menu = new GUI_Menulist();
			$menu->addButton("print_rankinglist.php?round=$round&type=single&formaction=speaker", $GLOBALS['strRankingList']);
			$menu->addButton("print_rankinglist.php?round=$round&type=single&formaction=speaker&show_efforts=sb_pb", $GLOBALS['strRankingListEfforts']);
			$menu->printMenu();
			echo "<p/>";
		}

		$prog_mode = AA_results_getProgramMode();
	
		// display all athletes
		$result = mysql_query("
			SELECT
				rt.Name
				, rt.Typ
				, s.xSerie
				, s.Bezeichnung
				, s.Wind
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
				, w.Windmessung
			FROM
				runde AS r
				, serie AS s
				, serienstart AS ss
				, start AS st
				, anmeldung AS a
				, athlet AS at
				, verein AS v
				, wettkampf AS w
			LEFT JOIN rundentyp AS rt
				ON rt.xRundentyp = r.xRundentyp
			WHERE r.xRunde = $round
			AND w.xWettkampf = r.xWettkampf
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

		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			// initialize variables
			$h = 0;
			$i = 0;
			$r = 0;

			$resTable = new GUI_TechResultTable($round, $layout, $status);

			while($row = mysql_fetch_row($result))
			{
/*
 *  Heat headerline
 */
				if($h != $row[2])		// new heat
				{
					$h = $row[2];				// keep heat ID

					if(is_null($row[0])) {		// only one round
						$title = "$strFinalround $row[3]";
					}
					else {		// more than one round
						$title = "$row[0]: $row[1]$row[3]";
					}

					$c = 0;
					if($status == $cfgRoundStatus['results_done']) {
						$c++;		// increment colspan to include ranking
					}
					$resTable->printHeatTitle($row[2], $row[3], $title , $row[5]);
					$resTable->printAthleteHeader();
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
						resultat AS r
					WHERE r.xSerienstart = $row[6]
				");

				if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else
				{
					if($status == $cfgRoundStatus['results_done']) {
						$rank = $row[8];
					}

					while($resrow = mysql_fetch_row($res))
					{
						$perf = AA_formatResultMeter($resrow[0]);
						if($row[15] == 1) {		// with wind
							$info = $resrow[1];
							$perfs[] = "$perf ( $info )";
						}
						else {
							$perfs[] = "$perf";
						}
					}	// end loop every tech result acc. programm mode

					mysql_free_result($res);
				}

				print_r($row);
				
				$resTable->printAthleteLine($row[7], $row[9], "$row[10] $row[11]"
					, AA_formatYearOfBirth($row[12]), $row[13], $perfs, $rank);
			}
			$resTable->endTable();
			mysql_free_result($result);
		}		// ET DB error
	}		// ET heat seeding done

}	// End Function AA_speaker_Tech

}	// AA_SPEAKER_RESULTS_TECH_LIB_INCLUDED
?>
