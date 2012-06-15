<?php

require_once('_db.php');

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
        $this->dbh = MetaDatabaseConnection::get()->handle();
        $this->members = $this->null_members;
    }

    abstract public function create();

    abstract public function read($id);

    abstract public function update();

    abstract public function delete();

    /*
     * return the owner login Id
     */

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

}

abstract class OwnerList {

    protected $list;
    protected $dbh;

    function __construct() {
        $this->dbh = MetaDatabaseConnection::get()->handle();
        return $this->getList();
    }

    abstract public function getList();
}

class Owner_rux extends Owner {

    public function create() {
        if (isset($this->id))
            throw new Exception("Cannot re-use owner object for create");
        if (!isset($this->login) or !isset($this->password))
            throw new Exception("owner create requires login and password to be set");
        if (!isset($this->status))
            $this->status = 'active';

        $check = OwnerFactory::Create('rux');
        if ($check->read($this->login) == $this->login) {
            throw new Exception("$this->login already exists");
        }
        unset($check);

        $query = "insert into owners(id,login,password,primaryemail,name,status,comments)
                      values ('$this->login','$this->login','$this->password','$this->primaryemail','$this->name','$this->status','$this->comments')";
        $result = $this->dbh->query($query);
        if (!$result) {
            throw new Exception("Could not add owner details");
        }
        $this->id = $this->login;
        return TRUE;
    }

    public function read($id) {
        $this->members = $this->null_members;
        $this->id = NULL;
        if (!empty($id) and addslashes($id) == $id) {
            $query = "select * from owners where id='$id'";
            $result = $this->dbh->query($query);
            if (!$result) {
                throw new Exception("Could not get owner details");
            }
            $row = $result->fetch_assoc();
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
        $result = $this->dbh->query($query);
        if (!$result) {
            throw new Exception("Could not update owner details");
        }
        return TRUE;
    }

    public function delete() {
        if (!empty($this->id)) {
            $query = "delete from owners where id='$this->id'";
            $result = $this->dbh->query($query);
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

}

class OwnerList_rux extends OwnerList {

    public function getList() {
        $this->list = new Collection();
        $query = "select id from owners";
        $result = $this->dbh->query($query);
        if (!$result) {
            throw new Exception("Could not get owner list");
        }
        while ($row = $result->fetch_assoc()) {
            $owner = OwnerFactory::Create('rux');
            $owner->read($row['id']);
            $this->list->addItem($owner);
        }
        return $this->list;
    }

}

/*
 * Factories
 */

class OwnerFactory {

    public static function Create($provider) {
        if ($provider == 'rux') {
            return new Owner_rux();
        }
    }

}

class OwnerListFactory {

    public static function Create($provider) {
        if ($provider == 'rux') {
            return new OwnerList_rux();
        }
    }

}

?>
