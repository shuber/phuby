<?php

namespace Phuby;

class BasicObject {
    use Core;

    private static $constants = [];

    static function __callStatic($method_name, $args) {
        return self::const_get(get_called_class())->splat($method_name, $args);
    }

    static function const_get($name) {
        if (isset(self::$constants[$name])) {
            return self::$constants[$name];
        } else if (class_exists($name)) {
            self::$constants[$name] = new Module;
            self::$constants[$name]->initialize($name);
            return self::$constants[$name];
        } else {
            throw new NameError("uninitialized constant $name");
        }
    }
}