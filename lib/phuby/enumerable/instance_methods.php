<?php

namespace Phuby\Enumerable;

class InstanceMethods {
    static function initialized($self) {
        $self->alias_method('all?', 'all_query');
        $self->alias_method('any?', 'any_query');
        $self->alias_method('collect', 'map');
        $self->alias_method('find', 'detect');
        $self->alias_method('include?', 'include_query');
        $self->alias_method('member_query', 'include_query');
        $self->alias_method('member?', 'member_query');
        $self->alias_method('none?', 'none_query');
        $self->alias_method('reduce', 'inject');
    }

    function initialize($native = []) {
        $this->{'@native'} = $native;
    }

    function all_query($block) {
        return $this->{'none?'}(function($object, $key) use ($block) {
            return !$block($object, $key);
        });
    }

    function any_query($block) {
        return !$this->{'none?'}($block);
    }

    function count() {
        return count($this->{'@native'});
    }

    function detect($callback) {
        if (func_num_args() > 1) {
            $ifnone = $callback;
            $callback = func_get_arg(1);
        }

        foreach ($this->{'@native'} as $object)
            if ($callback($object))
                return $object;

        if (isset($ifnone))
            return $ifnone;
    }

    function drop($number) {
        if ($number < 0)
            throw new \Phuby\ArgumentError('attempt to drop negative size');

        $native = array_slice(array_values($this->{'@native'}), $number));

        return $this->Array->new($native);
    }

    function drop_while($block) {
        $native = array_values($this->{'@native'});

        if ($index = $this->find_index($block))
            $native = array_slice($native, $index);

        return $this->Array->new($native);
    }

    function each($block) {
        foreach ($this as $key => $object)
            if (!is_null($value = $block($key, $object)))
                return $value;

        return $this;
    }

    function find_all($block) {
        $matches = $this->Array->new;

        foreach ($this->{'@native'} as $object)
            if ($block($object))
                $matches[] = $object;

        return $matches;
    }

    function find_index($block) {
        if (!is_callable($block))
            $block = function ($object) use ($block) {
                return $block == $object;
            };

        foreach ($this->{'@native'} as $index => $object)
            if ($block($object))
                return $index;
    }

    function first() {
        if (!empty($this->{'@native'}))
            return reset($this->{'@native'});
    }

    function include_query($object) {
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

    function none_query($block) {
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

    function to_a() {
        return $this->Array(array_values($this->{'@native'}));
    }

    function to_ary() {
        return $this->to_a->{'@native'};
    }
}