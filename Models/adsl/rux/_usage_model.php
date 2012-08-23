<?php

require_once('_vox_adslservices.php');
require_once($GLOBALS['documentroot'] . '/classes/RestClient.class.php');

class Usage_rux extends Usage {

    public function totalCurrentUsage($accountId) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'totalusage');
        $param = array(
            "accountId" => $accountId
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . $result['message']);
        if (empty($result['usages']))
            throw new Exception("No usage found for account");
        $totalUsage = array("downloads" => 0, "uploads" => 0, "totalUsage" => 0);
        foreach ($result['usages'] as $usage) {
            $totalUsage['downloads'] += $usage['outputBytes'];
            $totalUsage['uploads'] += $usage['inputBytes'];
        }
        $totalUsage['totalUsage'] = $totalUsage['downloads'] + $totalUsage['uploads'];
        return $totalUsage;
    }

    public function totalUsageForMonth($accountId, $year, $month) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'totalusagebymonth');
        $usageMonth = "$year-$month-01";
        $param = array(
            "accountId" => $accountId,
            "usageMonth" => $usageMonth
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . $result['message']);
        if (empty($result['usages']))
            throw new Exception("No usage found for account");
        $totalUsage = array("usageMonth" => $usageMonth, "downloads" => 0, "uploads" => 0, "totalUsage" => 0);
        foreach ($result['usages'] as $usage) {
            $totalUsage['downloads'] += $usage['outputBytes'];
            $totalUsage['uploads'] += $usage['inputBytes'];
        }
        $totalUsage['totalUsage'] = $totalUsage['downloads'] + $totalUsage['uploads'];
        return $totalUsage;
    }

    public function dailyByMonth($accountId, $year, $month) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'dailybymonth');
        $usageMonth = "$year-$month-01";
        $param = array(
            "accountId" => $accountId,
            "usageMonth" => $usageMonth
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . (isset($result['message']) ? $result['message'] : ""));
        if (empty($result['usages']))
            throw new Exception("No usage found for account");
        $totalUsage = array("usageMonth" => $usageMonth, 'days' => array());
        // TODO stupid fix again
        if (isset($result['usages']['outputBytes'])) {
            $fix = $result['usages'];
            unset($result);
            $result = array('usages' => array($fix));
        }
        $totalUsage['totals']['downloads'] = 0;
        $totalUsage['totals']['uploads'] = 0;
        $totalUsage['totals']['total'] = 0;
        foreach ($result['usages'] as $usage) {
            $totalUsage['days'][$usage['day']] = array("downloads" => $usage['outputBytes'], "uploads" => $usage['inputBytes'], "totalUsage" => $usage['inputBytes'] + $usage['outputBytes']);
            $totalUsage['totals']['downloads'] += $usage['outputBytes'];
            $totalUsage['totals']['uploads'] += $usage['inputBytes'];
            $totalUsage['totals']['total'] += $usage['outputBytes'] + $usage['inputBytes'];
        }
        return $totalUsage;
    }

    public function sessionsByMonth($accountId, $year, $month) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'accountsessionsbymonth');
        $usageMonth = "$year-$month-01";
        $param = array(
            "accountId" => $accountId,
            "month" => $usageMonth
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . (isset($result['message']) ? $result['message'] : ""));
        if (empty($result['sessions']))
            throw new Exception("No sessions found for account");
        $allSessions = array("sessionMonth" => $usageMonth, 'sessions' => array());
        // TODO stupid fix again
        if (isset($result['sessions']['id'])) {
            $fix = $result['sessions'];
            unset($result);
            $result = array('sessions' => array($fix));
        }
        foreach ($result['sessions'] as $session) {
            $allSessions['sessions'][$session['id']] = $session;
            $allSessions['sessions'][$session['id']]['downloads'] = $session['outputBytes'];
            $allSessions['sessions'][$session['id']]['uploads'] = $session['inputBytes'];
            $allSessions['sessions'][$session['id']]['totalUsage'] = $session['outputBytes'] + $session['inputBytes'];
        }
        return $allSessions;
    }

    public function sessionsByRange($accountId, $startYear, $startMonth, $startDay, $endYear, $endMonth, $endDay) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'accountsessionsbydaterange');
        $startdate = "$startYear-$startMonth-$startDay";
        $enddate = "$endYear-$endMonth-$endDay";
        $param = array(
            "accountId" => $accountId,
            "startDate" => $startdate,
            "endDate" => $enddate
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . (isset($result['message']) ? $result['message'] : ""));
        if (empty($result['sessions']))
            throw new Exception("No sessions found for account");
        $allSessions = array("sessionStartDate" => $startdate, "sessionEndDate" => $enddate, 'sessions' => array());
        // TODO stupid fix again
        if (isset($result['sessions']['id'])) {
            $fix = $result['sessions'];
            unset($result);
            $result = array('sessions' => array($fix));
        }
        foreach ($result['sessions'] as $session) {
            $allSessions['sessions'][$session['id']] = $session;
            $allSessions['sessions'][$session['id']]['downloads'] = $session['outputBytes'];
            $allSessions['sessions'][$session['id']]['uploads'] = $session['inputBytes'];
            $allSessions['sessions'][$session['id']]['totalUsage'] = $session['outputBytes'] + $session['inputBytes'];
        }
        return $allSessions;
    }

    public function monthlyUsage($accountId, $numberOfMonths) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'bynumberofmonthsforaccount');
        $param = array(
            "accountId" => $accountId,
            "numberOfMonths" => $numberOfMonths
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . $result['message']);
        if (empty($result['usages']))
            throw new Exception("No usage found for account");
        $totalUsage = array();
        // TODO Stupid check to fix stupid service implementation
        if (isset($result['usages']['month'])) {
            $fix = $result['usages'];
            unset($result);
            $result = array('usages' => array($fix));
        }
        foreach ($result['usages'] as $usage) {
            if (strlen($usage['month']) == 1)
                $usage['month'] = '0'.$usage['month'];
            $month = $usage['year'] . "-" . $usage['month'];
            if (isset($totalUsage[$month])) {
                $totalUsage[$month]['downloads'] += $usage['outputBytes'];
                $totalUsage[$month]['uploads'] += $usage['inputBytes'];
                $totalUsage[$month]['totalUsage'] += $usage['outputBytes'] + $usage['inputBytes'];
            } else {
                $totalUsage[$month] = array(
                    "month" => $usage['month'],
                    "year" => $usage['year'],
                    "downloads" => $usage['outputBytes'],
                    "uploads" => $usage['inputBytes'],
                    "totalUsage" => $usage['inputBytes'] + $usage['outputBytes']);
            }
        }

        return $totalUsage;
    }

    public function totalUsageByRange($accountId, $startYear, $startMonth, $startDay, $endYear, $endMonth, $endDay) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'totalusagebydaterange');
        $param = array(
            "accountId" => $accountId,
            "startMonth" => "$startYear-$startMonth-$startDay",
            "endMonth" => "$endYear-$endMonth-$endDay"
        );
        $result = $adsl_service->call_method($param);
        //var_dump($result);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . $result['message']);
        if (empty($result['usages']))
            throw new Exception("No usage found for account");
        $totalUsage = array("downloads" => 0, "uploads" => 0, "totalUsage" => 0);
        foreach ($result['usages'] as $usage) {
            $totalUsage['downloads'] += $usage['outputBytes'];
            $totalUsage['uploads'] += $usage['inputBytes'];
        }
        $totalUsage['totalUsage'] = $totalUsage['downloads'] + $totalUsage['uploads'];
        return $totalUsage;
    }

    public function allUsageByRange($systemId, $startYear, $startMonth, $startDay, $endYear, $endMonth, $endDay) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'bysystemid');
        $param = array(
            "systemId" => $systemId,
            "start" => "$startYear-$startMonth-$startDay",
            "end" => "$endYear-$endMonth-$endDay"
        );
        //TODO remove fake data
        $result = $adsl_service->call_method($param);
        //$fakedata = file_get_contents('/var/www/IMS2/testing/usage.txt');
        //$result = json_decode($fakedata, true);
        //TODO end
        //var_dump($result);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . $result['message']);
        if (empty($result['usages']))
            throw new Exception("No usage found for account");
        if (isset($result['usages']['accountId'])) {
            $fix = $result['usages'];
            unset($result);
            $result = array('usages' => array($fix));
        }
        $usages = array();
        $usages['systemTotal'] = array("downloads" => 0, "uploads" => 0, "totalUsage" => 0);
        foreach ($result['usages'] as $usage) {
            $usages['accounts'][$usage['accountId']]['downloads'] = (string) $usage['outputBytes'];
            $usages['accounts'][$usage['accountId']]['uploads'] = (string) $usage['inputBytes'];
            $usages['accounts'][$usage['accountId']]['total'] = (string) ($usage['outputBytes'] + $usage['inputBytes']);
            $usages['systemTotal']['downloads'] += $usage['outputBytes'];
            $usages['systemTotal']['uploads'] += $usage['inputBytes'];
        }
        $usages['systemTotal']['totalUsage'] = (string) ($usages['systemTotal']['downloads'] + $usages['systemTotal']['uploads']);
        $usages['systemTotal']['downloads'] = (string) $usages['systemTotal']['downloads'];
        $usages['systemTotal']['uploads'] = (string) $usages['systemTotal']['uploads'];

        return $usages;
    }

    public function getActiveSessions($accountId) {
        $serviceURL = 'http://adsl_manager.voxdev.co.za/sessions';
        $service = new restclient();
        $service->username = 'philip';
        $service->password = 'Bpcxsa4%^';
        $service->url = "$serviceURL";
        $sessions = $service->callMethod(array('account_id' => "$accountId"));
        foreach ($sessions as $key => $session) {
            $sessions[$key]['downloads'] = $session['outputbytes'];
            $sessions[$key]['uploads'] = $session['inputbytes'];
            $sessions[$key]['totalUsage'] = $session['outputbytes'] + $session['inputbytes'];
        }
        return $sessions;
    }

}

?>
