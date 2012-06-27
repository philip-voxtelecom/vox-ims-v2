<?php
/*
 * GLOBALS
 * -------
 * documentroot     String      path to files on disk
 * 
 * DEFINED
 * -------
 * AUTH     Boolean     Is user authenticated
 * AUTH_READ    INTEGER 1
 * AUTH_CREATE  INTEGER 2
 * AUTH_UPDATE  INTEGER 4
 * AUTH_DELETE  INTEGER 8
 * AUTH_FULL    INTEGER 127
 * AUTH_ADMIN   INTEGER 128
 * AUTH_FULLADMIN   INTEGER 255
 * 
 * SESSION
 * -------
 * user     String      Authenticated user
 * 
 * 
 * Authentication System
 * ---------------------
 * 
 * Login is added to basic http auth system
 * auth class has mappings of login -> domain -> permissions
 * module auth has mapping of login to module user
 *
 */
?>
