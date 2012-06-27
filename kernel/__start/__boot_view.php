<?php

require_once($GLOBALS['documentroot'] . "/classes/View.class.php");

class _bootView extends View {

    public function display($viewarray) {
        if (!file_exists($this->smarty->template_dir . '/__start.tpl')) {
            $this->setModule(NULL);
        }

        if (!isset($this->module)) {
            $menu = '
            <ul id="Menu1" class="MM">
              <li><a href="?" accesskey="1" title="Home page">Home</a></li>
              <li><a href="#" title="Available services">Services</a>
                 <ul>';
            if ($GLOBALS['auth']->checkAuth('adsl', AUTH_READ)) {
                $menu = $menu . '
                    <li><a href="?module=adsl">ADSL</a></li>
            ';
            }
            $menu = $menu . '
                 </ul>
              </li>
            </ul>
         ';

            $this->smarty->assign('menu_bar', $menu);
            $this->smarty->assign('VERSION', $GLOBALS['config']->VERSION);
            $this->smarty->assign('xajax_javascript', $this->viewobject['xajax']);
            $this->smarty->assign('content', '
                <br/>Welcome to the Interprise Management System.<br/><br/>
                Select a service from the menu to start<p></p>
           ');
        }

        $this->smarty->force_compile = 1;
        return $this->smarty->fetch('__start.tpl');
    }

}
