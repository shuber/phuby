<?php

namespace Phuby {
    class String extends Object {
        public $__native__;
    }
}

namespace Phuby\String {
    class ClassMethods {
        
    }

    class InstanceMethods {
        static function initialized($self) {
            $self->alias_method('capitalize!', 'capitalize_bang');
        }

        function initialize($native = '') {
            $this->__native__ = (string) $native;
        }

        function capitalize() {
            return $this->dup->tap('capitalize!');
        }

        function capitalize_bang() {
            $capitalized = ucfirst($this->__native__);

            if ($capitalized != $this->__native__) {
                $this->__native__ = $capitalized;
                return $this;
            }
        }

        function to_s() {
            return $this;
        }

        function to_str() {
                return $this->__native__;
        }
    }
}