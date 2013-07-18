<?php

namespace Phuby\Module;

class InstanceMethods {
    function method_defined($method_name) {
        return !!$this->instance_method($method_name);
    }
}