<?php

namespace Phuby;

trait CountableTrait {
    function count() {
        return $this->__send__('countable_count');
    }
}