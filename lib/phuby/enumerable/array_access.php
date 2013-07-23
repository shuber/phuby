<?php

namespace Phuby\Enumerable;

class ArrayAccess {
    function array_access_offset_exists($offset) {
        return isset($this->__native__[$offset]);
    }

    function array_access_offset_get($offset) {
        return $this->__native__[$offset];
    }

    function array_access_offset_set($offset, $value) {
        if (is_null($offset))
            $offset = count($this->__native__);

        return $this->__native__[$offset] = $value;
    }

    function array_access_offset_unset($offset) {
        unset($this->__native__[$offset]);
    }
}