<?php

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

class Error extends View {

    public function display($viewarray = NULL) {
        if (!isset($this->viewobject)) {
            $errormsg = "An error was caught";
        } else {
            $errormsg = $this->viewobject;
        }
        $this->xajax->assign("right_bar_content", "innerHTML", "<pre>$errormsg</pre>");
        return $this->xajax;
    }

}
