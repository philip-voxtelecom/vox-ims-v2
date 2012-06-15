<?php

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

require_once('_owner_model.php');
require_once('_owner_view.php');

function displayOwnerList() {
    $arg_list = func_get_args();

    $ownerList = OwnerListFactory::Create();
    $ownerList->getList();

    $viewobject = new SimpleXMLElement('<root/>');
    $data = $viewobject->addChild('data');
    foreach ($ownerList->getList() as $owner) {
        append_simplexml($data, $owner->asXML());
    }

    $view = ownerListViewFactory::Create($viewobject->asXML());
    return $view->display();
}

?>
