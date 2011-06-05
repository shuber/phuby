<?php

namespace Phuby {
    class Klass extends Object {

        protected $_ancestors;
        protected $_name;
        protected $_parent;

        static protected $_instances = array();

        function __construct($name) {
            if (class_exists($name)) {
                $this->_name = $name;
                $this->_parent = get_parent_class($name);
                parent::__construct();
            } else {
                throw new \InvalidArgumentException("Undefined class $name");
            }
        }

        function ancestors() {
            if (!isset($this->_ancestors)) {
                $this->_ancestors = array($this);
                if ($superclass = $this->superclass()) $this->_ancestors = array_merge($this->_ancestors, $superclass->ancestors());
            }
            return $this->_ancestors;
        }

        function name() {
            return $this->_name;
        }

        function superclass() {
            if ($this->_parent) return self::instance($this->_parent);
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
            if (!isset(self::$_instances[$name])) self::$_instances[$name] = new self($name);
            return self::$_instances[$name];
        }

    }
}