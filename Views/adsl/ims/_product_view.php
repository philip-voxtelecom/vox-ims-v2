<?php

/*
 * ims _product_class view
 */

$xajax->registerFunction("displayProductList");

class productListView_ims extends View {

    protected $xajax;

    public function __construct($viewobject) {
        $this->xajax = new xajaxResponse();
        parent::__construct($viewobject);
    }

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
