<?php

namespace Phuby\Enumerable;

class ClassMethods {
    function numeric($object) {
        foreach ($object as $key => $value)
            if (!is_int($key))
                return false;

        return true;
    }
}