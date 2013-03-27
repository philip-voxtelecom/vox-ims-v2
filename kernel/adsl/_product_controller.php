<?php

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

require_once('_product_model.php');
require_once('_product_view.php');

class ProductController {

    protected $product;
    protected $productGroup;
    protected $list;
    protected $groupList;

    public function create($params) {
        if (!$GLOBALS['auth']->checkAuth('adsl_product', AUTH_CREATE))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $params);

        //TODO verify parameters
        $product = ProductFactory::Create();
        $product->create($params);

        $time = microtime(true) - $time_start;
        audit('product', 'create', $auditdata, $time);
    }

    public function read($id) {
        if (!(  ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) or
                ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_READ))
                )
        )
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $id);

        if (empty($id))
            throw new InvalidArgumentException("Invalid argument");

        if (empty($this->product))
            $this->product = ProductFactory::Create();

        $time = microtime(true) - $time_start;
        audit('product', 'read', $auditdata, $time);
        try {
            $this->product->read($id);
        } catch (Exception $e) {
            
        }
        return $this->product->getAttributes();
    }

    public function update($params) {
        if (!$GLOBALS['auth']->checkAuth('adsl_product', AUTH_UPDATE))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $params);

        //TODO verify parameters
        foreach ($params as $key => $value) {
            $this->product->$key = $value;
        }

        $this->product->update();

        $time = microtime(true) - $time_start;
        audit('product', 'update', $auditdata, $time);
    }

    public function delete($uid) {
        if (!$GLOBALS['auth']->checkAuth('adsl_product', AUTH_DELETE))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $uid);

        //TODO verify parameters
        $product = ProductFactory::Create();
        $product->delete($uid);

        $time = microtime(true) - $time_start;
        audit('product', 'create', $auditdata, $time);
    }

    public function listall() {
        if (!(  ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) or
                ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_READ))
                )
        )
            throw new Exception('Access Denied');
        /*
         * listall returns an array of accounts
         * 
         */
        $func_args = func_get_args();
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $func_args);

        $this->list = ProductListFactory::Create();

        $time = microtime(true) - $time_start;
        audit('product', 'listall', $auditdata, $time);
        $data = $this->list->getList()->getAll();
        $list = array();
        foreach ($data as $product) {
            array_push($list, $product->asArray());
        }
        return $list;
    }

    public function readGroup($uid) {
        if (!(  ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) or
                ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_READ))
                )
        )
            throw new Exception('Access Denied');
        $this->productGroup = ProductGroupFactory::Create();
        $this->productGroup->read($uid);
        return $this->productGroup->asArray();
    }

    public function createGroup($groupname) {
        if (!$GLOBALS['auth']->checkAuth('adsl_product', AUTH_CREATE))
            throw new Exception('Access Denied');
        $this->productGroup = ProductGroupFactory::Create();
        $uid = $this->productGroup->create($groupname);
        return $uid;
    }

    public function deleteGroup($uid) {
        if (!$GLOBALS['auth']->checkAuth('adsl_product', AUTH_DELETE))
            throw new Exception('Access Denied');
        $productGroup = ProductGroupFactory::Create();
        $productGroup->delete($uid);
    }

    public function updateGroup($params) {
        if (!$GLOBALS['auth']->checkAuth('adsl_product', AUTH_UPDATE))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $params);

        //TODO verify parameters
        foreach ($params as $key => $value) {
            $this->productGroup->$key = $value;
        }

        $this->productGroup->update();

        $time = microtime(true) - $time_start;
        audit('product', 'update', $auditdata, $time);
    }

    public function listGroups() {
        if (!(  ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) or
                ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_READ))
                )
        )
            throw new Exception('Access Denied');
        $func_args = func_get_args();
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $func_args);

        $groupList = array();

        $this->productGroupList = ProductGroupListFactory::Create();
        $productGroupList = $this->productGroupList->getList()->getAll();
        foreach ($productGroupList as $productGroup) {
            array_push($groupList, $productGroup->asArray());
        }
        return $groupList;
    }

    public function listGroupProducts($id) {
        if (!(  ($GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ)) or
                ($GLOBALS['auth']->checkAuth('adsl_product', AUTH_READ))
                )
        )
            throw new Exception('Access Denied');
        $func_args = func_get_args();
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $func_args);

        $this->product = ProductGroupFactory::Create();
        $groupProductList = $this->product->getProductGroupProductList($id);
        return $groupProductList;
    }
    
    public function getProductGroup($id) {
        if (!isset($this->product))
            $this->product = ProductFactory::Create();
        return $this->product->getAssignedGroupId($id);
    }
    
    public function options() {
        return ProductFactory::Create()->options();
    }

}

?>
