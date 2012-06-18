<?php

$xajax->registerFunction("initPageDisplay");
$xajax->registerFunction("PageInit");
        
class initPageView_ims extends View {

    public function __construct($viewobject) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }
    
    public function display() {
        $menulist = '
          <ul id="Menu2" class="MM" style="margin: auto;">
      ';
        /**
        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_CREATE)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="xajax_accountCreateDisplay();">Create account</a></li>
         ';
        }
         * 
         */
        if ($GLOBALS['auth']->checkAuth('adsl_accountlist', AUTH_READ)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="window.prevline = null; xajax_displayAccountList(\'%\',0);">Accounts</a></li>
         ';
        }
        if ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="xajax_ownerReportDisplay();">Reports</a></li>
         ';
        }
        /*
        if ($GLOBALS['auth']->checkAuth('adsl_owner', AUTH_CREATE)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="xajax_ownerCreateDisplay(\'%\',0);">Create owner</a></li>
         ';
        }
         * 
         */
        
        if ($GLOBALS['auth']->checkAuth('adsl_owner', AUTH_READ)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="xajax_displayOwnerList(\'%\',0);">Owners</a></li>
         ';
        }
        if ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_READ)) {
            $menulist = $menulist . '
           <li><a href="#" onclick="xajax_displayProductList(\'%\',0);">Products</a></li>
         ';
        }
        $this->smarty->assign('menulist', $menulist);
        $menulist = $this->smarty->fetch('mainMenu.tpl');
        $this->xajax->assign("left_bar", "innerHTML", $menulist);
        $this->xajax->assign("content", "innerHTML", "
            <div class='titleLabel'>Notifications</div>
            <div class='detail'> There are no notifications at present</div>
            ");
        //$this->xajax->assign("right_bar","innerHTML","ADSL Something");
        return $this->xajax;
    }

}

?>
