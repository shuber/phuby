<?php

namespace Phuby;

class Kernel {
    static function initialized($self) {
        $self->alias_method('[]', 'array_access_offset_get');
        $self->alias_method('[]=', 'array_access_offset_set');
    }
    

    function extend($module) {
        return $this->__extend__($module);
    }

    function inspect() {
        return '<'.$this->__class()->name().':'.$this->object_id().'>';
    }

    function is_a($module) {
        $module = (string) $module;
        foreach ($this->singleton_class()->ancestors() as $ancestor)
            if ($ancestor->name() == $module)
                return true;
    }

    function method($method_name) {
        if ($method = $this->singleton_class()->instance_method($method_name))
            return $method->bind($this);
    }

    function methods() {
        return $this->singleton_class()->instance_methods();
    }

    function object_id() {
        return $this->__id__();
    }

    function respond_to($method_name) {
        return !!$this->method($method_name) || $this->respond_to_missing($method_name);
    }

    function respond_to_missing($method_name) {
        return false;
    }

    function send($method_name) {
        return call_user_func_array([$this, '__send__'], func_get_args());
    }

    function splat($method_name, $args) {
        return $this->__splat__($method_name, $args);
    }

    function tap($block) {
        $block($this);
        return $this;
    }

    function to_s() {
        return $this->inspect();
    }
}