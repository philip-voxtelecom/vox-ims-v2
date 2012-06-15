<?php

class Config {

    protected $options = array();

    public function __set($name, $value) {
        // TODO : maybe make this a write once read many type config
        $this->options[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
                'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    public function __isset($name) {
        return isset($this->options[$name]);
    }

}

?>
