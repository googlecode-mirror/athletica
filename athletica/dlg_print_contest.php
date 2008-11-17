<?php

/**********
 *
 *	dlg_print_contest.php
 *	---------------------
 *	
 */
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');
require('./lib/cl_gui_dropdown.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	// invalid DB connection
{
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

// get presets
$round = $_GET['round'];
if(!empty($_POST['round'])) {
	$round = $_POST['round'];
}

// get number of athletes/relays with valid result
/*$result = mysql_query("
	SELECT
		COUNT(*)
	FROM
		serienstart AS ss
		, serie AS s
	WHERE s.xSerie = ss.xSerie
	AND s.xRunde = $round
");*/
$sql = "SELECT
			COUNT(*)
		FROM
			serienstart AS ss
		LEFT JOIN 
			serie AS s USING(xSerie)
		WHERE
			s.xRunde = ".$round.";";
$result = mysql_query($sql);
 
if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else
{
	$row = mysql_fetch_row($result);
	$present = $row[0];
	mysql_free_result($result);
}


// get nbr of heats
$result = mysql_query("
	SELECT
		COUNT(*)
	FROM serie
	WHERE xRunde = $round
");

if(mysql_errno() > 0) {		// DB error
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else {
	$row = mysql_fetch_row($result);
	$tot_heats = $row[0];
}
mysql_free_result($result);


// read round information
/*$result = mysql_query("
	SELECT
		DATE_FORMAT(r.Datum, '$cfgDBdateFormat')
		, TIME_FORMAT(r.Startzeit, '$cfgDBtimeFormat')
		, r.Bahnen
		, rt.Name
		, w.xWettkampf
		, k.Name
		, d.Name
		, d.Typ
		, r.QualifikationSieger
		, r.QualifikationLeistung
	FROM
		runde AS r
		, wettkampf AS w
		, kategorie AS k
		, disziplin AS d
	LEFT JOIN rundentyp AS rt
	ON r.xRundentyp = rt.xRundentyp
	WHERE r.xRunde = $round
	AND w.xWettkampf = r.xWettkampf
	AND k.xKategorie = w.xKategorie
	AND d.xDisziplin = w.xDisziplin
");*/
$sql = "SELECT
			  DATE_FORMAT(r.Datum, '".$cfgDBdateFormat."')
			, TIME_FORMAT(r.Startzeit, '".$cfgDBtimeFormat."')
			, r.Bahnen
			, rt.Name
			, w.xWettkampf
			, k.Name
			, d.Name
			, d.Typ
			, r.QualifikationSieger
			, r.QualifikationLeistung
            , rt.Typ
		FROM
			runde AS r
		LEFT JOIN
			rundentyp AS rt USING(xRundentyp)
		LEFT JOIN
			wettkampf AS w ON(w.xWettkampf = r.xWettkampf)
		LEFT JOIN
			kategorie AS k USING(xKategorie)
		LEFT JOIN 
			disziplin AS d ON(d.xDisziplin = w.xDisziplin)
		WHERE
			r.xRunde = ".$round.";";
$result = mysql_query($sql);

if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else
{
	$row = mysql_fetch_row($result);
	$event = $row[4];				// event ID
	$tracks = $row[2];			// nbr of tracks

	$relay = AA_checkRelay($event);
	if($relay == FALSE) {		// single event
		$XXXPresent = $strAthletesPresent;
	}
	else {							// relay event
		$XXXPresent = $strRelaysPresent;
	}

	$page = new GUI_Page('print_contest');
	$page->startPage();
	$page->printPageTitle($strPrintHeats);

	$menu = new GUI_Menulist();
	$menu->addButton($cfgURLDocumentation . 'help/event/print_heats.html', $strHelp, '_blank');
	$menu->printMenu();

	$page->printSubTitle("$row[6], $row[5]");
?>
<form action='print_contest.php' method='post' target='_blank' name='qual'>
<input name='round' type='hidden' value='<?php echo $round; ?>' />
<input name='present' type='hidden' value='<?php echo $present; ?>' />
<table class='dialog'>
	<tr>
		<th class='dialog' colspan='2'><?php echo $row[3]; ?></th>
	</tr>
	<tr>
		<td class='dialog'><?php echo $strTime; ?></td>
		<th class='dialog'><?php echo $row[0] . ", " . $row[1]; ?></th>
	</tr>
	<tr>
		<td class='dialog'><?php echo $strNbrOfHeats; ?></td>
		<th class='dialog'><?php echo $tot_heats; ?></th>
	</tr>
	<tr>
		<td class='dialog'><?php echo $strPageBreakHeat; ?></td>
		<th class='dialog'><input type="checkbox" name="heatpagebreak" value="yes"></th>
	</tr>
	<tr>
		<td class='dialog'><?php echo $XXXPresent; ?></td>
		<th class='dialog'><?php echo $present; ?></th>
	</tr>
	<?php
	//
	// display text field for entering the number of attempts to be printed
	//
	if($row[7] == $cfgDisciplineType[$strDiscTypeJump]
		|| $row[7] == $cfgDisciplineType[$strDiscTypeJumpNoWind]
		|| $row[7] == $cfgDisciplineType[$strDiscTypeThrow])
	{
		?>
	<tr>
		<td class='dialog'><?php echo $strCountAttempts; ?>:</td>
		<th class='dialog'><input type="text" value="" name="countattempts" size="3"></th>
	</tr> 
    <tr> 
        <td class='dialog'><?php echo $strOnlyBestResult; ?>:</td>     
        <th class='dialog'><input type='checkbox' name='onlyBest' value='y'/> 
                 
         </th>  
     </tr>         
		<?php
	} 
	?>
<?php    
	if($row[2] > 0) {		// discipline run in tracks
?>
	<tr>
		<td class='dialog'><?php echo $strNbrOfTracks; ?></td>
		<?php
			$dd = new GUI_ConfigDropDown('tracks', 'cfgTrackOrder', $tracks, '', true);
		?>
	</tr>
<?php
	}

	$qual_top = $row[8];
	$qual_perf = $row[9];

	mysql_free_result($result);
}

// show qualification form if another round follows
$nextRound = AA_getNextRound($event, $round);
$combined = AA_checkCombined($event);
$teamsm = AA_checkTeamSM($event);      

$quali = TRUE;
if ($row[10] == 'S' || $row[10] == 'O'){
    $quali = FALSE;                                     // double round "serie"" or "(ohne)"  --> no need of qualification 
}

if($nextRound > 0 && !$combined && !$teamsm && $quali)		// next round found
{
	/*$result = mysql_query("
		SELECT
			rt.Name
		FROM
			runde AS r
		LEFT JOIN rundentyp AS rt
		ON r.xRundentyp = rt.xRundentyp
		WHERE r.xRunde = $nextRound
	");*/
	$sql = "SELECT
				rt.Name
			FROM
				runde AS r
			LEFT JOIN
				rundentyp AS rt USING(xRundentyp)
			WHERE
				r.xRunde = ".$nextRound.";";
	$result = mysql_query($sql);

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		$row = mysql_fetch_row($result);
?>
	<tr>
		<td class='dialog' colspan='2'><hr />
		<script type="text/javascript">
<!--
	function calcTotal()
	{
		if((isNaN(document.qual.qual_top.value) == true)
			|| ((parseInt(document.qual.qual_top.value)) < 0))
		{
			document.qual.qual_top.value = 0;
		}

		if((isNaN(document.qual.qual_perf.value) == true) 
			|| ((parseInt(document.qual.qual_perf.value)) < 0))
		{
			document.qual.qual_perf.value = 0;
		}

		document.getElementById("total").firstChild.nodeValue =
			(parseInt(<?php echo $tot_heats; ?>)
				* parseInt(document.qual.qual_top.value))
			+ parseInt(document.qual.qual_perf.value);
	}
//-->
		</script>
		</td>
	</tr>

	<tr>
		<th class='dialog' colspan='2'>
			<?php echo $strQualification . " " . $row[0]; ?></th>
	</tr>
	<tr>
		<td class='dialog'><?php echo $strQualifyTop; ?></td>
		<td class='forms'>
			<input name='next_round' type='hidden' value='<?php echo $nextRound; ?>' />
			<input class='nbr' name='qual_top' type='text' maxlength='4'
				value='<?php echo $qual_top; ?>' onChange='calcTotal()' />
		</td>
	</tr>
	<tr>
		<td class='dialog'><?php echo $strQualifyPerformance; ?></td>
		<td class='forms'>
			<input class='nbr' name='qual_perf' type='text' maxlength='4'
				value='<?php echo $qual_perf; ?>' onChange='calcTotal()' />
		</td>
	</tr>
	<tr>
		<td class='dialog'><?php echo $strTotal; ?></td>
		<th class='dialog' id='total'>
			<?php echo ($tot_heats * $qual_top) + $qual_perf; ?>
		</th>
	</tr>

<?php
		mysql_free_result($result);
	}		// ET DB error
}		// next round found
?>
</table>

<p />

<table>
	<tr>
		<td>
			<button type='submit'>
				<?php echo $strTerminateSeeding; ?>
		  	</button>
		</td>
		<td>
			<button name='reset' type='reset' onClick='window.open("event_heats.php?round=<?php echo $round; ?>", "main")'>
			<?php echo $strBack; ?>
			</button>
		</td>
	</tr>
</table>
</form>

<?php
$page->endPage();
