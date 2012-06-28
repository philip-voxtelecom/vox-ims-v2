<?php

$model = 'owner';

load_provider_model($model);

abstract class Owner {

    protected $dbh;

    /*
     * owner properties
     */
    protected $id = NULL;
    protected $members = array();
    /*
     * associated owner lists
     */
    protected $userList;
    protected $productList;

    public function __sleep() {
        return array('id', 'members', 'login');
    }

    public function __wakeup() {
        $this->dbh = MetaDatabaseConnection::get('owner')->handle();
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
        $this->dbh = MetaDatabaseConnection::get('owner')->handle();
    }

    abstract public function create();

    public function read($id) {
        $this->members = array();
        $this->id = NULL;
        if (!empty($id) and addslashes($id) == $id) {
            $query = "select * from owners where id='$id'";
            if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
                $result = $this->dbh->query($query);
                $row = $result->fetch_assoc();
            } else {
                throw new Exception("Unsupported DB type for adsl_meta_dbtype");
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
            if (!empty($this->id))
                return $this->id;
        }
        $this->login = NULL;
        return FALSE;
    }

    public function update($parameters) {
        if (empty($this->id))
            throw new Exception("Owner not loaded for update");

        foreach ($parameters as $parameter => $value) {
            $this->$parameter = $value;
        }

        if (!isset($this->password))
            throw new Exception("owner update requires password to be set");

        isset($this->primaryemail) ? $primaryemail = $this->primaryemail : $primaryemail = '';
        isset($this->name) ? $name = $this->name : $name = '';
        isset($this->comments) ? $comments = $this->comments : $comments = '';
        isset($this->status) ? $status = $this->status : $status = 'active';

        $query = "update owners set 
                    password='$this->password',
                    primaryemail='$primaryemail',
                    name='$name',
                    status='$status',
                    comments='$comments'
                  where id='$this->id'
                ";
        if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
            $result = $this->dbh->query($query);
        } else {
            throw new Exception("Unsupported DB type for adsl_meta_dbtype");
        }
        if (!$result) {
            throw new Exception("Could not update owner details");
        }
        return TRUE;
    }

    public function delete() {
        if (!empty($this->id)) {
            $query = "delete from owners where id='$this->id'";
            if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
                $result = $this->dbh->query($query);
            } else {
                throw new Exception("Unsupported DB type for adsl_meta_dbtype");
            }
            if (!$result) {
                throw new Exception("Could not delete owner");
            }
            $this->id = NULL;
            $this->members = array();
        } else {
            throw new Exception('No owner instantiated for deletion');
        }
        return TRUE;
    }

    public function members() {
        return $this->members;
    }

    /*
      public function getId() {
      return $this->id;
      }
     * 
     */

    public function id() {
        return $this->id;
    }

    public function getByLogin($id) {
        $this->members = array();
        $this->id = NULL;
        if (!empty($id) and addslashes($id) == $id) {
            $query = "select * from owners where login='$id'";
            if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
                $result = $this->dbh->query($query);
                $row = $result->fetch_assoc();
            } else {
                throw new Exception("Unsupported DB type for adsl_meta_dbtype");
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
            if (!empty($this->login))
                return $this->login;
        }
        $this->login = NULL;
        return FALSE;
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

    public function getRealms() {

        if (!isset($this->id))
            throw new Exception("No owner object loaded");
        $query = "select realm, status from realms a, ownerRealm b where b.owner_id='$this->id' and a.id=b.realm_id";
        $result = $this->dbh->query($query);
        if (!$result) {
            throw new Exception("Could not get owner realms");
        }
        $realms = array();
        while ($row = $result->fetch_assoc()) {
            $realms[] = $row;
        }
        return $realms;
    }

}

class OwnerList {

    protected $list = NULL;
    protected $dbh;
    protected $countall;
    protected $count;

    function __construct() {
        $this->dbh = MetaDatabaseConnection::get('ownerlist')->handle();
        return $this->getList();
    }

    public function getList($offset = 0, $limit = 0, $searchkey = '.*') {
        if (empty($this->list)) {
            $this->list = new Collection();
            $query = "select id from owners order by id";
            if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
                $result = $this->dbh->query($query);
                if (!$result) {
                    throw new Exception("Could not get owner list");
                }
                while ($row = $result->fetch_assoc()) {
                    $owner = OwnerFactory::Create();
                    $owner->read($row['id']);
                    $this->list->addItem($owner);
                }
            } else {
                throw new Exception("Unsupported DB type for adsl_meta_dbtype");
            }
        }
        $this->countall = $this->list->count();
        $orderedlist = array();
        $newlist = array();
        foreach ($this->list->getAll() as $value) {
            if (
                    preg_match("/$searchkey/i", $value->login) or
                    preg_match("/$searchkey/i", isset($value->name) ? $value->name : '')
            )
                $orderedlist[$value->login] = $value;
        }
        ksort($orderedlist);
        foreach ($orderedlist as $key => $value)
            array_push($newlist, $value);
        $this->list = new Collection($newlist);
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
