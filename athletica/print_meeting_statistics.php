<?php

/**********
 *
 *	print_meeting_statistics.php
 *	----------------------------
 *	
 */

require('./lib/common.lib.php');
require('./lib/cl_print_page.lib.php');
require('./lib/results.lib.php');

if(AA_connectToDB() == FALSE)	{				// invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

if($_GET['arg'] == 'print') {	// page for printing
	$doc = new PRINT_Statistics($_COOKIE['meeting']);
	$doc->printPageTitle($strStatistics . " " . $_COOKIE['meeting']);
}
else {
	$doc = new GUI_Statistics("Statistics");
}

//
//	Statistic 1: Entry overview
// ---------------------------

$doc->printSubTitle($strEntries);
$doc->startList();
$doc->printHeaderLine($strCategory, $strAthletes, $strRelays);

// read all entries
$result = mysql_query("
	SELECT
		k.xKategorie
		, k.Name
		, IF(a.xKategorie IS NULL,0,COUNT(*))
	FROM
		kategorie AS k
	LEFT JOIN anmeldung AS a
		ON a.xMeeting = " . $_COOKIE['meeting_id'] . "
	WHERE k.xKategorie = a.xKategorie
	GROUP BY
		a.xKategorie
	ORDER BY
		k.Anzeige
");

if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else
{
	$te = 0;		// totel entries
	$tr = 0;		// totel relays
	while ($row = mysql_fetch_row($result))
	{
		// get nbr of relays for this category
		$rel = 0;
		$res = mysql_query("
			SELECT
				COUNT(*)
			FROM
				staffel AS s
			WHERE s.xMeeting = " . $_COOKIE['meeting_id'] . "
			AND s.xKategorie = $row[0]
		");

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			$relay_row = mysql_fetch_row($res);
			$rel = $relay_row[0];		// save nbr of relays
			mysql_free_result($res);
		}

		// print data
		$te = $te + $row[2];		// add entries
		$tr = $tr + $rel;			// add relays
		$doc->printLine($row[1], $row[2], $rel);
	}
	mysql_free_result($result);
}
// add total
$doc->printTotalLine($strTotal, $te, $tr);
$doc->endList();


//
//	Statistic 2: Entries per discipline
// -----------------------------------

$doc->printSubTitle($strStartsPerDisc);
$doc->startList();
$doc->printHeaderLine($strCategory, $strDiscipline, $strEntries, $strStarted);

 mysql_query("DROP TABLE IF EXISTS result_tmp");    // temporary table    
  
 $query_tmp="CREATE TEMPORARY TABLE result_tmp select 
                            MIN(r.Startzeit) AS Startzeit, r.xWettkampf from runde as r  
                            group by r.xWettkampf 
 ";   
 $res_tmp = mysql_query($query_tmp);     
 
 if(mysql_errno() > 0)        // DB error
    {
    AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
 }
 else  
     {   
// read all events
/*
$sql = "
	SELECT
		k.Name
		, d.Name
		, IF(s.xWettkampf IS NULL,0,COUNT(*))
		, w.xWettkampf
		, SUM(s.Anwesend)
		, IF(w.Mehrkampfcode > 0, dd.Name,w.Info) as DiszInfo
		, wk.Name
		, IF(w.Typ = ".$cfgEventType[$strEventTypeSingleCombined].",w.Mehrkampfcode, 0)
		, IF(s.xAnmeldung > 0, an.xKategorie, st.xKategorie) AS Cat
		, w.Mehrkampfcode
	FROM
		disziplin AS d
		, kategorie AS wk
		, wettkampf AS w
	LEFT JOIN start AS s
		ON w.xWettkampf = s.xWettkampf
		AND ((d.Staffellaeufer = 0
				AND s.xAnmeldung > 0)
			OR (d.Staffellaeufer > 0
				AND s.xStaffel > 0))
	LEFT JOIN anmeldung AS an ON (s.xAnmeldung = an.xAnmeldung)
	LEFT JOIN staffel AS st ON (s.xStaffel = st.xStaffel)
	LEFT JOIN kategorie AS k ON ( k.xKategorie = 
		IF(an.xKategorie > 0, an.xKategorie, st.xKategorie))
	LEFT JOIN disziplin as dd ON (w.Info = dd.Kurzname)
	WHERE w.xMeeting = " . $_COOKIE['meeting_id'] . "
	AND d.xDisziplin= w.xDisziplin
	AND wk.xKategorie = w.xKategorie
	GROUP BY
		Cat, s.xWettkampf
	ORDER BY
		k.Anzeige
		, k.Kurzname DESC
		, w.Typ
		, w.Mehrkampfcode
		, d.Anzeige
";
*/
$sql = "
    SELECT
        k.Name
        , d.Name
        , IF(s.xWettkampf IS NULL,0,COUNT(*))
        , w.xWettkampf
        , SUM(s.Anwesend)
        , IF(w.Mehrkampfcode > 0, dd.Name,w.Info) as DiszInfo
        , wk.Name
        , IF(w.Typ = ".$cfgEventType[$strEventTypeSingleCombined].",w.Mehrkampfcode, 0)
        , IF(s.xAnmeldung > 0, an.xKategorie, st.xKategorie) AS Cat
        , w.Mehrkampfcode
        , r.Status
        , r.xRundentyp
        , t.Startzeit  
    FROM
        disziplin AS d
        , kategorie AS wk
        , wettkampf AS w
    LEFT JOIN start AS s
        ON w.xWettkampf = s.xWettkampf
        AND ((d.Staffellaeufer = 0
                AND s.xAnmeldung > 0)
            OR (d.Staffellaeufer > 0
                AND s.xStaffel > 0))
    LEFT JOIN anmeldung AS an ON (s.xAnmeldung = an.xAnmeldung)
    LEFT JOIN staffel AS st ON (s.xStaffel = st.xStaffel)
    LEFT JOIN kategorie AS k ON ( k.xKategorie = 
        IF(an.xKategorie > 0, an.xKategorie, st.xKategorie))
    LEFT JOIN disziplin as dd ON (w.Info = dd.Kurzname)    
    LEFT JOIN runde AS r ON (r.xWettkampf = w.xWettkampf)    
    LEFT JOIN result_tmp as t ON (s.xWettkampf = t.xWettkampf) 
    WHERE w.xMeeting = " . $_COOKIE['meeting_id'] . "
    AND d.xDisziplin= w.xDisziplin
    AND wk.xKategorie = w.xKategorie
    AND r.Startzeit = t.Startzeit  
    GROUP BY
        Cat, s.xWettkampf
    ORDER BY
        k.Anzeige
        , k.Kurzname DESC
        , w.Typ
        , w.Mehrkampfcode
        , d.Anzeige
";

$result = mysql_query($sql);

if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else if(mysql_num_rows($result) > 0)  // data found
{
	$e = 0;		// total entries per category
	$s = 0;		// total startet per category
	$te = 0;	// total entries overall
	$ts = 0;	// total started
	$cat = '';
	$mkCode = 0;	// combined code
	$rowclass='odd';
	while ($row = mysql_fetch_row($result))
	{
		if($cat != $row[0]){
			$mkCode = 0;
		}
		if($row[7] != 0){ // if we got combined codes
			if($mkCode != $row[7]){
				$mkCode = $row[7];
			}else{
				// skip next entries because a combined event is "one discipline"
				continue;
			}
		}else{
			$mkCode = 0;
		}
		if ($row[10]==$cfgRoundStatus['open'] 
                    || $row[10]== $cfgRoundStatus['enrolement_pending']
                    || $row[10]== $cfgRoundStatus['enrolement_done']
                    || $row[10]== $cfgRoundStatus['heats_done']) 
            $row2 = 0;                  // no started athletes when enrolement open or pending
        else 
		    $row2 = $row[2] - $row[4];	// calculating started athletes:
									    // registrations - athletes with s.Anwesend = 1 (didn't show up at apell)
		
		$Info = ($row[5]!="") ? ' ('.$row[5].')': '';
		$disc = $row[1] ." ". $row[6] . $Info;
		$disc = ($row[9]>0) ? $row[5] . " " . $row[6] : $disc;
			
		// add category total
		if($cat != $row[0]) {
			if($cat != '') {
				$te = $te + $e;		// calculate entries grand total
				$ts += $s;
				$doc->printTotalLine($strTotal, '', $e, $s);
				$e=0;
				$s=0;
			}
			$cat = $row[0];
			$doc->printLine($row[0], $disc, $row[2], $row2);	// line with category
		}
		else {
			$doc->printLine('', $disc, $row[2], $row2);	// line without category
		}
		$e = $e + $row[2];					// add entries
		$s += $row2;
	}

	// add last category total
	if($cat != '') {
		$te = $te + $e;		// calculate entries grand total
		$ts += $s;
		$doc->printTotalLine($strTotal, '', $e, $s);
		$doc->printTotalLine($strTotal." ".$strMeeting, '', $te, $ts);
	}
	mysql_free_result($result);
}
}
$doc->endList();


//
//	Statistic 3: Fees and deposits 
// ------------------------------
$doc->printSubTitle($strFee." / ".$strDeposit);
$doc->startList();
$doc->printHeaderLine($strClub, $strFee, $strDeposit, $strEntries, $strStarted);

// read all starts per club and add fee and deposit
/*
$result = mysql_query("
	SELECT
		v.xVerein
		, v.Name
	FROM
		verein AS v
	ORDER BY
		v.Sortierwert
");


if(mysql_errno() > 0)		// DB error
{
	AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
}
else if(mysql_num_rows($result) > 0)  // data found
{
*/
	$tf = 0;		// total fees
	$td = 0;		// total deposit
	$ts = 0;		// total starts
	$te = 0;		// total entries
	$clubs = array();
	$clublist = "";
	$rowclass='odd';
	/*while ($row = mysql_fetch_row($result))
	{
		// get fee, deposit for this club's athletes
		// and entries
		$ath_f = 0;
		$ath_d = 0;
		$ath_e = 0;
		$res = mysql_query("
			SELECT
				SUM(w.Startgeld)
				, SUM(w.Haftgeld)
				, COUNT(s.xStart)
			FROM
				anmeldung AS a
				, athlet AS at
				, disziplin AS d
				, start AS s
				, wettkampf AS w
			WHERE at.xVerein = $row[0]
			AND a.xAthlet = at.xAthlet
			AND a.xMeeting = " . $_COOKIE['meeting_id'] . "
			AND s.xAnmeldung = a.xAnmeldung
			AND w.xWettkampf = s.xWettkampf
			AND d.xDisziplin = w.xDisziplin
			AND d.Staffellaeufer = 0
		");
		
		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			$athlete_row = mysql_fetch_row($res);
			$ath_f = $athlete_row[0];		// save relay fee
			$ath_d = $athlete_row[1];		// save relay deposit
			$ath_e = $athlete_row[2];
			mysql_free_result($res);
		}

		// get fee, deposit for this club's relays
		$rel_f = 0;
		$rel_d = 0;
		$res = mysql_query("
			SELECT
				SUM(w.Startgeld)
				, SUM(w.Haftgeld)
			FROM
				staffel AS st
				, start AS s
				, wettkampf AS w
			WHERE st.xVerein = $row[0]
			AND st.xMeeting = " . $_COOKIE['meeting_id'] . "
			AND s.xStaffel = st.xStaffel
			AND w.xWettkampf = s.xWettkampf
		");

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			$relay_row = mysql_fetch_row($res);
			$rel_f = $relay_row[0];		// save relay fee
			$rel_d = $relay_row[1];		// save relay deposit
			mysql_free_result($res);
		}
		
		$club_s = 0;
		// get starts by club
		$res = mysql_query("
			SELECT
				COUNT(DISTINCT ss.xSerienstart)
			FROM
				anmeldung AS a
				, athlet AS at
				, disziplin AS d
				, start AS s
				, wettkampf AS w
				, serienstart as ss
			LEFT JOIN resultat as r ON ss.xSerienstart = r.xSerienstart
			WHERE at.xVerein = $row[0]
			AND a.xAthlet = at.xAthlet
			AND a.xMeeting = " . $_COOKIE['meeting_id'] . "
			AND s.xAnmeldung = a.xAnmeldung
			AND w.xWettkampf = s.xWettkampf "
			//AND d.xDisziplin = w.xDisziplin
			//AND d.Staffellaeufer = 0
			." AND	ss.xStart = s.xStart
			AND 	r.Leistung <> '".$cfgInvalidResult['DNS']['code']."'
		");
		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			$starts = mysql_fetch_row($res);
			$club_s = $starts[0];
			mysql_free_result($res);
		}
		
		// Calculations
		// - fee and deposit per club
		// - accumulate totals
		$f = $ath_f + $rel_f;		// calculate fee
		$d = $ath_d + $rel_d;		// calculate deposit
		
		$tf = $tf + $f;		// calculate fee grand total
		$td = $td + $d;		// calculate deposit grand total
		$ts = $ts + $club_s;	// calc starts total
		$te = $te + $ath_e;	// calc entries total

		if(($f > 0) || ($d > 0))
		{
			$doc->printLine($row[1], $f, $d);
		}	// ET data for this club
	}*/
/*	
	while ($row = mysql_fetch_row($result)){
		$clubs[$row[0]]['name'] = $row[1];
		$clublist .= "$row[0],";
	}
	$clublist = substr($clublist, 0, -1);
	
	if(count($clubs) > 0){
*/		
		// get fee, deposit for this club's athletes
		// and entries
      /*  
		$sql ="
			SELECT
				at.xVerein
				, SUM(w.Startgeld)
				, SUM(w.Haftgeld)
				, COUNT(s.xStart)
				, SUM(s.Anwesend)
				, IF(w.Typ = ".$cfgEventType[$strEventTypeSingleCombined].",w.Mehrkampfcode, 0)
				, w.xKategorie
			FROM
				anmeldung AS a
				, athlet AS at
				, disziplin AS d
				, start AS s
				, wettkampf AS w
			WHERE at.xVerein IN ($clublist)
			AND a.xAthlet = at.xAthlet
			AND a.xMeeting = " . $_COOKIE['meeting_id'] . "
			AND s.xAnmeldung = a.xAnmeldung
			AND w.xWettkampf = s.xWettkampf
			AND d.xDisziplin = w.xDisziplin
			AND d.Staffellaeufer = 0
			GROUP BY
				at.xVerein, w.xWettkampf
			ORDER BY
				at.xVerein
				, w.xKategorie
				, w.Typ
				, w.Mehrkampfcode
		";
       */ 
         $sql ="
            SELECT  
                v.xVerein,                 
                SUM(w.Startgeld), 
                SUM(w.Haftgeld), 
                count(*),
                SUM(s.Anwesend), 
                IF(w.Typ = ".$cfgEventType[$strEventTypeSingleCombined].",w.Mehrkampfcode, 0),   
                w.xKategorie,
                v.Name,
                s.xWettkampf, 
                w.Mehrkampfcode, 
                s.xStaffel, 
                s.xAnmeldung, 
                a.xAthlet, 
                at.Name, 
                at.Vorname , 
                d.xDisziplin  , 
                d.Name 
            FROM
                start AS s
                LEFT  JOIN anmeldung AS a ON (s.xAnmeldung=a.xAnmeldung) 
                LEFT JOIN athlet as at ON (a.xAthlet=at.xAthlet)
                LEFT JOIN wettkampf as w ON (s.xWettkampf=w.xWettkampf)
                LEFT JOIN disziplin as d On (w.xDisziplin=d.xDisziplin)
                LEFT JOIN verein as v ON (at.xVerein=v.xVerein) 
                LEFT JOIN Staffel as st ON (s.xStaffel = st.xStaffel)  
            where s.xStaffel = 0    
            GROUP BY v.xVerein , w.xWettkampf                    
            ORDER BY v.xVerein, w.Mehrkampfcode
        ";
        
		$res = mysql_query($sql);
		
		if(mysql_errno() > 0){
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}else{
			$mkCode = 0;
			$cat = 0;
			$club = 0;
			while($row = mysql_fetch_array($res)){
				if($cat != $row[6]){
					$cat = $row[6];
					$mkCode = 0;
				}
				if($club != $row[0]){
                    $clubs[$row[0]]['name'] = $row[7];  
					$club = $row[0];
					$mkCode = 0;
				}
				if($row[5] != 0){ // if we got combined codes
					if($mkCode != $row[5]){
						$mkCode = $row[5];
					}else{
						//$clubs[$row[0]]['fee'] += $row[1];       //calculate fee and deposit for each combined event
						//$clubs[$row[0]]['deposit'] += $row[2];
						
						// skip next entries because a combined event is "one discipline"
						continue;
					}
				}else{
					$mkCode = 0;
				}
				$clubs[$row[0]]['fee'] += $row[1];
				$clubs[$row[0]]['deposit'] += $row[2];
				$clubs[$row[0]]['entries'] += $row[3];
				$clubs[$row[0]]['starts'] += $row[3]-$row[4]; // calculate started athletes (those who were at the apell)
			}
			
			mysql_free_result($res);
		}
		
	/*	// get fee, deposit for this club's relays
		$res = mysql_query("
			SELECT
				st.xVerein
				, SUM(w.Startgeld)
				, SUM(w.Haftgeld)
				, COUNT(s.xStart)
			FROM
				staffel AS st
				, start AS s
				, wettkampf AS w
			WHERE st.xVerein IN ($clublist)
			AND st.xMeeting = " . $_COOKIE['meeting_id'] . "
			AND s.xStaffel = st.xStaffel
			AND w.xWettkampf = s.xWettkampf
			GROUP BY
				st.xVerein
		");

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			while($row = mysql_fetch_array($res)){
				$clubs[$row[0]]['fee'] += $row[1];
				$clubs[$row[0]]['deposit'] += $row[2];
				$clubs[$row[0]]['entries'] += $row[3]; // add relay starts to entries and starts
				$clubs[$row[0]]['starts'] += $row[3];
			}
			
			mysql_free_result($res);
		}
		
		// get starts by club
		/*$res = mysql_query("
			SELECT
				at.xVerein
				, COUNT(DISTINCT ss.xSerienstart)
			FROM
				anmeldung AS a
				, athlet AS at
				, disziplin AS d
				, start AS s
				, wettkampf AS w
				, serienstart as ss
			LEFT JOIN resultat as r ON ss.xSerienstart = r.xSerienstart
			WHERE at.xVerein IN ($clublist)
			AND a.xAthlet = at.xAthlet
			AND a.xMeeting = " . $_COOKIE['meeting_id'] . "
			AND s.xAnmeldung = a.xAnmeldung
			AND w.xWettkampf = s.xWettkampf "
			//AND d.xDisziplin = w.xDisziplin
			//AND d.Staffellaeufer = 0
			." AND	ss.xStart = s.xStart
			AND 	r.Leistung <> '".$cfgInvalidResult['DNS']['code']."'
			GROUP BY
				at.xVerein
		");
		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			while($row = mysql_fetch_array($res)){
				$clubs[$row[0]]['starts'] = $row[1];
			}
			
			mysql_free_result($res);
		}*/
		
		
		/*echo "<pre>";
		print_r($clubs);
		echo "</pre>";
		*/
		
		// output all collected data
		foreach($clubs as $xVerein => $club){
			if($club['fee'] > 0 || $club['deposit'] > 0){

				//calculate fee-reduction for second and more disciplines per athlete
				/*
                $sql ="SELECT
						athlet.xVerein
						, (count(start.xWettkampf)-1) * (StartgeldReduktion/100) as ReductionAmount    
					FROM
						athlet
						INNER JOIN anmeldung 
						ON (athlet.xAthlet = anmeldung.xAthlet)
						INNER JOIN start 
						ON (anmeldung.xAnmeldung = start.xAnmeldung)
						INNER JOIN wettkampf 
						ON (start.xWettkampf = wettkampf.xWettkampf)
						INNER JOIN meeting 
						ON (wettkampf.xMeeting = meeting.xMeeting)
					WHERE  ((athlet.xVerein = '$xVerein')
						AND	(wettkampf.Mehrkampfcode =0
						OR wettkampf.Mehrkampfende =1))
						AND anmeldung.xMeeting = " . $_COOKIE['meeting_id'] . "
					GROUP BY athlet.xVerein, athlet.xAthlet
					HAVING (count(start.xWettkampf) >1)";
                */
                
                $sql="SELECT
                        athlet.xVerein
                       , (count(s.xWettkampf)-1) * (StartgeldReduktion/100) as ReductionAmount                                                                                        
                        ,athlet.Name
                        ,athlet.Vorname
                        , t.Startzeit
                        , SUM(if (r.Status=4 OR r.Status=3,1,0)) as started
                        , SUM(s.Anwesend)  as anwesend
                        , SUM(if (r.Status=4 OR r.Status=3,wettkampf.Haftgeld=0,wettkampf.Haftgeld) )  AS Haftgeld
                        , SUM(wettkampf.Startgeld) AS Startgeld
                        , count(s.xWettkampf) as enrolement
                        , meeting.Haftgeld 
                    FROM
                        athlet
                        INNER JOIN anmeldung 
                        ON (athlet.xAthlet = anmeldung.xAthlet)
                        INNER JOIN start As s
                        ON (anmeldung.xAnmeldung = s.xAnmeldung)
                        INNER JOIN wettkampf 
                        ON (s.xWettkampf = wettkampf.xWettkampf)
                        INNER JOIN meeting 
                        ON (wettkampf.xMeeting = meeting.xMeeting)
                        LEFT JOIN runde AS r ON (r.xWettkampf = s.xWettkampf)   
                        LEFT JOIN result_tmp as t ON (s.xWettkampf=t.xWettkampf) 
                    WHERE  ((athlet.xVerein = '$xVerein')
                        AND    (wettkampf.Mehrkampfcode =0
                        OR wettkampf.Mehrkampfende =1))
                        AND anmeldung.xMeeting =  " . $_COOKIE['meeting_id'] . " 
                         AND r.Startzeit = t.Startzeit  
                    GROUP BY athlet.xVerein, athlet.xAthlet";
                
				$res = mysql_query($sql);    
                
				$reduction = 0;
                $starts = 0;
               // $fee = 0;
               // $deposit = 0;
               // $entries = 0;
                
				while($row = mysql_fetch_array($res)){ 
					$reduction += $row['ReductionAmount'];
                    $starts+=$row['started'] -$row['anwesend'];
                    $m_deposit= $row['Haftgeld']/100;
                    //$fee+=$row['Startgeld']-$row['ReductionAmount'] ;
                    //$deposit+=$row['Haftgeld'];
                    //$entries+=$row['enrolement'];
                    
				}
                $deposit=($club['entries'] - $starts) * $m_deposit;  
                
				$tf += $club['fee']-$reduction ;
				//$td += $club['deposit'];
				$te += $club['entries'];
				//$ts += $club['starts'];
                
               // $tf += $fee;
                $td += $deposit;
               // $te += $entries;
                $ts += $starts;
				
				//$doc->printLine($club['name'], $club['fee']-$reduction, $club['deposit'], $club['entries'], $club['starts']);
			    $doc->printLine($club['name'], $club['fee']-$reduction, $deposit, $club['entries'],  $starts);                                              
               // $doc->printLine($club['name'],$fee, $deposit, $entries, $starts);   
            }
		}
		
	//}
	
	// add grand total
	$doc->printTotalLine($strTotal, $tf, $td, $te, $ts);
    
	mysql_free_result($result);
//}
    mysql_query("DROP TABLE IF EXISTS result_tmp");   
    
$doc->endList();
$doc->endPage();	// end HTML page
?>
