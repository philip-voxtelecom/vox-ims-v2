<?php

require_once 'testADSLInit.php';

/*
$product = ProductFactory::Create();

$product->read('104100');
print "*** Read\n\n";
var_dump($product);
 * 
 */

$productlist = ProductListFactory::Create();
var_dump($productlist);

/*
$cache = CacheFactory::Create();
$cache->type = 'profilelist';
$cache->provider = 'rux';
$cache->identifier = 'datapro';

$cache->expire();
*/



return;

?>
