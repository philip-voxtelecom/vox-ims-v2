<?php

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

require_once('_usage_model.php');

//require_once('_usage_view.php');

class UsageController {

    protected $usage;

    public function dailyUsageForMonth($accountId, $month, $year = NULL) {
        $this->usage = UsageFactory::Create();
        if (empty($year)) {
            $year = date('Y');
        }

        if (!checkdate($month, '01', $year))
            throw new Exception("Invalid date specification");

        $usage = $this->usage->dailyByMonth($accountId, $year, $month);
        return $usage;
    }

    public function totalAccountUsage($accountId, $year = NULL, $month = NULL, $day = NULL) {
        $this->usage = UsageFactory::Create();

        if (empty($year)) {
            return $this->usage->totalCurrentUsage($accountId);
        } elseif (empty($month)) {
            if (!checkdate('01', '01', $year))
                throw new Exception("Invalid date specification");
            return $this->usage->totalUsageByRange($accountId, "$year", "01", "01", $year, "12", "31");
        } elseif (empty($day)) {
            if (!checkdate($month, '01', $year))
                throw new Exception("Invalid date specification");
            return $this->usage->totalUsageForMonth($accountId, $year, $month);
        } else {
            if (!checkdate($month, $day, $year))
                throw new Exception("Invalid date specification");
            return $this->usage->totalUsageByRange($accountId, "$year", "$month", "$day", $year, "$month", "$day");
        }
    }

    public function monthlyUsage($accountId,$numberOfMonths = 12){
        $this->usage = UsageFactory::Create();
        $usage = $this->usage->monthlyUsage($accountId,$numberOfMonths);
        return  $usage;
        
    }
}

?>
