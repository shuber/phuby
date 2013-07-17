<?php

namespace Phuby\Traits;

trait IteratorAggregate {
    function getIterator() {
        return $this->__send__('iterator_aggregate_get_iterator');
    }
}