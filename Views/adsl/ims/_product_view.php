<?php

/*
 * ims _product_class view
 */

$xajax->registerFunction("productView");
$xajax->registerFunction("productSubmit");
$xajax->registerFunction("productGroupView");
$xajax->registerFunction("productGroupSubmit");

function productView($viewrequest, $viewarray) {
    $view = new ProductView_ims();
    if (method_exists($view, $viewrequest)) {
        return call_user_func_array(array($view, $viewrequest), array($viewarray));
    } else {
        throw new Exception("Function $viewrequest does not exist in productView class");
    }
}

function productSubmit($submitrequest, $data) {
    $request = new ProductSubmit_ims();
    if (method_exists($request, $submitrequest)) {
        return call_user_func_array(array($request, $submitrequest), array($data));
    } else {
        throw new Exception("Function $submitrequest does not exist in productSubmit class");
    }
}

function productGroupView($viewrequest, $viewarray) {
    $view = new ProductGroupView_ims();
    if (method_exists($view, $viewrequest)) {
        return call_user_func_array(array($view, $viewrequest), array($viewarray));
    } else {
        throw new Exception("Function $viewrequest does not exist in productGroupView class");
    }
}

function productGroupSubmit($submitrequest, $data) {
    $request = new ProductGroupSubmit_ims();
    if (method_exists($request, $submitrequest)) {
        return call_user_func_array(array($request, $submitrequest), array($data));
    } else {
        throw new Exception("Function $submitrequest does not exist in productGroupSubmit class");
    }
}

class ProductView_ims extends ProductView {

    protected $xajax;
    protected $meta = array();

    public function __construct($viewobject = null) {
        $this->xajax = new xajaxResponse();
        $this->meta['show_deleted'] = $GLOBALS['config']->show_deleted_products;
        parent::__construct($viewobject);
    }

    public function display($viewarray = NULL) {
        ;
    }

