<?php

namespace Phuby\Enumerable;

class ArrayAccess {
    function array_access_offset_exists($offset) {
        return isset($this->{'@native'}[$offset]);
    }

    function array_access_offset_get($offset) {
        return $this->{'@native'}[$offset];
    }

    function array_access_offset_set($offset, $value) {
        if (is_null($offset))
            $offset = count($this->{'@native'});

        return $this->{'@native'}[$offset] = $value;
    }

    function array_access_offset_unset($offset) {
        unset($this->{'@native'}[$offset]);
    }
}