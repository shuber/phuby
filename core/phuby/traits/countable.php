<?php

namespace Phuby\Traits;

trait Countable {
    function count() {
        return $this->__send__('countable_count');
    }
}