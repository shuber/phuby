<?php

class Klass extends Object {

    protected $_name;
    protected $_parent;
    protected $_reflection;

    static $instances = array();

    function __construct($class, $create_if_undefined = true) {
        parent::__construct();
        if (!class_exists($class)) {
            if ($create_if_undefined) {
                $namespaces = array_filter(preg_split('#\\\\|::#', $class));
                $class_name = array_pop($namespaces);
                $namespace = implode('\\', $namespaces);
                $superclass = '\\'.get_parent_class(__CLASS__);
                $class_definition = 'namespace '.$namespace.' { class '.$class_name.' extends '.$superclass.' { } }';
                eval($class_definition);
            } else {
                throw new \InvalidArgumentException('Undefined class '.$class);
            }
        }
        $this->_name = $class;
        $this->_parent = get_parent_class($class);
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
        if (!isset(static::$instances[$class])) static::$instances[$class] = new static($class, false);
        return static::$instances[$class];
    }

}