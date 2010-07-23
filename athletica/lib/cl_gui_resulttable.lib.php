<?php

if (!defined('AA_CL_GUI_RESULTTABLE_LIB_INCLUDED'))
{
	define('AA_CL_GUI_RESULTTABLE_LIB_INCLUDED', 1);


/********************************************
 *
 * CLASS GUI_ResultTable
 *
 * Prints an HTML result table.
 *
 *******************************************/

class GUI_ResultTable
{
	var $layout;
	var $next;
	var $round;
	var $rowclass;
	var $spanincr;
	var $status;

	/*
	 * Constructor
	 *		- round		DB primary key
	 *		- layout		according to cfgDisciplineType
	 *		- status		round status
	 *		- next		next round, if any
	 */
	function GUI_ResultTable($round, $layout, $status, $next=0)
	{
		$this->layout = $layout;
		$this->status = $status;
		$this->round = $round;
		$this->next = $next;
		$this->rowclass = array('even', 'odd');
		$this->spanincr = 0;
		// adjust last column depending on round status
		if($this->status == $GLOBALS['cfgRoundStatus']['results_done'])
		{
			$this->spanincr++;
			if($next > 0) {
				$this->spanincr++;
			}
		}
		?>
<table class='dialog'>
		<?php
	}


	function endTable()
	{
		?>
</table>
		<?php
	}


	/**
	 *	switch row's CSS-class 
	 */
	function switchRowClass()
	{
		$this->rowclass=array_reverse($this->rowclass);	// switch rowclass
	}

} // END CLASS Gui_ResultTable



/********************************************
 *
 * CLASS GUI_TrackResultTable
 *
 * Prints an HTML table for track results.
 *
 *******************************************/

class GUI_TrackResultTable extends GUI_ResultTable
{
	/*
	 * Heat title line
	 *		- id			DB primary key
	 *		- scroll		scroll ID
	 *		- title		heat name
	 *		- status		heat status
	 *		- film		film nbr, if any
	 *		- wind		wind, if any
	 */
     
function printHeatTitleRegie($cat, $disc)
    {
        ?>
    <tr>
        <th class='dialog_title' colspan='2'><?php echo $cat; ?>             
        </th>
         <th class='dialog_title' colspan='6'><?php echo $disc; ?>                          
        </th>          
    </tr>
        <?php
    }

	function printHeatTitle($id, $scroll, $title, $status, $film='', $wind='', $arg='', $relay=false)
	{
		?>
	<tr>
		<th class='dialog' colspan='3'>
			<a name='heat_<?php echo $scroll; ?>' /><?php echo $title; ?></a>
		</th>
		<th class='dialog' colspan='2'><?php echo $GLOBALS['strFilm'] . " " . $film; ?></th>
		<?php
		// track discipline with wind
		
		if($this->status >= $GLOBALS['cfgRoundStatus']['results_in_progress']){
			$span_announced = -1;
		} else {
			$span_announced = 0;
		}
		
		if($this->layout == $GLOBALS['cfgDisciplineType'][$GLOBALS['strDiscTypeTrack']])
		{
			?>
		<th class='dialog' colspan='2'>
			<?php echo  $GLOBALS['strWind'] . " " . $wind; ?>
		</th>
			<?php
		}
		else	// no wind
		{
            if ($arg != 'regie'){
                
                ?> 
                <th class='dialog' colspan='<?php echo 2+$this->spanincr + $span_announced; ?>' />
                <?php
            }
            elseif (!$relay) {
                 ?>  
                   <th class='dialog'></th>
                    <?php  
            }
			
		}	// ET track discipline with wind
        if ($arg != 'regie') {
		    if($this->status >= $GLOBALS['cfgRoundStatus']['results_in_progress'])
		    {
			    if($status == $GLOBALS['cfgHeatStatus']['announced']) {
				    $checked = 'checked';
			    }
			    else {
				    $checked = '';
			    }

			    ?>
		    <form action='controller.php' method='post'
			    name='resstat_<?php echo $scroll; ?>' target='controller'>
		    <th class='dialog'>
			    <?php echo $GLOBALS['strResultsAnnounced']; ?>
			    <input type='hidden' name='act' value='saveHeatStatus' />
			    <input type='hidden' name='round' value='<?php echo $this->round; ?>' />
			    <input type='hidden' name='item' value='<?php echo $id; ?>' />
			    <input type='checkbox' name='status' <?php echo $checked;?>
				    onClick="document.resstat_<?php echo $scroll; ?>.submit()" />
		    </th>
		    </form>
			    <?php
		    } 
        }
		?>
	</tr>
		<?php
	}


	function printAthleteHeader($arg='')
	{
		?>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strPositionShort']; ?></th>
		<th class='dialog' colspan='2'><?php echo $GLOBALS['strAthlete']; ?></th>
        <?php
        if ($arg != 'regie'){
            ?>
            <th class='dialog'><?php echo $GLOBALS['strYearShort']; ?></th>
            <th class='dialog'><?php echo $GLOBALS['strCountry']; ?></th>
            <th class='dialog'><?php echo $GLOBALS['strClub']; ?></th>
           <?php 
        }
        ?>		
		<th class='dialog'><?php echo $GLOBALS['strTopPerformance']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strPerformance']; ?></th>
		<?php
		// show rank if all results entered
		if($this->status == $GLOBALS['cfgRoundStatus']['results_done'] )
		{
			?>
		<th class='dialog'><?php echo $GLOBALS['strRank']; ?></th>
			<?php
			// show qualification info if another round follows
			if($this->next > 0) {
				?>
		<th class='dialog'><?php echo $GLOBALS['strQualification']; ?></th>
				<?php
			}
		}
        elseif ($arg == 'regie'){
             ?>
              <th class='dialog'><?php echo $GLOBALS['strRank']; ?></th>
            <?php
        }
		?>
	</tr>
		<?php
	}


	function printRelayHeader($arg='')
	{
		?>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strPositionShort']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strRelay']; ?></th>     
		<th class='dialog'><?php echo $GLOBALS['strClub']; ?></th> 
		<th class='dialog'><?php echo $GLOBALS['strPerformance']; ?></th>
		<?php
		// show rank if all results entered
		if($this->status == $GLOBALS['cfgRoundStatus']['results_done'])
		{
			?>
		<th class='dialog'><?php echo $GLOBALS['strRank']; ?></th>
			<?php
			// show qualification info if another round follows
			if($this->next > 0) {
				?>
		<th class='dialog'><?php echo $GLOBALS['strQualification']; ?></th>
				<?php
			}
		}
        elseif ($arg == 'regie') {
              ?>
        <th class='dialog'><?php echo $GLOBALS['strRank']; ?></th>
            <?php
        }
		?>
	</tr>
		<?php
	}

	/*
	 * Athlete data line
	 *		- pos		roster position, e.g. track
	 *		- nbr		start nbr
	 *		- name	athlete's name (preformatted)
	 *		- year	year of birth
	 *		- club	
	 *		- perf	performance (preformatted)	
	 *		- rank	rank, if any
	 *		- qual	qualification info, if any
	 */
	function printAthleteLine($pos, $nbr, $name, $year, $club, $topperf, $perf, $rank=0, $qual=0, $country="", $athletID, $arg='',  $rank_regie=0)
	{
?>
	<tr class='<?php echo $this->rowclass[0]; ?>' onClick='window.open("speaker_entry.php?item=<?php echo $athletID; ?>", "_self")' style="cursor: pointer;">  	
        <td class='forms_right'><?php echo $pos; ?></td>
		<td class='forms_right'><?php echo $nbr; ?></td>
		<td><?php echo $name; ?></td>
        <?php 
        if ($arg != 'regie'){
            ?>
            <td class='forms_ctr'><?php echo $year; ?></td>
            <td class='forms_ctr'><?=(($country!='' && $country!='-') ? $country : '&nbsp;')?></td>
            <td><?php echo $club; ?></td>
            <?php
        }
		 ?>
		<td><?php echo $topperf; ?></td>
		<td class='forms_right'><b><?php echo $perf; ?></b></td>
<?php
		// show rank if all results entered
		if($this->status == $GLOBALS['cfgRoundStatus']['results_done'])
		{
			if($rank > 0)	// rank OK, athlete has valid result
			{
				?>
		<td class='forms_right'><?php echo $rank; ?></td>
				<?php
				// show qualification info if another round follows
				if($this->next > 0)
				{
					$qtext = '';
					if($qual > 0)
					{	// Athlete qualified
						foreach($GLOBALS['cfgQualificationType'] as $qtype)
						{
							if($qtype['code'] == $qual) {
								$qtext = $qtype['text'];
							}
						}
					}	// ET athlete qualified
					?>
		<td><?php echo $qtext; ?></td>
					<?php
				}	// qualification info
			}
			else
			{	// no rank
				?>
		<td />
				<?php
				if($this->next> 0)
				{
					?>
		<td />
					<?php
				}
			}	// ET valid rank
		}	// ET 'results_done'
        elseif ($arg == 'regie'){
                if ($rank_regie == 0){
                    $rank_regie = '';
                }
            ?>
                <td class='forms_right'><?php echo $rank_regie; ?></td>   
            <?php
        }
		?>
	</tr>
		<?php
		$this->switchRowClass();
	}


	/*
	 * Relay data line
	 *		- pos		roster position, e.g. track
	 *		- name	relay name
	 *		- club	
	 *		- perf	performance (preformatted)	
	 *		- rank	rank, if any
	 *		- qual	qualification info, if any
	 *		- athl  array with athletes
	 */
	function printRelayLine($pos, $name, $club, $perf, $rank=0, $qual=0, $athl=0, $arg = '', $rank_regie=0)
	{
    $tds = 3;
    if ($arg == 'regie'){
         $tds++;
    }
	
   
?>
	<tr class='<?php echo $this->rowclass[0]; ?>'>    
		<td class='forms_right'><?php echo $pos; ?></td>
		<td><?php echo $name; ?></td>
		<td><?php echo $club; ?></td>
		<td class='forms_right'><?php echo $perf; ?></td>
<?php
		if($this->status == $GLOBALS['cfgRoundStatus']['results_done'])
		{
			if($rank > 0)	// rank OK, athlete has valid result
			{
				$tds++;
				?>
		<td><?php echo $rank; ?></td>
				<?php
				if($this->next > 0)
				{
					$tds++;
					$qtext = '';
					if($qual > 0)
					{	// Athlete qualified
						foreach($GLOBALS['cfgQualificationType'] as $qtype)
						{
							if($qtype['code'] == $qual) {
								$qtext = $qtype['text'];
							}
						}
					}	// ET athlete qualified
					?>
		<td><?php echo $qtext; ?></td>
					<?php
				}	// qualification info
			}
			else
			{	// no rank
				$tds++;
				?>
		<td />
				<?php
				if($this->next> 0)
				{
					?>
		<td />
					<?php
				}
			}	// ET valid rank
		}	// ET 'results_done'
        elseif ($arg == 'regie'){ 
                if ($rank_regie == 0){
                    $rank_regie = '';
                }
            ?>
             <td class='forms_intend'><?php echo $rank_regie; ?></td>  
             <?php
        }
		?>
	</tr>
		<?php
		if(is_array($athl) && count($athl)>0){
			$strAthl = '';
			for($a=0; $a<count($athl); $a++){
				$actAthl = $athl[$a];
				$strAct = $actAthl[0].' '.$actAthl[1].' '.$actAthl[2];
				$strAthl .= $strAct;
				$strAthl .= ($a<(count($athl)-1)) ? ' / ' : '';
			}
			$strAthl = '( '.$strAthl.' )';
			?>
			<tr class='<?php echo $this->rowclass[0]; ?>'>
				<td class='forms_right'>&nbsp;</td>
				<td colspan="<?=$tds?>"><?=$strAthl?></td>
			</tr>
			<?php
		}
		$this->switchRowClass();
	}


	/**
	 * print empty tracks
	 * 	- position: heat position
	 * 	- last: up to this position
	 * 	- span: column span
	 *
	 * returns next position
	 */
	function printEmptyTracks($position, $last, $span)
	{
		while($position <= $last)
		{
			?>
		<tr class='<?php echo $this->rowclass[0]; ?>'>
			<td class='forms_right'><?php echo $position; ?></td>
			<td colspan='<?php echo $span+1; ?>'><?php echo $GLOBALS['strEmpty']; ?>
				</td>
		</tr>
			<?php
			$position++;
			$this->switchRowClass();
		}
		return $position;
	}


} // END CLASS Gui_TrackResultTable


