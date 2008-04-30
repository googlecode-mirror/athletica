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

$catFrom = 0;
if(isset($_GET['catFrom'])){
    $catFrom = $_GET['catFrom'];
}

$catTo = 0;
if(isset($_GET['catTo'])){
    $catTo = $_GET['catTo'];
}
$discFrom = 0;
if(isset($_GET['discFrom'])){
    $discFrom = $_GET['discFrom'];
}
$discTo = 0;
if(isset($_GET['discTo'])){
    $discTo = $_GET['discTo'];
}

$mDate = "";
if(isset($_GET['mDate'])){
    $mDate = $_GET['mDate'];
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
<input type="hidden" name="catFrom" value="<?php echo $catFrom ?>"> 
<input type="hidden" name="catTo" value="<?php echo $catTo ?>"> 
<input type="hidden" name="discFrom" value="<?php echo $discFrom ?>"> 
<input type="hidden" name="discTo" value="<?php echo $discTo ?>"> 
<input type="hidden" name="mDate" value="<?php echo $mDate ?>"> 

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
