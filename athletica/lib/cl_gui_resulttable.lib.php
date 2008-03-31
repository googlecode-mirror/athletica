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
	function printHeatTitle($id, $scroll, $title, $status, $film='', $wind='')
	{
		?>
	<tr>
		<th class='dialog' colspan='3'>
			<a name='heat_<?php echo $scroll; ?>' /><?php echo $title; ?></a>
		</th>
		<th class='dialog' colspan='2'><?php echo $GLOBALS['strFilm'] . " " . $film; ?></th>
		<?php
		// track discipline with wind
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
			?>
		<th class='dialog' colspan='<?php echo 2+$this->spanincr; ?>' />
			<?php
		}	// ET track discipline with wind

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
		?>
	</tr>
		<?php
	}


	function printAthleteHeader()
	{
		?>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strPositionShort']; ?></th>
		<th class='dialog' colspan='2'><?php echo $GLOBALS['strAthlete']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strCountry']; ?></th>
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
		?>
	</tr>
		<?php
	}


	function printRelayHeader()
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
	function printAthleteLine($pos, $nbr, $name, $year, $club, $perf, $rank=0, $qual=0, $country="")
	{
?>
	<tr class='<?php echo $this->rowclass[0]; ?>'>
		<td class='forms_right'><?php echo $pos; ?></td>
		<td class='forms_right'><?php echo $nbr; ?></td>
		<td><?php echo $name; ?></td>
		<td class='forms_ctr'><?php echo $year; ?></td>
		<td class='forms_ctr'><?=(($country!='' && $country!='-') ? $country : '&nbsp;')?></td>
		<td><?php echo $club; ?></td>
		<td class='forms_right'><?php echo $perf; ?></td>
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
	function printRelayLine($pos, $name, $club, $perf, $rank=0, $qual=0, $athl=0)
	{
	$tds = 3;
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
 * Prints an HTML table for all technical results (incl. high jump
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
	function printHeatTitle($id, $scroll, $title, $status)
	{
		?>
	<tr>
		<th class='dialog' colspan='<?php echo 5 + $this->spanincr; ?>'>
			<a name='heat_<?php echo $scroll; ?>' /><?php echo $title; ?></a>
		</th>
		<?php

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
		<th class='dialog' colspan='2'>
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
		?>
	</tr>
		<?php
	}


	function printAthleteHeader()
	{
		?>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strPositionShort']; ?></th>
		<th class='dialog' colspan='2'><?php echo $GLOBALS['strAthlete']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strCountry']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strClub']; ?></th>
		<?php
		// show rank if all results entered
		if($this->status == $GLOBALS['cfgRoundStatus']['results_done'])
		{
			?>
		<th class='dialog'><?php echo $GLOBALS['strRank']; ?></th>
			<?php
		}
		?>
		<th class='dialog' colspan='2'><?php echo $GLOBALS['strPerformance']; ?></th>
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
	function printAthleteLine($pos, $nbr, $name, $year, $club, $perfs, $rank=0, $country="")
	{
?>
	<tr class='<?php echo $this->rowclass[0]; ?>'>
		<td class='forms_right'><?php echo $pos; ?></td>
		<td class='forms_right'><?php echo $nbr; ?></td>
		<td nowrap><?php echo $name; ?></td>
		<td class='forms_ctr'><?php echo $year; ?></td>
		<td class='forms_ctr'><?=(($country!='' && $country!='-') ? $country : '&nbsp;')?></td>
		<td nowrap><?php echo $club; ?></td>
<?php
		// show rank if all results entered
		if($this->status == $GLOBALS['cfgRoundStatus']['results_done'])
		{
			?>
		<td class='forms_right'><?php echo $rank; ?></td>
			<?php
		}

		// show all results
		foreach($perfs as $perf)
		{
			?>
		<td><?php echo $perf; ?></td>
			<?php
		}
		?>
	</tr>
		<?php
		$this->switchRowClass();
	}

} // END CLASS Gui_TechResultTable

} // end AA_CL_GUI_RESULTTABLE_LIB_INCLUDED

?>
