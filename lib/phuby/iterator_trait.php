<?php

namespace Phuby;

trait IteratorTrait {
    function current() {
        return $this->__call('iterator_current');
    }

    function key() {
        return $this->__call('iterator_key');
    }

    function next() {
        return $this->__call('iterator_next');
    }

    function rewind() {
        return $this->__call('iterator_rewind');
    }

    function valid() {
        return $this->__call('iterator_valid');
    }
}