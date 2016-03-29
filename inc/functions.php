<?php

function getLastDate($table, $field) {

    global $oDB, $_Debug;

    $lastDate = $oDB->getOne('SELECT timestamp FROM ' . $table . ' WHERE ' . $field . ' > 0  ORDER BY timestamp DESC');

    if (!PEAR::isError($lastDate)) {
         
        if ($lastDate == null) {
            	
            $lastDate = strtotime(date("m/d/y"));
        }

        return $lastDate;
    }
    else
    {
        die($lastDate->getMessage());
    }
}

function checkRateExists($table, $field, $date) {

    global $oDB, $_Debug;
    $float = 0;

    $float = $oDB->getOne('SELECT ' . $field . ' FROM ' . $table . ' WHERE timestamp = ?', strtotime($date));

    return ($float > 0) ? true : false ;

}

function insertRate($table, $field, $r, $d) {

    global $oDB, $_Debug;


    if ( !is_array($r) ) {
        if($_Debug >= 2) fwrite(STDOUT, "-- Rate is no array doing transform into array\n");
        $rate[] = $r;
    }

    if ( !is_array($d) ) {
        if($_Debug >= 2) fwrite(STDOUT, "-- Date is no array doing transform into array\n");
        $date[] = $d;
    }
    
    //var_dump($rate);
    //var_dump($date);


    if($_Debug >= 2) fwrite(STDOUT, "-- Start loop through array Rate/Date\n");
    for ($i=0;$i<count($rate);$i++) {

        $query  = 'SELECT id FROM ' . $table . ' WHERE timestamp = ?';
        $data = strtotime($date[$i]);

        $oResult = $oDB->query($query, $data);

        if($_Debug >= 2) fwrite(STDOUT, "-- Checking date " . $date[$i] . " already exists in table " . $table . "\n");
        //echo $oDB->last_query . "\n";

        if ($oResult->numRows() > 0) {
            	
            while ($oResult->fetchInto($row, DB_FETCHMODE_ASSOC)) {
                $insertID = $row['id'];
            }
            if($_Debug >= 2) fwrite(STDOUT, "-- Date " . $date[$i] . " already exists in table " . $table . " got ID " . $insertID . "\n");
        }
        else
        {
            $query = "INSERT INTO " . $table . " (date,timestamp) VALUES (?,?)";
            $data = array($date[$i],strtotime($date[$i]));
            	
            $oResult = $oDB->query($query, $data);
            $insertID = $oDB->nextId($table);
            if($_Debug >= 1) fwrite(STDOUT, "-- Date " . $date[$i] . " doesn't exist in table " . $table . "\n");
            if($_Debug >= 1) fwrite(STDOUT, "-- Doing Date insert and received ID " . $insertID . " via DB sequence\n");
        }

        if($_Debug >= 2) fwrite(STDOUT, "-- Checking if Rate > 0 already exists in table " . $table . "\n");

        if (checkRateExists($table, $field, $date[$i]) == false) {
            	
            $query = "UPDATE " . $table . " SET " . $field . " = ? WHERE id = ?";
            $data = array($rate[$i], $insertID);
            $oResult = $oDB->query($query, $data);
            if($_Debug >= 2) fwrite(STDOUT, "-- Rate " . $rate[$i] . " doesn't exists in table " . $table . "\n");
            if($_Debug >= 1) fwrite(STDOUT, "-- Updating Rate " . $rate[$i] . " in table " . $table . "\n");
        }
        else
        {
            if($_Debug >= 2) fwrite(STDOUT, "-- Rate > 0 already exists in table " . $table . " skipping update\n");
        }
    }
    if($_Debug >= 2) fwrite(STDOUT, "-- End loop through array Rate/Date\n");
}

function returnDates($fromdate, $todate) {
    $fromdate = \DateTime::createFromFormat('m/d/y', $fromdate);
    $todate = \DateTime::createFromFormat('m/d/y', $todate);
    return new \DatePeriod(
        $fromdate,
        new \DateInterval('P1D'),
        $todate->modify('+1 day')
    );
}