/********************************************
 *
 * CLASS GUI_TechResultTable
 *
 * Prints an HTML table for all technical results (excl.. high jump
 * and pole vault).
 *
 *******************************************/

class GUI_TechResultTable extends GUI_ResultTable
{
	/*
	 * Heat title line
	 *		- id			DB primary key
	 *		- scroll		scroll ID
	 *		- title		heat name
	 *		- status		heat status
	 */
	
     function printHeatTitleRegie($cat, $disc)
     {
        ?>
         <tr>
        <th class='dialog_title' colspan='2'><?php echo $cat; ?>             
        </th>
         <th class='dialog_title' colspan='6'><?php echo $disc; ?>                          
        </th>
        </tr> 
        <?php
     }

	function printHeatTitle($id, $scroll, $title, $status, $arg='')
	{
		?>
	<tr>
		<th class='dialog' colspan='<?php if ($arg == 'regie'){ echo 4 + $this->spanincr;} else {echo 7 + $this->spanincr;} ?>'>
			<a name='heat_<?php echo $scroll; ?>' /><?php echo $title; ?></a>
		</th>
		<?php
       if ($arg != 'regie'){
		    if($this->status >= $GLOBALS['cfgRoundStatus']['results_in_progress'])
		    {
			    if($status == $GLOBALS['cfgHeatStatus']['announced']) {
				    $checked = 'checked';
			    }
			    else {
				    $checked = '';
			    }

			    ?>
		    <form action='controller.php' method='post'
			    name='resstat_<?php echo $scroll; ?>' target='controller'>
		    <th class='dialog' colspan='6'>
			    <?php echo $GLOBALS['strResultsAnnounced']; ?>
			    <input type='hidden' name='act' value='saveHeatStatus' />
			    <input type='hidden' name='round' value='<?php echo $this->round; ?>' />
			    <input type='hidden' name='item' value='<?php echo $id; ?>' />
			    <input type='checkbox' name='status' <?php echo $checked;?>
				    onClick="document.resstat_<?php echo $scroll; ?>.submit()" />
		    </th>
		    </form>
			    <?php
		    }  
       }
       else {
           ?>
           <th class='dialog' colspan='2'></th>
           <?php
       }
		?>
	</tr>
		<?php
	}


