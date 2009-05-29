<?php

if (!defined('AA_CL_PRINT_TEAMPAGE_LIB_INCLUDED'))
{
	define('AA_CL_PRINT_TEAMPAGE_LIB_INCLUDED', 1);


 	include('./lib/cl_print_relaypage.lib.php');

 /********************************************
 *
 * PRINT_TeamsPage
 *
 *    Class to print team lists
 *
 *******************************************/


class PRINT_TeamsPage extends PRINT_RelayPage
{
    function printHeaderLine()
    {
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }
?>
    <tr>
        <th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='teams_entry_name'><?php echo $GLOBALS['strName']; ?></th>
        <th class='team_entry_year'><?php echo $GLOBALS['strYearShort']; ?></th>
        <th class='teams_entry_disc'><?php echo $GLOBALS['strDisciplines']; ?></th>
    </tr>
<?php
        $this->linecnt++;
    }


    function printDiscHeaderLine()
    {
        if(($this->lpp - $this->linecnt) < 4)        // page break check
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
        if(($this->lpp - $this->linecnt) < 2)        // page break check
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
        if(($this->lpp - $this->linecnt) < 2)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table class='sheet'>");
        }
        $this->linecnt++;            // increment line count
?>
    <tr>
        <td class='team_disc'><?php echo $disc; ?></td>
        <td class='team_disc_name'><?php echo $name; ?></td>
        <td class='team_disc_year'><?php echo $year; ?></td>
    </tr>
<?php
    }


} // end PRINT_TeamsPage
    
/********************************************
 *
 * PRINT_TeamPage
 *
 *	Class to print team lists
 *
 *******************************************/


class PRINT_TeamPage extends PRINT_RelayPage
{
	function printHeaderLine($enrolSheet=false)
	{
		if(($this->lpp - $this->linecnt) < 12)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
		}
?>
	<tr>
    <?php if  ($enrolSheet) {
              ?>
             <th class='team_entry_tic' /> 
              <?php 
          }
      ?>  
          
		<th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='team_entry_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='team_entry_cat'><?php echo $GLOBALS['strCategoryShort']; ?></th>
        <th class='team_entry_club'><?php echo $GLOBALS['strClub']; ?></th> 
		<th class='team_entry_disc'><?php echo $GLOBALS['strDiscipline']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	} 

	function printLine($name, $cat, $club, $disc, $perf, $nbr, $enrolSheet)
	{
		if(($this->lpp - $this->linecnt) < 12)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
			$this->printHeaderLine($enrolSheet);
		}
?>
	<tr>
    <?php if  ($enrolSheet) {
              ?>
              <td class='team_entry_tic'>[&nbsp;&nbsp;&nbsp;]</td>  
              <?php 
          }
      ?>  
		<td class='team_entry_nbr'><?php echo $nbr; ?></td>
		<td class='team_entry_name'><?php echo $name; ?></td>
		<td class='team_entry_cat'><?php echo $cat; ?></td>
        <td class='team_entry_club'><?php echo $club; ?></td> 
		<td class='team_entry_disc'><?php echo $disc; ?></td>
	</tr>
