<?php

/**********
 *
 *	event_rankinglists.php
 *	----------------------
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

// get presets
$round = 0;
if(!empty($_GET['round'])){
	$round = $_GET['round'];
}
else if(!empty($_POST['round'])) {
	$round = $_POST['round'];
}

$presets = AA_results_getPresets($round);

// check discipline type of event if selected
$dtype = "";
if(!empty($presets['event'])){
	$res = mysql_query("
		SELECT d.Typ FROM 
			wettkampf as w
			LEFT JOIN disziplin as d USING(xDisziplin) 
		WHERE w.xWettkampf = ".$presets['event']
	);
	if(mysql_errno() > 0){
		
	}else{
		$row = mysql_fetch_array($res);
		$dtype = $row[0];
	}
}

//
//	Display print form
//

$page = new GUI_Page('event_rankinglists');
$page->startPage();
$page->printPageTitle($strRankingLists . ": " . $_COOKIE['meeting']);

$menu = new GUI_Menulist();
$menu->addButton($cfgURLDocumentation . 'help/event/rankinglists.html', $strHelp, '_blank');
$menu->printMenu();

?>
<script type="text/javascript">
<!--
	function setPrint()
	{
		document.printdialog.formaction.value = 'print';
		document.printdialog.target = '_blank';
	}
	
	function setView()
	{
		document.printdialog.formaction.value = 'view';
		document.printdialog.target = '';
	}
	
	function setExportPress()
	{
		document.printdialog.formaction.value = 'exportpress';
		document.printdialog.target = '';
	}
	
	function setExportDiplom()
	{
		document.printdialog.formaction.value = 'exportdiplom';
		document.printdialog.target = '';
	}
//-->
</script>

<p/>

<table><tr>
	<td>
		<?php	AA_printCategorySelection('event_rankinglists.php'
			, $presets['category'], 'post'); ?>
	</td>
	<td>
		<?php	AA_printEventSelection('event_rankinglists.php'
			, $presets['category'], $presets['event'], 'post'); ?>
	</td>
<?php
if($presets['event'] > 0) {		// event selected
?>
	<td>
		<?php AA_printRoundSelection('event_rankinglists.php'
			, $presets['category'] , $presets['event'], $round); ?>
	</td>
<?php
}
?>

<form action='print_rankinglist.php' method='get' name='printdialog'>

<input type='hidden' name='category' value='<?php echo $presets['category']; ?>'>
<input type='hidden' name='event' value='<?php echo $presets['event']; ?>'>
<input type='hidden' name='round' value='<?php echo $round; ?>'>
<input type='hidden' name='formaction' value=''>

<table class='dialog'>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='single' checked>
			<?php echo $strSingleEvent; ?></input>
	</th>
</tr>
<?php
if(($dtype == $cfgDisciplineType[$strDiscTypeJump])
	|| ($dtype == $cfgDisciplineType[$strDiscTypeJumpNoWind])
	|| ($dtype == $cfgDisciplineType[$strDiscTypeThrow])
	|| ($dtype == $cfgDisciplineType[$strDiscTypeHigh])
	|| empty($presets['event'])) {
?>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='single_attempts'>
			<?php echo $strSingleEventAttempts; ?></input>
	</th>
</tr>

<?php
}

// Rankginglists for club and combined-events
if(empty($presets['event']))	// no event selected
{
?>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='combined'>
			<?php echo $strCombinedEvent; ?></input>
	</th>
</tr>
<tr>
	<td class='dialog'>
		&nbsp;&nbsp;
		<input type='checkbox' name='sepu23' value='yes'>
			<?php echo $strSeparateU23; ?></input>
	</td>
</tr>

<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='team'>
			<?php echo $strClubRanking; ?></input>
	</td>
</tr>

<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='sheets'>
			<?php echo $strClubSheets; ?></input>
	</td>
</tr>

<?php
}

if(empty($round)){	// team sm ranking minimum is discipline
?>
<tr>
	<th class='dialog'>
		<input type='radio' name='type' value='teamsm'>
			<?php echo $strTeamSMRanking; ?></input>
	</td>
</tr>
<?php
}

if(empty($presets['event']))	// show page break only event not selected
{										
?>
<tr>
	<th class='dialog'>
		<?php echo $strPageBreak; ?>
	</th>
</tr>


<tr>
	<td class='dialog'>
		<input type='radio' name='break' value='none' checked>
			<?php echo $strNoPageBreak; ?></input>
	</td>
</tr>
<?php
	if(empty($presets['category']))	// show page break 'category' only if no
	{											// specific category selected
?>
<tr>
	<td class='dialog'>
		<input type='radio' name='break' value='category'>
			<?php echo $strCategory; ?></input>
	</td>
</tr>
<?php
	}		// ET page break category
?>
<tr>
	<td class='dialog'>
		<input type='radio' name='break' value='discipline'>
			<?php echo $strDiscipline; ?></input>
	</td>
</tr>
<?php
}		// ET page break

$tage = 1;
$sql = "SELECT DISTINCT(Datum) AS Datum 
		  FROM runde 
	 LEFT JOIN wettkampf USING(xWettkampf) 
		 WHERE xMeeting = ".$_COOKIE['meeting_id']." 
	  ORDER BY Datum ASC;";
$query = mysql_query($sql);

$tage = mysql_num_rows($query);
if($tage>1){
	?>
	<tr>
		<th class='dialog'>
			<?php echo $strDay; ?></input>
		</th>
	</tr>
	<tr>
		<td class='dialog'>
			<select name='date'>
				<option value="%">- <?=$strAll?> -</option>
				<?php
				while($row = mysql_fetch_assoc($query)){
					?>
					<option value="<?=$row['Datum']?>"><?=date('d.m.Y', strtotime($row['Datum']))?></option>
					<?php
				}
				?>
			</select>
		</td>
	</tr>
	<?php
}
?>
<tr>
	<th class='dialog'>
		<input type='checkbox' name='cover' value='cover'>
			<?php echo $strCover; ?></input>
	</th>
</tr>
<tr>
	<td class='dialog'>
		<input type='checkbox' name='cover_timing' value='1'>
			<?php echo $strTiming; ?></input>
	</td>
</tr>

</table>

<p />

<table>
<tr>
	<td>
		<button name='view' type='submit' onClick='setView()'>
			<?php echo $strShow; ?>
		</button>
	</td>
	<td>
		<button name='print' type='submit' onClick='setPrint()'>
			<?php echo $strPrint; ?>
		</button>
	</td>
</tr>
</table>

<br>

<table class="dialog">
<tr>
	<th class="dialog"><?php echo $strExport ?></th>
</tr>
<tr>
	<td class="forms">
		<input type="radio" name="limitRank" value="yes" id="limitrank">
		<?php echo $strExportRanks ?> <input type="text" size="2" name="limitRankFrom" onfocus="o = document.getElementById('limitrank'); o.checked='checked'">
		<?php echo strtolower($strTo) ?> <input type="text" size="2" name="limitRankTo" onfocus="o = document.getElementById('limitrank'); o.checked='checked'">
	</td>
</tr>
<tr>
	<td class="forms">
		<input type="radio" name="limitRank" value="no" checked><?php echo $strExportAllRanks ?>
	</td>
</tr>
<tr>
	<td class="forms" align="right">
		<button name='print' type='submit' onClick='setExportPress()'>
			<?php echo $strExportPress; ?>
		</button>
		<button name='print' type='submit' onClick='setExportDiplom()'>
			<?php echo $strExportDiplom; ?>
		</button>
	</td>
</tr>
</table>

</form>

<?php

$page->endPage();
?>
