# Currency-Grabber
Grab daily currency exchange rates

A tiny script to grab daily currency exchange rates.
currently it uses xe.com to grap the daily currency rates
in the past it used oanda.com as source but they changed their format a while ago

the script is run daily via cronjob, e.g.

0 6 * * * /path/to/currency-grabber.php

the data is stored in the database. the storage tables can be created via script in /mysql/createTable.php

have a look in the source code on how to set the $currency to create the necessary tables to store the exchange rates

all is BETA status and if you have any questions then please ask

thanks & greetings
becki

