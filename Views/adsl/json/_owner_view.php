<?php

/*
 * json _owner_class view
 */

class OwnerView_json extends OwnerView {

    public function __construct($viewobject = null) {
        parent::__construct($viewobject);
    }

    public function display($viewarray = NULL) {
        ;
    }

    public function listall() {
        $owners = parent::listall();
        //return json_encode($owners);
        header("Content-Type: application/json");
        echo json_encode($response);
    }

}

?>
