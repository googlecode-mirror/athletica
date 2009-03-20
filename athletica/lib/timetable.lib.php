<?php

/**********
 *
 *	timetable extension
 *	-------------------
 * The timetable display function is used by the event monitor
 * and the speaker monitor to print an overview of all events
 * of a meeting.
 * Set the arg-parameter as follows:
 * - monitor
 * - speaker
 */

if (!defined('AA_TIMETABLE_LIB_INCLUDED'))
{
	define('AA_TIMETABLE_LIB_INCLUDED', 1);



/**
 *	show timetable
 *	-------------------
 */
function AA_timetable_display($arg = 'monitor')
{
	require('./config.inc.php');
	require('./lib/common.lib.php');
     
	$result = mysql_query("
		SELECT DISTINCT
			k.Name
		FROM
			wettkampf AS w
			, kategorie AS k
		WHERE w.xMeeting = " . $_COOKIE['meeting_id'] . "
		AND w.xKategorie = k.xKategorie
		ORDER BY
			k.Anzeige,
            k.Kurzname
	");
   
	if(mysql_errno() > 0)	// DB error
	{
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else			// no DB error
	{
		$headerline = "";
		// assemble headerline and category array
		$cats = array();
		while ($row = mysql_fetch_row($result))
		{
			$headerline = $headerline . "<th class='timetable'>$row[0]</th>";
			$cats[] = $row[0];		// category array
		}
		mysql_free_result($result);
       
		// all rounds ordered by date/time
		// - count nbr of present athletes or relays (don't include
		//   athletes starting in relays)
		// - group by r.xRunde to show event-rounds entered more than once
		// - group by s.xWettkampf to count athletes per event
		// (the different date and time fields are required to properly set
		// up the table)
		$res = mysql_query("
			SELECT
				r.xRunde
				, r.Status
				, rt.Typ
				, k.Name
				, d.Kurzname
				, IF(s.xWettkampf IS NULL,0,COUNT(*))
				, TIME_FORMAT(r.Startzeit, '$cfgDBtimeFormat')
				, TIME_FORMAT(r.Startzeit, '%H')
				, DATE_FORMAT(r.Datum, '$cfgDBdateFormat')
				, r.Datum
				, r.xWettkampf
				, k.xKategorie
				, r.Speakerstatus
				, w.Info
				, r.StatusZeitmessung
				, r.Gruppe
				, w.Typ
				, w.Mehrkampfende
				, w.Mehrkampfcode
				, rs.xRundenset
				, rs.Hauptrunde
			FROM
				runde AS r
				, wettkampf AS w
				, kategorie AS k
				, disziplin AS d
			LEFT JOIN rundentyp AS rt
				ON r.xRundentyp = rt.xRundentyp
			LEFT JOIN start AS s
				ON w.xWettkampf = s.xWettkampf
				AND s.Anwesend = 0
				AND ((d.Staffellaeufer = 0
					AND s.xAnmeldung > 0)
					OR (d.Staffellaeufer > 0
					AND s.xStaffel > 0))
			LEFT JOIN rundenset AS rs ON (rs.xRunde = r.xRunde AND rs.xMeeting = " . $_COOKIE['meeting_id'] .") 
			WHERE w.xMeeting=" . $_COOKIE['meeting_id'] ."
			AND r.xWettkampf = w.xWettkampf
			AND w.xKategorie = k.xKategorie
			AND w.xDisziplin = d.xDisziplin
			GROUP BY
				r.xRunde
				, s.xWettkampf
	 		ORDER BY
				r.Datum
				, r.Startzeit
				, k.Anzeige
                , k.Kurzname
				, d.Anzeige
		");   
       
		if(mysql_errno() > 0)	// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else			// no DB error
		{
			$date = 0;
			$hour = '';
			$time = 0;
			$i=0;
			$k='';
			$events = array();	// array to hold last processed round per event
			?>
<table class=timetable> 
			<?php
			while ($row = mysql_fetch_row($res))
			{   
				$combGroup = "";	// combined group if set
				$combined = false;	// is combined event
				$teamsm = false;	// is team sm event
				if($row[16] == $cfgEventType[$strEventTypeSingleCombined]){
					$combined = true;
				}
				if($row[16] == $cfgEventType[$strEventTypeTeamSM]){
					$teamsm = true;
				}
				$roundSet = $row[19];	// set if round is merged
				$roundSetMain = $row[20];	// main round flag of round set
								// if main round -> count athletes from all merged rounds
				
				// new date or time: start a new line
				if(($date != $row[8]) || ($time != $row[6]))	// new date or time
				{
					if($date != 0) {		// not first item
						?>
	</td>
						<?php
						// fill previous line with cell items if necessary
						while(current($cats) == TRUE) {
							?>
	<td class='monitor' />
							<?php
							next($cats);
						}
						?>
</tr>
						<?php
					}

					if($date != $row[8])	{	// new date -> headerline with date
						?>
<tr>
	<th class='date' id='<?php echo "$row[9]$row[7]"; ?>'><?php echo $row[8]; ?></th>
	<?php echo $headerline; ?>
</tr>
						<?php
					}
					else if ($hour != $row[7]) {	// new hour -> headerline
						?>
<tr>
	<th class='timetable_sub' id='<?php echo "$row[9]$row[7]"; ?>' />
	<?php echo $headerline; ?>
</tr>
						<?php
					}		// ET new date or new hour

					if($i % 2 == 0 ) {		// even row number
						$class='even';
					}
					else {	// odd row number
						$class='odd';
					}	
					$i++;
					reset($cats);		// reset category array to first item
					$k = '';
					?>
<tr class='<?php echo $class; ?>'>
	<th class='timetable_sub'><?php echo $row[6]; ?></th>
					<?php
				}		// ET new date, time

				$time = $row[6];
				$hour = $row[7];
				$date = $row[8];
				
				// check round status and set correct link
				if($arg == 'monitor')		// event monitor
				{   
					// check status
					switch($row[1]) {
					case($cfgRoundStatus['open']):
						$class = "";
						//$href = "event_heats.php?round=$row[0]";
						if($combined){ 
							$href = "event_enrolement.php?category=$row[11]&comb=$row[11]_$row[18]&group=$row[15]";
						}else{
							$href = "event_enrolement.php?category=$row[11]&event=$row[10]";
						}
						break;
					case($cfgRoundStatus['enrolement_pending']):
						$class = "st_enrlmt_pend";
						if($combined){  
							$href = "event_enrolement.php?category=$row[11]&comb=$row[11]_$row[18]";
						}else{
							$href = "event_enrolement.php?category=$row[11]&event=$row[10]";
						}
						break;
					case($cfgRoundStatus['enrolement_done']):
						$class = "st_enrlmt_done";
						$href = "event_heats.php?round=$row[0]";
						break;
					case($cfgRoundStatus['heats_in_progress']):
						$class = "st_heats_work";
						$href = "event_heats.php?round=$row[0]";
						break;
					case($cfgRoundStatus['heats_done']):
						$class = "st_heats_done";
						$href = "event_results.php?round=$row[0]";
						break;
					case($cfgRoundStatus['results_in_progress']):
						$class = "st_res_work";
						$href = "event_results.php?round=$row[0]";
						break;
					case($cfgRoundStatus['results_done']):
						$class = "st_res_done";
						$href = "event_results.php?round=$row[0]";
						break;
					}
					if($row[14] == 1 && $row[1] == $cfgRoundStatus['heats_done']){ // results importet from timing
						$class = "st_res_timing";
					}
				}
				else if($arg == 'speaker')		// speaker monitor
				{
					// check round status and set CSS class
					switch($row[1]) {
					case($cfgRoundStatus['open']):
					case($cfgRoundStatus['enrolement_pending']): 
						$class = "";
						$href = "speaker_entries.php?round=$row[0]&group=$row[15]";
						break;  
                    case($cfgRoundStatus['enrolement_done']): 
                       $class = "st_enrlmt_done"; 
                        $href = "speaker_entries.php?round=$row[0]&group=$row[15]";
                        break;   
                    case($cfgRoundStatus['heats_in_progress']): 
                     	$class = "st_heats_work"; 
                        $href = "speaker_entries.php?round=$row[0]&group=$row[15]";
                        break;  			    
					case($cfgRoundStatus['heats_done']):
						$class = "st_heats_done";
						$href = "speaker_results.php?round=$row[0]";
						break;
					case($cfgRoundStatus['results_in_progress']):
						$class = "st_res_work";
						$href = "speaker_results.php?round=$row[0]";
						break;
					case($cfgRoundStatus['results_done']):
						$class = "st_res_done";
						$href = "speaker_results.php?round=$row[0]";
						break;
					}

					// overrule by speaker status, CSS class
					switch($row[12]) {
					case($cfgSpeakerStatus['announcement_pend']):
						$class = "st_anct_pend";
						break;
					case($cfgSpeakerStatus['announcement_done']):
						$class = "st_anct_done";
						break;
					case($cfgSpeakerStatus['ceremony_done']):
						$class = "st_crmny_done";
						break;
					}
				}

                
				// next event is in a different category: go to next cell
				if($k != $row[3])
				{  
					if(key($cats) != 0) { 	// not first category
						?>
	</td>
						<?php
					}

					$k = $row[3];		// keep current category
					while(current($cats) != $k) {
						?>
	<td class='monitor' />
						<?php
						if((next($cats)) == FALSE) {		// after end of array
							break;
						}
					}
                   
					if(array_key_exists($row[10], $events) == TRUE 		// not first round (count qualified athletes)
						&& $combined == false && $teamsm == false) 	// no combined event
					{    
						$starts = "-";
						// get number of athletes/relays with valid result
                        if ($row[2] == 'S' || $row[2] == 'O'){                // round typ: S = Serie ,  O = ohne
						    $sql = "
							    SELECT
								    COUNT(*)
							    FROM
								    serienstart AS ss
								    , serie AS s
							    WHERE 
							        s.xSerie = ss.xSerie
							        AND s.xRunde =" . $events[$row[10]];
						
                        }
                        else {
                            $sql="SELECT
                                COUNT(*)
                            FROM
                                serienstart AS ss
                                , serie AS s
                            WHERE ss.Qualifikation > 0
                                AND s.xSerie = ss.xSerie
                                AND s.xRunde =" . $events[$row[10]];
                        }
                        $result=mysql_query($sql);
                           
						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
						else {
							$start_row = mysql_fetch_row($result);
							$starts = $start_row[0];
							mysql_free_result($result);
						}
					}elseif($combined || $teamsm){ // for combined rounds, count starts for correct group
						 
						if($row[17] == 1){ // if this is a combined last event, every athlete starts
							$starts = $row[5];
						}elseif(empty($row[15])){ // if no group is set
							$starts = $row[5];
						}else{      
							$result = mysql_query("SELECT COUNT(*) FROM
											start as st
											, anmeldung as a
										WHERE	st.xAnmeldung = a.xAnmeldung
										AND	st.xWettkampf = $row[10]
										AND	a.Gruppe = '$row[15]'
                                        AND st.Anwesend = 0");  
							
							if(mysql_errno() > 0) {	 
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}else{
								$start_row = mysql_fetch_array($result);
								$starts = $start_row[0];
								mysql_free_result($result);
							}
							$combGroup = "&nbsp;g".$row[15];
						}
						
					}elseif($roundSet > 0){
						   
						if($roundSetMain == 0){
							$starts = "m";
						}else{
							  
							$result = mysql_query("SELECT COUNT(*) FROM
											rundenset AS rs
											, runde AS r
											, wettkampf AS w
											, disziplin AS d
											LEFT JOIN start AS s
												ON w.xWettkampf = s.xWettkampf
												AND s.Anwesend = 0
												AND ((d.Staffellaeufer = 0
													AND s.xAnmeldung > 0)
													OR (d.Staffellaeufer > 0
													AND s.xStaffel > 0))
										WHERE
											rs.xRundenset = $roundSet
										AND	r.xRunde = rs.xRunde
										AND	w.xWettkampf = r.xWettkampf
										AND	d.xDisziplin = w.xDisziplin 
                                        AND s.xWettkampf > 0 
                                        AND rs.xMeeting = " . $_COOKIE['meeting_id'] ."  
                                        AND w.xMeeting = " . $_COOKIE['meeting_id'] ."                                         
										"); 
							if(mysql_errno() > 0) {		// DB error
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}else{ 
								$start_row = mysql_fetch_array($result);
								$starts = $start_row[0];
								mysql_free_result($result);
							}
						}
						
					}else
					{
						$starts = $row[5];
					}
					?>
<td class='monitor'>
	<div class='<?php echo $class; ?>'>
		<a href='<?php echo $href; ?>'>
			<?php echo "&nbsp;$row[4]&nbsp;$row[2]$combGroup"; ?>
		(<?php echo $starts; ?>) <?php echo $row[13]; ?></a>
	</div>
					<?php
					next($cats);
				}

				// next event has same category: linebreak within cell
				else		// same category
				{   
					if(array_key_exists($row[10], $events) == TRUE 		// not first round (count qualified athletes)
						&& $combined == false && $teamsm == false) 	// no combined event, no team sm event
					{
						$starts = "-";
						// get number of athletes/relays with valid result
						$result = mysql_query("
							SELECT
								COUNT(*)
							FROM
								serienstart AS ss
								, serie AS s
							WHERE ss.Qualifikation > 0
							AND s.xSerie = ss.xSerie
							AND s.xRunde =" . $events[$row[10]]
						);

						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
						else {
							$start_row = mysql_fetch_row($result);
							$starts = $start_row[0];
							mysql_free_result($result);
						}
					}elseif($combined || $teamsm){ // for combined rounds, count starts for correct group
						
						if($row[17] == 1){ // if this is a combined last event, every athlete starts
							$starts = $row[5];
						}elseif(empty($row[15])){ // if no group is set
							$starts = $row[5];
						}else{
							$result = mysql_query("SELECT COUNT(*) FROM   
											start as st
											, anmeldung as a
										WHERE	st.xAnmeldung = a.xAnmeldung
										AND	st.xWettkampf = $row[10]
										AND	a.Gruppe = '$row[15]'");
							if(mysql_errno() > 0) {		// DB error
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}else{
								$start_row = mysql_fetch_array($result);
								$starts = $start_row[0];
								mysql_free_result($result);
							}
							$combGroup = "&nbsp;g".$row[15];
						}
						
					}elseif($roundSet > 0){
						
						if($roundSetMain == 0){
							$starts = "m";   
						}else{
							
							$result =mysql_query("SELECT COUNT(*) FROM
                                            rundenset AS rs
                                            , runde AS r
                                            , wettkampf AS w
                                            , disziplin AS d
                                            LEFT JOIN start AS s
                                                ON w.xWettkampf = s.xWettkampf
                                                AND s.Anwesend = 0
                                                AND ((d.Staffellaeufer = 0
                                                    AND s.xAnmeldung > 0)
                                                    OR (d.Staffellaeufer > 0
                                                    AND s.xStaffel > 0))
                                        WHERE
                                            rs.xRundenset = $roundSet
                                        AND    r.xRunde = rs.xRunde
                                        AND    w.xWettkampf = r.xWettkampf
                                        AND    d.xDisziplin = w.xDisziplin 
                                        AND s.xWettkampf > 0 
                                        AND rs.xMeeting = " . $_COOKIE['meeting_id'] ."  
                                        AND w.xMeeting = " . $_COOKIE['meeting_id'] ."   
                                        "); 
							if(mysql_errno() > 0) {		// DB error
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}else{
								$start_row = mysql_fetch_array($result);  
								$starts = $start_row[0];
								mysql_free_result($result);
							}
						}
						
					}
					else {   
						$starts = $row[5];
					}
					?>
	<div class='<?php echo $class; ?>'>
		<a <?php echo $class; ?> href='<?php echo $href; ?>'>
			<?php echo "&nbsp;$row[4]&nbsp;$row[2]$combGroup&nbsp; " ?>
		&nbsp;(<?php echo $starts; ?>) <?php echo $row[13]; ?></a>
	</div>
					<?php
				}

				$events[$row[10]] = $row[0]; // keep last processed round per event
			}	// END while every event round
			mysql_free_result($res);
			?>
	</td>
			<?php
			while(current($cats) == TRUE) {
				?>
	<td />
				<?php
				next($cats);
			}
			?>
</tr>
			<?php
		}		// ET DB error event rounds
		?>
</table>
		<?php
	}		// ET DB timetable item error
}

}		// AA_TIMETABLE_LIB_INCLUDED
?>
