<?php

require_once($GLOBALS['documentroot'] . "/libs/xajax/xajax_core/xajax.inc.php");
require_once($GLOBALS['documentroot'] . "/libs/Smarty/Smarty.class.php");

abstract class View {

    protected $smarty;
    protected $viewobject;
    protected $module;
    protected $default_template = '/templates/__start';

    function __construct($viewobject) {
        $this->init($viewobject);
    }

    /**
     *
     * @param string $viewobject XML document
     * @return none
     */
    protected function init($viewobject) {
        $this->smarty = new Smarty();

        $this->smarty->template_dir = $GLOBALS['documentroot'] . $this->default_template;
        $this->smarty->compile_dir = $GLOBALS['documentroot'] . '/templates_c';
        $this->smarty->cache_dir = $GLOBALS['documentroot'] . '/cache';
        $this->smarty->config_dir = $GLOBALS['documentroot'] . '/configs';
        if ($GLOBALS['config']->debug)
            $this->smarty->debugging = TRUE;

        //set current template directory for view
        $this->setModule($GLOBALS['module']);

        if (isset($viewobject)) {
            $this->viewobject = $viewobject;
        } else {
            return;
        }
    }

    abstract protected function display();

    /**
     *
     * @param string $module module templates to use
     */
    public function setModule($module) {
        if (isset($module) and file_exists($GLOBALS['documentroot'] . '/templates/' . $module . '/' . $GLOBALS['config']->view)) {
            $this->module = $module;
            $this->smarty->template_dir = $GLOBALS['documentroot'] . '/templates/' . $this->module . '/' . $GLOBALS['config']->view;
        } else {
            $this->module = NULL;
            $this->smarty->template_dir = $GLOBALS['documentroot'] . $this->default_template;
        }
    }

    public function asText() {
        return;
    }

}

class ViewObject extends SimpleXMLElement {
    
}

?>
