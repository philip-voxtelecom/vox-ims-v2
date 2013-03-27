<?php

require_once('_vox_adslservices.php');

class Product_ruxSandvine extends Product {

    public function create($params) {
        // need to have group id
        // add product to db
        // assign to group
        // map to quota plans
        $uid = UUID::v4();
        $groupid = $params['groupid'];
        $owner = $GLOBALS['login']->getLoginId();
        $name = $params['name'];
        $istopupable = $params['isTopupable'];
        $radiusclass = $this->mkRadiusClass(array('accesstype' => $params['accesstype'], 'accessqos' => $params['accessqos']));
        $simultaneoususe = $params['simultaneousUse'];
        $maxusage = 0;
        $status = $params['status'];
        $realm = $params['realm'];
        $rolloverday = $params['rolloverDay'];

        $query1 = "
            INSERT INTO svProducts (uid,owner,name,istopupable,radiusclass,simultaneoususe,maxusage,status,realm,rolloverday)
                VALUES ('$uid','$owner','$name','$istopupable','$radiusclass','$simultaneoususe','$maxusage','$status','$realm','$rolloverday')
                ";
        $query2 = "
            INSERT INTO svProductGroupProducts(productgroup,product)
                VALUES ('$groupid','$uid')
            ";
        try {
            $this->dbh->query('begin');
            $this->dbh->query($query1);
            $this->dbh->query($query2);
            $this->dbh->query('commit');
        } catch (Exception $e) {
            throw new Exception("Error adding product to database: " . $e->getMessage());
        }
    }

    public function read($id) {

        if (!empty($id) and addslashes($id) == $id) {

            $query = "select * from svProducts where uid='$id'";
            try {
                $result = $this->dbh->query($query);
                $row = $result->fetch_assoc();
            } catch (Exception $e) {
                throw new Exception("Could not get products from database: " . $e->getMessage());
            }
            $this->id = $id;
            $this->productId = 23;
            $this->name = $row['name'];
            $this->description = $row['name'];
            $this->owner = $row['owner'];
            $radiusclass = explode('|', $row['radiusclass']);
            $this->accesstype = $radiusclass[0];
            $this->accessqos = $radiusclass[1];
            $this->radiusClass = $row['radiusclass'];
            $this->simultaneousUse = $row['simultaneoususe'];
            $this->isTopupable = $row['istopupable'];
            $this->maxUsage = $row['maxusage'];
            $this->realm = $row['realm'];
            $this->status = $row['status'];
            $this->rolloverDay = $row['rolloverday'];
            $query = "SELECT b.uid,b.quotaname,b.quotaplan,b.bytesize,c.rolloverday,c.istopupable, c.status 
                        FROM ims_adsl.svProducts a, svQuotaPlans b, svProductQuotaPlan c 
                        WHERE c.svProduct=a.uid 
                          AND c.svQuotaPlan=b.uid
                          AND a.uid='$id'
                          AND owner='" . $this->owner . "'
                          AND c.status='active';
                      ";
            try {
                $result = $this->dbh->query($query);
            } catch (Exception $e) {
                throw new Exception("Could not get quotawheels for product from database: " . $e->getMessage());
            }

            $quotaWheels = array();
            while ($row = $result->fetch_assoc()) {
                $plan = array();
                preg_match('/([a-zA-Z]+)(\d*)/', $row['quotaplan'], $plan);
                array_push($quotaWheels, array(
                    "uid" => $row['uid'],
                    "name" => $row['quotaname'],
                    "plan" => $row['quotaplan'],
                    "planname" => $plan[2] . $plan[1],
                    "bytes" => $row['bytesize'],
                    "istopupable" => $row['istopupable'],
                    "status" => $row['status'],
                    "rolloverDay" => $row['rolloverday']));
            }
            $this->quotaWheels = $quotaWheels;
            $this->groupid = $this->getAssignedGroupId();
        }
    }

    public function update() {
        $uid = $this->id;
        $owner = $GLOBALS['login']->getLoginId();

        $groupid = $this->groupid;
        $name = $this->name;
        $istopupable = $this->isTopupable;
        $radiusclass = $this->mkRadiusClass(array('accesstype' => $this->accesstype, 'accessqos' => $this->accessqos));
        $this->radiusClass = $radiusclass;
        $simultaneoususe = $this->simultaneousUse;
        $maxusage = 0;
        $status = $this->status;
        $realm = $this->realm;
        $rolloverday = $this->rolloverDay;
        $quotawheels = $this->quotaWheels;

        $query1 = "
            UPDATE svProducts SET
                name='$name',
                istopupable='$istopupable',
                radiusclass='$radiusclass',
                simultaneoususe='$simultaneoususe',
                maxusage = '$maxusage',
                status = '$status',
                realm = '$realm',
                rolloverday = '$rolloverday'
           WHERE uid = '$uid' and owner = '$owner'";

        $query2 = "
            DELETE FROM svProductQuotaPlan 
            WHERE svProduct = '$uid';
            ";
        $query3 = array();
        foreach ($quotawheels as $quotawheel) {
            $query = "
               INSERT INTO svProductQuotaPlan(svProduct,svQuotaPlan,rolloverday,istopupable)
               VALUES ('$uid','$quotawheel','$rolloverday','$istopupable')
               ";
            array_push($query3, $query);
        }
        // TODO fix this. DB supports individual topup settings per product/plan
        // but frontend doesn't
        $query4 = "
            UPDATE svProductQuotaPlan b, svQuotaPlans a
            SET istopupable=0
            WHERE a.quotaname like '%Counter'
              and b.svProduct='$uid'
              and a.uid = b.svQuotaPlan;
            ";
        try {
            $this->dbh->query('begin');
            $this->dbh->query($query1);
            $this->dbh->query($query2);
            foreach ($query3 as $query) {
                $this->dbh->query($query);
            }
            $this->dbh->query($query4);
            $this->dbh->query('commit');
        } catch (Exception $e) {
            throw new Exception("Error updating product to database: " . $e->getMessage());
        }
    }

