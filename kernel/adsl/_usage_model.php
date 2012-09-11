<?php
$model = 'usage';

load_provider_model($model);

class EmptyException extends Exception { };

abstract class Usage {
    
    abstract public function totalCurrentUsage($accountId);
    abstract public function totalUsageForMonth($accountId,$year,$month);
    abstract public function dailyByMonth($accountId,$year,$month);
    abstract public function monthlyUsage($accountId,$numberOfMonths);
    abstract public function totalUsageByRange($accountId, $startYear,$startMonth,$startDay,$endYear, $endMonth, $endDay);
    
}

class UsageFactory {

    public static function Create() {
        $required_class = "Usage_" . $GLOBALS['config']->provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No UsageFactory exists for provider");
        }
    }

}
?>
