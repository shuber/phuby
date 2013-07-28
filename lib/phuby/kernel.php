<?php

namespace Phuby;

class Kernel {
    static function initialized($self) {
        $self->alias_method('[]', 'array_access_offset_get');
        $self->alias_method('[]=', 'array_access_offset_set');
        $self->alias_method('is_a?', 'is_a_query');
        $self->alias_method('caller', '__caller__');
        $self->alias_method('extend', '__extend__');
        $self->alias_method('kind_of?', 'is_a_query');
        $self->alias_method('object_id', '__id__');
        $self->alias_method('respond_to?', 'respond_to_query');
        $self->alias_method('respond_to_missing?', 'respond_to_missing_query');
        $self->alias_method('send', '__send__');
        $self->alias_method('splat', '__call');
        $self->alias_method('to_s', 'inspect');
    }

    function dup() {
        return clone $this;
    }

    function inspect() {
        return '<'.$this->__class()->name().':'.$this->object_id().'>';
    }

    function instance_variable_get($name) {
        return $this->{$name};
    }

    function instance_variable_set($name, $value) {
        return $this->{$name} = $value;
    }

    function instance_variables() {
        return array_keys($this->__phuby__);
    }

    function is_a_query($module) {
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

    function respond_to_query($method_name) {
        return !!$this->method($method_name) || $this->respond_to_missing_query($method_name);
    }

    function respond_to_missing_query($method_name) {
        return false;
    }

    function tap($block) {
        if (is_string($block))
            $block = function($object) use ($block) {
                $object->__send__($block);
            };

        $block($this);
        return $this;
    }
}