<?php

namespace Phuby\Enumerable;

class Iterator {
    function iterator_current() {
        return current($this->__enum__);
    }

    function iterator_key() {
        return key($this->__enum__);
    }

    function iterator_next() {
        $this->__valid__ = (next($this->__enum__) !== false);
    }

    function iterator_rewind() {
        $this->__valid__ = (reset($this->__enum__) !== false);
    }

    function iterator_valid() {
        return $this->__valid__;
    }
}