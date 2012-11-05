<?php

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

require_once('_account_model.php');

if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->view . '/_account_view.php')) {
    include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->view . '/_account_view.php');
}

class AccountView extends View {

    public $offset;
    public $limit;
    public $search;

    public function display($viewarray = NULL) {
        ;
    }

    protected function setPage($viewarray) {

        $this->offset = 0;
        $this->limit = $GLOBALS['config']->displayRowLimit;
        $this->search = NULL;

        if (isset($viewarray['offset']))
            $this->offset = $viewarray['offset'];

        if (isset($viewarray['limit']))
            $this->limit = $viewarray['limit'];

        if (isset($viewarray['search']))
            $this->search = $viewarray['search'];
    }

    public function listall($viewarray = NULL) {

        $this->setPage($viewarray);

        if (isset($viewarray['init'])) {
            $accounts = array();
        } else {
            $accountList = new AccountController();
            $accounts = $accountList->listall($this->offset, $this->limit, $this->search);
        }
        
        $viewobject = new SimpleXMLElement('<root/>');
        $data = $viewobject->addChild('data');
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

        return $accounts;
    }

    public function status($viewarray = NULL) {

        $userId = $viewarray['id'];

        $account = new AccountController();
        $account->read($userId);

        $viewobject = new ViewObject('<root/>');
        $data = $viewobject->addChild('data');
        $data->addChild('status', $account->status());
        $data->addChild('id', $userId);

        $options = $viewobject->addChild('options');

        $option = $options->addChild('option');
        $option->addChild('name', 'Active');
        $option->addChild('value', 'active');

        $option = $options->addChild('option');
        $option->addChild('name', 'Suspended');
        $option->addChild('value', 'suspended');

        /*
          if ($GLOBALS['login']->isAdmin()) {

          $option = $options->addChild('option');
          $option->addChild('name', 'Cancelled');
          $option->addChild('value', 'cancelled');
          }
         * 
         */

        return $viewobject->asXML();
    }

    public function topup($viewarray = NULL) {
        $userId = $viewarray['id'];

        $account = new AccountController();
        $account->read($userId);

        $viewobject = new ViewObject('<root/>');
        $data = $viewobject->addChild('data');
        $data->addChild('id', $userId);
        foreach ($account->properties() as $key => $value) {
            if (!empty($value)) {
                $data->addChild($key, $value);
            }
        }
        return $viewobject->asXML();
    }

    public function delete($viewarray = NULL) {
        $userId = $viewarray['id'];

        $account = new AccountController();
        $account->read($userId);

        $product = ProductFactory::Create();
        $product->read($account->product());

        $viewobject = new ViewObject('<root/>');
        $data = $viewobject->addChild('data');
        $data->addChild('id', $userId);
        $data->addChild('product', $product->name);
        foreach ($account->properties() as $key => $value) {
            if ($value != '') {
                $data->addChild($key, $value);
            }
        }

        return $viewobject->asXML();
    }

    public function dailyUsage($viewarray = NULL) {
        $accountId = $viewarray['id'];
        $forYear = isset($viewarray['year']) ? $viewarray['year'] : NULL;
        $forMonth = isset($viewarray['month']) ? $viewarray['month'] : date('m');

        $usage = new UsageController();
        $totals = $usage->dailyUsageForMonth($accountId, $forMonth, $forYear);
        /*
          $viewobject = new ViewObject('<root/>');
          $data = $viewobject->addChild('data');
          $data->addChild('id', $accountId);
          $days = $data->addChild('days');
          foreach ($totals['days'] as $day => $dates) {
          $daydata = $days->addChild($day);
          foreach($dates as $key => $value)
          $daydata->addChild($key, $value);
          }

          //return $viewobject->asXML();
         * 
         */
        //TODO this is Wrong
        return $totals;
    }

    public function dailyUsageDetail($viewarray = NULL) {
        $accountId = $viewarray['id'];
        $forYear = isset($viewarray['year']) ? $viewarray['year'] : NULL;
        $forMonth = isset($viewarray['month']) ? $viewarray['month'] : date('m');
        $forDay = isset($viewarray['day']) ? $viewarray['day'] : date('d');

        $usage = new UsageController();
        $totals = $usage->sessionsForDay($accountId, $forYear, $forMonth, $forDay);
        //TODO this is Wrong
        return $totals;
    }

    public function monthlyUsage($viewarray = NULL) {
        $accountId = $viewarray['id'];
        $numberOfMonths = isset($viewarray['numberofmonths']) ? $viewarray['numberofmonths'] : 12;

        $usage = new UsageController();
        $totals = $usage->monthlyUsage($accountId, $numberOfMonths);
        return array_reverse($totals);
    }

    public function activeSessions($viewarray = NULL) {
        $accountId = $viewarray['id'];

        $usage = new UsageController();
        $totals = $usage->activeSessions($accountId);
        return $totals;
    }

    public function read($id) {
        $account = AccountFactory::Create();
        $account->read($id);
        return $account->members();
    }

}

class AccountViewFactory {

    public static function Create($viewobject = null) {
        $required_class = "AccountView_" . $GLOBALS['config']->view;
        if (class_exists($required_class)) {
            return new $required_class($viewobject);
        } else {
            return new AccountView($viewobject);
        }
    }

}

