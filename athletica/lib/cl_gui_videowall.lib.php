<?php

if (!defined('AA_CL_GUI_VIDEOWALL_LIB_INCLUDED'))
{
	define('AA_CL_GUI_VIDEOWALL_LIB_INCLUDED', 1);


/********************************************
 *
 * CLASS GUI_Videowall
 *
 * Outputs a dynamic Screen for presentation over a video wall (beamer)
 *
 *******************************************/

class GUI_Videowall{
	
	var $stylesheet;
	var $title;
	var $settings;
	
	function GUI_Videowall($title)
	{
		$this->title = $title;
		$this->stylesheet = "videowall.css";
		$this->printHTMLHeader();
	}

	function printHTMLHeader()
	{
?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Refresh" CONTENT="3;URL=speaker_videowall_show.php">
<title><?php echo $this->title; ?></title>
<link rel="stylesheet"
	href="css/<?php echo $this->stylesheet; ?>" type="text/css">
</head>
<?php
	}



	function startPage()
	{
?>
<script type="text/javascript">

</script>
<body>
<table width="1024px" height="768px" border=0 cellpadding=0 cellspacing=0>
<tr>
	
	<td class='forms' height="10%">
	<span style="font-size:50px;">asdas  </span>
	</td>
</tr>
<tr>
	
	<td class='forms' height="90%">
	<span style="font-size:45px;">asdas  </span>
	</td>
</tr>
</table>
<?php
	}
	
	
	function endPage(){
?>
</body>
</html>
<?php
	}
	
	
}

}
?>