	function printAthleteHeader($argT='', $round=0)
	{
	if(!empty($_GET['round'])){
	$round = $_GET['round'];
}
else if(!empty($_POST['round'])) {
	$round = $_POST['round'];
}
    if ($argT == 'regie'){
           $arg = (isset($_GET['arg1'])) ? $_GET['arg1'] : ((isset($_COOKIE['sort_speakres'])) ? $_COOKIE['sort_speakres'] : 'pos');
setcookie('sort_speakres', $arg1, time()+2419200);
    }
    else {
         $arg = (isset($_GET['arg'])) ? $_GET['arg'] : ((isset($_COOKIE['sort_speakres'])) ? $_COOKIE['sort_speakres'] : 'pos');
setcookie('sort_speakres', $arg, time()+2419200);
    }
	
// sort argument
	$img_nbr="img/sort_inact.gif";
	$img_pos="img/sort_inact.gif";
	$img_name="img/sort_inact.gif";
	$img_club="img/sort_inact.gif";
	$img_perf="img/sort_inact.gif";
	$img_rang="img/sort_inact.gif";

	if ($arg=="nbr" && !$relay) {        
		$argument="a.Startnummer";
		$img_nbr="img/sort_act.gif";
	} else if ($arg=="pos") {
		$argument="ss.Position";
		$img_pos="img/sort_act.gif";
	} else if ($arg=="name") {
		$argument="at.Name, at.Vorname";
		$img_name="img/sort_act.gif";
	} else if ($arg=="club") {
		$argument="v.Name, a.Startnummer";
		$img_club="img/sort_act.gif";
	} else if ($arg=="perf") {
		$argument="st.Bestleistung, ss.Position";
		$img_perf="img/sort_act.gif";
	} else if ($arg=="rang") {
		$argument="t.rang, ss.Position";
		$img_rang="img/sort_act.gif";
	} else if($relay == FALSE) {		// single event
		$argument="ss.Position";
		$img_pos="img/sort_act.gif";
	}
		?>
	<tr>
		<th class='dialog'>
        <?php 
        if ($argT == 'regie'){
            ?>
             <a href='regie.php?arg=regie&arg1=pos&round=<?php echo $round; ?>'>
             <?php
        }
        else {
             ?>
             <a href='speaker_results.php?arg=pos&round=<?php echo $round; ?>'>
             <?php
        }
       ?>
				<?php echo $GLOBALS['strPositionShort']; ?>
				<img src='<?php echo $img_pos; ?>' />
			</a>
        </th>
		<th class='dialog' colspan='2'>
        <?php
         if ($argT == 'regie'){
            ?>
             <a href='regie.php?arg=regie&arg1=name&round=<?php echo $round; ?>'>
             <?php
        }
        else {
             ?>
             <a href='speaker_results.php?arg=name&round=<?php echo $round; ?>'>    
             <?php
        }
        ?>
        
				<?php echo $GLOBALS['strAthlete']; ?>
				<img src='<?php echo $img_name; ?>' />
			</a>
        </th>
        <?php 
        if ($argT != 'regie'){
                ?>
               <th class='dialog'><?php echo $GLOBALS['strYearShort']; ?></th>
                <th class='dialog'><?php echo $GLOBALS['strCountry']; ?></th>
                <th class='dialog'>
                <a href='speaker_results.php?arg=club&round=<?php echo $round; ?>'>
                        <?php echo $GLOBALS['strClub']; ?>
                        <img src='<?php echo $img_club; ?>' />
                    </a>
                </th>
                <?php
        }
		?>
		<th class='dialog'>
        <?php
         if ($argT == 'regie'){
            ?>
             <a href='regie.php?arg=regie&arg1=perf&round=<?php echo $round; ?>'>
             <?php
        }
        else {
             ?>
            <a href='speaker_results.php?arg=perf&round=<?php echo $round; ?>'>  
             <?php
        }
        ?>
		
				<?php echo $GLOBALS['strTopPerformance']; ?>
				<img src='<?php echo $img_perf; ?>' />
			</a>
        </th>
		<th class='dialog'>
        <?php
         if ($argT == 'regie'){
            ?>
             <a href='regie.php?arg=regie&arg1=rang&round=<?php echo $round; ?>'>
             <?php
        }
        else {
             ?>
            <a href='speaker_results.php?arg=rang&round=<?php echo $round; ?>'>    
             <?php
        }
        ?>    
		
				<?php echo $GLOBALS['strRank']; ?>
				<img src='<?php echo $img_rang; ?>' />
			</a>
        </th>
		<th class='dialog' colspan='6'><?php echo $GLOBALS['strPerformance']; ?></th>
		
		
		
	</tr>
		<?php
	}


