<?php

require_once('_vox_adslservices.php');

class Account_ruxT2Services extends Account {

    //private $quotaWheels;
    protected $options = array(
        'needed' => array(
            'mandatory' => false
        )
    );
    protected $showCancelled = FALSE;

    public function create() {
        if (!($this->password && $this->username && $this->product))
            throw new Exception("Required information not present for account creation");

        $loginId = $GLOBALS['login']->getLoginId();

        $this->ownerObj = OwnerFactory::Create();
        if ($this->ownerObj->getByLogin($loginId) != $loginId)
            throw new Exception("Owner not verified");

        $this->productObj = ProductFactory::Create();
        $this->productObj->read($this->product);

        $createObj = array(
            'callingSystemId' => $this->ownerObj->login,
            'callingSystemReference' => $this->ownerObj->login,
            'userName' => $this->username,
            'password' => $this->password,
            'friendlyName' => (isset($this->description) ? $this->description : ''),
            'allowedUsage' => 0,
            'accountProfileId' => $this->productObj->getProductId(),
            'isActive' => true,
            'radiusClass' => $this->productObj->getRadiusClass(),
            'simultaneousUse' => $this->productObj->getSimultaneousUse(),
            'radiusProfile' => 'normal',
            'quotaWheelInfos' => $this->productObj->getQuotaWheels(),
            'radiusCallingStationId' => (isset($this->callingstation) ? $this->callingstation : '')
        );

        $adsl_service = new VoxADSL();
        $adsl_service->loadMethod('account', 'create');
        $result = $adsl_service->call_method($createObj);
        if ($result['responseCode'] != 'COMPLETED')
            throw new Exception($result['message']);
        $this->id = $result['account']['id'];
        $this->accountId = $this->id;
        $adsl_service->loadMethod('account', 'addattribute');
        $updateObj = array(
            'accountId' => $this->id,
            'attributeKey' => '_accesskey_',
            'attributeValue' => encrypt($this->password, strtolower($this->username)),
            'systemInfos' => array()
        );
        $adsl_service->call_method($updateObj);
        if ($result['responseCode'] != 'COMPLETED')
            error_log("Error creating _accesskey__: " . $result['message']);
        $updateObj = array(
            'accountId' => $this->id,
            'attributeKey' => '_product_',
            'attributeValue' => $this->productObj->getId(),
            'systemInfos' => array()
        );
        $adsl_service->call_method($updateObj);
        if ($result['responseCode'] != 'COMPLETED')
            error_log("Error creating _product_: " . $result['message']);
        // call read to cache and all that
        $this->read($this->id);
        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            //clean up accountlist
            $cache->type = 'accountlist';
            $cache->provider = $GLOBALS['config']->adsl_model_provider;
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
                $cache->provider = $GLOBALS['config']->adsl_model_provider;
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
        $this->callingstation = isset($result['account']['radiusCallingStationId']) ? $result['account']['radiusCallingStationId'] : NULL;
        $this->topupsize = (string) ($result['account']['allowedUsage'] - $result['account']['baseUsage']);
        $this->maxallowedusage = $result['account']['maxAllowedUsage'];
        $this->owner = $result['account']['accountReference']['systemId'];
        if (isset($result['account']['attributes'])) {
            foreach ($result['account']['attributes'] as $attrib) {
                if (isset($attrib['key']) and isset($attrib['value']))
                    $this->$attrib['key'] = $attrib['value'];
            }
        }

        $this->product = isset($this->_product_) ? $this->_product_ : NULL;
        $this->description = isset($result['account']['customerReference']) ? htmlspecialchars($result['account']['customerReference']) : '';
        $this->note = isset($result['account']['note']) ? htmlspecialchars($result['account']['note']) : '';


        if (isset($this->product)) {
            $accountProduct = ProductFactory::Create();
            $accountProduct->read($this->product);

            if (isset($result['account']['quotaWheelInfos'])) {
                $this->quotaWheels = array();
                foreach ($result['account']['quotaWheelInfos'] as $quotaWheel) {
                    $productQuotaWheel = $accountProduct->getQuotaWheelByName($quotaWheel['name'], $quotaWheel['plan']);
                    $productQuotaWheel['remainingTopUp'] = $quotaWheel['remainingTopUp'];
                    
                    // Need to do it this way because of the __set magic method and arrays issue 
                    // see http://php.net/manual/en/language.oop5.magic.php#99058
                    $this->quotaWheels += array($productQuotaWheel['uid'] => $productQuotaWheel);
                    
                    //$this->quotaWheels[$productQuotaWheel['uid']] = $productQuotaWheel;
                    //$this->quotaWheels[$productQuotaWheel['uid']]['remainingTopUp'] = $quotaWheel['remainingTopUp'];
                }
            }
        }

        if ($this->owner != $GLOBALS['login']->getLoginId())
            throw new Exception('Accessing account owner does not own');
        return $this->id;
    }

