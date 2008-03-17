<?php
/**********
 *
 *	meeting_copy.php
 *	------------------
 *	
 */

require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	// invalid DB connection
{
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

$page = new GUI_Page('meeting_copy');
$page->startPage();
$page->printPageTitle($strCopyMeeting . ": " . $_COOKIE['meeting']);

$arg = "";
if(isset($_POST['arg'])){
	$arg = $_POST['arg'];
}

// check on new name != empty for copy prozess
if(empty($arg) || empty($_POST['newname'])){
?>
<form action='meeting_copy.php' method='post' name='copy'>

<table class='dialog'>
	<tr>
		<th class='dialog' colspan="2"><?php echo $strMeetingNew; ?></th>
		<input type="hidden" name="arg" value="copy">
	</tr>

	<tr>
		<td>
			<?php echo $strNewName ?>
			<input type="text" name="newname" value="<?php echo $_COOKIE['meeting'] ?>">
			<?php echo $strNewNumber ?>
			<input type="text" name="newnumber" value="">
		</td>
		<td><input type="submit" name="submit" value="<?php echo $strCopy ?>"></td>
	</tr>
</table>

</form>

<?php
}
elseif($arg == "copy")
{
?>
<table class='dialog'>
<?php
	// new meeting name
	$newname = $_POST['newname'];
	$newnumber = $_POST['newnumber'];
	$newxMeeting = 0;
	
	mysql_query("LOCK TABLES meeting WRITE, wettkampf WRITE");
	
	// copy meeting entry
	$resFields = mysql_query("SHOW COLUMNS FROM meeting");
	$resData = mysql_query("SELECT * FROM meeting WHERE xMeeting = ".$_COOKIE['meeting_id']);
	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}else{
		
		$data = mysql_fetch_assoc($resData);
		
		$sql = "";
        $dateDiff = "";   
		while($f = mysql_fetch_assoc($resFields)){  
            if ($f['Field'] == "DatumVon"){
                    $dateFrom=$data[$f['Field']]; 
                }
            elseif ($f['Field'] == "DatumBis"){
                    $dateTo=$data[$f['Field']];  
                    $dateDiff=(str_replace("-","",$dateTo))-(str_replace("-","",$dateFrom));  // get meeting duration   
                    } 
            
			if($f['Key'] != "PRI" && $f['Field'] != "Name" && $f['Field'] != "Nummer" && $f['Field'] != "DatumVon" && $f['Field'] != "DatumBis"){ // exclude primary key and 2 fields
				$sql .= ", ".$f['Field']." = '".$data[$f['Field']]."' ";  
			}
			
		} 
        // get date today and end date meeting
		$dateFrom=date("Y.m.d");
        $j = date('Y');
        $m = date('m');
        $d = date('d');
        $dateTo = date('Y.m.d',mktime(0,0,0,$m,$d+$dateDiff,$j));   
        
		mysql_query("INSERT INTO meeting SET
				Name = '$newname'
				, Nummer = '$newnumber'
                , DatumVon = '$dateFrom'
                , DatumBis = '$dateTo' 
				$sql
		");
		
		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			$newxMeeting = mysql_insert_id();
		}
		
		mysql_free_result($resData);
		mysql_free_result($resFields);
		
	}
	
	// copy discipline entrys
	if($newxMeeting > 0){
		
		$resFields = mysql_query("SHOW COLUMNS FROM wettkampf");
		$fields = array();
		while($row = mysql_fetch_assoc($resFields)){
			$fields[] = $row;
		}
		
		$resData = mysql_query("SELECT * FROM wettkampf WHERE xMeeting = ".$_COOKIE['meeting_id']);
		
		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			
			while($data = mysql_fetch_assoc($resData)){
				
				$sql = "";
				foreach($fields as $f){
					
					if($f['Key'] != "PRI" && $f['Field'] != "xMeeting"){ // exclude primary key and meeting id
						$sql .= ", ".$f['Field']." = '".$data[$f['Field']]."' ";
					}
					
				}
				
				mysql_query("INSERT INTO wettkampf SET
						xMeeting = $newxMeeting
						$sql
				");
				
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
			}
			
			mysql_free_result($resData);
			mysql_free_result($resFields);
			
		}
		
		if(mysql_errno() > 0) {
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			// unlock all tables
			mysql_query("UNLOCK TABLES");
			
			?>
			
	<tr>
		<th class='dialog'><?php echo $strCopyMade ?></th>
	</tr>
	<tr>
		<td><input type="button" name="" value="<?php echo $strBack ?>" onclick="parent.location = 'index.php'"></td>
	</tr>
			
			<?php
		}
	}
	
	

?>
</table>
<?php

}

$page->endPage();
?>
