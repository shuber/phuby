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
            $self->alias_method('empty?', 'empty_query');
            $self->alias_method('lstrip!', 'lstrip_bang');
            $self->alias_method('rstrip!', 'rstrip_bang');
            $self->alias_method('strip!', 'strip_bang');
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

        function empty_query() {
            return $this->size > 0;
        }

        function lstrip() {
            return $this->dup->tap('lstrip!');
        }

        function lstrip_bang() {
            $stripped = ltrim($this->{'@native'});

            if ($stripped != $this->{'@native'}) {
                $this->{'@native'} = $stripped;
                return $this;
            }
        }

        function rstrip() {
            return $this->dup->tap('rstrip!');
        }

        function rstrip_bang() {
            $stripped = rtrim($this->{'@native'});

            if ($stripped != $this->{'@native'}) {
                $this->{'@native'} = $stripped;
                return $this;
            }
        }

        function size() {
            return strlen($this->{'@native'});
        }

        function strip() {
            return $this->dup->tap('strip!');
        }

        function strip_bang() {
            $stripped = trim($this->{'@native'});

            if ($stripped != $this->{'@native'}) {
                $this->{'@native'} = $stripped;
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