<?php

define("AUTH_READ",1);
define("AUTH_CREATE",2);
define("AUTH_UPDATE",4);
define("AUTH_DELETE",8);
define("AUTH_FULL",127);
define("AUTH_ADMIN",128);
define("AUTH_FULLADMIN",255);

class Auth
{
    private $domainCapabilities = array();
    private $userCapabilities = array();

    function __construct($user)
    {
        $this->loadACL($user);
        $this->loadDomains();
    }

    public function addDomainCapability($domain,$capabilities)
    {
        $this->domainCapabilities[$domain] = $capabilities;
    }

    public function checkAuth($domain,$acl)
    {
        if (isset($this->domainCapabilities[$domain]) and isset($this->userCapabilities[$domain]))
        {
            if ( ( $this->userCapabilities[$domain] & $this->domainCapabilities[$domain] & $acl ) ==  $acl  )
            {
                return true;
            }
        }
        return false;
    }

    protected function loadDomains() {
        if (is_readable($GLOBALS['documentroot'] . '/configs/local_domains.php')) {
            include $GLOBALS['documentroot'] . '/configs/local_domains.php';
        }
    }

    protected function loadACL($user)
    {
        if (is_readable($GLOBALS['documentroot'] . '/configs/local_acls.php')) {
            include $GLOBALS['documentroot'] . '/configs/local_acls.php';
        }
    }

}
