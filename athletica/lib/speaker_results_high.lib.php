<?php

/**********
 *
 *	high jump, pole vault results speaker
 *	
 */

if (!defined('AA_SPEAKER_RESULTS_HIGH_LIB_INCLUDED'))
{
	define('AA_SPEAKER_RESULTS_HIGH_LIB_INCLUDED', 1);

function AA_speaker_High($event, $round, $layout)
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
			FROM
				runde AS r
				, serie AS s
				, serienstart AS ss
				, start AS st
				, anmeldung AS a
				, athlet AS at
				, verein AS v
			LEFT JOIN rundentyp_" . $_COOKIE['language'] . " AS rt
				ON rt.xRundentyp = r.xRundentyp
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

		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			// initialize variables
			$h = 0;
			$i = 0;

			$resTable = new GUI_HighResultTable($round, $layout, $status);

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
					$resTable->printHeatTitle($row[2], $row[3], $title, $row[4]);
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

					while($resrow = mysql_fetch_row($res))
					{
						$perf = AA_formatResultMeter($resrow[0]);
						$info = $resrow[1];
						$perfs[] = "$perf ( $info )";
					}	// end loop every tech result acc. programm mode

					mysql_free_result($res);
				}

				$resTable->printAthleteLine($row[6], $row[8], "$row[9] $row[10]"
					, AA_formatYearOfBirth($row[11]), $row[12], AA_formatResultMeter($row[14]), $perfs, $fett, $rank, $row[16], $row[15]);
			}
			$resTable->endTable();
			mysql_free_result($result);
		}		// ET DB error
	}		// ET heat seeding done
}	// End function AA_speaker_High


}	// AA_SPEAKER_RESULTS_HIGH_LIB_INCLUDED
?>
