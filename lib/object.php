<?php

namespace Object {
    abstract class InstanceMethods { }
}

namespace {
    class Object {

        function __construct() { }

        function &__get($method) {
            if (method_exists($this, $method)) {
                $result = &$this->$method();
            } else {
                $result = null;
            }
            return $result;
        }

    }
}