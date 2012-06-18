<?php

class MetaDatabaseConnection {

    private $user;
    private $pass;
    private $host;
    private $db;

    public static function get($id = 'default') {
        static $db = array();
        if (empty($db[$id]))
            $db[$id] = new MetaDatabaseConnection();
        return $db[$id];
    }

    private $_handle = null;

    private function __construct() {
        $this->user = $GLOBALS['config']->adsl_meta_dbuser;
        $this->pass = $GLOBALS['config']->adsl_meta_dbpass;
        $this->host = $GLOBALS['config']->adsl_meta_dbhost;
        $this->db = $GLOBALS['config']->adsl_meta_dbname;
        /*
        if ($GLOBALS['config']->adsl_meta_dbtype == 'pg') {
            $this->_handle = & pg_pconnect('host=' . $this->host . ' dbname=' . $this->db . ' user=' . $this->user . ' password=' . $this->pass);
            if (empty($this->_handle)) {
                throw new Exception('Could not connect to database: ' . pg_last_error());
            }
            $query = "SET search_path = adsl, public";
            pg_query($this->_handle, $query);
        } elseif ($GLOBALS['config']->adsl_meta_dbtype == 'pdodb') {
         * 
         */
        if ($GLOBALS['config']->adsl_meta_dbtype == 'pdodb') {
            $dsn = "mysql:dbname=$this->db;host=$this->host";
            try {
                $this->_handle = new PDO($dsn, $this->user, $this->pass);
            } catch (PDOException $e) {
                throw new Exception('Could not connect to database: ' . $e->getMessage());
            }
        } elseif ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
            $this->_handle = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
            if (mysqli_connect_errno($this->_handle)) {
                throw new Exception('Could not connect to database: ' . mysqli_connect_error());
            }
        } else {
            throw new Exception('No ADSL database type defined');
        }
    }

    public function handle() {
        return $this->_handle;
    }

}

?>
