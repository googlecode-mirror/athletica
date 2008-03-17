<?php

/**********
 *
 *	admin_disciplines.php
 *	------------------
 *
 */
 
$noMeetingCheck = true;

require('./lib/cl_gui_button.lib.php');
require('./lib/cl_gui_dropdown.lib.php');
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	// invalid DB connection
{
	return;
}


//
// Process add or change-request if required
//
if ($_POST['arg']=="add" || $_POST['arg']=="change")
{
	// Error: Empty fields
	if(empty($_POST['short']) || empty($_POST['name']) || empty($_POST['order'])) {
		AA_printErrorMsg($strErrEmptyFields);
	}
	// Error: 'order' must be between 1 and 999
	else if($_POST['order']< 1 || $_POST['order'] > 999) {
		AA_printErrorMsg($strErrInvalidOrder);
	}
	// OK: try to add item
	else if ($_POST['arg']=="add") {
		
		// self made combined events must have an unique code
		$code = 0;
		if($_POST['type'] == $cfgDisciplineType[$strDiscCombined]){
			$res = mysql_query("SELECT MAX(Code) FROM disziplin WHERE Typ = ".$cfgDisciplineType[$strDiscCombined]);
			$maxrow = mysql_fetch_array($res);
			if($maxrow[0] < 9000){	// define codes
				$code = 9000;
			}else{
				$code = $maxrow[0]+1;
			}
		}
		
		mysql_query("
			INSERT INTO disziplin SET 
				Kurzname=\"" . strtoupper($_POST['short']) . "\"
				, Name=\"" . $_POST['name'] . "\"
				, Anzeige=" . $_POST['order'] . "
				, Seriegroesse=" . $_POST['heat'] . "
				, Staffellaeufer=" . $_POST['relay'] . "
				, Typ=" . $_POST['type'] . "
				, Appellzeit=SEC_TO_TIME(". ($_POST['time']*60) .")
				, Stellzeit=SEC_TO_TIME(". ($_POST['mtime']*60) .")
				, Code = $code
		");
	}
	// OK: try to change item
	else if ($_POST['arg']=="change") {
		mysql_query("
			UPDATE disziplin SET 
				Kurzname=\"" . strtoupper($_POST['short']) . "\"
				, Name=\"" . $_POST['name'] . "\"
				, Anzeige=" . $_POST['order'] . "
				, Seriegroesse=" . $_POST['heat'] . "
				, Staffellaeufer=" . $_POST['relay'] . "
				, Typ=" . $_POST['type'] . "
				, Appellzeit=SEC_TO_TIME(". ($_POST['time']*60) .")
				, Stellzeit=SEC_TO_TIME(". ($_POST['mtime']*60) .")
			WHERE xDisziplin=" . $_POST['item']
		);
	}
}
//
// Process delete-request if required
//
else if ($_GET['arg']=="del")
{
	mysql_query("LOCK TABLES wettkampf READ, disziplin WRITE");

	// Check if not used anymore
	if(AA_checkReference("wettkampf", "xDisziplin", $_GET['item']) == 0) {
		mysql_query("DELETE FROM disziplin WHERE xDisziplin=" . $_GET['item']);
	}
	// Error: still in use
	else {
		AA_printErrorMsg($strDiscipline . $strErrStillUsed);
	}
	mysql_query("UNLOCK TABLES");
}


// Check if any error returned from DB
if(mysql_errno() > 0) {
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}

//
// Display current data
//

$page = new GUI_Page('admin_disciplines', TRUE);
$page->startPage();
$page->printPageTitle($strDisciplines);

$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/administration/disciplines.html', $strHelp, '_blank');
$menu->printMenu();

// sort argument
$img_disc="img/sort_inact.gif";
$img_short="img/sort_inact.gif";
$img_time="img/sort_inact.gif";
$img_rel="img/sort_inact.gif";
$img_heat="img/sort_inact.gif";
$img_order="img/sort_inact.gif";
$img_mtime="img/sort_inact.gif";

if ($_GET['arg']=="disc") {
	$argument="Name";
	$img_disc="img/sort_act.gif";
} else if ($_GET['arg']=="short") {
	$argument="Kurzname";
	$img_short="img/sort_act.gif";
} else if ($_GET['arg']=="time") {
	$argument="Appellzeit DESC";
	$img_time="img/sort_act.gif";
} else if ($_GET['arg']=="rel") {
	$argument="Staffellaeufer";
	$img_rel="img/sort_act.gif";
} else if ($_GET['arg']=="heat") {
	$argument="Seriegroesse";
	$img_heat="img/sort_act.gif";
} else if ($_GET['arg']=="order") {
	$argument="Anzeige";
	$img_order="img/sort_act.gif";
} else if ($_GET['arg']=="mtime") {
	$argument="Stellzeit DESC";
	$img_mtime="img/sort_act.gif";
} else {							// relay event
	$argument="Anzeige";
	$img_order="img/sort_act.gif";
}

?>
<p/>
<table class='dialog'>
<tr>
	<th class='dialog'>
		<a href='admin_disciplines.php?arg=disc'>
			<?php echo $strDiscipline; ?>
			<img src='<?php echo $img_disc; ?>'>
		</a>
	</th>
	<th class='dialog'>
		<a href='admin_disciplines.php?arg=short'>
			<?php echo $strShortname; ?>
			<img src='<?php echo $img_short; ?>'>
		</a>
	</th>
	<th class='dialog'><?php echo $strType; ?></th>
	<th class='dialog'>
		<a href='admin_disciplines.php?arg=time'>
			<?php echo $strEnrolementTime; ?>
			<img src='<?php echo $img_time; ?>'>
		</a>
	</th>
	<th class='dialog'>
		<a href='admin_disciplines.php?arg=mtime'>
			<?php echo $strManipulationTime; ?>
			<img src='<?php echo $img_mtime; ?>'>
		</a>
	</th>
	<th class='dialog'>
		<a href='admin_disciplines.php?arg=rel'>
			<?php echo $strRelaysize; ?>
			<img src='<?php echo $img_rel; ?>'>
		</a>
	</th>
	<th class='dialog'>
		<a href='admin_disciplines.php?arg=heat'>
			<?php echo $strHeatSize; ?>
			<img src='<?php echo $img_heat; ?>'>
		</a>
	</th>
	<th class='dialog'>
		<a href='admin_disciplines.php?arg=order'>
			<?php echo $strOrder; ?>
			<img src='<?php echo $img_order; ?>'>
		</a>
	</th>
</tr>

<tr>
	<form action='admin_disciplines.php' method='post'>
	<td class='forms'>
		<input name='arg' type='hidden' value='add'>
		<input class='text' name='name' type='text' maxlength='30'
			value="(<?php echo $strNew; ?>)" ></td>
	<td class='forms'>
		<input class='textmedium' name='short' type='text' maxlength='15' /></td>
<?php
	$dd = new GUI_ConfigDropDown('type', 'cfgDisciplineType', 0);
?>
	<td class='forms_ctr'>
		<input class='nbr' name='time' type='text' maxlength='2' /></td>
	<td class='forms_ctr'>
		<input class='nbr' name='mtime' type='text' maxlength='2' /></td>
	<td class='forms_ctr'>
		<input class='nbr' name='relay' type='text' maxlength='5' /></td>
	<td class='forms_ctr'>
		<input class='nbr' name='heat' type='text' maxlength='5' /></td>
	<td class='forms_ctr'>
		<input class='nbr' name='order' type='text' maxlength='5' /></td>
	<td class='forms'>
		<button type='submit'>
			<?php echo $strSave; ?>
		</button>
	</td>
	</form>	
</tr>
	
<?php
$result = mysql_query("SELECT xDisziplin"
						. ", Kurzname"
						. ", Name"
						. ", Anzeige"
						. ", Seriegroesse"
						. ", Staffellaeufer"
						. ", Typ"
						. ", TRUNCATE(TIME_TO_SEC(Appellzeit)/60, 0)"
						. ", TRUNCATE(TIME_TO_SEC(Stellzeit)/60, 0)"
						. ", Code"
						. " FROM disziplin ORDER BY " . $argument);

$i = 0;
$btn = new GUI_Button('', '');	// create button object

while ($row = mysql_fetch_row($result))
{
	$i++;		// line counter

	if( $i % 2 == 0 ) {		// even row number
		$rowclass = 'odd';
	}
	else {	// odd row number
		$rowclass = 'even';
	}
?>
		<tr class='<?php echo $rowclass; ?>'>
			<form action='admin_disciplines.php#item_<?php echo $row[0]; ?>'
				method='post' name='disc<?php echo $i; ?>'>
			<td class='forms'>
				<input name='arg' type='hidden' value='change' />
				<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
				<input class='text' name='name' type='text' maxlength='30'
					value="<?php echo $row[2]; ?>"
					onChange='submitForm(document.disc<?php echo $i; ?>)' />
			</td>
			<td class='forms'>
				<input class='textmedium' name='short' type='text' maxlength='15'
					value="<?php echo $row[1]; ?>"
					onChange='submitForm(document.disc<?php echo $i; ?>)' />
			</td>
<?php
	$dd = new GUI_ConfigDropDown('type', 'cfgDisciplineType', $row[6], "submitForm(document.disc$i)");
?>
			</td>
			<td class='forms_ctr'>
				<input class='nbr' name='time' type='text' maxlength='2'
					value='<?php echo $row[7]; ?>'
					onChange='submitForm(document.disc<?php echo $i; ?>)' />
			</td>
			<td class='forms_ctr'>
				<input class='nbr' name='mtime' type='text' maxlength='2'
					value='<?php echo $row[8]; ?>'
					onChange='submitForm(document.disc<?php echo $i; ?>)' />
			</td>
			<td class='forms_ctr'>
				<input class='nbr' name='relay' type='text' maxlength='5'
					value='<?php echo $row[5]; ?>'
					onChange='submitForm(document.disc<?php echo $i; ?>)' />
			</td>
			<td class='forms_ctr'>
				<input class='nbr' name='heat' type='text' maxlength='5'
					value='<?php echo $row[4]; ?>'
					onChange='submitForm(document.disc<?php echo $i; ?>)' />
			</td>
			<td class='forms_ctr'>
				<input class='nbr' name='order' type='text' maxlength='5'
					value='<?php echo $row[3]; ?>'
					onChange='submitForm(document.disc<?php echo $i; ?>)' />
			</td>
			<td>
<?php
	if(empty($row[9])){
		$btn->set("admin_disciplines.php?arg=del&item=$row[0]", $strDelete);
		$btn->printButton();
	} else {
		?>
		&nbsp;
		<?php
	}
?>
			</td>
			</form>
		</tr>
<?php
}
mysql_free_result($result);
?>
	
</table>

<script type="text/javascript">
<!--
	scrollDown();
//-->
</script>

<?php
$page->endPage();
?>
