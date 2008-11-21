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

$max_startnr = 0;

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
        } else if ($_GET['sort']=="cat" ) { 
		  $argument="k.Anzeige, at.Name, at.Vorname";
		} else if ($_GET['sort']=="club") {
		  $argument="v.Sortierwert, at.Name, at.Vorname";   
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
		
            $result = mysql_query("
                SELECT 
                    DISTINCT (a.xAnmeldung) ,
                    w.xKategorie , 
                    at.xVerein , 
                    a.xTeam, 
                    at.Name, 
                    at.Vorname,
                    t.Name 
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
                    $limit = $_GET["to2_$row[1]"];  
					//$nbr==0?1:$nbr;
                    $nbr=($nbr==0 && $limit>0)?1:$nbr;  
					
				}elseif($t != $row[3]			// new team
					&& $teamgap > 0
					&& $t > 0
					&& isset($_GET['persvmteam']))
				{
					if ($nbr > 0) {
                        $nbr += $teamgap;  
                    }
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
       
        // assign startnumbers per disciplines
        //       
          elseif ($_GET['assign']=="perdiscipline"){   
                if(isset($_GET['persvmteam'])){
                   $argument = "t.Name, ".$argument;    
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
  
            // startnumbers for disciplines        
                             
            if (!empty($_GET["of_track1"]) || !empty($_GET["of_track2"]) ||  !empty($_GET["of_tech"]) || 
                       !empty($_GET["to_track1"]) || !empty($_GET["to_track2"]) ||  !empty($_GET["to_tech"] )){  
                
              //  if ( (!empty($_GET["of_track1"]) || !empty($_GET["to_track1"]) ) )
              //      $desc='ASC';   
              //  else    
              //      $desc='ASC';  
                    
                // Assign startnumbers
                    $track1=false;
                    $track2=false; 
                    $tech=false; 
                
                    $nbr_track1 = $_GET["of_track1"];
                    $limit_track1 = $_GET["to_track1"]; 
                    $nbr_track1=($nbr_track1==0 && $limit_track1>0)?1:$nbr_track1;
                    if ($nbr_track1 > 0)
                        $track1=true;   
                    
                    $nbr_track2 = $_GET["of_track2"];
                    $limit_track2 = $_GET["to_track2"];    
                    $nbr_track2=($nbr_track2==0 && $limit_track2>0)?1:$nbr_track2; 
                    if ($nbr_track2 > 0)
                        $track2=true;     
              
                    $nbr_tech = $_GET["of_tech"] ;
                    $limit_tech = $_GET["to_tech"]; 
                    $nbr_tech=($nbr_tech==0 && $limit_tech>0)?1:$nbr_tech;  
                    if ($nbr_tech > 0)
                        $tech=true; 
                        
                    $nbr_track1-=1;  
                    $nbr_track2-=1;  
                    $nbr_tech-=1;  
                        
                $sql_d="SELECT 
                                DISTINCT at.Name, 
                                at.Vorname,
                                a.xAnmeldung, 
                                IF( (d.Typ = ".$cfgDisciplineType[$strDiscTypeTrack]." 
                                    || d.Typ = ".$cfgDisciplineType[$strDiscTypeTrackNoWind]."   
                                     ),1, IF( (d.Typ = ".$cfgDisciplineType[$strDiscTypeDistance]."
                                     || d.Typ = ".$cfgDisciplineType[$strDiscTypeRelay]."                                       
                                     ),2, 3 ) ) as discSort,
                                w.xDisziplin, 
                                at.xVerein,
                                t.Name,
                                d.Typ ,
                                t.xTeam
                        FROM 
                                anmeldung AS a
                                LEFT JOIN athlet AS at ON a.xAthlet = at.xAthlet
                                INNER JOIN verein AS v ON at.xVerein = v.xVerein
                                INNER JOIN START AS s ON a.xAnmeldung = s.xAnmeldung
                                INNER JOIN wettkampf AS w
                                USING ( xWettkampf )
                                INNER JOIN disziplin AS d
                                USING ( xDisziplin )
                                LEFT JOIN team AS t ON t.xTeam = a.xTeam
                        WHERE 
                                a.xMeeting = " . $_COOKIE['meeting_id'] . "                                   
                        GROUP BY a.xAnmeldung,  discSort 
                        ORDER BY  " . $argument . ", discSort ";       
                   
                $result_d=mysql_query($sql_d);    
                
                if(mysql_errno() > 0)        // DB error
                    {
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                }
                else if(mysql_num_rows($result_d) > 0)  // data found
                    {   
                    $t = 0;         // current team     
                    
                    $teamChangeTrack1=false; 
                    $teamChangeTrack2=false;
                    $teamChangeTech=false;
                    while ($row_d = mysql_fetch_row($result_d))
                    { 
                       if ($name_enrol == $row_d[0] . $row_d[1] . $row_d[2]) {
                            if ($d==1)
                                $dprev=$d; 
                       }
                       else
                          $dprev='';
                          
                      
                       if($t != $row_d[8]            // new team
                            && $teamgap > 0
                            && $t > 0
                            && isset($_GET['persvmteam']))
                            {   
                                 $teamChangeTrack1=true;
                                 $teamChangeTrack2=true; 
                                 $teamChangeTech=true;   
                       } 
                         
                         
                       if ( $row_d[3]== 1)        // discipline track under 400 m  
                                {      
                                 if ($teamChangeTrack1)
                                      $nbr_track1 += $teamgap;  
                                 else
                                    $nbr_track1++;
                                    
                                if(($limit_track1 > 0 && $nbr_track1 > $limit_track1) || $limit_track1 == 0){
                                    $nbr_track1 = 0;
                                    $limit_track1 = 0;
                                }   
                            $teamChangeTrack1=false;     
                       }
                       else if ( $row_d[3]== 2)        // discipline track over 400 m      
                                {      
                                if(($limit_track2 > 0 && $nbr_track2 > $limit_track2) || $limit_track2 == 0){
                                    $nbr_track2 = 0;
                                    $limit_track2 = 0;
                                    if ( ($name_enrol == $row_d[0] . $row_d[1] . $row_d[2]) )  
                                         if ($d==1 && $track1 ) {
                                            $noUpdate=true;   
                                         }      
                                }
                                else {
                                     if ( ($name_enrol != $row_d[0] . $row_d[1] . $row_d[2])   ) { 
                                          if ($teamChangeTrack2)
                                                $nbr_track2 += $teamgap; 
                                          else 
                                                $nbr_track2++;   
                                     }
                                     else  
                                        if ($d==1 && $track1 )  {
                                            $noUpdate=true;          // nbr already set for athlete in other discipline                                            
                                        }
                                        else {   
                                            if ($teamChangeTrack2)
                                                $nbr_track2 += $teamgap; 
                                          else 
                                                $nbr_track2++;    
                                        }
                                }     
                          $teamChangeTrack2=false; 
                       }
                       else {                        // discipline tech   
                                if(($limit_tech > 0 && $nbr_tech > $limit_tech) || $limit_tech == 0){
                                    $nbr_tech = 0;
                                    $limit_tech = 0;
                                     
                                    if ( ($name_enrol == $row_d[0] . $row_d[1] . $row_d[2]) )  {
                                         
                                         if ( ($d==1 && $track1) || ($d==2 && $track2) || ($dprev==1 && $track1)) {
                                            $noUpdate=true;   
                                         }  
                                    }           
                                }
                                 else {  
                                     if ( ($name_enrol != $row_d[0] . $row_d[1] . $row_d[2]) ) { 
                                           if ($teamChangeTech)
                                                $nbr_tech += $teamgap; 
                                          else 
                                                $nbr_tech++;   
                                     }                                     
                                     else {  
                                        if ($d==1 && $track1 || $d==2 && $track2  || $dprev==1 && $track1)  {
                                            $noUpdate=true;          // nbr already set for athlete in other discipline                                           
                                        }
                                        else {   
                                             if ($teamChangeTech)
                                                $nbr_tech += $teamgap; 
                                          else 
                                                $nbr_tech++;   
                                        }
                                     }
                                }     
                               $teamChangeTech=false;   
                       } 
                      
                        
                                        // update discipline track under 400 m 
                      if ( $row_d[3]== 1 )   
                            {   
                            mysql_query("
                                UPDATE anmeldung SET
                                    Startnummer='$nbr_track1'
                                    WHERE xAnmeldung = $row_d[2]
                                    ");  
                            if(mysql_errno() > 0) {
                                AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                            }  
                         }                                                     
                                        // update discipline track over 400 m 
                         elseif ( $row_d[3]== 2 )  
                            {                                        
                            
                             if (!$noUpdate) {
                                  mysql_query("
                                        UPDATE anmeldung SET
                                        Startnummer='$nbr_track2'
                                        WHERE xAnmeldung = $row_d[2]
                                        ");  
                                  if(mysql_errno() > 0) {
                                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                                  }
                             }
                             else   
                                 $noUpdate=false;  
                         }
                                    //  update discipline tecg 
                          elseif ( $row_d[3]== 3 ) 
                          {                                                                                                                                       // discipline tech  
                            if (!$noUpdate) {  
                                mysql_query("
                                    UPDATE anmeldung SET
                                        Startnummer='$nbr_tech'
                                        WHERE xAnmeldung = $row_d[2]
                                        "); 
                                if(mysql_errno() > 0) {
                                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                                }  
                             }
                             else   
                                 $noUpdate=false; 
                         }    
                 
                        if(mysql_errno() > 0) {
                            AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
                        } 
                           
                      
                        $t = $row_d[8];    // keep current team     
                        $d = $row_d[3];    // keep current discipline typ   
                       
                        $name_enrol= $row_d[0] . $row_d[1] . $row_d[2];     // keep name, prename, enrolement   
                        
                           
                 }   
                 mysql_free_result($result_d);
            }   
          }            
                                    // ET DB error
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
		            
           
            $result = mysql_query("
                SELECT
                    a.xAnmeldung
                    , a.xKategorie
                    , at.xVerein
                    , a.xTeam
                    , at.Name
                    , at.Vorname
                    , t.Name
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
                    if(empty($_GET['assign'])){ 
                        
                        if (($v != $row[2])         // new club
                            && ($clubgap > 0)           // gap between clubs
                            && ($v > 0)                 // not first row
                            && ($_GET['sort']=="club")) // gap after cat
                             
                    {  
                      if ($nbr>0) {  
                        $nbr = $nbr + $clubgap;    // calculate next number
                      }
                    }
                    else if (($k != $row[1])        // new category
                        && ($catgap > 0)                // gap between categories
                        && ($k > 0)                        // not first row
                        && ($_GET['sort']=="cat"))    // gap after cat
                          
                    { 
                      if ($nbr>0) {  
                        $nbr = $nbr + $catgap;                // calculate next number
                      }
                    }
                    else {
                        $nbr++;   
                    }
                 }
                 else   
                    {    
				 // set per category from, to    
					if ($k != $row[1]){			// new category
						$nbr = $_GET["of_$row[1]"];
                        $limit = $_GET["to_$row[1]"];  
						//$nbr==0?1:$nbr;
                        $nbr=($nbr==0 && $limit>0)?1:$nbr;
                         
						
					}elseif($t != $row[3]			// new team
						&& $teamgap > 0
						&& $t > 0
						&& isset($_GET['persvmteam']))
					{
						if ($nbr>0) {
                            $nbr += $teamgap;      
                        }
					}else{
						$nbr++;
						if(($limit > 0 && $nbr > $limit) || $limit == 0){
							$nbr = 0;
							$limit = 0;
						}
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
    
     h = document.getElementById("perdiscipline");   
     if (h.checked){
          h.checked=false;  
     }  
}

function select_CheckDisc(){ 
    
     e = document.getElementById("cat");  
     if (e.checked){ 
           h = document.getElementById("name");        
           h.checked=true; 
      } 
} 
    

</script>

<form action='meeting_entries_startnumbers.php' method='get'>
<input type='hidden' name='arg' value='assign'>
<table class='dialog'>
<tr>
	<th class='dialog'><?php echo $strSortBy; ?></th>
	<th class='dialog' colspan='5'><?php echo $strRules; ?></th>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' id='name' value='name' checked>
			<?php echo $strName; ?></input>	</td>
	<td class='dialog'>
		<?php echo $strBeginningWith; ?>	</td>
	<td>
		<input class='nbr' type='text' name='start' maxlength='5' value='<?php echo $cfgNbrStartWith; ?>'>	</td>
    <td>&nbsp;</td>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' id='cat' value='cat' onchange="select_CheckCat()">
			<?php echo $strCategory; ?></input>	</td>
	<td colspan='3'>
		<?php echo $strGapBetween; ?>	</td>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' id='club' value='club'>
			<?php echo $strClub; ?></input>	</td>
	<td class='dialog'>
		<?php echo $strCategory; ?>	</td>
	<td class='dialog'>
		<input class='nbr' type='text' name='catgap' maxlength='4' value='<?php echo $cfgNbrCategoryGap; ?>'>	</td>
    <td class='dialog'>&nbsp;</td>
</tr> 
<tr>
	<td class='dialog'>	</td>
	<td class='dialog'>
		<?php echo $strClub; ?>	</td>
	<td class='dialog'>
		<input class='nbr' type='text' name='clubgap' maxlength='4' value='<?php echo $cfgNbrClubGap; ?>'>	</td>
    <td class='dialog'>&nbsp;</td>
</tr> 

<tr>
	<td class='dialog' colspan="6"> 
     <hr>    
         <input type="radio" name="assign" id="percategory" value="percategory" onchange="select_CheckCat()"
            > </input>
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
          $sql="SELECT 
                    count(*)
                FROM 
                    anmeldung as a  
                WHERE 
                    a.xKategorie= " .$row[0]. "
                    AND a.xMeeting = ".$_COOKIE['meeting_id'];
               
          $res_count=mysql_query($sql);
          if(mysql_errno() > 0){
            AA_printErrorMsg(mysql_errno().": ".mysql_error());
          }else{
                $row_count = mysql_fetch_array($res_count);
               $max_startnr=$row_count[0];
          }
		?>
<tr>
	<td class='dialog'><?php echo $row[1] ?></td>
	<td class='forms'>
		<?php echo $strOf ?>
		<input type="text" size="3" value="0" name="of_<?php echo $row[0] ?>" onchange="select_PerCategory()">	</td>
	<td class='forms_right'>
		<?php echo $strTo ?>
		<input type="text" size="3" value="0" name="to_<?php echo $row[0] ?>" onchange="select_PerCategory()">	</td>
    <td><?php echo $strMax; ?>
    </td>
    <td class='forms_right_grey'><?php echo $max_startnr; ?></td>
    <td> &nbsp;</td>  
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
	<td class='forms_right'>
		<?php echo $strGap.":" ?>
		<input type="text" size="3" value="10" name="teamgap" onchange="select_PerSvmTeam('1')">	</td>
    <td class='forms'>&nbsp;</td>
    <td />        
</tr>

<tr>
	<td class='dialog' colspan="6">
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
           $sql=" SELECT 
                        DISTINCT a.xAnmeldung,  
                        count(w.xKategorie)
                  FROM 
                        anmeldung AS a 
                        INNER JOIN start AS s ON s.xAnmeldung = a.xAnmeldung 
                        INNER JOIN wettkampf AS w USING (xWettkampf) 
                        INNER JOIN kategorie AS k ON k.xKategorie = w.xKategorie 
                  WHERE 
                        a.xMeeting = ".$_COOKIE['meeting_id'] ."
                        AND w.xKategorie = " .$row[0] ."
                   GROUP BY w.xKategorie  
                   ";   
               
          $res_count=mysql_query($sql);
          if(mysql_errno() > 0){
            AA_printErrorMsg(mysql_errno().": ".mysql_error());
          }else{
                $row_count = mysql_fetch_array($res_count);
                $max_startnr=$row_count[1];
          }  
        
		?>
<tr>
	<td class='dialog'><?php echo $row[1] ?></td>
	<td class='forms'>
		<?php echo $strOf ?>
		<input type="text" size="3" value="0" name="of2_<?php echo $row[0] ?>" onchange="select_PerContestCat()">	</td>
	<td class='forms_right'>
		<?php echo $strTo ?>
		<input type="text" size="3" value="0" name="to2_<?php echo $row[0] ?>" onchange="select_PerContestCat()">	</td>
        <td><?php echo $strMax; ?>  
    </td>
    <td class='forms_right_grey'><?php echo $max_startnr; ?></td>
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
	<td class='forms_right'>
		<?php echo $strGap.":" ?>
		<input type="text" size="3" value="10" name="teamgap" onchange="select_PerSvmTeam('2')">	</td>
    <td class='forms'>&nbsp;</td>
    <td />   
</tr> 

<tr>
    <td class='dialog' colspan="6">
        <hr>  
        <input type="radio" name="assign" id="perdiscipline" value="perdiscipline" onchange="select_CheckDisc()"> </input> 
        <?php echo $strPerDiscipline; ?>
    </th></tr>
<?php

// check track disziplines in this meeting under 400 m
$selection_disciplines="(" . $cfgDisciplineType[$strDiscTypeTrack] . ","  
                           . $cfgDisciplineType[$strDiscTypeTrackNoWind]  . ")";   
                             
        
$res = mysql_query("SELECT           
                        a.xAnmeldung
                    FROM 
                        anmeldung AS a  
                        INNER JOIN START AS s ON a.xAnmeldung = s.xAnmeldung 
                        INNER JOIN wettkampf AS w USING ( xWettkampf ) 
                        INNER JOIN disziplin AS d USING ( xDisziplin )  
                    WHERE 
                        a.xMeeting = ".$_COOKIE['meeting_id']."  
                        AND d.Typ IN" . $selection_disciplines ." 
                    GROUP BY a.xAnmeldung
                    ");     
       
if(mysql_errno() > 0){
    AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
    if (mysql_num_rows($res)>0){ 
         $max_startnr=mysql_num_rows($res);  
        ?>
<tr>
    <td class='dialog'><?php echo $strTrack1; ?></td>
    <td class='forms'>
        <?php echo $strOf ?>
        <input type="text" size="3" value="0" name="of_track1" onchange="select_PerDiscipline()">    </td>
    <td class='forms_right'>
        <?php echo $strTo ?>
        <input type="text" size="3" value="0" name="to_track1" onchange="select_PerDiscipline()">    </td>
     <td><?php echo $strMax; ?>  
    </td> 
    <td class='forms_right_grey'><?php echo $max_startnr; ?></td>
    
</tr>
<?php
    }
}



// check track disziplines in this meeting  over 400m
$selection_disciplines="(" . $cfgDisciplineType[$strDiscTypeDistance]  . ","   
                            . $cfgDisciplineType[$strDiscTypeRelay] . ")";   
        
$res = mysql_query("SELECT           
                        a.xAnmeldung
                    FROM 
                        anmeldung AS a  
                        INNER JOIN START AS s ON a.xAnmeldung = s.xAnmeldung 
                        INNER JOIN wettkampf AS w USING ( xWettkampf ) 
                        INNER JOIN disziplin AS d USING ( xDisziplin )  
                    WHERE 
                        a.xMeeting = ".$_COOKIE['meeting_id']."  
                        AND d.Typ IN" . $selection_disciplines ." 
                    GROUP BY a.xAnmeldung
                    ");   
                            
              
if(mysql_errno() > 0){
    AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
    if (mysql_num_rows($res)>0){ 
        $max_startnr=mysql_num_rows($res);
        ?>
<tr>
    <td class='dialog'><?php echo $strTrack2; ?></td>
    <td class='forms'>
        <?php echo $strOf ?>
        <input type="text" size="3" value="0" name="of_track2" onchange="select_PerDiscipline()">    </td>
    <td class='forms_right'>
        <?php echo $strTo ?>
        <input type="text" size="3" value="0" name="to_track2" onchange="select_PerDiscipline()">    </td>
    <td><?php echo $strMax; ?>  
    </td> 
    <td class='forms_right_grey'><?php echo $max_startnr; ?></td>
</tr>
<?php
    }
}
// check tech disziplines in this meeting
$selection_disciplines="(" . $cfgDisciplineType[$strDiscTypeJump] . ","  
                           . $cfgDisciplineType[$strDiscTypeJumpNoWind] . ","  
                           . $cfgDisciplineType[$strDiscTypeHigh] . ","  
                           . $cfgDisciplineType[$strDiscTypeThrow] . ","   
                           . $cfgDisciplineType[$strDiscCombined] . ")";     
            
$res = mysql_query("SELECT           
                        a.xAnmeldung
                    FROM 
                        anmeldung AS a  
                        INNER JOIN START AS s ON a.xAnmeldung = s.xAnmeldung 
                        INNER JOIN wettkampf AS w USING ( xWettkampf ) 
                        INNER JOIN disziplin AS d USING ( xDisziplin )  
                    WHERE 
                        a.xMeeting = ".$_COOKIE['meeting_id']."  
                        AND d.Typ IN" . $selection_disciplines ." 
                    GROUP BY a.xAnmeldung
                    ");   
            
                       
if(mysql_errno() > 0){
    AA_printErrorMsg(mysql_errno().": ".mysql_error());
}else{
    if (mysql_num_rows($res)>0){ 
         $max_startnr=mysql_num_rows($res);  
        ?>    
    
   <tr>
    <td class='dialog'><?php echo $strTech; ?></td>
    <td class='forms'>
        <?php echo $strOf ?>
        <input type="text" size="3" value="0" name="of_tech" onchange="select_PerDiscipline()">    </td>
    <td class='forms_right'>
        <?php echo $strTo ?>
        <input type="text" size="3" value="0" name="to_tech" onchange="select_PerDiscipline()">    </td>
    <td><?php echo $strMax; ?>  
    </td> 
    <td class='forms_right_grey'><?php echo $max_startnr; ?></td>
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
    <td class='forms_right'>
        <?php echo $strGap.":" ?>
        <input type="text" size="3" value="10" name="teamgap" onchange="select_PerSvmTeam('3')">    </td>
    <td class='forms'>&nbsp;</td>
    <td />   
</tr> 
 
<tr>
	<td class='dialog' colspan = '6'>
		<hr>
		<input type='radio' name='sort' value='del'>
			<?php echo $strDeleteStartnumbers; ?></input>	</td>
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