function returnDates2($begin,$end){

    $begin = new DateTime($begin);

    $end = new DateTime($end.' +1 day');

    $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);

    foreach($daterange as $date){
        $dates[] = $date->format("m/d/y");
    }
    return $dates;

}

function getExchangeRates($currency, $currencies) {

    global $requestURL, $config, $_Debug, $exchange_rates;
    //global $queryString, $requestURL;
    
    // we send notify emails in a loop, so prevent send multiple times
    $mail_is_send = false;

    

    foreach ($currencies as $key => $val) {

        $date = array();
        $rate = array();

        if($_Debug >= 2) fwrite(STDOUT, "-- Loop through currencies with currency " . $currency . "\n");
        
        if($_Debug >= 2) fwrite(STDOUT, "-- Current Currency " . $currency . "\n");
        if($_Debug >= 2) fwrite(STDOUT, "-- Current For each currencies key " . $key . "\n");

        if ($currency != $key) {
            	
            
            	
            $startDate = date("m/d/y", getLastDate($currency, $key . "_" . $currency )+86400);
            //$startDate = "10/01/12";
            if($_Debug >= 2) fwrite(STDOUT, "-- Get start date from DB -> " . $startDate . " (plus 1 day!)\n");
            
            $endDate = date("m/d/y");
            //$endDate = "10/29/12";
            if($_Debug >= 2) fwrite(STDOUT, "-- Get end date " . $endDate . "\n");
            
            // here we insert the loop through each day
            
            //$datePeriod = returnDates2($startDate, $endDate);
            $datePeriod = returnDates($startDate, $endDate);
            
            //var_dump($datePeriod);
            //exit();
            
            $i=0;
            
            foreach($datePeriod as $dp) {

                if($_Debug >= 1) fwrite(STDOUT, "-- Found Currency match " . $key . " -> " . $currency . "\n");
                
                
                //echo $date->format('m/d/y'), PHP_EOL;
                
                //if($_Debug >= 2) fwrite(STDOUT, "-- " . $dp->format('m/d/y') . "\n");
                if($_Debug >= 2) fwrite(STDOUT, "-- foreach dateperiod " . $dp->format('Y-m-d') . "\n");
                //fwrite(STDOUT, "-- " . $date . "\n");
                
                // http://www.xe.com/currencytables/?from=THB&date=2016-03-23
                
                
                
                $xe_com_requestURL = $requestURL . "from=" . $currency . "&date=" . $dp->format('Y-m-d');
                //$xe_com_requestURL = "http://www.xe.com/currencytables/?from=THB&date=2016-04-23";
                
                if($_Debug >= 2) fwrite(STDOUT, "-- requestURL " . $xe_com_requestURL . "\n");
                
                if ( !isset($exchange_rates[$dp->format('Y-m-d')]) ) {
                    
                    if ( $i>0 ) {
                        
                        // let's pause a bit with sending requests to www.xe.com
                        
                        $pause = rand($config['randmin'], $config['randmax']);
                        
                        if($_Debug >= 1) fwrite(STDOUT, "-- Sleep for " . $pause . " seconds before doing next request\n\n");
                        sleep($pause);
                    
                    }
                    
                    $html = file_get_html($xe_com_requestURL);
                    
                    $table = $html->find('table',0);
                    
                    //var_dump($table->attr['id']);
                    
                    if ( $table->attr['id'] == "historicalRateTbl" ) {
                        
                        $rowData = array();
                        
                        foreach($table->find('tr') as $row) {
                            // initialize array to store the cell data from each row
                            //$currency = array();
                        
                            $currency_code = "";
                            $currency_rate = "";
                        
                            $c = 0;
                        
                            foreach($row->find('td') as $cell) {
                                // push the cell's text to the array
                                //var_dump($cell->plaintext);
                        
                                if ( $c == 0) $currency_code = $cell->plaintext;
                                if ( $c == 3) $currency_rate = $cell->plaintext;
                        
                                //if ($c == 0 || $c == 3) $currency[] = $cell->plaintext;
                                $c++;
                            }
                            $rowData[$currency_code] = $currency_rate;
                        }
                        
                        $exchange_rates[$dp->format('Y-m-d')] = $rowData;
                    }
                    else 
                    {
                        $exchange_rates[$dp->format('Y-m-d')] = false;
                        
                        if($_Debug >= 2) fwrite(STDOUT, "-- Error simple_html_dom.php: NO \$TABLE DOM AVAILABLE \n");
                        
                        if ($mail_is_send == false)
                        {
                            $mail_subject = $_SERVER['SERVER_ADDR'] . " Oanda Script - simple html dom Request Error";
                            $mail_message = $_SERVER['SERVER_ADDR'] . " -- Error simple_html_dom.php: NO \$TABLE DOM AVAILABLE ";
                            mail($config['email'], $mail_subject, $mail_message);
                        
                            $mail_is_send = true;
                        }
                    }
                    
                }
                
                
                if ( $exchange_rates[$dp->format('Y-m-d')] ) {
                    
                    /**
                     [0]=>string(9) "Thai Baht"
                     [1]=>string(3) "THB"
                     [2]=>string(7) "0.03073"
                     [3]=>string(7) "32.6358"
                     **/
                    
                    //$exchangeRate = $data[3];
                    $exchangeRate = $exchange_rates[$dp->format('Y-m-d')][$key];
                    
                    if($_Debug >= 2) fwrite(STDOUT, "-- Got values Date: " . $dp->format('m/d/y') . " and Exchange Rate: " . $exchangeRate . "\n");
                    //fwrite(STDOUT, "-- Got values Date: " . $date . " and Exchange Rate: " . $exchangeRate . "\n");
                    
                    if($_Debug >= 2) fwrite(STDOUT, "-- Cast string exchangerate as floatval: " . $exchangeRate . "\n");
                    
                    $exchangeRate = floatval($exchangeRate);
                    
                    if($_Debug >= 2) fwrite(STDOUT, "-- Check value exchange Rate: " . $exchangeRate . " greater than 0\n");
                    
                    if ($exchangeRate > 0) { //
                         
                        if($_Debug >= 2) fwrite(STDOUT, "-- Success Exchange Rate > 0 adding values Date: " . $dp->format('m/d/y') . " and Rate: " . $exchangeRate . " into array\n");
                        //fwrite(STDOUT, "-- Success Exchange Rate > 0 adding values Date: " . $date . " and Rate: " . $exchangeRate . " into array\n");
                    
                        //var_dump($date);
                    
                        //$currentDate = date('m/d/Y', $date->date);
                        //$currentDate = date('m/d/Y', $date);
                    
                        //var_dump($currentDate);
                    
                        $date = $dp->format('m/d/Y');
                        $rate = (string)$exchangeRate;
                    
                    }
                    else
                    {
                        if($_Debug >= 2) fwrite(STDOUT, "-- Value Rate: " . $pair[1] . " equals 0 skip adding into array\n");
                    }
                    
                    if($_Debug >= 2) fwrite(STDOUT, "-- Calling function insertRate with following params\n");
                    
                    //var_dump($date);
                    
                    if($_Debug >= 2) fwrite(STDOUT, "-- param " . $currency . "\n");
                    if($_Debug >= 2) fwrite(STDOUT, "-- param " . $key . "_" . $currency . "\n");
                    if($_Debug >= 2) fwrite(STDOUT, "-- param " . $rate . "\n");
                    if($_Debug >= 2) fwrite(STDOUT, "-- param " . $date . "\n");
                    
                    if($_Debug >= 2) fwrite(STDOUT, "-- end loop \n\n");
                    
                    insertRate($currency, $key . "_" . $currency, $rate, $date);
                    
                    //fwrite(STDOUT, "-- doing sleep 1 second\n");
                    //sleep(1);
                    
                } // end if $exchange_rates
                
                //var_dump($exchangeRate);
                //exit();
                
                $i++;
                
            } // end foreach dateperiod

            //exit();
            //break;

		}
		else
		{
			if($_Debug >= 2) fwrite(STDOUT, "-- Skip currency pair " . $currency . " -> " . $key . "\n");
		}
	}
	return true;

}

?>