<?php

        // count more lines if string is to long ($disc string)  
        $t = 0;
        $w = AA_getStringWidth($disc, 12);
        $t = ceil(($w / 90));  
        $this->linecnt+=$t;         
		
	}     

} // end PRINT_TeamPage

 /********************************************
 *
 * PRINT_ClubTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class PRINT_ClubTeamPage extends PRINT_RelayPage
{
    function printHeaderLine($enrolSheet=false)
    {
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
             <th class='team_entry_tic' /> 
              <?php 
          }
      ?>  
        <th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='team_entry_name'><?php echo $GLOBALS['strName']; ?></th>
        <th class='team_entry_cat'><?php echo $GLOBALS['strCategoryShort']; ?></th> 
        <th class='team_entry_disc'><?php echo $GLOBALS['strDiscipline']; ?></th>
    </tr>
<?php
        $this->linecnt++;
    }     

    function printLine($name, $cat, $disc, $perf, $nbr,$enrolSheet)
    {
        if(($this->lpp - $this->linecnt) < 2)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
            $this->printHeaderLine($enrolSheet);
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
              <td class='team_entry_tic'>[&nbsp;&nbsp;&nbsp;]</td>  
              <?php 
          }
      ?>  
        <td class='team_entry_nbr'><?php echo $nbr; ?></td>
        <td class='team_entry_name'><?php echo $name; ?></td>
        <td class='team_entry_cat'><?php echo $cat; ?></td>   
        <td class='team_entry_disc'><?php echo $disc; ?></td>
    </tr>
<?php
        $this->linecnt++;
    } 

} // end PRINT_ClubTeamPage

/********************************************
 *
 * PRINT_CatTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class PRINT_CatTeamPage extends PRINT_RelayPage
{
    function printHeaderLine($enrolSheet=false)
    {
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
             <th class='team_entry_tic' /> 
              <?php 
          }
      ?>  
        <th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='team_entry_name'><?php echo $GLOBALS['strName']; ?></th>  
        <th class='team_entry_club'><?php echo $GLOBALS['strClub']; ?></th> 
        <th class='team_entry_disc'><?php echo $GLOBALS['strDiscipline']; ?></th>
    </tr>
<?php
        $this->linecnt++;
    }     

    function printLine($name, $club, $disc, $perf, $nbr,$enrolSheet)
    {
        if(($this->lpp - $this->linecnt) < 2)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
            $this->printHeaderLine($enrolSheet);
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
              <td class='team_entry_tic'>[&nbsp;&nbsp;&nbsp;]</td>  
              <?php 
          }
      ?>  
        <td class='team_entry_nbr'><?php echo $nbr; ?></td>
        <td class='team_entry_name'><?php echo $name; ?></td>  
        <td class='team_entry_club'><?php echo $club; ?></td> 
        <td class='team_entry_disc'><?php echo $disc; ?></td>       
    </tr>
<?php
        $this->linecnt++;
    }     
                
} // end PRINT_CatTeamPage

/********************************************
 *
 * PRINT_ClubCatTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class PRINT_ClubCatTeamPage extends PRINT_RelayPage
{
    function printHeaderLine($enrolSheet=false)
    {
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
             <th class='team_entry_tic' /> 
              <?php 
          }
      ?>  
        <th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='team_entry_name'><?php echo $GLOBALS['strName']; ?></th>   
        <th class='team_entry_disc'><?php echo $GLOBALS['strDiscipline']; ?></th>
    </tr>
<?php
        $this->linecnt++;
    }     

    function printLine($name, $disc, $perf, $nbr, $enrolSheet)
    {
        if(($this->lpp - $this->linecnt) < 2)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
            $this->printHeaderLine($enrolSheet);
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
              <td class='team_entry_tic'>[&nbsp;&nbsp;&nbsp;]</td>  
              <?php 
          }
      ?>  
        <td class='team_entry_nbr'><?php echo $nbr; ?></td>
        <td class='team_entry_name'><?php echo $name; ?></td>  
        <td class='team_entry_disc'><?php echo $disc; ?></td>
    </tr>
<?php
        $this->linecnt++;
    }     

} // end PRINT_ClubCatTeamPage

/********************************************
 *
 * PRINT_CatDiscTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class PRINT_CatDiscTeamPage extends PRINT_RelayPage
{
    function printHeaderLine($enrolSheet=false)
    {
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
             <th class='team_entry_tic' /> 
              <?php 
          }
      ?>  
        <th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='team_entry_name'><?php echo $GLOBALS['strName']; ?></th>  
        <th class='team_entry_club'><?php echo $GLOBALS['strClub']; ?></th>  
    </tr>
<?php
        $this->linecnt++;
    }      
         
    function printLine($name, $club, $perf, $nbr,$enrolSheet)
    {
        if(($this->lpp - $this->linecnt) < 2)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
            $this->printHeaderLine($enrolSheet);
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
              <td class='team_entry_tic'>[&nbsp;&nbsp;&nbsp;]</td>  
              <?php 
          }
      ?>  
        <td class='team_entry_nbr'><?php echo $nbr; ?></td>
        <td class='team_entry_name'><?php echo $name; ?></td>  
        <td class='team_entry_club'><?php echo $club; ?></td>           
    </tr>
<?php
        $this->linecnt++;
    }   

} // end PRINT_CatDiscTeamPage

/********************************************
 *
 * PRINT_ClubDiscTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class PRINT_ClubDiscTeamPage extends PRINT_RelayPage
{
    function printHeaderLine($enrolSheet=false)
    {
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
             <th class='team_entry_tic' /> 
              <?php 
          }
      ?>  
        <th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='team_entry_name'><?php echo $GLOBALS['strName']; ?></th>
        <th class='team_entry_cat'><?php echo $GLOBALS['strCategoryShort']; ?></th> 
    </tr>
<?php
        $this->linecnt++;
    }
                                   
    function printLine($name, $cat, $perf, $nbr,$enrolSheet)
    {
        if(($this->lpp - $this->linecnt) < 2)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
            $this->printHeaderLine($enrolSheet);
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
              <td class='team_entry_tic'>[&nbsp;&nbsp;&nbsp;]</td>  
              <?php 
          }
      ?>  
        <td class='team_entry_nbr'><?php echo $nbr; ?></td>
        <td class='team_entry_name'><?php echo $name; ?></td>
        <td class='team_entry_cat'><?php echo $cat; ?></td>  
    </tr>
<?php
        $this->linecnt++;
    }       

} // end PRINT_ClubDiscTeamPage

/********************************************
 *
 * PRINT_ClubCatDiscTeamPage
 *
 *    Class to print team lists
 *
 *******************************************/


class PRINT_ClubCatDiscTeamPage extends PRINT_RelayPage
{
    function printHeaderLine($enrolSheet=false)
    {
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
             <th class='team_entry_tic' /> 
              <?php 
          }
      ?>  
        <th class='team_entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='team_entry_name'><?php echo $GLOBALS['strName']; ?></th>   
    </tr>
<?php
        $this->linecnt++;
    }    

    function printLine($name, $perf, $nbr,$enrolSheet)
    {
        if(($this->lpp - $this->linecnt) < 2)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
            $this->printHeaderLine($enrolSheet);
        }
?>
    <tr>
     <?php if  ($enrolSheet) {
              ?>
              <td class='team_entry_tic'>[&nbsp;&nbsp;&nbsp;]</td>  
              <?php 
          }
      ?>  
        <td class='team_entry_nbr'><?php echo $nbr; ?></td>
        <td class='team_entry_name'><?php echo $name; ?></td> 
    </tr>
<?php
        $this->linecnt++;
    }     

} // end PRINT_ClubCatDiscTeamPage

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
