<?php

/**********
 *
 *	admin_roundtypes.php
 *	--------------------
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
	if((empty($_POST['type'])) || (empty($_POST['name'])))
	{
		AA_printErrorMsg($strErrEmptyFields);
	}
	// OK: try to add item
	else if ($_POST['arg']=="add")
	{
		mysql_query("
			INSERT INTO rundentyp SET 
				Typ=\"" . $_POST['type'] . "\"
				, Name=\"" . $_POST['name'] . "\"
				, Wertung=" . $_POST['valtype']);
	}
	// OK: try to change item
	else if ($_POST['arg']=="change")
	{
		mysql_query("
			UPDATE rundentyp SET
				Typ=\"" . $_POST['type'] . "\"
				, Name=\"" . $_POST['name'] . "\"
				, Wertung=" . $_POST['valtype'] . "
			WHERE xRundentyp=" . $_POST['item']
		);
	}
}
//
// Process delete-request if required
//
else if ($_GET['arg']=="del")
{
	mysql_query("LOCK TABLES runde READ, rundentyp WRITE");

	// Still in use?
	$rows = AA_checkReference("runde", "xRundentyp", $_GET['item']);

	// OK: not used anymore
	if($rows == 0)
	{
		mysql_query("DELETE FROM rundentyp WHERE xRundentyp=" . $_GET['item']);
	}
	// Error: still in use
	else
	{
		AA_printErrorMsg($strRoundtype . $strErrStillUsed);
	}
	mysql_query("UNLOCK TABLES");
}

// Check if any error returned from DB
if(mysql_errno() > 0)
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}


//
//	Display current data
//

$page = new GUI_Page('admin_roundtypes', TRUE);
$page->startPage();
$page->printPageTitle($strRoundtypes);

$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/administration/roundtypes.html', $strHelp, '_blank');
$menu->printMenu();
?>
<p/>
<table class='dialog'>
	<tr>
		<th class='dialog'><?php echo $strType; ?></th>
		<th class='dialog'><?php echo $strName; ?></th>
		<th class='dialog'><?php echo $strEvaluation; ?></th>
	</tr>

	<tr>
		<form action='admin_roundtypes.php' method='post'>
		<td class='forms'>
			<input name='arg' type='hidden' value='add'>
			<input class='textshort' name='type' type='text' maxlength='2'></td>
		<td class='forms'>
			<input class='text' name='name' type='text' maxlength='20'
				value="(<?php echo $strNew; ?>)" ></td>
<?php
$dd = new GUI_ConfigDropDown('valtype', 'cfgEvalType', 0);
?>

		<td class='forms'>
			<button type='submit'>
				<?php echo $strSave; ?>
			</button>
		</td>
		</form>	
	</tr>

<?php
$result = mysql_query("SELECT xRundentyp"
							. ", Typ"
							. ", Name"
							. ", Wertung"
							. " FROM rundentyp ORDER BY Typ");

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
		<form action='admin_roundtypes.php#item_<?php echo $row[0]; ?>'
			method='post' name='rnd<?php echo $i; ?>'>
		<td class='forms'>
			<input name='arg' type='hidden' value='change'>
			<input name='item' type='hidden' value='<?php echo $row[0]; ?>'>
			<input class='textshort' name='type' type='text' maxlength='2'
				value="<?php echo $row[1]; ?>"
				onChange='submitForm(document.rnd<?php echo $i; ?>)'>
		</td>
		<td class='forms'>
			<input class='text' name='name' type='text' maxlength='20'
				value="<?php echo $row[2]; ?>"
				onChange='submitForm(document.rnd<?php echo $i; ?>)'>
		</td>
	<?php
	$dd = new GUI_ConfigDropDown('valtype', 'cfgEvalType', $row[3], "submitForm(document.rnd$i)");
	?>
		<td>
	<?php
	$btn->set("admin_roundtypes.php?arg=del&item=$row[0]", $strDelete);
	$btn->printButton();
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
