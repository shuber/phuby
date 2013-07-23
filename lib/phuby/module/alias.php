<?php

namespace Phuby\Module;

class Alias {
    function alias_method($new_name, $old_name) {
        $this->define_method($new_name, function() use ($old_name) {
            return $this->__splat__($old_name, func_get_args());
        });

        return $this;
    }

    function alias_method_chain($name, $with) {
        $this->alias_method($name.'_with_'.$with, $name);
        $this->alias_method($name, $name.'_without_'.$with);
        return $this;
    }
}