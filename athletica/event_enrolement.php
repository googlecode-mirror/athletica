<?php
/**********
 *
 *	event_enrolement.php
 *	--------------------
 *	
 */

require('./lib/cl_gui_button.lib.php');
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	// invalid DB connection
{
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}


// get presets
if(!empty($_GET['category'])) {
	$category = $_GET['category'];
}
else {
	$category = 0;
}

if(!empty($_GET['event'])) {
	$event = $_GET['event'];
}
else {
	$event = 0;
}

if(!empty($_GET['round'])) {
	$round = $_GET['round'];
}
else {
	$round = 0;
}

if(!empty($_GET['comb'])) {
	$comb = $_GET['comb'];
	list($cCat, $cCode) = explode("_", $comb);
}
else {
	$comb = 0;
	$cCat = 0;
	$cCode = 0;
}


//
//	Check if relay event
//
$relay = AA_checkRelay($event);
$combined = AA_checkCombined($event, $round);

//
// Update absent status
//
if($_GET['arg'] == 'change')
{
	mysql_query("LOCK TABLES serienstart READ, wettkampf WRITE, start WRITE");
	if($comb > 0){ // if combined set present for all starts
		/*$res = mysql_query("SELECT * FROM
				serienstart
				, start
				, wettkampf
			WHERE
				serienstart.xStart = start.xStart
			AND	start.xWettkampf = wettkampf.xWettkampf
			AND	wettkampf.xKategorie = $cCat
			AND	wettkampf.Mehrkampfcode = $cCode
			AND	wettkampf.xMeeting = ".$_COOKIE['meeting_id']."
			AND	start.xAnmeldung = ". $_GET['entry']);*/
		$sql = "SELECT
					*
				FROM
					serienstart
				LEFT JOIN 
					start USING(xStart)
				LEFT JOIN
					wettkampf USING(xWettkampf)
				WHERE
					wettkampf.xKategorie = ".$cCat."
				AND
					wettkampf.Mehrkampfcode = ".$cCode."
				AND
					wettkampf.xMeeting = ".$_COOKIE['meeting_id']."
				AND
					start.xAnmeldung = ".$_GET['entry'].";";
		$res = mysql_query($sql);
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			if(mysql_num_rows($res) > 0){
				AA_printErrorMsg($strErrAthleteSeeded);
			}else{
				
				if(empty($_GET['present'])) {		// athlete absent
					$present = 1;
				}
				else {
					$present = 0;
				}
				/*mysql_query("UPDATE start, wettkampf SET
						start.Anwesend='$present'
					WHERE	start.xWettkampf = wettkampf.xWettkampf
					AND	wettkampf.xKategorie = $cCat
					AND	wettkampf.Mehrkampfcode = $cCode
					AND	wettkampf.xMeeting = ".$_COOKIE['meeting_id']."
					AND	start.xAnmeldung='" . $_GET['entry'] . "'
					");*/
				$sql = "UPDATE
							start
						LEFT JOIN 
							wettkampf USING(xWettkampf)
						SET 
							start.Anwesend = '".$present."' 
						WHERE
							wettkampf.xKategorie = ".$cCat."
						AND	
							wettkampf.Mehrkampfcode = ".$cCode."
						AND	
							wettkampf.xMeeting = ".$_COOKIE['meeting_id']."
						AND	
							start.xAnmeldung='" . $_GET['entry']."';";
				mysql_query($sql);
				//echo $sql;
				if(mysql_errno() > 0){
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				
			}
		}
	}else{ // single normal event
		if(AA_checkReference("serienstart", "xStart", $_GET['item']) != 0) // seeded!
		{
			AA_printErrorMsg($strErrAthleteSeeded);
		}
		else
		{
			if(empty($_GET['present'])) {		// athlete absent
				$present = 1;
			}
			else {
				$present = 0;
			}
	
			mysql_query("UPDATE start SET"
						. " Anwesend='$present'"
						. " WHERE xStart='" . $_GET['item'] . "'");
		}
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
	}
	
	mysql_query("UNLOCK TABLES");
}

