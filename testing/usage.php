<?php

require_once 'testADSLInit.php';


//$usage = UsageFactory::Create();

//print_r($usage->totalCurrentUsage(23242005));

//print_r($usage->totalUsageForMonth(23242005,'2012','03'));

//print_r($usage->dailyByMonth(23242005,'2012','06'));

//print_r($usage->monthlyUsage(23242005,6));

//print_r($usage->totalUsageByRange(23242005,'2012','03','01','2012','03','03'));

//print_r($usage->sessionsByMonth(23242005,'2012','06'));

//print_r($usage->getActiveSessions(23242005));

$usage = new UsageController();

//print_r($usage->totalAccountUsage(23242005,"2012","05"));

//print_r($usage->sessionsForDay(23242005,"2012","06","12"));
//print_r($usage->dailyUsageForMonth(23242005, "05"))
print_r($usage->monthlyUsage(23242005));
//print_r($usage->systemUsage('Datapro',"2012","07"));
//print_r($usage->activeSessions(23242005));
?>
