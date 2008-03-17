<?php

// hide all errors
// error_reporting(0);

// INIT ///////////////////////////////////////

// params
$_db = array (
  'host' => 'localhost',
  'user' => 'root',
  'password' => 'root',
  'database' => 'gms'
);

// possible tables
$poss_tables = array(
	
	't_country' => array(
		'hidden_columns' => array('cnt_iso3166-1_numcode'),
		'editable'       => true,
		'disrows'        => array(),
		'filter'         => true,
		'adding'         => true,
		'deleting'       => true
	   ),
	
	'v_memberfederation' =>  array(
		'hidden_columns' => array(),
		'editable'       => true,
		'disrows'        => array(),
		'filter'         => false,
		'adding'         => false,
		'deleting'       => false
	   ),
	
	'table2' => array()
	
	);

$table  = $_GET['table'];
$cba_id = (int)$_GET['_cba_request_id'];

if (!isset($poss_tables[ $_GET['table'] ]))
   die('_cba.ready ( '.$cba_id.', [false,"Access Denied!"] ); ');

// connect db-interface
require_once( './mod/db_mysql.class.php' );
// init db
$db = new db( $_db['host'], $_db['user'], $_db['password'], $_db['database'] );
unset($_db);
if ( isset($db -> error) ) die($db -> error);

// JATable connect
require_once( './mod/jatable.class.php' );
// init JATable
$jatable = new JATable( $table, $cba_id, $db, $poss_tables[ $_GET['table'] ] );

// BACKEND ///////////////////////////////////////

switch ( $_GET['actiontype'] )
{
   case 'init':
	  $jatable -> init();
	  break;
	  
   case 'scrolling':
	  $filter = ( isset($_GET['filter']) && isset($_GET['filterrow']) ) ? array($_GET['filterrow'],$_GET['filter']) : array();
	  if ( isset($_GET['direction']) && isset($_GET['row']) )
		 $jatable -> scrolling( $_GET['start'], $_GET['count'], $filter, $_GET['direction'],$_GET['row'] );
	  else
		 $jatable -> scrolling( $_GET['start'], $_GET['count'], $filter );
	  break;
   
   case 'editing':
	  $jatable -> editing( $_GET['line'], $_GET['value'], $_GET['row'] );
	  break;
   
   case 'adding':
	  $jatable -> adding();
	  break;
   
   case 'deleting':
	  $jatable -> deleting( $_GET['line'] );
	  break;   
}



?>
