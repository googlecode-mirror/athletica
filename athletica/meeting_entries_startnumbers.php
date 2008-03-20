<?php

/**********
 *
 *	meeting_entries_startnumbers.php
 *	--------------------------------
 *	
 */
 
require('./lib/cl_gui_menulist.lib.php');
require('./lib/cl_gui_page.lib.php');

require('./lib/common.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(empty($_COOKIE['meeting_id'])) {
	AA_printErrorMsg($GLOBALS['strNoMeetingSelected']);
}
?>

<?php
//
// check if a heat is assigned
//
$heats_done = "false";
$res = mysql_query("
		SELECT xRunde FROM
			runde 
			LEFT JOIN wettkampf USING (xWettkampf)
		WHERE
			(Status = ".$cfgRoundStatus['heats_done']."
			OR Status = ".$cfgRoundStatus['results_in_progress']."
			OR Status = ".$cfgRoundStatus['results_done'].")
			AND xMeeting = ".$_COOKIE['meeting_id']
);

if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	if(mysql_num_rows($res) > 0){
		$heats_done = "true";
	}
}
      

if($_GET['arg'] == 'assign')
{
	if ($_GET['sort']!="del")		// assign startnumbers
	{    
		// sort argument
		if ($_GET['sort']=="name") {
		  $argument="at.Name, at.Vorname";
	  	} else if ($_GET['sort']=="nbr") {
		  $argument="a.Startnummer";
		} else if ($_GET['sort']=="cat" && !$_GET['assign']=="perdicipline") {
		  $argument="k.Anzeige, at.Name, at.Vorname";
		} else if ($_GET['sort']=="club") {
		  $argument="v.Sortierwert, at.Name, at.Vorname"; 
        } else if ($_GET['sort']=="discipline" && !$_GET['assign']=="percontestcat" && !$_GET['assign']=="percategory") {
          $argument="w.xDisziplin, v.Sortierwert, at.Name, at.Vorname";
		} else {
		  $argument="at.Name, at.Vorname";
		}                                         
		
		                                   
		// check on assign per category. if contest cat is choosen process in a special way
		       
	   if($_GET['assign']=="percontestcat"){  
			if(isset($_GET['persvmteam'])){
				$argument = "t.Name, ".$argument;
			}
			$argument = "w.xKategorie, ".$argument;
			
			if((!empty($_GET['teamgap'])) || ($_GET['teamgap'] == '0'))  {
				$teamgap = $_GET['teamgap'];	// nbr gap between each team
			}
			else {
				$teamgap = 10;	// default
			}
			
			//
			// Read athletes
			//
			
			mysql_query("
				LOCK TABLES
					athlet AS a READ
					, kategorie AS k READ
					, verein AS v READ
					, anmeldung AS a wRITE
					, wettkampf AS w READ
					, start AS s READ
					, team AS t READ
			");
		/*	
			$result = mysql_query("
				SELECT
					DISTINCT(anmeldung.xAnmeldung)
					, wettkampf.xKategorie
					, athlet.xVerein
					, anmeldung.xTeam
				FROM
					anmeldung
					, athlet
					, kategorie
					, verein
					, start as s
					, wettkampf
					LEFT JOIN team ON team.xTeam = anmeldung.xTeam
				WHERE anmeldung.xMeeting = " . $_COOKIE['meeting_id'] . "
				AND anmeldung.xAthlet = athlet.xAthlet
				AND athlet.xVerein = verein.xVerein
				AND s.xAnmeldung = anmeldung.xAnmeldung
				AND wettkampf.xWettkampf = s.xWettkampf
				AND kategorie.xKategorie = wettkampf.xKategorie
				ORDER BY
					$argument
			");
			
       */ 
            $result = mysql_query("
                SELECT 
                    DISTINCT (a.xAnmeldung) ,
                    w.xKategorie , 
                    at.xVerein , 
                    a.xTeam 
                FROM 
                    anmeldung AS a
                    LEFT JOIN athlet AS at ON a.xAthlet = at.xAthlet        
                    INNER JOIN verein AS v ON at.xVerein = v.xVerein 
                    INNER JOIN start AS s ON s.xAnmeldung = a.xAnmeldung 
                    INNER JOIN wettkampf AS w USING (xWettkampf)
                    INNER JOIN kategorie AS k ON k.xKategorie = w.xKategorie
                    LEFT JOIN team AS t ON t.xTeam = a.xTeam 
                WHERE 
                    a.xMeeting = " . $_COOKIE['meeting_id'] . " 
                    ORDER BY      
                         $argument
               ");    
            
			if(mysql_errno() > 0)		// DB error
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else if(mysql_num_rows($result) > 0)  // data found
			{ 
			  $k = 0;	// initialize current category
			  $v = 0;	// initialize current club
			  $t = 0;	// current team
			  $limit = 0;	// hold limit
			  
			  // Assign startnumbers
			  while ($row = mysql_fetch_row($result))
			  {
				// set per category from, to
				
				if ($k != $row[1]){			// new category
					$nbr = $_GET["of2_$row[1]"];
					$nbr==0?1:$nbr;
					$limit = $_GET["to2_$row[1]"];
				}elseif($t != $row[3]			// new team
					&& $teamgap > 0
					&& $t > 0
					&& isset($_GET['persvmteam']))
				{
					$nbr += $teamgap;
				}else{
					$nbr++;
					if(($limit > 0 && $nbr > $limit) || $limit == 0){
						$nbr = 0;
						$limit = 0;
					}
				}
				
				mysql_query("
					UPDATE anmeldung SET
						Startnummer='$nbr'
					WHERE xAnmeldung = $row[0]
				");
				
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				
				$k = $row[1];	// keep current category
				$v = $row[2];	// keep current club
				$t = $row[3];	// keep current team
				
				
			  }
			  mysql_free_result($result);
			}						// ET DB error
			mysql_query("UNLOCK TABLES");
			
		}   
        //
        // assign per disciplines
        //          
          elseif ($_GET['assign']=="perdiscipline"){  
                if(isset($_GET['persvmteam'])){
                   $argument = "t.Name, ".$argument;
                }   
            $argument = "w.xDisziplin, ".$argument;   
            
            // assignment rules
            if(!empty($_GET['start'])) {
              $nbr = $_GET['start'] - 1;        // first number
            }
            else {
                $nbr = $cfgNbrStartWith - 1;    // default
            }
            
            if((!empty($_GET['catgap'])) || ($_GET['catgap'] == '0')) {
              $catgap = $_GET['catgap'];        // nbr gap between each category
            }
            else {
                $catgap = $cfgNbrCategoryGap;    // default
            }
            
            if((!empty($_GET['clubgap'])) || ($_GET['clubgap'] == '0'))  {
              $clubgap = $_GET['clubgap'];    // nbr gap between each club
            }
            else {
              $clubgap = $cfgNbrClubyGap;    // default
            }
            
            if((!empty($_GET['teamgap'])) || ($_GET['teamgap'] == '0'))  {
                $teamgap = $_GET['teamgap'];    // nbr gap between each team
            }
            else {
                $teamgap = 10;    // default
            }
            
            //
            // Read athletes
            //
            
            mysql_query("
                LOCK TABLES
                    athlet AS at READ
                    , kategorie AS k READ
                    , verein As v READ
                    , anmeldung AS a WRITE
                    , anmeldung AS a READ
                    , team AS t READ
            ");
            
            $result = mysql_query("
                        SELECT 
                            a.xAnmeldung
                            , w.xDisziplin, at.xVerein , a.xTeam 
                        FROM 
                            anmeldung AS a
                            LEFT  JOIN athlet AS at ON a.xAthlet = at.xAthlet
                            INNER JOIN verein AS v ON at.xVerein = v.xVerein
                            INNER  JOIN  START  AS s ON a.xAnmeldung = s.xAnmeldung
                            INNER  JOIN wettkampf AS w USING ( xWettkampf )                         
                            INNER  JOIN disziplin AS d USING ( xDisziplin )                        
                            LEFT  JOIN team as t ON t.xTeam = a.xTeam
                        WHERE 
                            a.xMeeting = " . $_COOKIE['meeting_id'] . "                                  
                            ORDER BY
                                $argument
                    ");    
            
            if(mysql_errno() > 0)        // DB error
            {
                AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
            }
            else if(mysql_num_rows($result) > 0)  // data found
            {
              $d = 0;    // initialize current discipline
              $v = 0;    // initialize current club
              $t = 0;    // current team
              $limit = 0;    // hold limit
              
              // Assign startnumbers
              while ($row = mysql_fetch_row($result))
              {  
                    // set per discipline from, to
                        
                    if ($d != $row[1]){            // new discipline
                        $nbr = $_GET["of_$row[1]"];
                        $nbr==0?1:$nbr;
                        $limit = $_GET["to_$row[1]"];
                    }elseif($t != $row[3]            // new team
                        && $teamgap > 0
                        && $t > 0
                        && isset($_GET['persvmteam']))
                    {
                        $nbr += $teamgap;
                    }else{
                        $nbr++;
                        if(($limit > 0 && $nbr > $limit) || $limit == 0){
                            $nbr = 0;
                            $limit = 0;
                        }
                    }  
                 
                mysql_query("
                    UPDATE anmeldung SET
                        Startnummer='$nbr'
                    WHERE xAnmeldung = $row[0]
                ");
                
                if(mysql_errno() > 0) {
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                
                $d = $row[1];    // keep current discipline
                $v = $row[2];    // keep current club
                $t = $row[3];    // keep current team
                
            }
            mysql_free_result($result);
            }                        // ET DB error
            mysql_query("UNLOCK TABLES");    
        }  
        
		//
		// assign per athletes category
		//
		else{ 
            if($_GET['assign']=="percategory"){  
				if(isset($_GET['persvmteam'])){
					$argument = "t.Name, ".$argument;
				}
				$argument = "k.Anzeige, ".$argument; 
			}
			   
			// assignment rules
			if(!empty($_GET['start'])) {
			  $nbr = $_GET['start'] - 1;		// first number
			}
			else {
				$nbr = $cfgNbrStartWith - 1;	// default
			}
			
			if((!empty($_GET['catgap'])) || ($_GET['catgap'] == '0')) {
			  $catgap = $_GET['catgap'];		// nbr gap between each category
			}
			else {
				$catgap = $cfgNbrCategoryGap;	// default
			}
			
			if((!empty($_GET['clubgap'])) || ($_GET['clubgap'] == '0'))  {
			  $clubgap = $_GET['clubgap'];	// nbr gap between each club
			}
			else {
			  $clubgap = $cfgNbrClubyGap;	// default
			}
			
			if((!empty($_GET['teamgap'])) || ($_GET['teamgap'] == '0'))  {
				$teamgap = $_GET['teamgap'];	// nbr gap between each team
			}
			else {
				$teamgap = 10;	// default
			}
			
			//
			// Read athletes
			//
			
			mysql_query("
				LOCK TABLES
					athlet AS a READ
					, kategorie AS k READ
					, verein AS v READ
					, anmeldung AS a wRITE
					, team AS t READ
			");
			/*
			$result = mysql_query("
				SELECT
					anmeldung.xAnmeldung
					, anmeldung.xKategorie
					, athlet.xVerein
					, anmeldung.xTeam
				FROM
					anmeldung
					, athlet
					, kategorie
					, verein
					LEFT JOIN team ON team.xTeam = anmeldung.xTeam
				WHERE anmeldung.xMeeting = " . $_COOKIE['meeting_id'] . "
				AND anmeldung.xAthlet = athlet.xAthlet
				AND anmeldung.xKategorie = kategorie.xKategorie
				AND athlet.xVerein = verein.xVerein
				ORDER BY
					$argument
			");
            
            */
            $result = mysql_query("
                SELECT
                    a.xAnmeldung
                    , a.xKategorie
                    , at.xVerein
                    , a.xTeam
                FROM
                    anmeldung AS a
                    LEFT JOIN athlet AS at USING (xAthlet)
                    INNER JOIN kategorie AS k ON a.xKategorie = k.xKategorie
                    INNER JOIN verein AS v  ON at.xVerein = v.xVerein 
                    LEFT JOIN team AS t ON t.xTeam = a.xTeam
                WHERE a.xMeeting = " . $_COOKIE['meeting_id'] . " 
                ORDER BY
                    $argument
           ");  
            
			if(mysql_errno() > 0)		// DB error
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else if(mysql_num_rows($result) > 0)  // data found
			{
			  $k = 0;	// initialize current category
			  $v = 0;	// initialize current club
			  $t = 0;	// current team
			  $limit = 0;	// hold limit
			  
			  // Assign startnumbers
			  while ($row = mysql_fetch_row($result))
			  {   
				 // set per category from, to
					   
					if ($k != $row[1]){			// new category
						$nbr = $_GET["of_$row[1]"];
						$nbr==0?1:$nbr;
						$limit = $_GET["to_$row[1]"];
					}elseif($t != $row[3]			// new team
						&& $teamgap > 0
						&& $t > 0
						&& isset($_GET['persvmteam']))
					{
						$nbr += $teamgap;
					}else{
						$nbr++;
						if(($limit > 0 && $nbr > $limit) || $limit == 0){
							$nbr = 0;
							$limit = 0;
						}
					}   
					
				mysql_query("
					UPDATE anmeldung SET
						Startnummer='$nbr'
					WHERE xAnmeldung = $row[0]
				");
				
				if(mysql_errno() > 0) {
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				
				$k = $row[1];	// keep current category
				$v = $row[2];	// keep current club
				$t = $row[3];	// keep current team
				
			}
			mysql_free_result($result);
			}						// ET DB error
			mysql_query("UNLOCK TABLES");
			
		}
	}
	else		// delete startnumbers
	{
		mysql_query("LOCK TABLE anmeldung WRITE");

	  	mysql_query("
			UPDATE anmeldung SET
				Startnummer = 0
			WHERE xMeeting=" . $_COOKIE['meeting_id']
		);

		if(mysql_errno() > 0)
		{
		  AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}

		mysql_query("UNLOCK TABLES");
	}
}

//
// show dialog 
//

$page = new GUI_Page('meeting_entries_startnumbers');
$page->startPage();
$page->printPageTitle($strAssignStartnumbers);

if($_GET['arg'] == 'assign')	// refresh list
{
	?>
	<script>
		window.open("meeting_entrylist.php", "list")
	</script>
	<?php
}
?>

<script>

function check_rounds(){
	
	//if(true == <?php echo $heats_done ?>){
		
		check = confirm("<?php echo $strStartNrConfirm ?>");
		return check;
	//}
	
}

function select_PerCategory(){
	
	e = document.getElementById("percategory");
	if(!e.checked){
		e.click();
	}
	
}

function select_PerDiscipline(){
    
    e = document.getElementById("perdiscipline");
    if(!e.checked){
        e.click();
    }
    
}

function select_PerContestCat(){
	
	e = document.getElementById("percontestcat");
	if(!e.checked){
		e.click();
	}
	
}

function select_PerSvmTeam(arg){
	
	e = document.getElementById("persvmteam"+arg);
	if(!e.checked){
		e.click();
	}
	
}

function select_CheckCat(){  
     h = document.getElementById("discipline"); 
     if (h.checked){
          h.checked=false; 
           i = document.getElementById("name"); 
           i.checked=true;  
     }  
}

function select_CheckDisc(){ 
      e = document.getElementById("cat");
      f = document.getElementById("club_cat");  
      g = document.getElementById("cat_club");    
      if (e.checked){ 
            h = document.getElementById("discipline");        
            h.checked=true; 
      }
      if (f.checked ||  g.checked){ 
            h = document.getElementById("club");        
            h.checked=true; 
      }  
} 
    

</script>

<form action='meeting_entries_startnumbers.php' method='get'>
<input type='hidden' name='arg' value='assign'>
<table class='dialog'>
<tr>
	<th class='dialog'><?php echo $strSortBy; ?></th>
	<th class='dialog' colspan='2'><?php echo $strRules; ?></th>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' id='name' value='name' checked>
			<?php echo $strName; ?></input>
	</td>
	<td class='dialog'>
		<?php echo $strBeginningWith; ?>
	</td>
	<td>
		<input class='nbr' type='text' name='start' maxlength='5' value='<?php echo $cfgNbrStartWith; ?>'>
	</td>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' id='cat' value='cat'>
			<?php echo $strCategory; ?></input>
	</td>
	<td colspan='2'>
		<?php echo $strGapBetween; ?>
	</td>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' id='club' value='club'>
			<?php echo $strClub; ?></input>
	</td>
	<td class='dialog'>
		<?php echo $strCategory; ?>
	</td>
	<td class='dialog'>
		<input class='nbr' type='text' name='catgap' maxlength='4' value='<?php echo $cfgNbrCategoryGap; ?>'>
	</td>
</tr>



<tr>
	<td class='dialog'>  
        <input type='radio' name='sort' id='discipline' value='discipline' >
            <?php echo $strDiscipline  ?></input>
	</td>
	<td class='dialog'>
		<?php echo $strClub; ?>
	</td>
	<td class='dialog'>
		<input class='nbr' type='text' name='clubgap' maxlength='4' value='<?php echo $cfgNbrClubGap; ?>'>
	</td>
</tr>  
<tr>
	<td class='dialog' colspan="3">
		<hr>  
         <input type="radio" name="assign" id="percategory" value="percategory" onchange="select_CheckCat()"
            checked> </input>
		<?php echo $strPerCategory; ?>
	</th>      
</tr>
<?php

// get all used categories in this meeting
$res = mysql_query("	SELECT
				DISTINCT(a.xKategorie)
				, k.Kurzname
			FROM
				anmeldung as a
				LEFT JOIN kategorie as k USING (xKategorie)
			WHERE
				a.xMeeting = ".$_COOKIE['meeting_id']."   
			ORDER BY
				k.Anzeige");
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	while($row = mysql_Fetch_array($res)){ 
		?>
<tr>
	<td class='dialog'><?php echo $row[1] ?></td>
	<td class='forms'>
		<?php echo $strOf ?>
		<input type="text" size="3" value="0" name="of_<?php echo $row[0] ?>" onchange="select_PerCategory()">
	</td>
	<td class='forms'>
		<?php echo $strTo ?>
		<input type="text" size="3" value="0" name="to_<?php echo $row[0] ?>" onchange="select_PerCategory()">
	</td>
</tr>
		<?php
	}
}

?>

<tr>
	<td class='dialog' colspan="2">
		<input type="checkbox" name="persvmteam" id="persvmteam1" value="persvmteam">
		<?php echo $strPerSvmTeam; ?>
	</th>
	<td class='forms'>
		<?php echo $strGap.":" ?>
		<input type="text" size="3" value="10" name="teamgap" onchange="select_PerSvmTeam('1')">
	</td>
</tr>

<tr>
	<td class='dialog' colspan="3">
		<hr>   
        <input type="radio" name="assign" id="percontestcat" value="percontestcat" onchange="select_CheckCat()"> </input>
		<?php echo $strPerContestCategory; ?>
	</th>
</tr>
<?php

// get all used categories in contest
$res = mysql_query("	SELECT 
				DISTINCT(w.xKategorie)
				, k.Kurzname
			FROM
				wettkampf as w
				, kategorie as k
			WHERE
				w.xKategorie = k.xKategorie
			AND	w.xMeeting = ".$_COOKIE['meeting_id']."
			ORDER BY
				k.Anzeige");
if(mysql_errno() > 0){
	AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
	while($row = mysql_Fetch_array($res)){
		?>
<tr>
	<td class='dialog'><?php echo $row[1] ?></td>
	<td class='forms'>
		<?php echo $strOf ?>
		<input type="text" size="3" value="0" name="of2_<?php echo $row[0] ?>" onchange="select_PerContestCat()">
	</td>
	<td class='forms'>
		<?php echo $strTo ?>
		<input type="text" size="3" value="0" name="to2_<?php echo $row[0] ?>" onchange="select_PerContestCat()">
	</td>
</tr>
		<?php
	}
}

?>

<tr>
	<td class='dialog' colspan="2">
		<input type="checkbox" name="persvmteam" id="persvmteam2" value="persvmteam">
		<?php echo $strPerSvmTeam; ?>
	</th>
	<td class='forms'>
		<?php echo $strGap.":" ?>
		<input type="text" size="3" value="10" name="teamgap" onchange="select_PerSvmTeam('2')">
	</td>
</tr> 

<tr>
    <td class='dialog' colspan="3">
        <hr>  
        <input type="radio" name="assign" id="perdiscipline" value="perdiscipline" onchange="select_CheckDisc()"> </input> 
        <?php echo $strPerDiscipline; ?>
    </th>      
</tr>
<?php

// get all used disziplines in this meeting
$res = mysql_query("    SELECT
                DISTINCT (w.xDisziplin)  , d.Name
            FROM
               anmeldung as a
                INNER JOIN start as s USING (xAnmeldung)
                INNER JOIN wettkampf as w USING (xWettkampf)
                INNER JOIN disziplin as d USING (xDisziplin)
            WHERE
                a.xMeeting = ".$_COOKIE['meeting_id']."
            ORDER BY
                d.Anzeige");
                                                        
      
if(mysql_errno() > 0){
    AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
    while($row = mysql_Fetch_array($res)){ 
        ?>
<tr>
    <td class='dialog'><?php echo $row[1] ?></td>
    <td class='forms'>
        <?php echo $strOf ?>
        <input type="text" size="3" value="0" name="of_<?php echo $row[0] ?>" onchange="select_PerDiscipline()">
    </td>
    <td class='forms'>
        <?php echo $strTo ?>
        <input type="text" size="3" value="0" name="to_<?php echo $row[0] ?>" onchange="select_PerDiscipline()">
    </td>
</tr>
        <?php
    }
}

?>

<tr>
    <td class='dialog' colspan="2">
        <input type="checkbox" name="persvmteam" id="persvmteam3" value="persvmteam">
        <?php echo $strPerSvmTeam; ?>
    </th>
    <td class='forms'>
        <?php echo $strGap.":" ?>
        <input type="text" size="3" value="10" name="teamgap" onchange="select_PerSvmTeam('3')">
    </td>
</tr> 
 
<tr>
	<td class='dialog' colspan = 3>
		<hr>
		<input type='radio' name='sort' value='del'>
			<?php echo $strDeleteStartnumbers; ?></input>
	</td>
</tr>

</table>

<p />

<table>
<tr>
	<td>
		<button type='submit' onclick="return check_rounds();">
			<?php echo $strAssign; ?>
	  	</button>
	</td>
</tr>
</table>

</form>
 
</body>
</html>
