<?php

include '_common_view.php';
require_once('_meta_db.php');

function Error($errormsg) {
    $view = new Error($errormsg);
    return $view->display();
}

function humanify() {
    $arg_list = func_get_args();
    $size = array_shift($arg_list);
    if ($size < 0) {
        $neg = '-';
        $size = $size * -1;
    } else {
        $neg = '';
    }
    if ($size > 999999999) {
        return sprintf('%s%.2fGB', $neg, $size / 1000000000);
    } elseif ($size > 1000000) {
        return sprintf('%s%.2fMB', $neg, $size / 1000000);
    } elseif ($size > 1000) {
        return sprintf('%s%.2fKB', $neg, $size / 1000);
    } else {
        return sprintf('%s%dB', $neg, $size);
    }
}

function humanify_s() {
    $arg_list = func_get_args();
    $secs = array_shift($arg_list);
    if ($secs > 86400) {
        $days = $secs / 86400;
        $secs = $secs % 86400;
    } else {
        $days = 0;
    }
    if ($secs > 3600) {
        $hours = $secs / 3600;
        $secs = $secs % 3600;
    } else {
        $hours = 0;
    }
    if ($secs > 60) {
        $mins = $secs / 60;
        $secs = $secs % 60;
    } else {
        $mins = 0;
    }
    return sprintf("%dd %02dh %02dm %02ds", $days, $hours, $mins, $secs);
}

function audit() {
    $arg_list = func_get_args();
    $dbh = DatabaseConnection::get()->handle();
    $authuser = array_shift($arg_list);
    $identifier = array_shift($arg_list);
    $facility = array_shift($arg_list);
    $action = array_shift($arg_list);
    $parameter = array_shift($arg_list);
    $oldValue = array_shift($arg_list);
    $newValue = addslashes(array_shift($arg_list));
    $query = "INSERT INTO audit(authuser,facility,action,parameter,identifier,oldvalue,newvalue,auditdate) VALUES ('$authuser','$facility','$action','$parameter','$identifier','$oldValue','$newValue',current_timestamp)";
    //error_log($query);
    pg_query($dbh, "begin");
    pg_query($dbh, $query);
    pg_query($dbh, "commit");
}

function logout() {
    header('WWW-Authenticate: Basic realm="Management Authentication"');
    header('HTTP/1.0 401 Unauthorized');
    echo " <html> <body onload='document.execCommand(\"ClearAuthenticationCache\");'> You have been successfully logged off.Please click on the logon button if you would like to logon again.<br/> <input type='button' onclick='window.location = \"http://adsl-stats.interprise.co.za\"' value='Logon'/> </body> </html>";
    exit;
}

function encrypt($str, $key) {
    $result = '';
    for ($i = 0; $i < strlen($str); $i++) {
        $char = substr($str, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) + ord($keychar));
        $result.=$char;
    }
    return base64_encode($result);
}

function decrypt($str, $key) {
    $str = base64_decode($str);
    $result = '';
    for ($i = 0; $i < strlen($str); $i++) {
        $char = substr($str, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) - ord($keychar));
        $result.=$char;
    }
    return $result;
}

function append_simplexml(&$simplexml_to, &$simplexml_from) {
    foreach ($simplexml_from->children() as $simplexml_child) {
        $simplexml_temp = $simplexml_to->addChild($simplexml_child->getName(), htmlspecialchars((string) $simplexml_child));
        foreach ($simplexml_child->attributes() as $attr_key => $attr_value) {
            $simplexml_temp->addAttribute($attr_key, $attr_value);
        }

        append_simplexml($simplexml_temp, $simplexml_child);
    }
}

function load_provider_model($model) {
    if (file_exists($GLOBALS['documentroot'] . '/Models/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->provider . '/_' . $model . '_model.php')) {
        include($GLOBALS['documentroot'] . '/Models/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->provider . '/_' . $model . '_model.php');
    }
}

function load_view($view) {
    if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->view . '/_' . $view . '_view.php')) {
        include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] . '/' . $GLOBALS['config']->view . '/_' . $view . '_view.php');
    }
}

function validateParams($mask, $parameters) {

    foreach ($mask as $name => $option) {
        if ($option['mandatory'] and empty($parameters[$name]))
            throw new InvalidArgumentException($name);
    }
}

/*
  $arr = array(
  'pet_name'=>"fido",
  'favorite_food'=>"cat poop",
  'unique_id'=>3848908043
  );
  $param_string = encrypt(serialize($arr));
 *
  $param_string = $_GET["params"];
  $params = unserialize(decrypt($param_string));
 */
?>
