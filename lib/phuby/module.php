<?php

namespace Phuby {
    class Module extends Object {

        protected $_ancestors;
        protected $_included_modules = array();
        protected $_name;

        static protected $_instances = array();

        function __construct($name) {
            $this->_name = $name;
            parent::__construct();
        }

        function ancestors() {
            if (!isset($this->_ancestors)) {
                $this->_ancestors = array();
            }
            return $this->_ancestors;
        }

        function name() {
            return $this->_name;
        }

        protected function bind_instance_variables_to_properties($object) {
            $class = $this->_name;
            foreach (get_class_vars($class) as $property => $value) {
                if (isset($class::${$property})) {
                    if ($object && $this->instance_variable_defined($property)) $class::${$property} = $this->instance_variable_get($property, true);
                    $this->_instance_variables[$property] = &$class::${$property};
                }
            }
            parent::bind_instance_variables_to_properties($object);
        }

        static function instance($name) {
            if (!isset(self::$_instances[$name])) self::$_instances[$name] = new static($name);
            return self::$_instances[$name];
        }

    }
}