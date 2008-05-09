<?php

/**********
 *
 *	meeting_entries_receipt.php
 *	-----------------------------
 *	
 */   
	 

require('./lib/cl_gui_page.lib.php');
require('./lib/common.lib.php');
require('./lib/cl_performance.lib.php');


if(AA_connectToDB() == FALSE)	{		// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}   


$page = new GUI_Page('meeting_entries_efforts');
$page->startPage();
$page->printPageTitle($strUpdateEfforts);



if (isset($_POST['updateEfforts'])){    
	if ($_POST['mode']=="overwrite"){	
		//echo "Overwrite";
		$sql_where = "";
	} else {
		//echo "Skip";
		$sql_where = " AND start.BaseEffort ='y'";
	}
	
	$event = $_COOKIE['meeting_id'];

	$saison = $_SESSION['meeting_infos']['Saison'];
	if ($saison == ''){
		$saison = "O"; //if no saison is set take outdoor
	}
	
	$sql = "	SELECT
		athlet.Lizenznummer as License
		, disziplin.Code as DiszCode
		, disziplin.Typ
		, xStart
	FROM
		athletica.start
		INNER JOIN athletica.anmeldung 
			ON (start.xAnmeldung = anmeldung.xAnmeldung)
		INNER JOIN athletica.wettkampf 
			ON (start.xWettkampf = wettkampf.xWettkampf)
		INNER JOIN athletica.athlet 
			ON (anmeldung.xAthlet = athlet.xAthlet)
		INNER JOIN athletica.disziplin 
			ON (wettkampf.xDisziplin = disziplin.xDisziplin)
	WHERE (athlet.Lizenznummer != 0 AND 
		wettkampf.xMeeting =$event 
		$sql_where)";
	//echo $sql;
	
	$res_start = mysql_query($sql);
	if(mysql_errno() > 0){
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error() . $sql);
	} else { 

		while ($row_start = mysql_fetch_array($res_start)){
			// get performance from base data 
			$perf = 0;

			$sql = "SELECT notification_effort 
					  FROM base_performance 
				 LEFT JOIN base_athlete USING(id_athlete) 
					 WHERE base_athlete.license = ".$row_start['License']." 
					   AND base_performance.discipline = ".$row_start['DiszCode'] ." 
					   AND season = '".$saison."';";
			$res = mysql_query($sql); 
			//echo $sql;
			$rowPerf = mysql_fetch_array($res); 
			$perf = $rowPerf['notification_effort']; 
			
			
										
			if(($row_start['Typ'] == $cfgDisciplineType[$strDiscTypeTrack])
				|| ($row_start['Typ'] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
				|| ($row_start['Typ'] == $cfgDisciplineType[$strDiscTypeRelay])
				|| ($row_start['Typ'] == $cfgDisciplineType[$strDiscTypeDistance])) {  // disciplines track
				$pt = new PerformanceTime(trim($perf));
				$perf = $pt->getPerformance();
			}
														  
			if($perf != NULL) {	// invalid performance
				$sql = "UPDATE start SET 
				  Bestleistung = $perf
				 , BaseEffort = 'y'
				 WHERE xStart = ". $row_start['xStart'];
				//echo " <br>$sql";
				mysql_query($sql);
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
	 
			}
		}
		
		echo "<br>Die Besteleistungen wurden erfolgreich aktualisiert!";
	}

} else {


//---------------- Show Configuration Screen ------------------	
	
//get base-date
$res = mysql_query("SELECT MAX(global_last_change) as datum FROM base_log");
if ($res){
	$row =mysql_fetch_array($res);
	$date = $row['datum'];
} else {
	$date = $strNoBaseData ;
}

?>
<form action='meeting_entries_efforts.php' method='post' name='Form_updateEfforts'>   

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody><tr>
			<td style="vertical-align: top;" width="475">
				<table class="dialog" width="475">
					<tbody><tr>

						<th><?=$strConfiguration?></th>
					</tr>
					<tr>
						<td><p><?=$strEffortsUpdateInfo1?></p>
						  <p><?=$strEffortsUpdateInfo2?></p>
						  <p><?= $strBaseData?> <b><?=$date;?></b><br />
							<br />
						  </p>
						  <p><?=$strEffortsUpdateInfo3?></p>
						  <p>
							<label>
							  <input type="radio" name="mode" value="overwrite" id="mode_0" checked="checked" />
							  <?=$strOverwrite;?></label>
							<br />
							<label>
							  <input type="radio" name="mode" value="skip" id="mode_1" />
							  <?=$strLeaveBehind ;?></label>
							<br />
						  </p></td>
					</tr>
				</tbody>
		</table>
	
	<br>
	<button name='updateEfforts' type='submit'>
	<?php echo $strUpdateEfforts; ?>
	</button>          
</form>    

<?php
}

$page->endPage();
?>
