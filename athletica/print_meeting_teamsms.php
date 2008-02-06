<?php

/**********
 *
 *	print_meeting_teamsms.php
 *	-------------------------
 *	
 */

require('./lib/common.lib.php');
require('./lib/cl_gui_teampage.lib.php');
require('./lib/cl_print_teampage.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
	}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

//
// Content
// -------

$cat_clause="";
$club_clause="";

// selection arguments
if($_GET['category'] > 0) {		// category selected
	$cat_clause=" AND t.xKategorie = " . $_GET['category'];
}

if($_GET['club'] > 0) {		// club selected
	$club_clause=" AND t.xVerein = " . $_GET['club'];
}


$print = false;
if($_GET['formaction'] == 'print') {		// page for printing 
	$print = true;
}


// start a new HTML page for printing
if ($_GET['list']=='team')
{
	if($print == true) {
		$doc = new PRINT_TeamPage($_COOKIE['meeting']);
	}
	else {
		$doc = new GUI_TeamPage($_COOKIE['meeting']);
	}
}
else {
	if($print == true) {
		$doc = new PRINT_TeamDiscPage($_COOKIE['meeting']);
	}
	else {
		$doc = new GUI_TeamDiscPage($_COOKIE['meeting']);
	}
}

if($_GET['cover'] == 'cover') {		// print cover page
	$doc->printCover("$strEntries $strTeamsTeamSM");
}

// Read all teams
/*$result = mysql_query("
	SELECT
		t.xTeam
		, v.Name
		, k.Name
		, t.Name
		, t.xKategorie
	FROM
		team AS t
		, kategorie AS k
		, verein AS v
	WHERE t.xMeeting = " . $_COOKIE['meeting_id'] . "
	AND v.xVerein = t.xVerein
	AND k.xKategorie = t.xKategorie
	$cat_clause
	$club_clause
	ORDER BY
		v.Sortierwert
		, k.Anzeige
		, t.Name
");*/

// Read all teams
$sql = "SELECT t.xTeamsm
			   , v.Name
			   , k.Name
			   , t.Name
			   , t.xKategorie
		  FROM teamsm AS t
	 LEFT JOIN kategorie AS k ON(t.xKategorie = k.xKategorie)
	 LEFT JOIN verein AS v ON(t.xVerein = v.xVerein) 
	 	 WHERE t.xMeeting = ".$_COOKIE['meeting_id']." 
	 	   ".$cat_clause."
	 	   ".$club_clause." 
	  ORDER BY v.Sortierwert
	  		   , k.Anzeige
	  		   , t.Name";
$result = mysql_query($sql);

if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else if(mysql_num_rows($result) > 0)  // data found
{
	$l = 0;		// line counter

	// Team loop
	while ($row = mysql_fetch_row($result))
	{
		if($print == true)
		{	
			if(($_GET['break'] == 'team')
				&& ($l !=0))						// page break after each team 
			{
				$doc->insertPageBreak();
			}
		}
		$doc->printSubTitle("$row[1] $row[2]: $row[3]");
		$l = 0;					// reset line counter

		
		if($_GET['list'] == 'team')	// Print athlete list
		{
			// read all athletes per Team
			$list_sql = "SELECT a.xAnmeldung
								, a.Startnummer
								, at.Name
								, at.Vorname
								, at.Jahrgang 
						   FROM anmeldung AS a 
					  LEFT JOIN teamsmathlet AS sma ON(a.xAnmeldung = sma.xAnmeldung) 
					  LEFT JOIN athlet AS at ON(a.xAthlet = at.xAthlet) 
					  	  WHERE sma.xTeamsm = ".$row[0]." 
					   ORDER BY at.Name
					   			, at.Vorname;";
			$list_res = mysql_query($list_sql);
		}
		else									 // Print discipline list
		{
			// read all athletes per team discipline (not relays)
			$list_sql = "SELECT d.Name
								, a.Startnummer
								, at.Name
								, at.Vorname
								, at.Jahrgang
						   FROM anmeldung AS a
					  LEFT JOIN athlet AS at ON(a.xAthlet = at.xAthlet) 
					  LEFT JOIN start AS st ON(a.xAnmeldung = st.xAnmeldung)
					  LEFT JOIN wettkampf AS w ON(st.xWettkampf = w.xWettkampf) 
					  LEFT JOIN disziplin AS d ON(w.xDisziplin = d.xDisziplin) 
					  LEFT JOIN teamsmathlet AS sma ON(a.xAnmeldung = sma.xAnmeldung)
					 	  WHERE w.xMeeting = ".$_COOKIE['meeting_id']." 
					 	    AND w.xKategorie = ".$row[4]." 
					 	    AND d.Typ != ".$cfgDisciplineType[$strDiscTypeRelay]." 
					 	    AND xTeamsm = ".$row[0]." 
					   GROUP BY st.xStart
					   ORDER BY d.Anzeige
					   			, at.Name
					   			, at.Vorname;";
			$list_res = mysql_query($list_sql);
		}

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else if(mysql_num_rows($list_res) > 0)  // data found
		{
			$d = '';
			while ($list_row = mysql_fetch_row($list_res))
			{
				if($l == 0) {					// new page, print header line
					printf("<table class='dialog'>\n");
					$doc->printHeaderLine();
				}

				if($_GET['list'] == 'team')
				{
					$disc = '';		// list of disciplines

					$disc_res = mysql_query("
						SELECT
							d.Kurzname
						FROM
							disziplin AS d
							, start AS s
							, wettkampf AS w
						WHERE s.xAnmeldung = $list_row[0]
						AND s.xWettkampf = w.xWettkampf
						AND w.xDisziplin = d.xDisziplin
						ORDER BY
							d.Anzeige
					");

					if(mysql_errno() > 0)		// DB error
					{
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
					else
					{
						$sep = '';	
						while ($disc_row = mysql_fetch_row($disc_res))
						{
							$disc = $disc . $sep . $disc_row[0];	// add discipline	
							$sep = ", ";	
						}
						mysql_free_result($disc_res);
					}	// ET DB error
					
					$doc->printLine($list_row[1], $list_row[2] . " " . $list_row[3],
						AA_formatYearOfBirth($list_row[4]), $disc);
				}
				else		// discipline list
				{
					$disc = '';
					if($list_row[0] != $d) {
						$disc = $list_row[0];
					}
					$d = $list_row[0];	// keep discipline

					$doc->printLine($disc, $list_row[1],
						$list_row[2] . " " . $list_row[3],
						AA_formatYearOfBirth($list_row[4]));
				}
				$l++;			// increment line count
			}	// END LOOP Athletes
			mysql_free_result($list_res);
		}	// ET DB error athlets

		printf("</table>\n");

	}	// END LOOP Team
}	// ET DB error teams

$doc->endPage();		// end HTML page for printing

?>
