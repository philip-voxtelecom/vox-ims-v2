<?php

/*
 * ims _owner_class view
 */
$xajax->registerFunction("ownerView");

function ownerView($viewrequest, $viewarray) {
    $view = new OwnerView_ims();
    if (method_exists($view, $viewrequest))
        return call_user_func_array(array($view, $viewrequest), $viewarray);
}

class OwnerView_ims extends OwnerView {

    protected $xajax;

    public function __construct($viewobject = null) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }

    public function display($viewarray = NULL) {
        ;
    }

    public function listall() {
        $owners = parent::listall();

        $this->smarty->assign("owners", $owners);
        $this->smarty->assign("count", '1');
        $this->smarty->assign("offset", '0');
        $this->smarty->assign("rowlimit", '10');
        $this->smarty->assign("search", '%');
        $ownerlist = $this->smarty->fetch('ownerList.tpl');
        $this->smarty->clear_all_assign();
        $ownerlistbar = $this->smarty->fetch('ownerListMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $ownerlist);
        $this->xajax->assign("right_bar", "innerHTML", $ownerlistbar);
        return $this->xajax;
    }

}



?>
