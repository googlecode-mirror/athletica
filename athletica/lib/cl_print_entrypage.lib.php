<?php

if (!defined('AA_CL_PRINT_ENTRYPAGE_LIB_INCLUDED'))
{
	define('AA_CL_PRINT_ENTRYPAGE_LIB_INCLUDED', 1);


 	include('./lib/cl_print_page.lib.php');   


/********************************************
 *
 * PRINT_EntryPage
 *
 *	Class to print basic entry lists
 *
 *******************************************/


class PRINT_EntryPage extends PRINT_Page
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
		<th class='entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='entry_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='entry_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='entry_ioc'><?php echo $GLOBALS['strCountry']; ?></th>
		<th class='entry_cat'><?php echo $GLOBALS['strCategoryShort']; ?></th>
		<th class='entry_club'><?php echo $GLOBALS['strClub']; ?></th>
		<th class='entry_disc'><?php echo $GLOBALS['strDisciplines']; ?></th> 
		<?php
		if(isset($_GET['payment']) && isset($_GET['discgroup'])){
			?>
		<th class='entry_year'>&nbsp;</th>
			<?php
		}
		?>
	</tr>
<?php
		$this->linecnt++;
	}


	function printLine($nbr, $name, $year, $cat, $club, $disc, $ioc, $paid='')
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
		<td class='entry_nbr'><?php echo $nbr; ?></td>
		<td class='entry_name'><?php echo $name; ?></td>
		<td class='entry_year'><?php echo $year; ?></td>
		<td class='entry_ioc'><?php echo $ioc; ?></td>
		<td class='entry_cat'><?php echo $cat; ?></td>
		<td class='entry_club'><?php echo $club; ?></td>
		<td class='entry_disc'><?php echo $disc; ?></td> 
		<?php
		if(isset($_GET['payment']) && isset($_GET['discgroup'])){
			?>
		<td class='entry_year'><?php echo $paid; ?></td>
			<?php
		}
		?>
	</tr>
<?php
		// count more lines if string is to long (discipline string)
		$w = AA_getStringWidth($disc, 12);
		$t = ceil(($w / 200));
		if($w > 200){
			$this->linecnt += $t;
		}else{
			$this->linecnt++;
		}
	}
 
	function printSubTitle($title)
	{
		if(($this->lpp - $this->linecnt) < 7)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
		}
		$this->linecnt = $this->linecnt + 3;	// needs 3 lines (see style sheet)
?>
		<div class='hdr2'><?php echo $title; ?></div>
        
<?php
	}

} // end PRINT_EntryPage


/********************************************
 *
 * PRINT_CatEntryPage
 *
 *	Class to print entry lists per category
 *
 *******************************************/


class PRINT_CatEntryPage extends PRINT_EntryPage
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
		<th class='entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='entry_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='entry_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='entry_ioc'><?php echo $GLOBALS['strCountry']; ?></th>
		<th class='entry_club'><?php echo $GLOBALS['strClub']; ?></th>
		<th class='entry_disc'><?php echo $GLOBALS['strDisciplines']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	}


	function printLine($nbr, $name, $year, $club, $disc, $ioc)
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
		<td class='entry_nbr'><?php echo $nbr; ?></td>
		<td class='entry_name'><?php echo $name; ?></td>
		<td class='entry_year'><?php echo $year; ?></td>
		<td class='entry_ioc'><?php echo $ioc; ?></td>
		<td class='entry_club'><?php echo $club; ?></td>
		<td class='entry_disc'><?php echo $disc; ?></td>
	</tr>
<?php
		// count more lines if string is to long (discipline string)
		$w = AA_getStringWidth($disc, 12);
		$t = ceil(($w / 200));
		if($w > 200){
			$this->linecnt += $t;
		}else{
			$this->linecnt++;
		}
		
		//$this->linecnt++;
	}

} // end PRINT_CatEntryPage



/********************************************
 *
 * PRINT_ClubEntryPage
 *
 *	Class to print entry lists per club
 *
 *******************************************/


class PRINT_ClubEntryPage extends PRINT_EntryPage
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
		<th class='entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='entry_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='entry_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='entry_ioc'><?php echo $GLOBALS['strCountry']; ?></th>
		<th class='entry_cat'><?php echo $GLOBALS['strCategoryShort']; ?></th>
		<th class='entry_disc'><?php echo $GLOBALS['strDisciplines']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	}


	function printLine($nbr, $name, $year, $cat, $disc, $ioc)
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
		<td class='entry_nbr'><?php echo $nbr; ?></td>
		<td class='entry_name'><?php echo $name; ?></td>
		<td class='entry_year'><?php echo $year; ?></td>
		<td class='entry_ioc'><?php echo $ioc; ?></td>
		<td class='entry_cat'><?php echo $cat; ?></td>
		<td class='entry_disc'><?php echo $disc; ?></td>
	</tr>
