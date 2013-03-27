<?php

/*
 * ims _owner_class view
 */
$xajax->registerFunction("ownerView");
$xajax->registerFunction("ownerSubmit");

function ownerView($viewrequest, $viewarray) {
    $view = new OwnerView_ims();
    if (method_exists($view, $viewrequest))
        return call_user_func_array(array($view, $viewrequest), array($viewarray));
}

function ownerSubmit($submitrequest, $submitarray) {
    $submit = new OwnerSubmit_ims();
    if (method_exists($submit, $submitrequest))
        return call_user_func_array(array($submit, $submitrequest), array($submitarray));
}

class OwnerView_ims extends OwnerView {

    protected $xajax;
    protected $meta = array();

    public function __construct($viewobject = null) {
        $this->xajax = new xajaxResponse();
        if (isset($GLOBALS['config']->show_deleted_resellers))
            $this->meta['show_deleted'] = $GLOBALS['config']->show_deleted_resellers;
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

    public function read($params) {
        if (!isset($this->meta['action'])) {
            $this->meta['action'] = 'view';
        }

        $owner = new OwnerController();

        $actionMenu = array();
        $actionMenu['globalmenu'] = array();
        $actionMenu['menu'] = array();
        array_push($actionMenu['globalmenu'], array(
            'action' => "xajax_ownerView('listall',{});",
            'face' => "Back to Resellers"
        ));
        array_push($actionMenu['menu'], array(
            'action' => "xajax_ownerView('read',{id: '" . $params['id'] . "'});",
            'face' => "Details"
        ));
        array_push($actionMenu['menu'], array(
            'action' => "xajax_ownerView('update',{id: '" . $params['id'] . "'});",
            'face' => "Edit"
        ));
        array_push($actionMenu['menu'], array(
            'action' => "xajax_ownerView('realms',{id: '" . $params['id'] . "'});",
            'face' => "Realms"
        ));
        array_push($actionMenu['menu'], array(
            'action' => "xajax_ownerView('delete',{id: '" . $params['id'] . "'});",
            'face' => "Delete"
        ));

        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("reseller", $owner->read($params['id']));
        $ownerMAC = $this->smarty->fetch('ownerMAC.tpl');
        $this->smarty->clear_all_assign();
        $this->smarty->assign("actionmenu", $actionMenu);
        $menu = $this->smarty->fetch('actionMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $ownerMAC);
        $this->xajax->assign("right_bar", "innerHTML", $menu);
        return $this->xajax;
    }

    public function update($params) {
        $this->meta['action'] = 'update';
        return $this->read($params);
    }

    public function create() {
        $this->meta['action'] = 'create';

        //$actionMenu = array();
        //$actionMenu['globalmenu'] = array();
        //$actionMenu['menu'] = array();
        //array_push($actionMenu['globalmenu'], array(
        //    'action' => "xajax_productGroupView('viewproducts',{id: ''});",
        //    'face' => "Back to Group"
        //));

        $this->smarty->assign("meta", $this->meta);
        $ownerMAC = $this->smarty->fetch('ownerMAC.tpl');
        $this->smarty->clear_all_assign();
        //$this->smarty->assign("actionmenu", $actionMenu);
        //$menu = $this->smarty->fetch('actionMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $ownerMAC);
        $this->xajax->assign("right_bar", "innerHTML", '');
        return $this->xajax;
    }

    public function delete($params) {
        $id = $params['id'];
        $owner = new OwnerController();
        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("reseller", $owner->read($id));
        $content = $this->smarty->fetch('ownerDelete.tpl');
        $this->xajax->assign("content", "innerHTML", $content);
        return $this->xajax;
    }

    public function realms($params) {
        $id = $params['id'];
        $owner = new OwnerController();
        $details = $owner->read($id);
        $realms = $owner->realms();
        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("reseller", $details);
        $this->smarty->assign("realms", $realms);
        $content = $this->smarty->fetch('ownerRealms.tpl');
        $this->xajax->assign("content", "innerHTML", $content);
        return $this->xajax;
    }
    public function viewCallback($viewarray = NULL) {

        $view = $viewarray['view'];
        $params = json_encode($viewarray['params']);
        $this->xajax->script("xajax_ownerView('$view',$params);");
        return $this->xajax;
    }

    public function errorCallback($viewarray = NULL) {

        if (isset($viewarray['error']))
            $error = $viewarray['error'];

        $this->xajax->script("alert('The following error was encountered: $error');");
        return $this->xajax;
    }
}

class OwnerSubmit_ims {

    public function create($params) {
        $owner = new OwnerController();
        $createObj = array();
        $createObj['name'] = $params['name'];
        $createObj['primaryemail'] = $params['primaryemail'];
        $createObj['login'] = $params['login'];
        $createObj['password'] = $params['password'];
        $createObj['comments'] = $params['comments'];
        try {
            $owner->create($createObj);
        } catch (Exception $e) {
            $view = new OwnerView_ims();
            error_log("Error creating reseller: " . $e->getMessage());
            return $view->errorCallback(array('error' => $e->getMessage()));
        }
        $callback = new OwnerView_ims();
        return $callback->viewCallback(array('view' => 'listall', 'params' => array()));
    }

    public function update($params) {
        $owner = new OwnerController();
        $owner->read($params['id']);
        $createObj = array();
        $createObj['name'] = $params['name'];
        $createObj['primaryemail'] = $params['primaryemail'];
        $createObj['password'] = $params['password'];
        $createObj['comments'] = $params['comments'];
        $createObj['status'] = $params['status'];
        $owner->update($createObj);
        $callback = new OwnerView_ims();
        return $callback->viewCallback(array('view' => 'read', 'params' => array('id' => $params['id'])));
    }

    public function delete($params) {
        if ($params['confirm'] == 'on') {
            $owner = new OwnerController();
            $owner->read($params['id']);
            $owner->delete($params['id']);
            $callback = new OwnerView_ims();
            return $callback->viewCallback(array('view' => 'listall', 'params' => array()));
        } else {
            
        }
    }

}

?>
