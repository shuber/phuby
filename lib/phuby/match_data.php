<?php

namespace Phuby {
    class MatchData extends Object {
        static function initialized($self) {
            $self->include(__CLASS__.'\InstanceMethods');
        }
    }
}

namespace Phuby\MatchData {
    class InstanceMethods {
        static function initialized($self) {
            $self->attr_accessor('regexp');
            $self->attr_reader('captures');
        }

        function initialize($regexp, $matches) {
            $this->regexp = $regexp;
            $this->instance_variable_set('captures', array_slice($matches, 1));
        }
    }
}