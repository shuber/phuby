<?php

namespace Phuby;

class Object extends BasicObject {
    static function initialized($class) {
        $class->__include(__NAMESPACE__.'\Kernel');
    }
}