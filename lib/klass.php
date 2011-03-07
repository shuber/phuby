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

        static $auto_extends = array('_ClassMethods', '\ClassMethods');
        static $auto_includes = array('_InstanceMethods', '\InstanceMethods');
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
            if (is_subclass_of($this, __CLASS__) && $this->is_class()) {
                $is_class = true;
                $callback = 'extended';
                $object = $this->_object;
            } else {
                $is_class = false;
                $callback = 'included';
                $object = $this;
            }
            foreach (array_reverse($modules) as $module) {
                $module = self::instance($module);
                if (!in_array($module, $this->ancestors()) || $is_class) {
                    if (in_array($this, $module->included_modules())) {
                        throw new \InvalidArgumentException('cyclic include detected');
                    } else if (!in_array($module, $this->_included_modules)) {
                        array_unshift($this->_included_modules, $module);
                        if ($module->respond_to($callback)) $module->$callback($object);
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
            if ($unique) $ancestors = self::unique_sorted_modules($ancestors);
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
            if ($unique) $modules = array_diff(self::unique_sorted_modules($modules), self::instance(get_class($this))->included_modules(false));
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
            if ($unique) $modules = self::unique_sorted_modules($modules);
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

        protected function include_extend_and_inherit_defaults() {
            if (is_subclass_of($this, get_parent_class(__CLASS__))) {
                foreach (self::$auto_extends as $suffix) {
                    $class_methods = $this->_name.$suffix;
                    if (class_exists($class_methods, false)) $this->extend($class_methods);
                }
                foreach (self::$auto_includes as $suffix) {
                    $instance_methods = $this->_name.$suffix;
                    if (class_exists($instance_methods, false)) $this->__include($instance_methods);
                }
            }
            $superclass = $this->superclass();
            if ($superclass && $superclass->respond_to('inherited')) $superclass->inherited($this);
            if ($this->respond_to('initialize')) $this->initialize();
        }

        static function instance($class) {
            if (is_a($class, __CLASS__)) $class = $class->name();
            if (!isset(self::$instances[$class])) {
                $instance = new self($class, null, false);
                self::$instances[$class] = $instance;
                $instance->include_extend_and_inherit_defaults();
            }
            return self::$instances[$class];
        }

        protected static function unique_sorted_modules($modules) {
            return array_reverse(array_unique(array_reverse($modules), SORT_REGULAR));
        }

    }
}