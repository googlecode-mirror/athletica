<?php

/**********
 *
 *	speaker_entries.php
 *	-------------------
 *	
 */

require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');
require('./lib/cl_gui_searchfield.lib.php');
require('./lib/cl_performance.lib.php');

require('./lib/common.lib.php');
require('./lib/results.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

$arg = (isset($_GET['arg'])) ? $_GET['arg'] : ((isset($_COOKIE['sort_speakentries'])) ? $_COOKIE['sort_speakentries'] : 'nbr');
setcookie('sort_speakentries', $arg, time()+2419200);

$page = new GUI_Page('speaker_entries');
$page->startPage();
$page->printPageTitle($strEntries. ": " . $_COOKIE['meeting']);
$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/speaker/entries.html', $strHelp, '_blank');
$menu->addSearchfield('speaker_entry.php', '_self', 'post', 'speaker_entries.php');
$menu->printMenu();

$round = 0;
if(!empty($_GET['round'])){
	$round = $_GET['round'];
}
else if(!empty($_POST['round'])) {
	$round = $_POST['round'];
}

$presets = AA_results_getPresets($round);

$relay = AA_checkRelay($presets['event']);	// check, if this is a relay event

//
//	Display data
// ------------
?>
<p />

<table>
	<tr>
		<td class='forms'>
		<?php	AA_printCategorySelection("speaker_entries.php", $presets['category']); ?>
		</td>
		<td class='forms'>
		<?php	AA_printEventSelection("speaker_entries.php", $presets['category'], $presets['event'], "post"); ?>
		</td>
<?php
if($presets['event'] > 0) {		// event selected
	printf("<td class='forms'>\n");
	AA_printRoundSelection("speaker_entries.php", $presets['category'], $presets['event'], $round);
	printf("</td>\n");
}
?>
	</tr>
</table>

<?php
if($round > 0)
{
	// sort argument
	$img_nbr="img/sort_inact.gif";
	$img_name="img/sort_inact.gif";
	$img_club="img/sort_inact.gif";

	if ($arg=="nbr" && !$relay) {        
		$argument="a.Startnummer";
		$img_nbr="img/sort_act.gif";
	} else if ($arg=="name") {
		$argument="at.Name, at.Vorname";
		$img_name="img/sort_act.gif";
	} else if ($arg=="club") {
		$argument="v.Sortierwert, a.Startnummer";
		$img_club="img/sort_act.gif";
	} else if ($arg=="relay") {
		$argument="st.Name";
		$img_name="img/sort_act.gif";
	} else if ($arg=="relay_club") {
		$argument="v.Sortierwert, st.Name";
		$img_club="img/sort_act.gif";
	} else if($relay == FALSE) {		// single event
		$argument="at.Name, at.Vorname";
		$img_name="img/sort_act.gif";
	} else {									// relay event
		$argument="st.Name";
		$img_name="img/sort_act.gif";
	}  
    
    $mergedEvents=AA_getMergedEventsFromEvent($presets['event']);    
      
    if ($mergedEvents!=''){
       $sqlEvent=" IN ". $mergedEvents;        
    }
    else {
        $sqlEvent=" = ". $presets['event'];  
    }   
    
	if($relay == FALSE) 		// single event
	{
		$query = "
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
				, start AS s
				, verein AS v
			WHERE s.xWettkampf " . $sqlEvent . "
			AND a.xAnmeldung = s.xAnmeldung
			AND at.xAthlet = a.xAthlet
			AND v.xVerein = at.xVerein
            AND s.Anwesend = 0
			ORDER BY " . $argument;
	}
	else {							// relay event
		$query = "
			SELECT
				s.xStaffel
				, st.Name
				, v.Name
			FROM
				staffel AS st
				, start AS s
				, verein AS v
			WHERE s.xWettkampf " . $sqlEvent . "
			AND st.xStaffel = s.xStaffel
			AND v.xVerein = st.xVerein
            AND s.Anwesend = 0   
			ORDER BY " . $argument;
	}
    
	$result = mysql_query($query);

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else if(mysql_num_rows($result) > 0)  // data found
	{
		?>
<table class='dialog'>
	<tr>
		<?php
		if($relay == FALSE)		// single event
		{
			?>
		<th class='dialog'>
			<a href='speaker_entries.php?arg=nbr&round=<?php echo $round; ?>'>
				<?php echo $strStartnumber; ?>
				<img src='<?php echo $img_nbr; ?>' />
			</a>
		</th>
		<th class='dialog'>
			<a href='speaker_entries.php?arg=name&round=<?php echo $round; ?>'>
				<?php echo $strName; ?></a>
				<img src='<?php echo $img_name; ?>' />
			</th>
		<th class='dialog'>
		<?php echo $strYear; ?>
		</th>
		<th class='dialog'>
			<a href='speaker_entries.php?arg=club&round=<?php echo $round; ?>'>
				<?php echo $strClub; ?>
				<img src='<?php echo $img_club; ?>' />
			</a>
		</th>
			<?php
		}
		else		// relay event
		{
			?>
		<th class='dialog'>
			<a href='speaker_entries.php?arg=relay&round=<?php echo $round; ?>'>
				<?php echo $strRelays; ?>
				<img src='<?php echo $img_name; ?>' />
			</a>
		</th>
		</th>
		<th class='dialog'>
			<a href='speaker_entries.php?arg=relay_club&round=<?php echo $round; ?>'>
				<?php echo $strClub; ?>
				<img src='<?php echo $img_club; ?>' />
			</a>
		</th>
			<?php
		}
		?>
	</tr>
		<?php

		$i=0;
		$rowclass = "odd";

		while ($row = mysql_fetch_row($result))
		{
			$i++;
			if( $i % 2 == 0 ) {		// even row number
				$rowclass = "even";
			}
			else {	// odd row number
				$rowclass = "odd";
			}

			// print row: onClick show athlete- or relay-details
			?>
	<tr class='<?php echo $rowclass; ?>' onClick='window.open("speaker_entry.php?item=<?php echo $row[0]; ?>&relay=<?php echo $relay; ?>&round=<?php echo $round; ?>", "_self")' style="cursor: pointer;">
			<?php
			if($relay == FALSE)			// single event
			{
				?>
		<td class='forms_right'><?php echo $row[1]; ?></td>
		<td><?php echo "$row[2] $row[3]"; ?></td>
		<td class='forms_ctr'><?php echo AA_formatYearOfBirth($row[4]); ?></td>
		<td><?php echo $row[5]; ?></td>
				<?php
			}
			else							// relay event
			{
				?>
		<td><?php echo $row[1]; ?></td>
		<td><?php echo $row[2]; ?></td>
				<?php
			}
			?>
	</tr>
			<?php
		}
		?>
</table>
		<?php
	}
	else
	{
		AA_printWarningMsg($strNoEntries);
	}
	mysql_free_result($result);
}

$page->endPage();


