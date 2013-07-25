<?php

namespace Phuby {
    class String extends Object { }
}

namespace Phuby\String {
    class ClassMethods {
        
    }

    class InstanceMethods {
        static function initialized($self) {
            $self->alias_method('capitalize!', 'capitalize_bang');
            $self->alias_method('downcase!', 'downcase_bang');
            $self->alias_method('upcase!', 'upcase_bang');
        }

        function initialize($native = '') {
            $this->{'@native'} = (string) $native;
        }

        function capitalize() {
            return $this->dup->tap('capitalize!');
        }

        function capitalize_bang() {
            $capitalized = ucfirst($this->{'@native'});

            if ($capitalized != $this->{'@native'}) {
                $this->{'@native'} = $capitalized;
                return $this;
            }
        }

        function downcase() {
            return $this->dup->tap('downcase!');
        }

        function downcase_bang() {
            $downcased = strtolower($this->{'@native'});

            if ($downcased != $this->{'@native'}) {
                $this->{'@native'} = $downcased;
                return $this;
            }
        }

        function to_s() {
            return $this;
        }

        function to_str() {
                return $this->{'@native'};
        }

        function upcase() {
            return $this->dup->tap('upcase!');
        }

        function upcase_bang() {
            $upcased = strtoupper($this->{'@native'});

            if ($upcased != $this->{'@native'}) {
                $this->{'@native'} = $upcased;
                return $this;
            }
        }
    }
}