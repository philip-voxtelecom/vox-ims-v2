<?php

require_once('_vox_svlogparser.php');
require_once('_vox_adslservices.php');
require_once($GLOBALS['documentroot'] . '/classes/RestClient.class.php');

class Usage_ruxT2Services extends Usage {

    public function totalCurrentUsage($accountId) {
        $adsl_service = new VoxSVParser();
        $adsl_service->loadMethod('usage', 'subscriber');
        $account = AccountFactory::Create();
        $account->read($accountId);
        $subscriber = $account->username;
        $usageMonth = date('Y-m');
        $param = array(
            "subscriber" => $subscriber,
            "totals" => 'yes',
            "start" => $usageMonth
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseInfo']['status'] != 'OK')
            throw new Exception("Error getting usage for account: " . $result['responseInfo']['message']);
        if (empty($result['payload']['usage']))
            throw new EmptyException("No usage found for account");
        $totalUsage = array("downloads" => 0, "uploads" => 0, "totalUsage" => 0);
        $totalUsage['downloads'] += $result['payload']['usage']['totals']['rx'];
        $totalUsage['uploads'] += $result['payload']['usage']['totals']['tx'];
        $totalUsage['totalUsage'] = $totalUsage['downloads'] + $totalUsage['uploads'];
        return $totalUsage;
    }

    public function totalUsageForMonth($accountId, $year, $month) {
        $adsl_service = new VoxSVParser();
        $adsl_service->loadMethod('usage', 'subscriber');
        $usageMonth = "$year-$month";
        $account = AccountFactory::Create();
        $account->read($accountId);
        $subscriber = $account->username;
        $param = array(
            "subscriber" => $subscriber,
            "totals" => 'yes',
            "start" => $usageMonth
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseInfo']['status'] != 'OK')
            throw new Exception("Error getting usage for account: " . $result['responseInfo']['message']);
        if (empty($result['payload']['usage']))
            throw new EmptyException("No usage found for account");
        $totalUsage = array("usageMonth" => $usageMonth, "downloads" => 0, "uploads" => 0, "totalUsage" => 0);
        $totalUsage['downloads'] += $result['payload']['usage']['totals']['rx'];
        $totalUsage['uploads'] += $result['payload']['usage']['totals']['tx'];
        $totalUsage['totalUsage'] = $totalUsage['downloads'] + $totalUsage['uploads'];
        return $totalUsage;
    }

    public function dailyByMonth($accountId, $year, $month) {
        $adsl_service = new VoxSVParser();
        $adsl_service->loadMethod('usage', 'subscriber');
        $account = AccountFactory::Create();
        $account->read($accountId);
        $subscriber = $account->username;
        $usageMonth = "$year-$month";
        $startday = $usageMonth . "-01";
        $endday = $usageMonth . "-" . date('t', strtotime("$year-$month"));
        $param = array(
            "subscriber" => $subscriber,
            "start" => $startday,
            "end" => $endday
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseInfo']['status'] != 'OK')
            throw new Exception("Error getting usage for account: " . $result['responseInfo']['message']);
        if (empty($result['payload']['usage']))
            throw new EmptyException("No usage found for account");
        
        $totalUsage = array("usageMonth" => $usageMonth, 'days' => array());
        $totalUsage['totals']['downloads'] = 0;
        $totalUsage['totals']['uploads'] = 0;
        $totalUsage['totals']['total'] = 0;
        foreach ($result['payload']['usage']['periods'] as $period => $session) {
            $totalUsage['days'][$period] = array(
                "downloads" => $session['totals']['rx'],
                "uploads" => $session['totals']['tx'],
                "totalUsage" => $session['totals']['rx'] + $session['totals']['tx']
            );
        }
        $totalUsage['totals']['downloads'] = $result['payload']['usage']['totals']['rx'];
        $totalUsage['totals']['uploads'] = $result['payload']['usage']['totals']['tx'];
        $totalUsage['totals']['total'] = $result['payload']['usage']['totals']['rx'] + $result['payload']['usage']['totals']['tx'];
        return $totalUsage;
    }

    public function sessionsByMonth($accountId, $year, $month) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('usage', 'accountsessionsbymonth');
        $usageMonth = "$year-$month";
        $param = array(
            "accountId" => $accountId,
            "month" => $usageMonth
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error getting usage for account: " . (isset($result['message']) ? $result['message'] : ""));
        if (empty($result['sessions']))
            throw new EmptyException("No sessions found for account");
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
            throw new EmptyException("No sessions found for account");
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
        $adsl_service = new VoxSVParser();
        $adsl_service->loadMethod('usage', 'subscriber');
        $account = AccountFactory::Create();
        $account->read($accountId);
        $subscriber = $account->username;
        $lastmonth = date("Y-m", strtotime("-1 month"));
        $monthsago = date("Y-m", strtotime("-$numberOfMonths  month"));
        $param = array(
            "subscriber" => $subscriber,
            "start" => $monthsago,
            "end" => $lastmonth
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseInfo']['status'] != 'OK')
            throw new Exception("Error getting usage for account: " . $result['responseInfo']['message']);
        if (empty($result['payload']['usage']))
            throw new EmptyException("No usage found for account");
        $totalUsage = array();

        foreach ($result['payload']['usage']['periods'] as $month => $session) {
            if (isset($totalUsage[$month])) {
                $totalUsage[$month]['downloads'] += $session['totals']['rx'];
                $totalUsage[$month]['uploads'] += $session['totals']['tx'];
                $totalUsage[$month]['totalUsage'] += $session['totals']['rx'] + $session['totals']['tx'];
            } else {
                $thismonth = explode('-', $month);
                $totalUsage[$month] = array(
                    "month" => $thismonth[1],
                    "year" => $thismonth[0],
                    "downloads" => $session['totals']['rx'],
                    "uploads" => $session['totals']['tx'],
                    "totalUsage" => $session['totals']['rx'] + $session['totals']['tx']
                );
            }
        }

        return $totalUsage;
    }

    public function totalUsageByRange($accountId, $startYear, $startMonth, $startDay, $endYear, $endMonth, $endDay) {
        $adsl_service = new VoxSVParser();
        $adsl_service->loadMethod('usage', 'subscriber');
        $account = AccountFactory::Create();
        $account->read($accountId);
        $subscriber = $account->username;
        $param = array(
            "subscriber" => $subscriber,
            "start" => "$startYear-$startMonth-$startDay",
            "end" => "$endYear-$endMonth-$endDay",
            "totals" => 'yes'
        );
        $result = $adsl_service->call_method($param);
        if ($result['responseInfo']['status'] != 'OK')
            throw new Exception("Error getting usage for account: " . $result['responseInfo']['message']);
        if (empty($result['payload']['usage']))
            throw new EmptyException("No usage found for account");
        $totalUsage = array("downloads" => 0, "uploads" => 0, "totalUsage" => 0);
        $totalUsage['downloads'] += $result['payload']['usage']['totals']['rx'];
        $totalUsage['uploads'] += $result['payload']['usage']['totals']['tx'];
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
            throw new EmptyException("No usage found for account");
        if (isset($result['usages']['accountId'])) {
            $fix = $result['usages'];
            unset($result);
            $result = array('usages' => array($fix));
        }
        $usages = array();
        $usages['systemTotal'] = array("downloads" => 0, "uploads" => 0, "totalUsage" => 0);
        foreach ($result['usages'] as $usage) {
            $usages['accounts'][$usage['accountId']]['username'] = $usage['username'];
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
        $serviceURL = $GLOBALS['config']->rux_session_url;
        $service = new restclient();
        $service->username = $GLOBALS['config']->rux_username;
        $service->password = $GLOBALS['config']->rux_password;
        $service->url = "$serviceURL";
        try {
            $sessions = $service->callMethod(array('account_id' => "$accountId"));
        } catch (Exception $e) {
            throw new Exception("Error getting active sessions");
        }
        if (count($sessions) < 1) {
            throw new EmptyException("No active sessions found");
        }
        foreach ($sessions as $key => $session) {
            $sessions[$key]['downloads'] = $session['outputbytes'];
            $sessions[$key]['uploads'] = $session['inputbytes'];
            $sessions[$key]['totalUsage'] = $session['outputbytes'] + $session['inputbytes'];
        }
        return $sessions;
    }

}

?>
