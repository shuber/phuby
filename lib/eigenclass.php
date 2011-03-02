<?php

namespace Eigenclass {
    class InstanceMethods {

        function method_missing($method, $arguments) {
            return $this->reference()->send_array($method, $arguments);
        }

    }
}

namespace {
    class Eigenclass extends Klass {

        protected $_object;

        function __construct(&$object) {
            $this->_object = $object;
            parent::__construct(get_class($object), null, false);
        }

        function __include($modules, $instance = false) {
            if (!is_array($modules)) $modules = func_get_args();
            if (is_bool(end($modules))) $instance = array_pop($modules);
            $method = $instance ? 'parent::__include' : array($this->reference(), '__include');
            return call_user_func_array($method, $modules);
        }

        function ancestors($unique = true) {
            if ($this->is_class()) {
                $ancestors = array_merge($this->_object->extended_modules(false), array($this->_object));
                if ($this->_object->superclass()) {
                    $ancestors = array_merge($ancestors, $this->_object->superclass()->__class()->ancestors(false));
                } else {
                    $ancestors = array_merge($ancestors, Klass::instance(__CLASS__)->ancestors(false));
                }
                if ($unique) $ancestors = array_reverse(array_unique(array_reverse($ancestors), SORT_REGULAR));
            } else {
                $ancestors = parent::ancestors($unique);
            }
            return $ancestors;
        }

        function callee($method, &$caller = null) {
            $ancestors = $this->ancestors();
            if ($this->is_class()) $extended_modules = $this->_object->extended_modules(false);
            if ($caller && in_array($caller, $ancestors)) $ancestors = array_slice($ancestors, array_search($caller, $ancestors) + 1);
            foreach ($ancestors as $ancestor) {
                if (!$this->is_class() || in_array($ancestor, $extended_modules)) {
                    $methods = $ancestor->reflection()->instance_methods(false);
                    if (isset($methods[$method])) return $methods[$method];
                }
                if ($this->is_class() && ($this->_object == $ancestor || in_array($ancestor->name(), class_parents($this->_object->name())))) {
                    $methods = $ancestor->reflection()->class_methods(false);
                    if (isset($methods[$method])) return $methods[$method];
                }
            }
        }

        function is_class() {
            return get_class($this->_object) == get_parent_class(__CLASS__);
        }

        function object() {
            return $this->_object;
        }

        function reference() {
            return parent::instance($this->_name);
        }

    }
}