<?php

namespace Phuby\Enumerable;

class ArrayAccess {
    function array_access_exists($offset) {
        return isset($this->__enum__[$offset]);
    }

    function array_access_get($offset) {
        return $this->__enum__[$offset];
    }

    function array_access_set($offset, $value) {
        if (is_null($offset))
            $offset = count($this->__enum__);
        return $this->__enum__[$offset] = $value;
    }

    function array_access_unset($offset) {
        unset($this->__enum__[$offset]);
    }
}