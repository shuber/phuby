<?php

namespace Phuby\Traits;

trait ArrayAccess {
    function offsetExists($offset) {
        return $this->__send__('array_access_offset_exists', $offset);
    }

    function offsetGet($offset) {
        return $this->__send__('array_access_offset_get', $offset);
    }

    function offsetSet($offset, $value) {
        return $this->__send__('array_access_offset_set', $offset, $value);
    }

    function offsetUnset($offset) {
        return $this->__send__('array_access_offset_unset', $offset);
    }
}