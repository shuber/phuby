<?php

namespace Phuby\Enumerable;

class Countable {
    function countable_count() {
        return count($this->__enum__);
    }
}