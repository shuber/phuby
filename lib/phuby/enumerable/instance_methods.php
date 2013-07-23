<?php

namespace Phuby\Enumerable;

class InstanceMethods {
    static function initialized($self) {
        $self->alias_method('collect', 'map');
        $self->alias_method('member', 'includes');
        $self->alias_method('reduce', 'inject');
    }

    function initialize($native = []) {
        $this->__native__ = $native;
    }

    function all($block) {
        return $this->none(function($object, $key) use ($block) {
            return !$block($object, $key);
        });
    }

    function any($block) {
        return !$this->none($block);
    }

    function each($block) {
        foreach ($this as $key => $object)
            if (!is_null($value = $block($key, $object)))
                return $value;

        return $this;
    }

    function includes($object) {
        foreach ($this as $value)
            if ($object == $value)
                return true;

        return false;
    }

    function inject($initial, $block) {
        $this->each(function($key, $object) use (&$initial, $block) {
            $initial = $block($initial, $object, $key);
        });

        return $initial;
    }

    function map($block) {
        return $this->inject([], function($values, $object, $key) use ($block) {
            $values[] = $block($object, $key);
            return $values;
        });
    }

    function none($block) {
        return !!$this->each(function ($key, $object) {
            if ($block($object, $key))
                return false;
        });
    }

    function partition($block) {
        return $this->inject([[], []], function($partitions, $object, $key) use ($block) {
            $index = (int) !!$block($object, $key);
            $partitions[$index][] = $object;
            return $partitions; 
        });
    }

    function size() {
        return count($this);
    }

    function to_ary() {
        return $this->__native__;
    }
}