    public function listall() {
        $products = parent::listall();

        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("products", $products);
        $this->smarty->assign("search", '%');
        $productlist = $this->smarty->fetch('productList.tpl');
        $this->smarty->clear_all_assign();
        $productlistbar = $this->smarty->fetch('productListMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $productlist);
        $this->xajax->assign("right_bar", "innerHTML", $productlistbar);
        return $this->xajax;
    }

    public function read($params) {
        $id = $params['id'];
        if (isset($params['groupid']))
            $groupid = $params['groupid'] ;

        if (UUID::is_valid($id) and isset($params['value'])) {
            $product = parent::read($id);
            $target = $params['target'];
            $value = $params['value'];
            $this->xajax->assign($target, 'value', $product[$value]);
            return $this->xajax;
        }
        $actionMenu = array();
        $actionMenu['globalmenu'] = array();
        $actionMenu['menu'] = array();
        $actionMenu['menutitle'] = 'Product Actions';
        array_push($actionMenu['globalmenu'], array(
            'action' => "xajax_productGroupView('viewproducts',{id: '$groupid'});",
            'face' => "Back to Group"
        ));
        array_push($actionMenu['menu'], array(
            'action' => "xajax_productView('read',{'id': '$id', groupid: '$groupid'});",
            'face' => "Display Details"
        ));
        if ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_UPDATE)) {
            array_push($actionMenu['menu'], array(
                'action' => "xajax_productView('update',{'id': '$id', groupid: '$groupid'});",
                'face' => "Edit Product"
            ));
        }
        if ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_DELETE)) {
            array_push($actionMenu['menu'], array(
                'action' => "xajax_productView('delete',{'id': '$id'});",
                'face' => "Delete Product"
            ));
        }

        if (UUID::is_valid($id)) {
            $product = parent::read($id);
            $productgroup = new ProductController();
            $group = $productgroup->readGroup($groupid);

            $this->smarty->assign("meta", $this->meta);
            $this->smarty->assign("group", $group);
            $this->smarty->assign("product", $product);
            $product = $this->smarty->fetch('product.tpl');
            $this->smarty->clear_all_assign();
            $this->smarty->assign("actionmenu", $actionMenu);
            $menu = $this->smarty->fetch('actionMenu.tpl');
            $this->xajax->assign("content", "innerHTML", $product);
            $this->xajax->assign("right_bar", "innerHTML", $menu);
            return $this->xajax;
        }
    }

    public function create($params) {
        $groupid = $params['groupid'];

        $owner = OwnerViewFactory::Create();
        $realms = $owner->realms($GLOBALS['login']->getLoginId());

        $productInfo = ProductFactory::Create();
        $accessTypes = $productInfo->getAccessTypes();
        $accessQOSs = $productInfo->getAccessQOSs();
        $availableQuotaWheels = $productInfo->getAvailableQuotaWheels();

        $this->meta['action'] = 'create';

        $actionMenu = array();
        $actionMenu['globalmenu'] = array();
        $actionMenu['menu'] = array();
        array_push($actionMenu['globalmenu'], array(
            'action' => "xajax_productGroupView('viewproducts',{id: '$groupid'});",
            'face' => "Back to Group"
        ));

        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("groupid", $groupid);
        $this->smarty->assign("realms", $realms);
        $this->smarty->assign("accesstypes", $accessTypes);
        $this->smarty->assign("accessqoss", $accessQOSs);
        $this->smarty->assign("availablequotawheels", $availableQuotaWheels);
        $product = $this->smarty->fetch('productMAC.tpl');
        $this->smarty->clear_all_assign();
        $this->smarty->assign("actionmenu", $actionMenu);
        $menu = $this->smarty->fetch('actionMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $product);
        $this->xajax->assign("right_bar", "innerHTML", $menu);
        return $this->xajax;
    }

    public function delete($params) {
        $id = $params['id'];
        $product = parent::read($id);
        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("product", $product);
        $content = $this->smarty->fetch('productDelete.tpl');
        $this->xajax->assign("content", "innerHTML", $content);
        return $this->xajax;
    }

    public function update($params) {

        $id = $params['id'];

        if (UUID::is_valid($id)) {

            $product = parent::read($id);

            $owner = OwnerViewFactory::Create();
            $realms = $owner->realms($GLOBALS['login']->getLoginId());

            $productInfo = ProductFactory::Create();
            $accessTypes = $productInfo->getAccessTypes();
            $accessQOSs = $productInfo->getAccessQOSs();
            $availableQuotaWheels = $productInfo->getAvailableQuotaWheels();

            $this->meta['action'] = 'update';

            $this->smarty->assign('meta', $this->meta);
            $this->smarty->assign('accesstypes', $accessTypes);
            $this->smarty->assign('accessqoss', $accessQOSs);
            $this->smarty->assign("availablequotawheels", $availableQuotaWheels);
            $this->smarty->assign('realms', $realms);
            $this->smarty->assign('product', $product);
            $product = $this->smarty->fetch('productMAC.tpl');
            $this->xajax->assign("content", "innerHTML", $product);
            //$this->xajax->assign("right_bar", "innerHTML", $productbar);

            return $this->xajax;
        }
    }

    public function viewCallback($viewarray = NULL) {

        $view = $viewarray['view'];
        $params = json_encode($viewarray['params']);
        $this->xajax->script("xajax_productView('$view',$params);");
        return $this->xajax;
    }

}

class ProductSubmit_ims {

    public function create($params) {
        $product = new ProductController();
        $createObj = array();
        $createObj['name'] = $params['name'];
        $createObj['description'] = $params['description'];
        $createObj['simultaneousUse'] = $params['simultaneousUse'];
        $createObj['realm'] = $params['realm'];
        $createObj['accesstype'] = $params['accesstype'];
        $createObj['accessqos'] = $params['accessqos'];
        $createObj['isTopupable'] = $params['isTopupable'];
        $createObj['status'] = $params['status'];
        $createObj['rolloverDay'] = $params['rolloverDay'];
        $createObj['groupid'] = $params['groupid'];
        $product->create($createObj);
        $callback = new ProductGroupView_ims();
        return $callback->viewCallback(array('view' => 'viewproducts', 'params' => array('id' => $params['groupid'])));
    }

