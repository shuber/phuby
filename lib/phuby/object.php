<?php

namespace Phuby {
    class Object {

        const KEYWORD_METHOD_FORMAT = '_%s_';

        protected $_eigenclass;
        protected $_instance_variables = array();

        static $keyword_methods = array('class', 'include', 'new');

        function __construct($object = null) {
            if ($object && is_a($object, __CLASS__)) $this->_instance_variables = &$object->_instance_variables;
            $this->bind_instance_variables_to_properties($object);
        }

        function _class_() {
            if (!isset($this->_eigenclass)) $this->_eigenclass = new Eigenclass($this);
            return $this->_eigenclass;
        }

        function __call($method, $arguments) {
            return $this->__send__array($method, $arguments);
        }

        function __get($property) {
            return $this->instance_variable_defined($property) ?
                $this->instance_variable_get($property, true) : $this->__send__array('instance_variable_missing', array($property));
        }

        function __id__() {
            return spl_object_hash($this);
        }

        function __isset($property) {
            return $this->instance_variable_isset($property);
        }

        function __send__($method) {
            return $this->__send__array(array_shift($arguments), $arguments);
        }

        function __send__array($method, $arguments = array()) {
            if (in_array($method, static::$keyword_methods)) $method = sprintf(KEYWORD_METHOD_FORMAT, $method);
            throw new \BadMethodCallException($method);
        }

        function __set($property, $value) {
            return $this->instance_variable_defined($property) ?
                $this->instance_variable_set($property, $value) : $this->__send__array('instance_variable_missing', array($property.'=', array($value)));
        }

        function __toString() {
            return $this->__send__array('to_s');
        }

        function __unset($property) {
            unset($this->_instance_variables[$property]);
        }

        function cast($class) {
            return new $class($this);
        }

        function instance_variable_defined($variable) {
            return array_key_exists($variable, $this->_instance_variables);
        }

        function instance_variable_get($variable, $defined = false) {
            if ($defined || $this->instance_variable_defined($variable)) return $this->_instance_variables[$variable];
        }

        function instance_variable_isset($variable) {
            return isset($this->_instance_variables[$variable]);
        }

        function instance_variable_set($variable, $value) {
            return $this->_instance_variables[$variable] = $value;
        }

        function instance_variable_unset($variable) {
            unset($this->$variable);
        }

        function instance_variables() {
            return $this->_instance_variables;
        }

        protected function bind_instance_variables_to_properties($object) {
            foreach (get_object_vars($this) as $property => $value) {
                if ($object && $this->instance_variable_defined($property)) $this->$property = $this->instance_variable_get($property, true);
                if ($property != '_instance_variables') {
                    $this->instance_variable_set($property, $this->$property);
                    unset($this->$property);
                }
            }
        }

    }
}