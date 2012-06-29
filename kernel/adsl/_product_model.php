<?php

$model = 'product';

load_provider_model($model);

abstract class Product {

    protected $dbh = null;
    protected $id = null;
    protected $null_members = array(
        'status' => '__EMPTY_',
        'name' => '__EMPTY_',
        'description' => '__EMPTY_',
        'owner' => '__EMPTY_',
        'productId' => '__EMPTY_',
    );
    protected $members = array();

    public function __sleep() {
        return array('id', 'null_members', 'members');
    }

    public function __wakeup() {
        $this->dbh = MetaDatabaseConnection::get('product')->handle();
        ;
    }

    public function __set($name, $value) {
        if (isset($this->members[$name])) {
            $this->members[$name] = $value;
        } else {
            throw new Exception("Attempting to set non-existent member");
        }
    }

    public function __get($name) {
        if (array_key_exists($name, $this->members) and $this->members[$name] != '__EMPTY_') {
            return $this->members[$name];
        } elseif (array_key_exists($name, $this->members) and $this->members[$name] == '__EMPTY_') {
            return NULL;
        }

        $trace = debug_backtrace();
        trigger_error(
                'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return NULL;
    }

    public function __isset($name) {
        if (isset($this->members[$name]) and $this->members[$name] != '__EMPTY_') {
            return TRUE;
        } elseif (isset($this->members[$name]) and $this->members[$name] == '__EMPTY_') {
            return FALSE;
        } else {
            throw new Exception("Attempting to access non-existent member");
        }
    }

    public function __unset($name) {
        if (isset($this->members[$name])) {
            unset($this->members[$name]);
        } else {
            throw new Exception("Attempting to delete non-existent member");
        }
    }

    function __construct() {
        $this->dbh = MetaDatabaseConnection::get('product')->handle();
        $this->members = $this->null_members;
    }

    abstract public function create();

    abstract public function read($id);

    abstract public function update();

    abstract public function delete();

    public function getId() {
        return $this->id;
    }

    public function getAttributes() {
        $attributes = $this->members;
        return $attributes;
    }

    public function asXML() {
        if (empty($this->id))
            return NULL;
        $xml = new SimpleXMLElement('<root/>');
        $data = $xml->addChild('product');
        $data->addChild('id', $this->id);
        foreach ($this->members as $member => $value) {
            $data->addChild($member, $value);
        }
        return $xml;
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
 * Factories
 */

class ProductFactory {

    public static function Create() {
        $required_class = "Product_" . $GLOBALS['config']->provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No ProductFactory exists for provider");
        }
    }

}

class ProductListFactory {

    public static function Create() {
        $required_class = "ProductList_" . $GLOBALS['config']->provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No ProductListFactory exists for provider");
        }
    }

}

?>