    public function update($params) {
        $product = new ProductController();
        $product->read($params['id']);
        $updateObj = array();
        $updateObj['name'] = $params['name'];
        $updateObj['description'] = $params['description'];
        $updateObj['simultaneousUse'] = $params['simultaneousUse'];
        $updateObj['realm'] = $params['realm'];
        $updateObj['accesstype'] = $params['accesstype'];
        $updateObj['accessqos'] = $params['accessqos'];
        $updateObj['isTopupable'] = $params['isTopupable'];
        $updateObj['status'] = $params['status'];
        $updateObj['rolloverDay'] = $params['rolloverDay'];
        $updateObj['groupid'] = $params['groupid'];
        $updateObj['quotaWheels'] = $params['quotawheels'];
        $product->update($updateObj);
        $callback = new ProductView_ims();
        return $callback->viewCallback(array(
                    'view' => 'read',
                    'params' => array('id' => $params['id'], 'groupid' => $params['groupid'])
                ));
    }

    public function delete($params) {
        if ($params['confirm'] == 'on' and UUID::is_valid($params['id'])) {
            $product = new ProductController();
            $attributes = $product->read($params['id']);
            $product->delete($params['id']);
            $callback = new ProductGroupView_ims();
            return $callback->viewCallback(array('view' => 'viewproducts', 'params' => array('id' => $attributes['groupid'])));
        } else {
            
        }
    }

}

/*
 * 
 * 
 * 
 * 
 */

class ProductGroupView_ims extends ProductGroupView {

    protected $xajax;
    protected $meta = array();

    public function __construct($viewobject = null) {
        $this->xajax = new xajaxResponse();
        $this->meta['show_deleted'] = $GLOBALS['config']->show_deleted_products;
        parent::__construct($viewobject);
    }

    public function display($viewarray = NULL) {
        ;
    }

