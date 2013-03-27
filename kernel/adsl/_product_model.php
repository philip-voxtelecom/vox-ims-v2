<?php

$model = 'product';

load_provider_model($model);
require_once('_owner_model.php');

abstract class Product {

    protected $dbh = null;
    protected $id = null;
    protected $members = array();

    public function __sleep() {
        return array('id', 'members');
    }

    public function __wakeup() {
        $this->dbh = MetaDatabaseConnection::get('product')->handle();
        ;
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
        $this->dbh = MetaDatabaseConnection::get('product')->handle();
    }

    abstract public function create($params);

    abstract public function read($id);

    abstract public function update();

    abstract public function delete($uid);

    public function getId() {
        return $this->id;
    }
    
    public function getProductId() {
        return $this->productId;
    }

    public function getAttributes() {
        $attributes = $this->members;
        $attributes['id'] = $this->id;
        return $attributes;
    }

    public function asXML() {
        if (empty($this->id))
            return NULL;
        $xml = new SimpleXMLElement('<root/>');
        $data = $xml->addChild('product');
        $data->addChild('id',$this->id);
        foreach ($this->members as $member => $value) {
            $data->addChild($member, (string) $value);
        }
        return $xml;
    }
    
    public function asJSON() {
        return json_encode($this->getAttributes());
    }
    
    public function asArray() {
        return $this->getAttributes();
    } 
}


abstract class ProductList {

    protected $list;
    protected $dbh;

    function __construct() {
        $this->dbh = MetaDatabaseConnection::get('productlist')->handle();
        return $this->getList();
    }

    abstract public function getList();
}

/*
 * 
 * 
 * 
 * 
 */
abstract class ProductGroup {
    protected $dbh = null;
    protected $id = null;
    protected $members = array();

    public function __sleep() {
        return array('id', 'members');
    }

    public function __wakeup() {
        $this->dbh = MetaDatabaseConnection::get('productgroup')->handle();
        ;
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
        $this->dbh = MetaDatabaseConnection::get('productgroup')->handle();
    }

    abstract public function create($params);

    abstract public function read($id);

    abstract public function update();

    abstract public function delete($uid);

    public function getId() {
        return $this->id;
    }

    public function getAttributes() {
        if (empty($this->id))
            return NULL;
        $attributes = $this->members;
        $attributes['id'] = $this->id;
        return $attributes;
    }
    
    public function asJSON() {
        return json_encode($this->getAttributes());
    }
    
    public function asArray() {
        return $this->getAttributes();
    }    
}

abstract class ProductGroupList {

    protected $list;
    protected $dbh;

    function __construct() {
        $this->dbh = MetaDatabaseConnection::get('productlist')->handle();
        return $this->getList();
    }

    abstract public function getList();
}

/*
 * Factories
 * 
 * 
 * 
 */

class ProductFactory {

    public static function Create() {
        $required_class = "Product_" . $GLOBALS['config']->adsl_model_provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No ProductFactory exists for provider");
        }
    }

}

class ProductListFactory {

    public static function Create() {
        $required_class = "ProductList_" . $GLOBALS['config']->adsl_model_provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No ProductListFactory exists for provider");
        }
    }

}

class ProductGroupFactory {

    public static function Create() {
        $required_class = "ProductGroup_" . $GLOBALS['config']->adsl_model_provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No ProductGroupFactory exists for provider");
        }
    }

}

class ProductGroupListFactory {

    public static function Create() {
        $required_class = "ProductGroupList_" . $GLOBALS['config']->adsl_model_provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No ProductGroupListFactory exists for provider");
        }
    }

}
?>
