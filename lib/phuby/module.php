<?php

namespace Phuby {
    class Object {}
    abstract class Module extends Object {

        const ANCESTRY_CHANGED = 1;

        static $instances = array();

        protected $_ancestors;
        protected $_dependents = array();
        protected $_included_modules = array();
        protected $_name;
        protected $_parent;
        protected $_superclass;

        function __construct($name) {
            $this->_name = $name;
            $this->_parent = get_parent_class($name);
            if ($this->_parent) {
                $this->_superclass = self::instance($this->_parent);
                $this->_superclass->_inherited($this);
            }
        }

        function ancestors() {
            if (!isset($this->_ancestors)) {
                $this->_ancestors = array();
            }
            return $this->_ancestors;
        }

        function clear_ancestors_cache() {
            unset($this->_ancestors);
            foreach ($this->_dependents as $dependent) $dependent->${__METHOD__}();
        }

        function superclass() {
            return $this->_superclass;
        }

        protected function _inherited($module) {
            // 
        }

    }
}