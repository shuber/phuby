<?php

namespace Phuby\Enumerable;

class InstanceMethods {
    static function initialized($self) {
        $self->alias_method('reduce', 'inject');
    }

    function initialize($native = []) {
        $this->__native__ = $native;
    }

    function each($block) {
        foreach ($this as $key => $value)
            $block($key, $value);
        return $this;
    }

    function inject($initial, $block) {
        $this->each(function($key, $value) use (&$initial, $block) {
            $initial = $block($initial, $value, $key);
        });
        return $initial;
    }

    function map($block) {
        return $this->inject([], function($values, $key, $value) use ($block) {
            $values[] = $block($value, $key);
            return $values;
        });
    }

    function size() {
        return count($this);
    }

    function to_ary() {
        return $this->__native__;
    }
}