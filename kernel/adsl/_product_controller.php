<?php

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

require_once('_product_model.php');
require_once('_product_view.php');

function displayProductList() {
    $arg_list = func_get_args();

    $productList = ProductListFactory::Create();
    $productList->getList();

    $viewobject = new SimpleXMLElement('<root/>');
    $data = $viewobject->addChild('data');
    foreach ($productList->getList() as $product) {
        append_simplexml($data, $product->asXML());
    }

    $view = productListViewFactory::Create($viewobject->asXML());
    return $view->display();
}



?>