    public function listall() {
        $groups = parent::listall();

        $actionMenu = array();
        $actionMenu['globalmenu'] = array();
        $actionMenu['menu'] = array();
        array_push($actionMenu['globalmenu'], array(
            'action' => "xajax_productGroupView('create',{});",
            'face' => "Create group"
        ));

        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("groups", $groups);
        $this->smarty->assign("search", '%');
        $grouplist = $this->smarty->fetch('productGroups.tpl');
        $this->smarty->clear_all_assign();
        $this->smarty->assign("actionmenu", $actionMenu);
        $productlistbar = $this->smarty->fetch('actionMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $grouplist);
        $this->xajax->assign("right_bar", "innerHTML", $productlistbar);
        return $this->xajax;
    }

    public function viewproducts($params) {
        $id = $params['id'];
        $group = parent::read($id);
        $groupproducts = parent::viewproducts($id);

        if (isset($params['as']) and $params['as'] == 'select') {
            $name = $params['name'];
            if (isset($params['onchange']))
                $onchange = ' onchange="'.$params['onchange'].'" ';

            if (isset($params['class']))
                $class = ' class="'.$params['class'].'" ';
            
            $select = "<select name='$name' id='$name' $class $onchange>\n";
            
            if (count($groupproducts) > 0) {
                $select .= "<option value='null'>Please select</option>\n";
                foreach ($groupproducts as $groupproduct) {
                    $select .= "<option value='" . $groupproduct['uid'] . "'>" . $groupproduct['name'] . "</option>\n";
                }
            } elseif ($id!='null') {
                $select .= "<option value='null'>No products available for selected group</option>\n";
            } else {
                $select .= "<option value='null'>Please select product group above</option>\n";
            }
            $select .= "</select>";
            $this->xajax->assign($params['target'], 'innerHTML', $select);
            return $this->xajax;
        }

        $actionMenu = array();
        $actionMenu['globalmenu'] = array();
        $actionMenu['menu'] = array();
        $actionMenu['menutitle'] = 'Group Actions';
        array_push($actionMenu['globalmenu'], array(
            'action' => "xajax_productGroupView('listall',{});",
            'face' => "Back to Groups"
        ));
        if ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_CREATE)) {
            array_push($actionMenu['menu'], array(
                'action' => "xajax_productView('create',{groupid: '$id'});",
                'face' => "Create Group Product"
            ));
        }
        if ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_UPDATE)) {
            array_push($actionMenu['menu'], array(
                'action' => "xajax_productGroupView('update',{groupid: '$id'});",
                'face' => "Edit Group"
            ));
        }
        if ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_DELETE)) {
            array_push($actionMenu['menu'], array(
                'action' => "xajax_productGroupView('delete',{groupid: '$id'});",
                'face' => "Delete Group"
            ));
        }

        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("group", $group);
        $this->smarty->assign("groupproducts", $groupproducts);
        $this->smarty->assign("search", '%');
        $groupproductlist = $this->smarty->fetch('productGroupProducts.tpl');
        $this->smarty->clear_all_assign();
        $this->smarty->assign("actionmenu", $actionMenu);
        $actionmenu = $this->smarty->fetch('actionMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $groupproductlist);
        $this->xajax->assign("right_bar", "innerHTML", $actionmenu);
        return $this->xajax;
    }

    public function create($params = NULL) {
        $actionMenu = array();
        //$actionMenu['menutitle'] = 'Actions';
        $actionMenu['globalmenu'] = array();
        $actionMenu['menu'] = array();
        array_push($actionMenu['globalmenu'], array(
            'action' => "xajax_productGroupView('listall',{});",
            'face' => "Back to Groups"
        ));

        $this->meta['action'] = 'create';

        $this->smarty->assign("meta", $this->meta);
        $form = $this->smarty->fetch('productGroupMAC.tpl');
        $this->smarty->clear_all_assign();
        $this->smarty->assign("actionmenu", $actionMenu);
        $actionmenu = $this->smarty->fetch('actionMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $form);
        $this->xajax->assign("right_bar", "innerHTML", $actionmenu);
        return $this->xajax;
    }

    public function delete($params) {
        $id = $params['groupid'];
        $group = parent::read($id);
        $groupproducts = parent::viewproducts($id);
        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("group", $group);
        $this->smarty->assign("products", $groupproducts);
        $content = $this->smarty->fetch('productGroupDelete.tpl');
        $this->xajax->assign("content", "innerHTML", $content);
        return $this->xajax;
    }

    public function update($params) {
        $id = $params['groupid'];
        $actionMenu = array();
        $actionMenu['globalmenu'] = array();
        $actionMenu['menu'] = array();
        array_push($actionMenu['globalmenu'], array(
            'action' => "xajax_productGroupView('viewproducts',{id: '$id'});",
            'face' => "Back to Group"
        ));

        $this->meta['action'] = 'update';
        $group = parent::read($id);

        $this->smarty->assign("meta", $this->meta);
        $this->smarty->assign("group", $group);
        $form = $this->smarty->fetch('productGroupMAC.tpl');
        $this->smarty->clear_all_assign();
        $this->smarty->assign("actionmenu", $actionMenu);
        $actionmenu = $this->smarty->fetch('actionMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $form);
        $this->xajax->assign("right_bar", "innerHTML", $actionmenu);
        return $this->xajax;
    }

    public function viewCallback($viewarray = NULL) {

        $view = $viewarray['view'];
        $params = json_encode($viewarray['params']);
        $this->xajax->script("xajax_productGroupView('$view',$params);");
        return $this->xajax;
    }

}

class ProductGroupSubmit_ims {

    public function create($params = NULL) {
        $productGroup = new ProductController();
        $uid = $productGroup->createGroup($params['name']);
        $callback = new ProductGroupView_ims();
        return $callback->viewCallback(array('view' => 'listall'));
    }

    public function delete($params = NULL) {
        if ($params['confirm'] == 'on' and UUID::is_valid($params['id'])) {
            $productGroup = new ProductController();
            $productGroup->deleteGroup($params['id']);
            $callback = new ProductGroupView_ims();
            return $callback->viewCallback(array('view' => 'listall'));
        } else {
            
        }
    }

    public function update($params = NULL) {
        $productGroup = new ProductController();
        $productGroup->readGroup($params['id']);
        $productGroup->updateGroup($params);
        $callback = new ProductGroupView_ims();
        return $callback->viewCallback(array('view' => 'listall'));
    }

}

?>