//
// Update round status at termination
//
else if($_GET['arg'] == 'terminate')
{
	mysql_query("LOCK TABLES round WRITE, wettkampf READ");
	
	// get rounds which enrolement is pending for termination
	if($comb > 0){	// combined event -> get all rounds
		/*$result = mysql_query("
			SELECT
				runde.xRunde
			FROM
				runde
				, wettkampf
			WHERE runde.xWettkampf = wettkampf.xWettkampf
			AND wettkampf.xKategorie = $cCat
			AND wettkampf.xMeeting = ".$_COOKIE['meeting_id']."
			AND wettkampf.Mehrkampfcode = $cCode
			AND (runde.Status = " . $cfgRoundStatus['enrolement_pending'] . "
			OR runde.Status = " . $cfgRoundStatus['open'] . ")
			ORDER BY
				runde.Datum ASC
				, runde.Startzeit ASC
		");*/
		$sql = "SELECT
					runde.xRunde
				FROM
					runde
				LEFT JOIN
					wettkampf USING(xWettkampf)
				WHERE
					wettkampf.xKategorie = ".$cCat."
				AND
					wettkampf.xMeeting = ".$_COOKIE['meeting_id']."
				AND
					wettkampf.Mehrkampfcode = ".$cCode."
				AND
					(runde.Status = ".$cfgRoundStatus['enrolement_pending']." 
				OR	runde.Status = ".$cfgRoundStatus['open'].")
				ORDER BY
					  runde.Datum ASC
					, runde.Startzeit ASC;";
		$result = mysql_query($sql);
	}else{		// normal single event
		$result = mysql_query("
			SELECT
				xRunde
			FROM
				runde
			WHERE xWettkampf = $event
			AND (Status = " . $cfgRoundStatus['enrolement_pending'] . "
			OR Status = " . $cfgRoundStatus['open'] . ")
			ORDER BY
				Datum ASC
				, Startzeit ASC
		");
	}
	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	
	while($row = mysql_fetch_array($result)){
		mysql_query("
			UPDATE runde SET
				Status = " . $cfgRoundStatus['enrolement_done'] . "
			WHERE xRunde = ".$row[0]."
		");
		if(mysql_errno() > 0)
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
	}
	
	mysql_query("UNLOCK TABLES");
}

$arg = (isset($_GET['arg'])) ? $_GET['arg'] : ((isset($_COOKIE['sort_enrolement'])) ? $_COOKIE['sort_enrolement'] : 'nbr');
setcookie('sort_enrolement', $arg, time()+2419200);

//
//	Display enrolement list
//

$page = new GUI_Page('event_enrolement', TRUE);
$page->startPage();
$page->printPageTitle($strEnrolement . ": " . $_COOKIE['meeting']);

$menu = new GUI_Menulist();
$menu->addButton("dlg_print_event_enrolement.php?category=$category&event=$event&comb=$comb", $strPrint." ...", '_self');
$menu->addButton($cfgURLDocumentation . 'help/event/enrolement.html', $strHelp, '_blank');
$menu->printMenu();

// sort argument
$img_nbr="img/sort_inact.gif";
$img_name="img/sort_inact.gif";
$img_club="img/sort_inact.gif";

if ($arg=="nbr") {
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
} else {							// relay event
	$argument="st.Name";
	$img_name="img/sort_act.gif";
}
?>
<p />

<table><tr>
	<td class='forms'>
		<?php	AA_printCategorySelection('event_enrolement.php', $category, 'get'); ?>
	</td>
	<td class='forms'>
		<?php	AA_printEventSelection('event_enrolement.php', $category, $event); ?>
	</td>
	<td class='forms'>
		<?php	AA_printEventCombinedSelection('event_enrolement.php', $category, $comb, 'get'); ?>
	</td>
</tr></table>

<?php

if($event > 0 || $comb > 0)
{
	// check if enrolement pending for this event
	if($comb > 0){ // combined event selected
		/*$result = mysql_query("
			SELECT
				xRunde
			FROM
				runde as r
				, wettkampf as w
			WHERE w.xWettkampf = r.xWettkampf
			AND w.xKategorie = $cCat
			AND w.Mehrkampfcode = $cCode
			AND w.xMeeting = ".$_COOKIE['meeting_id']."
			AND (r.Status = " . $cfgRoundStatus['enrolement_pending'] . "
			OR r.Status = " . $cfgRoundStatus['open'] . ")
			ORDER BY
				r.Datum ASC
				, r.Startzeit ASC
		");*/
		$sql = "SELECT
					xRunde
				FROM
					runde AS r
				LEFT JOIN
					wettkampf AS w USING(xWettkampf)
				WHERE
					w.xKategorie = ".$cCat."
				AND
					w.Mehrkampfcode = ".$cCode."
				AND
					w.xMeeting = ".$_COOKIE['meeting_id']."
				AND
					(r.Status = ".$cfgRoundStatus['enrolement_pending']."
				OR
					r.Status = ".$cfgRoundStatus['open'].")
				ORDER BY
					  r.Datum ASC
					, r.Startzeit ASC;";
		$result = mysql_query($sql);
	}else{ // normal single event
		$result = mysql_query("
			SELECT
				xRunde
			FROM
				runde
			WHERE xWettkampf = $event
			AND (Status = " . $cfgRoundStatus['enrolement_pending'] . "
			OR Status = " . $cfgRoundStatus['open'] . ")
			ORDER BY
				Datum ASC
				, Startzeit ASC
		");
	}

	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else if(mysql_num_rows($result) > 0)  // data found
	{
		$row = mysql_fetch_row($result);
		$round=$row[0];

		$btn = new GUI_Button("event_enrolement.php?arg=terminate&round=$row[0]&category=$category&event=$event&comb=$comb", $strTerminateEnrolement);
		$btn->printButton();
?>
<p/>
<?php
		mysql_free_result($result);
	}
	if($_GET['arg'] == 'terminate' && $comb == 0){
		$btn = new GUI_Button("dlg_heat_seeding.php?round=$round", $strHeatSeeding);
		$btn->printButton();
	}

?>
<p/>
<table class='dialog'>
	<tr>
		<th class='dialog'>
			<?php echo $strPresent; ?>
		</th>
<?php
	if($relay == FALSE)		// single event
	{
?>
		<th class='dialog'>
			<a href='event_enrolement.php?arg=nbr&category=<?php echo $category; ?>&event=<?php echo $event; ?>'><?php echo $strStartnumber; ?>
				<img src='<?php echo $img_nbr; ?>' />
			</a>
		</th>
		<th class='dialog'>
			<a href='event_enrolement.php?arg=name&category=<?php echo $category; ?>&event=<?php echo $event; ?>'><?php echo $strName; ?>
				<img src='<?php echo $img_name; ?>' />
			</a>
		</th>
		<th class='dialog'>
		<?php echo $strYear; ?>
		</th>
		<th class='dialog'>
			<a href='event_enrolement.php?arg=club&category=<?php echo $category; ?>&event=<?php echo $event; ?>'><?php echo $strClub; ?>
				<img src='<?php echo $img_club; ?>' />
			</a>
		</th>
<?php
	}
	else		// relay event
	{
?>
		<th class='dialog'>
			<a href='event_enrolement.php?arg=nbr&category=<?php echo $category; ?>&event=<?php echo $event; ?>'><?php echo $strStartnumber; ?>
				<img src='<?php echo $img_nbr; ?>' />
			</a>
		</th>
		<th class='dialog'>
			<a href='event_enrolement.php?arg=name&category=<?php echo $category; ?>&event=<?php echo $event; ?>'><?php echo $strName; ?>
				<img src='<?php echo $img_name; ?>' />
			</a>
		</th>
		<th class='dialog'>
		<?php echo $strYear; ?>
		</th>
		<th class='dialog'>
			<a href='event_enrolement.php?arg=relay&category=<?php echo $category; ?>&event=<?php echo $event; ?>'><?php echo $strRelays; ?>
				<img src='<?php echo $img_name; ?>' />
			</a>
		</th>
		<th class='dialog'>
			<a href='event_enrolement.php?arg=relay_club&category=<?php echo $category; ?>&event=<?php echo $event; ?>'><?php echo $strClub; ?>
				<img src='<?php echo $img_club; ?>' />
			</a>
		</th>
<?php
	}
?>
	</tr>

<?php
	
	//
	// read merged rounds an select all events
	//
	$sqlEvents = "";
	$eventMerged = false;
	$result = mysql_query("SELECT xRundenset FROM rundenset
				WHERE	xRunde = $round
				AND	xMeeting = ".$_COOKIE['meeting_id']);
	if(mysql_errno() > 0){
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}else{
		$rsrow = mysql_fetch_array($result); // get round set id
		mysql_free_result($result);
	}
	
	if($rsrow[0] > 0){
		/*$result = mysql_query("	SELECT r.xWettkampf FROM
						rundenset as s
						, runde as r
					WHERE
						s.xMeeting = ".$_COOKIE['meeting_id']."
					AND	s.xRundenset = $rsrow[0]
					AND	r.xRunde = s.xRunde");*/
		$sql = "SELECT
					r.xWettkampf
				FROM
					rundenset AS s
				LEFT JOIN 
					runde AS r USING(xRunde)
				WHERE
					s.xMeeting = ".$_COOKIE['meeting_id']."
				AND
					s.xRundenset = ".$rsrow[0].";";
		$result = mysql_query($sql);
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			$sqlEvents .= " s.xWettkampf = ".$event." ";
		}else{
			if(mysql_num_rows($result) == 0){ // no merged rounds
				$sqlEvents .= " s.xWettkampf = ".$event." ";
			}else{
				$eventMerged = true;
				$sqlEvents .= "( s.xWettkampf = ".$event." ";
				while($row = mysql_fetch_array($result)){
					if($row[0] != $event){ // if there are additional events (merged rounds) add them as sql statement
						$sqlEvents .= " OR s.xWettkampf = ".$row[0]." ";
					}
				}
				$sqlEvents .= ") ";
			}
			mysql_free_result($result);
		}
	}else{
		$sqlEvents .= " s.xWettkampf = ".$event." ";
	}
	
	if($relay == FALSE) {		// single event
		if($comb > 0){ // combined, select entries over each discipline
			/*$query = "SELECT s.xStart"
					. ", s.Anwesend"
					. ", a.Startnummer"
					. ", at.Name"
					. ", at.Vorname"
					. ", at.Jahrgang"
					. ", v.Name"
					. ", a.xAnmeldung"
					. " FROM anmeldung AS a"
					. ", athlet AS at"
					. ", start AS s"
					. ", verein AS v"
					. ", wettkampf AS w"
					. " WHERE s.xWettkampf = w.xWettkampf"
					. " AND w.xKategorie = $cCat"
					. " AND w.Mehrkampfcode = $cCode"
					. " AND w.xMeeting = ".$_COOKIE['meeting_id']
					. " AND s.xAnmeldung = a.xAnmeldung"
					. " AND a.xAthlet = at.xAthlet"
					. " AND at.xVerein = v.xVerein"
					. " ORDER BY " . $argument;*/
			$sql = "SELECT
						  s.xStart
						, s.Anwesend
						, a.Startnummer
						, at.Name
						, at.Vorname
						, at.Jahrgang
						, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo) 
						, a.xAnmeldung
					FROM
						anmeldung AS a
					LEFT JOIN
						athlet AS at USING(xAthlet)
					LEFT JOIN 
						start AS s ON(s.xAnmeldung = a.xAnmeldung)
					LEFT JOIN 
						verein AS v ON(at.xVerein = v.xVerein)
					LEFT JOIN
						wettkampf AS w ON(s.xWettkampf = w.xWettkampf)
					WHERE
						w.xKategorie = ".$cCat."
					AND
						w.Mehrkampfcode = ".$cCode."
					AND
						w.xMeeting = ".$_COOKIE['meeting_id']."
					ORDER BY
						".$argument.";";
			$query = $sql;
		}else{ // no combined
			/*$query = "SELECT s.xStart"
					. ", s.Anwesend"
					. ", a.Startnummer"
					. ", at.Name"
					. ", at.Vorname"
					. ", at.Jahrgang"
					. ", v.Name"
					. " FROM anmeldung AS a"
					. ", athlet AS at"
					. ", start AS s"
					. ", verein AS v"
					. " WHERE " //s.xWettkampf = " . $event
					. $sqlEvents
					. " AND s.xAnmeldung = a.xAnmeldung"
					. " AND a.xAthlet = at.xAthlet"
					. " AND at.xVerein = v.xVerein"
					. " ORDER BY " . $argument;*/
			$sql = "SELECT
						  s.xStart
						, s.Anwesend
						, a.Startnummer
						, at.Name
						, at.Vorname
						, at.Jahrgang
						, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo) 
					FROM
						anmeldung AS a
					LEFT JOIN
						athlet AS at USING(xAthlet)
					LEFT JOIN 
						start AS s ON(s.xAnmeldung = a.xAnmeldung)
					LEFT JOIN 
						verein AS v ON(at.xVerein = v.xVerein)
					LEFT JOIN
						wettkampf AS w ON(s.xWettkampf = w.xWettkampf)
					WHERE
						".$sqlEvents."
					ORDER BY
						".$argument.";";
			$query = $sql;
		}
	}
	else {							// relay event
		//
		// get each athlete from all registered relays
		//
		/*$query = "SELECT s2.xStart"
				. ", s2.Anwesend"
				. ", st.Name"
				. ", v.Name"
				. ", a.Startnummer"
				. ", at.Name"
				. ", at.Vorname"
				. ", at.Jahrgang"
				. " FROM staffel AS st"
				. ", start AS s"
				. ", verein AS v"
				. ", staffelathlet as stat"
				. ", start as s2"
				. ", anmeldung as a"
				. ", athlet as at"
				. " WHERE " //s.xWettkampf = " . $event
				. $sqlEvents
				. " AND s.xStaffel = st.xStaffel"
				. " AND st.xVerein = v.xVerein"
				. " AND stat.xStaffelstart = s.xStart"
				. " AND s2.xStart = stat.xAthletenstart"
				. " AND a.xAnmeldung = s2.xAnmeldung"
				. " AND at.xAthlet = a.xAthlet"
				. " GROUP BY stat.xAthletenstart"
				. " ORDER BY " . $argument;*/
		$sql = "SELECT
					  s2.xStart
					, s2.Anwesend
					, st.Name
					, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo) 
					, a.Startnummer
					, at.Name
					, at.Vorname
					, at.Jahrgang
				FROM
					staffel AS st
				LEFT JOIN 
					start AS s USING(xStaffel)
				LEFT JOIN 
					verein AS v ON(st.xVerein = v.xVerein)
				LEFT JOIN
					staffelathlet AS stat ON(stat.xStaffelstart = s.xStart)
				LEFT JOIN
					start AS s2 ON(s2.xStart = stat.xAthletenstart)
				LEFT JOIN
					anmeldung AS a USING(xAnmeldung)
				LEFT JOIN
					athlet AS at USING(xAthlet)
				WHERE
					".$sqlEvents."
				GROUP BY 
					stat.xAthletenstart
				ORDER BY
					".$argument.";";
		$query = $sql;
	}
	
	$result = mysql_query($query);
	
	if(mysql_errno() > 0)		// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else if(mysql_num_rows($result) > 0)  // data found
	{
		$i=0;
		$rowclass = "odd";
		$xEntry = 0;
		
		while ($row = mysql_fetch_row($result))
		{
			if($comb > 0 && $xEntry == $row[7]){ // combined, merge starts
				continue;
			}
			$xEntry = $row[7];
			
			if($a != 0) {				// not first row
				printf("</tr>\n");
			}
			
			$i++;
			if( $i % 2 == 0 ) {		// even row number
				$rowclass = "even";
			}
			else {	// odd row number
				$rowclass = "odd";
			}
			printf("<tr class='$rowclass'>\n");
			printf("<form action='event_enrolement.php#$row[0]' method='get'"
					. " name='change_present_$i'>");
			
			if($row[1] == 0) {	// present (zero)
				$present = 0;
				$checked = "checked";
			}
			else {					// absent (not zero)
				$present = 1;
				$checked = "";
			}
			
			printf("<td class='forms_ctr'>");
			printf("<input name='arg' type='hidden' value='change' />");
			printf("<input name='item' type='hidden' value='$row[0]' />");
			printf("<input name='entry' type='hidden' value='$xEntry' />");
			printf("<input name='category' type='hidden' value='$category' />");
			printf("<input name='event' type='hidden' value='$event' />");
			printf("<input name='comb' type='hidden' value='$comb' />");
			printf("<input type='checkbox' name='present' value='$present' $checked"
					. " onClick='submitForm(document.change_present_$i)' />\n");
			printf("</td>\n");
			
			if($relay == FALSE)			// single event
			{
				printf("<td class='forms_right'><a name='$row[0]'></a>$row[2]</td>");		// startnummer
				printf("<td>$row[3] $row[4]</td>");		// name
				printf("<td class='forms_ctr'>" . $row[5] . "</td>");	// year
				printf("<td>$row[6]</td>\n");		// club name
			}
			else							// relay event
			{
				printf("<td class='forms_right'><a name='$row[0]'></a>$row[4]</td>");		// startnummer
				printf("<td>$row[5] $row[6]</td>");		// name
				printf("<td class='forms_ctr'>" . $row[7] . "</td>");	// year
				printf("<td>$row[2]</td>");		// relay
				printf("<td>$row[3]</td>\n");		// club name
			}

			printf("</form>\n");
			printf("</tr>\n");
		}
	}
	printf("</table>\n");
	mysql_free_result($result);
}
?>

<script type="text/javascript">
<!--
	scrollDown();
//-->
</script>
<?php

$page->endPage();
?>
