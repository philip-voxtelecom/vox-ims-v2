<?php
class restclient {

    public $url;
    public $username;
    public $password;
    public $exchange = 'T2';

    function callMethod($param, $method = 'GET') {

        if ($this->url == NULL)
            throw new Exception("No URL method specified");
        if ($method != 'GET' and $param == NULL)
            throw new Exception("Null parameter send through");

        $getParams = '?';
        $content = '';
        if (strtolower($method) != 'get') {
            $content = json_encode($param);
            if ($content == FALSE)
                throw new Exception("Could not convert parameter to json object");
        }

        if (strtolower($method) == 'get' and $param != NULL) {
            foreach ($param as $key => $value) {
                if ($getParams != '?')
                    $getParams .= "&";
                $getParams .= urlencode($key) . '=' . urlencode($value);
            }
            $this->url .= $getParams;
        }

        try {
            $username = $this->username;
            $password = $this->password;
            $exchange = $this->exchange;
            $header = 'Content-Type: application/json' . "\r\n";
            $header .= "Authorization: Basic " . base64_encode("$username:$password");

            $http_context = stream_context_create(array(
                'http' => array(
                    'method' => $method,
                    'header' => $header,
                    'content' => $content,
                ),
                    ));
            ini_set('default_socket_timeout', 300);
            $result_json = file_get_contents($this->url, null, $http_context);
            $result = json_decode($result_json, true);
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}
?>
