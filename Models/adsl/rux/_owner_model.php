<?php

class Owner_rux extends Owner {

    public function create() {
        if (isset($this->id))
            throw new Exception("Cannot re-use owner object for create");
        if (!isset($this->login) or !isset($this->password))
            throw new Exception("owner create requires login and password to be set");
        if (!isset($this->status))
            $this->status = 'active';

        $check = OwnerFactory::Create();
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

}
?>
