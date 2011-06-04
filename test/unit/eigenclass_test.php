<?php

namespace Phuby\EigenclassTest {
    class User extends \Phuby\Object { }
}

namespace Phuby {
    class EigenclassTest extends \ztest\UnitTestCase {

        function setup() {
            $this->user_class_name = __CLASS__.NS.'User';
            $this->user = new $this->user_class_name;
            $this->eigenclass = $this->user->_class_();
        }

        function test_object() {
            assert_equal($this->user, $this->eigenclass->object());
        }

        function test_reference() {
            assert_equal(Klass::instance($this->user_class_name), $this->eigenclass->reference());
        }

    }
}