<?php
		// count more lines if string is to long (discipline string)
		$w = AA_getStringWidth($disc, 12);
		$t = ceil(($w / 200));
		if($w > 200){
			$this->linecnt += $t;
		}else{
			$this->linecnt++;
		}
		//$this->linecnt++;
	}

} // end PRINT_ClubEntryPage


/********************************************
 *
 * PRINT_CatDiscEntryPage
 *
 *	Class to print entry lists per category and discipline
 *
 *******************************************/


class PRINT_CatDiscEntryPage extends PRINT_EntryPage
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
		<th class='entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='entry_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='entry_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='entry_ioc'><?php echo $GLOBALS['strCountry']; ?></th>
		<th class='entry_club'><?php echo $GLOBALS['strClub']; ?></th>
		<th class='entry_perf'><?php echo $GLOBALS['strTopPerformance']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	}


	function printLine($nbr, $name, $year, $club, $perf, $ioc)
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
		<td class='entry_nbr'><?php echo $nbr; ?></td>
		<td class='entry_name'><?php echo $name; ?></td>
		<td class='entry_year'><?php echo $year; ?></td>
		<td class='entry_ioc'><?php echo $ioc; ?></td>
		<td class='entry_club'><?php echo $club; ?></td>
		<td class='entry_perf'><?php echo $perf; ?></td>
	</tr>
<?php
		$this->linecnt++;
	}

} // end PRINT_CatDiscEntryPage


/********************************************
 *
 * PRINT_ClubCatEntryPage
 *
 *	Class to print entry lists per club and category
 *
 *******************************************/


class PRINT_ClubCatEntryPage extends PRINT_EntryPage
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
		<th class='entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='entry_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='entry_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='entry_ioc'><?php echo $GLOBALS['strCountry']; ?></th>
		<th class='entry_disc'><?php echo $GLOBALS['strDisciplines']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	}


	function printLine($nbr, $name, $year, $disc, $ioc)
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
		<td class='entry_nbr'><?php echo $nbr; ?></td>
		<td class='entry_name'><?php echo $name; ?></td>
		<td class='entry_year'><?php echo $year; ?></td>
		<td class='entry_ioc'><?php echo $ioc; ?></td>
		<td class='entry_disc'><?php echo $disc; ?></td>
	</tr>
<?php
		// count more lines if string is to long (discipline string)
		$w = AA_getStringWidth($disc, 12);
		$t = ceil(($w / 200));
		if($w > 200){
			$this->linecnt += $t;
		}else{
			$this->linecnt++;
		}
		//$this->linecnt++;
	}

} // end PRINT_ClubCatEntryPage



/********************************************
 *
 * PRINT_ClubCatDiscEntryPage
 *
 *	Class to print entry lists per club, category and discipline
 *
 *******************************************/


class PRINT_ClubCatDiscEntryPage extends PRINT_EntryPage
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
		<th class='entry_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='entry_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='entry_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='entry_ioc'><?php echo $GLOBALS['strCountry']; ?></th>
		<th class='entry_perf'><?php echo $GLOBALS['strTopPerformance']; ?></th>
	</tr>
<?php
		$this->linecnt++;
	}


	function printLine($nbr, $name, $year, $perf, $ioc)
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
		<td class='entry_nbr'><?php echo $nbr; ?></td>
		<td class='entry_name'><?php echo $name; ?></td>
		<td class='entry_year'><?php echo $year; ?></td>
		<td class='entry_ioc'><?php echo $ioc; ?></td>
		<td class='entry_perf'><?php echo $perf; ?></td>
	</tr>
<?php
		$this->linecnt++;
	}

} // end PRINT_PRINT_ClubCatDiscEntryPage



/********************************************
 *
 * PRINT_EnrolementPage
 *
 *	Class to print enrolement lists
 *
 *******************************************/


class PRINT_EnrolementPage extends PRINT_EntryPage
{

	var $event;
	var $cat;
	var $time;
	var $bRelay;
	var $timeinfo;
	
	function printTitle()
	{  
		// page break check (at least one further line left)
		if(($this->lpp - $this->linecnt) < 8)		
		{
			$this->insertPageBreak();
		}
		$this->linecnt = $this->linecnt + 4;	// needs four lines (see style sheet)
?>
		<table class="enrolmt_disc"><tr>
			<th class='enrolmt_event'><?php echo $this->event; ?></th>
			<th class='enrolmt_cat'><?php echo $this->cat; ?></th>
			<th class='enrolmt_time'><?php echo $this->time; ?></th>
		</tr>
		<tr><th class='enrolmt_timeinfo' colspan="3">
			<?php echo $this->timeinfo; ?>
		</td></tr>
		</table>
<?php
	}

