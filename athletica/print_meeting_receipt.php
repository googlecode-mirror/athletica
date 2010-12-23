<?php

/**********
 *
 *	print_meeting_receipt.php
 *	-------------------------
 *	
 */     
require('./lib/cl_gui_entrypage.lib.php');
require('./lib/cl_print_entrypage.lib.php');
require('./lib/cl_export_entrypage.lib.php');
require('./lib/common.lib.php');


if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
	}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

//
// Content
// -------

  

// basic sort argument sort by name    
	
$argument = "v.Sortierwert, at.Name, at.Vorname, d2.Name, d.Anzeige";  

// selection arguments
$club_clause = "";
if($_GET['club'] > 0) {        // club selected
    $club_clause = " AND v.xVerein = " . $_GET['club'];
} 

$athlete_clause="";   
if($_GET['athleteSearch'] > 0 ) {        // athlete selected   
    $athlete_clause = " AND a.xAnmeldung = " . $_GET['athleteSearch'];    
} 

if (!empty($_GET['item'])){               // athlete selected  from meeting_entry.php
   $athlete_clause = " AND a.xAnmeldung = " . $_GET['item']; 
}

$print = false;
if($_GET['formaction'] == 'print') {		// page for printing 
	$print = true;
} 
                
  
// start a new HTML page for printing
                                            
	if($print == true) { 
		$doc = new PRINT_ReceiptEntryPage($_COOKIE['meeting']);  
    } 
       
                 
    $reduction=AA_getReduction(); 
  
    $date=date("d.m.Y");      
 
    $result = mysql_query("
	SELECT DISTINCT a.xAnmeldung
		, a.Startnummer
		, at.Name
		, at.Vorname
		, at.Jahrgang
		, k.Kurzname
		, k.Name
		, v.Name
		, t.Name
		, d.Kurzname
		, d.Name
		, d.Typ
		, s.Bestleistung
		, if(at.xRegion = 0, at.Land, re.Anzeige)
		, ck.Kurzname
		, ck.Name
		, s.Bezahlt
		, w.Info
		, d2.Kurzname
		, d2.Name
		, v.Sortierwert
		, k.Anzeige
        , w.Startgeld  
        , m.Name   
        , m.Ort 
        , m.DatumVon  
        , m.DatumBis
        , sd.Name    
        , m.Organisator    
	FROM
		anmeldung AS a
		, athlet AS at
		, disziplin_" . $_COOKIE['language'] . " AS d
		, kategorie AS k
		, kategorie AS ck
		, start AS s
		, verein AS v
		, wettkampf AS w
    LEFT JOIN runde AS r 
        ON (s.xWettkampf = r.xWettkampf) 
	LEFT JOIN team AS t
		ON a.xTeam = t.xTeam
	LEFT JOIN region as re 
		ON at.xRegion = re.xRegion
	LEFT JOIN disziplin_" . $_COOKIE['language'] ." AS d2 
		ON (w.Typ = 1 AND w.Mehrkampfcode = d2.Code)
    LEFT JOIN meeting AS m ON (a.xMeeting = m.xMeeting)  
    LEFT JOIN stadion AS sd ON (m.xStadion = sd.xStadion)  
	WHERE a.xMeeting = " . $_COOKIE['meeting_id'] . "
	AND a.xAthlet = at.xAthlet
	AND at.xVerein = v.xVerein
	AND a.xKategorie = k.xKategorie
	AND s.xAnmeldung = a.xAnmeldung
	AND w.xWettkampf = s.xWettkampf
	AND ck.xKategorie = w.xKategorie
	AND d.xDisziplin = w.xDisziplin  
	$club_clause  
    $date_clause
    $athlete_clause
	$limitNrSQL
	ORDER BY
		$argument
    
");     
 
if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else if(mysql_num_rows($result) > 0)  // data found
{                                     
	$a = 0;		// current athlete enrolement  
	$l = 0;		// line counter  
    $first=true;
    $tf = 0;    // total fee    
	
	// full list, sorted by name 
	while ($row = mysql_fetch_row($result))
	{          
		// print previous athlete, if any
		if($a != $row[0] && $a > 0)
		{   
            if ($club_clause!='' && $athlete_clause=='') {
                  if ($first) {  
                       $doc->printHeader($mname,$mDateFrom,$mDateTo,$stadion,$organisator);
                       $doc->printLineBreak(1); 
                       $doc->printLineClub($club); 
                       $l+=4; 
                       $first=false;
                       printf("<tr><td colspan='4'>");    
                  } 
                                 
                  $doc->printLine4($first, $name, $year, $cat ,$disc, $fee); 
                  $tf=$tf+$fee;      
            }
            else {    
                $doc->printHeader($mname,$mDateFrom,$mDateTo,$stadion,$organisator);
                $doc->printLineBreak(2);     
                $doc->printLine1($nbr, $name, $year );   
                $doc->printLine2($club, $cat);  
                $doc->printLine3($disc);   
                $doc->printLineFooter($fee, $date, $place, false);  
                $l+=6;  
			    $doc->insertPageBreak();   
            }
            $l++;            // increment line count
		}
	
        // new athlete   
		if($a != $row[0])		
		    {  
             
            $l = 0;                  // reset line counter  
            $fee=0;    
            $disc="";
            $sep="";  
			$name = $row[2] . " " . $row[3];		// assemble name field
			$year = $row[4];
			$cat = $row[5];  
            $mname = $row[23];  
            $stadion = $row[27];  
            $organisator = $row[28];  
            $mDateFrom = $row[25];  
            $mDateTo = $row[26];     
             
			if(empty($row[8])) {		// not assigned to a team
				$club = $row[7];		// use club name
			}
			else {
				$club = $row[8];		// use team name
			}    
            $place = $row[24];  
		}
	
            $Info = ($row[18]!="") ? ' ('.$row[18].')' : '';    
            $noFee=false;   
            if  ($row[18]!="" && $m != $row[19]) { 
                    $disc = $disc . $sep . $row[19] . $Info;    // add combined   
                }
            else 
                if  ($row[18]!="" && $m == $row[19]) { 
                          $noFee=true;                        // the same combined
                }
                else {
                     $Info = ($row[17]!="") ? ' ('.$row[17].')'  : '';   
				     $disc = $disc . $sep . $row[10] .$Info ; 	// add discipline
                }      
		               
		$sep = ", ";
	  
        if (!$noFee) {
            if ($fee==0) {
		     $fee+=$row[22];  
             }
             else {
             $fee+=($row[22] - ($reduction/100));  
             }   
        }        
	
		$l++;            // increment line count  
		$a = $row[0];
        $m = $row[19];    // keep combined    
    }
	
	if($a > 0)
	    {  
         if ($club_clause!='' && $athlete_clause=='') { 
               if ($first) {                                  
                       $doc->printHeader($mname,$mDateFrom,$mDateTo,$stadion,$organisator);
                       $doc->printLineBreak(1); 
                       $doc->printLineClub($club); 
                       $l+=4; 
                       $first=false;
                       printf("<tr><td colspan='4'>"); 
                  } 
                              
              $doc->printLine4($first, $name, $year, $cat ,$disc, $fee); 
              printf("</td></tr>");  
              $tf=$tf+$fee;  
              $doc->printLineFooter($tf,$date, $place, true);   
         }
         else {  
             $doc->printHeader($mname,$mDateFrom,$mDateTo,$stadion,$organisator);
             $doc->printLineBreak(2);     
			 $doc->printLine1($nbr, $name, $year );   
             $doc->printLine2($club, $cat);  
             $doc->printLine3($disc);              
             $doc->printLineFooter($fee,$date, $place, false);  
             $l+=6;     
         }    
	}     
	
	mysql_free_result($result);
}						// ET DB error     


$doc->endPage();		// end HTML page for printing

?>
