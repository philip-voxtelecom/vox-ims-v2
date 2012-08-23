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
    
    public function sessionsForDay($accountId,$year = null,$month = null,$day = null) {
        $this->usage = UsageFactory::Create();
        if (empty($year)) {
            $year = date('Y');
        }
        if (empty($month)) {
            $month = date('m');
        }
        if (empty($day)) {
            $day = date('d');
        }
        
        if(strlen($month) == 1){
            $month = '0'.$month;
        }
        if(strlen($day) == 1){
            $day = '0'.$day;
        }
        //$allSessions = $this->usage->sessionsByRange($accountId,"$year","$month","$day","$year","$month","$day");
        $allSessions = $this->usage->sessionsByMonth($accountId,"$year","$month");
        foreach ($allSessions['sessions'] as $id => $session) {
            if (!preg_match("/^$year-$month-$day.*/", $session['startTime'])) {
                unset($allSessions['sessions'][$id]);
            }
        }
        ksort($allSessions['sessions']);
        return $allSessions;
    }
    
    public function activeSessions($accountId) {
        $this->usage = UsageFactory::Create();
        $sessions = $this->usage->getActiveSessions($accountId);
        return $sessions;

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
    
    public function systemUsage($systemId,$year, $month = NULL, $day = NULL){
        $this->usage = UsageFactory::Create();

        if (empty($month)) {
            if (!checkdate('01', '01', $year))
                throw new Exception("Invalid date specification");
            return $this->usage->allUsageByRange($systemId, "$year", "01", "01", $year, "12", "31");
        } elseif (empty($day)) {
            if (!checkdate($month, '01', $year))
                throw new Exception("Invalid date specification");
            $lastday = cal_days_in_month ( CAL_GREGORIAN , $month , $year );
            return $this->usage->allUsageByRange($systemId, $year, $month, "01", $year, $month, $lastday);
        } else {
            if (!checkdate($month, $day, $year))
                throw new Exception("Invalid date specification");
            return $this->usage->allUsageByRange($systemId, "$year", "$month", "$day", $year, "$month", "$day");
        }
        
    }
}

?>
