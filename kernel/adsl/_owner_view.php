<?php

/* 
 * System _owner_view class
 */

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

require_once('_owner_model.php');

if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] .'/'. $GLOBALS['config']->view . '/_owner_view.php')) {
    
    include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] .'/'. $GLOBALS['config']->view . '/_owner_view.php');
}

class OwnerView extends View {
    public function display($viewarray = NULL) {
        ;
    }
    
    public function listall() {
        $arg_list = func_get_args();

        $ownerList = OwnerListFactory::Create();
        $ownerList->getList();

        $viewobject = new SimpleXMLElement('<root/>');
        $data = $viewobject->addChild('data');
        foreach ($ownerList->getList() as $owner) {
            append_simplexml($data, $owner->asXML());
        }
        
        $owners = array();

        foreach ($viewobject->data->children() as $element) {
            $id = (string) $element->id;
            $owner = array();
            foreach ($element->children() as $param) {
                $owner[$param->getName()] = (string) $param;
            }
            array_push($owners, $owner);
        }
        return $owners;
    }
    
    public function read($id) {
        $owner = OwnerFactory::Create();
        $owner->read($id);
        return $owner->members();
    }

    public function realms($id) {
        $owner = OwnerFactory::Create();
        $owner->read($id);
        return $owner->getRealms();
    }
}

class OwnerViewFactory {

    public static function Create($viewobject = null) {
        $required_class = "OwnerView_" . $GLOBALS['config']->view;
        if (class_exists($required_class)) {
            return new $required_class($viewobject);
        } else {
            return new OwnerView($viewobject);
        }
    }

}



?>