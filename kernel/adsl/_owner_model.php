<?php

$model = 'owner';

load_provider_model($model);

abstract class Owner {

    protected $dbh;

    /*
     * owner properties
     */
    protected $id = null;
    protected $null_members = array(
        'login' => '__EMPTY_',
        'password' => '__EMPTY_',
        'primaryemail' => '__EMPTY_',
        'status' => '__EMPTY_',
        'comments' => '__EMPTY_',
        'name' => '__EMPTY_'
    );
    protected $members = array();
    /*
     * associated owner lists
     */
    protected $userList;
    protected $productList;

    public function __sleep() {
        return array('id', 'null_members', 'members');
    }

    public function __wakeup() {
        $this->dbh = MetaDatabaseConnection::get('owner')->handle();
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
        $this->dbh = MetaDatabaseConnection::get('owner')->handle();
        $this->members = $this->null_members;
    }

    abstract public function create();

    public function read($id) {
        $this->members = $this->null_members;
        $this->id = NULL;
        if (!empty($id) and addslashes($id) == $id) {
            $query = "select * from owners where id='$id'";
            if ($GLOBALS['config']->adsl_meta_dbtype == 'pdodb') {
                try {
                    $sth = $this->dbh->prepare($query);
                } catch (Exception $e) {
                    print $e->getMessage();
                }
                $result = $sth->execute();
                if (!$result) {
                    throw new Exception("Could not get owner details");
                }

                $row = $sth->fetch(PDO::FETCH_ASSOC);
            } elseif ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
                $result = $this->dbh->query($query);
                $row = $result->fetch_assoc();
            }
            if (!empty($row)) {
                $this->id = $row['id'];
                $this->login = $row['login'];
                $this->password = $row['password'];
                $this->primaryemail = $row['primaryemail'];
                $this->name = $row['name'];
                $this->status = $row['status'];
                $this->comments = $row['comments'];
            }
            return $this->id;
        }
        return FALSE;
    }

    public function update() {
        if (empty($this->id))
            throw new Exception("Owner not loaded for update");
        if (!isset($this->password))
            throw new Exception("owner update requires password to be set");
        if (!isset($this->status))
            $this->status = 'active';

        $query = "update owners set 
                    password='$this->password',
                    primaryemail='$this->primaryemail',
                    name='$this->name',
                    status='$this->status',
                    comments='$this->comments'
                  where id='$this->id'
                ";
        if ($GLOBALS['config']->adsl_meta_dbtype == 'pdodb') {
            $sth = $this->dbh->prepare($query);
            $result = $sth->execute();
        } elseif ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
            $result = $this->dbh->query($query);
        }
        if (!$result) {
            throw new Exception("Could not update owner details");
        }
        return TRUE;
    }

    public function delete() {
        if (!empty($this->id)) {
            $query = "delete from owners where id='$this->id'";
            if ($GLOBALS['config']->adsl_meta_dbtype == 'pdodb') {
                $sth = $this->dbh->prepare($query);
                $result = $sth->execute();
            } elseif ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
                $result = $this->dbh->query($query);
            }
            if (!$result) {
                throw new Exception("Could not delete owner");
            }
            $this->id = NULL;
            $this->members = $this->null_members;
            return TRUE;
        } else {
            throw new Exception('No owner instantiated for deletion');
        }
        return FALSE;
    }

    /*
     * return the owner login Id
     */

    public function members() {
        return $this->members;
    }
    
    public function getId() {
        return $this->id;
    }

    /*
     * returns a collection of this owner's products
     */

    public function getProductList() {
        if (empty($this->productList)) {
            try {
                // TODO 
            } catch (Exception $e) {
                throw new Exception("Error in owner::getProductList" . $e->getMessage());
            }
        }
        return $this->productList;
    }

    /*
     * returns a collection of owners user objects
     */

    public function getUserList() {
        if (empty($this->userList)) {
            try {
                // TODO
            } catch (Exception $e) {
                throw new Exception("Error in owner::getUserList" . $e->getMessage());
            }
        }
        return $this->userList;
    }

    public function asXML() {
        if (empty($this->id))
            return NULL;
        $xml = new SimpleXMLElement('<root/>');
        $data = $xml->addChild('owner');
        $data->addChild('id', $this->id);
        foreach ($this->members as $member => $value) {
            $data->addChild($member, $value);
        }
        return $xml;
    }

}

class OwnerList {

    protected $list = NULL;
    protected $dbh;

    function __construct() {
        $this->dbh = MetaDatabaseConnection::get('ownerlist')->handle();
        return $this->getList();
    }

    public function getList() {
        if (empty($this->list)) {
            $this->list = new Collection();
            $query = "select id from owners order by id";
            if ($GLOBALS['config']->adsl_meta_dbtype == 'pdodb') {
                $sth = $this->dbh->prepare($query);
                $result = $sth->execute();
                if (!$result) {
                    throw new Exception("Could not get owner list");
                }
                while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                    $owner = OwnerFactory::Create();
                    $owner->read($row['id']);
                    $this->list->addItem($owner);
                }
            } elseif ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
                $result = $this->dbh->query($query);
                if (!$result) {
                    throw new Exception("Could not get owner list");
                }
                while ($row = $result->fetch_assoc()) {
                    $owner = OwnerFactory::Create();
                    $owner->read($row['id']);
                    $this->list->addItem($owner);
                }                
            }
        }
        return $this->list;
    }

}

/*
 * Factories
 */

class OwnerFactory {

    public static function Create() {
        $required_class = "Owner_" . $GLOBALS['config']->provider;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No OwnerFactory exists for provider");
        }
    }

}

class OwnerListFactory {

    public static function Create() {
        return new OwnerList();
    }

}

?>
