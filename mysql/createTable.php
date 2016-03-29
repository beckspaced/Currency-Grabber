#!/usr/bin/php -Cq
<?php

/*
/ create mysql table
*/

require_once(dirname(dirname(__FILE__)) . "/config/config.php");
require_once 'DB.php';

$dsnOptions = array(
    'debug'       => 2,
    'portability' => DB_PORTABILITY_ALL,
);

$oDB =& DB::connect($dsn, $options);
if (PEAR::isError($oDB)) {
    die($oDB->getMessage());
}

/*
/ function 
*/

/**
 * Enter description here...
 *
 * @param string $currency
 * @param array $aCurrencies
 * @return unknown
 */
function createTable ($currency, $currencies) {
	
	global $dsn, $oDB;
	
	$query  = "CREATE TABLE " . $currency . " (id int(11) NOT NULL auto_increment, date char(10) NOT NULL default '01/01/1970', timestamp int(11) NOT NULL default '0'";
	
	foreach ($currencies as $key => $val) {
		
		if ($key != $currency) {
			
			$query .= ", " . $key . "_" . $currency . " float NOT NULL default '0'";
		}
	}
	
	$query .= ", PRIMARY KEY (id))";
	
	$oResult =& $oDB->query($query);
	
	//var_dump($oResult);
	
	if (PEAR::isError($oResult)) {
	    return  $oResult->getMessage();
	}
	else 
	{
		return "Successfully Created!";
	}
	
	
}

$currency = "CHF";
$response = createTable($currency, $currencies);
echo "Table " . $currency . " - " . $response . "\n";

/*
/ loop through available currencies and create tables
*/

/*
foreach ($currencies as $key => $val) {
	
	$response = createTable($key, $currencies);
	echo "Table " . $key . " - " . $response . "\n";
}
*/
?>