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

$clubGap = 0;
if ($_GET['clubGap']){
    $clubGap = $_GET['clubGap'];            // nbr gap between each club
}

$max_startnr = 0;
$max_startnr_track1 = 0;
$max_startnr_track2 = 0;
$max_startnr_tech = 0;
$nbr1 = 0;
$nbr2 = 0; 
$nbr3 = 0; 
$limit1 = 0;
$limit2 = 0;
$limit3 = 0;

$allNr = false;

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
		  $argument2="at.Name, at.Vorname"; 	  	
		} else if ($_GET['sort']=="club") {
		  $argument2="v.Sortierwert, at.Name, at.Vorname";   
		} else {
		  $argument2="at.Name, at.Vorname";
		}                                         
		
		
		// assign per contest cat      
			$argument = "k.Anzeige ";    
			
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
          
            $sql="SELECT 
                    DISTINCT (a.xAnmeldung) ,
                    w.xKategorie , 
                    at.xVerein , 
                    a.xTeam, 
                    at.Name, 
                    at.Vorname,
                    t.Name, 
                    IF( (d.Typ = ".$cfgDisciplineType[$strDiscTypeTrack]." 
                                    || d.Typ = ".$cfgDisciplineType[$strDiscTypeTrackNoWind]."   
                                     ),2, IF( (d.Typ = ".$cfgDisciplineType[$strDiscTypeDistance]."
                                     || d.Typ = ".$cfgDisciplineType[$strDiscTypeRelay]."                                       
                                     ),3, 1 ) ) as discSort
                FROM 
                    anmeldung AS a
                    LEFT JOIN athlet AS at ON a.xAthlet = at.xAthlet        
                    INNER JOIN verein AS v ON at.xVerein = v.xVerein 
                    INNER JOIN start AS s ON s.xAnmeldung = a.xAnmeldung 
                    INNER JOIN wettkampf AS w USING (xWettkampf)
                    INNER JOIN disziplin AS d On (w.xdisziplin = d.xDisziplin)
                    INNER JOIN kategorie AS k ON k.xKategorie = w.xKategorie
                    LEFT JOIN team AS t ON t.xTeam = a.xTeam 
                WHERE 
                    a.xMeeting = " . $_COOKIE['meeting_id'] . " 
                    ORDER BY      
                         $argument, discSort, $argument2";    
            
            $result = mysql_query($sql); 
          
			if(mysql_errno() > 0)		// DB error
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else if(mysql_num_rows($result) > 0)  // data found
			{ 
            $noCat = true;   
           
              // check if choosen per name/club or per category
              while ($row = mysql_fetch_row($result))
              { 
                if (($_GET["of_$row[1]"] != 0)   ||
                     ($_GET["of_tech_$row[1]"] != 0) ||
                      ($_GET["of_track1_$row[1]"] != 0) ||
                      ($_GET["of_track2_$row[1]"] != 0)) 
                {
                      $noCat = false;   
                }  
              } 
               
              if ($noCat){       // set per name or per club  
                  
                   $sql="SELECT 
                            DISTINCT (a.xAnmeldung) ,
                            w.xKategorie , 
                            at.xVerein , 
                            a.xTeam, 
                            at.Name, 
                            at.Vorname,
                            t.Name, 
                            IF( (d.Typ = ".$cfgDisciplineType[$strDiscTypeTrack]." 
                                    || d.Typ = ".$cfgDisciplineType[$strDiscTypeTrackNoWind]."   
                                     ),2, IF( (d.Typ = ".$cfgDisciplineType[$strDiscTypeDistance]."
                                     || d.Typ = ".$cfgDisciplineType[$strDiscTypeRelay]."                                       
                                     ),3, 1 ) ) as discSort
                         FROM 
                            anmeldung AS a
                            LEFT JOIN athlet AS at ON a.xAthlet = at.xAthlet        
                            INNER JOIN verein AS v ON at.xVerein = v.xVerein 
                            INNER JOIN start AS s ON s.xAnmeldung = a.xAnmeldung 
                            INNER JOIN wettkampf AS w USING (xWettkampf)
                            INNER JOIN disziplin AS d On (w.xdisziplin = d.xDisziplin)
                            INNER JOIN kategorie AS k ON k.xKategorie = w.xKategorie
                            LEFT JOIN team AS t ON t.xTeam = a.xTeam 
                         WHERE 
                            a.xMeeting = " . $_COOKIE['meeting_id'] . " 
                         ORDER BY      
                            $argument2 , discSort";    
              }
              
              $result = mysql_query($sql); 
              if(mysql_errno() > 0)        // DB error
                    {
                    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
              }         
              
			  $k = 0;	// initialize current category
			  $v = 0;	// initialize current club   
              $first = true;
              
              $arr_enrolment = array();   
			  
			  // Assign startnumbers
			  while ($row = mysql_fetch_row($result))
			  { 
              
				// set per category from, to  and per disciplines (all or tech and/or track under 400m and/or track over 400m)  
                if (($v != $row[2] )         // new club
                            && ($clubgap > 0)           // gap between clubs
                            && ($v > 0)                 // not first row
                            && ($_GET['sort']=="club")) // gap after cat 
                    { 
                     
                     if ($noCat){           // set per name or per club 
                            $nbr = $nbr + $clubgap - 1;    // calculate next number  all disciplines     
                     }
                     else {
                            if (!empty($_GET["of_$row[1]"])){  
                                $nbr = $nbr + $clubgap - 1;    // calculate next number  all disciplines
                            }
                            else { 
                                if (!empty($_GET["of_tech_$row[1]"])){ 
                                    $nbr1 = $nbr1 + $clubgap - 1;    // calculate next number tech  
                                }
                                if (!empty($_GET["of_track1_$row[1]"])){    
                                    $nbr2 = $nbr2 + $clubgap - 1;    // calculate next number track under 400m
                                }
                                if (!empty($_GET["of_track2_$row[1]"])){
                                    $nbr3 = $nbr3 + $clubgap -1 ;    // calculate next number track over 400m  
                                }  
                            }
                     }
                    }
            
                if ($noCat) {                  // set per name or per club 
                    if ($first){                         
                        if (!empty($_GET["name_of"]) && $_GET["sort"] == 'name'){
                            $nbr = $_GET["name_of"];                                 // set nbr of per name
                        }
                        elseif (!empty($_GET["club_of"]) && $_GET["sort"] == 'club'){    
                                 $nbr = $_GET["club_of"];                             // set nbr of per club
                        }
                        else {
                           $nbr = 0;   
                        } 
                        
                        $nbr1 = 0; 
                        $nbr2 = 0; 
                        $nbr3 = 0; 
                        $limit = 0;
                        $limit1 = 0;
                        $limit2 = 0;
                        $limit3 = 0;
                        $all = false;
                        
                       
                        $limit = 9999999;
                        $all = true;                          
                        $allNr = true;
                                 
                        $nbr=($nbr==0 && $limit>0)?1:$nbr;   
                        $nbr1=($nbr1==0 && $limit1>0)?1:$nbr1;
                        $nbr2=($nbr2==0 && $limit2>0)?1:$nbr2; 
                        $nbr3=($nbr3==0 && $limit3>0)?1:$nbr3; 
                        
                        $first = false;
                    }
                    else {
                         if(($limit > 0 && $nbr > $limit) || $limit == 0){
                            $nbr = 0;
                            $limit = 0;
                         } 
                    
                        if(($limit1 > 0 && $nbr1 > $limit1) || $limit1 == 0){
                            $nbr1 = 0;
                            $limit1 = 0;
                        } 
                        if(($limit2 > 0 && $nbr2 > $limit2) || $limit2 == 0){
                            $nbr2 = 0;
                            $limit2 = 0;
                        }
                        if(($limit3 > 0 && $nbr3 > $limit3) || $limit3 == 0){
                            $nbr3 = 0;
                            $limit3 = 0;
                        } 
                    } 
                }
                else {
				    if ($k != $row[1]  ){			// new category      
                        $nbr = 0;  
                        $nbr1 = 0; 
                        $nbr2 = 0; 
                        $nbr3 = 0; 
                        $limit = 0;
                        $limit1 = 0;
                        $limit2 = 0;
                        $limit3 = 0;
                        $all = false;
                    
                        if (!empty($_GET["of_$row[1]"])){
                            $nbr = $_GET["of_$row[1]"];
                            $limit = $_GET["to_$row[1]"]; 
                            $all = true;                          
                        } 
                        else {
                           if (!empty($_GET["of_tech_$row[1]"])){
                                $nbr1 = $_GET["of_tech_$row[1]"];
                                $limit1 = $_GET["to_tech_$row[1]"];  
                           } 
                           if (!empty($_GET["of_track1_$row[1]"])){
                                  $nbr2 = $_GET["of_track1_$row[1]"];
                                  $limit2 = $_GET["to_track1_$row[1]"]; 
                           }
                           if (!empty($_GET["of_track2_$row[1]"])){
                                  $nbr3 = $_GET["of_track2_$row[1]"];
                                  $limit3 = $_GET["to_track2_$row[1]"]; 
                           } 
                        }  
                        $nbr=($nbr==0 && $limit>0)?1:$nbr;   
                        $nbr1=($nbr1==0 && $limit1>0)?1:$nbr1;
                        $nbr2=($nbr2==0 && $limit2>0)?1:$nbr2; 
                        $nbr3=($nbr3==0 && $limit3>0)?1:$nbr3; 
                
				    }else{ 
                        if(($limit > 0 && $nbr > $limit) || $limit == 0){
                            $nbr = 0;
                            $limit = 0;
                        }   
					    if(($limit1 > 0 && $nbr1 > $limit1) || $limit1 == 0){
						    $nbr1 = 0;
						    $limit1 = 0;
					    } 
                        if(($limit2 > 0 && $nbr2 > $limit2) || $limit2 == 0){
                            $nbr2 = 0;
                            $limit2 = 0;
                        }
                        if(($limit3 > 0 && $nbr3 > $limit3) || $limit3 == 0){
                            $nbr3 = 0;
                            $limit3 = 0;
                        }
				    }   
                }   
				
                switch ($row[7]){
                       case 1:  if ($all){
                                     if (!isset($arr_enrolment[$row[0]])){ 
                                            mysql_query("
                                                UPDATE anmeldung SET
                                                       Startnummer='$nbr'
                                                WHERE xAnmeldung = $row[0]
                                                ");
                                   
                                            if ($nbr > 0){
                                                $arr_enrolment [$row[0]] = 'y';   
                                                $nbr++;
                                            }                          
                                     }  
                                }
                                else {  
                                      if (!isset($arr_enrolment[$row[0]])){ 
                                            mysql_query("
                                                UPDATE anmeldung SET
                                                       Startnummer='$nbr1'
                                                WHERE xAnmeldung = $row[0]
                                                ");
                                    
                                            if ($nbr1 > 0){ 
                                                $arr_enrolment [$row[0]] = 'y';   
                                                $nbr1++;
                                            }
                                      }
                                }
                                break;   
                       case 2:  if ($all){
                                     if (!isset($arr_enrolment[$row[0]])){  
                                            mysql_query("
                                                UPDATE anmeldung SET
                                                       Startnummer='$nbr'
                                                WHERE xAnmeldung = $row[0]
                                                ");
                                    
                                            if ($nbr > 0){ 
                                                $arr_enrolment [$row[0]] = 'y';   
                                                $nbr++; 
                                            }   
                                     }
                           
                                }
                                else {   
                                      if (!isset($arr_enrolment[$row[0]])){   
                                            mysql_query("
                                                UPDATE anmeldung SET
                                                       Startnummer='$nbr2'
                                                WHERE xAnmeldung = $row[0]
                                            ");
                                    
                                            if ($nbr2 > 0){ 
                                                $arr_enrolment [$row[0]] = 'y';   
                                                $nbr2++; 
                                            }   
                                         }
                                }
                                break;                     
                       case 3:  if ($all){
                                     if (!isset($arr_enrolment[$row[0]])){   
                                            mysql_query("
                                                UPDATE anmeldung SET
                                                       Startnummer='$nbr'
                                                WHERE xAnmeldung = $row[0]
                                            ");
                                    
                                            if ($nbr > 0){ 
                                                $arr_enrolment [$row[0]] = 'y';   
                                                $nbr++;
                                            }    
                                     } 
                                }
                                else {   
                                      if (!isset($arr_enrolment[$row[0]])){ 
                                            mysql_query("
                                                UPDATE anmeldung SET
                                                       Startnummer='$nbr3'
                                                WHERE xAnmeldung = $row[0]
                                            ");
                                   
                                            if ($nbr3 > 0){ 
                                                $arr_enrolment [$row[0]] = 'y';   
                                                $nbr3++;    
                                            }
                                      }
                                }
                                break;                     
                       default: break;  
                }
               
				if(mysql_errno() > 0) {
					    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				
                if (!$noCat) {
				    $k = $row[1];	// keep current category
                }
				$v = $row[2];	// keep current club    
				                                            
			  }
			  mysql_free_result($result);
			}						// ET DB error
			mysql_query("UNLOCK TABLES");
			
	
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

<script type="text/javascript">     

function check_rounds(){
	   
	//if(true == <?php echo $heats_done ?>){
		
		check = confirm("<?php echo $strStartNrConfirm ?>");
		return check;
	//}
    
}
         
function check_of(){
    
    // check of values in name_of and club_of
   
   if (document.getElementById("name").checked){       
       document.getElementById("club_of").value = '';
   }
   else {           
        document.getElementById("name_of").value = '';  
   } 
}   	

  

</script>

<form action='meeting_entries_startnumbers.php' method='get' id='startnr'>
<input type='hidden' name='arg' value='assign'>
<table class='dialog'>
<tr>
	<th class='dialog'><?php echo $strSortBy; ?></th>
    <th class='dialog'><?php echo $strOf; ?></th>     
	<th class='dialog' colspan='12'><?php echo $strRules; ?></th>
</tr>
<tr>
	<td class='dialog'>
		<input type='radio' name='sort' id='name' onChange='check_of()' value='name' checked ">
    </td>         
     </td>
</tr>
<tr>
	<td><?php echo $strName; ?>
   	</td>	
	<td class='forms'> 
		<input type="text" size="3" value="0" name="name_of" id="name_of"> 
    </td>  
</tr>

<tr>
	<td class='dialog'>
		<input type='radio' name='sort' id='club' value='club' onChange='check_of()'>
    </td>
</tr>
<tr>
    <td>
         <?php echo $strClub; ?></input>   
    </td>
    <td class='forms'>
        <input type="text" size="3" value="0" name="club_of" >  
     </td> 
     <td class='dialog' colspan='5'>
        <?php echo $strGapBetween . " " . $strClub; ?>    </td>
    <td class='dialog'>
        <input class='nbr' type='text' name='clubgap' maxlength='4' value='<?php echo $clubGap; ?>'>    </td>
    <td class='dialog'>&nbsp;</td> 
</tr>






<?php

$i = 0;
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
	while($row = mysql_fetch_array($res)){        
           $max_startnr = 0;
           $max_startnr_track1 = 0;
           $max_startnr_track2 = 0;
           $max_startnr_tech = 0;
           
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
          
          // check track disziplines in this meeting under 400 m
          $selection_disciplines="(" . $cfgDisciplineType[$strDiscTypeTrack] . ","  
                           . $cfgDisciplineType[$strDiscTypeTrackNoWind]  . ")";   
                             
        
          $res_track1 = mysql_query("SELECT           
                        a.xAnmeldung
                    FROM 
                        anmeldung AS a  
                        INNER JOIN START AS s ON a.xAnmeldung = s.xAnmeldung 
                        INNER JOIN wettkampf AS w USING ( xWettkampf ) 
                        INNER JOIN disziplin AS d USING ( xDisziplin )  
                    WHERE 
                        a.xMeeting = ".$_COOKIE['meeting_id']." 
                        AND w.xKategorie = " .$row[0] ." 
                        AND d.Typ IN" . $selection_disciplines ." 
                    GROUP BY a.xAnmeldung
                    ");     
      
          if(mysql_errno() > 0){
                AA_printErrorMsg(mysql_errno().": ".mysql_error());
          }else{
                if (mysql_num_rows($res_track1)>0){ 
                    $max_startnr_track1=mysql_num_rows($res_track1); 
                }
          } 
          
          
         // check track disziplines in this meeting  over 400m
         $selection_disciplines="(" . $cfgDisciplineType[$strDiscTypeDistance]  . ","   
                            . $cfgDisciplineType[$strDiscTypeRelay] . ")";   
        
         $res_track2 = mysql_query("SELECT           
                        a.xAnmeldung
                    FROM 
                        anmeldung AS a  
                        INNER JOIN START AS s ON a.xAnmeldung = s.xAnmeldung 
                        INNER JOIN wettkampf AS w USING ( xWettkampf ) 
                        INNER JOIN disziplin AS d USING ( xDisziplin )  
                    WHERE 
                        a.xMeeting = ".$_COOKIE['meeting_id']."  
                        AND w.xKategorie = " .$row[0] ." 
                        AND d.Typ IN" . $selection_disciplines ." 
                    GROUP BY a.xAnmeldung
                    ");   
                            
              
         if(mysql_errno() > 0){
                AA_printErrorMsg(mysql_errno().": ".mysql_error());
         }else{
                if (mysql_num_rows($res_track2)>0){ 
                    $max_startnr_track2=mysql_num_rows($res_track2);
                }
         } 
          
        // check tech disziplines in this meeting
        $selection_disciplines="(" . $cfgDisciplineType[$strDiscTypeJump] . ","  
                           . $cfgDisciplineType[$strDiscTypeJumpNoWind] . ","  
                           . $cfgDisciplineType[$strDiscTypeHigh] . ","  
                           . $cfgDisciplineType[$strDiscTypeThrow] . ","   
                           . $cfgDisciplineType[$strDiscCombined] . ")";     
            
        $res_tech = mysql_query("SELECT           
                        a.xAnmeldung
                    FROM 
                        anmeldung AS a  
                        INNER JOIN START AS s ON a.xAnmeldung = s.xAnmeldung 
                        INNER JOIN wettkampf AS w USING ( xWettkampf ) 
                        INNER JOIN disziplin AS d USING ( xDisziplin )  
                    WHERE 
                        a.xMeeting = ".$_COOKIE['meeting_id']."  
                        AND w.xKategorie = " .$row[0] ." 
                        AND d.Typ IN" . $selection_disciplines ." 
                    GROUP BY a.xAnmeldung
                    ");   
            
                       
        if(mysql_errno() > 0){
            AA_printErrorMsg(mysql_errno().": ".mysql_error());
        }else{
                if (mysql_num_rows($res_tech)>0){ 
                    $max_startnr_tech=mysql_num_rows($res_tech); 
                }
        }   
          
        if ($i == 0) {
		?>
        <tr>
        <th class='dialog' />
        <th class='dialog' colspan='3'>Alle</th>
        <th class='dialog' colspan='3'><?php echo $strTrack1; ?></th>
        <th class='dialog' colspan='3'><?php echo $strTrack2; ?></th>
        <th class='dialog' colspan='3'><?php echo $strTech; ?></th>
        </tr>
        
        <tr>
        <th class='dialog' />
        <th class='dialog' > <?php echo $strOf ?></th>
        <th class='dialog' ><?php echo $strTo ?></th>
        <th class='dialog' ><?php echo $strMax; ?>  </th>
          <th class='dialog' > <?php echo $strOf ?></th>
        <th class='dialog' ><?php echo $strTo ?></th>
         <th class='dialog' ><?php echo $strMax; ?>  </th>
          <th class='dialog' > <?php echo $strOf ?></th>
        <th class='dialog' ><?php echo $strTo ?></th>
         <th class='dialog' ><?php echo $strMax; ?>  </th>
         <th class='dialog' > <?php echo $strOf ?></th>
        <th class='dialog' ><?php echo $strTo ?></th>
         <th class='dialog' ><?php echo $strMax; ?>  </th>
        </tr>
        
        <?php 
        }
        ?>
<tr>
	<td class='dialog'><?php echo $row[1] ?></td>
	<td class='forms'>
		
		<input type="text" size="3" value="0" name="of_<?php echo $row[0] ?>" >	</td>
	<td class='forms_right'>
		
		<input type="text" size="3" value="0" name="to_<?php echo $row[0] ?>" >	</td>
        
        </td>  
    <td class='forms_right_grey'><?php echo $max_startnr; ?></td>
    
    
    
    <td class='forms'>
        
        <input type="text" size="3" value="0" name="of_track1_<?php echo $row[0] ?>" >    </td>
    <td class='forms_right'>
       
        <input type="text" size="3" value="0" name="to_track1_<?php echo $row[0] ?>" >    </td>
    
    
    <td class='forms_right_grey'><?php echo $max_startnr_track1; ?></td>
    
    
     <td class='forms'>
        
        <input type="text" size="3" value="0" name="of_track2_<?php echo $row[0] ?>" >    </td>
    <td class='forms_right'>
        
        <input type="text" size="3" value="0" name="to_track2_<?php echo $row[0] ?>" >    </td>
    
    <td class='forms_right_grey'><?php echo $max_startnr_track2; ?></td>
    
    
    <td class='forms'>
       
        <input type="text" size="3" value="0" name="of_tech_<?php echo $row[0] ?>" >    </td>
    <td class='forms_right'>
       
        <input type="text" size="3" value="0" name="to_tech_<?php echo $row[0] ?>" >    </td>
   
    
    <td class='forms_right_grey'><?php echo $max_startnr_tech; ?></td>
</tr>
		<?php
        $i++;
	}
}

?>


 
<tr>
	<td class='dialog' colspan = '13'>
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
