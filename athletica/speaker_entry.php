<?php

/**********
 *
 *	speaker_entry.php
 *	-----------------
 *	
 */

require('./lib/cl_gui_page.lib.php');
require('./lib/cl_gui_menulist.lib.php');

require('./lib/common.lib.php');
require('./lib/results.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

$round = 0;
if(!empty($_GET['round'])){
	$round = $_GET['round'];
}


$page = new GUI_Page('speaker_entry');
$page->startPage();
$page->printPageTitle($strEntry. ": " . $_COOKIE['meeting']);

$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/speaker/entry.html', $strHelp, '_blank');
if(!empty($_POST['back'])) {
	$menu->addButton($_POST['back'], $strBack);
}
$menu->printMenu();

?>
<p />
<?php

$item = 0;
if(!empty($_GET['item'])){
	$item = $_GET['item'];
}

$relay = 0;
if(!empty($_GET['relay'])){
	$relay = $_GET['relay'];
}


//
// set up search parameters, if any 
//
unset($searchparam);

if($_POST['arg']=='search')
{
	$name = '';
	$nbr = '';
	if(is_numeric($_POST['searchfield'])) {
		$searchparam = " AND a.Startnummer = '" . $_POST['searchfield'] . "'";
	}	
	else {
		$searchparam = " AND at.Name = '" . $_POST['searchfield'] . "'";
	}

	$result = mysql_query("
		SELECT
			a.xAnmeldung
			, a.Startnummer
			, at.Name
			, at.Vorname
			, at.Jahrgang
			, v.Name
		FROM
			anmeldung AS a
			, athlet AS at
			, verein AS v
		WHERE a.xMeeting = " . $_COOKIE['meeting_id'] . "
		AND at.xAthlet = a.xAthlet
		AND v.xVerein = at.xVerein "
		. $searchparam . "
		ORDER BY
			at.Name
			, at.Vorname"
	);

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	// more than one hit: show selection list
	else if(mysql_num_rows($result) > 1)
	{

		$i=0;
		$rowclass = "odd";

		?>
<table class='dialog'>
	<tr>
		<th class='dialog'><?php echo $strStartnumber; ?></th>
		<th class='dialog'><?php echo $strName; ?></th>
		<th class='dialog'><?php echo $strYear; ?></th>
		<th class='dialog'><?php echo $strClub; ?></th>
	</tr>
		<?php

		while ($row = mysql_fetch_row($result))
		{
			$i++;
			if( $i % 2 == 0 ) {		// even row number
				$rowclass = "even";
			}
			else {	// odd row number
				$rowclass = "odd";
			}
			?>
	<tr class='<?php echo $rowclass; ?>' onClick='window.open("speaker_entry.php?item=<?php echo $row[0]; ?>", "_self")'>
		<td class='forms_right'><?php echo $row[1]; ?></td>
		<td><?php echo "$row[2] $row[3]"; ?></td>
		<td class='forms_ctr'><?php echo AA_formatYearOfBirth($row[4]); ?></td>
		<td><?php echo $row[5]; ?></td>
	</tr>
			<?php
		}

		?>
</table>
		<?php

		mysql_free_result($result);
	}

	// one hit: save item to display further down
	else if(mysql_num_rows($result) == 1)
	{
		$row = mysql_fetch_row($result);
		$item = $row[0];
	}
	else
	{
		AA_printErrorMsg($strErrAthleteNotFound);
	}
}


// 
// show athlete's or relay's data
//
if ($item > 0)
{
	// athlet
	if ($relay == 0) {
		$query = "
			SELECT
				a.Startnummer
				, d.Name
				, d.Typ
				, at.Name
				, at.Vorname
				, at.Jahrgang
				, k.Name
				, v.Name
				, t.Name
				, r.Leistung
				, r.Info
				, rt.Typ
				, s.Wind
			FROM
				anmeldung AS a
				, athlet AS at
				, disziplin AS d
				, kategorie AS k
				, resultat AS r
				, runde AS ru
				, serie AS s
				, start  AS st
				, serienstart AS ss
				, verein AS v
				, wettkampf AS w
			LEFT  JOIN team AS t
				ON a.xTeam = t.xTeam
			LEFT  JOIN rundentyp AS rt
				ON ru.xRundentyp = rt.xRundentyp
			WHERE a.xAnmeldung = $item
			AND at.xAthlet = a.xAthlet
			AND v.xVerein = at.xVerein
			AND k.xKategorie = a.xKategorie
			AND st.xAnmeldung = a.xAnmeldung
			AND w.xWettkampf = st.xWettkampf
			AND d.xDisziplin = w.xDisziplin
			AND ss.xStart = st.xStart
			AND r.xSerienstart = ss.xSerienstart
			AND s.xSerie = ss.xSerie
			AND ru.xRunde = s.xRunde
			ORDER BY
				d.Anzeige
				, ru.Datum
				, ru.Startzeit
				, r.xResultat
		";
	}
	// relay
	else {
		$query = "
			SELECT
				s.Name
				, d.Name
				, k.Name
				, v.Name
				, t.Name
				, s.xStaffel
				, r.Leistung
				, rt.Typ
			FROM
				staffel AS s
				, disziplin AS d
				, kategorie AS k
				, resultat AS r 
				, runde AS ru
				, serie AS se
				, start AS st
				, serienstart AS ss
				, verein AS v
				, wettkampf AS w
			LEFT JOIN team AS t
				ON s.xTeam = t.xTeam
			LEFT  JOIN rundentyp AS rt
				ON ru.xRundentyp = rt.xRundentyp
			WHERE s.xStaffel = $item
			AND v.xVerein = s.xVerein
			AND k.xKategorie = s.xKategorie
			AND st.xStaffel = s.xStaffel
			AND w.xWettkampf = st.xWettkampf
			AND d.xDisziplin = w.xDisziplin
			AND ss.xStart = st.xStart
			AND r.xSerienstart = ss.xSerienstart
			AND se.xSerie = ss.xSerie
			AND ru.xRunde = se.xRunde
			ORDER BY
				d.Anzeige
				, ru.Datum
				, ru.Startzeit
		";
	}

	$result = mysql_query($query);

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else if(mysql_num_rows($result) == 0)  // data found
	{
		AA_printErrorMsg($strErrNoResults);
		mysql_free_result($result);
	}
	else
	{
		$i = 0;
		$disc = '';
		while($row = mysql_fetch_row($result))
		{
			// athlete
			if($relay == 0)
			{
				if($i == 0)
				{
					?>
<table class='dialog'>
	<tr>
		<th class='dialog'><?php echo $strName; ?></th>
		<th class='dialog'><?php echo "$row[3] $row[4]"; ?></th>
	</tr>
	<tr>
		<th class='dialog'><?php echo $strStartnumberLong; ?></th>
		<td class='dialog'><?php echo $row[0]; ?></td>
	</tr>
	<tr>
		<th class='dialog'><?php echo $strCategory; ?></th>
		<td class='dialog'><?php echo $row[6]; ?></td>
	</tr>
	<tr>
		<th class='dialog'><?php echo $strYear; ?></th>
		<td class='dialog'><?php echo AA_formatYearOfBirth($row[5]); ?></td>
	</tr>
	<tr>
		<th class='dialog'><?php echo $strClub; ?></th>
		<td class='dialog'><?php echo $row[7]; ?></td>
	</tr>
	<tr>
		<th class='dialog'><?php echo $strTeam; ?></th>
		<td class='dialog'><?php echo $row[8]; ?></td>
	</tr>
</table>
<p/>
<table class='dialog'>
	<tr>
		<th class='dialog' colspan='4'><?php echo $strDisciplines; ?></th>
	</tr>
				<?php
				}

				if($disc != $row[1]) 	// new discipline
				{
					?>
	<tr>
		<td class='dialog'><?php echo $row[1]; ?></td>
		<td class='dialog'><?php echo $row[11]; ?></td>
					<?php
				}
				else
				{
					?>
	<tr>
		<td />
		<td />
					<?php
				}

				// track disciplines (timed)
				if(($row[2] == $cfgDisciplineType[$strDiscTypeNone])
					|| ($row[2]== $cfgDisciplineType[$strDiscTypeTrack])
					|| ($row[2]== $cfgDisciplineType[$strDiscTypeTrackNoWind])
					|| ($row[2]== $cfgDisciplineType[$strDiscTypeDistance])
					|| ($row[2]== $cfgDisciplineType[$strDiscTypeRelay]))
				{
					$perf = AA_formatResultTime($row[9]);
				}
				// technical disciplines
				else 
				{
					$perf = AA_formatResultMeter($row[9]);
				}
				// track discipline with wind
				if($row[2]== $cfgDisciplineType[$strDiscTypeTrack])
				{
					$info = $row[12];
				}
				// technical disciplines
				else 
				{
					$info = $row[10];
				}

				?>
		<td class='dialog_right'><?php echo $perf; ?></td>
		<td class='dialog_right'><?php echo $info; ?></td>
	</tr>
				<?php
			}
			// relay
			else
			{
				if($i == 0)		// only once
				{
					?>
<table class='dialog'>
	<tr>
		<th class='dialog'><?php echo $strRelay; ?></th>
		<th class='dialog'><?php echo $row[0]; ?></th>
	</tr>
	<tr>
		<th class='dialog'><?php echo $strCategory; ?></th>
		<td class='dialog'><?php echo $row[2]; ?></td>
	</tr>
	<tr>
		<th class='dialog'><?php echo $strClub; ?></th>
		<td class='dialog'><?php echo $row[3]; ?></td>
	</tr>
	<tr>
		<th class='dialog'><?php echo $strTeam; ?></th>
		<td class='dialog'><?php echo $row[4]; ?></td>
	</tr>
</table>
<p/>
<table class='dialog'>
	<tr>
		<th class='dialog' colspan='4'><?php echo $strDiscipline; ?></th>
	</tr>
					<?php
				}

				if($disc != $row[1]) 	// new discipline
				{
					?>
	<tr>
		<td class='dialog_right'><?php echo $row[1]; ?></td>
		<td class='dialog_right'><?php echo $row[7]; ?></td>
					<?php
				}
				else
				{
					?>
	<tr>
		<td />
		<td />
					<?php
				}
				?>
		<td class='dialog_right'><?php echo AA_formatResultTime($row[6]); ?></td>
	</tr>
				<?php
			}

			$i++;
			$disc = $row[1]; 		// keep discipline
		}

		mysql_free_result($result);
		// add relay athletes
		if($relay > 0)
		{
			$result = mysql_query("
				SELECT
					a.xAnmeldung
					, a.Startnummer
					, at.Name
					, at.Vorname
				FROM
					athlet AS at
					, anmeldung AS a
					, start AS ss
					, staffelathlet AS sta
					, start AS st
				WHERE ss.xStaffel = $item
				AND sta.xStaffelstart = ss.xStart
				AND sta.xAthletenstart = st.xStart
				AND st.xAnmeldung = a.xAnmeldung
				AND a.xAthlet = at.xAthlet
				ORDER BY
					sta.Position
			");

			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else
			{
				?>
	<tr>
		<td/>
		<td class='dialog' colspan='2'>
			<table>
				<?php
				while ($row = mysql_fetch_row($result))
				{
					?>
					<tr>
					<td class='forms_right'><?php echo $row[1]; ?></td>
					<td><?php echo "$row[2] $row[3]"; ?></td>
					</tr>
					<?php
				}
				?>
			</table>
		</td>
	</tr>
				<?php
				mysql_free_result($result);
			}
		}
		?>
</table>
		<?php
	}

}

$page->endPage();


