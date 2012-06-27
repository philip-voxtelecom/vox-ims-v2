<?php

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

//require_once('_init_model.php');
require_once('_init_view.php');



?>
