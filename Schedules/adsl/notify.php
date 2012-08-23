<?php

$serviceURL = "http://ims2-devel.voxtelecom.co.za";
$GLOBALS['documentroot'] = dirname(dirname(dirname(__FILE__)));

require_once $GLOBALS['documentroot'] . '/classes/RestClient.class.php';
require_once $GLOBALS['documentroot'] . '/libs/Smarty/Smarty.class.php';

$service = new restclient();
$service->username = 'philip';
$service->password = 'poppett-1';

$callTypes = array('notify' => 1, 'daily' => 1, 'weekly' => 1, 'monthly' => 1);
if ($argc < 2 or $argc > 2 or !isset($callTypes[$argv[1]]))
    die();
$callType = strtolower($argv[1]);

//todo call to return list of owners
$owners = array('IMS-Demo');

$year = date('Y');
$month = date('m');

$smarty = new Smarty();
$smarty->compile_dir = $GLOBALS['documentroot'] . '/templates_c';
$smarty->cache_dir = $GLOBALS['documentroot'] . '/cache';
$smarty->config_dir = $GLOBALS['documentroot'] . '/configs';

foreach ($owners as $owner) {
    $service->url = "$serviceURL/adsl/usage/$owner";
    $usages = $service->callMethod(array('type' => 'owner', 'year' => "$year", 'month' => "$month"));
//print_r($usages);
//die();

    $smarty->template_dir = array(
        $GLOBALS['documentroot'] . '/templates/adsl/notify/' . $owner,
        $GLOBALS['documentroot'] . '/templates/adsl/notify/_DEFAULT_'
    );

    foreach ($usages['reply']['accounts'] as $accountId => $usage) {

        $service->url = "$serviceURL/adsl/account/$accountId";

        $account = $service->callMethod(null);
        //print_r($account);
        if (empty($account['reply']['notifyemail']))
            continue;

        switch ($callType) {
            case 'notify':
                $usedPercent = round($usage['total'] / ($account['reply']['bundlesize'] * 1000000000) * 100);
                $data['percent'] = $usedPercent;
                $data['account'] = $account['reply'];
                if ($usedPercent > 100) {
                    print "usage for $accountId exceeds allowed amount\n";
                    notify($data, 'exceed');
                } elseif ($usedPercent > 75) {
                    print "usage for $accountId exceeds 75% of cap ($usedPercent%)\n";
                    notify($data, 'warn');
                }
                break;
            case 'daily':
            case 'weekly':
            case 'monthly':
                $data['reporttype'] = ucfirst($callType);
                $data['usage'] = $usage;
                $data['account'] = $account['reply'];
                $data['year'] = $year;
                $data['month'] = $month;
                notify($data, $callType);
        }
    }
}

function notify($data, $template) {
    global $smarty;
    $smarty->assign("data", $data);

    $productid = $data['account']['productId'];
    
    if ($smarty->template_exists("$template-$productid.tpl")) {
        $content = $smarty->fetch("$template-$productid.tpl");
    } else {
        $content = $smarty->fetch("$template.tpl");
    }

    $to = $data['account']['notifyemail'];
    $from = "noreply@voxtelecom.co.za";
    $subject = "Report";

    $headers = "From: $from\r\n";
    $headers .= "Content-type: text/html\r\n";

    //mail($to, $subject, $content, $headers);

    print $content . "\n\n";
}

?>
