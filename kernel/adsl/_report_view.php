<?php

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

require_once('_account_model.php');
require_once('_usage_model.php');

if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->view . '/_report_view.php')) {
    include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->view . '/_report_view.php');
}

class ReportView extends View {

    public function display($viewarray = NULL) {
        ;
    }

    public function summary($viewobject=null) {
        $accountList = new AccountController();
        $accounts = $accountList->listall();
        $count = count($accounts);

        $accountmap = array();
        foreach ($accounts as $account) {
            $accountmap[$account->id()] = $account->username;
        }

        $year = date('Y');
        $month = date('m');
        $owner = $GLOBALS['login']->getLoginId();

        $usage = new UsageController();
        try {
            $allusage = $usage->systemUsage($owner, $year, $month);
            foreach ($allusage['accounts'] as $key => $accountusage) {
                if (isset($accountmap[$key])) {
                    $allusage['accounts'][$key]['username'] = $accountmap[$key];
                } else {
                    $allusage['accounts'][$key]['username'] = $accountusage['username']." (CXD)";
                    $allusage['accounts'][$key]['status'] = 'CANCELLED';
                }
            }
        } catch (EmptyException $e) {
            $allusage['systemTotal']['downloads'] = 0;
            $allusage['systemTotal']['uploads'] = 0;
            $allusage['systemTotal']['totalUsage'] = 0;
        }

        $viewobject = new SimpleXMLElement('<root/>');
        $data = $viewobject->addChild('data');



        /*
          foreach ($accounts as $account) {
          append_simplexml($data, $account->asXML());
          }

          $accounts = array();
          foreach ($viewobject->data->children() as $element) {
          //$id = (string) $element->id;
          $username = (string) $element->username;
          $account = array();
          foreach ($element->children() as $param) {
          $account[$param->getName()] = (string) $param;
          }
          array_push($accounts, $account);
          }
         * 
         */
        ksort($allusage['accounts']);
        $allusage['count'] = $count;
        //todo should return XML data
        return $allusage;
    }


}

class ReportViewFactory {

    public static function Create($var = null) {
        $required_class = "ReportView_" . $GLOBALS['config']->view;
        if (class_exists($required_class)) {
            return new $required_class($var);
        } else {
            return new ReportView($var);
        }
    }

}

?>