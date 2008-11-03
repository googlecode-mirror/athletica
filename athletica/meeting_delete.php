<?php
/**********
 *
 *	meeting_delete.php
 *	------------------
 *	
 */

require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');
require('./lib/cl_protect.lib.php');

if(AA_connectToDB() == FALSE)	// invalid DB connection
{
	return;
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}


// check if access restricted
$pass = new Protect();
if($pass->isRestricted($_COOKIE['meeting_id'])){
	
	if(!$pass->isLoggedIn($_COOKIE['meeting_id'])){ // user not logged in -> only speaker access
		
		header("Location: login.php?redirect=meeting_delete");
		die();
	}
	
}


$page = new GUI_Page('meeting_delete');
$page->startPage();
$page->printPageTitle($strDeleteMeeting . ": " . $_COOKIE['meeting']);

// meeting deletion not confirmed yet
if($_GET['conf'] != 1)
{
?>
<form action='meeting_delete.php' method='get' name='delete'>

<table class='dialog'>
	<tr>
		<th class='dialog'><?php echo $strWarningDeleteMeeting; ?></th>	
	</tr>

	<tr>
		 <td><input name='conf' type='radio' value='0' checked>
		 <?php echo $strNo; ?></input></td>
	</tr>
	<tr>
		 <td><input name='conf' type='radio' value='1'>
		 <?php echo $strYes; ?></input></td>
	 </tr>

	<tr>
		<td>
			<button name='delete' type='submit'>
			<?php echo $strDelete; ?>
		</button>
		<button name='reset' type='reset' onClick='window.open("meeting.php", "main")'>
			<?php echo $strCancel; ?>
		</button>
	</td>
</tr>
</table>

</form>

<?php
}
else		// delete all meeting data
{
?>
<table>
<tr>
	<th><?php echo $strTable; ?></th>
	<th><?php echo $strDeletedItems; ?></th>
</tr>
<?php

	//
	//	delete 'resultat'
	// -----------------
	mysql_query("LOCK TABLES resultat WRITE
					, serienstart READ, start READ, wettkampf READ");
	
	$items = 0;
	// get 'serienstart' IDs 
	$result = mysql_query("
		SELECT
			serienstart.xSerienstart
		FROM
			serienstart
			, start
			, wettkampf
		WHERE serienstart.xStart = start.xStart
		AND start.xWettkampf = wettkampf.xWettkampf
		AND wettkampf.xMeeting = " . $_COOKIE['meeting_id'] . "
		ORDER BY
			serienstart.xSerienstart
	");

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else				// no DB error
	{
		if(mysql_num_rows($result) > 0)
		{
			// build DELETE statement
			$keys = "";
			$cma = "";

			while($row = mysql_fetch_row($result))
			{
				$keys = $keys . $cma . $row[0];
				$cma = ",";
			}
			$sql = $sql . ")";
			mysql_free_result($result);
			
			/*mysql_query("	SELECT * INTO OUTFILE 'c:/wwwroot/resultat.txt'
					FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
					LINES TERMINATED BY '\n'
					FROM resultat
					WHERE xSerienstart IN ($keys)");
			echo mysql_error();*/
			
			$sql = "DELETE FROM resultat WHERE xSerienstart IN ("; 
			mysql_query("
				DELETE FROM
					resultat
				WHERE xSerienstart IN ($keys)
			");

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$items = mysql_affected_rows();	// nbr of items deleted
			}
?>
<tr>
	<td>resultat</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
		}	// ET any results
	}	// ET DB error
	// unlock all tables
	mysql_query("UNLOCK TABLES");


	//
	//	delete 'serienstart', 'staffelathlet'
	// -------------------------------------
	mysql_query("LOCK TABLES serienstart WRITE, staffelathlet WRITE
					, start READ, wettkampf READ");
	
	$items = 0;
	// get 'start' IDs 
	$result = mysql_query("
		SELECT
			start.xStart
		FROM
			start
			, wettkampf
		WHERE start.xWettkampf = wettkampf.xWettkampf
		AND wettkampf.xMeeting = " . $_COOKIE['meeting_id'] . "
		ORDER BY
			start.xStart
	");

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else				// no DB error
	{
		if(mysql_num_rows($result) > 0)
		{
			// build DELETE statement
			$keys = "";
			$cma = "";

			while($row = mysql_fetch_row($result))
			{
				$keys = $keys . $cma . $row[0];
				$cma = ",";
			}
			mysql_free_result($result);

			mysql_query("
				DELETE FROM
					serienstart
				WHERE xStart IN ($keys)
			"); 

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$items = mysql_affected_rows();	// nbr of items deleted
			}
?>
<tr>
	<td>serienstart</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
			$items = 0;
			mysql_query("
				DELETE FROM
					staffelathlet
				WHERE xStaffelstart IN ($keys)
			"); 

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$items = mysql_affected_rows();	// nbr of items deleted
			}

			mysql_query("
				DELETE FROM
					staffelathlet
				WHERE xAthletenstart IN ($keys)
			"); 

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$items = $items + mysql_affected_rows();	// nbr of items deleted
			}
?>
<tr>
	<td>staffelathlet</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
		}	// ET any results
	}	// ET DB error

	// unlock all tables
	mysql_query("UNLOCK TABLES");


	//
	//	delete 'serie'
	// --------------
	mysql_query("LOCK TABLES serie WRITE, rundenlog WRITE
					, runde READ, wettkampf READ");
	
	$items = 0;
	// get 'runde' IDs 
	$result = mysql_query("
		SELECT
			runde.xRunde
		FROM
			runde
			, wettkampf
		WHERE runde.xWettkampf = wettkampf.xWettkampf
		AND wettkampf.xMeeting = " . $_COOKIE['meeting_id'] . "
		ORDER BY
			runde.xRunde
	");

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else				// no DB error
	{
		if(mysql_num_rows($result) > 0)
		{
			// build DELETE statement
			$keys = "";
			$cma = "";

			while($row = mysql_fetch_row($result))
			{
				$keys = $keys . $cma . $row[0];
				$cma = ",";
			}
			mysql_free_result($result);

			mysql_query("
				DELETE FROM
					serie
				WHERE xRunde IN ($keys)
			"); 

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$items = mysql_affected_rows();	// nbr of items deleted
			}
?>
<tr>
	<td>serie</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
			mysql_query("
				DELETE FROM
					rundenlog
				WHERE xRunde IN ($keys)
			"); 

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$items = mysql_affected_rows();	// nbr of items deleted
			}
?>
<tr>
	<td>rundenlog</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
		}	// ET any results
	}	// ET DB error

	// unlock all tables
	mysql_query("UNLOCK TABLES");



	//
	//	delete 'runde', 'start'
	// -----------------------
	mysql_query("LOCK TABLES runde WRITE, start WRITE, wettkampf READ");
	
	$items = 0;
	// get 'wettkampf' IDs 
	$result = mysql_query("
		SELECT
			xWettkampf
		FROM
			wettkampf
		WHERE xMeeting = " . $_COOKIE['meeting_id'] . "
		ORDER BY
			xWettkampf
	");

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else				// no DB error
	{
		if(mysql_num_rows($result) > 0)
		{
			// build DELETE statement
			$keys = "";
			$cma = "";

			while($row = mysql_fetch_row($result))
			{
				$keys = $keys . $cma . $row[0];
				$cma = ",";
			}
			mysql_free_result($result);

			mysql_query("
				DELETE FROM
					runde
				WHERE xWettkampf IN ($keys)
			"); 

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$items = mysql_affected_rows();	// nbr of items deleted
			}
?>
<tr>
	<td>runde</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
			$items = 0;
			mysql_query("
				DELETE FROM
					start
				WHERE xWettkampf IN ($keys)
			"); 

			if(mysql_errno() > 0) {
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				$items = mysql_affected_rows();	// nbr of items deleted
			}
?>
<tr>
	<td>start</td>
	<td><?php echo $items; ?></td>
</tr>
<?php

		}	// ET any results
	}	// ET DB error

	// unlock all tables
	mysql_query("UNLOCK TABLES");



	//
	//	delete 'rundenset', 'anmeldung', 'staffel', 'team', 'wettkampf', 'meeting'
	// ---------------------------------------------------------------------------
	mysql_query("LOCK TABLES rundenset WRITE, anmeldung WRITE, staffel WRITE, team WRITE
					, wettkampf WRITE, meeting WRITE, athlet WRITE");
	        
    $items = 0;
    mysql_query("
        DELETE FROM
            rundenset
        WHERE xMeeting = " . $_COOKIE['meeting_id'] . "
    "); 

    if(mysql_errno() > 0) {
        AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
    }
    else {
        $items = mysql_affected_rows();    // nbr of items deleted
?>
<tr>
    <td>rundenset</td>
    <td><?php echo $items; ?></td>
</tr>
<?php
    }    // ET DB error
    
       
	$items = 0;
	mysql_query("
		DELETE FROM
			anmeldung
		WHERE xMeeting = " . $_COOKIE['meeting_id'] . "
	"); 

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else {
		$items = mysql_affected_rows();	// nbr of items deleted
?>
<tr>
	<td>anmeldung</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
	}	// ET DB error

	$items = 0;
	mysql_query("
		DELETE FROM
			staffel
		WHERE xMeeting = " . $_COOKIE['meeting_id'] . "
	"); 

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else {
		$items = mysql_affected_rows();	// nbr of items deleted
?>
<tr>
	<td>staffel</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
	}	// ET DB error


	$items = 0;
	mysql_query("
		DELETE FROM
			team
		WHERE xMeeting = " . $_COOKIE['meeting_id'] . "
	"); 

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else {
		$items = mysql_affected_rows();	// nbr of items deleted
?>
<tr>
	<td>team</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
	}	// ET DB error


	$items = 0;
	mysql_query("
		DELETE FROM
			wettkampf
		WHERE xMeeting = " . $_COOKIE['meeting_id'] . "
	"); 

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else {
		$items = mysql_affected_rows();	// nbr of items deleted
?>
<tr>
	<td>wettkampf</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
	}	// ET DB error

	$items = 0;
	mysql_query("
		DELETE FROM
			meeting
		WHERE xMeeting = " . $_COOKIE['meeting_id'] . "
	"); 

	if(mysql_errno() > 0) {
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else {
		$items = mysql_affected_rows();	// nbr of items deleted
?>
<tr>
	<td>meeting</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
		// delete cookies
		setcookie("meeting_id", "", time()-3600);
		setcookie("meeting", "", time()-3600);
		
		if(isset($_SESSION['meeting_infos'])){
			unset($_SESSION['meeting_infos']);
		}
	}	// ET DB error
	
	$items = 0;
	$sql = "SELECT * 
			  FROM athlet;";
	$query = mysql_query($sql);
	
	while($athlet = mysql_fetch_assoc($query)){
		$ref = AA_checkReference('anmeldung', 'xAthlet', $athlet['xAthlet']);
		
		if($ref==0){
			$sql2 = "DELETE FROM athlet 
						   WHERE xAthlet = ".$athlet['xAthlet'].";";
			$query2 = mysql_query($sql2);
			if($query2 && mysql_affected_rows()==1){
				$items++;
			}
		}
	}
?>
<tr>
	<td>athlet</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
	$items = 0;
	$sql = "SELECT * 
			  FROM team;";
	$query = mysql_query($sql);
	
	while($team = mysql_fetch_assoc($query)){
		$ref = AA_checkReference('meeting', 'xMeeting', $team['xMeeting']);
		
		if($ref==0){
			$sql2 = "DELETE FROM team 
						   WHERE xTeam = ".$team['xTeam'].";";
			$query2 = mysql_query($sql2);
			if($query2 && mysql_affected_rows()==1){
				$items++;
			}
		}
	}
?>
<tr>
	<td>team</td>
	<td><?php echo $items; ?></td>
</tr>
<?php
	$items = 0;
	$sql = "SELECT * 
			  FROM staffel;";
	$query = mysql_query($sql);
	
	while($staffel = mysql_fetch_assoc($query)){
		$ref = AA_checkReference('meeting', 'xMeeting', $staffel['xMeeting']);
		
		if($ref==0){
			$sql2 = "DELETE FROM staffel 
						   WHERE xStaffel = ".$staffel['xStaffel'].";";
			$query2 = mysql_query($sql2);
			if($query2 && mysql_affected_rows()==1){
				$items++;
			}
		}
	}
?>
<tr>
	<td>staffel</td>
	<td><?php echo $items; ?></td>
</tr>
</table>
<?php

	// unlock all tables
	mysql_query("UNLOCK TABLES");
}

$page->endPage();
?>
