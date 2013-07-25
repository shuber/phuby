<?php

namespace Phuby\Enumerable;

class Iterator {
    function iterator_current() {
        return current($this->{'@native'});
    }

    function iterator_key() {
        return key($this->{'@native'});
    }

    function iterator_next() {
        $this->{'@valid'} = (next($this->{'@native'}) !== false);
    }

    function iterator_rewind() {
        $this->{'@valid'} = (reset($this->{'@native'}) !== false);
    }

    function iterator_valid() {
        return $this->{'@valid'};
    }
}