    public function delete($uid) {

        if (!UUID::is_valid($uid))
            throw new InvalidArgumentException;

        $query1 = "UPDATE svProductGroupProducts SET status='deleted' where product='$uid'";
        $query2 = "UPDATE svProducts SET status='deleted' where uid='$uid'";

        try {
            $this->dbh->query('begin');
            $this->dbh->query($query1);
            $this->dbh->query($query2);
            $this->dbh->query('commit');
        } catch (Exception $e) {
            throw new Exception("Error deleting product from database: " . $e->getMessage());
        }
    }

    public static function options() {
        return array(
            /*
              'bundlesize' => array(
              'description' => 'Bundle Size',
              'defaultvalue' => '',
              'value' => NULL,
              'mandatory' => TRUE,
              'immutable' => array(),
              'validation' => array('regex' => '^[0-9]*$', 'class' => 'number'),
              'hint' => 'Size of traffic bundle in GB'
              ),
             * 
             */
            'callingstation' => array(
                'description' => 'ADSL line number to allow',
                'defaultvalue' => '',
                'value' => NULL,
                'mandatory' => FALSE,
                'immutable' => array(),
                'validation' => array('regex' => '^[0-9]*$', 'class' => 'phone'),
                'hint' => 'ADSL line number to limit connections from'
            )
        );
    }

    public function getQuotaWheels() {
        return $this->quotaWheels;
    }

    public function getQuotaWheel($uid) {
        if (empty($this->id))
            throw new Exception("Attempting to get quota wheel for unloaded product");
        foreach ($this->quotaWheels as $quotaWheel) {
            if ($quotaWheel['uid'] == $uid) {
                return $quotaWheel;
            }
        }
        return null;
    }
    
    public function getQuotaWheelByName($name,$plan) {
        if (empty($this->id))
            throw new Exception("Attempting to get quota wheel for unloaded product");
        foreach ($this->quotaWheels as $quotaWheel) {
            if ($quotaWheel['name'] == $name and $quotaWheel['plan'] == $plan) {
                return $quotaWheel;
            }
        }
        return null;        
    }

    public function getRadiusClass() {
        return $this->radiusClass;
    }

    public function getSimultaneousUse() {
        return $this->simultaneousUse;
    }

    public function getAssignedGroupId($id = null) {
        if (isset($id))
            $this->read($id);

        if (empty($this->id))
            throw new Exception("Attempting to get assigned group for unloaded product");

        $query = "select productgroup from svProductGroupProducts where product='" . $this->id . "'";

        try {
            $result = $this->dbh->query($query);
            $row = $result->fetch_assoc();
        } catch (Exception $e) {
            throw new Exception($e);
        }
        return $row['productgroup'];
    }

    public function getAccessTypes() {
        $query = "select * from svAccessTypes";
        $accessTypes = array();
        try {
            $result = $this->dbh->query($query);
            while ($row = $result->fetch_assoc()) {
                array_push($accessTypes, array(
                    "uid" => $row['uid'],
                    "name" => $row['name'],
                    "status" => $row['status']));
            }
            return $accessTypes;
        } catch (Exception $e) {
            throw new Exception("Error getting access types: " . $e->getMessage());
        }
    }

    public function getAccessQOSs() {
        $query = "select * from svAccessQOS";
        $accessQOSs = array();
        try {
            $result = $this->dbh->query($query);
            while ($row = $result->fetch_assoc()) {
                array_push($accessQOSs, array(
                    "uid" => $row['uid'],
                    "name" => $row['name'],
                    "status" => $row['status']));
            }
            return $accessQOSs;
        } catch (Exception $e) {
            throw new Exception("Error getting access QOS: " . $e->getMessage());
        }
    }

    public function mkRadiusClass($params) {
        $accesstype = isset($params['accesstype']) ? $params['accesstype'] : 'N';
        $accessqos = isset($params['accessqos']) ? $params['accessqos'] : 'S';
        return "$accesstype|$accessqos|10240|0|0|0|31";
    }

