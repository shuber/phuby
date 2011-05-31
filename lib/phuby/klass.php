<?php

namespace Phuby {
    class Klass extends Module {

        protected $_name;

        function __construct($name) {
            $this->_name = $name;
            parent::__construct();
        }

        function name() {
            return $this->_name;
        }

        protected function bind_instance_variables_to_properties($object) {
            $class = $this->_name;
            foreach (get_class_vars($this) as $property => $value) {
                if ($object && $this->instance_variable_defined($property)) $class::${$property} = $this->_instance_variables[$property];
                $this->_instance_variables[$property] = &$class::${$property};
            }
            parent::bind_instance_variables_to_properties($object);
        }

    }
}