	/*
	 * Athlete data line
	 *		- pos		roster position, e.g. track
	 *		- nbr		start nbr
	 *		- name	athlete's name (preformatted)
	 *		- year	year of birth
	 *		- club	
	 *		- perfs	array of preformatted results
	 *		- rank	rank, if any
	 */
	function printAthleteLine($pos, $nbr, $name, $year, $club, $topperf, $perfs, $fett, $rank, $country="", $athletID,  $curr_class='', $arg='')
	{
?>
	<tr class='<?php if (empty($curr_class)) {echo $this->rowclass[0];} else {echo $curr_class; } ?>' onClick='window.open("speaker_entry.php?item=<?php echo $athletID; ?>", "_self")' style="cursor: pointer;">    
		<td class='forms_right'><?php echo $pos; ?></td>
		<td class='forms_right'><?php echo $nbr; ?></td>
		<td nowrap><?php echo $name; ?></td>
        <?php
        if ($arg != 'regie'){
            ?>
            <td class='forms_ctr'><?php echo $year; ?></td>
            <td class='forms_ctr'><?=(($country!='' && $country!='-') ? $country : '&nbsp;')?></td>
            <td nowrap><?php echo $club; ?></td>
            <?php
        }
		?>
		<td><?php echo $topperf; ?></td>
		<td class='forms_ctr'><?php echo $rank; ?></td>
			<?php
		
        if ($arg == 'regie'){  
            ?>
            <td><nobr>
            <?php
        }
		// show all results
		foreach($perfs as $key => $perf)
		{
			 if ($arg == 'regie'){  
                 
                if($fett[$key]==1) {
                    echo "<b>&nbsp; $perf &nbsp;</b>"; 
                }
                else {
                     echo "&nbsp; $perf &nbsp;";    
                }  
               
             }
             else {
                  ?>
                  <td>
                  <?php 
                  if($fett[$key]==1) {
                     echo "<b> $perf</b></td>";   
                  }
                  else {
                    echo " $perf</td>";    
                  }  
             }  
		} 
        
         if ($arg == 'regie'){  
            ?>
            </nobr>  </td>
            <?php
        } 
		?>
			
	</tr>
		<?php
		$this->switchRowClass();
	}

} // END CLASS Gui_TechResultTable


