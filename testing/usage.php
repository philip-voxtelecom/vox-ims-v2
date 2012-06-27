<?php

require_once 'testADSLInit.php';

$usage = UsageFactory::Create();

//print_r($usage->totalCurrentUsage(23242005));

//print_r($usage->totalUsageForMonth(23242005,'2012','03'));

//print_r($usage->dailyByMonth(23242005,"2012-06-01"));

//print_r($usage->monthlyUsage(23242005,6));

print_r($usage->totalUsageByRange(23242005,'2012','03','01','2012','03','03'));

//$usage = new UsageController();

//print_r($usage->totalAccountUsage(23242005,"2012","05"));

//print_r($usage->dailyUsageForMonth(23242005, "05"))
//print_r($usage->monthlyUsage(23242005));
?>
