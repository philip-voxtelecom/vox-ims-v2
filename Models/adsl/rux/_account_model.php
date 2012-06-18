<?php

require_once('_vox_adslservices.php');

class Account_rux extends Account {

    public function create() {
        if (!($this->password && $this->name))
            throw new Exception("Required information not present for account creation");

        $loginId = $GLOBALS['login']->getLoginId();

        $this->owner = OwnerFactory::Create();
    }

    public function read($id) {

        if (is_array($id)) {
            $this->loadAccount($id);
            return;
        }

        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            $cache->type = 'account';
            $cache->provider = 'rux';
            $cache->identifier = "$id";
        }
        if ($GLOBALS['config']->use_cache and $cache->load()) {
            $result = $cache->getData();
        } else {
            $adsl_service = new VoxADSL();
            $adsl_service->loadMethod('account', 'findbyid');

            $accountfind_obj = array(
                'accountId' => $id
            );

            $result = $adsl_service->call_method($accountfind_obj);
            if ($GLOBALS['config']->use_cache) {
                $cache->save($result);
            }
        }
        $this->id = $id;
        $this->username = $result['account']['username'];
        $this->password = $result['account']['password'];
        $this->status = $result['account']['status'];
        if (!empty($result['account']['customerReference']))
            $this->description = htmlspecialchars($result['account']['customerReference']);
        $this->baseusage = $result['account']['baseUsage'];
        $this->allowedusage = $result['account']['allowedUsage'];
        $this->maxallowedusage = $result['account']['maxAllowedUsage'];
        $this->owner = $result['account']['accountReference']['systemId'];
        $this->productId = $result['account']['accountProfile']['id'];

        /*
         * TODO uncomment
         */
        //if ($this->owner != $GLOBALS['login']->getLoginId())
        //    throw new Exception('Accessing account owner does not own');
    }

    public function update() {
        if (!($this->password && $this->name))
            throw new Exception("Required information not present for account update");

        $query = "update users set
                       password='$this->password',
                       cellno='$this->cellno',
                       email='$this->email',
                       name='$this->name',
                       comments='$this->comments',
                       status='$this->status'
                  where id='$this->id'";

        pg_query($this->dbh, "BEGIN TRANSACTION");
        pg_query($this->dbh, $query);
        $result = pg_query($this->dbh, "COMMIT TRANSACTION");
        if (!$result) {
            throw new Exception("Database update error occured - " . pg_last_error());
        }
        $updated = false;
        /*
          if (isset($this->ownerproductoptionId))
          $updated = $this->product->update($this->id, $this->ownerproductoptionId);
          $this->productDetail->update($this->product->userProductId());
         *
         */
    }

    public function delete() {
        if (!isset($this->exists))
            throw new Exception("Account does not exist");
        if (preg_match('/.*%.*/', $this->id))
            throw new Exception("Invalid account ID");
        try {
            foreach ($this->product->getExpiredList($this->id) as $expiredUserProductId) {
                $this->productDetail->delete($expiredUserProductId);
            }
            $this->productDetail->delete($this->product->userProductId());
            $this->product->delete($this->id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        pg_query($this->dbh, "BEGIN TRANSACTION");

        $query = "delete from owneruser
                  where users_id=" . $this->id;
        pg_query($this->dbh, $query);
        $query = "delete from users
                  where id=" . $this->id;
        pg_query($this->dbh, $query);
        $result = pg_query($this->dbh, "COMMIT TRANSACTION");
        if (!$result) {
            throw new Exception("Database update error occured - " . pg_last_error());
        }
    }

    private function loadAccount($item) {
        $id = $item['id'];
        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            $cache->type = 'account';
            $cache->provider = 'rux';
            $cache->identifier = "$id";

            if (!$cache->load()) {
                $save_item = array('account' => $item);
                $cache->save($save_item);
            }
        }
        $this->id = $item['id'];
        $this->username = $item['username'];
        $this->password = $item['password'];
        $this->status = $item['status'];
        if (!empty($item['customerReference']))
            $this->description = htmlspecialchars($item['customerReference']);
        $this->baseusage = $item['baseUsage'];
        $this->allowedusage = $item['allowedUsage'];
        $this->maxallowedusage = $item['maxAllowedUsage'];
        $this->owner = $item['accountReference']['systemId'];
        $this->productId = $item['accountProfile']['id'];
    }

}

class AccountList_rux extends AccountList {

    public function getList() {
        if (isset($this->list))
            return $this->list;

        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            $cache->type = 'accountlist';
            $cache->provider = 'rux';
            $cache->identifier = $GLOBALS['login']->getLoginId();
        }
        if ($GLOBALS['config']->use_cache and $cache->load()) {
            $this->list = $cache->getData();
            foreach ($this->list->keys() as $id) {
                $account = AccountFactory::Create();
                $account->read($id);
                unset($account);
            }
        } else {
            $this->list = new Collection();

            $adsl_service = new VoxADSL();
            $adsl_service->loadMethod('account', 'FINDBYSYSTEMID');

            $accountfind_obj = array(
                'systemId' => $GLOBALS['login']->getLoginId()
            );

            $result = $adsl_service->call_method($accountfind_obj);
            foreach ($result['accounts'] as $item) {
                $account = AccountFactory::Create();
                $account->read($item);
                $this->list->addItem($account, $item['id']);
                /*
                  $this->list->addItem(array(
                  'id' => $item['id'],
                  'username' => $item['username'],
                  'status' => $item['status'],
                  'owner' => $item['accountReference']['systemId']
                  ));
                 * 
                 */
            }
            if ($GLOBALS['config']->use_cache) {
                $cache->save($this->list);
            }
        }
        return $this->list;
    }

}

?>
