<?php

class Eigenclass extends Klass {

    function __construct($instance) {
        parent::__construct(get_class($instance));
    }

    function __include($modules, $instance = false) {
        if (!is_array($modules)) $modules = func_get_args();
        if (is_bool(end($modules))) $instance = array_pop($modules);
        $callee = $instance ? 'parent::__include' : array($this->reference(), '__include');
        return call_user_func_array($callee, $modules);
    }

    function reference() {
        return parent::instance($this->_name);
    }

}