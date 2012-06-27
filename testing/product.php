<?php

require_once 'testADSLInit.php';

/*
$product = ProductFactory::Create();

$product->read('104100');
print "*** Read\n\n";
var_dump($product);
 * 
 */

//$productlist = ProductListFactory::Create();
$view = ProductViewFactory::Create();
echo json_encode($view->listall());

return;
?>
