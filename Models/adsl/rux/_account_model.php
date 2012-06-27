<?php

require_once('_vox_adslservices.php');

class Account_rux extends Account {

    protected $options = array(
        'needed' => array(
            'mandatory' => false
        )
    );
    protected $showCancelled = FALSE;

    public function create() {
        if (!($this->password && $this->username && $this->product && $this->bundlesize))
            throw new Exception("Required information not present for account creation");

        $loginId = $GLOBALS['login']->getLoginId();

        $this->ownerObj = OwnerFactory::Create();
        if ($this->ownerObj->getByLogin($loginId) != $loginId)
            throw new Exception("Owner not verified");

        $this->productObj = ProductFactory::Create();
        $this->productObj->read($this->product);

        $createObj = array(
            'callingSystemId' => $this->ownerObj->login,
            'callingSystemReference' => (isset($this->systemReference) ? $this->systemReference : 'UNKNOWN'),
            'userName' => $this->username,
            'password' => $this->password,
            'friendlyName' => (isset($this->description) ? $this->description : ''),
            'allowedUsage' => $this->bundlesize,
            'accountProfileId' => $this->productObj->getId(),
            'isActive' => true
        );

        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('account', 'create');
        $result = $adsl_service->call_method($createObj);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception("Error creating account: " . $result['message']);
        $this->id = $result['account']['id'];
        $this->accountId = $this->id;
        $adsl_service->loadMethod('account', 'addattribute');
        $updateObj = array(
            'accountId' => $this->id,
            'attributeKey' => '_accesskey_',
            'attributeValue' => encrypt(strtolower($this->username), $this->password),
            'systemInfos' => array()
        );
        $adsl_service->call_method($createObj);
        if ($result['responseCode'] != 'COMPLETED')
            error_log("Error creating _accesskey__: " . $result['message']);
        // call read to cache and all that
        $this->read($this->id);
        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            //clean up accountlist
            $cache->type = 'accountlist';
            $cache->provider = 'rux';
            $cache->identifier = $GLOBALS['login']->getLoginId();
            if ($cache->load()) {
                $templist = $cache->getData();
                $templist->addItem($this, $this->id);
                $cache->save($templist);
            }
        }
        return $this->id;
    }

    public function read($id) {

        if (empty($id))
            throw new Exception("No account ID in read request");

        if (is_array($id)) {
            $result['account'] = $id;
            $id = $result['account']['id'];
            //$this->loadAccount($id);
            //return;
        } else {

            if ($GLOBALS['config']->use_cache) {
                $cache = CacheFactory::Create();
                $cache->type = 'account';
                $cache->provider = 'rux';
                $cache->identifier = "$id";
            }
            if ($GLOBALS['config']->use_cache and $cache->load()) {
                $result = $cache->getData();
                //todo remove
                //error_log(print_r($result, true));
            } else {
                $adsl_service = new VoxADSL();
                $adsl_service->loadMethod('account', 'findbyid');

                $accountfind_obj = array(
                    'accountId' => $id
                );

                $result = $adsl_service->call_method($accountfind_obj);
                if ($result['responseCode'] == VoxADSL::FAILED)
                    throw new Exception("Error encountered finding account: " . $result['message']);
                if (empty($result['account']) or isset($result['account']['deleted']))
                    throw new Exception("Account does not exist");

                //todo remove
                //error_log(print_r($result, true));

                if ($GLOBALS['config']->use_cache) {
                    $cache->save($result);
                }
            }
        }

        if (isset($result['account']['deleted']) and !$this->showCancelled)
            throw new Exception("Account does not exist");
        $this->id = $id;
        $this->accountid = $id;
        $this->systemReference = $result['account']['accountReference']['systemReference'];
        $this->username = $result['account']['username'];
        $this->password = $result['account']['password'];
        $this->status = $result['account']['status'];
        $this->bundlesize = $result['account']['baseUsage'];
        $this->topupsize = (string) ($result['account']['allowedUsage'] - $result['account']['baseUsage']);
        $this->maxallowedusage = $result['account']['maxAllowedUsage'];
        $this->owner = $result['account']['accountReference']['systemId'];
        $this->product = $result['account']['accountProfile']['id'];
        if (isset($result['account']['attributes'])) {
            foreach ($result['account']['attributes'] as $attrib) {
                if (isset($attrib['key']) and isset($attrib['value']))
                    $this->$attrib['key'] = $attrib['value'];
            }
        }
        $this->description = isset($result['account']['customerReference']) ? htmlspecialchars($result['account']['customerReference']) : '';
        $this->note = isset($result['account']['note']) ? htmlspecialchars($result['account']['note']) : '';

        /*
         * TODO uncomment
         */
        if ($this->owner != $GLOBALS['login']->getLoginId())
            throw new Exception('Accessing account owner does not own');
        return $this->id;
    }

    public function update($parameters) {
        $adsl_service = new VoxADSL();
        foreach ($parameters as $parameter => $value) {
            try {
                switch ($parameter) {
                    case 'password':
                        $adsl_service->loadMethod('account', 'updatepassword');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'password' => "$value"
                        );
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating password: " . $result['message']);
                        $adsl_service->loadMethod('account', 'addattribute');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'attributeKey' => '_accesskey_',
                            'attributeValue' => encrypt(strtolower($this->username), $value),
                            'systemInfos' => array()
                        );
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating password: " . $result['message']);
                        break;
                    case 'note':
                        $adsl_service->loadMethod('account', 'updatenote');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'note' => "$value"
                        );
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating note: " . $result['message']);
                        break;
                    case 'bundlesize':
                        $adsl_service->loadMethod('account', 'updateallowedusage');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'allowedUsage' => $value,
                            'isPermanent' => TRUE
                        );
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating allowed usage: " . $result['message']);
                        break;
                    case 'topup':
                        $total = $this->bundlesize + $this->topupsize + $value;
                        $adsl_service->loadMethod('account', 'updateallowedusage');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'allowedUsage' => $total,
                            'isPermanent' => FALSE
                        );
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating allowed usage: " . $result['message']);
                        break;
                    case 'description':
                        $adsl_service->loadMethod('account', 'updatecustomerreference');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'customerReference' => "$value"
                        );
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating customer reference: " . $result['message']);
                        break;
                    case 'status':
                        $updateObj = array(
                            'accountId' => $this->id,
                            'message' => "$value"
                        );
                        if (strtolower($value) == 'suspended') {
                            $adsl_service->loadMethod('account', 'suspend');
                        } elseif (strtolower($value) == 'active') {
                            $adsl_service->loadMethod('account', 'activate');
                        }
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered suspending account: " . $result['message']);
                        break;
                    case 'notifycell':
                    case 'notifyemail':
                        $adsl_service->loadMethod('account', 'addattribute');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'attributeKey' => $parameter,
                            'attributeValue' => $value,
                            'systemInfos' => array()
                        );
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating attributes: " . $result['message']);
                        break;
                };
                if ($GLOBALS['config']->use_cache) {
                    $cache = CacheFactory::Create();
                    $cache->type = 'account';
                    $cache->provider = 'rux';
                    $cache->identifier = "$this->id";
                    if ($cache->load())
                        $cache->expire();
                    $cache->type = 'accountlist';
                    $cache->provider = 'rux';
                    $cache->identifier = $GLOBALS['login']->getLoginId();
                    if ($cache->load()) {
                        $templist = $cache->getData();
                        $templist->removeItem($this->id);
                        $this->read($this->id);
                        $templist->addItem($this, $this->id);
                        $cache->save($templist);
                    }
                }
            } catch (Exception $e) {
                throw new Exception("Error encountered updating account: " . $e->getMessage());
            }
        }
        return TRUE;
    }

    public function delete() {
        if (!isset($this->id))
            throw new Exception("No account loaded for deletion");
        if (empty($this->message))
            $this->message = '';
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('account', 'delete');
        $queryObj = array(
            'accountId' => "$this->id",
            'message' => "$this->message"
        );
        $result = $adsl_service->call_method($queryObj);
        if ($result['responseCode'] != VoxADSL::OK)
            throw new Exception("Could not delete account: " . $result['message']);
        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            $cache->type = 'account';
            $cache->provider = 'rux';
            $cache->identifier = "$this->id";
            if ($cache->load())
                $cache->expire();
            //clean up accountlist
            $cache->type = 'accountlist';
            $cache->provider = 'rux';
            $cache->identifier = $GLOBALS['login']->getLoginId();
            if ($cache->load()) {
                $templist = $cache->getData();
                $templist->removeItem($this->id);
                $cache->save($templist);
            }
        }
        $this->id = NULL;
        $this->product = NULL;
        $this->owner = NULL;
        $this->members = array();
        return TRUE;
    }

    public static function options() {
        return array(
            'systemReference' => array(
                'description' => 'System reference',
                'defaultvalue' => '',
                'value' => NULL,
                'mandatory' => TRUE,
                'immutable' => array('update' => TRUE),
                'validation' => array('regex' => '.*'),
                'hint' => 'System reference for account - this cannot be changed once created'
            ),
        );
    }

    public function isUsernameAvailable($username) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('account', 'isaccountavailable');
        $queryObj = array(
            'userName' => "$username"
        );
        $result = $adsl_service->call_method($queryObj);
        if (isset($result['responseCode']) and $result['responseCode'] == VoxADSL::FAILED)
            throw new Exception("Error encountered checking account availability: " . $result['message']);
        return $result['return'];
    }

    public function findByUsername($username) {
        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('account', 'FINDBYNAME');
        $queryObj = array(
            'userName' => "$username"
        );
        $result = $adsl_service->call_method($queryObj);
        if (isset($result['responseCode']) and $result['responseCode'] == VoxADSL::FAILED)
            throw new Exception("Error encountered finding by username: " . $result['message']);
        if (
                isset($result['responseCode'])
                and $result['responseCode'] == VoxADSL::OK
                and isset($result['accounts'])
                and $result['accounts']['accountReference']['systemId'] == $GLOBALS['login']->getLoginId()
        ) {
            $this->read($result['accounts']);
            return $result['accounts']['id'];
        }
        return FALSE;
    }

}

