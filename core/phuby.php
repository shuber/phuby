<?php

function Phuby($object, $lookup_class = true) {
    if ($lookup_class && is_string($object) && class_exists($object))
        return Phuby\Module::const_get($object);

    // if (is_numeric_array($object))
    //     return Phuby\Array::__new($object);

    if (is_array($object))
        return Phuby\Hash::__new($object);

    switch(gettype($object)) {
        case 'float':   return Phuby\Float::__new($object);
        case 'integer': return Phuby\Integer::__new($object);
        case 'object':  return Phuby\Proxy::__new($object);
        case 'string':  return Phuby\String::__new($object);
        default:        return $object;
    }
}

trait Phuby {
    use Phuby\Core;

    static function initialized($self) {
        while ($superclass = $self->superclass())
            $self = $superclass;

        $self->__include(__TRAIT__.'\Object');
    }
}