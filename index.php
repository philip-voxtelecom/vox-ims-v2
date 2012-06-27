<?php

/**
 * @global string $GLOBALS['documentroot']
 * @name $documentroot
 * 
 */
$GLOBALS['documentroot'] = dirname(__FILE__);

if (isset($_GET['module']) and $_GET['module'] != '__start') {
    $module = $_GET['module'];
} else {
    $module = NULL;
}

include $GLOBALS['documentroot'] . '/includes/bootstrap.php';

if (isset($module) and file_exists('kernel/' . $module . '/__boot_controller.php')) {
    include 'kernel/__start/__boot_controller_step1.php';
    try {
        include 'kernel/' . $module . '/__boot_controller.php';
    } catch (Exception $e) {
        //TODO do something here like send a mail and log the error.
        //Show nice errorpage to user
    }
    include 'kernel/__start/__boot_controller_step2.php';
} else {
    include 'kernel/__start/__boot_controller.php';
}
?>
