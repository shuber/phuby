<?php

namespace Phuby\Core {
    class MethodTable {

        protected $klass;
        protected $methods;

        function __construct($klass) {
            $this->klass = $klass;
        }

        function lookup($method, $caller = null) {
            $methods = $this->methods();
            return isset($methods[$method][$caller]) ? $methods[$method][$caller] : false;
        }

        function methods() {
            if (!isset($this->methods)) $this->methods = $this->_methods();
            return $this->methods;
        }

        function refresh() {
            unset($this->methods);
        }

        protected function _methods() {
            $methods = array();
            $last_ancestor = null;
            foreach ($this->klass->ancestors() as $ancestor) {
                foreach ($ancestor->reflection()->getInstanceMethods() as $name => $method) {
                    if (isset($methods[$name])) {
                        $current_ancestor = $last_ancestor;
                    } else {
                        $methods[$name] = array();
                        $current_ancestor = null;
                    }
                    $methods[$name][$current_ancestor] = $method;
                }
                $last_ancestor = $ancestor->name();
            }
            return $methods;
        }

    }
}