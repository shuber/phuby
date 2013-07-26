<?php

namespace Phuby\Module;

class Reflection {
    static function initialized($self) {
        $self->attr_reader('reflection');
    }

    function interfaces() {
        return $this->reflection->getInterfaceNames();
    }

    function traits() {
        return $this->reflection->getTraitNames();
    }
}