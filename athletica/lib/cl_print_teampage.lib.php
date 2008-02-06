<?php

if (!defined('AA_CL_PRINT_TEAMPAGE_LIB_INCLUDED'))
{
	define('AA_CL_PRINT_TEAMPAGE_LIB_INCLUDED', 1);


 	include('./lib/cl_print_relaypage.lib.php');

/********************************************
 *
 * PRINT_TeamPage
 *
 *	Class to print team lists
 *
 *******************************************/


class PRINT_TeamPage extends PRINT_RelayPage
{
	function printHeaderLine()
	{
		if(($this->lpp - $this->linecnt) < 4)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
		}
?>
	<tr>
		<th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='team_entry_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='team_entry_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='team_entry_disc'><?php echo $GLOBALS['strDisciplines']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	}


	function printDiscHeaderLine()
	{
		if(($this->lpp - $this->linecnt) < 4)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
		}
?>
	<tr>
		<th class='team_disc'><?php echo $GLOBALS['strDiscipline']; ?></th>
		<th class='team_disc_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='team_disc_year'><?php echo $GLOBALS['strYearShort']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	}


	function printLine($nbr, $name, $year, $disc)
	{
		if(($this->lpp - $this->linecnt) < 2)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
			$this->printHeaderLine();
		}
?>
	<tr>
		<td class='team_entry_nbr'><?php echo $nbr; ?></td>
		<td class='team_entry_name'><?php echo $name; ?></td>
		<td class='team_entry_year'><?php echo $year; ?></td>
		<td class='team_entry_disc'><?php echo $disc; ?></td>
	</tr>
<?php
		$this->linecnt++;
	}

	function printDiscLine($disc, $name, $year)
	{
		if(($this->lpp - $this->linecnt) < 2)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table class='sheet'>");
		}
		$this->linecnt++;			// increment line count
?>
	<tr>
		<td class='team_disc'><?php echo $disc; ?></td>
		<td class='team_disc_name'><?php echo $name; ?></td>
		<td class='team_disc_year'><?php echo $year; ?></td>
	</tr>
<?php
	}


} // end PRINT_TeamPage


/********************************************
 *
 * PRINT_TeamDiscPage
 *
 *	Class to print discipline lists per team
 *
 *******************************************/


class PRINT_TeamDiscPage extends PRINT_RelayPage
{
	function printHeaderLine()
	{
		if(($this->lpp - $this->linecnt) < 4)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
		}
?>
	<tr>
		<th class='team_disc'><?php echo $GLOBALS['strDiscipline']; ?></th>
		<th class='team_disc_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='team_disc_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='team_disc_year'><?php echo $GLOBALS['strYearShort']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	}


	function printLine($disc, $nbr, $name, $year)
	{
		if(($this->lpp - $this->linecnt) < 2)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table class='sheet'>");
		}
		$this->linecnt++;			// increment line count
?>
	<tr>
		<td class='team_disc'><?php echo $disc; ?></td>
		<td class='team_disc_nbr'><?php echo $nbr; ?></td>
		<td class='team_disc_name'><?php echo $name; ?></td>
		<td class='team_disc_year'><?php echo $year; ?></td>
	</tr>
<?php
	}


} // end PRINT_TeamDiscPage

} // end AA_CL_PRINT_TEAMPAGE_LIB_INCLUDED
?>
