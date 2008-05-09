<?php

/**********
 *
 *	meeting_entries_receipt.php
 *	-----------------------------
 *	
 */   
	 
//require('./lib/cl_gui_dropdown.lib.php');
//require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
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
		echo "Overwrite";
	} else {
		echo "Skip";
	}
} else {


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
