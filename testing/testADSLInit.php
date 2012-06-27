<?php

$GLOBALS['documentroot'] = '/var/www/IMS2';

$module = 'adsl';

require_once $GLOBALS['documentroot'] . '/classes/Auth.class.php';
require_once $GLOBALS['documentroot'] . '/classes/Collection.class.php';

session_start();
$auth = new Auth('philip');

define('AUTH', true);
$_SESSION['name'] = 'philip';
include $GLOBALS['documentroot'] . '/configs/config.php';
$GLOBALS['config']->view = 'NULL';

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

require_once($GLOBALS['documentroot'] . '/kernel/adsl/_meta_db.php');

include $GLOBALS['documentroot'] . '/kernel/adsl/_auth.php';
include $GLOBALS['documentroot'] . '/kernel/adsl/_cache.php';
include $GLOBALS['documentroot'] . '/kernel/adsl/_common_functions.php';
include $GLOBALS['documentroot'] . '/kernel/adsl/_owner_controller.php';
include $GLOBALS['documentroot'] . '/kernel/adsl/_product_controller.php';
include $GLOBALS['documentroot'] . '/kernel/adsl/_account_controller.php';
include $GLOBALS['documentroot'] . '/kernel/adsl/_usage_controller.php';

?>
