<?php

namespace Phuby;

class Object extends BasicObject {
    static function initialized($self) {
        $self->__include(__NAMESPACE__.'\Kernel');
    }
}