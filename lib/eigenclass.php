<?php

class Eigenclass extends Klass {

    function __construct($instance) {
        parent::__construct(get_class($instance));
    }

    function __include($modules, $instance = false) {
        if (!is_array($modules)) $modules = func_get_args();
        if (is_bool(end($modules))) $instance = array_pop($modules);
        $method = $instance ? 'parent::__include' : array($this->reference(), '__include');
        return call_user_func_array($method, $modules);
    }

    function callee($method, &$caller = null) {
        $ancestors = $this->ancestors();
        if ($caller && in_array($caller, $ancestors)) $ancestors = array_slice($ancestors, array_search($caller, $ancestors) + 1);
        foreach ($ancestors as $ancestor) {
            $methods = $ancestor->reflection()->instance_methods(false);
            if (isset($methods[$method])) return $methods[$method];
        }
    }

    function reference() {
        return parent::instance($this->_name);
    }

}