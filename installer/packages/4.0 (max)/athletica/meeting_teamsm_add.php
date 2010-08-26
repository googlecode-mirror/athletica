<?php

/**********
 *
 *	meeting_teamsm_add.php
 *	--------------------
 *	
 */

require('./lib/cl_gui_page.lib.php');
require("./lib/cl_gui_dropdown.lib.php");

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	// invalid DB connection
{
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

// initialize variables
$category = 0;
if(!empty($_POST['category'])) {
	$category = $_POST['category'];	// store selected category
}
else if(!empty($_GET['cat'])) {
	$category = $_GET['cat'];	// store selected category
}

$club = 0;
if(!empty($_POST['club'])) {
	$club = $_POST['club'];	// store selected category
}

$event = 0;
if(!empty($_POST['event'])) {
	$event = $_POST['event'];	// store selected event
}

$nbr = 0;
if(!empty($_POST['startnumber'])) {
    $nbr = $_POST['startnumber'];    // store selected startnumber
}

//
// add team
//
if ($_POST['arg']=="add")
{
	$name = $_POST['name'];
	
	// Error: Empty fields
	if(empty($_POST['name']) || empty($_POST['category']) || empty($_POST['club']) || empty($_POST['event']))
	{                 
		AA_printErrorMsg($strErrEmptyFields);
	}else{  
		mysql_query("LOCK TABLES
				teamsm WRITE
				, wettkampf READ
				, kategorie READ
                , anmeldung READ
                , staffel READ
				, verein READ");
		
		if(AA_checkReference("kategorie", "xKategorie", $_POST['category']) == 0){
			AA_printErrorMsg($strCategory . $strErrNotValid);
		}elseif(AA_checkReference("wettkampf", "xWettkampf", $_POST['event']) == 0){
			AA_printErrorMsg($strEvent . $strErrNotValid);
		}else{
			
            // check startnumber
            $lastnbr = AA_getLastStartnbrTeamsm();
            $nbr = $_POST['startnumber'];
                               
            if($nbr > 0){
                $res = mysql_query("SELECT * FROM teamsm 
                                    WHERE Startnummer = $nbr 
                                    AND xMeeting = ".$_COOKIE['meeting_id']);
                if(mysql_num_rows($res) > 0){ 
                    $nbr = $lastnbr;
                    $nbr++;
                }
                //mysql_free_result($res);
                //
                // check if startnumber is used for athletes
                $res = mysql_query("SELECT * FROM anmeldung 
                                    WHERE Startnummer = $nbr 
                                    AND xMeeting = ".$_COOKIE['meeting_id']);
                if(mysql_num_rows($res) > 0){ 
                    $nbr = AA_getNextStartnbr($nbr);
                }
            }else{
                    if($lastnbr > 0){
                        $nbr = $lastnbr+1;
                        // check if startnumber is used for athletes
                        $res = mysql_query("SELECT * FROM anmeldung 
                                            WHERE Startnummer = $nbr 
                                            AND xMeeting = ".$_COOKIE['meeting_id']);
                        if(mysql_num_rows($res) > 0){
                            $nbr = AA_getNextStartnbr($nbr);
                        }
                    }
            }
            
			// add
			mysql_query("INSERT INTO teamsm SET
					Name = '".$_POST['name']."'
					, xKategorie = ".$_POST['category']."
					, xVerein = ".$_POST['club']."
					, xWettkampf = ".$_POST['event']."
                    , Startnummer = ".$nbr." 
					, xMeeting = ".$_COOKIE['meeting_id']."");
			
			if(mysql_errno() > 0){
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}else{
				$xTeam = mysql_insert_id();
			}
		}
		
		mysql_query("UNLOCK TABLES");
	}
}


//
// display data
//
$page = new GUI_Page('meeting_teamsm_add');
$page->startPage();
$page->printPageTitle("$strNewEntry $strTeamsTeamSM");

if ($_POST['arg']=="add")
{
	?>
<script>
	window.open("meeting_teamsmlist.php?item="
		+ <?php echo $xTeam; ?> + "#" + <?php echo $xTeam; ?>,
		"list");
</script>
	<?php
}


?>
<table>
<tr>
	<form action="meeting_teamsm_add.php" method="POST" name="select">
	<th class="dialog"><?php echo $strClub ?></td>
	<?php $dd = new GUI_ClubDropDown($club, true, "document.select.submit()"); ?>
	<th class="dialog"><?php echo $strCategory ?></td>
	<?php $dd = new GUI_CategoryDropDown($category, "document.select.event.value=0; document.select.submit()", false); ?>
	<th class="dialog"><?php echo $strDiscipline ?></td>
	<?php $dd = new GUI_EventDropDown($category, $event, "document.select.submit()"); ?>
	</form>
</tr>
</table>
<?php

if((!empty($event)) && (!empty($club)))		// category & club selected
{
	?>

<br>
<table class='dialog'>
<form action='meeting_teamsm_add.php' method='post' name='entry'>
<?php
// get information about the selected event, category and club
$res = mysql_query("SELECT d.Kurzname, k.Geschlecht FROM
			wettkampf AS w
			, disziplin AS d
			, kategorie AS k
		WHERE
			xWettkampf = $event
		AND	d.xDisziplin = w.xDisziplin
		AND	k.xKategorie = w.xKategorie");
$row = mysql_fetch_array($res);
$disciplineName = $row[0];
$categorySex = $row[1]; 
mysql_free_result($res);

$res = mysql_query("SELECT Name FROM
			Verein
		WHERE
			xVerein = $club");
$row = mysql_fetch_array($res);
$clubName = $row[0];
mysql_free_result($res);

?>
<tr>
<th class='dialog'><?php echo $strStartnumberLong ?></th>
    <td class='forms'>
        <?php
        
         $lastnbr = AA_getLastStartnbrTeamsm(); 
         $nbr = 0;
         if($lastnbr > 0){
            $nbr = $lastnbr+1;
        }
      
        ?>
        <input class='nbr' type='text' maxlength='6' name="startnumber" value="<?php echo $nbr ?>">
        <?php echo $strNextNr.": ".($lastnbr+1); ?>
    </td>
</tr>
<tr>
    <th class='dialog'><?php echo $strName; ?></th>
    <td class='forms'>
        <input name='arg' type='hidden' value='add' />
        <input name='event' type='hidden' value='<?php echo $event; ?>' />
        <input name='club' type='hidden' value='<?php echo $club; ?>' />
        <input class='text' name='name' type='text' maxlength='100'
            value="<?php echo $clubName." ".$disciplineName ?>" />
    </td>
</tr>
<tr>
	<th class='dialog'><?php echo $strCategory; ?></th>
	<td class="forms">
		<?php
		// get the ids of category MAN and WOM for choosing
		$tempcat = array();
		$res = mysql_query("SELECT xKategorie, Code FROM
					kategorie
				WHERE
					Code = 'MAN_' OR Code = 'WOM_'");
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}else{
			while($row = mysql_fetch_array($res)){
				$tempcat[$row[1]] = $row[0];
			}
		}
		?>
		<input type="radio" name="category" value="<?php echo $tempcat['MAN_'] ?>"
			<?php echo ($categorySex == 'm') ? "checked" : ""; ?>> MAN
		<input type="radio" name="category" value="<?php echo $tempcat['WOM_'] ?>"
			<?php echo ($categorySex == 'w') ? "checked" : ""; ?>> WOM   
	</td>
</tr>
</table>
<p/>
<table>
	<tr>
		<td class='forms'>
			<button type='submit'>
				<?php echo $strSave; ?>
		  	</button>
		</td>
	</tr>
</form>	
</table>

<?php
}			// ET category selected
?>

<script type="text/javascript">
<!--
	if(document.entry) {
		document.entry.name.focus();
		document.entry.name.select();
	}
//-->
</script>

<?php

$page->endPage();

?>
