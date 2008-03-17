<?php
/**********
 *
 *	meeting_definition_eventadd.php
 *	-------------------------------
 *	
 */

require('./convtables.inc.php');

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


$category = 0;
if(!empty($_POST['cat'])) {
	$category = $_POST['cat'];
}
else if(!empty($_GET['cat'])) {
	$category = $_GET['cat'];
}

// add a new event
if ($_POST['arg']=="add_event")
{
	AA_meeting_addEvent();

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	
}

/***************************
 *
 *		General meeting data
 *
 ***************************/

$page = new GUI_Page('meeting_definition_event_add');
$page->startPage();
$page->printPageTitle($strNewEvent);

if ($_POST['arg']=="add_event")
{
	?>
<script>
	window.open("meeting_definition_eventlist.php?updateCat="
		+ <?php echo $category; ?>,
		"list");
</script>
	<?php
}

//get default fee and deposit
$sql= "
	SELECT
		Startgeld
		, Haftgeld
	FROM
		meeting
	WHERE
		xMeeting = " . $_COOKIE['meeting_id'];
$result_meeting = mysql_query($sql);

if(mysql_errno() > 0) {		// DB error
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else		// no DB error
{

	if(mysql_num_rows($result_meeting) == 1)	
	{
		$row_meeting = mysql_fetch_array($result_meeting);
		$_POST['deposit'] = $row_meeting['Haftgeld']/100; //amounts ar stored in cents
		$_POST['fee'] =$row_meeting['Startgeld']/100;	  //amounts ar stored in cents
	} else {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());	
	}
	
}
?>



<script type="text/javascript">
<!--
	var disz = new Array();
	var timingrow;

	<?php
	// get disciplines with type for checking if wind can be enabled or not
	$res = mysql_query("select xDisziplin, Typ from disziplin");
	$i=0;
	while($row_dis = mysql_fetch_array($res)){
		?>
		//disz[<?php echo $i ?>] = new Array(2);
		//disz[<?php echo $i ?>][0] = "<?php echo $row_dis[0] ?>";
		//disz[<?php echo $i ?>][1] = '<?php echo $row_dis[1] ?>';
		disz[<?php echo $row_dis[0] ?>] = '<?php echo $row_dis[1] ?>';
		<?php
		$i++;
	}
	mysql_Free_result($res);
	?>
	
	function check(item)	// state has changed; check what to do
	{
		if((item=='discipline')
			&& (document.add_event.discipline.value=='new'))	// new discipline
		{
			window.open("admin_disciplines.php", "_self");
		}
		else if((item=='category')
			&& (document.add_event.cat.value=='new'))	// new category
		{
			window.open("admin_categories.php", "_self");
		}
	}
	
	function check_discipline(){
		val = document.getElementById("disciplineselectbox").value;
		if(disz[val] == <?php echo $cfgDisciplineType[$strDiscTypeTrack] ?> || disz[val] == <?php echo $cfgDisciplineType[$strDiscTypeJump] ?>){
			// wind may be measured
			document.getElementById('wind').style.visibility = "visible";
			document.getElementById('nowind').style.visibility = "hidden";
		}else{
			document.getElementById('wind').style.visibility = "hidden";
			document.getElementById('nowind').style.visibility = "visible";
		}
		
		if(disz[val] == <?php echo $cfgDisciplineType[$strDiscTypeNone] ?> 
			|| disz[val] == <?php echo $cfgDisciplineType[$strDiscTypeTrack] ?> 
			|| disz[val] == <?php echo $cfgDisciplineType[$strDiscTypeTrackNoWind] ?> 
			|| disz[val] == <?php echo $cfgDisciplineType[$strDiscTypeDistance] ?> 
			|| disz[val] == <?php echo $cfgDisciplineType[$strDiscTypeRelay] ?> ){
			document.getElementById('timing').style.visibility = "visible";
		}else{
			document.getElementById('timing').style.visibility = "hidden";
		}
	}
	
	function jump_time(curr, next){
		var e = window.event;
		//alert (e);
		//if(e.keyCode > 31){
		if(curr.value.length == 2){
			var tmp = document.getElementsByName(next);
			tmp[0].focus();
			tmp[0].select();
		}
		//}
	}
	
	function create_combined(){
		
		if(document.add_event.cat.value != 'new' && document.add_event.cat.value != 0){
			
			// redirect for adding a combined event in the current category
			var val = document.add_event.cat.value;
			document.location.href = "meeting_definition_category.php?arg=create_combined&cat="+val;
			
		}else{
			
			alert("<?php echo $strErrCreateCombined ?>");
			
		}
	}
//-->
</script>

<?php
/*****************************************
 *
 *	Form to add new category/discipline
 *
 *****************************************/

