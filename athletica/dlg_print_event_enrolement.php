<?php

/**********
 *
 *	dlg_print_event_enrolement.php
 *	-----------------------------
 *	
 */

require('./lib/cl_gui_dropdown.lib.php');
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}


// enrolement params
$category = 0;
if(isset($_GET['category'])){
	$category = $_GET['category'];
}

$event = 0;
if(isset($_GET['event'])){
	$event = $_GET['event'];
}

$comb = 0;
if(isset($_GET['comb'])){
	$comb = $_GET['comb'];
}


$page = new GUI_Page('dlg_print_event_enrolement');
$page->startPage();
$page->printPageTitle($strPrint . ": " . $strEnrolement);

/*$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/meeting/print_entries.html', $strHelp, '_blank');
$menu->printMenu();*/

?>
<script type="text/javascript">

</script>

<form action='print_event_enrolement.php' method='get' name='printdialog' target="_blank">
<input type="hidden" name="category" value="<?php echo $category ?>">
<input type="hidden" name="event" value="<?php echo $event ?>">
<input type="hidden" name="comb" value="<?php echo $comb ?>">

<table class='dialog'>
	<tr>
		<th class='dialog'><?php echo $strPageBreak ?></th>
	</tr>
	
	<tr>
		<td class='forms'>
			<input type="radio" value="no" name="pagebreak" checked>
			<?php echo $strNoPageBreak ?>
		</td>
	</tr>
	<?php
	if($event == 0 && $comb == 0){
		?>
	<tr>
		<td class='forms'>
			<input type="radio" value="discipline" name="pagebreak">
			<?php echo $strPageBreakDiscipline ?>
		</td>
	</tr>
	<?php
	}
	if($category == 0 && $event == 0 && $comb == 0){
		?>
	<tr>
		<td class='forms'>
			<input type="radio" value="category" name="pagebreak">
			<?php echo $strPageBreakCategory ?>
		</td>
	</tr>
	<?php
	}
	?>
	
</table>
<br>
<input type="submit" value="<?php echo $strPrint ?>" >

</form>

<?php
$page->endPage();
?>
