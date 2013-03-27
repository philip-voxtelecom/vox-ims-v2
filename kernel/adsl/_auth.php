<?php

class accountAuth {

    private $login;
    private $admin = false;
    private $loginId = false;
    private $userMap = array();
    private $dbh;

    public function __construct($user) {
        $this->dbh = MetaDatabaseConnection::get('accountauth')->handle();
        $this->loadUserMap($user);
        $effectiveUser = $this->getEffectiveLogin($user);
        $this->setEffectiveLogin($effectiveUser);
    }

    public function isAdmin() {
        return $this->admin;
    }

    protected function setAdmin() {
        $this->admin = true;
    }

    protected function setLoginId($id) {
        $this->loginId = $id;
    }

    public function getLoginId() {
        return $this->loginId;
    }

    public function getEffectiveLogin($user) {
        if (isset($this->userMap[$user])) {
            return $this->userMap[$user];
        } else {
            throw new Exception("No user map exists for $user");
        }
    }

    /* this is the user on the ADSL system */

    public function setEffectiveLogin($user) {
        $this->setLoginId($user);
        //$this->setAdmin();
    }

    /* map the logged in user to an ADSL system user */

    protected function loadUserMap($user) {
        $query = "select a.mappeduser, a.loginuser, a.status as userstatus, b.status as resellerstatus from usermap a, owners b where loginuser='$user' and a.mappeduser=b.login";
        if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
            $result = $this->dbh->query($query);
            $row = $result->fetch_assoc();
        } else {
            throw new Exception("Unsupported DB type for adsl_meta_dbtype");
        }
        if (!empty($row)) {
            if ($row['resellerstatus'] != 'active') {
                throw new Exception("Reseller account is not active for ADSL module");
            } elseif ($row['userstatus'] != 'active') {
                throw new Exception("User account is not active for ADSL module");
            } else {
                $this->userMap[$row['loginuser']] = $row['mappeduser'];
            }
        } else {
            throw new Exception("No ADSL user map found for $user");
        }
    }

}

//$login = new accountAuth($_SERVER['PHP_AUTH_USER']);
// TODO is this right?
try {
    $login = new accountAuth($_SESSION['name']);
} catch (Exception $e) {
    $login = false;
    $GLOBALS['adsl_notification'] = $e->getMessage();
}
?>