/********************************************
 *
 * CLASS GUI_HighResultTable
 *
 * Prints an HTML table for high jump
 * and pole vault).
 *
 *******************************************/

class GUI_HighResultTable extends GUI_ResultTable
{
	/*
	 * Heat title line
	 *		- id			DB primary key
	 *		- scroll		scroll ID
	 *		- title		heat name
	 *		- status		heat status
	 */
     function printHeatTitleRegie($cat, $disc)
     {
        ?>
         <tr>
        <th class='dialog_title' colspan='2'><?php echo $cat; ?>             
        </th>
         <th class='dialog_title' colspan='6'><?php echo $disc; ?>                          
        </th>
        </tr> 
        <?php
     }
     
	function printHeatTitle($id, $scroll, $title, $status, $arg='')
	{
		?>
	<tr>
		<th class='dialog' colspan='<?php if ($arg == 'regie') {echo 4 + $this->spanincr;} else {echo 7 + $this->spanincr;} ?>'>
			<a name='heat_<?php echo $scroll; ?>' /><?php echo $title; ?></a>
		</th>
		<?php
        if ($arg != 'regie') {
		    if($this->status >= $GLOBALS['cfgRoundStatus']['results_in_progress'])
		    {
			    if($status == $GLOBALS['cfgHeatStatus']['announced']) {
				    $checked = 'checked';
			    }
			    else {
				    $checked = '';
			    }

			    ?>
		    <form action='controller.php' method='post'
			    name='resstat_<?php echo $scroll; ?>' target='controller'>
		    <th class='dialog' colspan='6'>
			    <?php echo $GLOBALS['strResultsAnnounced']; ?>
			    <input type='hidden' name='act' value='saveHeatStatus' />
			    <input type='hidden' name='round' value='<?php echo $this->round; ?>' />
			    <input type='hidden' name='item' value='<?php echo $id; ?>' />
			    <input type='checkbox' name='status' <?php echo $checked;?>
				    onClick="document.resstat_<?php echo $scroll; ?>.submit()" />
		    </th>
		    </form>
			    <?php
		    } 
        }
        else {
            ?>   
             <th class='dialog'  colspan='2'></th>
             <?php
        }
		?>
	</tr>
		<?php
	}


