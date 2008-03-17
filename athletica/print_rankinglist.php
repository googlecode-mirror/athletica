<?php

/**********
 *
 *	print_rankinglist.php
 *	---------------------
 *	
 */

require('./lib/common.lib.php');
require('./lib/rankinglist_sheets.lib.php');
require('./lib/rankinglist_single.lib.php');
require('./lib/rankinglist_combined.lib.php');
require('./lib/rankinglist_team.lib.php');
require('./lib/rankinglist_teamsm.lib.php');

if(AA_connectToDB() == FALSE)	{ // invalid DB connection
	return;		// abort
}

if(AA_checkMeetingID() == FALSE) {		// no meeting selected
	return;		// abort
}

$arrayDisc=array();





// get presets
// -----------
$category = 0;
if(!empty($_GET['category'])) {
	$category = $_GET['category'];
}

$event = 0;
if(!empty($_GET['event'])) {
	$event = $_GET['event'];
}

$round = 0;
if(!empty($_GET['round'])) {
	$round = $_GET['round'];
}

$type = 'single';
if(!empty($_GET['type'])) {
	$type = $_GET['type'];
}

$team = 'ranking';
if(!empty($_GET['team'])) {
	$team = $_GET['team'];
}

$date = '%';
if(isset($_GET['date']) && !empty($_GET['date'])) {
	$date = $_GET['date'];
}

$cover = FALSE;
$cover_timing = false;
if($_GET['cover'] == 'cover') {
	$cover = TRUE;
	$cover_timing = (isset($_GET['cover_timing']));
}

$formaction = 'view';
if(!empty($_GET['formaction'])) {
	$formaction = $_GET['formaction'];
}

$break = 'none';
if(!empty($_GET['break'])) {
	$break = $_GET['break'];
}

$biglist = false;
if($type == "single_attempts"){
	$type = "single";
	$biglist = true;
}

$sepu23 = false;
if($_GET['sepu23'] == "yes"){
	$sepu23 = true;
}                     

if ($category==0 ){
    
  $result_disc="SELECT   
         Distinct (k.Name )          
        , w.Mehrkampfcode
        , d.Name
        , w.xKategorie 
        , k.Geschlecht  
    FROM
        anmeldung AS a
        LEFT JOIN athlet AS at USING (xAthlet)
        LEFT JOIN verein AS v USING (xVerein)
        LEFT JOIN kategorie AS k  ON (k.xKategorie = w.xKategorie )
        LEFT JOIN kategorie AS ka  ON (ka.xKategorie = a.xKategorie ) 
        LEFT JOIN start as st ON (st.xAnmeldung = a.xAnmeldung )
        LEFT JOIN wettkampf as w  USING (xWettkampf)
        LEFT JOIN disziplin as d ON (w.Mehrkampfcode = d.Code) 
        LEFT JOIN region as re ON at.xRegion = re.xRegion
    WHERE a.xMeeting =  " . $_COOKIE['meeting_id'] ."  
     " . $selection . "   
    AND w.Mehrkampfcode > 0
    ORDER BY k.Anzeige";   
  
    $res_disc=mysql_query($result_disc);   
  
    while ($row_disc=mysql_fetch_array($res_disc)) {
            
            for ($i=0;$i<=$_GET['count_' .$row_disc[3]];$i++){   
                if (!empty($_GET['comb_' . $row_disc[3] . '_' . $i ]))
                    $arrayDisc[$row_disc[3]][$i]=$_GET['comb_'. $row_disc[3] . '_' . $i ];  
            } 
    }
}

for ($i=0;$i<=$_GET['count_' .$category];$i++){   
    if (!empty($_GET['comb_' . $category . '_' . $i ]))
        $arrayDisc[$category][$i]=$_GET['comb_'. $category . '_' . $i ];  
} 


// Ranking list single event
if($type == 'single')
{
	AA_rankinglist_Single($category, $event, $round, $formaction, $break, $cover, $biglist, $cover_timing, $date);
}

// Ranking list combined events
else if($type == 'combined')
{   
	AA_rankinglist_Combined($category, $formaction, $break, $cover, $sepu23, $cover_timing, $date, $arrayDisc);
}                                                                                                  

// Ranking list teams events
else if($type == 'team')
{
	@AA_rankinglist_Team($category, $formaction, $break, $cover);
}

// Team sheets
else if($type == 'sheets')
{
	AA_rankinglist_Sheets($category, $formaction, $cover);
}

// Ranking list team sm events
else if($type == 'teamsm')
{
	AA_rankinglist_TeamSM($category, $event, $formaction, $break, $cover, $cover_timing, $date);
}

?>
