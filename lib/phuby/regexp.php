<?php

namespace Phuby {
    class Regexp extends Object { }
}

namespace Phuby\Regexp {
    class ClassMethods {
        static function initialized($self) {
            $self->alias_method('quote', 'escape');
            $self->alias_method('valid?', 'valid_query');
        }

        function escape($string, $delimiter = null) {
            return preg_quote($string, $delimiter);
        }

        function valid_query($regexp) {
            return @preg_match($regexp, '') !== false;
        }
    }

    class InstanceMethods {
        static function initialized($self) {
            $self->attr_reader('regexp');
        }

        function initialize($regexp) {
            if (!$this->class->{'valid?'}($regexp))
                $regexp = "/$regexp/";

            $this->{'@regexp'} = $regexp;
        }

        function match($string) {
            $string = (string) $string;
            $this->{'$&'} = null;

            if (preg_match($this->regexp, $string, $matches)) {
                $match = Phuby('Phuby\MatchData')->new($this, $string, $matches);

                $this->{'$&'} = $string;
                $this->{'$~'} = $match;

                foreach ($matches as $index => $value)
                    if ($index > 0)
                        $this->{"$$index"} = $value;

                return $match;
            }
        }
    }
}