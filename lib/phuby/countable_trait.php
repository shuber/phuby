<?php

namespace Phuby;

trait CountableTrait {
    function count() {
        return $this->__call('countable_count');
    }
}