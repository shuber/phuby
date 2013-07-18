<?php

function Phuby($object) {
    if (is_string($object) && class_exists($object))
        return Phuby\BasicObject::const_get($object);
}

trait Phuby {
    use Phuby\Core;

    static function initialized($self) {
        while ($superclass = $self->superclass())
            $self = $superclass;

        $self->__include(__TRAIT__.'\Object');
    }
}