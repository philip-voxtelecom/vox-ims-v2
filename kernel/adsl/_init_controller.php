<?php

//if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
//    throw new Exception('Access Denied');

//require_once('_init_model.php');
if (!isset($adsl_notification)) {
    $adsl_notification = "Welcome to the ADSL Module - there are no notifications";
}
require_once('_init_view.php');



?>
