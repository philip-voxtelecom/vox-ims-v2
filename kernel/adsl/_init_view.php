<?php

require_once($GLOBALS['documentroot'] . '/classes/View.class.php');

if (file_exists($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] .'/'. $GLOBALS['config']->view . '/_init_view.php')) {
    include($GLOBALS['documentroot'] . '/Views/' . $GLOBALS['module'] .'/'. $GLOBALS['config']->view . '/_init_view.php');
}

function initPageDisplay() {
    $view = initPageViewFactory::Create(null);
    return $view->display();
}

function PageInit() {
    return initPageDisplay();
}

class initPageView extends View {
    public function display($viewarray = NULL) {
        return;
    }
}

class initPageViewFactory {

    public static function Create($viewobject) {
        $required_class = "initPageView_" . $GLOBALS['config']->view;
        if (class_exists($required_class)) {
            return new $required_class($viewobject);
        } else {
            return new initView($viewobject);
        }
    }

}
?>
