<?php

namespace Phuby;

class Enumerable extends Object implements \ArrayAccess, \Countable, \Iterator {
    use Traits\ArrayAccess;
    use Traits\Countable;
    use Traits\Iterator;

    static function initialized($self) {
        $self->include(__CLASS__.'\ArrayAccess');
        $self->include(__CLASS__.'\Countable');
        $self->include(__CLASS__.'\Iterator');
    }

    public $__native__;
    public $__valid__;
}