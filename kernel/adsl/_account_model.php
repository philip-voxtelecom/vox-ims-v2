<?php

$model = 'account';

load_provider_model($model);

require_once('_product_model.php');
require_once('_owner_model.php');

abstract class Account {

    protected $id = null;
    protected $product;
    protected $owner;
    protected $members = array();
    protected $productObj;
    protected $ownerObj;

    public function __sleep() {
        return array('id', 'members', 'product', 'owner', 'productObj', 'ownerObj');
    }

    public function __set($name, $value) {
        $this->members[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->members)) {
            return $this->members[$name];
        }
        $trace = debug_backtrace();
        trigger_error(
                'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return NULL;
    }

    public function __isset($name) {
        return isset($this->members[$name]);
    }

    public function __unset($name) {
        unset($this->members[$name]);
    }

    function __construct() {
        
    }

    abstract public function create();

    abstract public function read($id);

    abstract public function update($parameters);

    abstract public function delete();

    public function asXML() {

        if (empty($this->id))
            return NULL;
        $xml = new SimpleXMLElement('<root/>');
        $data = $xml->addChild('account');
        $data->addChild('id', $this->id);
        $data->addChild('productId', $this->product);
        foreach ($this->members as $member => $value) {
            $value = (string) $value;
            $data->addChild($member, htmlspecialchars($value));
        }

        return $xml;
    }

    abstract public static function options();

    abstract public function isUsernameAvailable($username);

    abstract public function findByUsername($username);

    /*
      public function getOwner() {
      return $this->owner;
      }

      public function productId() {
      $arg_list = func_get_args();
      $productId = array_shift($arg_list);
      if (isset($productId)) {
      $this->product = $productId;
      } else {
      return $this->product;
      }
      }
     * 
     */

    public function id($id = NULL) {
        if (empty($id))
            return $this->id;
        $this->id = $id;
    }

    public function product($id = NULL) {
        if (empty($id))
            return $this->product;
        $this->product = $id;
    }

    public function productObj($id = NULL) {
        if (empty($id))
            return $this->productObj;
        $this->productObj = $id;
    }

    public function owner($id = NULL) {
        if (empty($id))
            return $this->owner;
        $this->owner = $id;
    }

    public function ownerObj($id = NULL) {
        if (empty($id))
            return $this->ownerObj;
        $this->ownerObj = $id;
    }

    public function getAttributes() {
        $attributes = $this->members;
        $attributes['id'] = $this->id;
        $attributes['productId'] = $this->product;
        return $attributes;
    }

    public function properties() {
        $properties = $this->members;
        $properties['productId'] = $this->product;
        $properties['ownerId'] = $this->owner;
        return $properties;
    }

}

abstract class AccountList {

    protected $list;
    protected $count;
    protected $countall;

//protected $dbh;

    function __construct() {
//$this->dbh = MetaDatabaseConnection::get('accountlist')->handle();
        return $this->getList();
    }

    abstract public function getList($offset = 0, $limit = 0, $searchkey = NULL);

    public function count() {
        if (empty($this->list))
            $this->getList();
        return $this->count;
    }

    public function countall() {
        if (empty($this->list))
            $this->getList();
        return $this->countall;
    }

}

class AccountFactory {

    public static function Create() {
        $required_class = "Account_" . $GLOBALS['config']->adsl_model_provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No AccountFactory exists for provider");
        }
    }

}

class AccountListFactory {

    public static function Create() {
        $required_class = "AccountList_" . $GLOBALS['config']->adsl_model_provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No AccountListFactory exists for provider");
        }
    }

}

?>