	function printHeaderLine($relay, $svm)
	{   
		if(($this->lpp - $this->linecnt) < 4)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
		}
		
		$this->bRelay = $relay;
		
		if($relay == FALSE)
		{
?>
	<tr>
		<th class='enrolmt_tic' />
		<th class='enrolmt_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
		<th class='enrolmt_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='enrolmt_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='enrolmt_year'><?php echo $GLOBALS['strCountry']; ?></th>
		<?php 
		if($svm){
			?>
		<th class='enrolmt_club'><?php echo $GLOBALS['strTeam']; ?></th>
			<?php
		}else{
			?>
		<th class='enrolmt_club'><?php echo $GLOBALS['strClub']; ?></th>
			<?php
		}
		?>
		<th class='enrolmt_top'><?php echo $GLOBALS['strTopPerformance']; ?></th>
	</tr>
<?php 
		}
		else
		{  
?>
	<tr>
		<th class='enrolmt_tic' />
		<th class='enrolmt_nbr'><?php echo $GLOBALS['strStartnumber']; ?></th>
        <th class='enrolmt_pos'><?php echo $GLOBALS['strPositionShort']; ?></th>  
		<th class='enrolmt_name'><?php echo $GLOBALS['strName']; ?></th>
		<th class='enrolmt_year'><?php echo $GLOBALS['strYearShort']; ?></th>
		<th class='enrolmt_year'><?php echo $GLOBALS['strCountry']; ?></th>
		<th class='enrolmt_club'><?php echo $GLOBALS['strRelay']; ?></th>
		<?php 
		if($svm){
			?>
		<th class='enrolmt_club'><?php echo $GLOBALS['strTeam']; ?></th>
			<?php
		}else{
			?>
		<th class='enrolmt_club'><?php echo $GLOBALS['strClub']; ?></th>
			<?php
		}
		?>
	</tr>
<?php
		}
		$this->linecnt++;
	}


	function printLine($nbr,  $name, $year, $club, $ioc, $top, $club2='', $pos)
	{   
		if(($this->lpp - $this->linecnt) < 2)		// page break check
		{
			printf("</table>");
			$this->insertPageBreak();
			printf("<table>");
			$this->printHeaderLine($this->bRelay);
		}

		if(!$this->bRelay)		// athlete
		{      
?>
	<tr>
		<td class='enrolmt_tic'>[&nbsp;&nbsp;&nbsp;]</td>
		<td class='enrolmt_nbr'><?php echo $nbr; ?></td> 
		<td class='enrolmt_name'><?php echo $name; ?></td>
		<td class='enrolmt_year'><?php echo $year; ?></td>
		<td class='enrolmt_year'><?php echo $ioc; ?></td>
		<td class='enrolmt_club'><?php echo $club; ?></td>
		<td class='enrolmt_top'><?php echo $top; ?></td>
	</tr>
<?php

		}
		else		// relay
		{     
			$headerline = str_pad("", $GLOBALS['cfgPrtEnrolementLine']['tic'])
									. str_pad($GLOBALS['strName'], $GLOBALS['cfgPrtEnrolementLine']['name'])
									. str_pad($GLOBALS['strClub'], $GLOBALS['cfgPrtEnrolementLine']['club']);
?>
	<tr>
		<td class='enrolmt_tic'>[&nbsp;&nbsp;&nbsp;]</td>
		<td class='enrolmt_nbr'><?php echo $nbr; ?></td>
        <td class='enrolmt_pos'><?php echo $pos; ?></td>   
		<td class='enrolmt_name'><?php echo $name; ?></td>
		<td class='enrolmt_year'><?php echo $year; ?></td>
		<td class='enrolmt_year'><?php echo $ioc; ?></td>
		<td class='enrolmt_club'><?php echo $club; ?></td>
		<td class='enrolmt_club'><?php echo $club2; ?></td>
	</tr>
<?php
		}
		$this->linecnt++;
	}

} // end PRINT_EnrolementPage

/********************************************
 *
 * PRINT_ReceiptEntryPage
 *
 *    Class to print receipt entry lists
 *
 *******************************************/


class PRINT_ReceiptEntryPage extends PRINT_Page
{
    function printHeaderLine()
    {   
        if(($this->lpp - $this->linecnt) < 6)        // page break check
        {    
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }
?>
    <tr>
        <th class='receipt'><?php echo $GLOBALS['strReceipt']; ?></th>  
         <br>&nbsp;
         <br>    
    </tr>
    <tr>
        <td>&nbsp;
        </td>
    <tr>
    
<?php
        $this->linecnt+=7;  
    }     

