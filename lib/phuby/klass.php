<?php

namespace Phuby {
    class Klass extends Object {

        protected $_ancestors;
        protected $_dependants = array();
        protected $_method_table;
        protected $_name;
        protected $_superclass;

        static protected $_instances = array();

        function __construct($name) {
            if (class_exists($name)) {
                $this->_name = $name;
                $this->_method_table = new Core\MethodTable($this);
                if ($parent = get_parent_class($name)) $this->_superclass = self::instance($parent);
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
            foreach ($this->_dependants as $dependant) $dependant->clear_ancestors_cache();
        }

        function method_table() {
            return $this->_method_table;
        }

        function name() {
            return $this->_name;
        }

        function reflection() {
            return Core\ReflectionClass::instance($this->_name);
        }

        function superclass() {
            return $this->_superclass;
        }

        protected function add_dependant($object) {
            $this->_dependants[] = $object;
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

        protected function build_ancestors_cache() {
            $ancestors = array($this);
            if ($superclass = $this->superclass()) $ancestors = array_merge($ancestors, $superclass->ancestors());
            return $ancestors;
        }

        protected function initialize_instance() {
            if ($superclass = $this->superclass()) {
                $superclass->add_dependant($this);
                if ($superclass->respond_to('inherited')) $superclass->__send_array__('inherited', array($this));
            }
            if ($this->respond_to('initialize')) $this->__send_array__('initialize');
        }

        static function instance($name) {
            if (!isset(self::$_instances[$name])) {
                $instance = new self($name);
                self::$_instances[$name] = $instance;
                $instance->initialize_instance();
            }
            return self::$_instances[$name];
        }

    }
}