	function printAthleteHeader($arg='')
	{
		?>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strPositionShort']; ?></th>
		<th class='dialog' colspan='2'><?php echo $GLOBALS['strAthlete']; ?></th>
        <?php 
        if ($arg != 'regie'){
            ?>
              <th class='dialog'><?php echo $GLOBALS['strYearShort']; ?></th>
              <th class='dialog'><?php echo $GLOBALS['strCountry']; ?></th>
              <th class='dialog'><?php echo $GLOBALS['strClub']; ?></th>
              <?php
        }
		?>
		<th class='dialog'><?php echo $GLOBALS['strTopPerformance']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strRank']; ?></th>
		<th class='dialog' colspan='6'><?php echo $GLOBALS['strPerformance']; ?></th>    
		
	</tr>
		<?php
	}


	/*
	 * Athlete data line
	 *		- pos		roster position, e.g. track
	 *		- nbr		start nbr
	 *		- name	athlete's name (preformatted)
	 *		- year	year of birth
	 *		- club	
	 *		- perfs	array of preformatted results
	 *		- rank	rank, if any
	 */
	function printAthleteLine($pos, $nbr, $name, $year, $club, $topperf, $perfs, $fett, $rank, $country="", $athletID, $curr_class='', $arg='')
	{
?>
	<tr class='<?php if (empty($curr_class)) {echo $this->rowclass[0];} else {echo $curr_class; } ?>' onClick='window.open("speaker_entry.php?item=<?php echo $athletID; ?>", "_self")' style="cursor: pointer;">   
		<td class='forms_right'><?php echo $pos; ?></td>
		<td class='forms_right'><?php echo $nbr; ?></td>
		<td nowrap><?php echo $name; ?></td>
        <?php
        if ($arg != 'regie'){
            ?>
            <td class='forms_ctr'><?php echo $year; ?></td>
            <td class='forms_ctr'><?=(($country!='' && $country!='-') ? $country : '&nbsp;')?></td>
            <td nowrap><?php echo $club; ?></td>
            <?php
        }
        if ($rank == 0){
            $rank = '';
        }
        ?>  		
		<td><?php echo $topperf; ?></td>
		<td class='forms_ctr'><?php echo $rank; ?></td>
			<?php
		

        if ($arg == 'regie'){ 
            ?>
            <td><nobr>
            <?php  
        } 
		// show all results
		foreach($perfs as $key => $perf)
		{
			if ($arg == 'regie'){  
                    
            ?>
        <?php if($fett[$key]==1) echo "<b>";?><?php echo "&nbsp;&nbsp;&nbsp;$perf"; ?> 
            <?php
            
            
            }
            else {
                
              
			?>
		<td><?php if($fett[$key]==1) echo "<b>";?><?php echo $perf;?></td>
			<?php
		   } 
		}
         if ($arg == 'regie'){ 
            ?>
            </nobr> </td>
            <?php  
        } 
		?>
			
	</tr>
		<?php
		$this->switchRowClass();
	}
	
} // END CLASS Gui_HighResultTable


} // end AA_CL_GUI_RESULTTABLE_LIB_INCLUDED

?>