$result = mysql_query("
	SELECT
		xKategorie
		, Kurzname
	FROM
		kategorie
	ORDER BY
		Anzeige
");

if(mysql_errno() > 0) {		// DB error
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else		// no DB error
{

	if(mysql_num_rows($result) >= 1)	// any more categories
	{
?>
<form action='meeting_definition_event_add.php' method='post' name='add_event'>

<table class='dialog' id="properties">
<tr>
	<th class='dialog'><?php echo $strCategory; ?></th>
	<td class='forms'>
		<input name='arg' type='hidden' value='add_event' />
<?php
		$dropdown = new GUI_Select('cat', 1, "check(\"category\")");
		$dropdown->addOptionNone();
		while ($row = mysql_fetch_row($result))
		{
			$dropdown->addOption($row[1], $row[0]);
		}
		$dropdown->addOptionNew();
		$dropdown->selectOption($category);
		$dropdown->printList();
?>
	<input type="button" onclick="create_combined()" value="<?php echo $strCreateCombined ?>">
	</td>
</tr>

<tr>
	<th class='dialog'><?php echo $strDiscipline; ?></th>
<?php
		$dd = new GUI_DisciplineDropDown(0, true, false, $keys, 'check_discipline()');
?>
</tr>

<tr>
	<th class='dialog'><?php echo $strWind; ?></th>
	<td class='forms'>
		<input type="checkbox" name="wind" id="wind" value="yes"><span id="nowind"><?php echo $strNoWind ?></span>
	</td>
	<script language="javascript">
		document.getElementById('wind').style.visibility = "hidden";
	</script>
</tr>


<tr>
	<th class='dialog'><?php echo $strDeposit; ?></th>
	<td class='forms'>
		<input class='nbr' name='deposit' type='text' maxlength='10'
			value='<?php echo $_POST['deposit']; ?>' />
	</td>
</tr>

<tr>
	<th class='dialog'><?php echo $strFee; ?></th>
	<td class='forms'>
		<input class='nbr' name='fee' type='text' maxlength='10'
			value='<?php echo $_POST['fee']; ?>' />
	</td>
</tr>

<tr>
	<th class='dialog'><?php echo $strInfo; ?></th>
	<td class='forms' colspan='3'>
		<input class='text' name='info' type='text' maxlength='15'
			value='' />
	</td>
</tr>

<tr id="timing">
	<th class='dialog'><?php echo $strTiming; ?></th>
	<td class='forms' colspan='3'>
		<input type="checkbox" name="timing" value="yes"> <?php echo $strOn ?> /
		<input type="checkbox" name="timingAuto" value="yes"> <?php echo $strAutomatic ?>
	</td>
	<script language="javascript">
		document.getElementById('timing').style.visibility = "hidden";
		/*timingrow = document.getElementById('timing');
		var tmp = timingrow.parentNode;
		tmp.removeChild(timingrow);*/
	</script>
</tr>

</table>

<p/>

<table class='dialog'>
<tr>
	<th class='dialog'><?php echo $strType; ?></th>
	<th class='dialog'><?php echo $strDate; ?></th>
	<th class='dialog'><?php echo $strTimeFormat; ?></th>
	<th class='dialog'><?php echo $strEnrolementTime; ?></th>
	<th class='dialog'><?php echo $strManipulationTime; ?></th>
</tr>
<tr>
	<?php
	$dd = new GUI_RoundtypeDropDown(0, 1);
	$dd = new GUI_DateDropDown(0, 1);
	?>
	<td class='forms'>
		<input size="4" type='text' name='time_1' maxlength='5'
			value='' />
	</td>
	<td class='forms'>
		<input size="4" type='text' name='etime_1' maxlength='5'
			value='' />
	</td>
	<td class='forms'>
		<input size="4" type='text' name='mtime_1' maxlength='5'
			value='' />
	</td>
</tr>

<tr>
	<?php
	$dd = new GUI_RoundtypeDropDown(0, 2);
	$dd = new GUI_DateDropDown(0, 2);
	?>
	<td class='forms'>
		<input size="4" type='text' name='time_2' maxlength='5'
			value='' />
	</td>
	<td class='forms'>
		<input size="4" type='text' name='etime_2' maxlength='5'
			value='' />
	</td>
	<td class='forms'>
		<input size="4" type='text' name='mtime_2' maxlength='5'
			value='' />
	</td>
</tr>

<tr>
	<?php
	$dd = new GUI_RoundtypeDropDown(0, 3);
	$dd = new GUI_DateDropDown(0, 3);
	?>
	<td class='forms'>
		<input size="4" type='text' name='time_3' maxlength='5'
			value='' />
	</td>
	<td class='forms'>
		<input size="4" type='text' name='etime_3' maxlength='5'
			value='' />
	</td>
	<td class='forms'>
		<input size="4" type='text' name='mtime_3' maxlength='5'
			value='' />
	</td>
</tr>

</table>

<button type='submit'>
	<?php echo $strAdd; ?>
</button>
</form>
<?php
	}	// any category found
}	// ET DB error
mysql_free_result($result);

if($category > 0)
{
	?>
<script type="text/javascript">
<!--
	if(document.add_event) {
		document.add_event.discipline.focus();
	}
//-->
</script>
	<?php
}
else
{
	?>
<script type="text/javascript">
<!--
	if(document.add_event) {
		document.add_event.cat.focus();
	}
//-->
</script>
	<?php
}

$page->endPage();
