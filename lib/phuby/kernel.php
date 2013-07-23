<?php

namespace Phuby;

class Kernel {
    static function initialized($self) {
        $self->alias_method('[]', 'array_access_offset_get');
        $self->alias_method('[]=', 'array_access_offset_set');
        $self->alias_method('caller', '__caller__');
        $self->alias_method('extend', '__extend__');
        $self->alias_method('object_id', '__id__');
        $self->alias_method('send', '__send__');
        $self->alias_method('splat', '__splat__');
        $self->alias_method('to_s', 'inspect');
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

    function respond_to($method_name) {
        return !!$this->method($method_name) || $this->respond_to_missing($method_name);
    }

    function respond_to_missing($method_name) {
        return false;
    }

    function tap($block) {
        $block($this);
        return $this;
    }
}