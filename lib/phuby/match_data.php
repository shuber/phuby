<?php

namespace Phuby {
    class MatchData extends Object { }
}

namespace Phuby\MatchData {
    class InstanceMethods {
        static function initialized($self) {
            $self->attr_reader('captures', 'regexp', 'string');
        }

        function initialize($regexp, $string, $matches) {
            $this->instance_variable_set('regexp', $regexp);
            $this->instance_variable_set('string', $string);
            $this->instance_variable_set('captures', array_slice($matches, 1));
        }
    }
}