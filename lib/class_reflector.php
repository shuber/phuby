<?php

class ClassReflector {

    protected $_ancestors;
    protected $_methods;
    protected $_name;
    protected $_reflection;
    protected $_variables;

    static $instances = array();

    function __construct($class) {
        $this->_name = $class;
        $this->_ancestors = class_parents($class);
    }

    function __get($method) {
        return $this->$method();
    }

    function ancestors() {
        return array_map(function($ancestor) { return ClassReflector::instance($ancestor); }, $this->_ancestors);
    }

    function class_methods($include_super = true) {
        return array_filter($this->methods($include_super), function($method) { return $method->isStatic(); });
    }

    function class_variables($include_super = true) {
        return array_filter($this->variables($include_super), function($variable) { return $variable->isStatic(); });
    }

    function instance_methods($include_super = true) {
        return array_filter($this->methods($include_super), function($method) { return !$method->isStatic(); });
    }

    function instance_variables($include_super = true) {
        return array_filter($this->variables($include_super), function($variable) { return !$variable->isStatic(); });
    }

    function methods($include_super = true) {
        if (!isset($this->_methods)) {
            $this->_methods = array();
            foreach ($this->reflection->getMethods() as $method) $this->_methods[$method->name] = $method;
        }
        $methods = $this->_methods;
        if (!$include_super) {
            foreach ($methods as $name => $method) {
                if ($method->getDeclaringClass()->name != $this->name) unset($methods[$name]);
            }
        }
        return $methods;
    }

    function name() {
        return $this->_name;
    }

    function reflection() {
        if (!isset($this->_reflection)) $this->_reflection = new ReflectionClass($this->_name);
        return $this->_reflection;
    }

    function superclass() {
        if (!empty($this->_ancestors)) return static::instance(reset($this->_ancestors));
    }

    function variables($include_super = true) {
        if (!isset($this->_variables)) {
            $this->_variables = array();
            foreach ($this->reflection->getProperties() as $variable) $this->_variables[$variable->name] = $variable;
        }
        $variables = $this->_variables;
        if (!$include_super) {
            foreach ($variables as $name => $variable) {
                if ($variable->getDeclaringClass()->name != $this->name) unset($variables[$name]);
            }
        }
        return $variables;
    }

    static function instance($class) {
        if (!isset(static::$instances[$class])) static::$instances[$class] = new static($class);
        return static::$instances[$class];
    }

}