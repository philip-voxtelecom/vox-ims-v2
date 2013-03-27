<?php

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

require_once('_product_model.php');

if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->adsl_view_provider . '/_product_view.php')) {
    include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->adsl_view_provider . '/_product_view.php');
}

class ProductView extends View {

    public function display($viewarray = NULL) {
        ;
    }

    public function listall() {
        $arg_list = func_get_args();

        $productList = new ProductController();
        $list = $productList->listall();

        return $list;
    }

    public function read($id) {
        
        $product = new ProductController();
        //$product->read($id);
        return $product->read($id);
    }

    public function options() {
        return ProductFactory::Create()->options();
    }

}

class ProductViewFactory {

    public static function Create($productList=null) {
        $required_class = "ProductView_" . $GLOBALS['config']->view;
        if (class_exists($required_class)) {
            return new $required_class($productList);
        } else {
            return new ProductView($productList);
        }
    }

}
/*
 * 
 * 
 * 
 * 
 */
class ProductGroupView extends View {

    public function display($viewarray = NULL) {
        ;
    }
    
    public function read($uid) {
        $product = new ProductController();
        $group = $product->readGroup($uid);
        return $group;
    }
    
    public function listall() {
        $product = new ProductController();
        $groups = $product->listGroups();
        return $groups;
    }
    
    public function viewproducts($id) {
        $product = new ProductController();
        $groupProducts = $product->listGroupProducts($id);
        return $groupProducts;
    }
}

class ProductGroupViewFactory {

    public static function Create($productList=null) {
        $required_class = "ProductView_" . $GLOBALS['config']->view;
        if (class_exists($required_class)) {
            return new $required_class($productList);
        } else {
            return new ProductView($productList);
        }
    }

}

?>