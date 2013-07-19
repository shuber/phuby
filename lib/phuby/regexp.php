<?php

namespace Phuby {
    class Regexp extends Object {
        static function initialized($self) {
            $self->extend(__CLASS__.'\ClassMethods');
            $self->include(__CLASS__.'\InstanceMethods');
        }
    }
}

namespace Phuby\Regexp {
    class ClassMethods {
        static function initialized($self) {
            $self->alias_method('quote', 'escape');
        }

        function escape($string, $delimiter = null) {
            return preg_quote($string, $delimiter);
        }
    }

    class InstanceMethods {
        static function initialized($self) {
            $self->attr_accessor('regexp');
        }

        function initialize($regexp) {
            if (@preg_match($regexp, '') === false)
                $regexp = "/$regexp/";
            $this->regexp = $regexp;
        }

        function match($string) {
            $string = (string) $string;
            if (preg_match($this->regexp, $string, $matches))
                return Phuby('Phuby\MatchData')->new($this, $string, $matches);
        }
    }
}