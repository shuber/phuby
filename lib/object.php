<?php

namespace Object {
    abstract class InstanceMethods {

        function inspect() {
            return '#<'.$this->__class()->name().':'.$this->object_id().'>';
        }

        function object_id() {
            return spl_object_hash($this);
        }

        function method_missing($method, $arguments) {
            throw new \BadMethodCallException('Undefined method '.$this->__class()->name().'::'.$method.'()');
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

        static $keyword_methods = array('class', 'include');

        function __call($method, $arguments) {
            return $this->send_array($method, $arguments);
        }

        function __class() {
            if (!isset($this->_eigenclass)) $this->_eigenclass = new Eigenclass($this);
            return $this->_eigenclass;
        }

        function __get($method) {
            return $this->respond_to($method) ? $this->$method() : null;
        }

        function __include($modules) {
            if (!is_array($modules)) $modules = func_get_args();
            $this->__class()->__include($modules, true);
            return $this;
        }

        function __toString() {
            return $this->to_s();
        }

        /**
         * [QUIRK] call_user_func_array() does not set $this
         * [QUIRK] ReflectionMethod requires the calling object to be an instance of ReflectionMethod#class
         * [QUIRK] calling methods statically e.g. Object\InstanceMethods::object_id() sets $this equal to the calling object
        **/
        function call_method($method, $arguments = array()) {
            $variables = array();
            $arguments = array_values($arguments);
            foreach ($arguments as $index => $argument) $variables[] = '$arguments['.$index.']';
            eval('$result = '.$method->class.'::'.$method->name.'('.implode(', ', $variables).');');
            return $result;
        }

        function callee($method, &$caller = null) {
            $ancestors = $this->__class()->ancestors();
            if ($caller && in_array($caller, $ancestors)) $ancestors = array_slice($ancestors, array_search($caller, $ancestors) + 1);
            foreach ($ancestors as $ancestor) {
                $methods = $ancestor->reflection()->instance_methods(false);
                if (isset($methods[$method])) return $methods[$method];
            }
        }

        function caller() {
            $backtrace = debug_backtrace();
            array_shift($backtrace);
            if ($backtrace[1]['function'] == 'super' && $backtrace[1]['class'] == __CLASS__) array_shift($backtrace);
            $caller_index = ($backtrace[2]['function'] == 'call_method' && $backtrace[2]['class'] == __CLASS__) ? 6 : 1;
            return $backtrace[$caller_index];
        }

        function method_defined($method) {
            return !!$this->callee($method);
        }

        function respond_to($method) {
            return $this->method_defined($method) || $this->respond_to_missing($method);
        }

        /**
         * [QUIRK] function __KEYWORD__() e.g. "function include($modules) { }" throws syntax errors
        **/
        function send_array($method, $arguments = array()) {
            if (in_array($method, static::$keyword_methods)) $method = "__$method";
            if (!$callee = $this->callee($method)) {
                $callee = $this->callee('method_missing');
                $arguments = array($method, $arguments);
            }
            return $this->call_method($callee, $arguments);
        }

        protected function super() {
            $arguments = func_get_args();
            $caller = $this->caller();
            $method = $caller['function'];
            if (!$callee = $this->callee($method, Klass::instance($caller['class']))) {
                $callee = $this->callee('method_missing');
                array_unshift($arguments, $method);
            }
            return $this->call_method($callee, $arguments);
        }

        protected function super_array($arguments = array()) {
            return $this->send_array('super', $arguments);
        }

    }

    Klass::instance('Object')->__include('Object\InstanceMethods');
}