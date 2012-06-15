<?php

require_once $GLOBALS['documentroot'].'/classes/Auth.class.php';
require_once $GLOBALS['documentroot'].'/classes/Collection.class.php';

if (!isset($_SERVER['PHP_AUTH_USER']))
{
    define('AUTH',false);
    print "Not authenticated";
} else
{
    session_start();

    if (!isset($_SESSION['name']) or $_SESSION['name'] != $_SERVER['PHP_AUTH_USER'])
        error_log($_SERVER['PHP_AUTH_USER']." logged in");
    $auth = new Auth($_SERVER['PHP_AUTH_USER']);

    define('AUTH',true);
    $_SESSION['name'] = $_SERVER['PHP_AUTH_USER'];
    include $GLOBALS['documentroot'].'/configs/config.php';
}

?>
