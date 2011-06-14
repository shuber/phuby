<?php

namespace Phuby {
    class Eigenclass extends Klass {

        protected $_object;
        protected $_reference;

        function __construct($object) {
            parent::__construct(get_class($object));
            $this->_object = $object;
            $this->_reference = self::instance($this->_name);
            $this->_reference->add_dependant($this);
        }

        function object() {
            return $this->_object;
        }

        function reference() {
            return $this->_reference;
        }

    }
}