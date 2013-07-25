<?php

namespace Phuby;

class Enumerable extends Object implements \ArrayAccess, \Countable, \Iterator {
    use ArrayAccessTrait;
    use CountableTrait;
    use IteratorTrait;

    static function initialized($self) {
        $self->include(__CLASS__.'\ArrayAccess');
        $self->include(__CLASS__.'\Countable');
        $self->include(__CLASS__.'\Iterator');
    }
}