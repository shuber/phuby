<?php

namespace Phuby;

class Marshal {
    static function initialized($self) {
        $self->extend('self');
        $self->alias_method('restore', 'load');
    }

    function dump($object) {
        return serialize($object);
    }

    function load($string) {
        return unserialize($string);
    }
}