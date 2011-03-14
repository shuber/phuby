<?php

namespace Object {
    abstract class InstanceMethods {

        function inspect() {
            return '#<'.$this->__class()->name().':'.$this->object_id().'>';
        }

        function instance_variable_missing($variable) {
            return $this->$variable();
        }

        function object_id() {
            return spl_object_hash($this);
        }

        function method_missing($method, $arguments) {
            if ($this->is_a('Klass')) {
                $class = $this;
                $scope = '::';
            } else {
                $class = $this->__class();
                $scope = '->';
            }
            throw new \BadMethodCallException('Undefined method '.$class->name().$scope.$method.'()');
        }

        function respond_to_missing($method) {
            return false;
        }

        function send($method) {
            $arguments = func_get_args();
            $method = array_shift($arguments);
            return $this->send_array($method, $arguments);
        }

        function to_s() {
            return $this->inspect();
        }

    }
}

namespace {
    abstract class Object {

        protected $_eigenclass;
        protected $_instance_variables = array();

        static $keyword_methods = array('class', 'include', 'new');

        function __construct() {
            foreach (array_keys(get_object_vars($this)) as $variable) $this->_instance_variables[$variable] = &$this->$variable;
        }

        function __call($method, $arguments) {
            return $this->send_array($method, $arguments);
        }

        function __class() {
            if (!isset($this->_eigenclass)) $this->_eigenclass = new Eigenclass($this);
            return $this->_eigenclass;
        }

        function __get($variable) {
            if ($this->instance_variable_defined($variable)) {
                $response = $this->instance_variable_get($variable);
            } else {
                $response = $this->instance_variable_missing($variable);
            }
            return $response;
        }

        function __include($modules) {
            if (!is_array($modules)) $modules = func_get_args();
            $this->__class()->__include($modules, true);
            return $this;
        }

        function __invoke() {
            return $this->send_array('call', func_get_args());
        }

        function __set($variable, $value) {
            if ($this->instance_variable_defined($variable)) {
                $response = $this->instance_variable_set($variable, $value);
            } else {
                $response = $this->method_missing("$variable=", array($value));
            }
            return $response;
        }

        function __toString() {
            return $this->to_s();
        }

        /**
         * [QUIRK] call_user_func_array() does not set $this
         * [QUIRK] ReflectionMethod requires the calling object to be an instance of ReflectionMethod#class
         * [QUIRK] calling methods statically e.g. Object\InstanceMethods::object_id() sets $this equal to the calling object
         *         but the method must be public
        **/
        function call_method($method, $arguments = array()) {
            $variables = array();
            $arguments = array_values($arguments);
            foreach ($arguments as $index => $argument) $variables[] = '$arguments['.$index.']';
            eval('$response = '.$method->class.'::'.$method->name.'('.implode(', ', $variables).');');
            return $response;
        }

        function extend($modules) {
            if (!is_array($modules)) $modules = func_get_args();
            $this->__class()->__class()->__include($modules, true);
            return $this;
        }

        function instance_variable_defined($variable) {
            return in_array($variable, array_keys($this->_instance_variables)) || in_array($variable, array_keys($this->instance_variables()));
        }

        function instance_variable_get($variable) {
            if ($this->instance_variable_defined($variable)) return $this->_instance_variables[$variable];
        }

        function instance_variable_isset($variable) {
            if ($this->instance_variable_defined($variable)) {
                return isset($this->_instance_variables[$variable]);
            } else {
                return false;
            }
        }

        function instance_variable_set($variable, $value) {
            $this->_instance_variables[$variable] = $value;
            return $value;
        }

        // TODO: unset() will remove key from _instance_variables, calling instance_variables() may reload that key
        //       - set equal to null instead?
        //       - store an array of "unset" instance variables? (don't lookup in ancestors)
        function instance_variable_unset($variable) {
            unset($this->_instance_variables[$variable]);
        }

        function instance_variables() { // TODO: merge with ancestors
            return $this->_instance_variables;
        }

        function is_a($module, $include_super = true) {
            $module = Klass::instance($module);
            $class = $this->__class();
            return $include_super ? in_array($module, $class->ancestors()) : $module == $class->reference();
        }

        function method_defined($method) {
            return !!$this->__class()->callee($method);
        }

        function respond_to($method) {
            if (in_array($method, static::$keyword_methods)) $method = "__$method";
            return $this->method_defined($method) || ($this->method_defined('respond_to_missing') && $this->respond_to_missing($method));
        }

        /**
         * [QUIRK] function __KEYWORD__() e.g. "function include($modules) { }" throws syntax errors
        **/
        function send_array($method, $arguments = array()) {
            if (in_array($method, static::$keyword_methods)) $method = "__$method";
            if (!$callee = $this->__class()->callee($method)) {
                $callee = $this->__class()->callee('method_missing');
                $arguments = array($method, $arguments);
            }
            return $this->call_method($callee, $arguments);
        }

        protected function caller() {
            $backtrace = debug_backtrace();
            array_shift($backtrace);
            if ($backtrace[1]['function'] == 'super' && $backtrace[1]['class'] == __CLASS__) array_shift($backtrace);
            $caller_index = ($backtrace[2]['function'] == 'call_method' && $backtrace[2]['class'] == __CLASS__) ? 6 : 1;
            return $backtrace[$caller_index];
        }

        protected function super() {
            $arguments = func_get_args();
            $caller = $this->caller();
            $method = $caller['function'];
            if (!$callee = $this->__class()->callee($method, Klass::instance($caller['class']))) {
                $callee = $this->__class()->callee('method_missing');
                array_unshift($arguments, $method);
            }
            return $this->call_method($callee, $arguments);
        }

        protected function super_array($arguments = array()) {
            return $this->send_array('super', $arguments);
        }

        static function __callStatic($method, $arguments) {
            return Klass::instance(get_called_class())->send_array($method, $arguments);
        }

    }
}