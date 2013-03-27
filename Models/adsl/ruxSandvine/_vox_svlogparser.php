<?php

class VoxSVParser {

    private $url = NULL;

    const OK = 'OK';
    const FAILED = 'FAILED';

    function call_method($param, $method = 'get') {

        $method = strtoupper($method);
        $header = "Connection: close\r\n";


        if ($this->url == NULL)
            throw new Exception("No method specified for Vox SV log parsing service");
        if ($param == NULL)
            throw new Exception("Null parameter send through");

        if ($method == 'post' or $method == 'put') {
            $json_param = json_encode($param);
            $header .= 'Content-Type: application/json' . "\r\n";

            //error_log(print_r($json_param,true));

            if ($json_param == FALSE)
                throw new Exception("Could not convert parameter to json object");
        } else {
            $json_param = '';
            $header .= 'Content-Type: text/plain' . "\r\n";
        }

        $getparams = '?';
        if ($method == 'GET') {
            foreach ($param as $getparam => $value) {
                $this->url = str_replace("|$getparam|", $value, $this->url, $count);
                if ($count == 0) {
                    $getparams .= "$getparam=$value&";
                }
            }
            $getparams = rtrim($getparams, '&');
            $getparam = urlencode($getparam);
        }

        try {

            $http_context = stream_context_create(array(
                'http' => array(
                    'method' => $method,
                    'header' => $header,
                    'content' => $json_param,
                    'timeout' => 60,
                ),
                    ));
            ini_set('default_socket_timeout', 300);
            error_log(rtrim($this->url . $getparams, "?") . " - " . $json_param);
            $time_start = microtime(true);
            $result_json = file_get_contents(rtrim($this->url . $getparams, "?"), null, $http_context);
            $time = microtime(true) - $time_start;
            error_log($time . " seconds\n");
            if (empty($result_json))
                throw new Exception('No response from service');
            $result = json_decode($result_json, true);
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    function loadMethod($service, $methodName) {

        $methodList['usage']['URL'] = $GLOBALS['config']->svlogparser_url;
        $parserVer = $GLOBALS['config']->svlogparser_version;
        $methodList['usage']['method']['subscriber'] = "/$parserVer/usages/|subscriber|/usage";

        $service = strtolower($service);
        $methodName = strtolower($methodName);

        if (empty($methodList[$service]['method'][$methodName]))
            throw new Exception("Requested Vox SV log parser service does not exist");

        $this->url = $methodList[$service]['URL'] . $methodList[$service]['method'][$methodName];
    }

}

?>
