<?php

namespace Phuby\Traits;

trait Iterator {
    function current() {
        return $this->__send__('iterator_current');
    }

    function key() {
        return $this->__send__('iterator_key');
    }

    function next() {
        return $this->__send__('iterator_next');
    }

    function rewind() {
        return $this->__send__('iterator_rewind');
    }

    function valid() {
        return $this->__send__('iterator_valid');
    }
}