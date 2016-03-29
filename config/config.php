<?php

/*
/ Config File Oanda
*/

$config['email'] = "name@yourdomain.com";
$config['randmin'] = 5;
$config['randmax'] = 10;

/* 
/ PEAR DB DSN Data Source Name
*/

$dsn = array(
    'phptype'  => 'mysql',
    'username' => 'user',
    'password' => 'pass',
    'hostspec' => 'localhost',
    'database' => 'currency-rates',
);

/*
/ Oanda request URL
*/

//$requestURL = "http://www.oanda.com/convert/fxhistory?";
//$requestURL = "http://www.oanda.com/currency/table?";
$requestURL = "http://www.xe.com/currencytables/?";

/*
/ Oanda query string
*/

/**
$queryString['lang'] = "en";
$queryString['date_fmt'] = "us";
$queryString['margin_fixed'] = "0";
$queryString['SUBMIT'] = "Get+Table";
$queryString['format'] = "CSV";
$queryString['redirected'] = "1";
$queryString['exch2'] = ""; // no idea for what this is needed
$queryString['expr2'] = ""; // no idea for what this is needed
**/

//$queryString['date_fmt'] = "us";
//$queryString['format'] = "CSV";
//$queryString['format'] = "ASCII";
//$queryString['redirected'] = "1";

/*
/ Currencies
*/

$currencies = array(
		"USD" => "US Dollar",
		"AUD" => "Australian Dollar",
		"GBP" => "British Pound",
		"CAD" => "Canadian Dollar",
		"DKK" => "Danish Krone",
		"EUR" => "Euro",
		"HKD" => "Hong Kong Dollar",
		"JPY" => "Japanese Yen",
		"SGD" => "Singapore Dollar",
		"SEK" => "Swedish Krona",
		"CHF" => "Swiss Franc",
		"THB" => "Thai Baht",
		"MYR" => "Malaysian Ringit",
		"RUB" => "Russian Ruble",
		"NOK" => "Norwegian Kroner",
		"NZD" => "New Zealand Dollar",
		"INR" => "Indian Rupee",
		"IDR" => "Indonesian Rupiah",
		"KHR" => "Cambodian Riel",
		"MMK" => "Myanmar Kyat"
		);


?>