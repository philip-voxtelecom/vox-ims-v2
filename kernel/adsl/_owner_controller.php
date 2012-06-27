<?php

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

require_once('_owner_model.php');
require_once('_owner_view.php');

class OwnerController {
    public function create() {
        
    }
    
    public function update() {
        
    }
    
    public function delete() {
        
    }
}

?>
