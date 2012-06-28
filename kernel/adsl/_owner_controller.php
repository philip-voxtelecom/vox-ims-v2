<?php

if (!$GLOBALS['auth']->checkAuth('adsl', AUTH_READ))
    throw new Exception('Access Denied');

require_once('_owner_model.php');
require_once('_owner_view.php');

class OwnerController {

    protected $owner;
    private $required_create_params = array(
        'login' => array(
            'mandatory' => TRUE,
            'validation' => ''
        ),
        'password' => array(
            'mandatory' => TRUE,
            'validation' => ''
        ),
    );

    public function create($parameters) {
        try {
            validateParams($this->required_create_params, $parameters);
        } catch (InvalidArgumentException $e) {
            throw new Exception("Parameter " . $e->getMessage() . " required");
        }
        $this->owner = OwnerFactory::Create();

        foreach ($parameters as $parameter => $value) {
            $this->owner->$parameter = $value;
        }

        try {
            $this->owner->create();
        } catch (Exception $e) {
            throw new Exception("Error creating owner: " . $e->getMessage());
        }
        return $this->owner->id();
    }

    public function read($id) {
        if (empty($id))
            throw new InvalidArgumentException("Invalid argument");

        if (empty($this->owner))
            $this->owner = OwnerFactory::Create();
        return $this->owner->read($id);
    }

    public function update($parameters) {
        if (empty($this->owner))
            throw new Exception("No owner loaded for update");
        //todo check parameters
        $this->owner->update($parameters);
    }

    public function delete($id = NULL) {
        if (empty($id) and empty($this->owner))
            throw new InvalidArgumentException("Invalid argument");

        if (empty($this->owner)) {
            try {
                $this->owner = OwnerFactory::Create();
                $this->owner->read($id);
            } catch (Exception $e) {
                throw new Exception("Could not load owner for delete: " . $e->getMessage());
            }
        }

        try {
            $this->owner->delete();
            unset($this->owner);
        } catch (Exception $e) {
            throw new Exception("Problem encountered deleting account");
        }
        return TRUE;
    }

}

?>
