<?php

require_once 'REST.class.php';
require_once 'testADSLInit.php';

$service = new Request();

if ($service->verb == 'GET' and $service->url_elements[1] == 'clients') {
    $view = OwnerViewFactory::Create();
    header("Content-Type: application/json");
    echo $view->listall();
}
?>
