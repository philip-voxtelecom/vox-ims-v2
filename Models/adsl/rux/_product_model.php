<?php

require_once('_vox_adslservices.php');

class Product_rux extends Product {

    public function create() {

        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            $cache->type = 'profilelist';
            $cache->provider = 'rux';
            // TODO fix identifier
            $cache->identifier = 'Datapro';
            $cache->expire();
        }
    }

    public function read($id) {

        if (!empty($id) and addslashes($id) == $id) {

            if ($GLOBALS['config']->use_cache) {
                $cache = CacheFactory::Create();
                $cache->type = 'profile';
                $cache->provider = 'rux';
                $cache->identifier = "$id";
            }
            if ($GLOBALS['config']->use_cache and $cache->load()) {
                $result = $cache->getData();
            } else {
                $adsl_service = new VoxADSL();
                $adsl_service->loadMethod('profile', 'findbyid');

                $profilefind_obj = array(
                    'profileId' => $id
                );

                $result = $adsl_service->call_method($profilefind_obj);
                if ($result['responseCode'] == VoxADSL::FAILED)
                    throw new Exception("Error encountered finding profile by id: " . $result['message']);
                if (empty($result['accountProfile']))
                    throw new Exception("Product not found");
                if ($GLOBALS['config']->use_cache) {
                    $cache->save($result);
                }
            }
            //var_dump($result);
            if (empty($result['accountProfile']))
                throw new Exception("Product not found");
            $this->id = $result['accountProfile']['id'];
            $this->productId = $this->id;
            $this->name = $result['accountProfile']['name'];
            $this->description = $result['accountProfile']['name'];
            $this->owner = $result['accountProfile']['systemId'];
            if ($result['accountProfile']['active'] == TRUE) {
                $this->status = 'active';
            }
        }
    }

    public function update() {
        
    }

    public function delete() {

        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            $cache->type = 'profilelist';
            $cache->provider = 'rux';
            // TODO fix identifier
            $cache->identifier = 'Datapro';
            $cache->expire();
        }
    }

    public static function options() {
        return array(
            'bundlesize' => array(
                'description' => 'Bundle Size',
                'defaultvalue' => '',
                'value' => NULL,
                'mandatory' => TRUE,
                'immutable' => array(),
                'validation' => array('regex' => '^[0-9]*$', 'class' => 'number'),
                'hint' => 'Size of traffic bundle in GB'
            ),
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

}

class ProductList_rux extends ProductList {

    public function getList() {
        if (isset($this->list))
            return $this->list;

        if ($GLOBALS['config']->use_cache) {
            $cache = CacheFactory::Create();
            $cache->type = 'profilelist';
            $cache->provider = 'rux';
            $cache->identifier = $GLOBALS['login']->getLoginId();
        }
        if ($GLOBALS['config']->use_cache and $cache->load()) {
            $this->list = $cache->getData();
        } else {
            $this->list = new Collection();

            $adsl_service = new VoxADSL();
            $adsl_service->loadMethod('profile', 'findbysystemid');

            $profilefind_obj = array(
                'systemId' => $GLOBALS['login']->getLoginId()
            );

            $result = $adsl_service->call_method($profilefind_obj);
            if ($result['responseCode'] == VoxADSL::FAILED)
                throw new Exception("Error encountered geting profile list: " . $result['message']);
            //var_dump($result);
            //Fix for single result in collection
            if (isset($result['accountProfiles']['id'])) {
                $fix = $result['accountProfiles'];
                unset($result);
                $result = array('accountProfiles' => array($fix));
            }
            foreach ($result['accountProfiles'] as $profile) {
                $product = ProductFactory::Create();
                $product->read($profile['id']);
                $this->list->addItem($product, $profile['id']);
            }
            if ($GLOBALS['config']->use_cache) {
                $cache->save($this->list);
            }
        }
        return $this->list;
    }

}

?>
