<?php

namespace Phuby\Enumerable;

class ClassMethods {
    function numeric($object) {
        if (!is_array($object))
            return false;

        foreach ($object as $key => $value)
            if (!is_int($key))
                return false;

        return true;
    }
}