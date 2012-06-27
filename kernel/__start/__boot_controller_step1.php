<?php

if (!defined('AUTH'))
    exit;

if (file_exists($GLOBALS['documentroot'] . '/Views/__start/'. $GLOBALS['config']->view . '/__boot_view_stage_1.php')) {
    include($GLOBALS['documentroot'] . '/Views/__start/' . $GLOBALS['config']->view . '/__boot_view_stage_1.php');
}

?>
