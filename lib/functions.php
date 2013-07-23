<?php

function Phuby($object, $lookup_class = true) {
    if ($lookup_class && is_string($object) && class_exists($object))
        return Phuby\Module::const_get($object);

    // if (Phuby\Enumerable::numeric($object))
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