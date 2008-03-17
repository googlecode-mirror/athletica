<?php

if (!defined('AA_CL_GUI_TEAMPAGE_LIB_INCLUDED'))
{
	define('AA_CL_GUI_TEAMPAGE_LIB_INCLUDED', 1);


 	include('./lib/cl_gui_relaypage.lib.php');

/********************************************
 *
 * GUI_TeamPage
 *
 *	Class to print team lists
 *
 *******************************************/


class GUI_TeamPage extends GUI_RelayPage
{
	function printHeaderLine()
	{
		?>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strName']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strDisciplines']; ?></th>
	</tr>
		<?php
	}


	function printDiscHeaderLine()
	{
		?>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strDiscipline']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strName']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strYearShort']; ?></th>
	</tr>
		<?php
	}


	function printLine($nbr, $name, $year, $disc)
	{
		if(!empty($disc)) {	// new discipline
			$this->switchRowClass();
		}
		?>
	<tr class='<?php echo $this->rowclass[0]; ?>'>
		<td class='forms_right'><?php echo $nbr; ?></td>
		<td><?php echo $name; ?></td>
		<td class='forms_ctr'><?php echo $year; ?></td>
		<td><?php echo $disc; ?></td>
	</tr>
		<?php
	}

} // end GUI_TeamPage


/********************************************
 *
 * GUI_TeamDiscPage
 *
 *	Class to print discipline lists per team
 *
 *******************************************/


class GUI_TeamDiscPage extends GUI_RelayPage
{
	function printHeaderLine()
	{
		?>
	<tr>
		<th class='dialog'><?php echo $GLOBALS['strDiscipline']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strName']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strYearShort']; ?></th>
	</tr>
		<?php
	}


	function printLine($disc, $nbr, $name, $year)
	{
		if(!empty($disc)) {	// new discipline
			$this->switchRowClass();
		}
		?>
	<tr class='<?php echo $this->rowclass[0]; ?>'>
		<td><?php echo $disc; ?></td>
		<td class='forms_right'><?php echo $nbr; ?></td>
		<td><?php echo $name; ?></td>
		<td class='forms_ctr'><?php echo $year; ?></td>
	</tr>
		<?php
	}


} // end GUI_TeamDiscPage

} // end AA_CL_GUI_TEAMPAGE_LIB_INCLUDED
?>