class AccountList_rux extends AccountList {

    public function getList($offset = 0, $limit = 0, $searchkey = '.*') {
        if (!isset($this->list)) {

            if ($GLOBALS['config']->use_cache) {
                $cache = CacheFactory::Create();
                $cache->type = 'accountlist';
                $cache->provider = 'rux';
                $cache->identifier = $GLOBALS['login']->getLoginId();
            }

            if ($GLOBALS['config']->use_cache and $cache->load()) {
                $this->list = $cache->getData();
                /*
                 * TODO speed this up, maybe key comparison

                  foreach ($this->list->keys() as $id) {
                  $account = AccountFactory::Create();
                  $account->read($id);
                  unset($account);
                  }
                 * 
                 */
            } else {
                $this->list = new Collection();

                $adsl_service = new VoxADSL();
                $adsl_service->loadMethod('account', 'FINDBYSYSTEMID');

                $accountfind_obj = array(
                    'systemId' => $GLOBALS['login']->getLoginId()
                );

                $result = $adsl_service->call_method($accountfind_obj);
                if ($result['responseCode'] == VoxADSL::FAILED)
                    throw new Exception("Error encountered fetching account list: " . $result['message']);
                foreach ($result['accounts'] as $item) {
                    if (empty($item['deleted'])) {
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
                }
                if ($GLOBALS['config']->use_cache) {
                    $cache->save($this->list);
                }
            }
        }
        $this->countall = $this->list->count();
        $orderedlist = array();
        $newlist = array();
        foreach ($this->list->getAll() as $value) {
            if (
                    preg_match("/$searchkey/i", $value->username) or
                    preg_match("/$searchkey/i", isset($value->description) ? $value->description : '') or
                    preg_match("/$searchkey/i", isset($value->systemReference) ? $value->systemReference : '')
            )
                $orderedlist[$value->username] = $value;
        }
        ksort($orderedlist);
        foreach ($orderedlist as $key => $value)
            array_push($newlist, $value);
        $this->list = new Collection($newlist);

        if ($offset > 0 or $limit > 0) {
            $list_full = $this->list->getAll();
            $list_slice = array_slice($list_full, $offset, $limit);
            $list = new Collection($list_slice);
            return $list;
        }
        $this->count = $this->list->count();

        return $this->list;
    }

}

?>
