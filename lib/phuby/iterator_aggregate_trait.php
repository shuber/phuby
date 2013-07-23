<?php

namespace Phuby;

trait IteratorAggregateTrait {
    function getIterator() {
        return $this->__send__('iterator_aggregate_get_iterator');
    }
}