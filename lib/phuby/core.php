<?php

namespace Phuby;

trait Core {
    static function __callStatic($method_name, $args) {
        return Module::const_get(get_called_class())->splat($method_name, $args);
    }

    protected $__class__;
    protected $__id__;
    protected $__singleton_class__;
    protected $__variables__ = [];

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
            $this->__class__ = Module::const_get(get_class($this));

        return $this->__class__;
    }

    function __extend__($module) {
        foreach ($this->singleton_class()->ancestors() as $ancestor)
            if ($ancestor->name() == $module)
                return $this;

        $this->singleton_class()->__include($module);

        if (is_a($this, __NAMESPACE__.'\Module')) {
            if (method_exists($module, 'extended'))
                call_user_func("$module::extended", $this);
        } else {
            if (method_exists($module, 'extend_object'))
                call_user_func("$module::extend_object", $this);
        }

        return $this;
    }

    function __get($method_name) {
        return $this->__send__($method_name);
    }

    function __id__() {
        if (!isset($this->__id__))
            $this->__id__ = spl_object_hash($this);

        return $this->__id__;
    }

    function __send__($method_name) {
        $args = array_slice(func_get_args(), 1);

        if ($method = $this->singleton_class()->instance_method($method_name)) {
            return $method->bind($this)->splat($args);
        } else if ($method = $this->singleton_class()->instance_method('method_missing')) {
            return $method->bind($this)->call($method_name, $args);
        } else {
            return $this->__undefined__($method_name);
        }
    }

    function __set($method_name, $args) {
        return $this->__send__("$method_name=", $args);
    }

    function __splat__($method_name, $args) {
        array_unshift($args, $method_name);
        return call_user_func_array([$this, '__send__'], $args);
    }

    function __toString() {
        return $this->__send__('to_s');
    }

    function __undefined__($method_name) {
        throw new NoMethodError("undefined method '$method_name' for ".$this->__class()->name());
    }

    function instance_eval($block) {
        $singleton = $this->singleton_class();
        return $block->bindTo($singleton, $singleton)->invoke();
    }

    function instance_exec($args, $block) {
        $args = array_slice(0, -1, func_get_args());
        $singleton = $this->singleton_class();
        return $block->bindTo($singleton, $singleton)->invokeArgs($args);
    }

    function instance_variable_get($name) {
        if (isset($this->__variables__[$name]))
            return $this->__variables__[$name];
    }

    function instance_variable_set($name, $value) {
        return $this->__variables__[$name] = $value;
    }

    function instance_variables() {
        return $this->__variables__;
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

        return $this->__undefined__(__FUNCTION__, $args);
    }
}