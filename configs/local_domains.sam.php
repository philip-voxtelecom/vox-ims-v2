<?php

/*
 * This file should be named local_domains.php for it to be used
 */
$this->addDomainCapability('system', AUTH_FULL);
$this->addDomainCapability('adsl', AUTH_READ | AUTH_CREATE | AUTH_UPDATE | AUTH_DELETE);
$this->addDomainCapability('adsl_account', AUTH_READ | AUTH_CREATE | AUTH_UPDATE | AUTH_DELETE);
$this->addDomainCapability('adsl_accountlist', AUTH_READ);
$this->addDomainCapability('adsl_owner', AUTH_READ | AUTH_CREATE | AUTH_UPDATE | AUTH_DELETE);
$this->addDomainCapability('adsl_ownerlist', AUTH_READ | AUTH_CREATE | AUTH_UPDATE | AUTH_DELETE);
$this->addDomainCapability('adsl_product', AUTH_READ | AUTH_CREATE | AUTH_UPDATE | AUTH_DELETE);

?>
