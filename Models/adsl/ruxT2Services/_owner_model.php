<?php

require_once '_vox_adslservices.php';

class Owner_ruxT2Services extends Owner {

    public function create() {
        if (isset($this->id))
            throw new Exception("Cannot re-use owner object for create");
        if (!isset($this->login) or !isset($this->password))
            throw new Exception("owner create requires login and password to be set");
        if (!isset($this->status))
            $this->status = 'active';

        // TODO check if the user exists on rux
        $check = OwnerFactory::Create();
        $check->getByLogin($this->login);
        if ($check->login == $this->login) {
            throw new Exception("$this->login already exists");
        }
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('account','findbysystemid');
        $check = $adsl_service->call_method(array('systemId' => $this->login));
        if (isset($check['accounts']))
            throw new Exception("$this->login already exists");
        $adsl_service->loadMethod('profile','findbysystemid');
        $check = $adsl_service->call_method(array('systemId' => $this->login));
        if (isset($check['accountProfiles']))
            throw new Exception("$this->login already exists");
        unset($check);

        isset($this->primaryemail)?$primaryemail=$this->primaryemail:$primaryemail='';
        isset($this->name)?$name=$this->name:$name='';
        isset($this->comments)?$comments=$this->comments:$comments='';
        
        $query = "insert into owners(login,password,primaryemail,name,comments)
                      values ('$this->login','$this->password','$primaryemail','$name','$comments')";
        $result = $this->dbh->query($query);
        if (!$result) {
            throw new Exception("Could not add owner details: ".$this->dbh->error);
        }
        $this->id = $this->dbh->insert_id;
        $this->ownerId = $this->id;
        return $this->id;
    }

}

?>
