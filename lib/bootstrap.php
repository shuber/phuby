<?php

require_once 'functions.php';
require_once 'phuby/environment.php';

Phuby\Environment::initialize(__DIR__);

// TODO: why doesn't this autoload?
trait Phuby {
    use Phuby\Core;

    static function initialized($self) {
        while ($superclass = $self->superclass())
            $self = $superclass;

        $self->__include(__TRAIT__.'\Object');
    }
}