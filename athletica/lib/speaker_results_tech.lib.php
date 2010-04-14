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
	
		$temp = mysql_query("
			CREATE TABLE `temp` (
  `id` int(10) NOT NULL auto_increment,
  `athlet` int(10) NOT NULL default '0',
  `leistung` int(11) NOT NULL default '0',
  `rang` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1
		");
	
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		
		$temp = mysql_query("
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
			while($row = mysql_fetch_row($temp))
			{
			$tempsql = "SELECT
						MAX(r.Leistung) As maxLeistung
							FROM
						resultat AS r
					WHERE r.xSerienstart = $row[6]";
				$tempres = mysql_query($tempsql);
				if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else
				{
					while($restemp = mysql_fetch_row($tempres))
					{
				mysql_query("INSERT INTO `temp` ( `id` , `athlet` , `leistung` , `rang`) 
				VALUES ('', '$row[6]', '$restemp[0]' , '1');");
					}
				}
				mysql_free_result($tempres);
				
			}
			mysql_free_result($temp);
		}
		
		$xrang=1;
		$templeistung=0;
		$temprang = mysql_query("SELECT * FROM temp ORDER BY leistung DESC");
		
		while($rowrang = mysql_fetch_row($temprang))
			{
			
				if ($rowrang[2]==$templeistung)
				{
				$rangieren= mysql_query("UPDATE temp SET rang = $yrang WHERE id=$rowrang[0]");
				}
				else
				{
				$rangieren= mysql_query("UPDATE temp SET rang = $xrang WHERE id=$rowrang[0]");
				$yrang=$xrang;
				
				
				}	
			$templeistung=$rowrang[2];
			$xrang=$xrang+1;
			} 
	
	
	
	
	
	
	
	
	
		
		
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
		$arg = (isset($_GET['arg'])) ? $_GET['arg'] : ((isset($_COOKIE['sort_speakres'])) ? $_COOKIE['sort_speakres'] : 'pos');
setcookie('sort_speakres', $arg, time()+2419200);
		// display all athletes
		if ($arg=="nbr" && !$relay) {        
		$argument="a.Startnummer";
		$img_nbr="img/sort_act.gif";
	} else if ($arg=="pos") {
		$argument="ss.Position";
		$img_pos="img/sort_act.gif";
	} else if ($arg=="name") {
		$argument="at.Name, at.Vorname";
		$img_name="img/sort_act.gif";
	} else if ($arg=="club") {
		$argument="v.Name, a.Startnummer";
		$img_club="img/sort_act.gif";
	} else if ($arg=="perf") {
		$argument="st.Bestleistung, ss.Position";
		$img_perf="img/sort_act.gif";
	} else if ($arg=="rang") {
		$argument="t.rang, ss.Position";
		$img_rang="img/sort_act.gif";
	} else if($relay == FALSE) {		// single event
		$argument="ss.Position";
		$img_pos="img/sort_act.gif";
	}
		
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
				, st.Bestleistung
				, at.xAthlet
				, at.Land
				, t.rang
			FROM
				runde AS r
				, serie AS s
				, serienstart AS ss
				, start AS st
				, anmeldung AS a
				, athlet AS at
				, verein AS v
				, wettkampf AS w
				, temp AS t
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
			AND t.athlet = ss.xSerienstart
			ORDER BY 
				" . $argument . "
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
					
						$c++;		// increment colspan to include ranking
					
					$resTable->printHeatTitle($row[2], $row[3], $title , $row[5]);
					$resTable->printAthleteHeader();
				}		// ET new heat

/*
 * Athlete data lines
 */
				
				$perfs = array();
				$fett = array();

				$sql = "SELECT
						r.Leistung
						, r.Info
					FROM
						resultat AS r
					WHERE r.xSerienstart = $row[6]
					ORDER BY
					r.xResultat";
				$res = mysql_query($sql);
				//echo $sql;

				if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else
				{
					
				while($resrow = mysql_fetch_row($res))
					{
						
						$sql2 = "SELECT
						leistung
							FROM
						temp
						WHERE athlet = $row[6]";
				$res2 = mysql_query($sql2);
				while($row2 = mysql_fetch_row($res2))
					{
							
							if ($row2[0]==$resrow[0])
							{
							$fett[]=1;
							}
							else
							{
								$fett[]=0;
							}
						
						$perf = AA_formatResultMeter($resrow[0]);
						if($row[15] == 1) {		// with wind
							$info = $resrow[1];
							$perfs[] = "$perf ( $info )";
						}
						else {
							$perfs[] = "$perf";
						}
					}
					}	// end loop every tech result acc. programm mode

					mysql_free_result($res);
				}

				//print_r($perfs);
				
				$resTable->printAthleteLine($row[7], $row[9], "$row[10] $row[11]"
					, AA_formatYearOfBirth($row[12]), $row[13], AA_formatResultMeter($row[16]) ,$perfs, $fett, $row[19], $row[18], $row[17]);
			}
			$resTable->endTable();
			mysql_free_result($result);
		}		// ET DB error
	}		// ET heat seeding done

	$temp = mysql_query("
			DROP TABLE IF EXISTS `temp`
		");

}	// End Function AA_speaker_Tech

}	// AA_SPEAKER_RESULTS_TECH_LIB_INCLUDED
?>
