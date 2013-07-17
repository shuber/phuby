<?php

trait Phuby {
    use Phuby\Core;

    static function initialized($self) {
        $self->include(__TRAIT__.'\Kernel');
    }
}