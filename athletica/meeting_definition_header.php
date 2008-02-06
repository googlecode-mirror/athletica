<?php
/**********
 *
 *	meeting_definition_header.php
 *	-----------------------------
 *	
 */

require('./lib/cl_gui_button.lib.php');
require('./lib/cl_gui_dropdown.lib.php');
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');
require('./lib/cl_gui_select.lib.php');

require('./lib/meeting.lib.php');
require('./lib/common.lib.php');


if(AA_connectToDB() == FALSE)	// invalid DB connection
{
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

//
// Process changes to meeting data
//

// change genereal meeting data
if ($_POST['arg']=="change")
{
	AA_meeting_changeData();
}

// Check if any error returned from DB
if(mysql_errno() > 0) {
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}


/***************************
 *
 *		General meeting data
 *
 ***************************/

$page = new GUI_Page('meeting_definition_header');
$page->startPage();
$page->printPageTitle("$strMeeting $strMeetingDefinition: " . $_COOKIE['meeting']);
$menu = new GUI_Menulist();
$menu->addButton('print_meeting_definition.php', $strPrint, '_blank');
$menu->addButton('meeting_definition_event_add.php', $strNewEvent . " ...", 'detail');
$menu->addButton('print_meeting_statistics.php?arg=view', $strStatistics, 'detail');
$menu->addButton('print_meeting_statistics.php?arg=print', $strPrintStatistics, '_blank');
$menu->addButton('print_timetable.php', $strPrintTimetable, '_blank');
$menu->addButton('print_timetable.php?arg=comp', $strPrintTimetableComp, '_blank');
$menu->addButton($cfgURLDocumentation . 'help/meeting/definition.html', $strHelp, '_blank');
$menu->printMenu();
?>

<script type="text/javascript">
<!--
	function check(item)	// stadium has changed; check what to do
	{
		if(item=='stadium')
		{
			if (document.change_def.stadium.value=='new') {	// new stadium
				window.open("admin_stadiums.php", "main");
			}
			else {
				document.change_def.submit();
			}
		}
	}


//-->
</script>

<?php
// get meeting from DB
$result = mysql_query("
	SELECT xMeeting
		, Name
		, Ort
		, DatumVon
		, DatumBis
		, Nummer
		, ProgrammModus
		, xStadion
		, Online
		, Organisator
		, Startgeld
		, StartgeldReduktion
		, Haftgeld
	FROM meeting
	WHERE xMeeting=" . $_COOKIE['meeting_id']
);

if(mysql_errno() > 0)	// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else		// no DB error
{
	$row = mysql_fetch_row($result);
?>
<form action='meeting_definition_header.php' method='post' name='change_def'>
<input name='arg' type='hidden' value='change' />
<input name='item' type='hidden' value='<?php echo $row[0]; ?>' />
<table>
	<tr>
		<th class='dialog'><?php echo $strName; ?></th>
		<th class='dialog' colspan='3'><?php echo $strDateFrom; ?></th>
		<th class='dialog' colspan='3'><?php echo $strDateTo; ?></th>
		<th class='dialog'><?php echo $strPlace; ?></th>
		<th class='dialog'><?php echo $strStadium; ?></th>
		<th class='dialog'><?php echo $strMeetingNbr; ?></th>
		<th class='dialog'><?php echo $strProgramMode; ?></th>
	</tr>
	<tr>
		<td class='forms'><input class='text' name='name' type='text'
			maxlength='60' value="<?php echo $row[1]; ?>"
			onChange='document.change_def.submit()' /></td>
		<?php AA_meeting_printDate('from', $row[3], TRUE); ?>
		<?php AA_meeting_printDate('to', $row[4], TRUE); ?>
		<td class='forms'><input class='text' name='place' type='text'
			maxlength='20' value="<?php echo $row[2]; ?>"
			onChange='document.change_def.submit()' /></td>
		<?php
			$dd = new GUI_StadiumDropDown($row[7]);
		?>
		<td class='forms'><input class='text' name='nbr' type='text'
			maxlength='20' value="<?php echo $row[5]; ?>"
			onChange='document.change_def.submit()' /></td>
		<td class='forms'>
<?php
	$dropdown = new GUI_Select('mode', 1, "document.change_def.submit()");
	foreach($cfgProgramMode as $key=>$value)
	{
		$dropdown->addOption($value['name'], $key);
		if($row[6] == $key) {
			$dropdown->selectOption($key);
		}
	}
	$dropdown->printList();
	
	if($row[8] == 'y'){
		$check = "checked";
	}
?>		</td>
	</tr>
	<tr>
	  <th class='dialog'><?php echo $strOrganizer ?></th>
	  <td colspan='6' class='forms'>
	    <input style="width:98%;" type="text" name="organisator" value="<?php echo $row[9] ?>"
			onchange='document.change_def.submit()' />
      </td>
	  <th class='dialog'><?= $strFee;?></th>
	  <td class='forms'><input name="fee" type="text"
			onchange='document.change_def.submit()' value="<?php echo ($row[10]/100) ?>" size="10" /></td>
	  <th class='dialog'><?= $strDeposit;?></th>
	  <td class='forms'><input name="deposit" type="text"
			onchange='document.change_def.submit()' value="<?php echo ($row[12]/100) ?>" size="10" /></td>

    </tr>
	<tr>
		<th class='dialog' colspan='4'><?php echo $strMeetingWithUpload ?>:</th>
		<th class='dialog'><input type="checkbox" value="yes" name="online"
			onChange='document.change_def.submit()' <?php echo $check ?>><?php echo $strYes ?></th>
			
		<td colspan="2"></td>
	  <th class='dialog'><?= $strFeeReduction;?></th>
	  <td class='forms'><input name="feereduction" type="text"
			onchange='document.change_def.submit()' value="<?php echo ($row[11]/100) ?>" size="10" /></td>
	</tr>
</table>
</form>

<?php
	mysql_free_result($result);
}		// ET DB error

$page->endPage();
