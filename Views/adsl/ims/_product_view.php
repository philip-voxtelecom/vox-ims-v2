<?php

/*
 * ims _product_class view
 */

$xajax->registerFunction("productView");

function productView($viewrequest, $viewarray) {
    $view = new ProductView_ims();
    if (method_exists($view, $viewrequest))
        return call_user_func_array(array($view, $viewrequest), $viewarray);
}

class ProductView_ims extends ProductView {

    protected $xajax;

    public function __construct($viewobject = null) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }

    public function display($viewarray = NULL) {
        ;
    }

    public function listall() {
        $products = parent::listall();

        $this->smarty->assign("products", $products);
        $this->smarty->assign("count", '1');
        $this->smarty->assign("offset", '0');
        $this->smarty->assign("rowlimit", '10');
        $this->smarty->assign("search", '%');
        $productlist = $this->smarty->fetch('productList.tpl');
        $this->smarty->clear_all_assign();
        $productlistbar = $this->smarty->fetch('productListMenu.tpl');
        $this->xajax->assign("content", "innerHTML", $productlist);
        $this->xajax->assign("right_bar", "innerHTML", $productlistbar);
        return $this->xajax;
    }

}

?>
