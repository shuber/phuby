<?php

namespace Phuby\Module;

class Reflection {
    function interfaces() {
        return $this->reflection()->getInterfaceNames();
    }

    function traits() {
        return $this->reflection()->getTraitNames();
    }
}