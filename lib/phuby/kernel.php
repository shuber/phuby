<?php

namespace Phuby;

class Kernel {
    function extend($module) {
        if ($module == 'self')
            $module = $this->name();

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

    function to_s() {
        return $this->inspect();
    }
}