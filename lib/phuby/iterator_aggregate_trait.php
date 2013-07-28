<?php

namespace Phuby;

trait IteratorAggregateTrait {
    function getIterator() {
        return $this->__call('iterator_aggregate_get_iterator');
    }
}