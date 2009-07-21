<?php

/**********
 *
 *	high jump, pole vault results
 *	
 */

if (!defined('AA_RESULTS_HIGH_LIB_INCLUDED'))
{
	define('AA_RESULTS_HIGH_LIB_INCLUDED', 1);

function AA_results_High($round, $layout)
{      
require('./lib/cl_gui_button.lib.php');

require('./config.inc.php');
require('./lib/common.lib.php');
require('./lib/heats.lib.php');
require('./lib/results.lib.php');
require('./lib/utils.lib.php');
require('./lib/cl_performance.lib.php'); 

$presets = AA_results_getPresets($round);	// read GET/POST variables

$performance = 0;		// initialize

$svm = AA_checkSVM(0, $round); // decide whether to show club or team name   

$click = true;           // true = User clicks at this athlete      false = user save athlete before 
//
// update result(s)
//
if($_POST['arg'] == 'save_res')
{   $click = false;
	// check if athlet valid
	if(AA_checkReference("serienstart", "xSerienstart", $_POST['start']) == 0)
	{
		AA_printErrorMsg($strErrAthleteNotInHeat);
	}
	else
	{
		AA_utils_changeRoundStatus($round, $cfgRoundStatus['results_in_progress']);
		if(!empty($GLOBALS['AA_ERROR'])) {
			AA_printErrorMsg($GLOBALS['AA_ERROR']);
		}

		mysql_query("
			LOCK TABLES
				disziplin READ
				, runde READ
				, serienstart READ
				, wettkampf READ
				, resultat WRITE
				, wertungstabelle READ
				, wertungstabelle_punkte READ
                , meeting READ
		");

		// validate result
		$perf = new PerformanceAttempt($_POST['perf']);
		$performance = $perf->getPerformance();

		// validate attempts
		if($performance > 0) {
			$info = strtoupper($_POST['attempts']);
			$info = strtr($info, '0', 'O');
			$info = str_replace("OOO", "O", $info);
			$info = str_replace("OO", "O", $info);
			if(in_array($info, $cfgResultsHigh) == false) {
				$info = NULL;
			}
		}
		else {				// negative or zero result
			$info = $cfgResultsHighOut;
		}
		
		// check on failed attempts (not more than 3 X in a row, it doesent matter on which hights)
		$res = mysql_query("SELECT Leistung, Info FROM 
					resultat
				WHERE
					xSerienstart = ".$_POST['start']."
				ORDER BY
					Leistung ASC");
		$Xcount = 0;
		while($row = mysql_fetch_array($res)){
			if(strpos($row[1], strtoupper("o")) === false){
				preg_match_all("[X]", $row[1], $m);
				$Xcount += count($m[0]);
			}else{
				$Xcount = 0;
			}
		}
		if(strpos($info, strtoupper("o")) === false){ // count X for last entered attempt
			preg_match_all("[X]", $info, $m);
			$Xcount += count($m[0]);
		}else{
			$Xcount = 0;
		}
        
        $prog_mode = AA_results_getProgramMode();                
		
		if($info == $cfgResultsHighOut || $Xcount >= 3) {		// last attempt
            if($cfgProgramMode[$prog_mode]['name'] == $strProgramModeBackoffice) {
               $_POST['athlete'] = $_POST['athlete'] + 1;    // next athlete    
            }   
			
			$points = 0;
		}
		else {
			/*$sql_sex = "SELECT Geschlecht 
						  FROM athlet 
					 LEFT JOIN anmeldung USING(xAthlet) 
					 LEFT JOIN start USING(xAnmeldung) 
					 LEFT JOIN serienstart USING(xStart) 
						 WHERE xSerienstart = ".$_POST['start'].";";*/
			$sql_sex = "SELECT Geschlecht 
						  FROM kategorie 
					 LEFT JOIN wettkampf USING(xKategorie) 
					 LEFT JOIN start USING(xWettkampf) 
					 LEFT JOIN serienstart USING(xStart) 
						 WHERE xSerienstart = ".$_POST['start'].";";
			$query_sex = mysql_query($sql_sex);
			
			if($_POST['attempts']== '-' ){
                $points=0;
            }
            else{
                $points = AA_utils_calcPoints($presets['event'], $performance, 0, mysql_result($query_sex, 0, 'Geschlecht'));
		    }
           
        }

		AA_results_update($performance, $info, $points);
	}	// ET Athlete valid
	mysql_query("UNLOCK TABLES");
}

//
// delete technical result
//
else if($_GET['arg'] == 'delete')
{
	AA_results_delete($round, $_GET['item']);
}

//
// terminate result processing
//
else if($_GET['arg'] == 'results_done')
{
	$eval = AA_results_getEvaluationType($round);
	$combined = AA_checkCombined(0, $round);
	
	mysql_query("DROP TABLE IF EXISTS tempresult");	// temporary table

	if(mysql_errno() > 0) {		// DB error
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else
	{
		mysql_query("
			LOCK TABLES
				serie READ
				, wettkampf READ
				, resultat WRITE
				, serienstart WRITE
				, tempresult WRITE
		");
		
		// clean ranks, set all to 0
		mysql_query("UPDATE 
				serienstart
				, serie
			SET
				serienstart.Rang = 0
			WHERE
				serienstart.xSerie = serie.xSerie
			AND	serie.xRunde = $round");
		
		// Set up a temporary table to hold all results for ranking.
		mysql_query("
			CREATE TABLE tempresult (
				xSerienstart int(11)
				, xSerie int(11)
				, Leistung int(9)
				, TopX int(1)
				, TotalX int(2)
				)
			TYPE=HEAP
		");
		
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			// if this is a combined event, rank all rounds togheter
			$roundSQL = "";
			if($combined){
				$roundSQL = "AND serie.xRunde IN (";
				$res_c = mysql_query("SELECT xRunde FROM runde WHERE xWettkampf = ".$presets['event']);
				while($row_c = mysql_fetch_array($res_c)){
					$roundSQL .= $row_c[0].",";
				}
				$roundSQL = substr($roundSQL,0,-1).")";
			}else{
				$roundSQL = "AND serie.xRunde = $round";
			}
			
			// read all valid results (per athlet)
			$result = mysql_query("
				SELECT
					resultat.Leistung
					, resultat.Info
					, serienstart.xSerienstart
					, serienstart.xSerie
				FROM
					resultat
					, serienstart
					, serie
				WHERE resultat.xSerienstart = serienstart.xSerienstart
				AND serienstart.xSerie = serie.xSerie
				$roundSQL
				AND resultat.Leistung != 0
				ORDER BY
					serienstart.xSerienstart
					,resultat.Leistung DESC
			");
            
			if(mysql_errno() > 0)		// DB error
			{
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else
			{
				// initialize variables
				$leistung = 0;		
				$serienstart = 0;
				$serie = 0;
				$topX = 0;
				$totX = 0;

				$ss = 0;		// athlete's ID
				$tt = FALSE;	// top result check

				// process every result
				while($row = mysql_fetch_row($result))
				{  
					// new athlete: save last athlete's data
					if(($ss != $row[2]) && ($ss != 0))
					{

						if($leistung != 0)
						{
							// add one row per athlete to temp table
							mysql_query("
								INSERT INTO tempresult
								VALUES(
									$serienstart
									, $serie
									, $leistung
									, $topX
									, $totX)
							");

							if(mysql_errno() > 0) {		// DB error
								AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
							}
						}
						// initialize variables
						$leistung = 0;		
						$serienstart = 0;
						$serie = 0;
						$totX = 0;
						$topX = 0;

						$tt = FALSE;
					}

					// save data of current athlete's top result
					if(($tt == FALSE) && (strstr($row[1], 'O')))
					{
						$leistung = $row[0];		
						$serienstart = $row[2];
						$serie = $row[3];
						$topX = substr_count($row[1], 'X');                         
						$tt = TRUE;
					}

					// count total invalid attempts
					$totX = $totX + substr_count($row[1], 'X');                     
					$ss = $row[2];				// keep athlete's ID
				}
				mysql_free_result($result);

				// insert last pending data in temp table
				if(($ss != 0) && ($leistung != 0)) {
					mysql_query("
						INSERT INTO tempresult
						VALUES(
							$serienstart
							, $serie
							, $leistung
							, $topX
							, $totX)
					");
                      
					if(mysql_errno() > 0) {		// DB error
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
				}
			}

			if($eval == $cfgEvalType[$strEvalTypeHeat]) {	// eval per heat
				$order = "xSerie ,";
			}
			else {	// default: rank results from all heats together
				$order = "";
			}

			// Read rows from temporary table ordered by performance,
			// nbr of invalid attempts for top performance and
			// total nbr of invalid attempts to determine ranking.
			$result = mysql_query("
				SELECT
					xSerienstart
					, xSerie
					, Leistung
					, TopX
					, TotalX
				FROM
					tempresult
				ORDER BY
					$order
					Leistung DESC
					,TopX ASC
					,TotalX ASC
			");

			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
			else {
				// initialize variables
				$heat = 0;
				$perf = 0;
				$topX = 0;
				$totalX = 0;
				$i = 0;
				$rank = 0;
				// set rank for every athlete
				while($row = mysql_fetch_row($result))
				{
					if(($eval == $cfgEvalType[$strEvalTypeHeat])	// new heat
						&&($heat != $row[1]))
					{
						$i = 0;		// restart ranking
						$perf = 0;
						$topX = 0;
						$totalX = 0;
					}

					$j++;								// increment ranking
					if($perf != $row[2] || $topX != $row[3] || $totalX != $row[4])
					{
						$rank = $j;	// next rank (only if not same performance)
					}

					mysql_query("
						UPDATE serienstart SET
							Rang = $rank
						WHERE xSerienstart = $row[0]
					");

					if(mysql_errno() > 0) {
						AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
					}
					$heat = $row[1];		// keep current heat ID
					$perf = $row[2];
					$topX = $row[3];
					$totalX = $row[4];
				}
				mysql_free_result($result);
			}

			mysql_query("DROP TABLE IF EXISTS tempresult");

			if(mysql_errno() > 0) {		// DB error
				AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
			}
		}	// ET DB error (create temp table)

		// read all starting athletes with no valid result (rank=0)
		// and add disqualification code
		$result = mysql_query("
			SELECT DISTINCT
				serienstart.xSerienstart
			FROM
				resultat
				, serienstart
				, serie
			WHERE resultat.xSerienstart = serienstart.xSerienstart
			AND serienstart.xSerie = serie.xSerie
			AND serienstart.Rang = 0
			AND serie.xRunde = $round
			AND resultat.Leistung >= 0
		");

		if(mysql_errno() > 0)		// DB error
		{
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			// 
			while($row = mysql_fetch_row($result))
			{
				// check if "disqualified" result already there
				$res = mysql_query("
					SELECT
						xResultat
					FROM
						resultat
					WHERE xSerienstart = $row[0]
					AND (Leistung = ". $cfgInvalidResult['DSQ']['code']."OR Leistung = ". $cfgInvalidResult['NRS']['code'] .")" 
				);

				if(mysql_errno() > 0) {		// DB error
					AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
				}
				else {
					if(mysql_num_rows($res) <= 0)
					{
						mysql_query("
							INSERT INTO
								resultat
							SET
								Leistung = ". $cfgInvalidResult['NRS']['code']."
								, Info = '$cfgResultsHighOut'
								, xSerienstart = $row[0]
						");

						if(mysql_errno() > 0) {		// DB error
							AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
						}
					}
					mysql_free_result($res);
				}
			}
			mysql_free_result($result);
		}

		mysql_query("UNLOCK TABLES");
	}	// ET DB error (drop temp table)

	AA_results_setNotStarted($round);	// update athletes with no result

	AA_utils_changeRoundStatus($round, $cfgRoundStatus['results_done']);
	if(!empty($GLOBALS['AA_ERROR'])) {
		AA_printErrorMsg($GLOBALS['AA_ERROR']);
	}
}

//
// calculate ranking points if needed
//
if(($_GET['arg'] == 'results_done')
|| ($_POST['arg'] == 'save_rank')){
	
	AA_utils_calcRankingPoints($round);
	
}
if ($_POST['arg'] == 'save_remark') {
    
    AA_utils_saveRemark($_POST['item'], $_POST['remark'], $_POST['xAthlete']);
}


//
// print HTML page header
//
AA_results_printHeader($presets['category'], $presets['event'], $round);

$mergedMain=AA_checkMainRound($round);
if ($mergedMain != 1) {

// read round data
if($round > 0)
{
	$status = AA_getRoundStatus($round);

	// No action yet
	if(($status == $cfgRoundStatus['open'])
		|| ($status == $cfgRoundStatus['enrolement_done'])
		|| ($status == $cfgRoundStatus['heats_in_progress']))
	{
		AA_printWarningMsg($strHeatsNotDone);
	}
	// Enrolement pending
	else if($status == $cfgRoundStatus['enrolement_pending'])
	{
		AA_printWarningMsg($strEnrolementNotDone);
	}
	// Heat seeding completed, ready to enter results
	else if($status >= $cfgRoundStatus['heats_done'])
	{
		AA_heats_printNewStart($presets['event'], $round, "event_results.php");

		// display all athletes
		$result = mysql_query("
			SELECT rt.Name
				, rt.Typ
				, s.xSerie
				, s.Bezeichnung
				, ss.xSerienstart
				, ss.Position
				, ss.Rang
				, a.Startnummer
				, at.Name
				, at.Vorname
				, at.Jahrgang
				, if('".$svm."', t.Name, IF(a.Vereinsinfo = '', v.Name, a.Vereinsinfo))   
				, LPAD(s.Bezeichnung,5,'0') as heatid
				, rs.xResultat
				, rs.Leistung
				, rs.Info
				, at.Land
                , ss.Bemerkung
                , at.xAthlet
			FROM
				runde AS r
				, serie AS s
				, serienstart AS ss
				, start AS st
				, anmeldung AS a
				, athlet AS at
				, verein AS v
            LEFT JOIN team AS t ON(a.xTeam = t.xTeam) 
			LEFT JOIN rundentyp AS rt
				ON rt.xRundentyp = r.xRundentyp
			LEFT JOIN resultat AS rs
				ON rs.xSerienstart = ss.xSerienstart
			WHERE r.xRunde = $round
			AND s.xRunde = r.xRunde
			AND ss.xSerie = s.xSerie
			AND st.xStart = ss.xStart
			AND a.xAnmeldung = st.xAnmeldung
			AND at.xAthlet = a.xAthlet
			AND v.xVerein = at.xVerein
			ORDER BY
				heatid
				, ss.Position
				, rs.xResultat DESC
		");
       
		if(mysql_errno() > 0) {		// DB error
			AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
		}
		else
		{
			AA_results_printMenu($round, $status);

			// initialize variables
			$a = 0;
			$h = 0;
			$i = 0;
			if(!empty($_GET['athlete'])) {	
				$_POST['athlete'] = $_GET['athlete'];
			}
			if((empty($_POST['athlete']))			// no athlete selected or after
				|| (mysql_num_rows($result) < $_POST['athlete'])) // last athlete
			{
				$_POST['athlete'] = 1;			// focus to first athlete
			}
			$rowclass = 'odd';

			$prog_mode = AA_results_getProgramMode();
			if($cfgProgramMode[$prog_mode]['name'] == $strProgramModeBackoffice)
			{
				$focus = 0;		// keep focus on this athlete if Backoffice Mode
			}
			else {
				$focus = 1;		// focus on next athlete if Field Mode
			}
?>
<p/>

<table class='dialog'>
<?php
            



			$btn = new GUI_Button('', '');	// create button object
			while($row = mysql_fetch_row($result))
			{   
				// terminate last row if new athlete and not first item
				if(($a != $row[4]) && ($i != 0))
				{
                   if($_POST['athlete'] == ($i+1) && $cfgProgramMode[$prog_mode]['name'] == $strProgramModeField) {
                        if ($row[15] == 'XXX' && !$click){
                            $_POST['athlete'] = $_POST['athlete'] + 1;
                        }
                    }        
                     
					if($_POST['athlete'] == $i)		// active item
					{  
						echo "<td>";
						$btn->set("event_results.php?arg=del_start&item=$a&round=$round", $strDelete);
						$btn->printButton();
						echo "</td>";
					}
					echo "</tr>";
				}
/*
 *  Heat headerline
 */   
				if($h != $row[2])		// new heat
				{
					$h = $row[2];				// keep heat ID
					if(is_null($row[0])) {		// only one round

						$title = "$strFinalround";
					}
					else {		// more than one round
						$title = "$row[0]";
					}

					$c = 0;
					if($status == $cfgRoundStatus['results_done']) {
						$c = 1;		// increment colspan to include ranking
					}
?>
	<tr>
		<form action='event_results.php#heat_<?php echo $row[3]; ?>' method='post'
			name='heat_id_<?php echo $h; ?>'>

		<th class='dialog' colspan='<?php echo 7+$c; ?>' />
			<?php echo $title; ?>
			<input type='hidden' name='arg' value='change_heat_name' />
			<input type='hidden' name='round' value='<?php  echo $round; ?>' />
			<input type='hidden' name='item' value='<?php echo $row[2]; ?>' />
			<input class='nbr' type='text' name='id' maxlength='2'
				value='<?php echo $row[3]; ?>'
				onChange='document.heat_id_<?php echo $h;?>.submit()' />
				<a name='heat_<?php echo $row[3]; ?>' />
		</th>
		</form>
	</tr>  
	<tr>
		<th class='dialog'><?php echo $strPositionShort; ?></th>
		<th class='dialog' colspan='2'><?php echo $strAthlete; ?></th>
		<th class='dialog'><?php echo $strYearShort; ?></th>
		<th class='dialog'><?php echo $strCountry; ?></th>
		<th class='dialog'><?php if($svm){ echo $strTeam; }else{ echo $strClub;} ?></th>
<?php
					if($status == $cfgRoundStatus['results_done'])
					{
?>
		<th class='dialog'><?php echo $strRank; ?></th>
<?php
					}
?>
		<th class='dialog' ><?php echo $strResultRemark; ?></th>
        <th class='dialog' colspan='2'><?php echo $strPerformance; ?></th>
          
	</tr>
<?php
				}		// ET new heat

/*
 * Athlete data lines
 */  
				if($a != $row[4])		// new athlete
				{
					$a = $row[4];		// keep athlete ID
					$i++;					// increment athlete counter
					$l = 0;				// reset result counter
					if($_POST['athlete'] == $i) {	// active item
						$rowclass='active';
					}
					else if($row[5] % 2 == 0) {		// even row numer
						$rowclass='even';
					}
					else {							// odd row number
						$rowclass='odd';
					}

					if($rowclass == 'active') {
?>
	<tr class='active'>
<?php
					}
					else {   
?>
	<tr class='<?php echo $rowclass; ?>'
		onclick='selectAthlete(<?php echo $i; ?>)'>
<?php
					}  
?>
		<td class='forms_right'><?php echo $row[5]; ?></td>
		<td class='forms_right'><?php echo $row[7]; /* start nbr */ ?></td>
		<td nowrap><?php echo $row[8] . " " . $row[9];  /* name */ ?></td>
		<td class='forms_ctr'><?php echo AA_formatYearOfBirth($row[10]); ?></td>
		<td><?=(($row[16]!='' && $row[16]!='-') ? $row[16] : '&nbsp;')?></td>
		<td nowrap><?php echo $row[11]; /* club */ ?></td>
   
<?php
   
					if($status == $cfgRoundStatus['results_done'])
					{
						if($_POST['athlete'] == $i)	// only current athlet
						{
?>
		<form action='event_results.php' method='post'
			name='rank'>
		<td>
			<input type='hidden' name='arg' value='save_rank' />
			<input type='hidden' name='round' value='<?php echo $round; ?>' />
			<input type='hidden' name='athlete' value='<?php echo $i+$focus; ?>' />
			<input type='hidden' name='item' value='<?php echo $row[4]; ?>' />
			<input class='nbr' type='text' name='rank' maxlength='3'
				value='<?php echo $row[6]; ?>' onChange='document.rank.submit()' />
		</td>
		</form>
<?php
						}
						else {
							echo "<td>" . $row[6] . "</td>";
                            
						}
                        echo "<td>" . $row[17] . "</td>";  
					}		// ET results done
                    else {
                         ?> 
                        

<form action='event_results.php' method='post'
            name='remark_<?php echo $i; ?>'>
        <td>
            <input type='hidden' name='arg' value='save_remark' />
            <input type='hidden' name='round' value='<?php echo $round; ?>' />
            <input type='hidden' name='athlete' value='<?php echo $i+$focus; ?>' />
            <input type='hidden' name='item' value='<?php echo $row[4]; ?>' />
            <input type='hidden' name='xAthlete' value='<?php echo $row[18]; ?>' />    
            <input class='textshort' type='text' name='remark' maxlength='5'
                value='<?php echo $row[17]; ?>' onChange='document.remark_<?php echo $i; ?>.submit()' />
        </td>
        </form>  
       
  
      
                   <?php  
                     
                    }
                
				}		// ET new athlete
               

				$new_perf = '';
				if($_POST['athlete'] == $i)				// only current athlet
				{
					if(is_null($row[14])) {		// no result yet: show form
						$last_perf = 0;
					}
					else {
						$last_perf = $row[14];
					}

					$item = '';
					if($l == 0)								// first item
					{
						// read all performances achieved in current heat and
						// better than last entered performance
                    if ($cfgProgramMode[$prog_mode]['name'] == $strProgramModeField) {
                        if(in_array($row[15], $cfgResultsHighStayDecentral)) {
                            $new_perf = AA_formatResultMeter($last_perf);
                            $new_info = $row[15];
                            $item = $row[13];
                        }
                        else 
                        {
                            $new_perf = getNextHeight($row[2], $last_perf);
                            $new_info = '';
                        } 
                        
                    }
                    else {
						if(in_array($row[15], $cfgResultsHighStay)) {
							$new_perf = AA_formatResultMeter($last_perf);
							$new_info = $row[15];
							$item = $row[13];
						}
						else 
                        {
							$new_perf = getNextHeight($row[2], $last_perf);
							$new_info = '';
						} 
                    }  
?>
		<form action='event_results.php' method='post'
			name='perf'>
		<td nowrap colspan='2'> 
			<input type='hidden' name='arg' value='save_res' />
			<input type='hidden' name='round' value='<?php echo $round; ?>' />
			<input type='hidden' name='athlete' value='<?php echo $i+$focus; ?>' />
			<input type='hidden' name='start' value='<?php echo $row[4]; ?>' />
			<input type='hidden' name='item' value='<?php echo $item; ?>' />
			<input class='perfheight' type='text' name='perf' maxlength='5'
				value='<?php echo $new_perf; ?>'
					onChange='checkSubmit(document.perf)' />
			<input class='texttiny' type='text' name='attempts' maxlength='3'
				value='<?php echo $new_info; ?>'
					onChange='document.perf.submit()' onBlur='document.perf.submit()' />
		</td>
		</form>   
        
        
						<?php
					}

					if((is_null($row[14]) == false)	// result to display
						&& (empty($item))) {				// next height                         
						?>
		<td nowrap>
			<?php echo AA_formatResultMeter($row[14]) . "<br/>( $row[15] )"; ?>
		</td>
		<td>
						<?php
						$btn = new GUI_Button("event_results.php?arg=delete&round=$round&item=$row[13]&athlete=$i", "X");
						$btn->printButton();
						?>
		</td>
						<?php
					}
					$l++;
				}
				else if (is_null($row[14]) == false) // result entered
				{
					echo "<td colspan='2' nowrap>" . AA_formatResultMeter($row[14])
						. " ( $row[15] )</td>";
				}
              
           
            
			}
            
          
			if($a != 0)
			{
				if($_POST['athlete'] == $i)		// active item
				{
					echo "<td>";
					$btn->set("event_results.php?arg=del_start&item=$a&round=$round", $strDelete);
					$btn->printButton();
					echo "</td>";
				}
				echo "</tr>";
			}
			?>
           
            
            
            
</table>
  
                 
			<?php
			mysql_free_result($result);
		}		// ET DB error
	}
}		// ET round selected
?>

<script type="text/javascript">
<!--
	if(document.rank) {
		document.rank.rank.focus();
		document.rank.rank.select();
		window.scrollBy(0,100);
	}
	else if(document.perf) {
		document.perf.perf.focus();
		document.perf.perf.select();
		window.scrollBy(0,100);
	}
//-->
</script>

</body>
</html>

<?php
}
else
{
        AA_printErrorMsg($strErrMergedRound); 
    } 
}	// end function AA_results_High



function getNextHeight($heat, $curr_perf)
{
	require('./lib/common.lib.php');

	$result = mysql_query("
		SELECT DISTINCT
			r.Leistung
		FROM
			resultat AS r
			, serienstart AS ss
		WHERE r.xSerienstart = ss.xSerienstart
		AND ss.xSerie = $heat
		AND r.Leistung > $curr_perf
		ORDER BY
			r.Leistung ASC
	");
    
	if(mysql_errno() > 0) {		// DB error
		AA_printErrorMsg(mysql_errno() . ": " . mysql_error());
	}
	else {
		$row = mysql_fetch_row($result);
		$new_perf = AA_formatResultMeter($row[0]);
		mysql_free_result($result);
	}

	return $new_perf;
}	// end function getNextHeight 


}	// AA_RESULTS_HIGH_LIB_INCLUDED
?>
