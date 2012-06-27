<?php

require_once 'classes/REST.class.php';

$GLOBALS['documentroot'] = dirname(__FILE__);

include $GLOBALS['documentroot'] . '/includes/bootstrap.php';
$GLOBALS['config']->view = NULL;

$service = new Request();

$module = $service->url_elements[1];
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
    $response = array(
        'responseCode' => 'FAILED',
        'message' => 'No such service exists'
    );
    echo json_encode($response);
    exit();
}

include './restservice/'.$module.'_service.php';


?>
