<?php

namespace Phuby {
    class ReflectionClass extends \ReflectionClass {

        protected $_parent;

        static protected $_instances = array();

        function __construct($class) {
            parent::__construct($class);
            $this->_parent = get_parent_class($class);
        }

        function getParentClass() {
            if ($this->_parent) return static::instance($this->_parent);
        }

        static function instance($class) {
            if (!isset(self::$_instances[$class])) self::$_instances[$class] = new static($class);
            return self::$_instances[$class];
        }

    }
}