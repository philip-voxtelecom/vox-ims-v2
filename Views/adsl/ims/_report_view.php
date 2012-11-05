<?php

/*
 * ims _report_class view
 */

$xajax->registerFunction("reportView");

function reportView($viewrequest, $viewarray) {
    $view = new ReportView_ims();
    if (method_exists($view, $viewrequest))
        return call_user_func_array(array($view, $viewrequest), $viewarray);
}

class ReportView_ims extends ReportView {

    protected $xajax;

    public function __construct($viewobject = null) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }

    public function display($viewarray = NULL) {
        ;
    }

    public function landing() {

        $viewobject = new ViewObject('<root/>');
        $data = $viewobject->addChild('data');
        $data->addChild('owner', $GLOBALS['login']->getLoginId());

        $menu2 = $viewobject->addChild('globalmenu');

        $menu1 = $viewobject->addChild('menu');
        $menu1->addAttribute('title', 'Account Actions');

        if ($GLOBALS['auth']->checkAuth('adsl_report', AUTH_READ)) {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'Usage Summary');
            $menuitem->addChild('action', "xajax_reportView('summary',{});");
        }
        if ($GLOBALS['auth']->checkAuth('adsl_report', AUTH_READ)) {
            $menuitem = $menu1->addChild('menuitem');
            $menuitem->addChild('face', 'User Report');
            $menuitem->addChild('action', "xajax_reportView('user',{});");
        }

        $reportlanding = $this->smarty->fetch('reportLanding.tpl');
        $this->smarty->assign("viewobject", $viewobject);
        $reportactionmenu = $this->smarty->fetch('reportActions.tpl');
        $this->xajax->assign("content", "innerHTML", $reportlanding);
        $this->xajax->assign("right_bar", "innerHTML", $reportactionmenu);
        return $this->xajax;
    }

    public function summary() {
        $summary = parent::summary();

        /*
          $viewobject = new ViewObject('<root/>');
          $data = $viewobject->addChild('data');
          $data->addChild('owner', $GLOBALS['login']->getLoginId());

          $menu2 = $viewobject->addChild('globalmenu');

          $menu1 = $viewobject->addChild('menu');
          $menu1->addAttribute('title', 'Account Actions');

          if ($GLOBALS['auth']->checkAuth('adsl_report', AUTH_READ)) {
          $menuitem = $menu1->addChild('menuitem');
          $menuitem->addChild('face', 'Usage Summary');
          $menuitem->addChild('action', "xajax_reportView('summary',{});");
          }
          if ($GLOBALS['auth']->checkAuth('adsl_report', AUTH_READ)) {
          $menuitem = $menu1->addChild('menuitem');
          $menuitem->addChild('face', 'User Report');
          $menuitem->addChild('action', "xajax_reportView('user',{});");
          }
         * 
         */

        $this->smarty->assign("viewobject", $summary);
        $reportsummary = $this->smarty->fetch('reportSummary.tpl');
        /*
          $this->smarty->assign("viewobject", $viewobject);
          $reportactionmenu = $this->smarty->fetch('reportActions.tpl');
         * 
         */
        $this->xajax->assign("content", "innerHTML", $reportsummary);
        //$this->xajax->assign("right_bar", "innerHTML", $reportactionmenu);
        return $this->xajax;
    }

    public function user($viewarray = null) {
        $detail = parent::summary($viewarray);

        $this->smarty->assign("usage", $detail['accounts']);
        $this->smarty->assign("year", $detail['year']);
        $this->smarty->assign("month", $detail['month']);
        $userreport = $this->smarty->fetch('reportUsers.tpl');
        $this->xajax->assign("content", "innerHTML", $userreport);
        $this->xajax->script("TableKit.reloadTable('list_tbl');");

        return $this->xajax;
    }

    public function exportuserreport($viewarray = NULL) {
        $detail = parent::summary($viewarray);

        $this->smarty->assign("usage", $detail['accounts']);
        $userreport = $this->smarty->fetch('reportUsersExport.tpl');
        $fileid = "reportusers_" . $detail['year'] . "-" . $detail['month'] . "_" . uniqid() . ".csv";
        file_put_contents($GLOBALS['documentroot'] . "/cache/" . $fileid, $userreport);
        //$this->xajax->append("content","innerHTML",$accountlist);
        $this->xajax->script("window.location= '/download.php?file=$fileid&rm=1'");

        return $this->xajax;
    }

}

?>
