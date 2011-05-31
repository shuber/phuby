<?php

namespace Phuby {
    class ReflectionClass extends \ReflectionClass {

        protected $_ancestors;

        static $instances = array();

        function __construct($class) {
            parent::__construct($class);
            $this->_ancestors = array_values(class_parents($class));
        }

        function ancestors() {
            return ($superclass = $this->superclass()) ? array_merge(array($superclass), call_user_func(array($superclass, __METHOD__))) : array();
        }

        function superclass() {
            if (!empty($this->_ancestors)) return static::instance($this->_ancestors[0]);
        }

        static function instance($class) {
            if (!isset(static::$instances[$class])) static::$instances[$class] = new static($class);
            return static::$instances[$class];
        }

    }
}