    public function update($parameters) {
        if (!isset($this->id))
            throw new Exception("No account loaded for update");
        $adsl_service = new VoxADSL();
        foreach ($parameters as $parameter => $value) {
            try {
                switch ($parameter) {
                    case 'password':
                        $updateObj = array(
                            'accountId' => (int) $this->id,
                            'password' => $value,
                        );

                        $adsl_service->loadMethod('account', 'updateaccount');
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating password: " . $result['message']);
                        /*
                          $adsl_service->loadMethod('account', 'updatepassword');
                          $updateObj = array(
                          'accountId' => $this->id,
                          'password' => "$value"
                          );
                          $result = $adsl_service->call_method($updateObj);
                          if ($result['responseCode'] == VoxADSL::FAILED)
                          throw new Exception("Error encountered updating password: " . $result['message']);
                         */
                        $adsl_service->loadMethod('account', 'addattribute');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'attributeKey' => '_accesskey_',
                            'attributeValue' => encrypt($value, strtolower($this->username)),
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
                        $this->description = $value;
                        break;
                    case 'status':
                        $loginId = $GLOBALS['login']->getLoginId();

                        $this->ownerObj = OwnerFactory::Create();
                        if ($this->ownerObj->getByLogin($loginId) != $loginId)
                            throw new Exception("Owner not verified");
                        $updateObj = array(
                            'accountId' => (int) $this->id,
                            'message' => ''
                        );
                        if (strtolower($value) == 'suspended') {
                            $adsl_service->loadMethod('account', 'suspend');
                        } elseif (strtolower($value) == 'active') {
                            $adsl_service->loadMethod('account', 'activate');
                        }
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating status: " . $result['message']);
                        $this->status = strtoupper($value);
                        break;
                    case 'notifycell':
                    case 'notifyemail':
                    case 'mailreport':
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
                    case 'callingstation':
                        $value = strtoupper($value);
                        $updateObj = array(
                            'accountId' => (int) $this->id,
                            'radiusCallingStationId' => "$value",
                        );
                        $adsl_service->loadMethod('account', 'updateaccount');
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] == VoxADSL::FAILED)
                            throw new Exception("Error encountered updating callingstation: " . $result['message']);

                        /*
                          $adsl_service->loadMethod('account', 'addattribute');
                          $updateObj = array(
                          'accountId' => $this->id,
                          'attributeKey' => $parameter,
                          'attributeValue' => $value,
                          'systemInfos' => array()
                          );
                          $result = $adsl_service->call_method($updateObj);
                          if ($result['responseCode'] == VoxADSL::FAILED)
                          throw new Exception("Error encountered updating attribute callingstation: " . $result['message']);
                         * 
                         */

                        break;
                    case 'product':
                        $this->productObj = ProductFactory::Create();
                        $this->productObj->read($value);
                        $quotawheels = $this->productObj->getQuotaWheels();
                        /*
                          $adsl_service->loadMethod('quota', 'deletequotawheelsbyaccount');
                          $updateObj = array(
                          'accountId' => $this->id,
                          );
                          $result = $adsl_service->call_method($updateObj);
                          if ($result['responseCode'] != 'COMPLETED')
                          error_log("Error deleting quotawheels: " . $result['message']);
                         */
                        $adsl_service->loadMethod('quota', 'addquotawheeltoaccount');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'quotaWheelInfos' => $quotawheels
                        );
                        $result = $adsl_service->call_method($updateObj);
                        $adsl_service->loadMethod('account', 'addattribute');
                        $updateObj = array(
                            'accountId' => $this->id,
                            'attributeKey' => '_product_',
                            'attributeValue' => $this->productObj->getId(),
                            'systemInfos' => array()
                        );
                        $result = $adsl_service->call_method($updateObj);
                        if ($result['responseCode'] != 'COMPLETED')
                            error_log("Error updating _product__: " . $result['message']);
                        break;
                };
                if ($GLOBALS['config']->use_cache) {
                    $cache = CacheFactory::Create();
                    $cache->type = 'account';
                    $cache->provider = $GLOBALS['config']->adsl_model_provider;
                    $cache->identifier = "$this->id";
                    if ($cache->load())
                        $cache->expire();
                    $cache->type = 'accountlist';
                    $cache->provider = $GLOBALS['config']->adsl_model_provider;
                    $cache->identifier = $GLOBALS['login']->getLoginId();
                    if ($cache->load()) {
                        $templist = $cache->getData();
                        $templist->removeItem($this->id);
                        //$this->read($this->id);
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
            $cache->provider = $GLOBALS['config']->adsl_model_provider;
            $cache->identifier = "$this->id";
            if ($cache->load())
                $cache->expire();
            //clean up accountlist
            $cache->type = 'accountlist';
            $cache->provider = $GLOBALS['config']->adsl_model_provider;
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
                /*
                  'systemReference' => array(
                  'description' => 'System reference',
                  'defaultvalue' => '',
                  'value' => NULL,
                  'mandatory' => TRUE,
                  'immutable' => array('update' => TRUE),
                  'validation' => array('regex' => '.*'),
                  'hint' => 'System reference for account - this cannot be changed once created'
                  ),
                 * 
                 */
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

        if (isset($result['responseCode']) and $result['responseCode'] == VoxADSL::OK) {
            if (isset($result['accounts']['id'])) {
                $fix = $result['accounts'];
                unset($result);
                $result = array('accounts' => array($fix));
            }
            foreach ($result['accounts'] as $account) {
                if (
                        $account['accountReference']['systemId'] == $GLOBALS['login']->getLoginId()
                        and $account['username'] = $username
                ) {
                    $this->read($account);

                    return $account['id'];
                }
            }
        }
        return FALSE;
    }

}

class AccountList_ruxT2Services extends AccountList {

    public function getList($offset = 0, $limit = 0, $searchkey = '.*') {
        if (!isset($this->list)) {
            error_log($GLOBALS['config']->adsl_model_provider);
            if ($GLOBALS['config']->use_cache) {
                $cache = CacheFactory::Create();
                $cache->type = 'accountlist';
                $cache->provider = $GLOBALS['config']->adsl_model_provider;
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
                if (isset($result['accounts']['id'])) {
                    $fix = $result['accounts'];
                    unset($result);
                    $result = array('accounts' => array($fix));
                }
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
        /*
          if ($offset > 0 or $limit > 0) {
          $list_full = $this->list->getAll();
          $list_slice = array_slice($list_full, $offset, $limit);
          $list = new Collection($list_slice);
          return $list;
          }
         * 
         */
        $this->count = $this->list->count();

        return $this->list;
    }

}

?>
