<?php

namespace Phuby;

class Base64 {
    static function initialized($self) {
        $self->extend('self');
    }

    function decode64($string) {
        return base64_decode($string);
    }

    function encode64($string) {
        return base64_encode($string);
    }
}