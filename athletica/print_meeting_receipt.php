<?php

/**********
 *
 *	print_meeting_entries.php
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


$athlete_clause="";

// basic sort argument sort by name    
	
$argument = "at.Name, at.Vorname, d2.Name, d.Anzeige";  

// selection arguments
if($_GET['club'] > 0) {        // club selected
    $club_clause = " AND v.xVerein = " . $_GET['club'];
} 
else if($_GET['athleteSearch'] > 0 ) {        // athlete selected
    $athlete_clause = " AND a.xAnmeldung = " . $_GET['athleteSearch'];
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
        , m.Ort    
	FROM
		anmeldung AS a
		, athlet AS at
		, disziplin AS d
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
	LEFT JOIN disziplin AS d2 
		ON (w.Typ = 1 AND w.Mehrkampfcode = d2.Code)
    LEFT JOIN meeting AS m ON (a.xMeeting = m.xMeeting)  
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
	
	// full list, sorted by name 
	while ($row = mysql_fetch_row($result))
	{          
		// print previous athlete, if any
		if($a != $row[0] && $a > 0)
		{    
            $name = "";
            $year = "";
            $cat = "";
            $club = "";
            $disc = "";  
            $sep = "";   
            $place = "";
            $fee = "";
            $date = "";  
            
            $doc->printHeaderLine();
            $doc->printLineBreak(2);     
            $doc->printLine1($nbr, $name, $year );   
            $doc->printLine2($club, $cat);  
            $doc->printLine3($disc);
            $doc->printLineBreak(1);
            $doc->printLine4($fee); 
            $doc->printLineBreak(2);   
            $doc->printLine5($date, $place);
            $doc->printLineBreak(1);   
            $doc->printLine6(); 
            $l+=6;     
            
			$doc->insertPageBreak();   
		}
	
        // new athlete   
		if($a != $row[0])		
		    {
            $l = 0;                  // reset line counter  
            $fee=0;    
			$name = $row[2] . " " . $row[3];		// assemble name field
			$year = $row[4];
			$cat = $row[5];   
			if(empty($row[8])) {		// not assigned to a team
				$club = $row[7];		// use club name
			}
			else {
				$club = $row[8];		// use team name
			}    
            $place = $row[23];  
		}
		
		if(($row[11] == $cfgDisciplineType[$strDiscTypeTrack])
			|| ($row[11] == $cfgDisciplineType[$strDiscTypeTrackNoWind])
			|| ($row[11] == $cfgDisciplineType[$strDiscTypeRelay])
			|| ($row[11] == $cfgDisciplineType[$strDiscTypeDistance]))
		    {
			$perf = AA_formatResultTime($row[12]);
		}
		else {
			$perf = AA_formatResultMeter($row[12]);
		}    
	
	    if($perf == 0){    
            $Info = ($row[18]!="") ? ' ('.$row[18].')' : '';    
            $noFee=false;  
            if  ($row[18]!="" && $m != $row[19]) { 
                    $disc = $disc . $sep . $row[19] . $Info;    // add combined   
                }
            else 
                if  ($row[18]!="" && $m == $row[19]) { 
                          $noFee=true;                        // the same combined
                }
                else  
				    $disc = $disc . $sep . $row[10] . $Info;	// add discipline
                      
			}else{   
				$Info = ($row[17]!="") ? $row[17] .', ' : '';
				$disc = $disc . $sep . $row[10] . " (".$Info . $perf.")";	// add discipline
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
            $doc->printHeaderLine();
            $doc->printLineBreak(2);     
			$doc->printLine1($nbr, $name, $year );   
            $doc->printLine2($club, $cat);  
            $doc->printLine3($disc);
            $doc->printLineBreak(1);
            $doc->printLine4($fee); 
            $doc->printLineBreak(2);   
            $doc->printLine5($date, $place);
            $doc->printLineBreak(1);   
            $doc->printLine6();  
            $l+=6;         
	}     
	
	mysql_free_result($result);
}						// ET DB error     


$doc->endPage();		// end HTML page for printing

?>
