<?php

$xajax->registerFunction("initPageDisplay");
$xajax->registerFunction("PageInit");

class initPageView_ims extends View {

    public function __construct($viewobject) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }

    public function display($viewarray = NULL) {
        $menulist = '
          <ul id="Menu2" class="MM" style="margin: auto;">
      ';
        /**
          if ($GLOBALS['login'] and $GLOBALS['auth']->checkAuth('adsl_account', AUTH_CREATE)) {
          $menulist = $menulist . '
          <li><a href="#" onclick="xajax_accountCreateDisplay();">Create account</a></li>
          ';
          }
         * 
         */
        if ($GLOBALS['login'] and $GLOBALS['auth']->checkAuth('adsl_accountlist', AUTH_READ)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="window.prevline = null; xajax_accountView(\'listall\',{offset:0,limit:20,init: true});">Accounts</a></li>
         ';
        }

        /*
          if ($GLOBALS['login'] and $GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) {
          $menulist = $menulist . '
          <li><a href="#" onclick="xajax_ownerReportDisplay();">Reports</a></li>
          ';
          }
         * 
         */
        /*
          if ($GLOBALS['login'] and $GLOBALS['auth']->checkAuth('adsl_owner', AUTH_CREATE)) {
          $menulist = $menulist . '
          <li><a href="#" onclick="xajax_ownerCreateDisplay(\'%\',0);">Create owner</a></li>
          ';
          }
         * 
         */

        if ($GLOBALS['login'] and $GLOBALS['auth']->checkAuth('adsl_owner', AUTH_READ)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="xajax_ownerView(\'listall\',{});">Resellers</a></li>
         ';
        }
        if ($GLOBALS['login'] and $GLOBALS['auth']->checkAuth('adsl_product', AUTH_UPDATE)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="xajax_productGroupView(\'listall\',{});">Products</a></li>
         ';
        }
        if ($GLOBALS['login'] and $GLOBALS['auth']->checkAuth('adsl_report', AUTH_READ)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="xajax_reportView(\'landing\',{});">Reports</a></li>
         ';
        }
        /*
        $menulist = $menulist . '
        <li><a href="#" onclick="xajax_accountView(\'logout\',{});">Logout</a></li>
         ';
         * 
         */
        $this->smarty->assign('menulist', $menulist);
        $menulist = $this->smarty->fetch('mainMenu.tpl');
        $this->xajax->assign("left_bar", "innerHTML", $menulist);
        $this->xajax->assign("content", "innerHTML", "
            <div class='titleLabel'>Notifications</div>
            <div class='detail'> ".$GLOBALS['adsl_notification']."</div>
            ");
        //$this->xajax->assign("right_bar","innerHTML","ADSL Something");
        return $this->xajax;
    }

}

?>
