<?php

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

require_once('_product_model.php');

if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->view . '/_product_view.php')) {
    include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->view . '/_product_view.php');
}

class ProductView extends View {

    public function display($viewarray = NULL) {
        ;
    }

    public function listall() {
        $arg_list = func_get_args();

        $productList = ProductListFactory::Create();
        $productList->getList();

        $viewobject = new SimpleXMLElement('<root/>');
        $data = $viewobject->addChild('data');
        foreach ($productList->getList() as $product) {
            append_simplexml($data, $product->asXML());
        }

        $products = array();

        foreach ($viewobject->data->children() as $element) {
            $id = (string) $element->id;
            $product = array();
            foreach ($element->children() as $param) {
                $product[$param->getName()] = (string) $param;
            }
            array_push($products, $product);
        }
        return $products;
    }

    public function read($id) {
        $product = ProductFactory::Create();
        $product->read($id);
        return $product->members();
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

?>