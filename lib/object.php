<?php

namespace Object {
    abstract class InstanceMethods {

        function object_id() {
            return spl_object_hash($this);
        }

        function &method_missing($method, $arguments) {
            throw new \BadMethodCallException('Undefined method '.$this->__class()->name().'::'.$method.'()');
        }

        function respond_to_missing($method) {
            return false;
        }

        function &send($method) {
            $arguments = func_get_args();
            $method = array_shift($arguments);
            $result = &$this->send_array($method, $arguments);
            return $result;
        }

        function to_s() {
            return '<'.$this->__class()->name().'#'.$this->object_id().'>';
        }

    }
}

namespace {
    abstract class Object {

        protected $_class;

        function __construct() { }

        function &__call($method, $arguments) {
            $result = &$this->send_array($method, $arguments);
            return $result;
        }

        function &__class() {
            if (!isset($this->_class)) $this->_class = Klass::instance(get_class($this));
            return $this->_class;
        }

        function &__get($method) {
            if ($method == 'super') {
                $backtrace = debug_backtrace();
                $result = &$this->super_array($backtrace[1]['arguments']);
            } else if (method_exists($this, $method)) {
                $result = &$this->$method();
            } else {
                $result = null;
            }
            return $result;
        }

        function &__toString() {
            $result = &$this->to_s();
            return $result;
        }

        function &call_method($method, $arguments = array()) {
            $variables = array();
            $arguments = array_values($arguments);
            foreach ($arguments as $index => $argument) $variables[] = '$arguments['.$index.']';
            eval('$result = &'.$method->class.'::'.$method->name.'('.implode(', ', $variables).');');
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

        function &caller() {
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

        function send_array($method, $arguments = array()) {
            if (!$callee = $this->callee($method)) {
                $callee = $this->callee('method_missing');
                $arguments = array($method, $arguments);
            }
            $result = &$this->call_method($callee, $arguments);
            return $result;
        }

        protected function &super() {
            $arguments = func_get_args();
            $caller = $this->caller();
            $method = $caller['function'];
            if (!$callee = $this->callee($method, Klass::instance($caller['class']))) {
                $callee = $this->callee('method_missing');
                array_unshift($arguments, $method);
            }
            $result = &$this->call_method($callee, $arguments);
            return $result;
        }

        protected function &super_array($arguments = array()) {
            $result = &$this->send_array('super', $arguments);
            return $result;
        }

    }

    Klass::instance('Object')->__include('Object\InstanceMethods');
}