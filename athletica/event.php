<?php

/**********
 *
 *	event.php
 *	---------
 *	
 */

require('./lib/cl_gui_page.lib.php');
require('./lib/cl_gui_button.lib.php');

require('./lib/common.lib.php');
require('./lib/timetable.lib.php');
require('./lib/results.lib.php');
require('./lib/timing.lib.php');

if(AA_connectToDB() == FALSE) {	// invalid DB connection
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

$now = getdate();
$zero = '';
if($now['minutes'] < 10) {
	$zero = '0';
}

$timestamp = $now['mday']
				. "." . $now['mon']
				. "." . $now['year']
				. ", " . $now['hours']
				. "." . $zero. $now['minutes'];

$page = new GUI_Page('event_monitor');
$page->startPage();
$page->printPageTitle($strEventMonitor . " " . $_COOKIE['meeting'] . " ($timestamp Uhr)");

//
// check if export for timing is forced
//
if($_GET['arg'] == "export_timing"){
	
	$timing = AA_timing_getTiming();
	
	if($timing == "alge"){ // export round wise
		
		/*$res = mysql_query("SELECT r.xRunde FROM
					wettkampf as w
					, runde as r
				WHERE
					r.xWettkampf = w.xWettkampf
				AND	w.xMeeting = ".$_COOKIE['meeting_id']."
				AND	r.Status = ".$cfgRoundStatus['heats_done']."
				");*/
		$sql = "SELECT
					r.xRunde
				FROM
					wettkampf AS w
				LEFT JOIN
					runde AS r USING(xWettkampf)
				WHERE
					w.xMeeting = ".$_COOKIE['meeting_id']."
				AND
					r.Status = ".$cfgRoundStatus['heats_done'].";";
		$res = mysql_query($sql);
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}else{
			
			while($row = mysql_fetch_array($res)){
				
				AA_timing_setStartInfo($row[0]);
				
			}
			
		}
		
	}elseif($timing == "omega"){
		
		AA_timing_setStartInfo(0);
		
	}else{
		
		AA_printErrorMsg($strErrTimingNotConfigured);
		
	}
	
}

?>

<table>
<tr>
	<th class='dialog' rowspan='3'>
		<?php echo $strStatus; ?>:
	</th>
	<td class='forms'>
		<div class='st_enrlmt_pend'>&nbsp;<?php echo $strEnrolementPending; ?>&nbsp;</div>
	</td>
	<td class='forms'>
		<div class='st_heats_work'>&nbsp;<?php echo $strHeatsInWork; ?>&nbsp;</div>
	</td>
	<td class='forms'>
		<div class='st_res_work'>&nbsp;<?php echo $strResultsInWork; ?>&nbsp;</div>
	</td>
	<td class='forms'>
	<?php
	$btn = new GUI_Button('event_log.php', $strEventLog);
	$btn->printButton();
	$btn->set($cfgURLDocumentation . 'help/event/index.html', $strHelp, '_blank');
	$btn->printButton();
	?>
	</td>	
</tr>
<tr>
	<td class='forms'>
		<div class='st_enrlmt_done'>&nbsp;<?php echo $strEnrolementDone; ?>&nbsp;</div>
	</td>
	<td class='forms'>
		<div class='st_heats_done'>&nbsp;<?php echo $strHeatsDone; ?>&nbsp;</div>
	</td>
	<td class='forms'>
		<div class='st_res_done'>&nbsp;<?php echo $strResultsDone; ?>&nbsp;</div>
	</td>
	<td class='forms' /></td>
</tr>
<tr>
	<td class='forms' />
	<td class='forms'>
		<div class='st_res_timing'>&nbsp;<?php echo $strTimingResults; ?>&nbsp;</div>
	</td>
	<td class='forms' /></td>
	<td class='forms'>
	<?php
	if(AA_timing_getTiming()=='omega'){
		$btn = new GUI_Button('event.php?arg=export_timing', $strTimingExport);
		$btn->printButton();
	}
	?>
	</td>
</tr>
</table>

<p />

<?php 

//
// get results from timing where auto timing is on
//
/*$res = mysql_query("
	SELECT xRunde, Status FROM
		runde
		LEFT JOIN wettkampf USING(xWettkampf)
	WHERE
		ZeitmessungAuto = 1
		AND Zeitmessung = 1
		AND xMeeting = ".$_COOKIE['meeting_id']
);

if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	while($row = mysql_fetch_array($res)){
		if($row[1]!=$cfgRoundStatus['results_done'] ){
			AA_timing_getResultsAuto($row[0]);
		}
	}
}*/

// COMMENT ROH:
// we check only rounds with heats_done and results_in_progress to improve performance
$res = mysql_query("
	SELECT r.xRunde, ru.Hauptrunde, ru.xMeeting FROM
		runde as r
		LEFT JOIN wettkampf as w USING(xWettkampf)
        LEFT JOIN rundenset as ru ON (r.xRunde = ru.xRunde)
	WHERE
		ZeitmessungAuto = 1
		AND Zeitmessung = 1
		AND w.xMeeting = ".$_COOKIE['meeting_id']."           
		AND (Status = ".$cfgRoundStatus['heats_done']." OR Status = ".$cfgRoundStatus['results_in_progress'].")"
);
  
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	while($row = mysql_fetch_array($res)){  
        if ($row[1] == NULL || $row[1] == 1){ 
                if ($row[1] == 1) {
                    if ($row[2] == $_COOKIE['meeting_id']) {
                        AA_timing_getResultsAuto($row[0]);                          
                    }  
                }
                else {
                     AA_timing_getResultsAuto($row[0]);                       
                }  
            }  
	}
}
   
AA_timetable_display('monitor');  

    $hour= date("H") + 2;   
    $dateHour = date("Y-m-d") . $hour;
              ?>

<script type="text/javascript">
<!--
	window.setTimeout("updatePage()", <?php echo $cfgMonitorReload * 1000; ?>);

	// scroll to put current time line approximately to the middle of the screen
	//var now = new Date();
	//var now = new Date(2003,2,9,10,0,0);	// test case
	//var year = now.getFullYear();      
	//var m = now.getMonth() + 1;
	//var month = ((m < 10) ? ("0" + m) : m);
	//var d = now.getDate();
	//var day = ((d < 10) ? ("0" + d) : d);
	//var h = now.getHours() + 2;
	//var hour = ((h < 10) ? ("0" + h) : h);
	//var date = year + "-" + month + "-" + day + hour;         
   
	if(document.getElementById('<?php echo $dateHour; ?>'))
	{   
		// scroll to current time (Internet Explorer only!!!)
		document.getElementById('<?php echo $dateHour; ?>').scrollIntoView("true");
        
		// get Y-offset 
		//if(document.documentElement && document.documentElement.scrollTop)
		//{														//IE6 standards compliant mode
		//	var scroll = document.documentElement.scrollTop;
	    //}
		// scroll to the left
		//window.scrollTo(0, scroll);
        
        
	}

	function updatePage()
	{
		window.open("event.php", "main");
	}

	
</script>



<?php
            
/*echo "<pre>";
print_r($GLOBALS);*/

$page->endPage();
 

