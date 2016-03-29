#!/usr/bin/php -Cq
<?php

/**
 * @link https://github.com/joshuaulrich/quantmod/issues/36
 * @link http://www.oanda.com/currency/table
 */

set_time_limit(0);

require_once(dirname(__FILE__) . "/config/config.php");
require_once(dirname(__FILE__) . "/inc/functions.php");
require_once(dirname(__FILE__) . "/inc/simple_html_dom.php");
require_once "Benchmark/Timer.php";
require_once 'DB.php';

/**
 * here we store our exchange rates
 * one table with all currencies per day
 * http://www.xe.com/currencytables/?from=THB&date=2016-03-23
 */
$exchange_rates = array();

/*
 / Currencies we want to get
 */

//$currency = array("THB","CHF");
$currency = array("THB");

/*
 / DB options
*/

$dsnOptions = array(
    'debug'       => 2,
    'portability' => DB_PORTABILITY_ALL,
);

/**
 * set debug level 0 / 1 / 2
 * level 0 = no output at all
 * level 1 = only critical activities will be written
 * level 2 = all activities will be written
 */

$_Debug = 2;

if($_Debug > 0) fwrite(STDOUT, "-- Start Oanda Script!\n");


$oBenchmark = new Benchmark_Timer;
$oBenchmark->start();

if($_Debug > 0) fwrite(STDOUT, "-- Init Benchmark Timer Start\n");

$oDB =& DB::connect($dsn, $dsnOptions);
if (PEAR::isError($oDB)) {
    die($oDB->getMessage());
}

if($_Debug > 0) fwrite(STDOUT, "-- Init DB Object and Connect\n");

//$oRequest = new HTTP_Request($requestURL);
//$oRequest->setMethod(HTTP_REQUEST_METHOD_GET);
//$oRequest->addHeader('User-Agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

//if($_Debug > 0) fwrite(STDOUT, "-- Init Object HTTP Request\n");

foreach ($currency as $k) {

    if($_Debug > 0) fwrite(STDOUT, "-- Get Exchange Rates for Currency " . $k . "\n\n");
    getExchangeRates($k, $currencies);
}

$oBenchmark->stop();
if($_Debug > 0) fwrite(STDOUT, "-- Benchmark Timer Stop\n");
if($_Debug > 0) fwrite(STDOUT, "-- Benchmark Timer Display\n");
$oBenchmark->display();



?>