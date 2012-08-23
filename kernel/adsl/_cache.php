<?php

abstract class Cache {

    public $data;
    public $type;
    public $provider;
    public $identifier;
    public $expirytime = 1460800;

    function getData() {
        if (!isset($this->data))
            $this->load();
        return $this->data;
    }

    abstract function load();

    abstract function save($data);

    abstract function expire();
}

class Cache_pdodb extends Cache {

    private $dbh;

    function __construct() {
        $this->dbh = MetaDatabaseConnection::get('cache')->handle();
        if (isset($GLOBALS['config']->cacheExpiryTime))
            $this->expirytime = $GLOBALS['config']->cacheExpiryTime;
    }

    function load() {
        if (!isset($this->type) or !isset($this->provider) or !isset($this->identifier))
            throw new Exception("provider, identifier and type required for caching");
        $query = "select length(data) as data,cachetime from cache where objecttype=? and provider=? and identifier=?";
        $sth = $this->dbh->prepare($query);
        $result = $sth->execute(array("$this->type", "$this->provider", "$this->identifier"));
        if (!$result) {
            throw new Exception("Could not load cache");
        }
        //$sth->bindcolumn(1, $data_fld, PDO::PARAM_LOB);
        //$sth->bindcolumn(2, $cachetime_fld);
        $row = $sth->fetch();
        if (!empty($row)) {
            $cachetime = strtotime($row['cachetime']);
            $now = strtotime(date('Y-m-d h:i:s'));
            $age = $now - $cachetime;
            if ($age < $this->expirytime) {
                $this->data = unserialize(base64_decode($row['data']));
                return $this->data;
            } else {
                $this->expire();
            }
        }
        return FALSE;
    }

    function save($data) {
        if (!isset($this->type) or !isset($this->provider) or !isset($this->identifier))
            throw new Exception("provider, identifier and type required for caching");
        $this->data = base64_encode(serialize($data));
        $cached = new Cache_pdodb();
        $cached->type = $this->type;
        $cached->provider = $this->provider;
        $cached->identifier = $this->identifier;
        if ($cached->load()) {
            $query = "update cache set data=:data where objecttype=:type and provider=:provider and identifier=:identifier";
        } else {
            $query = "insert into cache(provider,objecttype,identifier,data) values (:provider,:type,:identifier,:data)";
        }
        $sth = $this->dbh->prepare($query);
        unset($cached);
        $result = $sth->execute(array('data' => $this->data, 'type' => $this->type, 'provider' => $this->provider, 'identifier' => $this->identifier));
        if (!$result) {
            throw new Exception("Could not save cache: ");
        }
        return TRUE;
    }

    function expire() {
        if (!isset($this->type) or !isset($this->provider))
            throw new Exception("provider, identifier and type required for caching");
        $query = "delete from cache where objecttype='$this->type' and provider='$this->provider' and identifier='$this->identifier'";
        $sth = $this->dbh->prepare($query);
        $result = $sth->execute();
        if (!$result) {
            throw new Exception("Could not expire cache");
        }
        $this->data = NULL;
        return TRUE;
    }

}

class Cache_mysqli extends Cache {

    private $dbh;
    
    function __construct() {
        $this->dbh = MetaDatabaseConnection::get('cache')->handle();
    }
    
    function load() {
        if (!isset($this->type) or !isset($this->provider) or !isset($this->identifier))
            throw new Exception("provider, identifier and type required for caching");
        $query = "select data,cachetime from cache where objecttype='$this->type' and provider='$this->provider' and identifier='$this->identifier'";
        $result = $this->dbh->query($query);
        if (!$result) {
            throw new Exception("Could not load cache");
        }

        $row = $result->fetch_assoc();
        if (!empty($row)) {
            $cachetime = strtotime($row['cachetime']);
            $now = strtotime(date('Y-m-d h:i:s'));
            $age = $now - $cachetime;
            if ($age < $this->expirytime) {
                $this->data = unserialize(base64_decode($row['data']));
                return $this->data;
            } else {
                $this->expire();
            }
        }
        return FALSE;
    }

    function save($data) {
        if (!isset($this->type) or !isset($this->provider) or !isset($this->identifier))
            throw new Exception("provider, identifier and type required for caching");
        $this->data = base64_encode(serialize($data));
        $cached = new Cache_mysqli();
        $cached->type = $this->type;
        $cached->provider = $this->provider;
        $cached->identifier = $this->identifier;
        if ($cached->load()) {
            $query = "update cache set data='$this->data' where objecttype='$this->type' and provider='$this->provider' and identifier='$this->identifier'";
        } else {
            $query = "insert into cache(provider,objecttype,identifier,data) values ('$this->provider','$this->type','$this->identifier','$this->data')";
        }
        unset($cached);
        $result = $this->dbh->query($query);
        if (!$result) {
            throw new Exception("Could not save cache: ".$this->dbh->error);
        }
        return TRUE;
    }

    function expire() {
        if (!isset($this->type) or !isset($this->provider))
            throw new Exception("provider, identifier and type required for caching");
        $query = "delete from cache where objecttype='$this->type' and provider='$this->provider' and identifier='$this->identifier'";
        $result = $this->dbh->query($query);
        if (!$result) {
            throw new Exception("Could not expire cache");
        }
        $this->data = NULL;
        return TRUE;
    }

}

class CacheFactory {

    public static function Create() {
        $required_class = "Cache_" . $GLOBALS['config']->adsl_meta_dbtype;
        if (class_exists($required_class)) {
            return new $required_class();
        } else {
            throw new Exception("No CacheFactory exists for type");
        }
    }

}

?>
