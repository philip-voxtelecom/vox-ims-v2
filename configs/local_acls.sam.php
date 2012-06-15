<?php

/*
 * This should be renamed to local_acls.php to be used
 */
if ($user == 'philip') {
    $this->userCapabilities['system'] = AUTH_FULLADMIN;
    $this->userCapabilities['adsl'] = AUTH_FULL;
    $this->userCapabilities['adsl_account'] = AUTH_FULL;
    $this->userCapabilities['adsl_accountlist'] = AUTH_FULL;
    $this->userCapabilities['adsl_owner'] = AUTH_READ;
    $this->userCapabilities['adsl_product'] = AUTH_FULL;
} elseif ($user == 'demo') {
    $this->userCapabilities['adsl'] = AUTH_READ;
    $this->userCapabilities['adsl_account'] = AUTH_CREATE | AUTH_READ | AUTH_UPDATE;
    $this->userCapabilities['adsl_accountlist'] = AUTH_READ | AUTH_UPDATE;
    $this->userCapabilities['adsl_owner'] = AUTH_READ;
    $this->userCapabilities['adsl_product'] = AUTH_READ;
} else {
    $this->userCapabilities['adsl'] = AUTH_READ;
    $this->userCapabilities['adsl_account'] = AUTH_READ;
    $this->userCapabilities['adsl_accountlist'] = AUTH_READ;
    $this->userCapabilities['adsl_owner'] = AUTH_READ;
    $this->userCapabilities['adsl_product'] = AUTH_READ;
}
?>
