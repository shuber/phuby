<?php

namespace Klass {
    class InstanceMethods {

        function to_s() {
            return $this->name();
        }

    }
}

namespace {
    class Klass extends Object {

        protected $_included_modules = array();
        protected $_name;
        protected $_parent;
        protected $_reflection;
        protected $_superclass;

        static $instances = array();

        function __construct($class, $superclass = null, $create_if_undefined = true) {
            parent::__construct();
            if (!class_exists($class)) {
                if ($create_if_undefined) {
                    $namespaces = array_filter(preg_split('#\\\\|::#', $class));
                    $class_name = array_pop($namespaces);
                    $namespace = implode('\\', $namespaces);
                    if (!$superclass) $superclass = '\\'.get_parent_class(__CLASS__);
                    $class_definition = 'namespace '.$namespace.' { class '.$class_name.' extends '.$superclass.' { } }';
                    eval($class_definition);
                } else {
                    throw new \InvalidArgumentException('Undefined class '.$class);
                }
            }
            $this->_name = $class;
            if ($superclass = get_parent_class($class)) $this->_superclass = static::instance($superclass);
        }

        function __include($modules) {
            if (!is_array($modules)) $modules = func_get_args();
            foreach (array_reverse($modules) as $module) {
                $module = static::instance($module);
                if (!in_array($module, $this->ancestors())) {
                    if (in_array($this, $module->included_modules())) {
                        throw new \InvalidArgumentException('cyclic include detected');
                    } else {
                        array_unshift($this->_included_modules, $module);
                        // if ($module->respond_to('included')) $module->included($this);
                    }
                }
            }
            return $this;
        }

        function ancestors() {
            $ancestors = array();
            foreach ($this->_included_modules as $module) $ancestors = array_merge($ancestors, $module->ancestors());
            $ancestors[] = $this;
            if ($this->superclass()) $ancestors = array_merge($ancestors, $this->superclass()->ancestors());
            return array_reverse(array_unique(array_reverse($ancestors), SORT_REGULAR));
        }

        function included_modules() {
            $modules = array();
            foreach ($this->_included_modules as $module) $modules = array_merge($module->included_modules(), array($module), $modules);
            if ($this->superclass()) $modules = array_merge($modules, $this->superclass()->included_modules());
            return array_reverse(array_unique(array_reverse($modules), SORT_REGULAR));
        }

        function instance_methods($include_super = true) {
            $methods = array();
            foreach ($this->ancestors() as $ancestor) {
                $methods = array_merge($methods, array_keys($ancestor->reflection()->instance_methods(false)));
                if ($ancestor == $this && !$include_super) break;
            }
            return $methods;
        }

        function name() {
            return $this->_name;
        }

        function reflection() {
            if (!isset($this->_reflection)) $this->_reflection = ClassReflector::instance($this->_name);
            return $this->_reflection;
        }

        function superclass() {
            return $this->_superclass;
        }

        static function instance($class) {
            if (!isset(static::$instances[$class])) static::$instances[$class] = new static($class, null, false);
            return static::$instances[$class];
        }

    }

    Klass::instance('Klass')->__include('Klass\InstanceMethods');
}