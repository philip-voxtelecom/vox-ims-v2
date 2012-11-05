<?php

if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ))
    throw new Exception('Access Denied');

require_once('_account_model.php');
require_once('_product_model.php');
require_once('_account_view.php');

class AccountController {

    protected $account;
    protected $list;
    private $required_create_params = array(
        'username' => array(
            'mandatory' => TRUE,
            'validation' => ''
        ),
        'password' => array(
            'mandatory' => TRUE,
            'validation' => ''
        ),
        'product' => array(
            'mandatory' => TRUE,
            'validation' => ''
        ),
        'bundlesize' => array(
            'mandatory' => TRUE,
            'validation' => ''
        )
    );
    private $allowed_update_params = array(
        'password' => array(
            'mandatory' => TRUE,
            'validation' => ''
        ),
        'bundlesize' => array(
            'mandatory' => TRUE,
            'validation' => ''
        )
    );

    public function __construct() {
        if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
            throw new Exception('Access Denied');;
    }

    public function create($parameters) {
        /*
         * $parameters must be an associative array of parameters
         * Required parameters
         * 
         * username
         * password
         * product
         * bundlesize
         * 
         * Optional
         * description
         * notifyemail
         * notifycell
         * reference
         * note
         * 
         */
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_CREATE))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $parameters);

        $this->account = AccountFactory::Create();

        $options = $this->account->options();

        try {
            validateParams($this->required_create_params, $parameters);
            validateParams($options, $parameters);
        } catch (InvalidArgumentException $e) {
            throw new Exception("Parameter " . $e->getMessage() . " required");
        }

        $this->account->product($parameters['product']);
        unset($parameters['product']);

        foreach ($parameters as $parameter => $value) {
            $this->account->$parameter = $value;
        }

        //if (!$this->account->isUsernameAvailable($parameters['username']))
        //    throw new Exception("username not available");

        try {
            $this->account->create();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        if (isset($parameters['note']))
            $this->account->update(array('note' => $parameters['note']));
        if (isset($this->account->notifycell))
            $this->account->update(array('notifycell' => $this->account->notifycell));
        if (isset($this->account->notifyemail))
            $this->account->update(array('notifyemail' => $this->account->notifyemail));
        if (isset($this->account->mailreport))
            $this->account->update(array('mailreport' => $this->account->mailreport));
        if (isset($this->account->callingstation))
            $this->account->update(array('callingstation' => $this->account->callingstation));

        $time = microtime(true) - $time_start;
        audit('account', 'create', $auditdata, $time);
        return $this->account->id();
    }

    public function read($id) {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $id);

        if (empty($id))
            throw new InvalidArgumentException("Invalid argument");

        if (empty($this->account))
            $this->account = AccountFactory::Create();

        $time = microtime(true) - $time_start;
        audit('account', 'read', $auditdata, $time);
        return $this->account->read($id);
    }

    public function update($parameters) {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_UPDATE))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $parameters);

        if (empty($this->account))
            throw new Exception("No account loaded for update");
        array_push($auditdata, $this->account->id());

/*
        foreach ($parameters as $key => $update) {
            error_log("$key = $update");
        }
 * 
 */


        $this->account->update($parameters);
        /*
         * pass through an associative array of update commands
         * 
         * password
         * status [suspended,active]
         * notifyemail
         * notifycell
         * reference
         * note
         * bundlesize
         * topup
         * 
         */
        $time = microtime(true) - $time_start;
        audit('account', 'update', $auditdata, $time);
        return TRUE;
    }

    public function delete($id = NULL) {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_DELETE))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();

        if (empty($id) and empty($this->account))
            throw new InvalidArgumentException("Invalid arrgument");

        if (empty($this->account)) {
            try {
                $this->account = AccountFactory::Create();
                $this->account->read($id);
            } catch (Exception $e) {
                throw new Exception("Could not load account for delete: " . $e->getMessage());
            }
        }
        array_push($auditdata, $this->account->properties());

        try {
            $this->account->delete();
            unset($this->account);
        } catch (Exception $e) {
            throw new Exception("Problem encountered deleting account: " . $e->getMessage());
        }

        $time = microtime(true) - $time_start;
        audit('account', 'delete', $auditdata, $time);
        return TRUE;
    }

    public function listall($offset = 0, $limit = 0, $search = NULL) {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ))
            throw new Exception('Access Denied');
        /*
         * listall returns an array of accounts
         * 
         */
        $func_args = func_get_args();
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $func_args);


        $this->list = AccountListFactory::Create();

        $time = microtime(true) - $time_start;
        audit('account', 'listall', $auditdata, $time);
        return $this->list->getList($offset, $limit, $search)->getAll();
    }

    public function findByUsername($username) {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $username);

        if (empty($username))
            throw new InvalidArgumentException("Invalid argument");
        if (empty($this->account))
            $this->account = AccountFactory::Create();
        $id = $this->account->findByUsername($username);

        $time = microtime(true) - $time_start;
        audit('account', 'findByUsername', $auditdata, $time);
        return $id;
    }

    public function isUsernameAvailable($username) {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ))
            throw new Exception('Access Denied');
        $time_start = microtime(true);
        $auditdata = array();
        array_push($auditdata, $username);

        if (empty($username))
            throw new InvalidArgumentException("Invalid argument");
        if (empty($this->account))
            $this->account = AccountFactory::Create();

        $result = $this->account->isUsernameAvailable($username);

        $time = microtime(true) - $time_start;
        audit('account', 'isUsernameAvailable', $auditdata, $time);
        return $result;
    }

    public function product() {
        if (empty($this->account))
            throw new Exception("Cannot return product of unloaded account");
        return $this->account->product();
    }

    public function owner() {
        if (empty($this->account))
            throw new Exception("Cannot return owner of unloaded account");
        return $this->owner();
    }

    public function status() {
        return $this->account->status;
    }

    public function properties() {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ))
            throw new Exception('Access Denied');
        return $this->account->properties();
    }

    public function asXML() {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ))
            throw new Exception('Access Denied');
        return $this->account->asXML();
    }

    public function options() {
        if (empty($this->account))
            $this->account = AccountFactory::Create();
        return $this->account->options();
    }

    public function authenticate($password) {
        if (!$GLOBALS['auth']->checkAuth('adsl_account', AUTH_READ))
            throw new Exception('Access Denied');
        if (empty($this->account))
            throw new Exception("Cannot authenticate unloaded account");
        if (encrypt($password, $this->account->username) == $this->account->_accesskey_)
            return TRUE;
        return FALSE;
    }

}

/*
  class AccountControllerFactory {

  public static function Create() {
  $required_class = "AccountController_" . $GLOBALS['config']->provider;
  if (class_exists($required_class)) {
  return new $required_class();
  } else {
  throw new Exception("No AccountControllerFactory exists for provider");
  }
  }

  }
 * 
 */
?>
