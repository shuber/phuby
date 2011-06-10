<?php

namespace Phuby {
    class Klass extends Object {

        protected $_ancestors;
        protected $_dependants = array();
        protected $_method_table;
        protected $_name;
        protected $_parent;

        static protected $_instances = array();

        function __construct($name) {
            if (class_exists($name)) {
                $this->_method_table = new Core\MethodTable($this);
                $this->_name = $name;
                $this->_parent = get_parent_class($name);
                parent::__construct();
            } else {
                throw new \InvalidArgumentException("Undefined class $name");
            }
        }

        function ancestors() {
            if (!isset($this->_ancestors)) $this->_ancestors = $this->build_ancestors_cache();
            return $this->_ancestors;
        }

        function clear_ancestors_cache() {
            $this->_ancestors = null;
            $this->_method_table->clear_methods_cache();
            foreach ($this->_dependants as $dependant) call_user_func(array($dependant, __METHOD__));
        }

        function name() {
            return $this->_name;
        }

        function reflection() {
            return Core\ReflectionClass::instance($this->_name);
        }

        function superclass() {
            if ($this->_parent) return self::instance($this->_parent);
        }

        protected function build_ancestors_cache() {
            $ancestors = array($this);
            if ($superclass = $this->superclass()) $ancestors = array_merge($ancestors, $superclass->ancestors());
            return $ancestors;
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