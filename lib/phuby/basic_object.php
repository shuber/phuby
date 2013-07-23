<?php

namespace Phuby {
    class BasicObject {
        use Core;
    }
}

namespace Phuby\BasicObject {
    class InstanceMethods {
        static function initialized($self) {
            $self->alias_method('equal?', 'equal_query');
        }

        function equal_query($object) {
            return $this == $object;
        }
    }
}