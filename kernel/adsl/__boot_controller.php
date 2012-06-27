<?php

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

require_once('_meta_db.php');

include '_auth.php';
include '_cache.php';
include '_common_functions.php';
include '_init_controller.php';
//include '_accountlist_controller.php';
include '_account_controller.php';
//include '_user_controller.php';
include '_product_controller.php';
//include '_userproduct_controller.php';
//include '_userproductdetail_controller.php';
include '_owner_controller.php';
include '_usage_controller.php';
//include '_owneruser_controller.php';
//include '_ownerproduct_controller.php';
//include '_ownerproductoption_controller.php';
//include '_ownerproductoptionparam_controller.php';
//include '_ownerlist_controller.php';
//include '_ownerreports_controller.php';
?>