    public function getAvailableQuotaWheels() {
        $query = "select * from svQuotaPlans";
        $quotawheels = array();
        $names = array();
        $plans = array();
        $bytes = array();
        try {
            $result = $this->dbh->query($query);
            while ($row = $result->fetch_assoc()) {
                $plan = array();
                preg_match('/([a-zA-Z]+)(\d*)/', $row['quotaplan'], $plan);
                array_push($quotawheels, array(
                    "uid" => $row['uid'],
                    "name" => $row['quotaname'],
                    "plan" => $row['quotaplan'],
                    "planname" => $plan[2] . $plan[1],
                    "bytes" => $row['bytesize'],
                    "status" => $row['status']
                ));
                array_push($names, $row['quotaname']);
                array_push($plans, $plan[2] . $plan[1]);
            }
            array_multisort($names, SORT_ASC, $plans, SORT_NUMERIC, $quotawheels);
            return $quotawheels;
        } catch (Exception $e) {
            throw new Exception("Error getting available quotawheels: " . $e->getMessage());
        }
    }

}

class ProductList_ruxSandvine extends ProductList {

    public function getList() {
        if (isset($this->list))
            return $this->list;

        $this->list = new Collection();

        $owner = $GLOBALS['login']->getLoginId();

        $query = "select uid from svProducts where owner='$owner'";
        if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
            $result = $this->dbh->query($query);
        } else {
            throw new Exception("Unsupported DB type for adsl_meta_dbtype");
        }
        if (!$result) {
            throw new Exception("Could not get owner list");
        }
        while ($row = $result->fetch_assoc()) {
            $product = ProductFactory::Create();
            $product->read($row['uid']);
            $this->list->addItem($product, $row['uid']);
        }
        return $this->list;
    }

}

/*
 * 
 * 
 * 
 */

class ProductGroup_ruxSandvine extends ProductGroup {

    public function create($params) {
        $owner = $GLOBALS['login']->getLoginId();
        $uid = UUID::v4();
        $query = "INSERT INTO svProductGroups(uid,name,owner) VALUES('$uid','$params','$owner')";
        try {
            $result = $this->dbh->query($query);
        } catch (Exception $e) {
            throw new Exception("Error in ProductGroup_ruxSandvine->create: " . $e->getMessage());
        }
        return $uid;
    }

    public function read($id) {
        $owner = $GLOBALS['login']->getLoginId();

        $query = "select * from svProductGroups where owner='$owner' and uid='$id'";

        try {
            $result = $this->dbh->query($query);
            $row = $result->fetch_assoc();
        } catch (Exception $e) {
            throw new Exception($e);
        }

        $this->name = $row['name'];
        $this->status = $row['status'];
        $this->owner = $row['owner'];
        $this->id = $row['uid'];
    }

    public function update() {
        $uid = $this->id;
        $owner = $GLOBALS['login']->getLoginId();

        $name = $this->name;
        $status = $this->status;

        $query = "UPDATE svProductGroups set 
                    name='$name',
                    status='$status'
                  WHERE
                    uid='$uid' and owner='$owner'
                  ";
        try {
            $result = $this->dbh->query($query);
        } catch (Exception $e) {
            throw new Exception("Error in ProductGroup_ruxSandvine->update: " . $e->getMessage());
        }
    }

    public function delete($uid) {
        $owner = $GLOBALS['login']->getLoginId();
        if (!UUID::is_valid($uid))
            throw new InvalidArgumentException;

        //$query = "DELETE FROM svProductGroups WHERE uid='$uid' and owner='$owner'";
        $query = "UPDATE svProductGroups SET status='deleted' WHERE uid='$uid' and owner='$owner'";
        try {
            $result = $this->dbh->query($query);
        } catch (Exception $e) {
            throw new Exception("Error in ProductGroup_ruxSandvine->delete: " . $e->getMessage());
        }
    }

    public function getProductGroupProductList($id) {
        $query = "select b.* from svProducts b, svProductGroupProducts c 
                    where c.productgroup='$id'
                      and c.product=b.uid
                    order by name";

        if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
            $result = $this->dbh->query($query);
        } else {
            throw new Exception("Unsupported DB type for adsl_meta_dbtype");
        }
        $groupProducts = array();
        while ($row = $result->fetch_assoc()) {
            array_push($groupProducts, $row);
        }
        return $groupProducts;
    }

}

class ProductGroupList_ruxSandvine extends ProductGroupList {

    public function getList() {
        if (isset($this->list))
            return $this->list;

        $this->list = new Collection();

        $owner = $GLOBALS['login']->getLoginId();

        $query = "select * from svProductGroups where owner='$owner' order by name";

        if ($GLOBALS['config']->adsl_meta_dbtype == 'mysqli') {
            $result = $this->dbh->query($query);
        } else {
            throw new Exception("Unsupported DB type for adsl_meta_dbtype");
        }
        while ($row = $result->fetch_assoc()) {
            $productGroup = ProductGroupFactory::Create();
            $productGroup->read($row['uid']);
            $this->list->addItem($productGroup, $row['uid']);
        }
        return $this->list;
    }

}

?>
