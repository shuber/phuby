<?php

namespace Phuby;

class BasicObject {
    private static $constants = [];

    static function __callStatic($method_name, $args) {
        return self::const_get(get_called_class())->splat($method_name, $args);
    }

    static function const_get($name) {
        if (!isset(self::$constants[$name]))
            self::$constants[$name] = new Module($name);
        return self::$constants[$name];
    }

    protected $__class__;
    protected $__id__;
    protected $__singleton_class__;

    function __construct() {
        $args = func_get_args();
        if (!empty($args))
            call_user_func_array([$this, 'initialize'], $args);
    }

    function __call($method_name, $args) {
        return $this->__splat__($method_name, $args);
    }

    function __class() {
        if (!isset($this->__class__))
            $this->__class__ = self::const_get(get_class($this));
        return $this->__class__;
    }

    function __get($method_name) {
        return $this->__send__($method_name);
    }

    function __id__() {
        if (!isset($this->__id__))
            $this->__id__ = spl_object_hash($this);
        return $this->__id__;
    }

    function __set($method_name, $args) {
        return $this->__send__("$method_name=", $args);
    }

    function __toString() {
        return $this->__send__('to_s');
    }

    function __send__($method_name) {
        $args = array_slice(func_get_args(), 1);
        if ($method = $this->singleton_class()->instance_method($method_name)) {
            return $method->bind($this)->splat($args);
        } else {
            $method = $this->singleton_class()->instance_method('method_missing');
            return $method->bind($this)->call($method_name, $args);
        }
    }

    function __splat__($method_name, $args) {
        array_unshift($args, $method_name);
        return call_user_func_array([$this, '__send__'], $args);
    }

    function instance_eval($block) {
        return $block->bindTo($this, $this)->invoke();
    }

    function instance_exec($args, $block) {
        $args = array_slice(0, -1, func_get_args());
        return $block->bindTo($this, $this)->invokeArgs($args);
    }

    function singleton_class() {
        if (!isset($this->__singleton_class__))
            $this->__singleton_class__ = new Module($this->__class()->name(), $this->__class()->name());
        return $this->__singleton_class__;
    }

    protected function super() {
        $args = func_get_args();
        $backtrace = debug_backtrace(false, 11);
        $last = array_pop($backtrace);
        $method_name = $last['function'];
        if (isset($last['class'])) {
            $module = $last['class'];
            foreach ($this->singleton_class()->ancestors() as $ancestor) {
                if ($ancestor->name() == $module) {
                    $found = true;
                } else if (isset($found) && $method = $ancestor->instance_method($method_name)) {
                    return $method->bind($this)->splat($args);
                }
            }
        }
        return $this->method_missing(__FUNCTION__, $args);
    }

    private function method_missing($method_name, $args) {
        throw new \BadMethodCallException("Undefined method $method_name for ".$this->__class()->name());
    }
}