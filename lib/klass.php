<?php

class Klass extends Object {

    protected $_name;
    protected $_parent;
    protected $_reflection;

    static $instances = array();

    function __construct($class) {
        if (class_exists($class)) {
            parent::__construct();
            $this->_name = $class;
            $this->_parent = get_parent_class($class);
        } else {
            throw new InvalidArgumentException('Undefined class '.$class);
        }
    }

    function name() {
        return $this->_name;
    }

    function &reflection() {
        if (!isset($this->_reflection)) $this->_reflection = &ClassReflector::instance($this->_name);
        return $this->_reflection;
    }

    function &superclass() {
        if ($this->_parent) {
            $superclass = &static::instance($this->_parent);
        } else {
            $superclass = null;
        }
        return $superclass;
    }

    static function &instance($class) {
        if (!isset(static::$instances[$class])) static::$instances[$class] = new static($class);
        return static::$instances[$class];
    }

}