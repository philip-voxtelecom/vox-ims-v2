<?php

/*
 * ims _owner_class view
 */

$xajax->registerFunction("displayOwnerList");

class ownerListView_ims extends View {

    protected $xajax;

    public function __construct($viewobject) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }

    public function display() {

        $xml = new ViewObject($this->viewobject);
        $owners = array();

        foreach ($xml->data->children() as $element) {
            $id = (string) $element->id;
            $owner = array();
            foreach ($element->children() as $param) {
                $owner[$param->getName()] = (string) $param;
            }
            array_push($owners, $owner);
                    
        }

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

        /*
          if (isset($this->viewobject) and get_class($this->viewobject) == 'OwnerList') {
          //$ownerNo = $this->viewobject->count();

          $this->smarty->assign("logins", $this->viewobject->getList());
          //$this->smarty->assign("count", $this->viewobject->count());
          //$this->smarty->assign("offset", $this->viewobject->getOffset());
          //$this->smarty->assign("rowlimit", $GLOBALS['config']->displayRowLimit);
          //$this->smarty->assign("search", $this->viewobject->getSearch());
          //if ($this->viewobject->getSearch() == '%') {
          //}
          $ownerlist = $this->smarty->fetch('ownerlist.tpl');
          $this->smarty->clear_all_assign();
          $ownerlistbar = $this->smarty->fetch('ownerList_bar.tpl');

          $this->xajax->assign("content", "innerHTML", $ownerlist);
          $this->xajax->assign("right_bar", "innerHTML", $ownerlistbar);
          //$this->xajax->assign("error_bar", "innerHTML", $this->viewobject->errmsg());
          $this->xajax->assign("error_bar", "style.display", "block");

          return $this->xajax;
          } else {

          return null;
          }
         * 
         */
    }

}

?>
