<?php

/**********
 *
 *	meeting_entries_startnumbers.php
 *	--------------------------------
 *	
 */

require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(empty($_COOKIE['meeting_id'])) {
	AA_printErrorMsg($GLOBALS['strNoMeetingSelected']);
}

//
// check if a heat is assigned
//
$heats_done = "false";
$res = mysql_query("
		SELECT xRunde FROM
			runde 
			LEFT JOIN wettkampf USING (xWettkampf)
		WHERE
			(Status = ".$cfgRoundStatus['heats_done']."
			OR Status = ".$cfgRoundStatus['results_in_progress']."
			OR Status = ".$cfgRoundStatus['results_done'].")
			AND xMeeting = ".$_COOKIE['meeting_id']
);

if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	if(mysql_num_rows($res) > 0){
		$heats_done = "true";
	}
}



if($_GET['arg'] == 'assign')
{
	if ($_GET['sort']!="del")		// assign startnumbers
	{
		// sort argument
		if ($_GET['sort']=="name") {
		  $argument="athlet.Name, athlet.Vorname";
	  	} else if ($_GET['sort']=="nbr") {
		  $argument="anmeldung.Startnummer";
		} else if ($_GET['sort']=="cat") {
		  $argument="kategorie.Anzeige, athlet.Name, athlet.Vorname";
		} else if ($_GET['sort']=="club") {
		  $argument="verein.Sortierwert, athlet.Name, athlet.Vorname";
		} else if ($_GET['sort']=="club_cat") {
		  $argument="verein.Sortierwert, kategorie.Anzeige, athlet.Name, athlet.Vorname";
		} else if ($_GET['sort']=="cat_club") {
		  $argument="kategorie.Anzeige, verein.Sortierwert, athlet.Name, athlet.Vorname";
		} else {
		  $argument="athlet.Name, athlet.Vorname";
		}
		
		
		// check on assign per category. if contest cat is choosen process in a special way
		if(isset($_GET['percontestcat'])){
			
			if(isset($_GET['persvmteam'])){
				$argument = "team.Name, ".$argument;
			}
			$argument = "wettkampf.xKategorie, ".$argument;
			
			if((!empty($_GET['teamgap'])) || ($_GET['teamgap'] == '0'))  {
				$teamgap = $_GET['teamgap'];	// nbr gap between each team
			}
			else {
				$teamgap = 10;	// default
			}
			
			//
			// Read athletes
			//
			
			mysql_query("
				LOCK TABLES
					athlet READ
					, kategorie READ
					, verein READ
					, anmeldung wRITE
					, wettkampf READ
					, start READ
					, team READ
			");
			
			$result = mysql_query("
				SELECT
					DISTINCT(anmeldung.xAnmeldung)
					, wettkampf.xKategorie
					, athlet.xVerein
					, anmeldung.xTeam
				FROM
					anmeldung
					, athlet
					, kategorie
					, verein
					, start
					, wettkampf
					LEFT JOIN team ON team.xTeam = anmeldung.xTeam
				WHERE anmeldung.xMeeting = " . $_COOKIE['meeting_id'] . "
				AND anmeldung.xAthlet = athlet.xAthlet
				AND athlet.xVerein = verein.xVerein
				AND start.xAnmeldung = anmeldung.xAnmeldung
				AND wettkampf.xWettkampf = start.xWettkampf
				AND kategorie.xKategorie = wettkampf.xKategorie
				ORDER BY
					$argument
			");
			
			if(mysql_errno() > 0)		// DB error
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else if(mysql_num_rows($result) > 0)  // data found
			{
			  $k = 0;	// initialize current category
			  $v = 0;	// initialize current club
			  $t = 0;	// current team
			  $limit = 0;	// hold limit
			  
			  // Assign startnumbers
			  while ($row = mysql_fetch_row($result))
			  {
				// set per category from, to
				
				if ($k != $row[1]){			// new category
					$nbr = $_GET["of2_$row[1]"];
					$nbr==0?1:$nbr;
					$limit = $_GET["to2_$row[1]"];
				}elseif($t != $row[3]			// new team
					&& $teamgap > 0
					&& $t > 0
					&& isset($_GET['persvmteam']))
				{
					$nbr += $teamgap;
				}else{
					$nbr++;
					if(($limit > 0 && $nbr > $limit) || $limit == 0){
						$nbr = 0;
						$limit = 0;
					}
				}
					
				mysql_query("
					UPDATE anmeldung SET
						Startnummer='$nbr'
					WHERE xAnmeldung = $row[0]
				");
				
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				
				$k = $row[1];	// keep current category
				$v = $row[2];	// keep current club
				$t = $row[3];	// keep current team
				
				
			  }
			  mysql_free_result($result);
			}						// ET DB error
			mysql_query("UNLOCK TABLES");
			
		}
		
		//
		// assign per athletes category
		//
		else{
			
			if(isset($_GET['percategory'])){
				if(isset($_GET['persvmteam'])){
					$argument = "team.Name, ".$argument;
				}
				$argument = "kategorie.Anzeige, ".$argument;
			}
			
			// assignment rules
			if(!empty($_GET['start'])) {
			  $nbr = $_GET['start'] - 1;		// first number
			}
			else {
				$nbr = $cfgNbrStartWith - 1;	// default
			}
			
			if((!empty($_GET['catgap'])) || ($_GET['catgap'] == '0')) {
			  $catgap = $_GET['catgap'];		// nbr gap between each category
			}
			else {
				$catgap = $cfgNbrCategoryGap;	// default
			}
			
			if((!empty($_GET['clubgap'])) || ($_GET['clubgap'] == '0'))  {
			  $clubgap = $_GET['clubgap'];	// nbr gap between each club
			}
			else {
			  $clubgap = $cfgNbrClubyGap;	// default
			}
			
			if((!empty($_GET['teamgap'])) || ($_GET['teamgap'] == '0'))  {
				$teamgap = $_GET['teamgap'];	// nbr gap between each team
			}
			else {
				$teamgap = 10;	// default
			}
			
			//
			// Read athletes
			//
			
			mysql_query("
				LOCK TABLES
					athlet READ
					, kategorie READ
					, verein READ
					, anmeldung wRITE
					, team READ
			");
			
			$result = mysql_query("
				SELECT
					anmeldung.xAnmeldung
					, anmeldung.xKategorie
					, athlet.xVerein
					, anmeldung.xTeam
				FROM
					anmeldung
					, athlet
					, kategorie
					, verein
					LEFT JOIN team ON team.xTeam = anmeldung.xTeam
				WHERE anmeldung.xMeeting = " . $_COOKIE['meeting_id'] . "
				AND anmeldung.xAthlet = athlet.xAthlet
				AND anmeldung.xKategorie = kategorie.xKategorie
				AND athlet.xVerein = verein.xVerein
				ORDER BY
					$argument
			");
			
			if(mysql_errno() > 0)		// DB error
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else if(mysql_num_rows($result) > 0)  // data found
			{
			  $k = 0;	// initialize current category
			  $v = 0;	// initialize current club
			  $t = 0;	// current team
			  $limit = 0;	// hold limit
			  
			  // Assign startnumbers
			  while ($row = mysql_fetch_row($result))
			  {
				if(empty($_GET['percategory'])){
					
					if (($v != $row[2])		// new club
						&& ($clubgap > 0)		// gap between clubs
						&& ($v > 0)				// not first row
						&& (($_GET['sort']=="club_cat")	// gap after cat
							|| ($_GET['sort']=="club")
							|| ($_GET['sort']=="cat_club")))
					{
					  $nbr = $nbr + $clubgap;	// calculate next number
					}
					else if (($k != $row[1])		// new category
						&& ($catgap > 0)				// gap between categories
						&& ($k > 0)						// not first row
						&& (($_GET['sort']=="club_cat")	// gap after cat
						  || ($_GET['sort']=="cat")
						  || ($_GET['sort']=="cat_club")))
					{
					  $nbr = $nbr + $catgap;				// calculate next number
					}
					else {
						$nbr++;
					}
					
				}else{ // set per category from, to
					
					if ($k != $row[1]){			// new category
						$nbr = $_GET["of_$row[1]"];
						$nbr==0?1:$nbr;
						$limit = $_GET["to_$row[1]"];
					}elseif($t != $row[3]			// new team
						&& $teamgap > 0
						&& $t > 0
						&& isset($_GET['persvmteam']))
					{
						$nbr += $teamgap;
					}else{
						$nbr++;
						if(($limit > 0 && $nbr > $limit) || $limit == 0){
							$nbr = 0;
							$limit = 0;
						}
					}
					
				}
					
				mysql_query("
					UPDATE anmeldung SET
						Startnummer='$nbr'
					WHERE xAnmeldung = $row[0]
				");
				
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				
				$k = $row[1];	// keep current category
				$v = $row[2];	// keep current club
				$t = $row[3];	// keep current team
				
			}
			mysql_free_result($result);
			}						// ET DB error
			mysql_query("UNLOCK TABLES");
			
		}
	}
	else		// delete startnumbers
	{
		mysql_query("LOCK TABLE anmeldung WRITE");

	  	mysql_query("
			UPDATE anmeldung SET
				Startnummer = 0
			WHERE xMeeting=" . $_COOKIE['meeting_id']
		);

		if(mysql_errno() > 0)
		{
		  AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}

		mysql_query("UNLOCK TABLES");
	}
}

//
// show dialog 
//

$page = new GUI_Page('meeting_entries_startnumbers');
$page->startPage();
$page->printPageTitle($strAssignStartnumbers);

if($_GET['arg'] == 'assign')	// refresh list
{
	?>
	<script>
		window.open("meeting_entrylist.php", "list")
	</script>
	<?php
}
?>

<script>

function check_rounds(){
	
	//if(true == <?php echo $heats_done ?>){
		
		check = confirm("<?php echo $strStartNrConfirm ?>");
		return check;
	//}
	
}

function select_PerCategory(){
	
	e = document.getElementById("percategory");
	if(!e.checked){
		e.click();
	}
	
}

function select_PerContestCat(){
	
	e = document.getElementById("percontestcat");
	if(!e.checked){
		e.click();
	}
	
}

function select_PerSvmTeam(arg){
	
	e = document.getElementById("persvmteam"+arg);
	if(!e.checked){
		e.click();
	}
	
}

function select_CheckCat(o){
	
	if(o.checked){
		if(o.name == "percontestcat"){
			e = document.getElementById("percategory");
			e.checked = false;
		}else{
			e = document.getElementById("percontestcat");
			e.checked = false;
		}
	}
	
}

</script>

<form action='meeting_entries_startnumbers.php' method='get'>
<input type='hidden' name='arg' value='assign'>
<table class='dialog'>
<tr>
	<th class='dialog'><?php echo $strSortBy; ?></th>
	<th class='dialog' colspan='2'><?php echo $strRules; ?></th>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' value='name' checked>
			<?php echo $strName; ?></input>
	</td>
	<td class='dialog'>
		<?php echo $strBeginningWith; ?>
	</td>
	<td>
		<input class='nbr' type='text' name='start' maxlength='5' value='<?php echo $cfgNbrStartWith; ?>'>
	</td>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' value='cat'>
			<?php echo $strCategory; ?></input>
	</td>
	<td colspan='2'>
		<?php echo $strGapBetween; ?>
	</td>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' value='club'>
			<?php echo $strClub; ?></input>
	</td>
	<td class='dialog'>
		<?php echo $strCategory; ?>
	</td>
	<td class='dialog'>
		<input class='nbr' type='text' name='catgap' maxlength='4' value='<?php echo $cfgNbrCategoryGap; ?>'>
	</td>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' value='club_cat'>
			<?php echo $strClub . " & " . $strCategory; ?></input>
	</td>
	<td class='dialog'>
		<?php echo $strClub; ?>
	</td>
	<td class='dialog'>
		<input class='nbr' type='text' name='clubgap' maxlength='4' value='<?php echo $cfgNbrClubGap; ?>'>
	</td>
</tr>
<tr>
	<td class='dialog' colspan="3">
		<input type='radio' name='sort' value='cat_club'>
			<?php echo $strCategory . " & " . $strClub; ?></input>
	</td>
</tr>
<tr>
	<td class='dialog' colspan="3">
		<hr>
		<input type="checkbox" name="percategory" id="percategory" value="percategory"
			onchange="select_CheckCat(this)">
		<?php echo $strPerCategory; ?>
	</th>
</tr>
<?php

// get all used categories in this meeting
$res = mysql_query("	SELECT
				DISTINCT(a.xKategorie)
				, k.Kurzname
			FROM
				anmeldung as a
				, kategorie as k
			WHERE
				a.xMeeting = ".$_COOKIE['meeting_id']."
			AND	a.xKategorie = k.xKategorie
			ORDER BY
				k.Anzeige");
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	while($row = mysql_Fetch_array($res)){
		?>
<tr>
	<td class='dialog'><?php echo $row[1] ?></td>
	<td class='forms'>
		<?php echo $strOf ?>
		<input type="text" size="3" value="0" name="of_<?php echo $row[0] ?>" onchange="select_PerCategory()">
	</td>
	<td class='forms'>
		<?php echo $strTo ?>
		<input type="text" size="3" value="0" name="to_<?php echo $row[0] ?>" onchange="select_PerCategory()">
	</td>
</tr>
		<?php
	}
}

?>

<tr>
	<td class='dialog' colspan="2">
		<input type="checkbox" name="persvmteam" id="persvmteam1" value="persvmteam">
		<?php echo $strPerSvmTeam; ?>
	</th>
	<td class='forms'>
		<?php echo $strGap.":" ?>
		<input type="text" size="3" value="10" name="teamgap" onchange="select_PerSvmTeam('1')">
	</td>
</tr>

<tr>
	<td class='dialog' colspan="3">
		<hr>
		<input type="checkbox" name="percontestcat" id="percontestcat" value="percontestcat"
			onchange="select_CheckCat(this)">
		<?php echo $strPerContestCategory; ?>
	</th>
</tr>
<?php

// get all used categories in contest
$res = mysql_query("	SELECT 
				DISTINCT(w.xKategorie)
				, k.Kurzname
			FROM
				wettkampf as w
				, kategorie as k
			WHERE
				w.xKategorie = k.xKategorie
			AND	w.xMeeting = ".$_COOKIE['meeting_id']."
			ORDER BY
				k.Anzeige");
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	while($row = mysql_Fetch_array($res)){
		?>
<tr>
	<td class='dialog'><?php echo $row[1] ?></td>
	<td class='forms'>
		<?php echo $strOf ?>
		<input type="text" size="3" value="0" name="of2_<?php echo $row[0] ?>" onchange="select_PerContestCat()">
	</td>
	<td class='forms'>
		<?php echo $strTo ?>
		<input type="text" size="3" value="0" name="to2_<?php echo $row[0] ?>" onchange="select_PerContestCat()">
	</td>
</tr>
		<?php
	}
}

?>

<tr>
	<td class='dialog' colspan="2">
		<input type="checkbox" name="persvmteam" id="persvmteam2" value="persvmteam">
		<?php echo $strPerSvmTeam; ?>
	</th>
	<td class='forms'>
		<?php echo $strGap.":" ?>
		<input type="text" size="3" value="10" name="teamgap" onchange="select_PerSvmTeam('2')">
	</td>
</tr>

<tr>
	<td class='dialog' colspan = 3>
		<hr>
		<input type='radio' name='sort' value='del'>
			<?php echo $strDeleteStartnumbers; ?></input>
	</td>
</tr>

</table>

<p />

<table>
<tr>
	<td>
		<button type='submit' onclick="return check_rounds();">
			<?php echo $strAssign; ?>
	  	</button>
	</td>
</tr>
</table>

</form>

</body>
</html>
