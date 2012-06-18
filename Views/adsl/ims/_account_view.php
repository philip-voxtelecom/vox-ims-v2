<?php

$xajax->registerFunction("displayAccountList");

class AccountListView_ims extends View {

    protected $xajax;

    public function __construct($viewobject) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }

    public function display() {

        $xml = new ViewObject($this->viewobject);
        $accounts = array();

        foreach ($xml->data->children() as $element) {
            $id = (string) $element->id;
            $account = array();
            foreach ($element->children() as $param) {
                $account[$param->getName()] = (string) $param;
            }
            array_push($accounts, $account);
                    
        }

        $this->smarty->assign("accounts", $accounts);
        $this->smarty->assign("count", '1');
        $this->smarty->assign("offset", '0');
        $this->smarty->assign("rowlimit", '10');
        $this->smarty->assign("search", '%');
        $accountlist = $this->smarty->fetch('accountList.tpl');
        $this->smarty->clear_all_assign();
        $accountlistbar = $this->smarty->fetch('accountListMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $accountlist);
        $this->xajax->assign("right_bar", "innerHTML", $accountlistbar);
        return $this->xajax;

    }

}
?>
