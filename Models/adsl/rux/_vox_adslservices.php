<?php

class VoxADSL {

    private $exchange;
    private $username;
    private $password;
    private $url = NULL;
    
    const OK = 'COMPLETED';
    const FAILED = 'FAILED';

    function call_method($param) {

        if ($this->url == NULL)
            throw new Exception("No method specified for Vox Exchange service");
        if ($param == NULL)
            throw new Exception("Null parameter send through");
        
        $json_param = json_encode($param);
        
        if ($json_param == FALSE)
            throw new Exception("Could not convert parameter to json object");

        try {
            $username = $this->username;
            $password = $this->password;
            $exchange = $this->exchange;
            $header = 'Content-Type: application/json' . "\r\n";
            $header .= "Username: $username" . "\r\n";
            $header .= "Password: $password" . "\r\n";
            $header .= "Exchange: $exchange" . "\r\n";

            $http_context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $header,
                    'content' => $json_param,
                ),
                    ));
            ini_set('default_socket_timeout', 300);
            $result_json = file_get_contents($this->url, null, $http_context);
            $result = json_decode($result_json,true);
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    function loadMethod($service, $methodName) {

        $URL_ADSL_SERVICES = "http://rux-test.voxdev.co.za";
        //$URL_ADSL_SERVICES = "http://rux.vox.co.za";
        $URL_ADSL_SERVICES_USER = 'philipc';
        $URL_ADSL_SERVICES_PASS = 'Bpcxsa4%^';
        $URL_ADSL_SERVICES_EXCHANGE = 'T2';

        $URL_ADSL_USAGE = "http://rux.vox.co.za";
        $URL_ADSL_USAGE_USER = 'philipc';
        $URL_ADSL_USAGE_PASS = 'Bpcxsa4%^';
        $URL_ADSL_USAGE_EXCHANGE = 'T2';

        /*
         * Account services
         */
        $methodList['account']['URL'] = $URL_ADSL_SERVICES;
        $methodList['account']['exchange'] = $URL_ADSL_SERVICES_EXCHANGE;
        $methodList['account']['username'] = $URL_ADSL_SERVICES_USER;
        $methodList['account']['password'] = $URL_ADSL_SERVICES_PASS;
        $methodList['account']['method']['isaccountavailable'] = '/V2/ADSL/ACCOUNT/ISACCOUNTAVAILABLE';
        $methodList['account']['method']['findbyattributes'] = '/V2/ADSL/ACCOUNT/FINDBYATTRIBUTES';
        $methodList['account']['method']['findbyid'] = '/V2/ADSL/ACCOUNT/FINDBYID';
        $methodList['account']['method']['findbyname'] = '/V2/ADSL/ACCOUNT/FINDBYNAME';
        $methodList['account']['method']['findbysystemid'] = '/V2/ADSL/ACCOUNT/FINDBYSYSTEMID';
        $methodList['account']['method']['findbysystemreference'] = '/V2/ADSL/ACCOUNT/FINDBYSYSTEMREFERENCE';
        $methodList['account']['method']['findallbysystemreference'] = '/V2/ADSL/ACCOUNT/FINDALLBYSYSTEMREFERENCE';
        $methodList['account']['method']['findhardcapped'] = '/V2/ADSL/ACCOUNT/FINDHARDCAPPED';
        $methodList['account']['method']['findinactiveaccounts'] = '/V2/ADSL/ACCOUNT/FINDINACTIVEACCOUNTS';
        $methodList['account']['method']['activate'] = '/V2/ADSL/ACCOUNT/PROVISION/ACTIVATE';
        $methodList['account']['method']['addattribute'] = '/V2/ADSL/ACCOUNT/PROVISION/ADDATTRIBUTE';
        $methodList['account']['method']['create'] = '/V2/ADSL/ACCOUNT/PROVISION/CREATE';
        $methodList['account']['method']['delete'] = '/V2/ADSL/ACCOUNT/PROVISION/DELETE';
        $methodList['account']['method']['hardcap'] = '/V2/ADSL/ACCOUNT/PROVISION/HARDCAP';
        $methodList['account']['method']['softcap'] = '/V2/ADSL/ACCOUNT/PROVISION/SOFTCAP';
        $methodList['account']['method']['suspend'] = '/V2/ADSL/ACCOUNT/PROVISION/SUSPEND';
        $methodList['account']['method']['updateallowedusage'] = '/V2/ADSL/ACCOUNT/PROVISION/UPDATEALLOWEDUSAGE';
        $methodList['account']['method']['updatecustomerreference'] = '/V2/ADSL/ACCOUNT/PROVISION/UPDATECUSTOMERREFERENCE';
        $methodList['account']['method']['updatemaximumallowedusage'] = '/V2/ADSL/ACCOUNT/PROVISION/UPDATEMAXIMUMALLOWEDUSAGE';
        $methodList['account']['method']['updatenote'] = '/V2/ADSL/ACCOUNT/PROVISION/UPDATENOTE';
        $methodList['account']['method']['updatepassword'] = '/V2/ADSL/ACCOUNT/PROVISION/UPDATEPASSWORD';
        $methodList['account']['method']['updateaccounttype'] = '/V2/ADSL/ACCOUNT/PROVISION/UPDATEACCOUNTTYPE';


        /*
         * Account Profile services
         */
        $methodList['profile']['URL'] = $URL_ADSL_SERVICES;
        $methodList['profile']['exchange'] = $URL_ADSL_SERVICES_EXCHANGE;
        $methodList['profile']['username'] = $URL_ADSL_SERVICES_USER;
        $methodList['profile']['password'] = $URL_ADSL_SERVICES_PASS;
        $methodList['profile']['method']['findbyid'] = '/V2/ADSL/ACCOUNTPROFILE/FINDBYID';
        $methodList['profile']['method']['findbyname'] = '/V2/ADSL/ACCOUNTPROFILE/FINDBYNAME';
        $methodList['profile']['method']['findbysystemid'] = '/V2/ADSL/ACCOUNTPROFILE/FINDBYSYSTEMID';
        $methodList['profile']['method']['findall'] = '/V2/ADSL/ACCOUNTPROFILE/FINDALL';
        $methodList['profile']['method']['create'] = '/V2/ADSL/ACCOUNTPROFILE/CREATE';
        $methodList['profile']['method']['delete'] = '/V2/ADSL/ACCOUNTPROFILE/DELETE';
        $methodList['profile']['method']['update'] = '/V2/ADSL/ACCOUNTPROFILE/UPDATE';

        /*
         * Account usage services
         */
        $methodList['usage']['URL'] = $URL_ADSL_USAGE;
        $methodList['usage']['exchange'] = $URL_ADSL_USAGE_EXCHANGE;
        $methodList['usage']['username'] = $URL_ADSL_USAGE_USER;
        $methodList['usage']['password'] = $URL_ADSL_USAGE_PASS;
        $methodList['usage']['method']['findbyid'] = '/V2/ADSL/USAGE/FINDBYID';
        $methodList['usage']['method']['dailybymonth'] = '/V2/ADSL/USAGE/DAILYBYMONTH';
        $methodList['usage']['method']['totalusagebymonth'] = '/V2/ADSL/USAGE/TOTALUSAGEBYMONTH';
        $methodList['usage']['method']['totalusage'] = '/V2/ADSL/USAGE/TOTALUSAGE';
        $methodList['usage']['method']['totalusagebydaterange'] = '/V2/ADSL/USAGE/TOTALUSAGEBYDATERANGE';
        $methodList['usage']['method']['forreference'] = '/V2/ADSL/USAGE/FORREFERENCE';
        $methodList['usage']['method']['bynumberofmonthsforaccount'] = '/V2/ADSL/USAGE/BYNUMBEROFMONTHSFORACCOUNT';

        /*
         * ADSL usage methods - deprecated
        $methodList['adsl']['URL'] = $URL_ADSL_USAGE;
        $methodList['adsl']['exchange'] = $URL_ADSL_USAGE_EXCHANGE;
        $methodList['adsl']['username'] = $URL_ADSL_USAGE_USER;
        $methodList['adsl']['password'] = $URL_ADSL_USAGE_PASS;
        $methodList['adsl']['method']['usage'] = '/ADSL/USAGE';
         * 
         */

        $service = strtolower($service);
        $methodName = strtolower($methodName);

        if (empty($methodList[$service]['method'][$methodName]))
            throw new Exception("Requested Vox ADSL service does not exist");

        $this->username = $methodList[$service]['username'];
        $this->password = $methodList[$service]['password'];
        $this->exchange = $methodList[$service]['exchange'];
        $this->url = $methodList[$service]['URL'] . $methodList[$service]['method'][$methodName];
    }

}

?>
