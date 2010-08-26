<?php

/**********
 *
 *	meeting_teamsm.php
 *	-----------------
 *	
 */

require('./lib/cl_gui_button.lib.php');
require('./lib/cl_gui_page.lib.php');
require('./lib/cl_gui_select.lib.php');

require('./lib/meeting.lib.php'); 
require('./lib/common.lib.php');
require('./lib/cl_performance.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

//
// change team
//
if ($_POST['arg']=="change")
{
	if(!empty($_POST['name']) && !empty($_POST['item'])){
		
		// update team name
		mysql_query("UPDATE teamsm SET
				Name = '".$_POST['name']."'
			WHERE
				xTeamsm = ".$_POST['item']."");
		
		if(mysql_errno()>0){ 
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}
	}else{
		AA_printErrorMsg($strErrEmptyFields);
	}
}

//
// change startnumber teamsm
//
if($_POST['arg'] == "change_startnbr"){
    
    mysql_query("LOCK TABLES teamsm WRITE, anmeldung READ");
    
    $n = $_POST['startnumber'];
    
    // check if nbr already exists
    $res = mysql_query("SELECT * FROM teamsm WHERE Startnummer = $n AND xMeeting = ".$_COOKIE['meeting_id']);
    
    if(mysql_num_rows($res) == 0){
        
        // check if start number exists in athlete registration
        $res = mysql_query("SELECT * FROM anmeldung 
                    WHERE Startnummer = $n 
                    AND xMeeting = ".$_COOKIE['meeting_id']);
        if(mysql_num_rows($res) == 0){
            
            mysql_query("UPDATE teamsm SET Startnummer = $n WHERE xTeamsm = ".$_POST['item']);
            if(mysql_errno() > 0) { 
                AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
            }
            
        }else{
            AA_printErrorMsg($strStartnumberLong . $strErrNotValid);
        }
    }else{
        AA_printErrorMsg($strStartnumberLong . $strErrNotValid);
    }
    
    mysql_query("UNLOCK TABLES");
    
}

//
// add team athlete
//
if ($_POST['arg']=="add_athlete" || $_POST['arg']=="add_athlete_stnr")
{
	if( $_POST['arg']=="add_athlete" && (!empty($_POST['athlete']) && !empty($_POST['item']) && !empty($_POST['event']))
            || $_POST['arg']=="add_athlete_stnr" && (!empty($_POST['startnr']) && !empty($_POST['item']) && !empty($_POST['event']))  ){          
		
		
        $msgError = 0;    
        if ($_POST['arg']=="add_athlete_stnr"){
           $sql = " SELECT xAnmeldung FROM anmeldung WHERE Startnummer = ".$_POST['startnr'];
           $res = mysql_query($sql);
           if(mysql_errno() > 0)    // check DB error
                {  $msgError = 1;  
                   AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
           }
           if (mysql_num_rows($res) > 0){
              $row = mysql_fetch_row($res); 
              $_POST['athlete'] = $row[0];  
           }
           else {
               $msgError = 1; 
               AA_printErrorMsg($strStrNrNotExist);  
           }
       }         
        if ($msgError == 0){        
            mysql_query("LOCK TABLES start WRITE, teamsmathlet WRITE, athlet READ, anmeldung READ, disziplin READ, kategorie READ, wettkampf READ, base_performance READ, base_athlete READ");
		       
            if (!is_array($_POST['athlete'])){
                $tmp_athlete= $_POST['athlete'];
                $_POST['athlete'] = array();
                $_POST['athlete'][] = $tmp_athlete;
            }
         
            foreach ($_POST['athlete'] as $key => $val){   
           
		        // check if athlete already starts for event and set if not
		        $res = mysql_query("SELECT
					        xStart
				        FROM
					        start
				        WHERE
					        xAnmeldung = ".$_POST['athlete'][$key]."
				            AND	xWettkampf = ".$_POST['event']."");
       
		        if(mysql_errno()>0){  
			        AA_printErrorMsg(mysql_errno().": ".mysql_error());
		        }else{
			
			        if(mysql_num_rows($res) == 0){
				
				        // get top performance
				        $sql_xAthlet = "SELECT lizenznummer 
								  FROM athlet
							 LEFT JOIN anmeldung USING(xAthlet) 
								 WHERE xAnmeldung = ".$_POST['athlete'][$key].";";
				        $query_xAthlet = mysql_query($sql_xAthlet);
				
				        $licence = (mysql_num_rows($query_xAthlet)==1 && mysql_result($query_xAthlet, 0, 'lizenznummer')!='') ? mysql_result($query_xAthlet, 0, 'lizenznummer') : '';
				        $perf = 0;
				        if($licence != ''){
					        // need codes of category and discipline
					        $res = mysql_query("
						        SELECT disziplin.Code, kategorie.Code, disziplin.Typ FROM
							        disziplin
							        , kategorie
							        , wettkampf
						        WHERE	wettkampf.xWettkampf = ".$_POST['event']."
						            AND	wettkampf.xDisziplin = disziplin.xDisziplin
						            AND	wettkampf.xKategorie = kategorie.xKategorie");
					
					        if($res){
						        $rowCodes = mysql_fetch_array($res);
						        $res = mysql_query("
							        SELECT
								        base_performance.best_effort
								        , base_performance.season_effort
							        FROM
								        base_performance
								        , base_athlete
							        WHERE	base_athlete.license = ".$licence."
							        AND	base_performance.id_athlete = base_athlete.id_athlete
							        AND	base_performance.discipline = ".$rowCodes[0]);
					        }
					        if(mysql_errno() > 0){  
						        AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					        }else{
						        $bigger = 0;
						        $smaller = 0;
						        $rowPerf = mysql_fetch_array($res);
						        if($rowPerf[0] > $rowPerf[1]){
							        $bigger = $rowPerf[0];
							        $smaller = $rowPerf[1];
						        }else{
							        $bigger = $rowPerf[1];
							        $smaller = $rowPerf[0];
						        }
						        if($bigger == 0 || empty($bigger)){ $bigger = $smaller; }
						        if($smaller == 0 || empty($smaller)){ $smaller = $bigger; }
						            //echo ltrim($bigger,"0");
						
						        if(($rowCodes[2] == $cfgDisciplineType[$strDiscTypeTrack])
							    || ($rowCodes[2] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
							    || ($rowCodes[2] == $cfgDisciplineType[$strDiscTypeRelay])
							    || ($rowCodes[2] == $cfgDisciplineType[$strDiscTypeDistance]))
							    {
							    $pt = new PerformanceTime(trim($smaller));
							    $perf = $pt->getPerformance();
							
						        }
						        else {
							        //echo $bigger;
							        $perf = (ltrim($bigger,"0"))*100;
						        }
						        if($perf == NULL) {	// invalid performance
							        $perf = 0;
						        }
					        }
				        }
				
				        mysql_query("INSERT INTO start SET
						    xAnmeldung = ".$_POST['athlete'][$key]."
						    , xWettkampf = ".$_POST['event']."
						    , Bestleistung = $perf;");
				        if(mysql_errno()>0){ 
					        AA_printErrorMsg(mysql_errno().": ".mysql_error());
				        }
				
			        }
			
			        // insert
			        mysql_query("INSERT INTO teamsmathlet SET
					    xTeamsm = ".$_POST['item']."
					    , xAnmeldung = ".$_POST['athlete'][$key]."");
			        if(mysql_errno()>0){ 
				        AA_printErrorMsg(mysql_errno().": ".mysql_error());
			        }
		        }
		    }          // end foreach
        
		    mysql_query("UNLOCK TABLES");  
        }
	}
}

//
// remove team athlete
//
if ($_GET['arg']=="del_athlete")
{
	if(!empty($_GET['item']) && !empty($_GET['athlete'])){
		
		$_POST['item'] = $_GET['item'];
		
		mysql_query("DELETE FROM teamsmathlet
			WHERE	xTeamsm = ".$_GET['item']."
			AND	xAnmeldung = ".$_GET['athlete']."");
		
		$sql = "SELECT xWettkampf 
				  FROM teamsm 
				 WHERE xTeamsm = ".$_GET['item'].";";
		$query = mysql_query($sql);
		
		if($query && mysql_num_rows($query)==1){
			$teamsm = mysql_fetch_assoc($query);
			
			$sql2 = "DELETE FROM start 
						   WHERE xAnmeldung = ".$_GET['athlete']." 
							 AND xWettkampf = ".$teamsm['xWettkampf'].";";
			mysql_query($sql2);
		}
		
		if(mysql_errno()>0){ 
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}
	}
}

//
// Process del-request
//
if ($_GET['arg']=="del")
{
	if(!empty($_GET['item'])){
		
		mysql_query("LOCK TABLES teamsmathlet WRITE, teamsm WRITE");
		
		mysql_query("DELETE FROM teamsmathlet
			WHERE
				xTeamsm = ".$_GET['item']."");
		if(mysql_errno()>0){ 
			AA_printErrorMsg(mysql_errno().": ".mysql_error());
		}else{
			
			mysql_query("DELETE FROM teamsm
				WHERE
					xTeamsm = ".$_GET['item']."");
			if(mysql_errno()>0){   
				AA_printErrorMsg(mysql_errno().": ".mysql_error());
			}
			
		}
		
		mysql_query("UNLOCK TABLES");
	}
}

//
// display data
//
$page = new GUI_Page('meeting_teamsm');
$page->startPage();
$page->printPageTitle($strTeamTeamSM);

if($_GET['arg'] == 'del')	// refresh list
{
	?>
	<script type="text/javascript">
		window.open("meeting_teamsmlist.php", "list")
	</script>
	<?php
}
else 
{
	?>
	<script type="text/javascript">
		window.open("meeting_teamsmlist.php?item="
			+ <?php echo $_POST['item']; ?>, "list");
	</script>
	<?php
}

if($_POST['item'] > 0){

// get team
$result = mysql_query("
	SELECT
		t.Name
		, k.Kurzname
		, d.Kurzname
		, k2.Kurzname
		, t.xVerein
		, t.xWettkampf
		, a.xAnmeldung
		, a.Startnummer
		, at.Name
		, at.Vorname
		, at.Jahrgang 
        , t.Startnummer         
	FROM
		teamsm AS t
		LEFT JOIN teamsmathlet AS tsa USING(xTeamsm)
		LEFT JOIN anmeldung AS a USING(xAnmeldung)
		LEFT JOIN athlet AS at USING(xAthlet)
		, kategorie AS k
		, kategorie AS k2
		, wettkampf AS w
		, disziplin AS d
	WHERE
		t.xTeamsm = ".$_POST['item']."
	AND	k.xKategorie = t.xKategorie
	AND	w.xWettkampf = t.xWettkampf
	AND	d.xDisziplin = w.xDisziplin
	AND	k2.xKategorie = w.xKategorie
	ORDER BY
		a.Startnummer
");
    
if(mysql_errno() > 0)		// DB error
{              
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else if(mysql_num_rows($result) > 0)  // data found
{
	$row = mysql_fetch_array($result);
	$event = $row[5];
	$club = $row[4];
	
	?>
	
<table class='dialog'>
    <tr>
    <form action='meeting_teamsm.php' method='post' name='change_startnbr'>
    <th class='dialog'><?php echo $strStartnumberLong; ?></th>
    <td class='forms'>
        <input name='arg' type='hidden' value='change_startnbr' />
        <input name='item' type='hidden' value='<?php echo $_POST['item']  ?>' />
        <input name='start' type='hidden' value='<?php echo $row[5]; ?>' />         
        <input class='nbr' name='startnumber' type='text'
            maxlength='5' value="<?php echo $row[11]; ?>"
            onchange='document.change_startnbr.submit()' />
    </td>
    </form>
</tr>

	<form action="meeting_teamsm.php" method="POST" name="teamsm">
	<input type="hidden" name="arg" value="change">
	<input type="hidden" name="item" value="<?php echo $_POST['item'] ?>">
	<tr>
		<th class="dialog"><?php echo $strName ?></th>
		<td class="forms"><input type="text" name="name" value="<?php echo $row[0] ?>"
			onchange="document.teamsm.submit()"></td>
	</tr>
	<tr>
		<th class="dialog"><?php echo $strCategory ?></th>
		<td class="dialog"><?php echo $row[1] ?></td>
	</tr>
	<tr>
		<th class="dialog"><?php echo $strDiscipline ?></th>
		<td class="dialog"><?php echo $row[2]." (".$row[3].")" ?></td>
	</tr>
	</form>
	
</table>
<br>
<table class="dialog">
	<tr>
		<th class="dialog"><?php echo $strNbr ?></th>
		<th class="dialog"><?php echo $strName ?></th>
		<th class="dialog"><?php echo $strFirstname ?></th>
		<th class="dialog"><?php echo $strYear ?></th>
		<th class="dialog"> </th>
	</tr>
	<?php
	//
	// print team athletes if present
	//
	if(!empty($row[6])){
		
		do{
			?>
			<tr>
				<td class="dialog"><?php echo $row[7] ?></td>
				<td class="dialog"><?php echo $row[8] ?></td>
				<td class="dialog"><?php echo $row[9] ?></td>
				<td class="dialog"><?php echo $row[10] ?></td>
				<td class="forms">
					<input type="button" value="<?php echo $strDelete ?>"
						onclick="document.location.href='meeting_teamsm.php?arg=del_athlete&item=<?php echo $_POST['item'] ?>&athlete=<?php echo $row[6] ?>'">
				</td>
			</tr>
			<?php
		}while($row = mysql_fetch_array($result));
		
	}
	mysql_free_result($result);
	
	//
	// output selection boxes for athletes
	//
	?>
	
	<tr><td colspan="5"><hr></td></tr>
	
	<tr>
		<form action="meeting_teamsm.php" method="POST" name="teamsm_add1">
		<input type="hidden" name="arg" value="add_athlete">
		<input type="hidden" name="item" value="<?php echo $_POST['item'] ?>">
		<input type="hidden" name="event" value="<?php echo $event ?>">
		<td class="forms" colspan="3">
		<?php
		// athletes who are registered for the current team event      
		$dropdown = new GUI_Select("athlete", 1, "document.teamsm_add1.submit()");
		$dropdown->addOption($strUnassignedAthletes, 0);
		$dropdown->addOptionsFromDB("
			SELECT
				a.xAnmeldung
				, CONCAT(a.Startnummer, '. ', at.Name, ' ', at.Vorname, ' ( ' , at.Jahrgang, ' )')
			FROM
				anmeldung AS a
				, athlet AS at
				, start AS st
				LEFT JOIN teamsmathlet AS tsa ON (tsa.xAnmeldung = a.xAnmeldung
					AND tsa.xTeamsm = ".$_POST['item'].")
			WHERE
				tsa.xTeamsm IS NULL
			AND	st.xWettkampf = $event
			AND	a.xAnmeldung = st.xAnmeldung
			AND	at.xAthlet = a.xAthlet
			AND	at.xVerein = $club
			ORDER BY
				at.Name
				, at.Vorname
		");
	    
		if(!empty($GLOBALS['AA_ERROR']))
		{
			AA_printErrorMsg($GLOBALS['AA_ERROR']);
		}
		$dropdown->printList();
		?>
		</td>
		</form>
		
		<form action="meeting_teamsm.php" method="POST" name="teamsm_add2">
		<input type="hidden" name="arg" value="add_athlete">
		<input type="hidden" name="item" value="<?php echo $_POST['item'] ?>">
		<input type="hidden" name="event" value="<?php echo $event ?>">
		<td class="forms" colspan="2">
		<?php
		// athletes who are in the right club and LG          
        $sqlClubLG=" = " .$club;
        $arrClub=AA_meeting_getLG_Club($club);      // get all clubs with same LG
       
        if (count($arrClub) > 0) {
            $sqlClubLG=" IN (";
            foreach ($arrClub as $key => $val) {
                $sqlClubLG.=$val .",";              
           }
           $sqlClubLG=substr($sqlClubLG,0,-1);
           $sqlClubLG.=")";
        }         
		$dropdown = new GUI_Select("athlete", 1, "document.teamsm_add2.submit()");
		$dropdown->addOption($strOtherAthletes, 0);
		$dropdown->addOptionsFromDB("
			SELECT
				a.xAnmeldung
				, CONCAT(a.Startnummer, '. ', at.Name, ' ', at.Vorname, ' ( ' , at.Jahrgang, ' )')
			FROM
				anmeldung AS a
				, athlet AS at
				LEFT JOIN teamsmathlet AS tsa ON (tsa.xAnmeldung = a.xAnmeldung
					AND tsa.xTeamsm = ".$_POST['item'].")
				LEFT JOIN start AS st ON (st.xAnmeldung = a.xAnmeldung
					AND st.xWettkampf = $event)
			WHERE
				tsa.xTeamsm IS NULL
			
			AND	at.xAthlet = a.xAthlet
			AND	at.xVerein $sqlClubLG      
			AND	a.xMeeting = ".$_COOKIE['meeting_id']."
			ORDER BY
				at.Name
				, at.Vorname
		");  
      
		if(!empty($GLOBALS['AA_ERROR']))
		{
			AA_printErrorMsg($GLOBALS['AA_ERROR']);
		}
		$dropdown->printList();
		?>
		</td>
		</form>
	</tr>
	<tr><td colspan="5"><hr></td></tr>
	<tr>
		<form action="meeting_teamsm.php" method="POST" name="teamsm_add3"> 
		<input type="hidden" name="arg" value="add_athlete">
		<input type="hidden" name="item" value="<?php echo $_POST['item'] ?>">
		<input type="hidden" name="event" value="<?php echo $event ?>">
		<td class="forms" colspan="4">
		<?php
		// athletes who are in the right club
		$dropdown = new GUI_Select("athlete", 4, "", "multiple");
		$dropdown->addOption($strAllRegisteredAthletes, 0);
		$dropdown->addOptionsFromDB("
			SELECT
				a.xAnmeldung
				, CONCAT(a.Startnummer, '. ', at.Name, ' ', at.Vorname, ' ( ' , at.Jahrgang, ' )')
			FROM
				anmeldung AS a
				, athlet AS at
				LEFT JOIN teamsmathlet AS tsa ON (tsa.xAnmeldung = a.xAnmeldung
					AND tsa.xTeamsm = ".$_POST['item'].")
			WHERE
				tsa.xTeamsm IS NULL
			AND	at.xAthlet = a.xAthlet
			AND	a.xMeeting = ".$_COOKIE['meeting_id']."
			ORDER BY
				at.Name
				, at.Vorname
		");
        
	
		if(!empty($GLOBALS['AA_ERROR']))
		{
			AA_printErrorMsg($GLOBALS['AA_ERROR']);
		}
		$dropdown->printList();
		?>
		<br/>
        </td> 
        
        
        <td class="forms_bottom" > 
        <input type="button" value="<?php echo $strAdd ?>"
                        onclick="document.teamsm_add3.submit()">
                        
		</td>
		</form>         
  
	</tr>
    
   
    
    
    
     
     <tr><td class="blue" colspan="5"><?php echo $strCtrlHelp; ?></td></tr>  
     
     <tr><td colspan="5"><hr></td></tr> 
     
     <tr><form action='meeting_teamsm.php' method='post' >
    <td><?php echo $strStartnumberShort; ?>
    </td>
    <td class='forms' colspan='3'>
       <input type="hidden" name="arg" value="add_athlete_stnr"> 
       <input type="hidden" name="item" value="<?php echo $_POST['item'] ?>">
        <input type="hidden" name="event" value="<?php echo $event ?>">
        
       <input name='startnr' type='text' value='' size="4" onchange='this.form.submit()'/>  
    </td>
    
   
    </form>
 </tr> 
     
     
     
     
    <tr><td colspan="5"><br /><?php echo $strTeamSMClubRule; ?></td></tr> 
	
</table>
	<?php    
	
	echo "<br>";
	$btn = new GUI_Button("meeting_teamsm.php?arg=del&item=".$_POST['item'], $strDelete);
	$btn->printButton();
}// ET DB error
?>
<p />
<?php
} // if POST item > 0

$page->endPage();

?>
