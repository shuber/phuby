<?php

namespace Klass {
    class InstanceMethods {

        function __new() {
            $instance = $this->allocate();
            if ($instance->respond_to('initialize')) $instance->send_array('initialize', func_get_args());
            return $instance;
        }

        function allocate() {
            $class = $this->name();
            return new $class;
        }

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

        static $instances = array();

        function __construct($class, $superclass = null, $create_if_undefined = true) {
            $base_class = get_parent_class(__CLASS__);
            if (!class_exists($class)) {
                if ($create_if_undefined) {
                    $namespaces = array_filter(preg_split('#\\\\|::#', $class));
                    $class_name = array_pop($namespaces);
                    $namespace = implode('\\', $namespaces);
                    if (!$superclass) $superclass = '\\'.$base_class;
                    $class_definition = 'namespace '.$namespace.' { class '.$class_name.' extends '.$superclass.' { } }';
                    eval($class_definition);
                } else {
                    throw new \InvalidArgumentException('Undefined class '.$class);
                }
            }
            $this->_name = $class;
            $this->_parent = get_parent_class($class);
        }

        function __include($modules) {
            if (!is_array($modules)) $modules = func_get_args();
            foreach (array_reverse($modules) as $module) {
                $module = static::instance($module);
                if (!in_array($module, $this->ancestors()) || is_subclass_of($this, __CLASS__) && $this->is_class()) {
                    if (in_array($this, $module->included_modules())) {
                        throw new \InvalidArgumentException('cyclic include detected');
                    } else if (!in_array($module, $this->_included_modules)) {
                        array_unshift($this->_included_modules, $module);
                        if ($module->respond_to('included')) $module->included($this);
                    }
                }
            }
            return $this;
        }

        function ancestors($unique = true) {
            $ancestors = array();
            foreach ($this->_included_modules as $module) $ancestors = array_merge($ancestors, $module->ancestors(false));
            if (is_subclass_of($this, __CLASS__)) {
                $ancestors = array_merge($ancestors, $this->reference()->ancestors(false));
            } else {
                $ancestors[] = $this;
                if ($this->superclass()) $ancestors = array_merge($ancestors, $this->superclass()->ancestors(false));
            }
            if ($unique) $ancestors = array_reverse(array_unique(array_reverse($ancestors), SORT_REGULAR));
            return $ancestors;
        }

        function extend($modules) {
            if (!is_array($modules)) $modules = func_get_args();
            $class = is_subclass_of($this, __CLASS__) ? $this->reference() : $this;
            $class->__class()->__include($modules, true);
            return $this;
        }

        function extended_modules($unique = true) {
            $modules = $this->__class()->included_modules(false);
            if (is_subclass_of($this, __CLASS__)) {
                $modules = array_merge($modules, $this->reference()->extended_modules(false));
            } else if ($this->superclass()) {
                $modules = array_merge($modules, $this->superclass()->extended_modules(false));
            }
            if ($unique) $modules = array_diff(array_reverse(array_unique(array_reverse($modules), SORT_REGULAR)), Klass::instance(__CLASS__)->included_modules(false));
            return $modules;
        }

        function included_modules($unique = true) {
            $modules = array();
            foreach ($this->_included_modules as $module) $modules = array_merge($modules, $module->included_modules(false), array($module));
            if (is_subclass_of($this, __CLASS__)) {
                $modules = array_merge($modules, $this->reference()->included_modules(false));
            } else if ($this->superclass()) {
                $modules = array_merge($modules, $this->superclass()->included_modules(false));
            }
            if ($unique) $modules = array_reverse(array_unique(array_reverse($modules), SORT_REGULAR));
            return $modules;
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
            if ($this->_parent) return self::instance($this->_parent);
        }

        protected function include_and_extend_default_modules() {
            if (is_subclass_of($this, get_parent_class(__CLASS__))) {
                $instance_methods = $this->_name.'\InstanceMethods';
                if (class_exists($instance_methods, false)) $this->__include($instance_methods);
                $class_methods = $this->_name.'\ClassMethods';
                if (class_exists($class_methods, false)) $this->extend($class_methods);
            }
        }

        static function instance($class) {
            if (is_a($class, __CLASS__)) $class = $class->name();
            if (!isset(self::$instances[$class])) {
                $instance = new self($class, null, false);
                self::$instances[$class] = $instance;
                $instance->include_and_extend_default_modules();
            }
            return self::$instances[$class];
        }

    }
}