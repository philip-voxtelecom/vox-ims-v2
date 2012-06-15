<?php

/* 
 * System _owner_view class
 */

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

require_once('_owner_model.php');

if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] .'/'. $GLOBALS['config']->view . '/_owner_view.php')) {
    include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] .'/'. $GLOBALS['config']->view . '/_owner_view.php');
}

class ownerListView extends View {

    public function display() {
        $xml = new ViewObject($this->viewobject);
        $owners = array();

        foreach ($xml->data->children() as $element) {
            $id = (string) $element->id;
            $owner = array();
            foreach ($element->children() as $param) {
                $owner[$param->getName()] = (string) $param;
            }
            array_push($owners, $owner);
                    
        }
        var_dump($owners);
    }

}

class ownerListViewFactory {

    public static function Create($ownerList) {
        $required_class = "OwnerListView_" . $GLOBALS['config']->view;
        if (class_exists($required_class)) {
            return new $required_class($ownerList);
        } else {
            return new ownerListView($ownerList);
        }
    }

}

?>