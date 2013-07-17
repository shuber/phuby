<?php

namespace Phuby\Module;

class Accessor {
    function attr_accessor($name) {
        foreach (func_get_args() as $name) {
            $this->attr_reader($name);
            $this->attr_writer($name);
        }
    }

    function attr_reader($name) {
        foreach (func_get_args() as $name)
            $this->define_method($name, function() use ($name) {
                return $this->instance_variable_get($name);
            });
    }

    function attr_writer($name) {
        foreach (func_get_args() as $name)
            $this->define_method("$name=", function($value) use ($name) {
                return $this->instance_variable_set($name, $value);
            });
    }
}