    function printLine1($nbr, $name, $year)
    {  
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
            $this->printHeaderLine();
        }  
?>
    <tr>
        <th class='receipt_name'><?php echo $GLOBALS['strName']. ":  " ?></th>
        <td class='receipt_name'><?php echo $name; ?></td>
         <td  class='receipt_name'>&nbsp;</td>  
         <th class='receipt_year'><?php echo $GLOBALS['strYear']. ":  " ?></th>  
        <td class='receipt_year'><?php echo $year; ?></td> 
    </tr>
<?php
       
      $this->linecnt++;  
    }
    
    function printLine2($club,$cat)
    {  
        if(($this->lpp - $this->linecnt) < 4)        // page break check
        {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
            $this->printHeaderLine();
        }
          ?>
    <tr>
        <th class='receipt_club'><?php echo $GLOBALS['strClub']. ":  " ?></th>
        <td class='receipt_club'><?php echo $club; ?></td>
        <td  class='receipt_name'>&nbsp;</td>
         <th class='receipt_cat'><?php echo $GLOBALS['strCategory']. ":  " ?></th>  
        <td class='receipt_cat'><?php echo $cat; ?></td> 
    </tr>
<?php
       $this->linecnt++;   
    }  
    
     function printLine3($disc)
    {      
?>
        <tr>
        <th class='receipt_disc'><?php echo $GLOBALS['strDisciplines']. ":  " ?></th>
        <?php    
         // print seperate lines per discipline 
         
         $disc_arr = split(",",$disc);  
         $i=0;    
         
         foreach ($disc_arr as $key)
            { 
              if(($this->lpp - $this->linecnt) < 4)        // page break check
                {
                printf("</table>");
                $this->insertPageBreak();
                printf("<table>");
                $this->printHeaderLine();
            }  
             $this->linecnt++;
            
             if ($i==0) {     
             ?>                  
                    <td colspan='2' class='receipt_disc'><?php echo $key; ?></td>  
                    </tr>   
             <?php 
             }
             else
                {
               ?>   
                    <tr>
                    <td>&nbsp;</td>  
                    <td colspan='2' class='receipt_disc'><?php echo $key; ?></td>  
                    </tr>  
             <?php 
             } 
             $i++;
         }  
    }  
    
  function printLine4($fee='')
    {   
        if(($this->lpp - $this->linecnt) < 4)        // page break check
            {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table border='1'>");
            $this->printHeaderLine();
        }  
?>
    <tr>
     
        <th class='receipt_fee' ><?php  echo  $GLOBALS['strTotal'] . " " . $GLOBALS['strFee']. ":  " ?></th> 
        <?php
        if ($fee>0){?>
            <td class='receipt_fee'><?php echo $GLOBALS['strCHF'] . " &nbsp;&nbsp;" . $fee. ".00" ?></td>    
        <?php
        }
        ?>
    </tr>
<?php            
        $this->linecnt++;  
    }
    
     function printLine5($date, $place)
    {  
        if(($this->lpp - $this->linecnt) < 4)        // page break check
            {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table border='1'>");
            $this->printHeaderLine();
        }   
?>
    <tr>
     
        <th class='receipt_date' ><?php  echo  $GLOBALS['strDate']. ":  "  ?></th> 
        <td class='receipt_date'><?php  echo $date; ?></td> 
        <td  class='receipt_name'>&nbsp;</td>
         <th class='receipt_place' ><?php  echo  $GLOBALS['strPlace']. ":  "  ?></th> 
        <td class='receipt_place'><?php  echo $place; ?></td>     
       
    </tr>
<?php             
      $this->linecnt++;  
    }

    function printLine6()
    {  
        if(($this->lpp - $this->linecnt) < 4)        // page break check
            {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table border='1'>");
            $this->printHeaderLine();
        }
        
?>
    <tr>   
        <th colspan='2' class='receipt_subscribe' ><?php  echo  $GLOBALS['strSubscribe']. ":  "  ?></th>         
    </tr>
<?php        
          
     $this->linecnt++;  
    } 
    
   
     function  printLineBreak($count)
    {                                  
        if(($this->lpp - $this->linecnt) < 4)        // page break check
            {
            printf("</table>");
            $this->insertPageBreak();
            printf("<table>");
        }  
         
       for ($i=0;$i<$count;$i++){ 
            $this->linecnt++;  
?>    
        <tr>
        <td>&nbsp;
        </td>
        <tr>  
<?php
       }
    }
        

} // end PRINT_EntryPage

} // end AA_CL_PRINT_ENTRYPAGE_LIB_INCLUDED
?>
