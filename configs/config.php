<?php

require $GLOBALS['documentroot'] . '/classes/Config.class.php';

$config = new Config();

$config->VERSION = '0.9.0';
$config->debug = TRUE;

// ADSL system this system is interacting with
$config->provider = 'rux';

//Default view
$config->view = 'ims';

// Global defaults. Localise in local_config.php. This will make upgrading easier
// set the documentroot for the application
$config->documentroot = $GLOBALS['documentroot'];

// set display row limit for tables
$config->displayRowLimit = 20;

//ADSL management database details
$config->adsl_dbtype = 'pg';
$config->adsl_dbhost = 'localhost';
$config->adsl_dbuser = 'iprisedevel';
$config->adsl_dbname = 'radius-devel';
$config->adsl_dbpass = 'develiprise';

$config->adsl_meta_dbtype = 'mysqli';
$config->adsl_meta_dbhost = 'localhost';
$config->adsl_meta_dbuser = 'adslmeta';
$config->adsl_meta_dbname = 'adslmeta';
$config->adsl_meta_dbpass = 'y6t5r4e3';

$config->use_cache = TRUE;
// mysqli or pdodb
// currently there is a problem with pdodb
// This is un used
// $config->cachetype = 'mysqli';


if (is_readable($GLOBALS['documentroot'] . '/configs/local_config.php')) {
    include $GLOBALS['documentroot'] . '/configs/local_config.php';
}
?>
