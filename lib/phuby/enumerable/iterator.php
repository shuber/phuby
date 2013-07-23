<?php

namespace Phuby\Enumerable;

class Iterator {
    function iterator_current() {
        return current($this->__native__);
    }

    function iterator_key() {
        return key($this->__native__);
    }

    function iterator_next() {
        $this->__valid__ = (next($this->__native__) !== false);
    }

    function iterator_rewind() {
        $this->__valid__ = (reset($this->__native__) !== false);
    }

    function iterator_valid() {
        return $this->__valid__;
    }
}