<?php

namespace Phuby\Module;

class InstanceMethods {
    static function initialized($self) {
        $self->alias_method('class_eval', 'module_eval');
        $self->alias_method('class_exec', 'module_exec');
    }

    function extend($module) {
        if ($module == 'self')
            $module = $this->name();
        return $this->super($module);
    }

    function method_defined($method_name) {
        return !!$this->instance_method($method_name);
    }

    function module_eval($block) {
        return $block->bindTo($this, $this)->invoke();
    }

    function module_exec($args, $block) {
        $args = array_slice(0, -1, func_get_args());
        return $block->bindTo($this, $this)->invokeArgs($args);
    }
}