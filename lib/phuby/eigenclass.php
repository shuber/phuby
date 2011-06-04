<?php

namespace Phuby {
    class Eigenclass extends Klass {

        protected $_object;

        function __construct($object) {
            $this->_object = $object;
            parent::__construct(get_class($object));
        }

        function object() {
            return $this->_object;
        }

        function reference() {
            return self::instance($this->_name);
        }

    }
}