<?php

require_once('_vox_adslservices.php');

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
            throw new Exception("Error getting usage for account: " . $result['message']);
        if (empty($result['usages']))
            throw new Exception("No usage found for account");
        $totalUsage = array("usageMonth" => $usageMonth, 'days' => array());
        // TODO stupid fix again
        if (isset($result['usages']['outputBytes'])) {
            $fix = $result['usages'];
            unset($result);
            $result = array('usages' => array($fix));
        }
        foreach ($result['usages'] as $usage) {
            $totalUsage['days'][$usage['day']] = array("downloads" => $usage['outputBytes'], "uploads" => $usage['inputBytes'], "totalUsage" => $usage['inputBytes'] + $usage['outputBytes']);
        }
        return $totalUsage;
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
            $month = $usage['year'] . "-" . $usage['month'];
            if (isset($totalUsage[$month])) {
                $totalUsage[$month]['downloads'] += $usage['outputBytes'];
                $totalUsage[$month]['uploads'] += $usage['inputBytes'];
                $totalUsage[$month]['totalUsage'] += $usage['outputBytes'] + $usage['inputBytes'];
            } else {
                $totalUsage[$month] = array(
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

}

?>
