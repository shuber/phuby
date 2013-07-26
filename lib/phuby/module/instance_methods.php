<?php

namespace Phuby\Module;

class InstanceMethods {
    static function initialized($self) {
        $self->alias_method('class_eval', 'module_eval');
        $self->alias_method('class_exec', 'module_exec');
        $self->alias_method('include?', 'include_query');
        $self->alias_method('method_defined?', 'method_defined_query');
        $self->alias_method('to_s', 'name');
    }

    function __new() {
        $instance = $this->allocate();

        if ($method = $instance->method('initialize'))
            $method->splat(func_get_args());

        return $instance;
    }

    function include_query($module) {
        foreach ($this->ancestors() as $ancestor)
            if ($ancestor->name() == $module)
                return true;

        return false;
    }

    function method_defined_query($method_name) {
        return !!$this->instance_method($method_name);
    }

    function module_eval($block) {
        return $block->bindTo($this, $this)->invoke();
    }

    function module_exec($args, $block) {
        $args = array_slice(0, -1, func_get_args());
        return $block->bindTo($this, $this)->invokeArgs($args);
    }

    function remove_method($method_name) {
        unset($this->{'@methods'}[$method_name]);
        return $this;
    }

    function superclass() {
        return $this->{'@superclass'};
    }

    // TODO: prepended modules will still respond to this method
    function undef_method($method_name) {
        $this->define_method($method_name, function() use ($method_name) {
            return $this->__undefined__($method_name);
        });
    }
}