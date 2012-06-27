<?php

class accountAuth {

    private $login;
    private $admin = false;
    private $loginId;
    private $userMap = array();

    public function __construct($user) {
        $this->loadUserMap();
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
            return $user;
        }
    }

    /* this is the user on the ADSL system */
    public function setEffectiveLogin($user) {
        if ($user == 'interprise') {
            $this->setLoginId('Datapro');
            //$this->setAdmin();
        } elseif ($user == 'iprise-demo') {
            $this->setLoginId('c6e5de8b-a0e7-480b-8c6c-eec43ecbe23e');
        }
    }

    /* map the logged in user to an ADSL system user */
    protected function loadUserMap() {
        $this->userMap['philip'] = 'interprise';
        $this->userMap['demo'] = 'iprise-demo';
    }

}

//$login = new accountAuth($_SERVER['PHP_AUTH_USER']);
// TODO is this right?
$login = new accountAuth($_SESSION['name']);

?>
