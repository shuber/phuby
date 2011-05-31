<?php

namespace Phuby {
    class Klass extends Module {

        protected $_parent;

        function __construct($name) {
            $this->_parent = get_parent_class($name);
            parent::__construct($name);
        }

        function superclass() {
            if ($this->_parent) {
                $class = __CLASS__;
                return $class::instance($this->_parent);
            }
        }

    }
}