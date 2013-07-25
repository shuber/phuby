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
            $this->{'@regexp'} = $regexp;
            $this->{'@string'} = $string;
            $this->{'@captures'} = array_slice($matches, 1);
        }
    }
}