<?php

namespace Phuby {
    class Object implements \ArrayAccess {

        const INSTANCE_VARIABLE_MISSING = 'instance_variable_missing';
        const KEYWORD_METHOD_FORMAT = '_%s_';
        const METHOD_MISSING = 'method_missing';

        protected $_eigenclass;
        protected $_instance_variables = array();

        static $keyword_methods = array('class', 'include', 'new');

        function __construct($object = null) {
            if ($object && is_a($object, __CLASS__)) $this->_instance_variables = &$object->_class_()->object()->_instance_variables;
            $this->bind_instance_variables_to_properties($object);
        }

        function _class_() {
            if (!isset($this->_eigenclass)) $this->_eigenclass = new Eigenclass($this);
            return $this->_eigenclass;
        }

        function __call($method, $arguments = array()) {
            return $this->__send_array__($method, $arguments);
        }

        function __call__($method, $arguments = array()) {
            return is_a($this, $method->class) ? $method->invokeArgs($this, $arguments) : $this->cast($method->class)->__call__($method, $arguments);
        }

        function &__get($property) {
            if ($this->instance_variable_defined($property)) {
                $value = &$this->instance_variable_get($property, true);
            } else {
                $value = $this->__send_array__(self::INSTANCE_VARIABLE_MISSING, array($property));
            }
            return $value;
        }

        /**
         * [QUIRK] $this->__id__() does not equal $this->cast('OtherClass')->__id__()
         *         This might cause unexpected behavior since cast is used when calling methods from included modules
        **/
        function __id__() {
            return spl_object_hash($this);
        }

        function __isset($property) {
            return $this->instance_variable_isset($property);
        }

        function __send__($method) {
            $arguments = func_get_args();
            return $this->__send_array__(array_shift($arguments), $arguments);
        }

        function __send_array__($method, $arguments = array()) {
            if (in_array($method, self::$keyword_methods)) $method = sprintf(KEYWORD_METHOD_FORMAT, $method);
            if (!$method_reflection = $this->method($method)) {
                $method_reflection = $this->method(self::METHOD_MISSING);
                array_unshift($arguments, $method);
            }
            return $this->__call__($method_reflection, $arguments);
        }

        function __set($property, $value) {
            return $this->instance_variable_defined($property) ?
                $this->instance_variable_set($property, $value) : $this->__send_array__(self::INSTANCE_VARIABLE_MISSING, array($property.'=', array($value)));
        }

        function __toString() {
            return $this->__send_array__('to_s');
        }

        function __unset($property) {
            $this->_instance_variables[$property] = null;
        }

        function cast($class) {
            return get_class($this) == $class ? $this : new $class($this);
        }

        function instance_variable_defined($variable) {
            return array_key_exists($variable, $this->_instance_variables);
        }

        function &instance_variable_get($variable, $defined = false) {
            if ($defined || $this->instance_variable_defined($variable)) {
                $value = &$this->_instance_variables[$variable];
            } else {
                $value = null;
            }
            return $value;
        }

        function instance_variable_isset($variable) {
            return isset($this->_instance_variables[$variable]);
        }

        function instance_variable_missing($variable, $arguments = array()) {
            array_unshift($arguments, $variable);
            return $this->__send_array__(self::METHOD_MISSING, $arguments);
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

        function method($method) {
            return $this->_class_()->method_table()->lookup($method);
        }

        function method_missing($method, $arguments = array()) {
            throw new \BadMethodCallException('Undefined method '.$this->_class_()->name().'::'.$method);
        }

        function offsetExists($offset) {
            return $this->__send_array__('offset_exists', func_get_args());
        }

        function offsetGet($offset) {
            return $this->__send_array__('offset_get', func_get_args());
        }

        function offsetSet($offset, $value) {
            return $this->__send_array__('offset_set', func_get_args());
        }

        function offsetUnset($offset) {
            return $this->__send_array__('offset_unset', func_get_args());
        }

        function respond_to($method) {
            return !!$this->method($method) || $this->__send_array__('respond_to_missing', func_get_args());
        }

        function respond_to_missing($method) {
            return false;
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