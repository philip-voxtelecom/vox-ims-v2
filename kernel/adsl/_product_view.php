<?php

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

require_once('_product_model.php');

if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] .'/'. $GLOBALS['config']->view . '/_product_view.php')) {
    include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] .'/'. $GLOBALS['config']->view . '/_product_view.php');
}

class productListView extends View {

    public function display() {
        $xml = new ViewObject($this->viewobject);
        $products = array();

        foreach ($xml->data->children() as $element) {
            $id = (string) $element->id;
            $product = array();
            foreach ($element->children() as $param) {
                $product[$param->getName()] = (string) $param;
            }
            array_push($products, $product);
                    
        }
        var_dump($products);
    }

}

class productListViewFactory {

    public static function Create($productList) {
        $required_class = "ProductListView_" . $GLOBALS['config']->view;
        if (class_exists($required_class)) {
            return new $required_class($productList);
        } else {
            return new productListView($productList);
        }
    }

}
?>