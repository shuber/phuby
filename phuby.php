<?php

require_once 'lib/environment.php';
require_once 'lib/functions.php';
require_once 'lib/globals.php';

trait Phuby {
    use Phuby\Core;

    static function initialized($self) {
        while ($superclass = $self->superclass())
            $self = $superclass;

        $self->__include(__TRAIT__.'\Object');
    }
}