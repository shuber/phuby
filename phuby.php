<?php

require_once 'lib/bootstrap.php';

trait Phuby {
    use Phuby\Core;

    static function initialized($self) {
        while ($superclass = $self->superclass())
            $self = $superclass;

        $self->__include(__TRAIT__.'\Object');
    }
}