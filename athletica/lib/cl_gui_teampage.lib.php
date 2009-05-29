<?php

if (!defined('AA_CL_GUI_TEAMPAGE_LIB_INCLUDED'))
{
	define('AA_CL_GUI_TEAMPAGE_LIB_INCLUDED', 1);


 	include('./lib/cl_gui_relaypage.lib.php');

    
    
    
  /********************************************
 *
 * GUI_TeamsPage
 *
 *    Class to print team lists
 *
 *******************************************/


class GUI_TeamsPage extends GUI_RelayPage
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
        if(!empty($disc)) {    // new discipline
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

} // end GUI_TeamsPage  
    
       
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
		<th class='dialog'><?php echo $GLOBALS['strTeamTeamSM']; ?></th>
		<th class='dialog'><?php echo $GLOBALS['strCategoryShort']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strClub']; ?></th>   
		<th class='dialog'><?php echo $GLOBALS['strDiscipline']; ?></th>
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


	function printLine($name, $cat, $club, $disc, $perf, $startnbr)
	{
		if(!empty($disc)) {	// new discipline
			$this->switchRowClass();
		}
		?>
	<tr class='<?php echo $this->rowclass[0]; ?>'>
		<td class='forms_right'><?php echo $startnbr; ?></td>
		<td><?php echo $name; ?></td>
		<td><?php echo $cat; ?></td>
        <td><?php echo $club; ?></td> 
		<td><?php echo $disc; ?></td>
	</tr>
		<?php
	}

} // end GUI_TeamPage

 /********************************************
 *
 * GUI_ClubTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class GUI_ClubTeamPage extends GUI_RelayPage
{
    function printHeaderLine()
    {
        ?>
    <tr>
        <th class='dialog'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strTeamTeamSM']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strCategoryShort']; ?></th>         
        <th class='dialog'><?php echo $GLOBALS['strDiscipline']; ?></th>
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


    function printLine($name, $cat, $disc, $perf, $startnbr)
    {
        if(!empty($disc)) {    // new discipline
            $this->switchRowClass();
        }
        ?>
    <tr class='<?php echo $this->rowclass[0]; ?>'>
        <td class='forms_right'><?php echo $startnbr; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo $cat; ?></td>           
        <td><?php echo $disc; ?></td>
    </tr>
        <?php
    }

} // end GUI_ClubTeamPage

 /********************************************
 *
 * GUI_ClubTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class GUI_CatTeamPage extends GUI_RelayPage
{
    function printHeaderLine()
    {
        ?>
   <tr>
        <th class='dialog'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strTeamTeamSM']; ?></th>          
        <th class='dialog'><?php echo $GLOBALS['strClub']; ?></th>   
        <th class='dialog'><?php echo $GLOBALS['strDiscipline']; ?></th>
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


    function printLine($name, $club, $disc, $perf, $startnbr)
    {
        if(!empty($disc)) {    // new discipline
            $this->switchRowClass();
        }
        ?>
    <tr class='<?php echo $this->rowclass[0]; ?>'>
        <td class='forms_right'><?php echo $startnbr; ?></td>
        <td><?php echo $name; ?></td>           
        <td><?php echo $club; ?></td> 
        <td><?php echo $disc; ?></td>
    </tr>
        <?php
    }

} // end GUI_CatTeamPage

/********************************************
 *
 * GUI_ClubCatTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class GUI_ClubCatTeamPage extends GUI_RelayPage
{
    function printHeaderLine()
    {
        ?>
    <tr>
        <th class='dialog'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strTeamTeamSM']; ?></th>
       
        <th class='dialog'><?php echo $GLOBALS['strDiscipline']; ?></th>
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


    function printLine($name,  $disc, $perf, $startnbr)
    {
        if(!empty($disc)) {    // new discipline
            $this->switchRowClass();
        }
        ?>
    <tr class='<?php echo $this->rowclass[0]; ?>'>
        <td class='forms_right'><?php echo $startnbr; ?></td>
        <td><?php echo $name; ?></td>
       
        <td><?php echo $disc; ?></td>
    </tr>
        <?php
     }

} // end GUI_ClubCatTeamPage

 /********************************************
 *
 * GUI_CatDiscTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class GUI_CatDiscTeamPage extends GUI_RelayPage
{
    function printHeaderLine()
    {
        ?>
    <tr>
        <th class='dialog'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strTeamTeamSM']; ?></th>  
        <th class='dialog'><?php echo $GLOBALS['strClub']; ?></th>    
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


    function printLine($name,  $club, $perf, $startnbr)
    {
        if(!empty($disc)) {    // new discipline
            $this->switchRowClass();
        }
        ?>
    <tr class='<?php echo $this->rowclass[0]; ?>'>
        <td class='forms_right'><?php echo $startnbr; ?></td>
        <td><?php echo $name; ?></td>  
        <td><?php echo $club; ?></td>   
    </tr>
        <?php
    }

} // end GUI_CatDiscTeamPage

/********************************************
 *
 * GUI_ClubDiscTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class GUI_ClubDiscTeamPage extends GUI_RelayPage
{
    function printHeaderLine()
    {
        ?>
    <tr>
        <th class='dialog'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strTeamTeamSM']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strCategoryShort']; ?></th> 
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


    function printLine($name, $cat, $perf, $startnbr)
    {
        if(!empty($disc)) {    // new discipline
            $this->switchRowClass();
        }
        ?>
    <tr class='<?php echo $this->rowclass[0]; ?>'>
        <td class='forms_right'><?php echo $startnbr; ?></td>
        <td><?php echo $name; ?></td>
        <td><?php echo $cat; ?></td>   
    </tr>
        <?php
    }

} // end GUI_TeamPage

/********************************************
 *
 * GUI_ClubCatDiscTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class GUI_ClubCatDiscTeamPage extends GUI_RelayPage
{
    function printHeaderLine()
    {
        ?>
    <tr>
        <th class='dialog'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='dialog'><?php echo $GLOBALS['strTeamTeamSM']; ?></th> 
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


    function printLine($name, $perf, $startnbr)
    {
        if(!empty($disc)) {    // new discipline
            $this->switchRowClass();
        }
        ?>
    <tr class='<?php echo $this->rowclass[0]; ?>'>
        <td class='forms_right'><?php echo $startnbr; ?></td>
        <td><?php echo $name; ?></td>   
    </tr>
        <?php
    }

} // end GUI_ClubCatDiscTeamPage


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
