<?php

/**********
 *
 *	speaker_rankinglists.php
 *	------------------------
 *	
 */

require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');
require('./lib/results.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

$page = new GUI_Page('speaker_rankinglists');
$page->startPage();
$page->printPageTitle($strRankingLists . ": " . $_COOKIE['meeting']);
$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/speaker/rankinglists.html', $strHelp, '_blank');
$menu->printMenu();


// get presets
$round = 0;
if(!empty($_GET['round'])){
	$round = $_GET['round'];
}
else if(!empty($_POST['round'])) {
	$round = $_POST['round'];
}

$presets = AA_results_getPresets($round);

?>
<table><tr>
	<td class='forms'>
		<?php	AA_printCategorySelection('speaker_rankinglists.php'
			, $presets['category'], 'post'); ?>
	</td>
	<td class='forms'>
		<?php	AA_printEventSelection('speaker_rankinglists.php'
			, $presets['category'], $presets['event'], 'post'); ?>
	</td>
<?php
if($presets['event'] > 0) {		// event selected
?>
	<td class='forms'>
		<?php AA_printRoundSelection('speaker_rankinglists.php'
			, $presets['category'] , $presets['event'], $round); ?>
	</td>
<?php
}
?>

<form action='print_rankinglist.php' method='get' name='printdialog'>

<input type='hidden' name='category' value='<?php echo $presets['category']; ?>'>
<input type='hidden' name='event' value='<?php echo $presets['event']; ?>'>
<input type='hidden' name='round' value='<?php echo $round; ?>'>
<input type='hidden' name='formaction' value='view'>

<?php
// Rankginglists for club and combined-events
if(empty($presets['event']))	// no event selected
{
?>
<table class='dialog'>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='single' checked>
			<?php echo $strSingleEvent; ?></input>
	</th>
</tr>

<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='combined'>
			<?php echo $strCombinedEvent; ?></input>
	</th>
</tr>

<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='team'>
			<?php echo $strClubEvent; ?></input>
	</th>
</tr>

<tr>
	<td class='dialog_sub'>
		<input type='radio' name='team' value='ranking' checked>
			<?php echo $strClubRanking; ?></input>
	</td>
</tr>

<tr>
	<td class='dialog_sub'>
		<input type='radio' name='team' value='sheets'>
			<?php echo $strClubSheets; ?></input>
	</td>
</tr>
</table>

<?php
}	// ET event selected
?>

<p/>

<table>
<tr>
	<td>
		<button type='submit'>
			<?php echo $strShow; ?>
	  	</button>
	</td>

</tr>
</table>

</form>
<?php
	
$page->endPage();

