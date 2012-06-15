<?php

$xajax->processRequest();

// Initialise and setup display
$viewobject = array('xajax' => $xajax->getJavaScript());
$view = new _bootView($viewobject);
//$view->setModule($module);
print $view->display();

?>
